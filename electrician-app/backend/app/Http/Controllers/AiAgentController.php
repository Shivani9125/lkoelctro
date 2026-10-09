<?php

namespace App\Http\Controllers;

use App\Services\ElectroFixAgentService;
use App\Services\ElevenLabsTtsService;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    protected ElectroFixAgentService $agentService;
    protected ElevenLabsTtsService $ttsService;

    public function __construct(ElectroFixAgentService $agentService, ElevenLabsTtsService $ttsService)
    {
        $this->agentService = $agentService;
        $this->ttsService = $ttsService;
    }

  
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array',
            'user_location' => 'nullable|array',
            'pending_action' => 'nullable|array',
            'model' => 'nullable|string',
            'ollama_url' => 'nullable|string',
        ]);

        $message = $request->input('message');
        $history = $request->input('conversation_history', []);
        $location = $request->input('user_location', []);
        $pendingAction = $request->input('pending_action', null);
        $model = $request->input('model', null);
        $ollamaUrl = $request->input('ollama_url', null);

        $result = $this->agentService->processMessage(
            $message,
            $history,
            $location,
            $pendingAction,
            $model,
            $ollamaUrl
        );

        // Normalize response payload for web clients
        $primaryMessage = $result['reply'] ?? ($result['message'] ?? 'Processed.');
        $spokenText = $result['spoken_text'] ?? $primaryMessage;
        $state = $result['state'] ?? 'IDLE';
        $toolUsed = $result['tool_used'] ?? (isset($result['tools_used'][0]['name']) ? $result['tools_used'][0]['name'] : null);

        $dataPayload = [
            'electricians' => $result['electricians'] ?? [],
            'booking' => $result['booking'] ?? null,
            'contact_inquiry' => $result['contact_inquiry'] ?? null,
            'tools_used' => $result['tools_used'] ?? [],
            'intent' => $result['intent'] ?? null,
        ];

        // Append to history
        $updatedHistory = $history;
        $updatedHistory[] = ['role' => 'user', 'content' => $message];
        $updatedHistory[] = ['role' => 'assistant', 'content' => $primaryMessage];

        return response()->json([
            'success' => $result['success'] ?? true,
            'message' => $primaryMessage,
            'reply' => $primaryMessage,
            'spoken_text' => $spokenText,
            'state' => $state,
            'tool_used' => $toolUsed,
            'tool_display' => $result['tool_display'] ?? null,
            'ai_provider' => $result['ai_provider'] ?? 'autonomous_agent',
            'model_used' => $result['model_used'] ?? ($model ?: 'llama3.2'),
            'data' => $dataPayload,
            'pending_action' => $result['pending_action'] ?? null,
            'requires_confirmation' => $result['requires_confirmation'] ?? false,
            'history' => $updatedHistory
        ]);
    }

    /**
     * Check Ollama connectivity & list available local models
     * GET /api/ai-agent/ollama-status
     */
    public function ollamaStatus(Request $request)
    {
        $url = $request->query('url', null);
        return response()->json($this->agentService->checkOllamaStatus($url));
    }

    /**
     * Tool Endpoint: Get services catalog.
     * GET /api/ai-agent/tools/services
     */
    public function services()
    {
        return response()->json($this->agentService->find_services());
    }

    /**
     * Tool Endpoint: Find nearby electricians.
     * GET /api/ai-agent/tools/electricians
     */
    public function electricians(Request $request)
    {
        $lat = (float) $request->query('lat', 26.8530);
        $lng = (float) $request->query('lng', 80.9980);
        $service = (string) $request->query('service', '');
        $area = (string) $request->query('area', '');

        return response()->json(
            $this->agentService->find_nearby_electricians($lat, $lng, $service, $area)
        );
    }

    /**
     * Tool Endpoint: Create a booking (requires confirmed intent).
     * POST /api/ai-agent/tools/create-booking
     */
    public function createBooking(Request $request)
    {
        $request->validate([
            'electrician_id' => 'required|integer',
            'customer_name' => 'nullable|string',
            'customer_phone' => 'nullable|string',
            'customer_address' => 'nullable|string',
            'service_type' => 'nullable|string',
            'time_slot' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $result = $this->agentService->create_booking(
            (int) $request->input('electrician_id'),
            (string) ($request->input('customer_name') ?: 'Customer'),
            (string) ($request->input('customer_phone') ?: '+91 9812340001'),
            (string) ($request->input('customer_address') ?: 'Lucknow Address'),
            (string) ($request->input('service_type') ?: 'Electrical Repair'),
            (string) ($request->input('time_slot') ?: 'Within 30 mins'),
            (string) ($request->input('notes') ?: 'Booked via AI Agent')
        );

        return response()->json($result);
    }

    /**
     * Tool Endpoint: Check booking status.
     * GET /api/ai-agent/tools/booking/{reference}
     */
    public function bookingStatus(string $reference)
    {
        return response()->json($this->agentService->get_booking_status($reference));
    }

    /**
     * ElevenLabs Natural Voice Synthesis Endpoint
     * POST /api/ai-agent/tts
     */
    public function tts(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:2000',
            'voice_id' => 'nullable|string',
            'api_key' => 'nullable|string',
        ]);

        $text = (string) $request->input('text');
        $voiceId = $request->input('voice_id');
        $apiKey = $request->input('api_key');

        $result = $this->ttsService->synthesize($text, $voiceId, $apiKey);

        return response()->json($result);
    }

    /**
     * List Curated ElevenLabs Voices & Configuration Status
     * GET /api/ai-agent/voices
     */
    public function voices()
    {
        return response()->json([
            'success' => true,
            'configured' => !empty(env('ELEVENLABS_API_KEY')),
            'default_voice' => env('ELEVENLABS_VOICE_ID', 'pNInz6obpgDQGcFmaJgB'),
            'voices' => $this->ttsService->getAvailableVoices(),
        ]);
    }

    /**
     * Send contact email with AI Agent triage & logging
     * POST /api/ai-agent/contact-email
     */
    public function sendContactEmail(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:180',
            'phone' => 'nullable|string|max:40',
            'area' => 'nullable|string|max:120',
            'subject' => 'nullable|string|max:250',
            'message' => 'required|string|max:4000',
            'ai_diagnosis' => 'nullable|string',
            'ai_priority' => 'nullable|string',
            'channel' => 'nullable|string',
        ]);

        $result = $this->agentService->send_contact_email(
            $request->input('name'),
            $request->input('email'),
            $request->input('phone', ''),
            $request->input('message'),
            $request->input('subject', ''),
            $request->input('area', 'Lucknow'),
            $request->input('channel', 'web_form'),
            $request->input('ai_diagnosis', null),
            $request->input('ai_priority', null)
        );

        return response()->json($result);
    }

    /**
     * Auto-draft, diagnose & polish contact email using AI Agent
     * POST /api/ai-agent/ai-draft-contact
     */
    public function draftContactWithAi(Request $request)
    {
        $request->validate([
            'problem_description' => 'required|string|max:2000',
            'area' => 'nullable|string|max:120',
        ]);

        $result = $this->agentService->generate_ai_contact_draft(
            $request->input('problem_description'),
            $request->input('area', 'Lucknow')
        );

        return response()->json($result);
    }
}
