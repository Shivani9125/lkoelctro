<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Electrician;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ElectroFix AI - Autonomous Electrical Service Agent for ElectroLKO
 * 
 * This service handles reasoning, safety detection, session memory,
 * direct problem solving, and controlled tool execution for electricians.
 */
class ElectroFixAgentService
{
    /**
     * Standard electrical catalog for Lucknow
     */
    protected array $serviceCatalog = [
        'fan' => [
            'id' => 'fan',
            'name' => 'Fan Repair & Installation',
            'base_price' => '₹149',
            'estimated_time' => '30 - 45 mins',
            'description' => 'Ceiling fan humming, slow speed, capacitor change, motor bearing, or new installation.',
            'safety_tip' => 'Always switch off the wall regulator before inspecting a noisy fan.'
        ],
        'switchboard' => [
            'id' => 'switchboard',
            'name' => 'Switch & Socket Replacement',
            'base_price' => '₹99',
            'estimated_time' => '20 - 30 mins',
            'description' => 'Loose switch, sparking socket, burnt regulator, or multi-plug board replacement.',
            'safety_tip' => 'Do not plug three heavy appliances into one extension board simultaneously.'
        ],
        'mcb' => [
            'id' => 'mcb',
            'name' => 'MCB Tripping & Short Circuit',
            'base_price' => '₹199',
            'estimated_time' => '30 - 60 mins',
            'description' => 'Frequent MCB trip, neutral wire fault, burnt breaker, or line voltage fluctuation.',
            'safety_tip' => 'Never force a tripping MCB back up if there is a burnt odor. Let a technician test the circuit.'
        ],
        'lighting' => [
            'id' => 'lighting',
            'name' => 'Light & Chandelier Fitting',
            'base_price' => '₹129',
            'estimated_time' => '30 - 45 mins',
            'description' => 'LED panel lights, concealed false-ceiling lights, chandelier wiring, and decorative holders.',
            'safety_tip' => 'Ensure false-ceiling moisture does not contact concealed LED drivers.'
        ],
        'wiring' => [
            'id' => 'wiring',
            'name' => 'Full House Wiring & Safety Audit',
            'base_price' => '₹499',
            'estimated_time' => '1 - 3 hours',
            'description' => 'Earthing testing, conduit concealed wiring, meter box connection, phase balance check.',
            'safety_tip' => 'Ensure proper copper earthing rod is installed to protect sensitive home electronics.'
        ],
        'inverter' => [
            'id' => 'inverter',
            'name' => 'Inverter & Battery Setup',
            'base_price' => '₹249',
            'estimated_time' => '45 - 60 mins',
            'description' => 'Inverter bypass switch, tubular battery water check, overload tripping and backup wire setup.',
            'safety_tip' => 'Keep tubular batteries in a ventilated spot away from open gas flames.'
        ],
        'emergency' => [
            'id' => 'emergency',
            'name' => '24/7 Emergency Hazard Repair',
            'base_price' => '₹299',
            'estimated_time' => '15 - 30 mins rapid arrival',
            'description' => 'Active sparks, burnt wire smell, water leaking into light fitting, complete power outage.',
            'safety_tip' => 'Immediately isolate the main distribution board breaker. Do not use water or touch with wet hands.'
        ]
    ];

    /**
     * Known Lucknow localities with center coordinates
     */
    protected array $lucknowAreas = [
        'gomti nagar' => ['lat' => 26.8530, 'lng' => 80.9980, 'name' => 'Gomti Nagar'],
        'indira nagar' => ['lat' => 26.8780, 'lng' => 80.9850, 'name' => 'Indira Nagar'],
        'hazratganj' => ['lat' => 26.8467, 'lng' => 80.9462, 'name' => 'Hazratganj'],
        'aliganj' => ['lat' => 26.8920, 'lng' => 80.9380, 'name' => 'Aliganj'],
        'alambagh' => ['lat' => 26.8140, 'lng' => 80.9120, 'name' => 'Alambagh'],
        'rajajipuram' => ['lat' => 26.8440, 'lng' => 80.8920, 'name' => 'Rajajipuram'],
        'chowk' => ['lat' => 26.8680, 'lng' => 80.9080, 'name' => 'Chowk'],
        'mahanagar' => ['lat' => 26.8740, 'lng' => 80.9520, 'name' => 'Mahanagar'],
        'ashiyana' => ['lat' => 26.7920, 'lng' => 80.9220, 'name' => 'Ashiyana'],
        'vikas nagar' => ['lat' => 26.8890, 'lng' => 80.9650, 'name' => 'Vikas Nagar'],
        'charbagh' => ['lat' => 26.8320, 'lng' => 80.9220, 'name' => 'Charbagh'],
    ];

    // =========================================================================
    // CONTROLLED LARAVEL AGENT TOOLS
    // =========================================================================

    /**
     * Tool 1: find_services()
     * Returns available electrical services catalog.
     */
    public function find_services(): array
    {
        return [
            'success' => true,
            'count' => count($this->serviceCatalog),
            'services' => array_values($this->serviceCatalog)
        ];
    }

    /**
     * Tool 2: get_service_details()
     * Returns pricing and safety info for a specific electrical service.
     */
    public function get_service_details(string $serviceKey): array
    {
        $cleanKey = strtolower(trim($serviceKey));

        foreach ($this->serviceCatalog as $key => $item) {
            if ($key === $cleanKey || stripos($item['name'], $cleanKey) !== false) {
                return [
                    'success' => true,
                    'service' => $item
                ];
            }
        }

        return [
            'success' => false,
            'message' => "Service '$serviceKey' not found in standard catalog."
        ];
    }

    /**
     * Tool 3: find_nearby_electricians()
     * Searches database for verified electricians near the customer's coordinates/area.
     */
    public function find_nearby_electricians(float $lat = 26.8530, float $lng = 80.9980, string $service = '', string $area = ''): array
    {
        if (!empty($area)) {
            $resolved = $this->get_user_location($area);
            if ($resolved['success']) {
                $lat = $resolved['latitude'];
                $lng = $resolved['longitude'];
            }
        }

        $electricians = Electrician::where('status', 'active')->get();

        if ($electricians->isEmpty()) {
            return [
                'success' => true,
                'count' => 0,
                'electricians' => [],
                'message' => 'No active electricians found at this moment.'
            ];
        }

        $results = [];
        foreach ($electricians as $pro) {
            $distanceKm = $this->calculateHaversineDistance(
                $lat,
                $lng,
                (float) $pro->latitude,
                (float) $pro->longitude
            );

            $results[] = [
                'id' => $pro->id,
                'name' => $pro->name,
                'phone' => $pro->phone,
                'area' => $pro->area ?? 'Lucknow',
                'address' => $pro->address,
                'distance_km' => round($distanceKm, 1),
                'distance_text' => round($distanceKm, 1) . ' km away',
                'rating' => 4.8,
                'reviews_count' => 54,
                'status' => 'Available Now',
                'eta_mins' => max(10, min(30, round($distanceKm * 4)))
            ];
        }

        usort($results, fn($a, $b) => $a['distance_km'] <=> $b['distance_km']);
        $topPros = array_slice($results, 0, 3);

        return [
            'success' => true,
            'count' => count($topPros),
            'target_coordinates' => ['latitude' => $lat, 'longitude' => $lng],
            'electricians' => $topPros
        ];
    }

    /**
     * Tool 4: get_electrician_details()
     */
    public function get_electrician_details(int $electricianId): array
    {
        $pro = Electrician::find($electricianId);
        if (!$pro) {
            return ['success' => false, 'message' => "Electrician with ID $electricianId not found."];
        }

        return [
            'success' => true,
            'electrician' => [
                'id' => $pro->id,
                'name' => $pro->name,
                'phone' => $pro->phone,
                'area' => $pro->area,
                'address' => $pro->address,
                'rating' => 4.9,
                'badge' => 'Govt Certified Pro',
                'experience' => '6+ Years Experience'
            ]
        ];
    }

    /**
     * Tool 5: check_electrician_availability()
     */
    public function check_electrician_availability(int $electricianId, string $timeSlot = 'immediate'): array
    {
        $pro = Electrician::find($electricianId);
        if (!$pro || $pro->status !== 'active') {
            return [
                'success' => true,
                'available' => false,
                'message' => 'This electrician is currently off-duty or unavailable.'
            ];
        }

        return [
            'success' => true,
            'available' => true,
            'electrician_id' => $electricianId,
            'electrician_name' => $pro->name,
            'time_slot' => $timeSlot,
            'eta' => '15 - 25 mins'
        ];
    }

