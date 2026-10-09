import React, { useState, useEffect, useRef } from 'react';
import api from '../services/api';
import './ElectroFixRobot.css';

/**
 * ElectroFix AI - Bijli Guru (बिजली गुरु)
 * Intelligent General-Purpose & Electrical Service AI Assistant
 * Powered by Ollama Local AI, Instant Knowledge & Autonomous Cloud Intelligence.
 */
export default function ElectroFixRobot({
  customerLocation,
  onSelectElectrician,
  externalTriggerQuery,
  isForceOpen,
  onClose
}) {
  // --- UI & Agent States ---
  const [isOpen, setIsOpen] = useState(false);
  const [agentState, setAgentState] = useState('IDLE'); // IDLE, LISTENING, THINKING, USING TOOL, SPEAKING, EMERGENCY
  const [toolName, setToolName] = useState('');
  const [inputText, setInputText] = useState('');
  const [isListening, setIsListening] = useState(false);
  const [speechEnabled, setSpeechEnabled] = useState(true);
  const [isCompactAvatar, setIsCompactAvatar] = useState(false);
  const [activeChipCategory, setActiveChipCategory] = useState('all'); // all, electrical, tech, rates
  const [copiedIndex, setCopiedIndex] = useState(null);

  // --- ElevenLabs Human Voice State ---
  const [elevenVoice, setElevenVoice] = useState(() => {
    return localStorage.getItem('electrofix_eleven_voice') || 'pNInz6obpgDQGcFmaJgB'; // Adam (Warm & Friendly Male)
  });
  const [elevenApiKey, setElevenApiKey] = useState(() => {
    return localStorage.getItem('electrofix_eleven_key') || '';
  });
  const [elevenConfigured, setElevenConfigured] = useState(false);
  const [showVoiceSettings, setShowVoiceSettings] = useState(false);
  const [voiceIsPlaying, setVoiceIsPlaying] = useState(false);
  const [isSynthesizingVoice, setIsSynthesizingVoice] = useState(false);
  const [availableVoices, setAvailableVoices] = useState([
    { id: 'pNInz6obpgDQGcFmaJgB', name: 'Adam', description: 'Deep, warm & friendly (Recommended)', preview_avatar: '👨‍💼', gender: 'Male' },
    { id: 'ErXwobaYiN019PkySvjV', name: 'Antoni', description: 'Polite & conversational', preview_avatar: '🧑‍🔧', gender: 'Male' },
    { id: '21m00Tcm4TlvDq8ikWAM', name: 'Rachel', description: 'Clear, gentle & soothing', preview_avatar: '👩‍💼', gender: 'Female' },
    { id: 'IKne3meq5aSn9XLyUdCD', name: 'Charlie', description: 'Casual, approachable & natural', preview_avatar: '🧑', gender: 'Male' },
    { id: 'EXAVITQu4vr4xnSDxMaL', name: 'Bella', description: 'Expressive & friendly', preview_avatar: '👩', gender: 'Female' },
  ]);
  const audioPlayerRef = useRef(null);

  // --- Ollama Settings State ---
  const [ollamaModel, setOllamaModel] = useState(() => {
    return localStorage.getItem('electrofix_ollama_model') || 'llama3.2';
  });
  const [ollamaStatus, setOllamaStatus] = useState({
    running: false,
    checked: false,
    message: 'Checking Ollama...'
  });
  const [availableModels, setAvailableModels] = useState([
    'llama3.2',
    'llama3.1',
    'mistral',
    'deepseek-r1:8b',
    'qwen2.5:7b',
    'phi3'
  ]);

  // Chat message history with timestamps
  const [messages, setMessages] = useState([
    {
      role: 'assistant',
      content: `Namaste! Main hoon **Bijli Guru** (बिजली गुरु) — aapka intelligent AI assistant aur electrical guide.

Aap mujhse **general knowledge, technology, coding (jaise Laravel, Python), science, ya ghar ki kisi bhi electrical samasya** ke baare mein pooch sakte hain.

Likhkar ya 🎤 Mic dabakar Hindi, Hinglish ya English mein baat karein!`,
      state: 'IDLE',
      ai_provider: 'bijli_guru',
      model_used: 'intelligent_agent',
      time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }
  ]);

  const messagesEndRef = useRef(null);
  const recognitionRef = useRef(null);

  // Handle external force open or trigger query
  useEffect(() => {
    if (isForceOpen) {
      setIsOpen(true);
    }
  }, [isForceOpen]);

  useEffect(() => {
    if (externalTriggerQuery) {
      setIsOpen(true);
      sendMessage(externalTriggerQuery);
    }
  }, [externalTriggerQuery]);

  // Check Ollama status & load ElevenLabs voices on mount
  useEffect(() => {
    checkOllamaHealth();
    loadElevenLabsVoices();
  }, []);

  const loadElevenLabsVoices = async () => {
    try {
      const res = await api.get('/ai-agent/voices');
      if (res.data && res.data.success) {
        setElevenConfigured(res.data.configured);
        if (res.data.voices && res.data.voices.length > 0) {
          setAvailableVoices(res.data.voices);
        }
      }
    } catch (err) {
      console.warn('ElevenLabs voices load note:', err);
    }
  };

  const checkOllamaHealth = async () => {
    try {
      const res = await api.get('/ai-agent/ollama-status');
      if (res.data && res.data.success) {
        setOllamaStatus({
          running: res.data.running,
          checked: true,
          message: res.data.message
        });

        if (res.data.models && res.data.models.length > 0) {
          const names = res.data.models.map((m) => m.name);
          setAvailableModels((prev) => Array.from(new Set([...names, ...prev])));
        }
      }
    } catch (err) {
      setOllamaStatus({
        running: false,
        checked: true,
        message: 'Ollama local server not detected. Autonomous cloud intelligence active.'
      });
    }
  };

  const handleModelChange = (newModel) => {
    setOllamaModel(newModel);
    localStorage.setItem('electrofix_ollama_model', newModel);
  };

  // Auto scroll chat to bottom when new messages arrive
  useEffect(() => {
    if (isOpen) {
      messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }
  }, [messages, isOpen, agentState]);

  // Clean up speech synthesis, audio player & recognition on unmount
  useEffect(() => {
    return () => {
      if (audioPlayerRef.current) {
        audioPlayerRef.current.pause();
        audioPlayerRef.current = null;
      }
      if (window.speechSynthesis) window.speechSynthesis.cancel();
      if (recognitionRef.current) recognitionRef.current.stop();
    };
  }, []);

  const handleVoiceChange = (voiceId) => {
    setElevenVoice(voiceId);
    localStorage.setItem('electrofix_eleven_voice', voiceId);
  };

  const handleSaveApiKey = (key) => {
    setElevenApiKey(key);
    localStorage.setItem('electrofix_eleven_key', key);
    alert('ElevenLabs API Key saved successfully!');
    setShowVoiceSettings(false);
  };

  const stopActiveVoice = () => {
    if (audioPlayerRef.current) {
      audioPlayerRef.current.pause();
      audioPlayerRef.current = null;
    }
    if (window.speechSynthesis) {
      window.speechSynthesis.cancel();
    }
    setVoiceIsPlaying(false);
    setIsSynthesizingVoice(false);
    setAgentState((prev) => (prev === 'EMERGENCY' ? 'EMERGENCY' : 'IDLE'));
  };

  // --- 1. Speech Recognition (Speech to Text) ---
  const toggleVoiceInput = () => {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
      alert('Speech Recognition is not supported by your browser. Please use Google Chrome or Microsoft Edge.');
      return;
    }

    if (isListening) {
      if (recognitionRef.current) recognitionRef.current.stop();
      setIsListening(false);
      setAgentState('IDLE');
      return;
    }

    try {
      const recognition = new SpeechRecognition();
      recognition.lang = 'hi-IN'; // Listens to Hindi & Indian English seamlessly
      recognition.interimResults = false;
      recognition.continuous = false;

      recognition.onstart = () => {
        setIsListening(true);
        setAgentState('LISTENING');
      };

      recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript;
        setIsListening(false);
        if (transcript) {
          sendMessage(transcript);
        }
      };

      recognition.onerror = (err) => {
        console.warn('Speech recognition error:', err);
        setIsListening(false);
        setAgentState('IDLE');
      };

      recognition.onend = () => {
        setIsListening(false);
        if (agentState === 'LISTENING') setAgentState('IDLE');
      };

      recognitionRef.current = recognition;
      recognition.start();
    } catch (err) {
      console.error('Speech recognition exception:', err);
      setIsListening(false);
      setAgentState('IDLE');
    }
  };

  // --- 2. Text to Speech (ElevenLabs Human Natural Voice) ---
  const speakResponse = async (text) => {
    if (!speechEnabled) return;

    // Stop ongoing speech
    stopActiveVoice();

    setAgentState('SPEAKING');
    setIsSynthesizingVoice(true);

    try {
      // 1. Synthesize via ElevenLabs High-Fidelity Voice API
      const response = await api.post('/ai-agent/tts', {
        text,
        voice_id: elevenVoice,
        api_key: elevenApiKey.trim() || undefined,
      });

      setIsSynthesizingVoice(false);

      if (response.data && response.data.success && response.data.audio_base64) {
        const audio = new Audio(response.data.audio_base64);
        audioPlayerRef.current = audio;

        audio.onplay = () => {
          setAgentState('SPEAKING');
          setVoiceIsPlaying(true);
        };
        audio.onended = () => {
          setAgentState((prev) => (prev === 'EMERGENCY' ? 'EMERGENCY' : 'IDLE'));
          setVoiceIsPlaying(false);
          audioPlayerRef.current = null;
        };
        audio.onerror = () => {
          setVoiceIsPlaying(false);
          fallbackBrowserSpeech(response.data.clean_text || text);
        };

        await audio.play();
        return;
      }
    } catch (err) {
      console.warn('ElevenLabs API call error, falling back:', err);
      setIsSynthesizingVoice(false);
    }

    // 2. Fallback to Browser Speech Synthesis
    fallbackBrowserSpeech(text);
  };

  const fallbackBrowserSpeech = (text) => {
    if (!window.speechSynthesis) {
      setAgentState('IDLE');
      setVoiceIsPlaying(false);
      return;
    }

    const cleanText = text
      .replace(/[*#_`>]/g, '')
      .replace(/[\u{1F300}-\u{1F9FF}]/gu, '')
      .trim();

    if (!cleanText) {
      setAgentState('IDLE');
      setVoiceIsPlaying(false);
      return;
    }

    const utterance = new SpeechSynthesisUtterance(cleanText);
    utterance.rate = 1.0;
    utterance.pitch = 1.02;

    const voices = window.speechSynthesis.getVoices();
    const preferredVoice = voices.find((v) => v.lang.includes('hi') || v.lang.includes('en-IN')) || voices[0];
    if (preferredVoice) utterance.voice = preferredVoice;

    utterance.onstart = () => {
      setAgentState('SPEAKING');
      setVoiceIsPlaying(true);
    };
    utterance.onend = () => {
      setAgentState((prev) => (prev === 'EMERGENCY' ? 'EMERGENCY' : 'IDLE'));
      setVoiceIsPlaying(false);
    };
    utterance.onerror = () => {
      setAgentState('IDLE');
      setVoiceIsPlaying(false);
    };

    window.speechSynthesis.speak(utterance);
  };

  // --- 3. Send Message to Laravel AI Agent API ---
  const sendMessage = async (userText) => {
    const textToSend = userText || inputText;
    if (!textToSend.trim()) return;

    const timeString = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    // 1. Add User Message
    const updatedMessages = [
      ...messages,
      { role: 'user', content: textToSend, time: timeString }
    ];
    setMessages(updatedMessages);
    setInputText('');
    setAgentState('THINKING');

    // 2. Prepare payload
    const activeLocation = customerLocation || {
      area: 'Gomti Nagar, Lucknow',
      lat: 26.8530,
      lng: 80.9980
    };

    const historyPayload = updatedMessages.map((m) => ({
      role: m.role,
      content: m.content
    }));

    try {
      const response = await api.post('/ai-agent/chat', {
        message: textToSend,
        conversation_history: historyPayload,
        user_location: activeLocation,
        model: ollamaModel
      });

      const resData = response.data;

      if (resData.tool_used) {
        setToolName(resData.tool_used);
        setAgentState('USING TOOL');
      }

      const nextState = resData.state === 'EMERGENCY' ? 'EMERGENCY' : 'IDLE';

      // 3. Append Agent Response
      setMessages((prev) => [
        ...prev,
        {
          role: 'assistant',
          content: resData.message || resData.reply || 'I have processed your request.',
          state: resData.state,
          tool_used: resData.tool_used,
          tool_display: resData.tool_display,
          data: resData.data,
          ai_provider: resData.ai_provider || 'autonomous_agent',
          model_used: resData.model_used || ollamaModel,
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }
      ]);

      setAgentState(nextState);

      // 4. Speak aloud
      if (resData.message) {
        speakResponse(resData.message);
      }
    } catch (error) {
      console.error('Agent chat request error:', error);
      setMessages((prev) => [
        ...prev,
        {
          role: 'assistant',
          content: 'Kshama kijiye, server se connect karne me dikkat aayi. Kripya punah prayas karein.',
          state: 'IDLE',
          ai_provider: 'fallback',
          model_used: 'local',
          time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        }
      ]);
      setAgentState('IDLE');
    }
  };

  // --- Quick Prompt Chips Handler ---
  const handleQuickPrompt = (prompt) => {
    sendMessage(prompt);
  };

  // --- 1-Click Booking Confirmation ---
  const handleConfirmElectrician = (proName) => {
    sendMessage(`Yes, please book ${proName}`);
  };

  // --- Copy Message Content ---
  const copyMessage = (text, idx) => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text);
      setCopiedIndex(idx);
      setTimeout(() => setCopiedIndex(null), 2000);
    }
  };

  // Restart Conversation
  const resetConversation = () => {
    setMessages([
      {
        role: 'assistant',
        content: `Namaste! Conversation reset ho gaya hai.

Aap kisi bhi topic — general knowledge, technology, coding (jaise Laravel, Python), science, ya electrical issues — ke baare mein pooch sakte hain 😊`,
        state: 'IDLE',
        ai_provider: 'bijli_guru',
        model_used: ollamaModel,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
      }
    ]);
    setAgentState('IDLE');
  };

  // Close Console & Cancel Speech cleanly
  const handleClose = () => {
    if (isListening && recognitionRef.current) {
      recognitionRef.current.stop();
      setIsListening(false);
    }
    if (window.speechSynthesis) {
      window.speechSynthesis.cancel();
    }
    setIsOpen(false);
    if (onClose) onClose();
  };

  // Helper: Format message text with code blocks and bolding
  const renderFormattedMessage = (content) => {
    if (!content) return null;

    // Check for code blocks ```lang ... ```
    const codeBlockRegex = /```([a-zA-Z0-9_-]*)\n?([\s\S]*?)```/g;
    const parts = [];
    let lastIndex = 0;
    let match;

    while ((match = codeBlockRegex.exec(content)) !== null) {
      if (match.index > lastIndex) {
        parts.push({ type: 'text', text: content.substring(lastIndex, match.index) });
      }
      parts.push({
        type: 'code',
        lang: match[1] || 'code',
        code: match[2].trim()
      });
      lastIndex = match.index + match[0].length;
    }

    if (lastIndex < content.length) {
      parts.push({ type: 'text', text: content.substring(lastIndex) });
    }

    return (
      <div className="chat-rich-body">
        {parts.map((p, idx) => {
          if (p.type === 'code') {
            return (
              <div key={idx} className="chat-code-card">
                <div className="chat-code-header">
                  <span className="code-lang-tag">💻 {p.lang || 'code'}</span>
                  <button
                    type="button"
                    className="btn-code-copy"
                    onClick={() => navigator.clipboard?.writeText(p.code)}
                    title="Copy code to clipboard"
                  >
                    📋 Copy
                  </button>
                </div>
                <pre className="chat-code-pre"><code>{p.code}</code></pre>
              </div>
            );
          }

          // Plain text with headers, numbered lists, bullets, inline code & bold
          const parseInlineFormatting = (inlineText, keyPrefix) => {
            if (!inlineText) return '';
            const regex = /(\*\*[^*]+\*\*|`[^`]+`)/g;
            const elements = [];
            let lastIdx = 0;
            let m;
            while ((m = regex.exec(inlineText)) !== null) {
              if (m.index > lastIdx) {
                elements.push(inlineText.substring(lastIdx, m.index));
              }
              const token = m[0];
              if (token.startsWith('**') && token.endsWith('**')) {
                elements.push(
                  <strong key={`${keyPrefix}-b-${m.index}`} className="chat-bold-highlight">
                    {token.slice(2, -2)}
                  </strong>
                );
              } else if (token.startsWith('`') && token.endsWith('`')) {
                elements.push(
                  <code key={`${keyPrefix}-c-${m.index}`} className="chat-inline-code">
                    {token.slice(1, -1)}
                  </code>
                );
              }
              lastIdx = m.index + token.length;
            }
            if (lastIdx < inlineText.length) {
              elements.push(inlineText.substring(lastIdx));
            }
            return elements.length > 0 ? elements : inlineText;
          };

          const lines = p.text.split('\n');
          return (
            <div key={idx} className="chat-text-paragraphs">
              {lines.map((line, lIdx) => {
                const trimmed = line.trim();
                if (!trimmed) return <div key={lIdx} style={{ height: '6px' }} />;

                // Check markdown heading (## or ###)
                if (trimmed.startsWith('### ') || trimmed.startsWith('## ')) {
                  const headingText = trimmed.replace(/^#+\s*/, '');
                  return (
                    <h5 key={lIdx} className="chat-subheading">
                      {parseInlineFormatting(headingText, `h-${lIdx}`)}
                    </h5>
                  );
                }

                // Check numbered list (e.g. "1. Step")
                const numMatch = trimmed.match(/^(\d+)\.\s+(.*)/);
                if (numMatch) {
                  return (
                    <div key={lIdx} className="chat-num-line">
                      <span className="chat-num-badge">{numMatch[1]}</span>
                      <span className="chat-num-content">
                        {parseInlineFormatting(numMatch[2], `n-${lIdx}`)}
                      </span>
                    </div>
                  );
                }

                // Check bullet (-, *, •)
                const isBullet = trimmed.startsWith('•') || trimmed.startsWith('-') || trimmed.startsWith('*');
                if (isBullet) {
                  const bulletText = trimmed.replace(/^[-*•]\s*/, '');
                  return (
                    <p key={lIdx} className="chat-bullet-line">
                      <span className="bullet-dot">▸</span>
                      <span>{parseInlineFormatting(bulletText, `bl-${lIdx}`)}</span>
                    </p>
                  );
                }

                return (
                  <p key={lIdx} className="chat-para-line">
                    {parseInlineFormatting(trimmed, `p-${lIdx}`)}
                  </p>
                );
              })}
            </div>
          );
        })}
      </div>
    );
  };

  // Quick prompt chips list by category
  const QUICK_PROMPTS = [
    { cat: 'electrical', icon: '🌀', text: 'Fan slow chal raha hai aur humming aawaz kar raha hai', label: 'Fan Slow/Humming' },
    { cat: 'electrical', icon: '⚠️', text: 'Switchboard se spark aa raha hai aur jalne ki smell hai', label: 'Sparking Socket' },
    { cat: 'electrical', icon: '🔌', text: 'MCB baar baar trip ho rahi hai jab AC on karte hain', label: 'MCB Tripping' },
    { cat: 'electrical', icon: '🔋', text: 'Inverter battery backup nahi de rahi kya karu?', label: 'Inverter Issue' },
    { cat: 'electrical', icon: '✉️', text: 'Mujhe support desk ko contact email bhejna hai', label: 'Send Contact Email' },
    { cat: 'tech', icon: '⚡', text: 'What is Laravel in simple words?', label: 'What is Laravel?' },
    { cat: 'tech', icon: '🐍', text: 'Write a python hello world function', label: 'Python Code' },
    { cat: 'tech', icon: '💡', text: 'AC aur DC current mein kya fark hota hai?', label: 'AC vs DC Current' },
    { cat: 'tech', icon: '🌍', text: 'What is photosynthesis?', label: 'Photosynthesis' },
    { cat: 'rates', icon: '💰', text: 'Lucknow mein doorstep electrical services ka rate card kya hai?', label: 'Lucknow Rates' },
    { cat: 'rates', icon: '⚡', text: 'Find verified electricians near me in Lucknow', label: 'Electrician Near Me' },
    { cat: 'rates', icon: '🛡️', text: 'Ghar ke earthing test aur safety audit ka charge kitna hai?', label: 'Earthing Audit' },
  ];

  const visibleChips = activeChipCategory === 'all'
    ? QUICK_PROMPTS
    : QUICK_PROMPTS.filter((p) => p.cat === activeChipCategory);

  return (
    <div className={`electrofix-robot-widget ${isOpen ? 'widget-open' : ''}`}>
      {/* Floating Bijli Guru Launcher Button */}
      {!isOpen && (
        <button
          type="button"
          className="robot-launcher-btn"
          onClick={() => setIsOpen(true)}
          aria-label="Open Bijli Guru AI Assistant"
        >
          <div className="launcher-guru-avatar">
            <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" className="launcher-guru-img" />
            <span className="launcher-halo-badge">✨</span>
          </div>
          <div className="launcher-text-col">
            <div className="launcher-top-meta">
              <span className="launcher-badge">AUTONOMOUS AI</span>
              <span
                className="launcher-live-pulse"
                style={{ background: ollamaStatus.running ? '#10b981' : '#3b82f6' }}
                title={ollamaStatus.running ? 'Ollama Online' : 'AI Agent Ready'}
              ></span>
            </div>
            <span className="launcher-title">बिजली गुरु (AI Chat)</span>
          </div>
        </button>
      )}

      {/* Main Bijli Guru Console Window */}
      {isOpen && (
        <>
          <div className="robot-mobile-backdrop" onClick={handleClose} />
          <div className="robot-console-card">
            {/* Top Bar with Brand & Actions */}
            <div className="robot-console-header">
              <div className="console-title-group">
                <div className="console-avatar-ring">
                  <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" className="console-bot-icon" />
                  <span className="avatar-active-dot"></span>
                </div>
                <div>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                    <h4 className="console-title">Bijli Guru (बिजली गुरु)</h4>
                    <span className="console-verified-badge">⚡ Pro AI</span>
                  </div>
                  <span className="console-subtitle">Autonomous General & Electrical Intelligence</span>
                </div>
              </div>

              <div className="console-actions-group">
                <button
                  type="button"
                  className="console-btn-icon"
                  onClick={() => setIsCompactAvatar(!isCompactAvatar)}
                  title={isCompactAvatar ? 'Show Full Avatar Chamber' : 'Compact View (More Chat Space)'}
                >
                  {isCompactAvatar ? '🖼️' : '↕️'}
                </button>
                <button
                  type="button"
                  className="console-btn-icon"
                  onClick={() => setSpeechEnabled(!speechEnabled)}
                  title={speechEnabled ? 'Mute Voice' : 'Enable Voice'}
                >
                  {speechEnabled ? '🔊' : '🔇'}
                </button>
                <button
                  type="button"
                  className="console-btn-icon"
                  onClick={resetConversation}
                  title="Restart Conversation"
                >
                  ↺
                </button>
                <button
                  type="button"
                  className="console-btn-icon"
                  onClick={handleClose}
                  title="Close Bijli Guru"
                  aria-label="Close"
                >
                  ✕
                </button>
              </div>
            </div>

            {/* Ollama Engine Selector Strip */}
            <div className="ollama-control-bar">
              <div className="ollama-left-status">
                <span
                  className={`ollama-status-indicator ${ollamaStatus.running ? 'online' : 'offline'}`}
                  title={ollamaStatus.message}
                >
                  <span style={{ fontSize: '10px' }}>{ollamaStatus.running ? '●' : '⚡'}</span>
                  {ollamaStatus.running ? 'Ollama Online' : 'Cloud AI Ready'}
                </span>
                <span className="model-label-text">Model:</span>
                <select
                  className="ollama-model-select"
                  value={ollamaModel}
                  onChange={(e) => handleModelChange(e.target.value)}
                  title="Select AI Model"
                >
                  {availableModels.map((m) => (
                    <option key={m} value={m}>
                      {m}
                    </option>
                  ))}
                </select>
              </div>
              <button
                type="button"
                className="btn-test-ollama"
                onClick={checkOllamaHealth}
                title="Ping server status"
              >
                Ping
              </button>
            </div>

            {/* ElevenLabs Human Voice Bar */}
            <div className="elevenlabs-voice-bar">
              <div className="eleven-left-meta">
                <span className="eleven-brand-badge">
                  <span className="eleven-spark-icon">🎙️</span>
                  <span>ElevenLabs</span>
                </span>
                <span className="voice-label-text">Voice:</span>
                <select
                  className="eleven-voice-select"
                  value={elevenVoice}
                  onChange={(e) => handleVoiceChange(e.target.value)}
                  title="Select ElevenLabs Human Voice"
                >
                  {availableVoices.map((v) => (
                    <option key={v.id} value={v.id}>
                      {v.preview_avatar ? `${v.preview_avatar} ` : ''}{v.name} ({v.gender || 'Human'})
                    </option>
                  ))}
                </select>
              </div>

              <div className="eleven-right-actions">
                {voiceIsPlaying ? (
                  <button
                    type="button"
                    className="btn-voice-preview playing"
                    onClick={stopActiveVoice}
                    title="Stop Audio Playback"
                  >
                    ⏹ Stop
                  </button>
                ) : (
                  <button
                    type="button"
                    className="btn-voice-preview"
                    disabled={isSynthesizingVoice}
                    onClick={() => speakResponse('Namaste! Main Bijli Guru hoon. Aapki kya madad kar sakta hoon?')}
                    title="Preview ElevenLabs Voice"
                  >
                    {isSynthesizingVoice ? '⏳' : '▶ Test'}
                  </button>
                )}
                <button
                  type="button"
                  className={`btn-voice-settings ${elevenApiKey || elevenConfigured ? 'configured' : ''}`}
                  onClick={() => setShowVoiceSettings(!showVoiceSettings)}
                  title="ElevenLabs API & Voice Settings"
                >
                  ⚙️
                </button>
              </div>
            </div>

            {/* ElevenLabs Settings Popover Drawer */}
            {showVoiceSettings && (
              <div className="eleven-settings-drawer">
                <div className="eleven-drawer-header">
                  <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                    <span style={{ fontSize: '18px' }}>🎙️</span>
                    <div>
                      <h5 style={{ margin: 0, fontSize: '13px', fontWeight: 800, color: '#0f172a' }}>
                        ElevenLabs Human Voice Settings
                      </h5>
                      <span style={{ fontSize: '11px', color: '#64748b' }}>
                        Ultra-human conversational speech (Hindi, Hinglish, English)
                      </span>
                    </div>
                  </div>
                  <button
                    type="button"
                    className="btn-drawer-close"
                    onClick={() => setShowVoiceSettings(false)}
                  >
                    ✕
                  </button>
                </div>

                <div className="eleven-drawer-body">
                  <label className="drawer-field-label">
                    Select Human Voice Persona:
                  </label>
                  <div className="voices-persona-grid">
                    {availableVoices.map((v) => (
                      <div
                        key={v.id}
                        className={`voice-persona-card ${elevenVoice === v.id ? 'active' : ''}`}
                        onClick={() => handleVoiceChange(v.id)}
                      >
                        <div style={{ fontSize: '20px' }}>{v.preview_avatar || '🧑'}</div>
                        <div style={{ flex: 1, minWidth: 0 }}>
                          <div style={{ fontSize: '12px', fontWeight: 800, color: '#0f172a' }}>
                            {v.name} <span style={{ fontSize: '10px', color: '#64748b', fontWeight: 500 }}>({v.gender})</span>
                          </div>
                          <div style={{ fontSize: '10.5px', color: '#64748b', textOverflow: 'ellipsis', overflow: 'hidden', whiteSpace: 'nowrap' }}>
                            {v.description}
                          </div>
                        </div>
                        {elevenVoice === v.id && <span style={{ color: '#2563eb', fontWeight: 900 }}>✓</span>}
                      </div>
                    ))}
                  </div>

                  <div style={{ marginTop: '12px' }}>
                    <label className="drawer-field-label">
                      ElevenLabs API Key (Optional):
                    </label>
                    <div className="api-key-input-row">
                      <input
                        type="password"
                        className="eleven-api-input"
                        placeholder="xi-api-key (Leave blank for server/neural voice)"
                        value={elevenApiKey}
                        onChange={(e) => setElevenApiKey(e.target.value)}
                      />
                      <button
                        type="button"
                        className="btn-save-key"
                        onClick={() => handleSaveApiKey(elevenApiKey)}
                      >
                        Save
                      </button>
                    </div>
                    <div style={{ fontSize: '11px', color: '#64748b', marginTop: '4px' }}>
                      💡 You can get a key from <a href="https://elevenlabs.io" target="_blank" rel="noreferrer" style={{ color: '#2563eb', textDecoration: 'underline' }}>elevenlabs.io</a> or set <code>ELEVENLABS_API_KEY</code> in <code>backend/.env</code>.
                    </div>
                  </div>
                </div>
              </div>
            )}

            {/* Sadhu Avatar Chamber (Collapsible for compact view) */}
            {!isCompactAvatar && (
              <div
                className={`robot-chamber ${
                  agentState === 'SPEAKING'
                    ? 'speaking-mode'
                    : agentState === 'LISTENING'
                    ? 'listening-mode'
                    : agentState === 'EMERGENCY'
                    ? 'emergency-mode'
                    : agentState === 'THINKING'
                    ? 'thinking-mode'
                    : ''
                }`}
              >
                <div className="guru-avatar-wrapper">
                  <div className="guru-portrait-ring">
                    <div className="guru-halo-aura"></div>
                    <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" className="guru-avatar-img" />
                    <span className="guru-aura-badge">
                      {agentState === 'LISTENING' ? '🎙️' : agentState === 'THINKING' ? '💡' : agentState === 'SPEAKING' ? '🗣️' : agentState === 'EMERGENCY' ? '⚠️' : '🌸'}
                    </span>
                  </div>
                </div>

                {/* Live State Badge */}
                <div className="robot-state-badge">
                  <span
                    className="state-dot"
                    style={{
                      background:
                        agentState === 'EMERGENCY'
                          ? '#ef4444'
                          : agentState === 'LISTENING'
                          ? '#10b981'
                          : agentState === 'THINKING'
                          ? '#a855f7'
                          : agentState === 'USING TOOL'
                          ? '#f59e0b'
                          : '#3b82f6'
                    }}
                  ></span>
                  <span>
                    {agentState === 'LISTENING' && '🎤 LISTENING (HINDI/ENGLISH)...'}
                    {agentState === 'THINKING' && `💡 THINKING & REASONING...`}
                    {agentState === 'USING TOOL' && `⚡ EXECUTING ${toolName ? toolName.toUpperCase() : 'DATABASE'} TOOL...`}
                    {agentState === 'SPEAKING' && `🗣️ ELEVENLABS SPEAKING (${availableVoices.find(v => v.id === elevenVoice)?.name || 'HUMAN'})...`}
                    {agentState === 'EMERGENCY' && '⚠️ SAFETY PROTOCOL ACTIVE'}
                    {agentState === 'IDLE' && `⚡ BIJLI GURU ONLINE • ELEVENLABS AI`}
                  </span>
                </div>

                {/* Equalizer Visualizer */}
                <div className="robot-audio-visualizer">
                  {[...Array(12)].map((_, i) => (
                    <span key={i} className="eq-bar"></span>
                  ))}
                </div>
              </div>
            )}

            {/* Tool Execution Notification Banner */}
            {agentState === 'USING TOOL' && (
              <div className="robot-tool-banner">
                <span className="tool-spinner">⚡</span>
                <span>Executing tool: {toolName || 'Application Database Search'} in Lucknow...</span>
              </div>
            )}

            {/* Chat Messages Box */}
            <div className="robot-messages-box">
              {messages.map((msg, index) => (
                <div
                  key={index}
                  className={msg.role === 'user' ? 'user-msg-bubble' : 'agent-msg-bubble'}
                >
                  {msg.role === 'assistant' && (
                    <div className="agent-msg-avatar">
                      {msg.state === 'EMERGENCY' ? '⚠️' : <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" />}
                    </div>
                  )}
                  <div className={msg.role === 'user' ? 'user-msg-content' : 'agent-msg-text'}>
                    {/* Emergency Banner */}
                    {msg.state === 'EMERGENCY' && (
                      <div className="chat-emergency-warning">
                        🚨 <strong>SAFETY WARNING:</strong> Do NOT touch sparking switches or exposed wires. Main MCB turn off karein agar safe ho!
                      </div>
                    )}

                    {/* Rich Formatted Text */}
                    {msg.role === 'assistant'
                      ? renderFormattedMessage(msg.content)
                      : <div style={{ whiteSpace: 'pre-line' }}>{msg.content}</div>}

                    {/* Provider Tag & Action Bar */}
                    {msg.role === 'assistant' && (
                      <div className="msg-footer-bar">
                        <div className="msg-provider-tag">
                          <span>
                            {msg.ai_provider === 'ollama'
                              ? '🦙 Ollama Local'
                              : msg.ai_provider === 'knowledge_engine'
                              ? '🔍 Verified Facts'
                              : msg.ai_provider === 'dynamic_ai'
                              ? '🤖 Cloud LLM'
                              : '⚡ Bijli Guru'}
                          </span>
                          {msg.time && <span className="msg-time-tag">• {msg.time}</span>}
                        </div>
                        <div className="msg-actions-inline">
                          <button
                            type="button"
                            className="btn-msg-copy"
                            onClick={() => copyMessage(msg.content, index)}
                            title="Copy reply text"
                          >
                            {copiedIndex === index ? '✓ Copied' : '📋 Copy'}
                          </button>
                          {speechEnabled && (
                            <button
                              type="button"
                              className="btn-msg-speak"
                              onClick={() => speakResponse(msg.content)}
                              title="Listen aloud"
                            >
                              🔊
                            </button>
                          )}
                        </div>
                      </div>
                    )}

                    {/* Electrician Suggestions List */}
                    {msg.data?.electricians && msg.data.electricians.length > 0 && (
                      <div className="chat-pro-suggestions-list">
                        <div className="chat-pro-suggestions-title">
                          ⚡ Verified Electricians Near You ({customerLocation?.areaName || 'Lucknow'}):
                        </div>
                        {msg.data.electricians.map((pro, pIdx) => (
                          <div key={pIdx} className="robot-pro-card">
                            <div>
                              <div className="robot-pro-name">
                                ⚡ {pro.name}{' '}
                                <span className="pro-rating-badge">
                                  ⭐ {pro.rating || '4.8'}
                                </span>
                              </div>
                              <div className="robot-pro-meta">
                                📍 {pro.area || 'Lucknow'}{' '}
                                {pro.distance_km ? `• ${pro.distance_km} km away` : ''} • ~{pro.eta_mins || '20'} mins arrival
                              </div>
                            </div>
                            <button
                              type="button"
                              className="btn-confirm-robot-booking"
                              onClick={() => {
                                if (onSelectElectrician) onSelectElectrician(pro);
                                handleConfirmElectrician(pro.name);
                              }}
                            >
                              Book {pro.name.split(' ')[0]}
                            </button>
                          </div>
                        ))}
                      </div>
                    )}

                    {/* Booking Confirmation Success Card */}
                    {msg.data?.booking && (
                      <div className="robot-booking-success-card">
                        <div className="booking-card-head">
                          ✅ Service Booking Confirmed!
                        </div>
                        <div className="booking-card-row">
                          <strong>Booking ID:</strong> <span className="ref-code">{msg.data.booking.booking_reference || msg.data.booking.id}</span>
                        </div>
                        <div className="booking-card-row">
                          <strong>Electrician:</strong> {msg.data.booking.electrician_name || 'Assigned Master Pro'}
                        </div>
                        <div className="booking-card-row">
                          <strong>Service:</strong> {msg.data.booking.service_type || msg.data.booking.service_name || 'Electrical Repair'}
                        </div>
                        <div className="booking-card-row">
                          <strong>Estimated Arrival:</strong> ~20 to 25 mins
                        </div>
                        <div className="booking-card-footer">
                          Technician will call before arrival. Pay safely via UPI or cash only after job completion.
                        </div>
                      </div>
                    )}

                    {/* Contact Email Dispatched Success Card */}
                    {(msg.tool_used === 'send_contact_email' || msg.data?.contact_inquiry) && (
                      <div className="robot-email-success-card">
                        <div className="email-card-head">
                          <span>📧</span>
                          <span>Contact Email Dispatched to Support Desk!</span>
                        </div>
                        <div className="email-card-body">
                          {msg.data?.contact_inquiry?.ticket_reference && (
                            <div className="email-card-row">
                              <strong>Ticket ID:</strong>
                              <span className="email-ref-code">{msg.data.contact_inquiry.ticket_reference}</span>
                            </div>
                          )}
                          {msg.data?.contact_inquiry?.email && (
                            <div className="email-card-row">
                              <strong>Sent To:</strong>
                              <span>{msg.data.contact_inquiry.email} & Support Desk</span>
                            </div>
                          )}
                          {msg.data?.contact_inquiry?.ai_priority && (
                            <div className="email-card-row">
                              <strong>AI Priority:</strong>
                              <span className={`priority-tag-pill ${msg.data.contact_inquiry.ai_priority.toLowerCase()}`}>
                                {msg.data.contact_inquiry.ai_priority}
                              </span>
                            </div>
                          )}
                          {msg.data?.contact_inquiry?.ai_diagnosis && (
                            <div className="email-card-row ai-diag-row">
                              <strong>AI Diagnosis:</strong>
                              <p>{msg.data.contact_inquiry.ai_diagnosis}</p>
                            </div>
                          )}
                        </div>
                        <div className="email-card-footer">
                          ✅ Logged in Lucknow database. Support team typically responds in 15–30 mins.
                        </div>
                      </div>
                    )}
                  </div>
                </div>
              ))}
              <div ref={messagesEndRef} />
            </div>

            {/* Quick Prompt Category Tabs */}
            <div className="chips-category-bar">
              <button
                type="button"
                className={`category-pill ${activeChipCategory === 'all' ? 'active' : ''}`}
                onClick={() => setActiveChipCategory('all')}
              >
                All
              </button>
              <button
                type="button"
                className={`category-pill ${activeChipCategory === 'electrical' ? 'active' : ''}`}
                onClick={() => setActiveChipCategory('electrical')}
              >
                ⚡ Electrical
              </button>
              <button
                type="button"
                className={`category-pill ${activeChipCategory === 'tech' ? 'active' : ''}`}
                onClick={() => setActiveChipCategory('tech')}
              >
                💻 Tech & Code
              </button>
              <button
                type="button"
                className={`category-pill ${activeChipCategory === 'rates' ? 'active' : ''}`}
                onClick={() => setActiveChipCategory('rates')}
              >
                💰 Rates
              </button>
            </div>

            {/* Quick Prompt Chips */}
            <div className="robot-quick-chips">
              {visibleChips.map((chip, cIdx) => (
                <button
                  key={cIdx}
                  type="button"
                  className="quick-chip"
                  onClick={() => handleQuickPrompt(chip.text)}
                  title={chip.text}
                >
                  <span>{chip.icon}</span>
                  <span>{chip.label}</span>
                </button>
              ))}
            </div>

            {/* Input Bar */}
            <div className="robot-input-bar">
              <button
                type="button"
                className={`robot-mic-btn ${isListening ? 'recording' : ''}`}
                onClick={toggleVoiceInput}
                title="Voice Input (Hindi/Hinglish/English)"
              >
                <span className="mic-pulse-ring"></span>
                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z" />
                  <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z" />
                </svg>
              </button>

              <div className="input-wrapper-field">
                <input
                  type="text"
                  value={inputText}
                  onChange={(e) => setInputText(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && sendMessage()}
                  placeholder={
                    isListening
                      ? 'Sun rahe hain... Boliye'
                      : 'Bijli Guru se poochhein (Laravel, code, fan, rates)...'
                  }
                />
              </div>

              <button
                type="button"
                className="robot-send-btn"
                onClick={() => sendMessage()}
                disabled={!inputText.trim()}
                title="Send message"
              >
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
                </svg>
              </button>
            </div>
          </div>
        </>
      )}
    </div>
  );
}
