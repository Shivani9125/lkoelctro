<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ElevenLabs High-Fidelity Human Voice Synthesis Service
 * Provides human-sounding, expressive speech in Hindi, Hinglish, and English
 * with multi-tier disk caching and graceful fallbacks.
 */
class ElevenLabsTtsService
{
    protected string $defaultVoiceId;
    protected string $defaultModelId;
    protected ?string $apiKey;
    protected string $cacheDir;

    // Curated high-fidelity voices
    public const VOICES = [
        'pNInz6obpgDQGcFmaJgB' => [
            'id' => 'pNInz6obpgDQGcFmaJgB',
            'name' => 'Adam',
            'description' => 'Deep, warm & friendly (Recommended for Bijli Guru)',
            'gender' => 'Male',
            'preview_avatar' => '👨‍💼',
        ],
        'ErXwobaYiN019PkySvjV' => [
            'id' => 'ErXwobaYiN019PkySvjV',
            'name' => 'Antoni',
            'description' => 'Polite & conversational',
            'gender' => 'Male',
            'preview_avatar' => '🧑‍🔧',
        ],
        '21m00Tcm4TlvDq8ikWAM' => [
            'id' => '21m00Tcm4TlvDq8ikWAM',
            'name' => 'Rachel',
            'description' => 'Clear, gentle & soothing',
            'gender' => 'Female',
            'preview_avatar' => '👩‍💼',
        ],
        'IKne3meq5aSn9XLyUdCD' => [
            'id' => 'IKne3meq5aSn9XLyUdCD',
            'name' => 'Charlie',
            'description' => 'Casual, approachable & natural',
            'gender' => 'Male',
            'preview_avatar' => '🧑',
        ],
        'EXAVITQu4vr4xnSDxMaL' => [
            'id' => 'EXAVITQu4vr4xnSDxMaL',
            'name' => 'Bella',
            'description' => 'Expressive & friendly',
            'gender' => 'Female',
            'preview_avatar' => '👩',
        ],
    ];

    public function __construct()
    {
        $this->apiKey = env('ELEVENLABS_API_KEY') ?: null;
        $this->defaultVoiceId = env('ELEVENLABS_VOICE_ID') ?: 'pNInz6obpgDQGcFmaJgB';
        $this->defaultModelId = env('ELEVENLABS_MODEL_ID') ?: 'eleven_multilingual_v2';
        $this->cacheDir = storage_path('app/tts_cache');

        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Synthesize natural speech audio
     */
    public function synthesize(string $text, ?string $voiceId = null, ?string $customApiKey = null): array
    {
        $cleanText = $this->cleanTextForSpeech($text);
        if (empty($cleanText)) {
            return [
                'success' => false,
                'message' => 'No speakable text provided',
            ];
        }

        $activeVoiceId = $voiceId ?: $this->defaultVoiceId;
        $activeApiKey = $customApiKey ?: $this->apiKey;

        // Check local disk cache
        $cacheKey = md5($cleanText . '_' . $activeVoiceId . '_' . ($activeApiKey ? 'el' : 'fb'));
        $cacheFile = $this->cacheDir . '/' . $cacheKey . '.mp3';

        if (file_exists($cacheFile) && filesize($cacheFile) > 100) {
            $audioBinary = file_get_contents($cacheFile);
            return [
                'success' => true,
                'cached' => true,
                'provider' => $activeApiKey ? 'elevenlabs' : 'neural_fallback',
                'voice_id' => $activeVoiceId,
                'audio_base64' => 'data:audio/mp3;base64,' . base64_encode($audioBinary),
                'clean_text' => $cleanText,
            ];
        }

        // 1. Primary Attempt: ElevenLabs API (if API key is present)
        if (!empty($activeApiKey)) {
            try {
                $endpoint = "https://api.elevenlabs.io/v1/text-to-speech/{$activeVoiceId}";
                $response = Http::withHeaders([
                    'xi-api-key' => $activeApiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'audio/mpeg',
                ])->timeout(15)->post($endpoint, [
                    'text' => $cleanText,
                    'model_id' => $this->defaultModelId,
                    'voice_settings' => [
                        'stability' => 0.50,
                        'similarity_boost' => 0.75,
                        'style' => 0.0,
                        'use_speaker_boost' => true,
                    ],
                ]);

                if ($response->successful()) {
                    $audioBinary = $response->body();
                    file_put_contents($cacheFile, $audioBinary);

                    return [
                        'success' => true,
                        'cached' => false,
                        'provider' => 'elevenlabs',
                        'voice_id' => $activeVoiceId,
                        'audio_base64' => 'data:audio/mp3;base64,' . base64_encode($audioBinary),
                        'clean_text' => $cleanText,
                    ];
                } else {
                    Log::warning('ElevenLabs API error: ' . $response->status() . ' - ' . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error('ElevenLabs synthesis exception: ' . $e->getMessage());
            }
        }

        // 2. High-Quality Neural Fallback (No API key needed, sounds natural in Hindi/Indian English)
        try {
            $fallbackUrl = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=hi&client=tw-ob&q=' . rawurlencode($cleanText);
            $fallbackRes = Http::timeout(6)->get($fallbackUrl);

            if ($fallbackRes->successful() && strlen($fallbackRes->body()) > 200) {
                $audioBinary = $fallbackRes->body();
                file_put_contents($cacheFile, $audioBinary);

                return [
                    'success' => true,
                    'cached' => false,
                    'provider' => 'neural_fallback',
                    'voice_id' => 'hindi_neural',
                    'audio_base64' => 'data:audio/mp3;base64,' . base64_encode($audioBinary),
                    'clean_text' => $cleanText,
                    'hint' => 'To use ultra-human ElevenLabs voice, set ELEVENLABS_API_KEY in backend/.env',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Neural TTS fallback failed: ' . $e->getMessage());
        }

        // 3. Client-side Speech fallback
        return [
            'success' => true,
            'cached' => false,
            'provider' => 'client_speech',
            'clean_text' => $cleanText,
            'hint' => 'Using browser speech synthesis fallback.',
        ];
    }

    /**
     * Clean and optimize raw message text for natural, conversational speech
     */
    public function cleanTextForSpeech(string $text): string
    {
        // 1. Remove code blocks
        $text = preg_replace('/```[\s\S]*?```/', '', $text);

        // 2. Remove markdown tables
        $text = preg_replace('/\|.*?\|/', '', $text);

        // 3. Remove markdown links [text](url)
        $text = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $text);

        // 4. Remove bold, italic, code ticks, blockquotes
        $text = preg_replace('/[*_`#~>]/', '', $text);

        // 5. Remove bullets / list markers
        $text = preg_replace('/^[\s*•\-–—\d\.]+/m', '', $text);

        // 6. Remove emojis
        $text = preg_replace('/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $text);

        // 7. Collapse extra whitespace
        $text = trim(preg_replace('/\s+/', ' ', $text));

        // 8. Truncate to max 350 characters or first 2 full sentences so speech stays snappy
        if (mb_strlen($text) > 350) {
            $sentences = preg_split('/(?<=[.?!।])\s+/', $text, 3);
            if (count($sentences) >= 2) {
                $text = trim($sentences[0] . ' ' . $sentences[1]);
            } else {
                $text = mb_substr($text, 0, 320) . '...';
            }
        }

        return $text;
    }

    public function getAvailableVoices(): array
    {
        return array_values(self::VOICES);
    }
}