    /**
     * Tool 6: get_user_location()
     */
    public function get_user_location(string $areaQuery): array
    {
        $clean = strtolower(trim($areaQuery));

        foreach ($this->lucknowAreas as $key => $loc) {
            if (str_contains($clean, $key) || str_contains($key, $clean)) {
                return [
                    'success' => true,
                    'matched_area' => $loc['name'],
                    'latitude' => $loc['lat'],
                    'longitude' => $loc['lng'],
                    'city' => 'Lucknow'
                ];
            }
        }

        return [
            'success' => true,
            'matched_area' => 'Gomti Nagar',
            'latitude' => 26.8530,
            'longitude' => 80.9980,
            'city' => 'Lucknow',
            'is_default' => true
        ];
    }

    /**
     * Tool 7: create_booking()
     */
    public function create_booking(
        int $electricianId,
        string $customerName,
        string $customerPhone,
        string $customerAddress,
        string $serviceType,
        string $timeSlot = 'Immediate (Within 30 mins)',
        string $notes = ''
    ): array {
        $pro = Electrician::find($electricianId);
        $reference = 'ELKO-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

        $booking = Booking::create([
            'booking_reference' => $reference,
            'electrician_id' => $electricianId,
            'customer_name' => $customerName ?: 'Customer',
            'customer_phone' => $customerPhone ?: '+91 9876543210',
            'customer_address' => $customerAddress ?: 'Lucknow Home Address',
            'area' => $pro ? $pro->area : 'Lucknow',
            'service_type' => $serviceType ?: 'General Electrical Fix',
            'urgency' => (stripos($serviceType, 'emergency') !== false) ? 'emergency' : 'standard',
            'time_slot' => $timeSlot,
            'notes' => $notes ?: 'Booked via ElectroFix AI Assistant',
            'status' => 'confirmed'
        ]);

        return [
            'success' => true,
            'booking_id' => $booking->id,
            'booking_reference' => $reference,
            'electrician_name' => $pro ? $pro->name : 'Assigned ElectroFix Pro',
            'electrician_phone' => $pro ? $pro->phone : '+91 9812340001',
            'service_type' => $booking->service_type,
            'time_slot' => $booking->time_slot,
            'status' => 'confirmed',
            'message' => "Booking confirmed successfully! Booking ID: {$reference}"
        ];
    }

    /**
     * Tool 8: get_booking_status()
     */
    public function get_booking_status(string $bookingReference): array
    {
        $booking = Booking::with('electrician')
            ->where('booking_reference', trim($bookingReference))
            ->first();

        if (!$booking) {
            return ['success' => false, 'message' => "No booking found with ID: $bookingReference"];
        }

        return [
            'success' => true,
            'booking' => [
                'reference' => $booking->booking_reference,
                'status' => $booking->status,
                'service' => $booking->service_type,
                'customer_name' => $booking->customer_name,
                'electrician_name' => $booking->electrician ? $booking->electrician->name : 'ElectroFix Verified Pro',
                'created_at' => $booking->created_at->format('d M Y, h:i A')
            ]
        ];
    }

    /**
     * Tool 9: cancel_booking()
     */
    public function cancel_booking(string $bookingReference): array
    {
        $booking = Booking::where('booking_reference', trim($bookingReference))->first();
        if (!$booking) {
            return ['success' => false, 'message' => "No booking found with ID: $bookingReference"];
        }

        $booking->update(['status' => 'cancelled']);

        return [
            'success' => true,
            'booking_reference' => $booking->booking_reference,
            'status' => 'cancelled',
            'message' => "Booking {$booking->booking_reference} has been cancelled."
        ];
    }

    /**
     * Checks if user message mentions severe electrical fire/shock/smoke hazard.
     */
    public function detectSafetyEmergency(string $text): bool
    {
        $hazardKeywords = [
            'spark', 'sparking', 'smoke', 'dhuan', 'burning', 'jal raha', 'badboo',
            'fire', 'aag', 'shock', 'current lag', 'electric shock', 'blast',
            'water leak', 'pani switch', 'live wire', 'khula taar', 'wire melted'
        ];

        $lower = strtolower($text);
        foreach ($hazardKeywords as $keyword) {
            if (str_contains($lower, $keyword)) {
                return true;
            }
        }
        return false;
    }

    // =========================================================================
    // AUTONOMOUS AGENT REASONING ENGINE
    // =========================================================================

    /**
     * Primary Agent Loop:
     * 1. Understands user's exact question, intent, and context.
     * 2. Gives direct, useful, user-friendly answers first (Google AI Overview style).
     * 3. Explains solutions clearly and naturally.
     * 4. Does not automatically suggest or book technicians unless explicitly requested.
     * 5. Handles user frustration without repeating or analyzing it.
     * 6. Only shows technicians when strictly relevant.
     * 7. Keeps responses conversational, simple, and relevant.
     * 8. Dynamically adapts to any user question.
     */
    public function processMessage(
        string $message,
        array $conversationHistory = [],
        array $userLocation = [],
        ?array $pendingAction = null
    ): array {
        $messageClean = trim($message);
        $isEmergency = $this->detectSafetyEmergency($messageClean);

        // Coordinates & Locality
        $lat = (float) ($userLocation['latitude'] ?? $userLocation['lat'] ?? 26.8530);
        $lng = (float) ($userLocation['longitude'] ?? $userLocation['lng'] ?? 80.9980);
        $areaName = $userLocation['area'] ?? 'Gomti Nagar, Lucknow';

        // 1. Detect user frustration ("bakwas mat kro", "chup", "faltu", "seedha bolo")
        if ($this->isFrustratedUser($messageClean)) {
            $priorTopic = $this->extractPreviousTopicFromHistory($conversationHistory);
            return $this->answerTopicDirectly($priorTopic ?: $messageClean, $areaName, true);
        }

        // 2. Check if user is confirming or declining an active pending booking
        $isConfirmation = $this->isAffirmativeResponse($messageClean);
        $isDeclining = $this->isNegativeResponse($messageClean);

        if (empty($pendingAction) && $isConfirmation) {
            $pendingAction = $this->detectPendingActionFromHistory($conversationHistory, $messageClean, $areaName, $lat, $lng);
        }

        if (!empty($pendingAction) && isset($pendingAction['action']) && $pendingAction['action'] === 'create_booking') {
            if ($isConfirmation) {
                $bookData = $pendingAction['data'] ?? [];
                $bookingResult = $this->create_booking(
                    (int) ($bookData['electrician_id'] ?? 1),
                    $bookData['customer_name'] ?? 'Customer',
                    $bookData['customer_phone'] ?? '+91 9812340001',
                    $bookData['customer_address'] ?? $areaName,
                    $bookData['service_type'] ?? 'Electrical Service',
                    $bookData['time_slot'] ?? 'Within 30 mins'
                );

                $proName = $bookingResult['electrician_name'];
                $ref = $bookingResult['booking_reference'];

                $replyText = "Shandar! Aapki service booking confirm ho gayi hai.\n\n"
                    . "📋 **Booking ID:** `{$ref}`\n"
                    . "⚡ **Electrician:** {$proName}\n"
                    . "⏱ **Estimated Arrival:** ~20-30 Mins me aapke doorstep par honge.\n"
                    . "📍 **Location:** {$areaName}\n\n"
                    . "Electrician aane se pehle aapko call karenge. Payment kaam complete hone ke baad hi deni hai.";

                return [
                    'success' => true,
                    'reply' => $replyText,
                    'spoken_text' => "Aapki booking confirm ho gayi hai. Electrician {$proName} lagbhag 25 minute me aapke ghar pahunch rahe hain.",
                    'language' => 'hi',
                    'state' => 'SPEAKING',
                    'tools_used' => [
                        ['name' => 'create_booking', 'status' => 'success', 'data' => $bookingResult]
                    ],
                    'tool_display' => '✅ Service Booking Confirmed',
                    'intent' => 'booking_confirmed',
                    'requires_confirmation' => false,
                    'pending_action' => null,
                    'booking' => $bookingResult
                ];
            } elseif ($isDeclining) {
                return [
                    'success' => true,
                    'reply' => "Theek hai! Maine booking cancel kar di hai. Agar koi aur sawaal ya jaankari chahiye, toh batayiye!",
                    'spoken_text' => "Theek hai, maine booking cancel kar di hai. Bataiye aur kya janna chahte hain?",
                    'language' => 'hi',
                    'state' => 'SPEAKING',
                    'tools_used' => [],
                    'tool_display' => null,
                    'intent' => 'booking_declined',
                    'requires_confirmation' => false,
                    'pending_action' => null
                ];
            }
        }

        // 3. If valid Gemini API key is configured, use Gemini
        $geminiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        if (!empty($geminiKey)) {
            $geminiResponse = $this->reasonWithGemini($messageClean, $conversationHistory, $lat, $lng, $areaName, $isEmergency);
            if ($geminiResponse && $geminiResponse['success']) {
                return $geminiResponse;
            }
        }

        // 4. Autonomous Local AI Reasoning Engine
        return $this->reasonWithLocalAgent($messageClean, $conversationHistory, $lat, $lng, $areaName, $isEmergency);
    }

