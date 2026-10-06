import React, { useState, useEffect, useRef } from 'react';
import api from '../services/api';
import './ElectroFixRobot.css';

/**
 * ElectroFix AI - Friendly, Autonomous Electrical Service Robot Assistant
 * Features:
 *  1. Text & Voice conversation (Hindi, Hinglish, English)
 *  2. 6 Animated States: IDLE, LISTENING, THINKING, USING TOOL, SPEAKING, EMERGENCY
 *  3. Controlled Laravel Tools execution (find electricians, check availability, create booking)
 *  4. 1-Click Booking Confirmation
 *  5. Speech Recognition & Text-to-Speech
 */
export default function ElectroFixRobot({ customerLocation, onSelectElectrician }) {
  // --- UI & Agent States ---
  const [isOpen, setIsOpen] = useState(false);
  const [agentState, setAgentState] = useState('IDLE'); // IDLE, LISTENING, THINKING, USING TOOL, SPEAKING, EMERGENCY
  const [toolName, setToolName] = useState('');
  const [inputText, setInputText] = useState('');
  const [isListening, setIsListening] = useState(false);
  const [speechEnabled, setSpeechEnabled] = useState(true);

  // Chat message history
  const [messages, setMessages] = useState([
    {
      role: 'assistant',
      content: `🌸 Pranam! Main hoon Bijli Guru (बिजली गुरु) — ElectroLKO ka aapka guide.
      
Aapke ghar me koi fan aawaz kar raha hai, switchboard se spark aa rahi hai, ya verified electrician chahiye?

Mujhe likhkar ya 🎤 Mic dabakar Hindi, Hinglish ya English me batayein. Main samasya ki jaanch aur saste sahi mistri provide karta hoon!`,
      state: 'IDLE'
    }
  ]);

  const messagesEndRef = useRef(null);
  const recognitionRef = useRef(null);

  // Auto scroll chat to bottom when new messages arrive
  useEffect(() => {
    if (isOpen) {
      messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
    }
  }, [messages, isOpen, agentState]);

  // Clean up speech synthesis & recognition on unmount
  useEffect(() => {
    return () => {
      if (window.speechSynthesis) window.speechSynthesis.cancel();
      if (recognitionRef.current) recognitionRef.current.stop();
    };
  }, []);

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

  // --- 2. Text to Speech (Natural Voice Synthesis) ---
  const speakResponse = (text) => {
    if (!speechEnabled || !window.speechSynthesis) return;

    window.speechSynthesis.cancel();

    // Remove markdown syntax & emojis for clear speech
    const cleanText = text
      .replace(/[*#_`>]/g, '')
      .replace(/[\u{1F300}-\u{1F9FF}]/gu, '')
      .trim();

    if (!cleanText) return;

    const utterance = new SpeechSynthesisUtterance(cleanText);
    utterance.rate = 1.0;
    utterance.pitch = 1.02;

    const voices = window.speechSynthesis.getVoices();
    const preferredVoice = voices.find((v) => v.lang.includes('hi') || v.lang.includes('en-IN')) || voices[0];
    if (preferredVoice) utterance.voice = preferredVoice;

    utterance.onstart = () => setAgentState('SPEAKING');
    utterance.onend = () => setAgentState((prev) => (prev === 'EMERGENCY' ? 'EMERGENCY' : 'IDLE'));
    utterance.onerror = () => setAgentState('IDLE');

    window.speechSynthesis.speak(utterance);
  };

  // --- 3. Send Message to Laravel AI Agent API ---
  const sendMessage = async (userText) => {
    const textToSend = userText || inputText;
    if (!textToSend.trim()) return;

    // 1. Add User Message
    const updatedMessages = [...messages, { role: 'user', content: textToSend }];
    setMessages(updatedMessages);
    setInputText('');
    setAgentState('THINKING');

    // 2. Prepare payload
    const activeLocation = customerLocation || {
      area: 'Gomti Nagar, Lucknow',
      lat: 26.8530,
      lng: 80.9980
    };

    // Format conversation history for memory
    const historyPayload = updatedMessages.map((m) => ({
      role: m.role,
      content: m.content
    }));

    try {
      const response = await api.post('/ai-agent/chat', {
        message: textToSend,
        conversation_history: historyPayload,
        user_location: activeLocation
      });

      const resData = response.data;

      // Update Agent State based on response
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
          content: resData.message || 'I have processed your request.',
          state: resData.state,
          tool_used: resData.tool_used,
          data: resData.data
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
          content: 'Sorry, I had trouble reaching the ElectroLKO server. Please try again.',
          state: 'IDLE'
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

  // Restart Conversation
  const resetConversation = () => {
    setMessages([
      {
        role: 'assistant',
        content: '🌸 Pranam! Main hoon Bijli Guru (बिजली गुरु) — ElectroLKO ka aapka guide. Aapke ghar me kya electrical pareshani hai? Batayiye, main turant madad karta hoon!',
        state: 'IDLE'
      }
    ]);
    setAgentState('IDLE');
  };

  return (
    <div className="electrofix-robot-widget">
      {/* Floating Launcher Button */}
      <button
        type="button"
        className="robot-launcher-btn"
        onClick={() => setIsOpen(!isOpen)}
        aria-label="Open Bijli Guru AI Assistant"
      >
        <div className="launcher-guru-avatar">
          <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" className="launcher-guru-img" />
          <span className="launcher-halo-badge">🌸</span>
        </div>
        <div className="launcher-text-col">
          <span className="launcher-badge">AI GUIDE • लखनऊ</span>
          <span className="launcher-title">बिजली गुरु (Ask AI)</span>
        </div>
        <span className="launcher-live-pulse" title="Bijli Guru Active"></span>
      </button>

      {/* Main Bijli Guru Console Window */}
      {isOpen && (
        <div className="robot-console-card">
          {/* Header Bar */}
          <div className="robot-console-header">
            <div className="console-title-group">
              <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" className="console-bot-icon" />
              <div>
                <h4 className="console-title">Bijli Guru (बिजली गुरु)</h4>
                <span className="console-subtitle">Friendly & Calm Electrical Guide • ElectroLKO</span>
              </div>
            </div>
            <div className="console-actions-group">
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
                onClick={() => setIsOpen(false)}
                title="Close"
              >
                ✕
              </button>
            </div>
          </div>

          {/* Sadhu Mahatma Avatar Chamber */}
          <div
            className={`robot-chamber ${
              agentState === 'SPEAKING'
                ? 'speaking-mode'
                : agentState === 'LISTENING'
                ? 'listening-mode'
                : agentState === 'EMERGENCY'
                ? 'emergency-mode'
                : ''
            }`}
          >
            <div className="guru-avatar-wrapper">
              <div className="guru-portrait-ring">
                <div className="guru-halo-aura"></div>
                <img src="/images/sadhu_guru.jpg" alt="Bijli Guru" className="guru-avatar-img" />
                <span className="guru-aura-badge">🙏</span>
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
                      : '#f59e0b'
                }}
              ></span>
              <span>
                {agentState === 'LISTENING' && '🎤 SUN RAHE HAIN (LISTENING)...'}
                {agentState === 'THINKING' && '💡 SOCH RAHE HAIN (THINKING)...'}
                {agentState === 'USING TOOL' && `⚡ ${toolName ? toolName.toUpperCase() : 'JAANCH'}...`}
                {agentState === 'SPEAKING' && '🗣️ SAMJHA RAHE HAIN...'}
                {agentState === 'EMERGENCY' && '⚠️ SAVDHANI • EMERGENCY ALERT'}
                {agentState === 'IDLE' && '🧘 BIJLI GURU • READY'}
              </span>
            </div>

            {/* Equalizer Visualizer */}
            <div className="robot-audio-visualizer">
              {[...Array(12)].map((_, i) => (
                <span key={i} className="eq-bar"></span>
              ))}
            </div>
          </div>

          {/* Tool Execution Notification Banner */}
          {agentState === 'USING TOOL' && (
            <div className="robot-tool-banner">
              <span className="tool-spinner">⚡</span>
              <span>Executing: {toolName || 'Application Tool'} in Lucknow DB...</span>
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
                <div className={msg.role === 'user' ? '' : 'agent-msg-text'}>
                  {msg.state === 'EMERGENCY' && (
                    <div
                      style={{
                        background: 'rgba(239,68,68,0.2)',
                        border: '1px solid #ef4444',
                        borderRadius: '8px',
                        padding: '8px 10px',
                        marginBottom: '8px',
                        color: '#fca5a5',
                        fontWeight: '700'
                      }}
                    >
                      🚨 SAFETY WARNING: Do NOT touch exposed wires or sparking switches. Main power turn off karein agar safe ho!
                    </div>
                  )}

                  <div style={{ whiteSpace: 'pre-line' }}>{msg.content}</div>

                  {/* Electrician Suggestions List */}
                  {msg.data?.electricians && msg.data.electricians.length > 0 && (
                    <div style={{ marginTop: '10px', display: 'flex', flexDirection: 'column', gap: '6px' }}>
                      {msg.data.electricians.map((pro, pIdx) => (
                        <div key={pIdx} className="robot-pro-card">
                          <div>
                            <div className="robot-pro-name">
                              ⚡ {pro.name}{' '}
                              <span style={{ color: '#f59e0b', fontSize: '11px' }}>
                                ⭐ {pro.rating || '4.8'}
                              </span>
                            </div>
                            <div className="robot-pro-meta">
                              📍 {pro.area || 'Lucknow'}{' '}
                              {pro.distance_km ? `• ${pro.distance_km} km` : ''} • ~₹
                              {pro.hourly_rate || '199'}/hr
                            </div>
                          </div>
                          <button
                            type="button"
                            className="btn-confirm-robot-booking"
                            onClick={() => handleConfirmElectrician(pro.name)}
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
                      <div style={{ fontWeight: 800, fontSize: '13px', marginBottom: '4px' }}>
                        ✅ Booking Confirmed!
                      </div>
                      <div>
                        <strong>Booking ID:</strong> {msg.data.booking.booking_reference || msg.data.booking.id}
                      </div>
                      <div>
                        <strong>Electrician:</strong> {msg.data.booking.electrician_name || 'Assigned Pro'}
                      </div>
                      <div>
                        <strong>Service:</strong> {msg.data.booking.service_name || 'Electrical Repair'}
                      </div>
                      <div>
                        <strong>Scheduled Time:</strong> {msg.data.booking.scheduled_time || 'Within 30 mins'}
                      </div>
                      <div style={{ marginTop: '4px', fontSize: '11px', color: '#cbd5e1' }}>
                        Technician is dispatched to {msg.data.booking.customer_address || 'your address in Lucknow'}.
                      </div>
                    </div>
                  )}
                </div>
              </div>
            ))}
            <div ref={messagesEndRef} />
          </div>

          {/* Quick Prompt Chips */}
          <div className="robot-quick-chips">
            <button
              type="button"
              className="quick-chip"
              onClick={() => handleQuickPrompt('Fan kharab hai, humming sound aa rahi hai')}
            >
              🌀 Fan Humming
            </button>
            <button
              type="button"
              className="quick-chip"
              onClick={() => handleQuickPrompt('Switchboard se spark aa raha hai, burning smell hai')}
            >
              ⚠️ Spark & Smoke
            </button>
            <button
              type="button"
              className="quick-chip"
              onClick={() => handleQuickPrompt('Find nearby electricians in Gomti Nagar')}
            >
              ⚡ Electrician Near Me
            </button>
            <button
              type="button"
              className="quick-chip"
              onClick={() => handleQuickPrompt('MCB baar baar trip ho rahi hai')}
            >
              🔌 MCB Tripping
            </button>
            <button
              type="button"
              className="quick-chip"
              onClick={() => handleQuickPrompt('New light fitting aur chandelier lagwana hai')}
            >
              💡 Light Fitting
            </button>
          </div>

          {/* Input Bar */}
          <div className="robot-input-bar">
            <button
              type="button"
              className={`robot-mic-btn ${isListening ? 'recording' : ''}`}
              onClick={toggleVoiceInput}
              title="Voice Input (Hindi/English)"
            >
              <span className="mic-pulse-ring"></span>
              <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z" />
                <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z" />
              </svg>
            </button>
            <input
              type="text"
              value={inputText}
              onChange={(e) => setInputText(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && sendMessage()}
              placeholder="Bijli Guru se poochhein (Hindi/English)..."
            />
            <button
              type="button"
              className="robot-send-btn"
              onClick={() => sendMessage()}
              title="Send"
            >
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z" />
              </svg>
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