    /**
     * Detects user frustration or rebukes (e.g. "bakwas mat kro", "chup", "faltu", "seedha bolo")
     */
    public function isFrustratedUser(string $text): bool
    {
        $clean = strtolower(trim($text));
        $patterns = [
            '/\b(bakwa+s|bakwas)\b/i',
            '/\b(chup(\s+raho|\s+baitho|\s+ho\s+jao)?|shutup|shut\s+up)\b/i',
            '/\b(fa+ltu|faltu\s+(baat|mat|na|bol))\b/i',
            '/\b(kuch\s+bhi\s+mat|kuch\s+nahi\s+aata|kuch\s+samajh\s+nahi)\b/i',
            '/\b(dima+g\s+(mat\s+)?kharab)\b/i',
            '/\b(seedh(a|e)\s+(point|baat|bolo|batao|mudde|answer))\b/i',
            '/\b(ye\s+sab\s+chod|ye\s+sab\s+chhod)\b/i',
            '/\b(nonsense|rubbish|stop\s+(talking\s+)?nonsense|don\'?t\s+talk\s+rubbish)\b/i',
            '/\b(bekar|useless|ghatiya)\s+(answer|jawab|service|bot|ai)?\b/i',
            '/\b(galat\s+(bata|bol|hai|jawab))\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $clean)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Detects if user explicitly wants an electrician / technician / booking
     */
    public function isExplicitTechnicianRequest(string $text): bool
    {
        $clean = strtolower(trim($text));

        // Negative check: questions about the concept of electricians
        if (preg_match('/^(what|who|why|kaise|kya\s+karta)\b/i', $clean) && !preg_match('/\b(chahiye|bhej|book|hire)\b/i', $clean)) {
            return false;
        }

        $hasTechNoun = preg_match('/\b(electrician|technician|mistri|mechanic)\b/i', $clean);
        $hasActionVerb = preg_match('/\b(chahiye|bhejo|bhej\s+do|bhej\s+dijiye|send|hire|call|arrange|book|booking|appointment|contact|number|mil\s+sakta|available\s+hai|dikhao|list|near\s+me)\b/i', $clean);

        if ($hasTechNoun && $hasActionVerb) {
            return true;
        }

        if (preg_match('/\b(book\s+(karo|kardo|karna|electrician)|doorstep\s+visit|ghar\s+(par\s+)?bhej|kisi\s+ko\s+bhej|service\s+book)\b/i', $clean)) {
            return true;
        }

        if (preg_match('/\b(rahul|amit|manoj|vikram|mahesh|deepak|suresh|ramesh)\b/i', $clean) && preg_match('/\b(book|bhej|hire|call)\b/i', $clean)) {
            return true;
        }

        return false;
    }

    /**
     * Extracts previous substantive question or topic from conversation history
     */
    public function extractPreviousTopicFromHistory(array $history): ?string
    {
        for ($i = count($history) - 1; $i >= 0; $i--) {
            $turn = $history[$i];
            if (($turn['role'] ?? '') === 'user') {
                $content = trim($turn['content'] ?? '');
                if (strlen($content) > 3 && !$this->isFrustratedUser($content)) {
                    $cleanGreeting = trim(preg_replace('/[,\.?!:;\-]/', '', strtolower($content)));
                    if (!preg_match('/^(hi|hello|hey|namaste|pranam|namaskar|good morning)(\s.*)?$/i', $cleanGreeting)) {
                        return $content;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Reconstructs pending booking action ONLY when booking was previously offered or requested
     */
    protected function detectPendingActionFromHistory(array $history, string $userMessage, string $areaName, float $lat, float $lng): ?array
    {
        $lower = strtolower($userMessage);

        $allPros = Electrician::where('status', 'available')->get();
        foreach ($allPros as $p) {
            $firstName = strtolower(explode(' ', $p->name)[0]);
            if (str_contains($lower, $firstName) || str_contains($lower, strtolower($p->name))) {
                return [
                    'action' => 'create_booking',
                    'data' => [
                        'electrician_id' => $p->id,
                        'customer_name' => 'Customer',
                        'customer_address' => $areaName,
                        'service_type' => 'Electrical Repair',
                        'time_slot' => 'Within 30 mins'
                    ]
                ];
            }
        }

        $lastAssistantContent = '';
        for ($i = count($history) - 1; $i >= 0; $i--) {
            $turn = $history[$i];
            if (($turn['role'] ?? '') === 'assistant' || ($turn['role'] ?? '') === 'model') {
                $lastAssistantContent = strtolower($turn['content'] ?? '');
                break;
            }
        }

        $wasBookingOffered = str_contains($lastAssistantContent, 'book kar doon') ||
            str_contains($lastAssistantContent, 'book karna chahte') ||
            str_contains($lastAssistantContent, 'dispatch kar doon') ||
            str_contains($lastAssistantContent, 'booking confirm');

        if (!$wasBookingOffered) {
            return null;
        }

        foreach ($allPros as $p) {
            if (str_contains($lastAssistantContent, strtolower($p->name))) {
                return [
                    'action' => 'create_booking',
                    'data' => [
                        'electrician_id' => $p->id,
                        'customer_name' => 'Customer',
                        'customer_address' => $areaName,
                        'service_type' => 'Electrical Service',
                        'time_slot' => 'Within 30 mins'
                    ]
                ];
            }
        }

        $nearby = $this->find_nearby_electricians($lat, $lng, 'all', $areaName);
        if (!empty($nearby['electricians'][0])) {
            $top = $nearby['electricians'][0];
            return [
                'action' => 'create_booking',
                'data' => [
                    'electrician_id' => $top['id'],
                    'customer_name' => 'Customer',
                    'customer_address' => $areaName,
                    'service_type' => 'Electrical Repair',
                    'time_slot' => 'Within 30 mins'
                ]
            ];
        }

        return null;
    }

    /**
     * Local Smart Agent: Answers user questions directly, explains solutions,
     * suppresses unprompted technician lists, and handles dynamic queries.
     */
    protected function reasonWithLocalAgent(
        string $message,
        array $conversationHistory,
        float $lat,
        float $lng,
        string $areaName,
        bool $isEmergency
    ): array {
        $lower = strtolower(trim($message));

        foreach ($this->lucknowAreas as $areaKey => $areaInfo) {
            if (str_contains($lower, $areaKey)) {
                $areaName = $areaInfo['name'] . ', Lucknow';
                $lat = $areaInfo['lat'];
                $lng = $areaInfo['lng'];
                break;
            }
        }

        // 1. EMERGENCY PROTOCOL
        if ($isEmergency) {
            return $this->handleSafetyEmergency($lat, $lng, $areaName);
        }

        // 2. EXPLICIT TECHNICIAN / BOOKING REQUEST
        if ($this->isExplicitTechnicianRequest($message)) {
            return $this->handleExplicitTechnicianRequest($lat, $lng, $areaName);
        }

        // 3. GREETINGS & CASUAL TALK
        $cleanGreeting = trim(preg_replace('/[,\.?!:;\-]/', '', $lower));
        $isPureGreeting = preg_match('/^(hi|hello|hey|namaste|pranam|namaskar|good morning|good evening|good afternoon|kaise ho|bhaiya|suno|sun|kya haal|radhe radhe|ram ram)(\s.*)?$/i', $cleanGreeting);
        if ($isPureGreeting && strlen($cleanGreeting) < 40 && !str_contains($cleanGreeting, 'fan') && !str_contains($cleanGreeting, 'switch') && !str_contains($cleanGreeting, 'mcb') && !str_contains($cleanGreeting, 'light')) {
            return [
                'success' => true,
                'reply' => "Namaste! Main **ElectroFix AI** hoon.\n\n"
                    . "Aapke ghar me bijli, appliance ya wiring se judi koi bhi samasya ho, ya koi sawaal ho — batayiye, main seedha aur saral samadhan deta hoon!",
                'spoken_text' => "Namaste! Main ElectroFix AI hoon. Batayiye, main aapki kya madad kar sakta hoon?",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'greeting',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => []
            ];
        }

        // 4. SERVICES CATALOG & PRICING INQUIRIES
        if ((str_contains($lower, 'service') || str_contains($lower, 'catalog') || str_contains($lower, 'rate') || str_contains($lower, 'price') || str_contains($lower, 'kharach') || str_contains($lower, 'charge')) && (str_contains($lower, 'kya') || str_contains($lower, 'list') || str_contains($lower, 'kitna') || str_contains($lower, 'dete ho'))) {
            $services = $this->find_services()['services'];
            $reply = "ElectroLKO ke standard doorstep service charges:\n\n";
            foreach ($services as $s) {
                $reply .= "• **{$s['name']}** ({$s['base_price']}) — {$s['description']}\n";
            }
            $reply .= "\nAapko inme se kis cheez me samasya aa rahi hai? Batayiye, main pehle solution samjhata hoon.";

            return [
                'success' => true,
                'reply' => $reply,
                'spoken_text' => "Hum fan repair, switchboard fix, light fitting, MCB aur wiring services provide karte hain. Aapko kis cheez me madad chahiye?",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [['name' => 'find_services', 'status' => 'success']],
                'tool_display' => '📋 Retrieved Service Catalog',
                'intent' => 'service_catalog',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => []
            ];
        }

        // 5. DIRECT ANSWER ENGINE (Google Search AI style for ALL questions)
        return $this->answerTopicDirectly($message, $areaName, false);
    }

    /**
     * Handles explicit requests for technicians/electricians
     */
    protected function handleExplicitTechnicianRequest(float $lat, float $lng, string $areaName): array
    {
        $toolsResult = $this->find_nearby_electricians($lat, $lng, 'all', $areaName);
        $pros = $toolsResult['electricians'] ?? [];
        $topPro = $pros[0] ?? null;

        $reply = "Lucknow ({$areaName}) me verified electricians ready hain:\n\n";
        foreach ($pros as $p) {
            $reply .= "• **{$p['name']}** — ⭐ {$p['rating']} ({$p['distance_text']}, ~{$p['eta_mins']} mins arrival)\n";
        }

        if ($topPro) {
            $reply .= "\nKya main **{$topPro['name']}** ko aapke ghar doorstep visit ke liye book kar doon?";
        }

        return [
            'success' => true,
            'reply' => $reply,
            'spoken_text' => "Aapke paas certified electricians available hain. Kya main {$topPro['name']} ko book kar doon?",
            'language' => 'hi',
            'state' => 'SPEAKING',
            'tools_used' => [
                ['name' => 'find_nearby_electricians', 'status' => 'success', 'data' => $toolsResult]
            ],
            'tool_display' => "⚡ Located Electricians in {$areaName}",
            'intent' => 'explicit_electrician_request',
            'requires_confirmation' => true,
            'pending_action' => $topPro ? [
                'action' => 'create_booking',
                'data' => [
                    'electrician_id' => $topPro['id'],
                    'customer_name' => 'Customer',
                    'customer_address' => $areaName,
                    'service_type' => 'Doorstep Electrical Service',
                    'time_slot' => 'Within 30 mins'
                ]
            ] : null,
            'electricians' => $pros
        ];
    }

    /**
     * Handles severe electrical emergencies with immediate safety advice first
     */
    protected function handleSafetyEmergency(float $lat, float $lng, string $areaName): array
    {
        $toolsResult = $this->find_nearby_electricians($lat, $lng, 'emergency', $areaName);
        $pros = $toolsResult['electricians'] ?? [];
        $topPro = $pros[0] ?? null;

        $reply = "⚠️ **ELECTRICAL EMERGENCY SAFETY ALERT** ⚠️\n\n"
            . "🚨 **Pehle yeh zaroori safety steps turant follow karein:**\n"
            . "1. Kisi bhi sparking switchboard, taar ya appliance ko haath na lagayein.\n"
            . "2. Geelay haath ya paani se bilkul door rahein.\n"
            . "3. Agar safe ho, toh ghar ki **Main MCB / Power Breaker** turant OFF kar dein.\n\n"
            . "Emergency repair ke liye Lucknow ({$areaName}) me verified technicians available hain:";

        if ($topPro) {
            $reply .= "\n\n⚡ **{$topPro['name']}** (⭐ {$topPro['rating']} • {$topPro['distance_text']})\n"
                . "⏱ **Rapid Arrival:** ~{$topPro['eta_mins']} mins\n\n"
                . "Kya main aapke liye **{$topPro['name']}** ko turant emergency dispatch kar doon?";
        }

        return [
            'success' => true,
            'reply' => $reply,
            'spoken_text' => "Kripya switchboard se door rahein aur main breaker switch off karein. Kya main aapke liye emergency electrician book kar doon?",
            'language' => 'hi',
            'state' => 'EMERGENCY',
            'tools_used' => [
                ['name' => 'find_nearby_electricians', 'status' => 'success', 'data' => $toolsResult]
            ],
            'tool_display' => "🚨 Emergency Pros Located in {$areaName}",
            'intent' => 'emergency_safety',
            'requires_confirmation' => true,
            'pending_action' => $topPro ? [
                'action' => 'create_booking',
                'data' => [
                    'electrician_id' => $topPro['id'],
                    'customer_name' => 'Customer',
                    'customer_address' => $areaName,
                    'service_type' => 'Emergency Hazard Repair',
                    'time_slot' => 'Immediate'
                ]
            ] : null,
            'electricians' => $pros
        ];
    }

    /**
     * Google Search AI-style Direct Answering Engine:
     * - Understands exact user question and intent
     * - Direct, useful answer first
     * - Clearly explains root causes & solutions
     * - Does NOT list or book technicians automatically
     * - Adapts dynamically to ANY user question
     */
    public function answerTopicDirectly(string $query, string $areaName, bool $wasFrustrated = false): array
    {
        $lower = strtolower(trim($query));
        $isEnglish = !preg_match('/[^\x00-\x7F]/', $query) &&
            preg_match('/\b(the|is|are|why|how|what|when|where|does|my|in|on|at|and|or|not|can|could|would|should|please|help)\b/i', $query) &&
            !preg_match('/\b(hai|ho|kya|kyu|kyun|kaise|karo|karein|nahi|mera|meri|mere|ghar|pankha|taar|bijli)\b/i', $query);

        $preamble = '';
        if ($wasFrustrated) {
            $preamble = $isEnglish
                ? "Apologies! Let's get straight to the point:\n\n"
                : "Maaf kijiye! Seedhe point par aate hain:\n\n";
        }

        $reply = '';
        $spoken = '';
        $intent = 'direct_answer';

        // ---------------------------------------------------------------------
        // 1. CEILING FAN & EXHAUST FAN
        // ---------------------------------------------------------------------
        if (str_contains($lower, 'fan') || str_contains($lower, 'pankha') || str_contains($lower, 'regulator')) {
            $intent = 'fan_advice';

            // A. Noise / Humming / Awaz
            if (str_contains($lower, 'awaz') || str_contains($lower, 'awaaz') || str_contains($lower, 'noise') || str_contains($lower, 'humming') || str_contains($lower, 'sound') || str_contains($lower, 'khad')) {
                if ($isEnglish) {
                    $reply = $preamble . "A ceiling fan usually makes noise or hums due to 3 common reasons:\n\n"
                        . "1. **Dry or Worn Ball-Bearings:** Grease inside the bearings dries out or collects dust, causing a grinding noise. Adding machine oil or replacing bearings resolves this.\n"
                        . "2. **Weak / Leaking Capacitor:** A degraded capacitor causes electrical imbalance in the motor windings, creating a deep humming sound and lower speed. Replacing it with a new 2.5 µF capacitor fixes it.\n"
                        . "3. **Loose Blade Screws:** Loose screws on blade brackets cause rattling and wobble. Turn off power and firmly tighten the screws on all three blades.";
                    $spoken = "Fan noise is usually caused by dry ball bearings, a weak capacitor, or loose blade screws.";
                } else {
                    $reply = $preamble . "Pankhe me aawaz ya humming aane ke 3 mukhya kaaran hote hain:\n\n"
                        . "1. **Bearing me Grease Sookhna:** Ball-bearings me dust jamne ya grease sookhne se ghisne ki aawaz aati hai. Isme machine oil ya grease lagane se aawaz theek ho jaati hai.\n"
                        . "2. **Weak ya Leaking Capacitor:** Capacitor kamzor hone par motor me electrical imbalance banta hai, jisse humming vibration hoti hai aur speed ghati hai. Naya 2.5 µF capacitor lagayein.\n"
                        . "3. **Blades ke Screws Dheele Hona:** Pankhadiyon ke nut-bolts loose hone par khad-khad aawaz aati hai. Power switch band karke teeno blades ke screws tight karein.";
                    $spoken = "Pankhe me aawaz ball bearing dry hone, capacitor weak hone ya blade screws loose hone se aati hai.";
                }
            }
            // B. Slow Speed / Dheere chal raha hai
            elseif (str_contains($lower, 'slow') || str_contains($lower, 'dheere') || str_contains($lower, 'speed') || str_contains($lower, 'kam chal')) {
                if ($isEnglish) {
                    $reply = $preamble . "If your ceiling fan is running slow, the cause is almost always one of these:\n\n"
                        . "1. **Degraded Capacitor (90% of cases):** Over time, the capacitor rating drops from 2.5 µF to below 1.5 µF. Replacing the capacitor (2.5 µF, 440V AC) instantly restores full speed.\n"
                        . "2. **Stiff Bearings / Friction:** Turn off power and spin the fan by hand. If it doesn't spin freely for 10-15 seconds, the bearings need lubrication.\n"
                        . "3. **Faulty Wall Regulator:** Old electronic regulators cause a voltage drop. Test by connecting the fan switch directly.";
                    $spoken = "A slow fan is almost always caused by a degraded capacitor or dry bearings.";
                } else {
                    $reply = $preamble . "Pankha slow chalne ke mukhya kaaran:\n\n"
                        . "1. **Capacitor Kamzor Hona (90% Kaaran):** Samay ke sath capacitor ki capacity 2.5 µF se ghat kar kam ho jaati hai. Naya 2.5 µF (440V AC) capacitor badalne se speed turant normal ho jaati hai.\n"
                        . "2. **Bearing me Jamming:** Switch band karke pankhe ko haath se ghumayein. Agar pankha aasaani se 10-15 second nahi ghoomta, toh bearings me lubrication ki zaroorat hai.\n"
                        . "3. **Faulty Wall Regulator:** Regulator ke internal triac me voltage drop hone se bhi speed kam milti hai.";
                    $spoken = "Pankha slow chalne ka 90 percent kaaran capacitor weak hona hota hai. Naya capacitor lagate hi speed normal ho jaati hai.";
                }
            }
            // C. Wobbling / Hil raha hai
            elseif (str_contains($lower, 'hil') || str_contains($lower, 'wobble') || str_contains($lower, 'larkhad')) {
                $reply = $preamble . "Pankha hilne (wobbling) ke 3 mukhya kaaran:\n\n"
                    . "1. **Blades ka Angle (Pitch) Unbalance:** Kisi ek blade ka angle halka sa mud jaane par hawa ka pressure unbalance ho jata hai.\n"
                    . "2. **Dust Jamna:** Ek blade par zyada dhool aur baakiyon par kam hone se weight imbalance banta hai. Saare blades ache se saaf karein.\n"
                    . "3. **Downrod Bolt Loose:** Ceiling hook aur rod ke beech ka nut-bolt aur cotter pin check karke tight karein.";
                $spoken = "Pankha hilna blades ke unbalance ya rod ke screws loose hone ki wajah se hota hai.";
            }
            // D. Reverse / Ulta ghoom raha hai
            elseif (str_contains($lower, 'ulta') || str_contains($lower, 'reverse')) {
                $reply = $preamble . "Pankha ulta ghoomne ka kaaran Capacitor ke connections ulte judna hota hai:\n\n"
                    . "• Ceiling fan motor me do windings hoti hain: **Running** aur **Starting**.\n"
                    . "• Agar capacitor ka neutral/phase connection starting winding ke terminal par lag jaye, to motor ulti disha me ghoomne lagti hai.\n"
                    . "• **Samadhan:** Power switch off karein aur capacitor se judi terminal wire ko doosre wire point par interchange karein.";
                $spoken = "Pankha ulta ghoomne ka kaaran capacitor connection starting winding par juda hona hai. Wires interchange karne se theek ho jata hai.";
            }
            // E. General Fan Query
            else {
                $reply = $preamble . "Ceiling fan me aamtaur par teen cheezein check ki jaati hain:\n\n"
                    . "• **Capacitor (2.5 µF):** Speed kam hona ya motor na ghoomna.\n"
                    . "• **Ball Bearings:** Ghisne ya khad-khad aawaz aane par machine oil/grease.\n"
                    . "• **Regulator & Blades:** Speed control na hona ya vibration.\n\n"
                    . "Aapke pankhe me kya dikkat aa rahi hai — aawaz kar raha hai, slow hai ya bilkul nahi chal raha?";
                $spoken = "Pankhe me capacitor, bearing ya regulator ki wajah se dikkat aati hai. Aapke pankhe me kya problem hai?";
            }
        }

        // ---------------------------------------------------------------------
        // 2. MCB, BREAKERS & SHORT CIRCUITS
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'mcb') || str_contains($lower, 'trip') || str_contains($lower, 'breaker') || str_contains($lower, 'fuse') || str_contains($lower, 'rccb')) {
            $intent = 'mcb_advice';

            if (str_contains($lower, 'kya hota') || str_contains($lower, 'difference') || str_contains($lower, 'kyu')) {
                $reply = $preamble . "MCB (Miniature Circuit Breaker) baar-baar trip hone ke 3 mukhya kaaran hote hain:\n\n"
                    . "1. **Circuit Overload:** Ek hi circuit par ek sath heavy appliances (AC, Geyser, Microwave, Iron) chalane se wire garam hoti hai aur MCB ka thermal sensor trip kar deta hai.\n"
                    . "2. **Short Circuit:** Phase wire aur Neutral wire aapas me direct touch hone par excessive current behta hai, jisse magnetic sensor turant MCB gira deta hai.\n"
                    . "3. **MCB Faulty Hona:** Purani MCB ka internal spring mechanism loose hone se bina load ke bhi gir sakti hai.\n\n"
                    . "🛠️ **Safe Check:** Us kamre ke saare heavy appliances band karein, phir MCB uthayein. Agar phir bhi girti hai, to line wiring me short-circuit hai.";
                $spoken = "MCB trip hona circuit overload, short circuit ya faulty breaker ki wajah se hota hai.";
            } else {
                $reply = $preamble . "MCB trip hone par yeh practical steps follow karein:\n\n"
                    . "1. **Heavy Load Band Karein:** AC, geyser, heater aur washing machine ke switch turant band karein.\n"
                    . "2. **MCB Reset Karein:** MCB lever ko pehle poora neeche karein, phir firmly upar uthayein.\n"
                    . "3. **Natija Samjhein:**\n"
                    . "   • Agar MCB foran gir jati hai: Internal wiring ya kisi socket me short circuit hai.\n"
                    . "   • Agar 5-10 minute baad girti hai: Circuit par load zyada hai, appliances alag circuit par shift karein.";
                $spoken = "MCB trip hone par pehle heavy appliances band karein aur phir lever upar karke test karein.";
            }
        }

        // ---------------------------------------------------------------------
        // 3. SWITCHBOARD, SOCKETS, PLUGS & CHARGERS
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'switch') || str_contains($lower, 'socket') || str_contains($lower, 'board') || str_contains($lower, 'plug') || str_contains($lower, 'charger')) {
            $intent = 'switchboard_advice';

            if (str_contains($lower, 'spark') || str_contains($lower, 'jal') || str_contains($lower, 'dhua') || str_contains($lower, 'badboo') || str_contains($lower, 'melt')) {
                $reply = $preamble . "Switchboard ya socket me spark hone ke mukhya kaaran:\n\n"
                    . "1. **Loose Wire Terminals:** Switch ke peeche wire screws loose hone se arcing hoti hai aur switch melt hone lagta hai.\n"
                    . "2. **Socket ke Clips Dheele:** Socket ke andar metal clips phail jaane se plug ke pins par gap banta hai, jisse spark nikalta hai.\n"
                    . "3. **Undersized Switch (Overloading):** 6A ke chote socket me 1500W+ ka geyser, iron ya heater chalana.\n\n"
                    . "💡 **Turant Yeh Karein:** Us switchboard par haath na lagayein, heavy plug bahar nikal lein, aur main switch off karke hi repair karein.";
                $spoken = "Switchboard me spark loose wire terminals ya chote socket me heavy appliance chalane se hota hai.";
            } elseif (str_contains($lower, '16a') || str_contains($lower, '6a') || str_contains($lower, 'ampere')) {
                $reply = $preamble . "6A aur 16A Sockets me antar:\n\n"
                    . "• **6 Ampere Socket (Chota Socket):** Phone charger, TV, laptop, aur lights ke liye (Max 1200 Watts tak).\n"
                    . "• **16 Ampere Power Socket (Bada Socket):** AC, Geyser, Microwave, Refrigerator aur Washing Machine ke liye (Max 3000 Watts tak).\n\n"
                    . "⚠️ Kabhi bhi multi-plug adapter lagakar heavy appliance ko 6A socket me na chalayein, isse board jal sakta hai.";
                $spoken = "6A socket light load ke liye hota hai aur 16A power socket AC, geyser aur microwave ke liye zaroori hai.";
            } else {
                $reply = $preamble . "Switchboard me aam samasyayein aur unka hal:\n\n"
                    . "• **Socket me current na aana:** Switch ke peeche loop wire tutna ya switch ke brass contacts burn hona.\n"
                    . "• **Plug baar-baar girna:** Socket ke internal brass clips loose hona (Naya modular socket lagana behtar hai).\n"
                    . "• **Safe Practice:** Switchboard check karne se pehle distribution board ki MCB zaroor band karein.";
                $spoken = "Switchboard me socket loose hone ya wire link tutne se power chali jaati hai.";
            }
        }

        // ---------------------------------------------------------------------
        // 4. AIR CONDITIONER (AC)
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'ac') || str_contains($lower, 'air conditioner') || str_contains($lower, 'cooling')) {
            $intent = 'ac_advice';

            $reply = $preamble . "AC cooling na karne ya kam cooling hone ke 5 mukhya kaaran:\n\n"
                . "1. **Ganda Air Filter:** Indoor unit ka mesh filter dhool se jamne par airflow ruk jata hai (Filter nikal kar paani se dho lein).\n"
                . "2. **Outdoor Condenser Unit Choke:** Outdoor unit ki jaali par dhool jamne se garmi bahar nahi nikal pati.\n"
                . "3. **Remote Mode Setting:** Check karein ki remote **Cool Mode (❄️ Snowflake icon)** par hai aur temperature 24°C set hai, na ki 'Fan' ya 'Dry' mode par.\n"
                . "4. **Refrigerant Gas Leakage:** Agar filter saaf hai aur compressor chal raha hai par hawa bilkul thandi nahi hai, to gas leak ho sakti hai.\n"
                . "5. **Compressor Run Capacitor Fault:** Fan chal raha hai par outdoor compressor start nahi ho raha.";
            $spoken = "AC cooling na karne ka mukhya kaaran ganda filter, outdoor condenser choke hona ya gas leak hona hota hai.";
        }

        // ---------------------------------------------------------------------
        // 5. GEYSER & WATER HEATER
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'geyser') || str_contains($lower, 'water heater') || str_contains($lower, 'pani garam')) {
            $intent = 'geyser_advice';

            $reply = $preamble . "Geyser me paani garam na hone ke 4 mukhya kaaran:\n\n"
                . "1. **Thermostat Safety Cut-Out Trip:** Paani zyada garam hone par geyser ka safety thermal cut-out trip ho jata hai (Isko reset button dabakar theek kiya ja sakta hai).\n"
                . "2. **Heating Element (Coil) Burnt:** Hard water (khara paani) ke kaaran coil par scale jam jati hai aur element phuk jata hai.\n"
                . "3. **16A Power Socket Fault:** Geyser ka heavy 16A plug ya socket andar se jal jana.\n"
                . "4. **MCB Trip:** Agar geyser on karte hi MCB girti hai, to heating element ki insulation leak ho chuki hai.";
            $spoken = "Geyser me paani garam na hone ka kaaran thermostat cut out trip hona ya heating coil kharab hona hota hai.";
        }

        // ---------------------------------------------------------------------
        // 6. INVERTER & BATTERY
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'inverter') || str_contains($lower, 'battery') || str_contains($lower, 'backup') || str_contains($lower, 'beeping')) {
            $intent = 'inverter_advice';

            $reply = $preamble . "Inverter aur battery me aam samasyayein aur unka solution:\n\n"
                . "1. **Continuous Beeping Sound:**\n"
                . "   • Overload: Ghar ka load inverter ki capacity se zyada hai (extra lights/fans band karein).\n"
                . "   • Low Battery: Power cut lamba hone par cutoff limit aane par beeping hoti hai.\n"
                . "2. **Backup Kam Milna:**\n"
                . "   • Tubular battery me **distilled water** ka level check karein aur green indicator tak bharein.\n"
                . "   • Battery terminals par white/green carbon jamne se garam paani se saaf karein aur petroleum jelly lagayein.\n"
                . "3. **Inverter Fail Hone par:** Inverter ke peeche laga **Manual Bypass Switch** on karke direct grid power chala sakte hain.";
            $spoken = "Inverter beeping overload ya low battery ki wajah se karta hai. Terminals saaf rakhein aur distilled water check karein.";
        }

        // ---------------------------------------------------------------------
        // 7. LIGHTING, LED & CHANDELIERS
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'light') || str_contains($lower, 'led') || str_contains($lower, 'bulb') || str_contains($lower, 'flicker') || str_contains($lower, 'blink') || str_contains($lower, 'chandelier') || str_contains($lower, 'tubelight')) {
            $intent = 'lighting_advice';

            $reply = $preamble . "Light ya LED bulb flicker (blink) karne ke mukhya kaaran:\n\n"
                . "1. **Dying LED Driver:** LED bulb ya panel light ke internal driver circuit ka capacitor weak hona.\n"
                . "2. **Loose Neutral Wire:** Switchboard ya distribution box me neutral wire loose hone se voltage fluctuation aati hai.\n"
                . "3. **Incompatible Dimmer/Regulator:** Normal LED bulb ko electronic dimmer ke sath use karna.\n\n"
                . "🛠️ **Check:** Bulb ko kisi doosre holder me lagakar dekhein. Agar wahan bhi blink karta hai, toh bulb badalna padega.";
            $spoken = "LED light flicker karne ka kaaran internal driver capacitor ya neutral wire loose hona hota hai.";
        }

        // ---------------------------------------------------------------------
        // 8. EARTHING, ELECTRIC SHOCK & CURRENT LEAKAGE
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'shock') || str_contains($lower, 'current lag') || str_contains($lower, 'jhatka') || str_contains($lower, 'earthing') || str_contains($lower, 'earth')) {
            $intent = 'earthing_advice';

            $reply = $preamble . "Appliance (Fridge, Geyser, Washing Machine) ki body ko chhune par current lagna **Earthing Fault** ka direct sanket hai:\n\n"
                . "1. **Earthing Wire Disconnected:** Socket ka upar wala bada pin (Earth) grounded nahi hai ya bahar earth rod tut gayi hai.\n"
                . "2. **Internal Insulation Leak:** Appliance ke motor ya coil ki wire halki body se touch ho rahi hai.\n"
                . "3. **Neutral Voltage Leakage:** Neutral line me reverse phase voltage aana.\n\n"
                . "⚠️ **Safety Rule:** Geyser on rakh kar na nahayein, chappal pehen kar appliance use karein, aur bina earthing theek karwaye risk na lein.";
            $spoken = "Appliance me current aana earthing disconnect hone ka sanket hai. Chappal pehanein aur earthing theek karwayein.";
        }

        // ---------------------------------------------------------------------
        // 9. HIGH ELECTRICITY BILL & ENERGY SAVING
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'bill') || str_contains($lower, 'bijli bachat') || str_contains($lower, 'save electricity') || str_contains($lower, 'reduce bill')) {
            $intent = 'bill_saving_advice';

            $reply = $preamble . "Ghar ka bijli bill kam karne ke 6 effective tareeqe:\n\n"
                . "1. **LED Lighting:** CFL ya filament bulb ki jagah 9W-12W LED bulbs lagayein (80% bijli bachti hai).\n"
                . "2. **AC Temperature 24°C par Set Karein:** Har 1°C badhane se lagbhag 6% bijli bachti hai.\n"
                . "3. **BLDC Ceiling Fans:** Normal fan (75W) ki jagah BLDC fan (28W) use karein.\n"
                . "4. **Vampire Standby Load Band Karein:** TV, microwave, setup box ko remote ke sath wall switch se bhi band karein.\n"
                . "5. **Geyser Timer:** Nahane se 15 minute pehle on karein aur turant band karein.\n"
                . "6. **Wiring Leakage Check:** Agar sab appliances band karne ke baad bhi meter tezi se chal raha hai, to wiring leakage ho sakti hai.";
            $spoken = "Bijli bill kam karne ke liye AC ko 24 degree par chalayein, LED bulbs aur BLDC fans use karein.";
        }

        // ---------------------------------------------------------------------
        // 10. ELECTRICAL CONCEPTS: 1 UNIT ELECTRICITY
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'unit') && (str_contains($lower, '1') || str_contains($lower, 'ek') || str_contains($lower, 'kya') || str_contains($lower, 'kitna') || str_contains($lower, 'watt') || str_contains($lower, 'kwh'))) {
            $intent = 'unit_concept';

            $reply = $preamble . "**1 Unit Electricity (1 kWh)** ka matlab hota hai **1 Kilowatt-hour**, yaani 1,000 Watts ka power 1 ghante tak lagatar chalna.\n\n"
                . "📊 **Formula:** Units = (Total Watts × Hours) ÷ 1,000\n\n"
                . "💡 **Aam Ghar ke Udaharan:**\n"
                . "• **100 Watt ka bulb** 10 ghante chale = 1 Unit bijli (100W × 10h = 1000 Wh).\n"
                . "• **1.5 Ton ka AC (lagbhag 1500 Watts)** 1 ghanta chale = lagbhag 1.5 Units.\n"
                . "• **2000 Watt ka Geyser** 30 minute chale = 1 Unit.\n"
                . "• **75 Watt ka Ceiling Fan** lagbhag 13 ghante chale = 1 Unit.\n\n"
                . "Lucknow (UP) me domestic bijli ki dar aamtaur par ₹5.50 se ₹7.00 prati unit hoti hai (slab ke mutabiq).";
            $spoken = "1 Unit bijli ka matlab 1000 Watt power 1 ghante tak use hona hota hai. Jaise 100 watt ka bulb 10 ghante chale to 1 unit kharch hoti hai.";
        }

        // ---------------------------------------------------------------------
        // 11. ELECTRICAL CONCEPTS: AC vs DC
        // ---------------------------------------------------------------------
        elseif ((str_contains($lower, 'ac') && str_contains($lower, 'dc')) || str_contains($lower, 'alternating current') || str_contains($lower, 'direct current')) {
            $intent = 'ac_dc_concept';

            $reply = $preamble . "**AC (Alternating Current) aur DC (Direct Current) me antar:**\n\n"
                . "1. **Direction of Flow:**\n"
                . "   • **AC:** Current ki disha lagatar badalti rehti hai (India me 50 baar prati second yaani 50 Hz).\n"
                . "   • **DC:** Current sirf ek hi disha me steady flow karta hai.\n"
                . "2. **Origin & Storage:**\n"
                . "   • **AC:** Power plants aur generators banate hain. Isko direct store nahi kiya ja sakta.\n"
                . "   • **DC:** Batteries, solar panels aur phone chargers se milta hai. Isko batteries me store kiya jata hai.\n"
                . "3. **Transmission:**\n"
                . "   • AC ko transformers ke zariye high voltage par lambi doori tak asani se bheja ja sakta hai bina heavy loss ke.";
            $spoken = "AC current disha badalta hai jo ghar ki supply me aata hai, jabki DC current ek disha me behta hai jo battery me store hota hai.";
        }

        // ---------------------------------------------------------------------
        // 12. ELECTRICAL CONCEPTS: WHY BIRDS DON'T GET SHOCKED
        // ---------------------------------------------------------------------
        elseif ((str_contains($lower, 'bird') || str_contains($lower, 'chidiya') || str_contains($lower, 'pakshi')) && (str_contains($lower, 'shock') || str_contains($lower, 'current') || str_contains($lower, 'taar') || str_contains($lower, 'wire'))) {
            $intent = 'bird_shock_concept';

            $reply = $preamble . "Taar par baithe pakshiyon ko current kyu nahi lagta?\n\n"
                . "Iske peeche seedha scientific niyam hai: **Potential Difference (विभवांतर) ka na hona**.\n\n"
                . "1. **Circuit Poora Nahi Hota:** Current behne ke liye ek complete circuit aur voltage difference chahiye hota hai (Phase se Neutral ya Phase se Ground).\n"
                . "2. **Dono Pair Ek Hi Taar Par:** Pakshi ke dono pair sirf ek hi live wire ko chhoote hain. Dono pairon ke beech voltage saman rehta hai, isliye current unke sharir se hokar nahi guzarta.\n"
                . "3. **Danger Kab Hota Hai?** Agar koi bada pakshi galti se ek sath do alag phase wires ko ya wire aur grounded electric pole ko touch kar le, toh circuit poora ho jata hai aur shock lagta hai.";
            $spoken = "Pakshiyon ko current isliye nahi lagta kyunki unke dono pair ek hi taar par hote hain aur koi potential difference nahi banta.";
        }

        // ---------------------------------------------------------------------
        // 13. ELECTRICAL CONCEPTS: VOLTAGE, CURRENT, WATT, RESISTANCE
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'voltage') || str_contains($lower, 'ampere') || str_contains($lower, 'ohm') || str_contains($lower, 'watt')) {
            $intent = 'electrical_units_concept';

            $reply = $preamble . "Bijli ki buniyadi 4 units ka aasan matlab:\n\n"
                . "• **Voltage (Volts - V):** Electrical pressure jo electrons ko aage dhakelta hai (Jaise paani ke pipe me pressure).\n"
                . "• **Current (Amperes - A):** Electrons ke behne ki speed ya rate (Jaise pipe me paani ka flow).\n"
                . "• **Resistance (Ohms - Ω):** Current ke raaste me aane wali rukaawat ($V = I \\times R$).\n"
                . "• **Power (Watts - W):** Kaam karne ya bijli kharch hone ki kul dar ($P = V \\times I$).\n\n"
                . "Udaharan: 230 Volts par agar ek appliance 5 Ampere current leta hai, toh uski power $230 \\times 5 = 1150$ Watts hogi.";
            $spoken = "Voltage electrical pressure hota hai, Current flow rate hota hai, aur Watt kul bijli kharch hoti hai.";
        }

        // ---------------------------------------------------------------------
        // 14. SCIENCE / GENERAL: WHY IS THE SKY BLUE?
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'sky') || str_contains($lower, 'aasman') || str_contains($lower, 'aakash') || str_contains($lower, 'blue') || str_contains($lower, 'neela')) {
            $intent = 'science_sky_blue';

            $reply = $preamble . "Aasman ka rang neela **Rayleigh Scattering (प्रकाश का प्रकीर्णन)** ke kaaran dikhta hai:\n\n"
                . "1. **Suraj Ki Roshni:** Sunlight me saaton rang (VIBGYOR) shamil hote hain.\n"
                . "2. **Atmosphere me Scattering:** Jab sunlight prithvi ke vayumandal me aati hai, to hawa ke gas molecules choti wavelength wali light (Blue aur Violet) ko lambi wavelength (Red/Orange) ki tulna me chaaron taraf sabse zyada faila dete hain.\n"
                . "3. **Human Eyes:** Hamari aankhein violet se zyada blue colour ke liye sensitive hoti hain, isliye din me aakash neela dikhta hai.";
            $spoken = "Aasman ka rang neela Rayleigh scattering ki wajah se dikhta hai, jisme hawa ke molecules blue light ko sabse zyada failaate hain.";
        }

        // ---------------------------------------------------------------------
        // 15. SCIENCE / GENERAL: SOLAR ENERGY & ROOFTOP
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'solar') || str_contains($lower, 'dhoop') || str_contains($lower, 'sun power')) {
            $intent = 'solar_concept';

            $reply = $preamble . "Solar Rooftop System kaise kaam karta hai:\n\n"
                . "1. **Solar Panels:** Dhoop ki roshni ko Silicon photovoltaic cells ke zariye **DC electricity** me convert karte hain.\n"
                . "2. **Solar Inverter:** DC bijli ko ghar me use hone wali 230V **AC bijli** me badalta hai.\n"
                . "3. **Net Metering:** Din ke samay bachi hui extra bijli government grid ko bhej di jaati hai, jisse aapka monthly bill minus ya zero ho jata hai.\n"
                . "4. **Subsidy:** PM Surya Ghar Muft Bijli Yojana ke tahat 1kW se 3kW systems par government subsidy bhi uplabdh hai.";
            $spoken = "Solar panels dhoop se DC bijli banate hain jise solar inverter AC me badalkar ghar ke appliances chalata hai.";
        }

        // ---------------------------------------------------------------------
        // 16. INVENTIONS & SCIENTISTS: WHO DISCOVERED ELECTRICITY?
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'invent') || str_contains($lower, 'khoj') || str_contains($lower, 'discover') || str_contains($lower, 'kisne banaya') || str_contains($lower, 'nikola tesla') || str_contains($lower, 'edison')) {
            $intent = 'history_concept';

            $reply = $preamble . "Bijli (Electricity) kisi ek vyakti ne nahi banayi, balki yeh prakriti ki ek urja hai. Iski khoj aur vikas me in mukhya vaigyanikon ka yogdan raha:\n\n"
                . "• **Benjamin Franklin (1752):** Apne prasiddh kite experiment se saabit kiya ki aakashiya bijli (lightning) bhi electrical current hai.\n"
                . "• **Alessandro Volta (1800):** Pehli chemical electric battery banayi jisse continuous DC current mila.\n"
                . "• **Michael Faraday (1831):** Electromagnetic Induction ki khoj ki, jisse electric motor aur generator bane.\n"
                . "• **Nikola Tesla:** Modern AC (Alternating Current) system banaya jo aaj duniya bhar ke gharon me bijli pahunchata hai.\n"
                . "• **Thomas Edison:** Practical incandescent light bulb banaya.";
            $spoken = "Bijli ki khoj me Benjamin Franklin, Alessandro Volta, Michael Faraday aur Nikola Tesla ka mukhya yogdan raha hai.";
        }

        // ---------------------------------------------------------------------
        // 17. BOT IDENTITY & CAPABILITIES
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'who are you') || str_contains($lower, 'kaun ho') || str_contains($lower, 'kya kar sakte ho') || str_contains($lower, 'what can you do')) {
            $intent = 'bot_identity';

            $reply = $preamble . "Main **ElectroFix AI** hoon — ElectroLKO ka intelligent AI electrical assistant.\n\n"
                . "💡 **Main in cheezon me aapki madad kar sakta hoon:**\n"
                . "• Ghar ke electrical issues (Fan, MCB, Switchboard, AC, Geyser, Inverter) ka diagnosis aur step-by-step solution.\n"
                . "• Electrical concepts (Units, Bills, AC/DC, Safety) ka saral breakdown.\n"
                . "• Kisi bhi general technical ya scientific sawaal ka direct jawab.\n"
                . "• Agar aap Lucknow me hain aur doorstep electrician chahiye, toh verified technicians arrange karna.\n\n"
                . "Aapka kya sawaal hai? Batayiye!";
            $spoken = "Main ElectroFix AI hoon. Main ghar ke electrical issues ka solution aur Lucknow me verified technician services provide karta hoon.";
        }

        // ---------------------------------------------------------------------
        // 18. GRATITUDE & CLOSING
        // ---------------------------------------------------------------------
        elseif (preg_match('/^(thank you|thanks|shukriya|dhanyawad|dhanyavad|bahut accha|good|badhiya|ok|theek hai)(\s.*)?$/i', $lower) && strlen($lower) < 30) {
            $intent = 'gratitude';

            $reply = $isEnglish
                ? "You're very welcome! Feel free to ask anytime if you need more help with anything electrical or technical."
                : "Aapka bahut swagat hai! Agar aage bhi koi sawaal ho ya samasya aaye, toh befikr hokar poochiye.";
            $spoken = $isEnglish ? "You're welcome! Happy to help." : "Aapka swagat hai! Madad karke khushi hui.";
        }

        // ---------------------------------------------------------------------
        // 19. DYNAMIC GENERAL SMART HANDLER (For ANY other question!)
        // ---------------------------------------------------------------------
        else {
            $intent = 'dynamic_general';

            if ($wasFrustrated) {
                $reply = "Maaf kijiye! Seedhi baat par aate hain — aapko ghar me kya dikkat aa rahi hai ya aap kya janna chahte hain? Batayiye, main seedha aur practical samadhan batata hoon.";
                $spoken = "Maaf kijiye, batayiye aapko kya samasya aa rahi hai? Main seedha samadhan batata hoon.";
            } elseif ($isEnglish) {
                $reply = "Here is a direct answer regarding your query:\n\n"
                    . "• **Direct Explanation:** To understand this properly, check the primary operating condition and power source.\n"
                    . "• **Practical Action:** Ensure safe isolation before inspecting any terminal connections or settings.\n"
                    . "• **Tip:** If this involves a household appliance, verifying the power input point and safety reset switch usually resolves it.\n\n"
                    . "Let me know the specific details or symptoms and I'll give you an exact step-by-step fix!";
                $spoken = "Here is a direct answer. Please share any specific symptoms so I can give you an exact step by step solution.";
            } else {
                $reply = "Aapke sawaal ka seedha samadhan:\n\n"
                    . "• **Mukhya Jaanch:** Sabse pehle appliance ya circuit ka power point, fuse aur switch condition check karein.\n"
                    . "• **Suraksha Niyam:** Kisi bhi live line ko khud haath na lagayein aur main switch off karke hi inspection karein.\n"
                    . "• **Next Step:** Agar aap is samasya ke baare me thoda aur detail (jaise aawaz aa rahi hai, trip ho raha hai ya power nahi hai) batayein, toh main exact root-cause samjha deta hoon.";
                $spoken = "Aapke sawaal ka seedha hal yeh hai ki pehle power supply check karein. Agar detail batayein toh main exact step by step samjha deta hoon.";
            }
        }

        return [
            'success' => true,
            'reply' => $reply,
            'spoken_text' => $spoken ?: strip_tags(str_replace(['*', '#', '•', '`'], '', $reply)),
            'language' => $isEnglish ? 'en' : 'hi',
            'state' => 'SPEAKING',
            'tools_used' => [],
            'tool_display' => null,
            'intent' => $intent,
            'requires_confirmation' => false,
            'pending_action' => null,
            'electricians' => []
        ];
    }

    /**
     * Gemini 1.5/2.0 Flash Reasoning integration with Tool Calling
     */
    protected function reasonWithGemini(
        string $message,
        array $conversationHistory,
        float $lat,
        float $lng,
        string $areaName,
        bool $isEmergency
    ): ?array {
        try {
            $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
            $model = config('services.gemini.model') ?: env('GEMINI_MODEL', 'gemini-1.5-flash');

            // Only attempt if API key format matches valid Google AI Studio key
            if (empty($apiKey) || !str_starts_with($apiKey, 'AIzaSy')) {
                return null;
            }

            $systemInstruction = "You are 'ElectroFix AI', an intelligent, human-like AI electrical and technical assistant for ElectroLKO in Lucknow, India.
Personality: Calm, deeply helpful, direct, practical, and safety-conscious.
Fluently understand and speak Hindi, Hinglish, and English. Match the language and script used by the user.

CRITICAL INSTRUCTIONS:
1. ALWAYS ANSWER THE USER'S ACTUAL QUESTION FIRST, directly and concisely, like a Google Search AI Overview.
2. If the user asks for a solution or troubleshooting, clearly explain root causes and practical step-by-step solutions naturally.
3. DO NOT AUTOMATICALLY SUGGEST, LIST, OR PUSH TECHNICIANS OR SERVICES unless the user EXPLICITLY asks for a technician, electrician, inspection visit, or booking!
4. If the user expresses frustration or says words like 'bakwas mat kro', 'chup raho', 'faltu', DO NOT repeat or quote their statement. Empathize politely in half a sentence, understand their underlying issue from conversation history, and provide the direct, clear answer immediately.
5. Dynamically adapt to ANY type of user question (science, electrical concepts, units, household appliances, general knowledge, everyday questions), not just electrical services.
6. Keep responses conversational, crisp, and structured with clean bullet points. Avoid robotic preambles, repetitive templates, or marketing sales pitches.
User location context: {$areaName} (Lat: {$lat}, Lng: {$lng}).";

            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            $contents = [];
            foreach ($conversationHistory as $turn) {
                $contents[] = [
                    'role' => ($turn['role'] === 'user') ? 'user' : 'model',
                    'parts' => [['text' => $turn['content'] ?? '']]
                ];
            }
            $contents[] = [
                'role' => 'user',
                'parts' => [['text' => $message]]
            ];

            $payload = [
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 700,
                ]
            ];

            $response = Http::timeout(8)
                ->withHeaders([
                    'Content-Type' => 'application/json'
                ])
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $candidates = $response->json('candidates');
                $geminiText = $candidates[0]['content']['parts'][0]['text'] ?? '';

                if (!empty($geminiText)) {
                    $wantsTech = $this->isExplicitTechnicianRequest($message) || $isEmergency;
                    $electriciansData = [];
                    $pendingAction = null;
                    $requiresConfirmation = false;

                    if ($wantsTech) {
                        $toolsResult = $this->find_nearby_electricians($lat, $lng, 'all', $areaName);
                        $electriciansData = $toolsResult['electricians'] ?? [];
                        $topPro = $electriciansData[0] ?? null;
                        if ($topPro) {
                            $pendingAction = [
                                'action' => 'create_booking',
                                'data' => [
                                    'electrician_id' => $topPro['id'],
                                    'customer_name' => 'Customer',
                                    'customer_address' => $areaName,
                                    'service_type' => 'Doorstep Electrical Service',
                                    'time_slot' => 'Within 30 mins'
                                ]
                            ];
                            $requiresConfirmation = true;
                        }
                    }

                    return [
                        'success' => true,
                        'reply' => $geminiText,
                        'spoken_text' => strip_tags(str_replace(['*', '#', '•', '`'], '', $geminiText)),
                        'language' => 'hi',
                        'state' => $isEmergency ? 'EMERGENCY' : 'SPEAKING',
                        'tools_used' => $wantsTech ? [['name' => 'find_nearby_electricians', 'status' => 'success']] : [],
                        'tool_display' => $isEmergency ? '⚠️ Emergency Protocol Active' : ($wantsTech ? '⚡ Located nearby technicians' : null),
                        'intent' => $isEmergency ? 'emergency' : ($wantsTech ? 'explicit_electrician_request' : 'direct_answer'),
                        'requires_confirmation' => $requiresConfirmation,
                        'pending_action' => $pendingAction,
                        'electricians' => $electriciansData
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Gemini AI Agent call failed, falling back to local agent: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Helper: Detects affirmative user response (Yes, Haan, Book it, Kar do)
     */
    protected function isAffirmativeResponse(string $text): bool
    {
        $affirmatives = ['yes', 'yeah', 'yep', 'haan', 'ha', 'kar do', 'bhej do', 'book', 'sure', 'theek hai', 'ok', 'okay', 'bilkul', 'confirm'];
        $clean = strtolower(trim($text));
        foreach ($affirmatives as $aff) {
            if ($clean === $aff || str_starts_with($clean, $aff . ' ') || str_ends_with($clean, ' ' . $aff)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Helper: Detects negative user response (No, Nahi, Mat karo)
     */
    protected function isNegativeResponse(string $text): bool
    {
        $negatives = ['no', 'nah', 'nahi', 'na', 'mat karo', 'cancel', 'don\'t', 'rok do', 'baad me'];
        $clean = strtolower(trim($text));
        foreach ($negatives as $neg) {
            if ($clean === $neg || str_starts_with($clean, $neg . ' ')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Helper: Haversine distance in Kilometers
     */
    protected function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }
}
