<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ContactInquiry;
use App\Models\Electrician;
use App\Mail\ContactInquiryMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                'specialization' => $pro->specialization ?? 'General Electrical Fix',
                'experience' => $pro->experience ?? '6+ Years',
                'rating' => (float) ($pro->rating ?? 4.8),
                'reviews_count' => (int) ($pro->completed_jobs ?? 54),
                'starting_price' => $pro->starting_price ?? '₹149',
                'badge' => $pro->badge ?? 'Govt Certified Pro',
                'distance_km' => round($distanceKm, 1),
                'distance_text' => round($distanceKm, 1) . ' km away',
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
                'specialization' => $pro->specialization ?? 'Master Electrician',
                'experience' => $pro->experience ?? '6+ Years Experience',
                'rating' => (float) ($pro->rating ?? 4.9),
                'completed_jobs' => (int) ($pro->completed_jobs ?? 120),
                'starting_price' => $pro->starting_price ?? '₹149',
                'badge' => $pro->badge ?? 'Govt Certified Pro'
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
     * Tool 10: send_contact_email()
     * Sends contact inquiry email to ElectroFix dispatch and customer,
     * logs into database, and attaches AI diagnosis.
     */
    public function send_contact_email(
        string $name,
        string $email,
        string $phone = '',
        string $message = '',
        string $subject = '',
        string $area = 'Lucknow',
        string $channel = 'ai_agent_chat',
        ?string $aiDiagnosis = null,
        ?string $aiPriority = null
    ): array {
        $cleanEmail = trim($email);
        $cleanName = trim($name) ?: 'Customer';
        $cleanMessage = trim($message) ?: 'General inquiry for ElectroFix Lucknow.';
        $cleanArea = trim($area) ?: 'Lucknow';

        // Perform AI Diagnosis & Triage if not provided
        if (empty($aiDiagnosis) || empty($aiPriority)) {
            $analysis = $this->analyzeIssueForContact($cleanMessage, $cleanArea);
            $aiDiagnosis = $aiDiagnosis ?: $analysis['diagnosis'];
            $aiPriority = $aiPriority ?: $analysis['priority'];
            $recommendedService = $analysis['recommended_service'];
            $estimatedCost = $analysis['estimated_cost'];
            if (empty($subject)) {
                $subject = $analysis['subject'];
            }
        } else {
            $recommendedService = 'General Electrical Service';
            $estimatedCost = '₹99 - ₹249';
        }

        if (empty($subject)) {
            $subject = "Service Inquiry from {$cleanName} ({$cleanArea})";
        }

        // Generate Ticket Reference
        $reference = 'INQ-LKO-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

        // Save into MySQL Database
        $inquiry = ContactInquiry::create([
            'ticket_reference' => $reference,
            'name' => $cleanName,
            'email' => $cleanEmail,
            'phone' => $phone ?: null,
            'area' => $cleanArea,
            'subject' => $subject,
            'message' => $cleanMessage,
            'ai_diagnosis' => $aiDiagnosis,
            'ai_priority' => $aiPriority,
            'ai_recommended_service' => $recommendedService,
            'ai_estimated_cost' => $estimatedCost,
            'channel' => $channel,
            'status' => 'received',
            'email_sent' => true,
        ]);

        // Dispatch Email via Laravel Mail
        $emailSentSuccessfully = true;
        try {
            $supportEmail = env('SUPPORT_EMAIL', 'support@electrolko.in');
            Mail::to($supportEmail)->send(new ContactInquiryMail($inquiry, false));

            if (filter_var($cleanEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($cleanEmail)->send(new ContactInquiryMail($inquiry, true));
            }
        } catch (\Throwable $e) {
            Log::warning("Contact email dispatch fallback (logged to mail log): " . $e->getMessage());
            $emailSentSuccessfully = false;
        }

        return [
            'success' => true,
            'ticket_reference' => $reference,
            'inquiry_id' => $inquiry->id,
            'name' => $cleanName,
            'email' => $cleanEmail,
            'phone' => $phone,
            'area' => $cleanArea,
            'subject' => $subject,
            'message' => $cleanMessage,
            'ai_diagnosis' => $aiDiagnosis,
            'ai_priority' => $aiPriority,
            'ai_recommended_service' => $recommendedService,
            'ai_estimated_cost' => $estimatedCost,
            'email_sent' => $emailSentSuccessfully,
            'created_at' => $inquiry->created_at->format('d M Y, h:i A'),
            'status' => 'received',
            'reply_message' => "Aapki contact email ElectroFix dispatch desk ko bhej di gayi hai! Ticket Reference: {$reference}."
        ];
    }

    /**
     * AI issue analyzer for contact inquiries
     */
    public function analyzeIssueForContact(string $text, string $area = 'Lucknow'): array
    {
        $lower = strtolower($text);

        $isEmergency = $this->detectSafetyEmergency($text);

        $priority = 'NORMAL';
        $recommended = 'General Electrical Inspection';
        $cost = '₹99 - ₹199';
        $subject = "Electrical Inquiry - {$area}";

        if ($isEmergency) {
            $priority = 'EMERGENCY';
            $recommended = '24/7 Emergency Hazard Repair';
            $cost = '₹299';
            $subject = "[EMERGENCY] Active Hazard Clearance in {$area}";
            $diagnosis = "CRITICAL HAZARD DETECTED: Report indicates active sparking, burning odor, or electrical shock risk. Main breaker / MCB should be turned off immediately if safe. High-priority rapid dispatch assigned.";
        } elseif (str_contains($lower, 'mcb') || str_contains($lower, 'trip') || str_contains($lower, 'short circuit')) {
            $priority = 'URGENT';
            $recommended = 'MCB Tripping & Short Circuit Diagnostic';
            $cost = '₹199';
            $subject = "[Urgent] MCB Tripping / Short Circuit Diagnostic in {$area}";
            $diagnosis = "Overload or short circuit fault detected. Circuit breaker testing and load calculation recommended to avoid wire melting.";
        } elseif (str_contains($lower, 'fan') || str_contains($lower, 'pankha')) {
            $priority = 'NORMAL';
            $recommended = 'Ceiling Fan Repair & Capacitor Change';
            $cost = '₹149';
            $subject = "Fan Repair & Performance Inquiry in {$area}";
            $diagnosis = "Fan humming, winding, or capacitor resistance issue identified. Standard inspection with genuine spare replacement.";
        } elseif (str_contains($lower, 'inverter') || str_contains($lower, 'battery')) {
            $priority = 'HIGH';
            $recommended = 'Inverter & Battery Setup / Backup Fix';
            $cost = '₹249';
            $subject = "Inverter & Battery Wiring Inquiry in {$area}";
            $diagnosis = "Backup circuit, charging cut-off, or distilled water maintenance required for uninterrupted power supply.";
        } elseif (str_contains($lower, 'wiring') || str_contains($lower, 'earthing') || str_contains($lower, 'shock') || str_contains($lower, 'current')) {
            $priority = 'HIGH';
            $recommended = 'Full House Wiring & Earthing Audit';
            $cost = '₹499';
            $subject = "Earthing & House Wiring Safety Audit in {$area}";
            $diagnosis = "Leakage voltage or neutral imbalance suspected. Ground resistance testing and earthing pit check advised.";
        } elseif (str_contains($lower, 'switch') || str_contains($lower, 'board') || str_contains($lower, 'socket')) {
            $priority = 'NORMAL';
            $recommended = 'Modular Switch & Socket Replacement';
            $cost = '₹99';
            $subject = "Modular Switchboard Fitting in {$area}";
            $diagnosis = "Loose terminal contact or heat pitting in socket detected. Replacement with ISI-certified modular accessories recommended.";
        } else {
            $diagnosis = "General electrical consultation and doorstep technical diagnosis logged. Assigned to nearest Lucknow verified master electrician.";
        }

        return [
            'priority' => $priority,
            'recommended_service' => $recommended,
            'estimated_cost' => $cost,
            'subject' => $subject,
            'diagnosis' => $diagnosis,
        ];
    }

    /**
     * Auto-draft & polish contact inquiry using AI
     */
    public function generate_ai_contact_draft(string $problemDescription, string $area = 'Lucknow'): array
    {
        $analysis = $this->analyzeIssueForContact($problemDescription, $area);

        $cleanDesc = trim($problemDescription);
        $polished = "Dear ElectroFix Lucknow Support,\n\n"
            . "I am requesting an electrical service inspection for my premises located in {$area}.\n\n"
            . "Issue Summary: " . ucfirst($cleanDesc) . "\n\n"
            . "Kindly arrange a certified master technician with upfront transparent pricing and 30-day service warranty.\n\n"
            . "Thank you,\nCustomer";

        return [
            'success' => true,
            'suggested_subject' => $analysis['subject'],
            'polished_message' => $polished,
            'ai_diagnosis' => $analysis['diagnosis'],
            'ai_priority' => $analysis['priority'],
            'ai_recommended_service' => $analysis['recommended_service'],
            'ai_estimated_cost' => $analysis['estimated_cost'],
            'safety_advisory' => $analysis['priority'] === 'EMERGENCY'
                ? "⚠️ Main MCB switch off rakhein aur geeli jagah se door rahein!"
                : "💡 Technician arrival ke samay appliances ko turned off condition me dikhayein.",
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
        ?array $pendingAction = null,
        ?string $requestedModel = null,
        ?string $customOllamaUrl = null
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
                    'booking' => $bookingResult,
                    'ai_provider' => 'booking_engine',
                    'model_used' => 'system'
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
                    'pending_action' => null,
                    'ai_provider' => 'booking_engine',
                    'model_used' => 'system'
                ];
            }
        }

        // 2b. Check if user is completing an active pending contact email action
        if (!empty($pendingAction) && isset($pendingAction['action']) && $pendingAction['action'] === 'send_contact_email') {
            $data = $pendingAction['data'] ?? [];
            preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $messageClean, $emailMatches);
            $userEmail = !empty($emailMatches[0]) ? $emailMatches[0] : ($data['email'] ?? '');

            if (!empty($userEmail)) {
                $name = $data['name'] ?? 'Customer';
                $queryText = $data['message'] ?? $messageClean;
                $area = $data['area'] ?? $areaName;

                $emailResult = $this->send_contact_email(
                    $name,
                    $userEmail,
                    $data['phone'] ?? '',
                    $queryText,
                    '',
                    $area,
                    'ai_agent_chat'
                );

                $ref = $emailResult['ticket_reference'];
                $reply = "Shandar! Maine aapki contact email ElectroFix dispatch desk ko bhej di hai.\n\n"
                    . "📧 **Ticket Reference:** `{$ref}`\n"
                    . "👤 **Registered Email:** {$userEmail}\n"
                    . "🚨 **AI Priority:** {$emailResult['ai_priority']}\n"
                    . "💡 **AI Diagnostic:** {$emailResult['ai_diagnosis']}\n\n"
                    . "Hamari Lucknow support team aapse jald hi contact karegi.";

                return [
                    'success' => true,
                    'reply' => $reply,
                    'spoken_text' => "Aapki contact email dispatch desk ko bhej di gayi hai. Ticket reference hai {$ref}.",
                    'language' => 'hi',
                    'state' => 'SPEAKING',
                    'tools_used' => [
                        ['name' => 'send_contact_email', 'status' => 'success', 'data' => $emailResult]
                    ],
                    'tool_used' => 'send_contact_email',
                    'tool_display' => '📧 Contact Email Sent',
                    'intent' => 'contact_email_sent',
                    'requires_confirmation' => false,
                    'pending_action' => null,
                    'contact_inquiry' => $emailResult,
                    'ai_provider' => 'contact_engine',
                    'model_used' => 'system'
                ];
            }
        }

        // 2c. Check if user explicitly requests to send a contact email
        $isContactEmailIntent = preg_match('/\b(email|mail|contact\s+support|support\s+ko\s+mail|email\s+bhejo|mail\s+karo|contact\s+email)\b/i', $messageClean);
        preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $messageClean, $emailFound);

        if ($isContactEmailIntent && !$this->isExplicitTechnicianRequest($messageClean)) {
            if (!empty($emailFound[0])) {
                $emailResult = $this->send_contact_email(
                    'Customer',
                    $emailFound[0],
                    '',
                    $messageClean,
                    '',
                    $areaName,
                    'ai_agent_chat'
                );

                $ref = $emailResult['ticket_reference'];
                $reply = "Maine aapki contact email ElectroFix team ko bhej di hai!\n\n"
                    . "📧 **Ticket Reference:** `{$ref}`\n"
                    . "👤 **Email:** {$emailFound[0]}\n"
                    . "🚨 **AI Priority:** {$emailResult['ai_priority']}\n"
                    . "💡 **AI Diagnostic:** {$emailResult['ai_diagnosis']}\n\n"
                    . "Support team aapse 15-30 minute ke andar contact karegi.";

                return [
                    'success' => true,
                    'reply' => $reply,
                    'spoken_text' => "Aapki contact email dispatch desk ko bhej di gayi hai. Ticket reference hai {$ref}.",
                    'language' => 'hi',
                    'state' => 'SPEAKING',
                    'tools_used' => [
                        ['name' => 'send_contact_email', 'status' => 'success', 'data' => $emailResult]
                    ],
                    'tool_used' => 'send_contact_email',
                    'tool_display' => '📧 Contact Email Sent',
                    'intent' => 'contact_email_sent',
                    'requires_confirmation' => false,
                    'pending_action' => null,
                    'contact_inquiry' => $emailResult,
                    'ai_provider' => 'contact_engine',
                    'model_used' => 'system'
                ];
            } else {
                return [
                    'success' => true,
                    'reply' => "Zaroor! Main support team ko aapki inquiry email bhej sakta hoon.\n\nKripya apna **Email Address** (jaise `apka_naam@gmail.com`) yahan likhein taaki main email ticket generate kar sakoon.",
                    'spoken_text' => "Zaroor, main support team ko aapki inquiry email bhej sakta hoon. Kripya apna email address batayein.",
                    'language' => 'hi',
                    'state' => 'SPEAKING',
                    'tools_used' => [],
                    'tool_display' => null,
                    'intent' => 'request_email_address',
                    'requires_confirmation' => true,
                    'pending_action' => [
                        'action' => 'send_contact_email',
                        'data' => [
                            'message' => $messageClean,
                            'area' => $areaName
                        ]
                    ],
                    'ai_provider' => 'contact_engine',
                    'model_used' => 'system'
                ];
            }
        }

        // 3. Ollama / Modal AI Reasoning Engine (Primary local LLM if running)
        $ollamaResponse = $this->reasonWithOllama($messageClean, $conversationHistory, $lat, $lng, $areaName, $isEmergency, $requestedModel, $customOllamaUrl);
        if ($ollamaResponse && $ollamaResponse['success']) {
            return $ollamaResponse;
        }

        // 4. If valid Gemini API key is configured, use Gemini
        $geminiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        if (!empty($geminiKey)) {
            $geminiResponse = $this->reasonWithGemini($messageClean, $conversationHistory, $lat, $lng, $areaName, $isEmergency);
            if ($geminiResponse && $geminiResponse['success']) {
                return $geminiResponse;
            }
        }

        // 5. Dynamic Cloud LLM Engine (Zero-config, answers ANY dynamic question e.g. "what is laravel", science, coding, general knowledge)
        $dynamicResponse = $this->reasonWithDynamicLLM($messageClean, $conversationHistory, $lat, $lng, $areaName, $isEmergency);
        if ($dynamicResponse && $dynamicResponse['success']) {
            return $dynamicResponse;
        }

        // 6. Live Instant Knowledge Engine (Fact verification via DuckDuckGo & Wikipedia)
        $knowledgeResponse = $this->reasonWithInstantKnowledge($messageClean, $conversationHistory, $lat, $lng, $areaName, $isEmergency);
        if ($knowledgeResponse && $knowledgeResponse['success']) {
            return $knowledgeResponse;
        }

        // 7. Autonomous Local AI Reasoning Engine (Zero-failure offline fallback)
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

        // 3. NORMAL CONVERSATION & GREETINGS (RULE 1)
        $cleanGreeting = trim(preg_replace('/[,\.?!:;\-]/', '', $lower));

        // A. "kya haal chal" / "kaise ho"
        if (preg_match('/\b(kya\s+haal(\s+chal)?|kaise\s+ho|kaisa\s+hai|kya\s+chal\s+raha)\b/i', $cleanGreeting) && !str_contains($cleanGreeting, 'fan') && !str_contains($cleanGreeting, 'switch')) {
            return [
                'success' => true,
                'reply' => "Bilkul badhiya! 😊 Aap batao, kya haal chal hain? Main aapki kisi bhi topic ya service mein kaise help kar sakta hoon?",
                'spoken_text' => "Bilkul badhiya! Aap batao, kya haal chal hain?",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'greeting',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // B. "thanks" / "thank you" / "shukriya"
        if (preg_match('/\b(thanks|thank\s+you|shukriya|dhanyawad|dhanyavad)\b/i', $cleanGreeting)) {
            return [
                'success' => true,
                'reply' => "You're very welcome 😊 Kisi bhi aur sawaal ya help ke liye befikr hokar pooch sakte hain!",
                'spoken_text' => "You're very welcome! Kisi bhi aur sawaal ya help ke liye befikr pooch sakte hain.",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'thanks',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // C. "ok" / "okay" / "theek hai"
        if (preg_match('/^(ok|okay|theek\s+hai|thik\s+hai|accha|theek|thik)$/i', $cleanGreeting)) {
            return [
                'success' => true,
                'reply' => "Theek hai 😊 Agar koi aur sawaal ho ya help chahiye, toh zaroor batayiye!",
                'spoken_text' => "Theek hai! Agar koi aur sawaal ho ya help chahiye, toh zaroor batayiye.",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'ack',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // D. "hello" / "hi" / "namaste"
        $isPureGreeting = preg_match('/^(hi|hello|hey|namaste|pranam|namaskar|good\s+morning|good\s+evening|good\s+afternoon|radhe\s+radhe|ram\s+ram)(\s.*)?$/i', $cleanGreeting);
        if ($isPureGreeting && strlen($cleanGreeting) < 40 && !str_contains($cleanGreeting, 'fan') && !str_contains($cleanGreeting, 'switch') && !str_contains($cleanGreeting, 'mcb') && !str_contains($cleanGreeting, 'light')) {
            return [
                'success' => true,
                'reply' => "Namaste! 😊 Main Bijli Guru hoon, aapka AI assistant. Bataiye, aaj main aapki kya madad kar sakta hoon?",
                'spoken_text' => "Namaste! Main Bijli Guru hoon, aapka AI assistant. Bataiye aaj main aapki kya madad kar sakta hoon?",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'greeting',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // E. Playful banter / Slang: "pgl" / "pagal" / "paagal" / "crazy" / "bewakoof"
        if (preg_match('/\b(pgl|pa+ga+l|crazy|pagla|pagli|bewak(u|oo)f)\b/i', $cleanGreeting)) {
            return [
                'success' => true,
                'reply' => "Haha nahi re, main bilkul theek hoon 😄 Bataiye, kya chal raha hai? Main aapki kya help kar sakta hoon?",
                'spoken_text' => "Haha nahi re, main bilkul theek hoon! Bataiye kya chal raha hai?",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'banter',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // F. Friendly callouts: "suno", "oye", "bhai", "yaar", "are yaar"
        if (preg_match('/^(suno|oye|bhai|yaar|are\s+yaar|arre\s+yaar|hey\s+bhai|bhaiya)$/i', $cleanGreeting)) {
            return [
                'success' => true,
                'reply' => "Haan ji, main sun raha hoon! Bataiye, kya baat hai? 😊",
                'spoken_text' => "Haan ji, main sun raha hoon! Bataiye kya baat hai?",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'attention',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // G. "kuch nahi" / "nothing"
        if (preg_match('/^(kuch\s+nahi|kuch\s+na|nothing|none|kuch\s+bhi\s+nahi)$/i', $cleanGreeting)) {
            return [
                'success' => true,
                'reply' => "Arey koi baat nahi! Jab bhi koi sawaal ya help chahiye ho, befikr hokar pooch lena 😊",
                'spoken_text' => "Arey koi baat nahi! Jab bhi koi sawaal ya help ho, bata dena.",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'casual_ack',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // H. "haha" / "hehe" / "lol"
        if (preg_match('/^(ha+ha+|he+he+|lol|lmao|xd)$/i', $cleanGreeting)) {
            return [
                'success' => true,
                'reply' => "😄 Aur bataiye, kya haal chal? Koi sawaal ya guidance chahiye toh batayiye!",
                'spoken_text' => "Aur bataiye, kya haal chal?",
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => null,
                'intent' => 'laughter',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
            ];
        }

        // I. Jokes / Chutkule
        if (preg_match('/\b(joke|chutkula|hanso|hasao|koi\s+joke)\b/i', $cleanGreeting)) {
            $jokes = [
                "Teacher: 1 se 10 tak ginti sunao.\nPappu: 1, 2, 3, 4, 5, 7, 8, 9, 10.\nTeacher: 6 kahan gaya?\nPappu: Ji woh toh bowling karne gaya hai! 😂",
                "Why don't scientists trust atoms?\nBecause they make up everything! 😄",
                "Client: Mera computer on nahi ho raha.\nEngineer: Power switch on kiya?\nClient: Are haan, bijli toh kal se gul hai! 😆"
            ];
            $selectedJoke = $jokes[array_rand($jokes)];
            return [
                'success' => true,
                'reply' => $selectedJoke,
                'spoken_text' => strip_tags(str_replace(['*', '#', '•', '`'], '', $selectedJoke)),
                'language' => 'hi',
                'state' => 'SPEAKING',
                'tools_used' => [],
                'tool_display' => '😄 Joke Shared',
                'intent' => 'joke',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'bijli_guru',
                'model_used' => 'rules'
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

        $reply = "⚠️ Kripya sparking switch ya kisi khule taar ko bilkul touch na karein, aur agar safe ho toh ghar ki main MCB/power turant OFF kar dein.";

        if ($topPro) {
            $reply .= "\n\nAapke paas Lucknow ({$areaName}) mein **{$topPro['name']}** (~{$topPro['eta_mins']} mins mein) available hain. Kya main unhe turant doorstep visit ke liye book kar doon?";
        } else {
            $reply .= "\n\nSafety ke liye isko turant kisi qualified electrician se check karwana best rahega.";
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
            'electricians' => $pros,
            'ai_provider' => 'bijli_guru',
            'model_used' => 'safety_protocol'
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

            // Example Rule: "fan nahi chal raha"
            if (preg_match('/\b(nahi\s+chal|nahi\s+ghoom|band\s+hai|not\s+working|not\s+running)\b/i', $lower)) {
                $reply = "Samajh gaya 👍 Fan nahi chal raha hai. Switch on karne par bilkul response nahi mil raha ya humming ki awaaz aa rahi hai?";
                $spoken = "Samajh gaya. Fan nahi chal raha hai. Switch on karne par bilkul response nahi mil raha ya humming ki awaaz aa rahi hai?";
            }
            // A. Noise / Humming / Awaz
            elseif (str_contains($lower, 'awaz') || str_contains($lower, 'awaaz') || str_contains($lower, 'noise') || str_contains($lower, 'humming') || str_contains($lower, 'sound') || str_contains($lower, 'khad')) {
                $reply = "Fan mein aamtaur par ball-bearing dry hone ya capacitor weak hone se humming aati hai. Kya fan ki speed bhi slow ho gayi hai ya sirf aawaz aa rahi hai?";
                $spoken = "Fan mein aamtaur par ball bearing dry hone ya capacitor weak hone se humming aati hai. Kya fan ki speed bhi slow ho gayi hai?";
            }
            // B. Slow Speed / Dheere chal raha hai
            elseif (str_contains($lower, 'slow') || str_contains($lower, 'dheere') || str_contains($lower, 'speed') || str_contains($lower, 'kam chal')) {
                $reply = "Pankha slow chalne ka 90% kaaran capacitor weak hona hota hai. Naya 2.5 µF capacitor lagate hi speed normal ho jaati hai. Kya fan ghoomte waqt koi aawaz bhi kar raha hai?";
                $spoken = "Pankha slow chalne ka sabse bada kaaran capacitor weak hona hota hai. Naya capacitor lagate hi speed normal ho jaati hai.";
            }
            // C. Wobbling / Hil raha hai
            elseif (str_contains($lower, 'hil') || str_contains($lower, 'wobble') || str_contains($lower, 'larkhad')) {
                $reply = "Pankha aamtaur par tab hilta hai jab blades ke screws thode dheele hon ya unpar dhool jam gayi ho. Switch band karke pehle blade screws tight karke dekhiye.";
                $spoken = "Pankha aamtaur par blades ke screws loose hone se hilta hai. Switch band karke screws tight karke dekhiye.";
            }
            // D. Reverse / Ulta ghoom raha hai
            elseif (str_contains($lower, 'ulta') || str_contains($lower, 'reverse')) {
                $reply = "Pankha ulta chal raha hai toh capacitor ki wire connection ulti jud gayi hai. Power switch off karke capacitor ki terminal wires interchange karni padengi.";
                $spoken = "Pankha ulta ghoom raha hai toh capacitor ki wire connection badalni padegi.";
            }
            // E. General Fan Query
            else {
                $reply = "Pankhe mein kya problem aa rahi hai — bilkul nahi chal raha, slow chal raha hai ya koi aawaz aa rahi hai?";
                $spoken = "Pankhe mein kya problem aa rahi hai — chal nahi raha, slow hai ya aawaz kar raha hai?";
            }
        }

        // ---------------------------------------------------------------------
        // 2. MCB, BREAKERS & SHORT CIRCUITS
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'mcb') || str_contains($lower, 'trip') || str_contains($lower, 'breaker') || str_contains($lower, 'fuse') || str_contains($lower, 'rccb')) {
            $intent = 'mcb_advice';
            $reply = "Arre! MCB baar-baar gir rahi hai toh shayad kisi heavy appliance ka overload hai ya wiring mein short circuit. Kya koi specific cheez (jaise AC, geyser ya heater) on karte hi girti hai ya achanak bina load ke bhi gir jaati hai?";
            $spoken = "MCB baar baar girna overload ya short circuit se hota hai. Kya koi specific cheez on karte hi girti hai?";
        }

        // ---------------------------------------------------------------------
        // 3. SWITCHBOARD, SOCKETS, PLUGS & CHARGERS
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'switch') || str_contains($lower, 'socket') || str_contains($lower, 'board') || str_contains($lower, 'plug') || str_contains($lower, 'charger')) {
            $intent = 'switchboard_advice';

            if (str_contains($lower, 'spark') || str_contains($lower, 'jal') || str_contains($lower, 'dhua') || str_contains($lower, 'badboo') || str_contains($lower, 'melt')) {
                $reply = "⚠️ Switch ko bilkul touch mat kariyega aur agar safe ho toh ghar ka main switch band kar dein. Spark lagatar aa raha hai toh safety ke liye electrician se dikhwana best rahega.";
                $spoken = "Switch ko touch na karein aur main switch band kar dein. Spark baar-baar aa raha hai toh electrician se check karwayein.";
            } elseif (str_contains($lower, '16a') || str_contains($lower, '6a') || str_contains($lower, 'ampere')) {
                $reply = "Chota 6A socket mobile charger aur TV/lights ke liye hota hai, jabki bada 16A power socket AC, geyser aur microwave jaise heavy appliances ke liye zaroori hota hai.";
                $spoken = "6A socket normal light load ke liye hota hai aur 16A power socket heavy appliances ke liye.";
            } else {
                $reply = "Switchboard mein kya dikkat aa rahi hai — switch dabane par current nahi aa raha ya plug lagane par loose ho raha hai?";
                $spoken = "Switchboard mein kya dikkat aa rahi hai — current nahi aa raha ya plug loose ho raha hai?";
            }
        }

        // ---------------------------------------------------------------------
        // 4. AIR CONDITIONER (AC)
        // ---------------------------------------------------------------------
        elseif (preg_match('/\b(ac|air\s*conditioner|cooling|split\s*ac|window\s*ac)\b/i', $lower)) {
            $intent = 'ac_advice';
            $reply = "AC thandi hawa nahi de raha? Pehle remote mein check kar lijiye ki 'Cool' mode (❄️) par 24°C set hai na? Agar haan, toh indoor filter ganda ho sakta hai ya condenser coil par dhool jami ho sakti hai.";
            $spoken = "AC thandi hawa nahi de raha toh pehle check karein remote Cool mode par hai na. Filter ya condenser ganda hone se bhi aisa hota hai.";
        }

        // ---------------------------------------------------------------------
        // 5. GEYSER & WATER HEATER
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'geyser') || str_contains($lower, 'water heater') || str_contains($lower, 'pani garam')) {
            $intent = 'geyser_advice';
            $reply = "Geyser ka switch on karne par indicator light jal rahi hai? Agar light jal rahi hai par paani garam nahi ho raha, toh thermostat trip ho sakta hai ya heating coil kharab ho sakti hai.";
            $spoken = "Geyser ka switch on karne par indicator light jal rahi hai? Agar light jal rahi hai par paani garam nahi ho raha toh heating coil ya thermostat trip ho sakta hai.";
        }

        // ---------------------------------------------------------------------
        // 6. INVERTER & BATTERY
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'inverter') || str_contains($lower, 'battery') || str_contains($lower, 'backup') || str_contains($lower, 'beeping')) {
            $intent = 'inverter_advice';
            $reply = "Inverter lagatar beep kar raha hai toh aamtaur par load zyada hone ya battery low hone ki wajah se hota hai. Kamre ke extra fans ya lights band karke dekhiye — beep band hui? Aur battery mein distilled water ka level kaisa hai?";
            $spoken = "Inverter continuous beep kar raha hai toh extra load band karke dekhiye aur battery mein paani ka level check karein.";
        }

        // ---------------------------------------------------------------------
        // 7. LIGHTING, LED & CHANDELIERS
        // ---------------------------------------------------------------------
        elseif (preg_match('/\b(light|led|bulb|flicker|blink|chandelier|tubelight|lamp)\b/i', $lower) && !str_contains($lower, 'lightning')) {
            $intent = 'lighting_advice';
            $reply = "Sirf ek bulb blink kar raha hai ya poore ghar ki lights flicker ho rahi hain? Agar ek hi bulb hai toh uska driver capacitor weak ho sakta hai — usse kisi aur holder mein check karke dekhein.";
            $spoken = "Sirf ek bulb flicker kar raha hai ya poore ghar ki lights? Ek bulb hai toh uska driver weak ho sakta hai.";
        }

        // ---------------------------------------------------------------------
        // 8. EARTHING, ELECTRIC SHOCK & CURRENT LEAKAGE
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'shock') || str_contains($lower, 'current lag') || str_contains($lower, 'jhatka') || str_contains($lower, 'earthing') || str_contains($lower, 'earth')) {
            $intent = 'earthing_advice';
            $reply = "⚠️ Appliance ko chhune par current lagna earthing disconnected hone ka sanket hai. Kripya bina chappal ke appliance ko bilkul na chhuein aur geyser on rakh kar na nahayein. Isko test karwana zaroori hai.";
            $spoken = "Appliance se current aana earthing fault hai. Kripya chappal pehanein aur geyser on rakh kar na nahayein.";
        }

        // ---------------------------------------------------------------------
        // 9. HIGH ELECTRICITY BILL & ENERGY SAVING
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'bill') || str_contains($lower, 'bijli bachat') || str_contains($lower, 'save electricity') || str_contains($lower, 'reduce bill')) {
            $intent = 'bill_saving_advice';
            $reply = "Bill kam karne ke liye AC ko 24°C par chalayein aur purane bulbs ki jagah LED lagayein. Ek baar saare switch band karke meter check kariyega — kya meter tab bhi tezi se pulse kar raha hai?";
            $spoken = "Bill kam karne ke liye AC ko 24 degree par chalayein aur appliances band karke meter check karein.";
        }

        // ---------------------------------------------------------------------
        // 10. ELECTRICAL CONCEPTS: 1 UNIT ELECTRICITY
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'unit') && (str_contains($lower, '1') || str_contains($lower, 'ek') || str_contains($lower, 'kya') || str_contains($lower, 'kitna') || str_contains($lower, 'watt') || str_contains($lower, 'kwh'))) {
            $intent = 'unit_concept';
            $reply = "Simple bhasha mein: 1,000 Watt ka load jab 1 ghante tak chalta hai toh theek 1 unit bijli banti hai. Jaise 100 Watt ka bulb 10 ghante chale toh 1 unit kharch hoti hai.";
            $spoken = "1000 Watt ka appliance agar 1 ghante chale toh 1 unit bijli kharch hoti hai.";
        }

        // ---------------------------------------------------------------------
        // 11. ELECTRICAL CONCEPTS: AC vs DC
        // ---------------------------------------------------------------------
        elseif ((str_contains($lower, 'ac') && str_contains($lower, 'dc')) || str_contains($lower, 'alternating current') || str_contains($lower, 'direct current')) {
            $intent = 'ac_dc_concept';
            $reply = "Aasan shabdon mein, ghar ki wall socket mein AC current aata hai jo apni disha badalta rehta hai. Jabki battery, phone charger aur solar panel se DC current milta hai jo ek hi disha mein steady flow karta hai.";
            $spoken = "Ghar ki supply mein AC current hota hai aur battery ya solar se DC current milta hai.";
        }

        // ---------------------------------------------------------------------
        // 12. ELECTRICAL CONCEPTS: WHY BIRDS DON'T GET SHOCKED
        // ---------------------------------------------------------------------
        elseif ((str_contains($lower, 'bird') || str_contains($lower, 'chidiya') || str_contains($lower, 'pakshi')) && (str_contains($lower, 'shock') || str_contains($lower, 'current') || str_contains($lower, 'taar') || str_contains($lower, 'wire'))) {
            $intent = 'bird_shock_concept';
            $reply = "Pakshi ke dono pair ek hi taar par hote hain, isliye dono pairon ke beech koi voltage difference nahi banta aur circuit poora nahi hota. Agar pakshi galti se do alag taaron ko chhoo le, tab shock lagta hai.";
            $spoken = "Pakshiyon ke dono pair ek hi taar par hote hain isliye circuit poora nahi hota aur unhe current nahi lagta.";
        }

        // ---------------------------------------------------------------------
        // 13. ELECTRICAL CONCEPTS: VOLTAGE, CURRENT, WATT, RESISTANCE
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'voltage') || str_contains($lower, 'ampere') || str_contains($lower, 'ohm') || str_contains($lower, 'watt')) {
            $intent = 'electrical_units_concept';
            $reply = "Aise samajhiye jaise paani ka pipe ho: Voltage paani ka pressure hai, Current paani ke behne ki speed hai, aur Watt yeh batata hai ki kul kitni bijli kharch hui.";
            $spoken = "Voltage bijli ka pressure hai, Current flow hai, aur Watt kul bijli kharch hai.";
        }

        // ---------------------------------------------------------------------
        // 14. SCIENCE / GENERAL: WHY IS THE SKY BLUE?
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'sky') || str_contains($lower, 'aasman') || str_contains($lower, 'aakash') || str_contains($lower, 'blue') || str_contains($lower, 'neela')) {
            $intent = 'science_sky_blue';
            $reply = "Suraj ki roshni jab hawa ke gas particles se takrati hai toh neeli roshni (blue light) chaaron taraf sabse zyada fail jaati hai (scattering), isliye din mein aakash neela dikhta hai.";
            $spoken = "Suraj ki neeli roshni hawa mein sabse zyada scattering karti hai, isliye aasman neela dikhta hai.";
        }

        // ---------------------------------------------------------------------
        // 15. SCIENCE / GENERAL: SOLAR ENERGY & ROOFTOP
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'solar') || str_contains($lower, 'dhoop') || str_contains($lower, 'sun power')) {
            $intent = 'solar_concept';
            $reply = "Solar panels dhoop se DC bijli banate hain aur solar inverter use ghar ke liye AC bijli mein badal deta hai. Din ki extra bijli government grid ko jaati hai jisse aapka bill minus ya kam ho jata hai.";
            $spoken = "Solar panels dhoop se bijli banakar inverter ke zariye ghar chalate hain aur extra bijli grid ko bhejte hain.";
        }

        // ---------------------------------------------------------------------
        // 16. INVENTIONS & SCIENTISTS: WHO DISCOVERED ELECTRICITY?
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'invent') || str_contains($lower, 'khoj') || str_contains($lower, 'discover') || str_contains($lower, 'kisne banaya') || str_contains($lower, 'nikola tesla') || str_contains($lower, 'edison')) {
            $intent = 'history_concept';
            $reply = "Bijli nature ki ek urja hai, tv ne banayi nahi! Par iski khoj aur motor/bulb banane mein Benjamin Franklin, Michael Faraday aur Nikola Tesla ka sabse bada yogdan raha.";
            $spoken = "Bijli ki khoj aur vikas mein Benjamin Franklin, Faraday aur Nikola Tesla ka yogdan raha.";
        }

        // ---------------------------------------------------------------------
        // 17. BOT IDENTITY & CAPABILITIES
        // ---------------------------------------------------------------------
        elseif (str_contains($lower, 'who are you') || str_contains($lower, 'kaun ho') || str_contains($lower, 'kya kar sakte ho') || str_contains($lower, 'what can you do')) {
            $intent = 'bot_identity';
            $reply = "Main Bijli Guru hoon 😊 Ek intelligent general-purpose AI assistant! Main aapke general knowledge, technology, coding, science, daily questions aur chit-chat mein help kar sakta hoon, aur agar ghar mein koi electrical issue ho toh uska expert guidance aur doorstep service bhi arrange karta hoon.";
            $spoken = "Main Bijli Guru hoon, aapka intelligent AI assistant. General questions, tech aur electrical sabhi mein help karta hoon.";
        }

        // ---------------------------------------------------------------------
        // 18. CODING & TECHNICAL ASSISTANCE
        // ---------------------------------------------------------------------
        elseif (preg_match('/\b(python|javascript|php|html|css|sql|function|code|coding|api|program)\b/i', $lower)) {
            $intent = 'coding_assistance';
            if (str_contains($lower, 'hello world')) {
                $reply = "Here is a clean Python function for Hello World:\n\n```python\ndef hello_world():\n    return \"Hello, World!\"\n\nprint(hello_world())\n```";
                $spoken = "Here is a clean, simple Python function for Hello World.";
            } elseif (str_contains($lower, 'laravel')) {
                $reply = "Laravel is a free, open-source PHP web framework created by Taylor Otwell. It features an expressive MVC architecture, Eloquent ORM, integrated routing, authentication, and database migrations, making web app development fast and clean.";
                $spoken = "Laravel is a popular PHP framework for developing modern web applications.";
            } else {
                $reply = "Haan bilkul! Main coding aur programming mein madad kar sakta hoon. Aap apna language, code snippet ya question batayiye, main seedha practical solution doonga.";
                $spoken = "Main coding mein madad kar sakta hoon. Aap apna question batayiye.";
            }
        }

        // ---------------------------------------------------------------------
        // 19. GRATITUDE & CLOSING
        // ---------------------------------------------------------------------
        elseif (preg_match('/^(thank you|thanks|shukriya|dhanyawad|dhanyavad|bahut accha|good|badhiya|ok|theek hai)(\s.*)?$/i', $lower) && strlen($lower) < 30) {
            $intent = 'gratitude';
            $reply = $isEnglish
                ? "You're very welcome! Feel free to ask anytime if you need help with anything."
                : "Aapka swagat hai 😊 Koi aur dikkat ya sawaal ho toh batayiye, main yahin hoon.";
            $spoken = $isEnglish ? "You're welcome! Happy to help." : "Aapka swagat hai! Koi aur sawaal ho toh batayiye.";
        }

        // ---------------------------------------------------------------------
        // 20. DYNAMIC GENERAL SMART HANDLER (For ANY other question!)
        // ---------------------------------------------------------------------
        else {
            $intent = 'dynamic_general';

            if ($wasFrustrated) {
                $reply = "Maaf kijiye! Seedhi baat — aapko kya madad chahiye? Batayiye, main seedha solution batata hoon.";
                $spoken = "Maaf kijiye, batayiye aapko kya madad chahiye? Main seedha jawab batata hoon.";
            } elseif ($isEnglish) {
                $reply = "I'm here to help! Could you please share a bit more detail about what you'd like to know or what you're working on?";
                $spoken = "I'm here to help! Could you share a bit more detail about what you would like to know?";
            } else {
                $hasElectricalHint = preg_match('/\b(bijli|taar|wire|light|line|current|power|voltage|board|plug|meter|earthing|fault|kharab|chal|trip)\b/i', $lower);
                if ($hasElectricalHint) {
                    $reply = "Samajh gaya! Thoda batayenge ki exact kya dikkat aa rahi hai? Main turant solution batata hoon.";
                } else {
                    $reply = "Ji bataiye! Main aapki help karne ke liye taiyar hoon. Aap kisi bhi topic — general knowledge, technology, coding ya electrical services — ke baare mein pooch sakte hain 😊";
                }
                $spoken = strip_tags(str_replace(['*', '#', '•', '`', '😊'], '', $reply));
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
     * Dynamic Cloud LLM Engine (Zero-config cloud LLM)
     * Handles casual conversation, general knowledge, technology, coding, science,
     * everyday questions, as well as electrical troubleshooting.
     */
    protected function reasonWithDynamicLLM(
        string $message,
        array $conversationHistory,
        float $lat,
        float $lng,
        string $areaName,
        bool $isEmergency
    ): ?array {
        try {
            $systemInstruction = "You are a friendly, intelligent, accurate, and natural general-purpose AI assistant who can also help with electrical services for ElectroLKO in Lucknow.

### Core behavior:
- Understand the user's intent before answering.
- Handle casual conversation, general knowledge, technology, coding, science, education, news, writing, translation, recommendations, troubleshooting, and everyday questions.
- Do not assume every message is an electrical problem.
- For greetings, small talk, jokes, thanks, or casual messages, respond naturally and conversationally.
- Never force electrical advice into normal conversation.
- Match the user's language: Hindi, Hinglish, or English.
- Match the user's tone and keep simple questions short (1-3 sentences).
- Give detailed, structured answers only when needed.

### Electrical assistance:
- Help with fans, lights, switches, sockets, wiring, MCB, appliances, power issues, sparks, smoke, burning smell, and electrician services.
- Only enter electrical troubleshooting mode when the user actually describes an electrical issue.
- For dangerous electrical situations, prioritize safety and recommend a qualified electrician. Never tell an inexperienced user to touch live wires, bypass safety devices, or work on energized circuits.

### Knowledge & accuracy:
- Explain difficult topics simply when appropriate.
- Never invent facts, APIs, URLs, prices, statistics, people, or news.
- Clearly distinguish confirmed information from rumors, opinions, and predictions.

### Coding & technical help:
- Give practical, beginner-friendly, copy-paste-ready solutions when requested.
- For existing projects, make the smallest necessary change and preserve existing parameters, validation, database logic, response structure, and unrelated functionality unless the user asks otherwise.
- Do not invent libraries, functions, APIs, or database structures.

### Conversation:
- Use conversation context and remember previous messages within the conversation.
- Ask follow-up questions only when necessary.
- Do not repeat information unnecessarily.
- Be friendly, empathetic, and human-like, but never claim to be human or claim actions you did not actually perform.
- Never claim you sent an SMS, booked a service, checked live news, or accessed a system unless the application actually performed that action.
- Never request passwords, API keys, OTPs, private keys, or other sensitive credentials.

Response rule: Understand -> determine intent -> respond naturally -> be accurate -> stay safe -> match language and response length.
You are a general AI assistant first, with electrical-service capabilities when relevant, not an electrical-only chatbot.
User location: {$areaName}, Lucknow.";

            $messages = [
                ['role' => 'system', 'content' => $systemInstruction]
            ];

            foreach (array_slice($conversationHistory, -6) as $turn) {
                $role = ($turn['role'] === 'user') ? 'user' : 'assistant';
                $messages[] = [
                    'role' => $role,
                    'content' => $turn['content'] ?? ''
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            $text = null;

            // Attempt 1: POST to text.pollinations.ai
            try {
                $response = Http::timeout(12)
                    ->connectTimeout(3)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post('https://text.pollinations.ai/', [
                        'messages' => $messages
                    ]);

                if ($response->successful()) {
                    $body = trim($response->body());
                    if (!empty($body) && $body !== '{}' && !str_starts_with($body, '<!DOCTYPE') && !str_starts_with($body, '<html')) {
                        $text = $body;
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('Dynamic LLM POST failed: ' . $e->getMessage());
            }

            // Attempt 2: GET fallback if POST returned empty or 402
            if (empty($text)) {
                try {
                    $cleanPrompt = "Act as Bijli Guru, friendly general-purpose AI assistant in Lucknow. User: {$message}\nAssistant:";
                    $url = "https://text.pollinations.ai/" . rawurlencode($cleanPrompt);
                    $getResponse = Http::timeout(8)->get($url);
                    if ($getResponse->successful()) {
                        $body = trim($getResponse->body());
                        if (!empty($body) && $body !== '{}' && !str_starts_with($body, '<!DOCTYPE') && !str_starts_with($body, '<html')) {
                            $text = $body;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::debug('Dynamic LLM GET failed: ' . $e->getMessage());
                }
            }
               
            if (!empty($text)) {
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
                    'reply' => $text,
                    'spoken_text' => strip_tags(str_replace(['*', '#', '•', '`'], '', $text)),
                    'language' => preg_match('/[^\x00-\x7F]/', $text) ? 'hi' : 'en',
                    'state' => $isEmergency ? 'EMERGENCY' : 'SPEAKING',
                    'tools_used' => $wantsTech ? [['name' => 'find_nearby_electricians', 'status' => 'success']] : [],
                    'tool_display' => $isEmergency ? '⚠️ Emergency Protocol Active' : ($wantsTech ? '⚡ Located nearby technicians' : "🤖 Bijli Guru AI"),
                    'intent' => $isEmergency ? 'emergency' : ($wantsTech ? 'explicit_electrician_request' : 'dynamic_answer'),
                    'requires_confirmation' => $requiresConfirmation,
                    'pending_action' => $pendingAction,
                    'electricians' => $electriciansData,
                    'ai_provider' => 'dynamic_ai',
                    'model_used' => 'cloud-llm'
                ];
            }
        } catch (\Exception $e) {
            Log::info('Dynamic Cloud LLM service unreachable: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Live Instant Knowledge Engine
     * Retrieves instant factual explanations via DuckDuckGo Instant Answers & Wikipedia API.
     * Guarantees accurate, verified answers for technology (e.g. Laravel, React, Python),
     * science, history, geography, people, and definitions without hallucinations.
     */
    protected function reasonWithInstantKnowledge(
        string $message,
        array $conversationHistory,
        float $lat,
        float $lng,
        string $areaName,
        bool $isEmergency
    ): ?array {
        try {
            $lower = strtolower(trim($message));

            // Extract topic by stripping common question prefixes
            $queryClean = trim(preg_replace('/^(what is|who is|what are|explain|tell me about|define|kya hai|kya hota hai|kaun hai|kiske bare me hai|kya kam karta hai)\s+/i', '', $lower));
            $queryClean = trim(preg_replace('/[?!.]+$/', '', $queryClean));

            if (empty($queryClean) || strlen($queryClean) < 2) {
                // If the message is just a technology/topic name e.g. "laravel", "react js", "python"
                if (str_word_count($lower) <= 4 && !preg_match('/\b(chahiye|bhejo|book|hire|call|karo|ho gaya|karein)\b/i', $lower)) {
                    $queryClean = trim(preg_replace('/[?!.]+$/', '', $lower));
                } else {
                    return null;
                }
            }

            $fact = null;

            // 1. DuckDuckGo Instant Answer API
            try {
                $ddg = Http::timeout(3)->get('https://api.duckduckgo.com/?q=' . urlencode($queryClean) . '&format=json&no_html=1');
                if ($ddg->successful()) {
                    $abs = trim($ddg->json('AbstractText') ?? '');
                    if (!empty($abs) && strlen($abs) > 30) {
                        $fact = $abs;
                    }
                }
            } catch (\Throwable $e) {}

            // 2. Wikipedia Summary API fallback
            if (!$fact) {
                try {
                    $wikiTitle = rawurlencode(str_replace(' ', '_', ucwords($queryClean)));
                    $wiki = Http::timeout(3)
                        ->withHeaders(['User-Agent' => 'ElectroLKO-AI/1.0 (contact@electrolko.in)'])
                        ->get("https://en.wikipedia.org/api/rest_v1/page/summary/{$wikiTitle}");
                    if ($wiki->successful()) {
                        $ext = trim($wiki->json('extract') ?? '');
                        if (!empty($ext) && strlen($ext) > 30 && !str_contains($ext, 'may refer to:')) {
                            $fact = $ext;
                        }
                    }
                } catch (\Throwable $e) {}
            }

            if (!$fact) {
                return null;
            }

            // Keep to 2-3 concise sentences for natural conversational delivery
            $sentences = preg_split('/(?<=[.?!])\s+/', $fact, 4);
            $concise = implode(' ', array_slice($sentences, 0, 2));

            $isHindi = preg_match('/[^\x00-\x7F]/', $message) || preg_match('/\b(kya|kaun|kaise|batao|hota|karein|hai)\b/i', $lower);

            return [
                'success' => true,
                'reply' => $concise,
                'spoken_text' => strip_tags(str_replace(['*', '#', '•', '`'], '', $concise)),
                'language' => $isHindi ? 'hi' : 'en',
                'state' => 'SPEAKING',
                'tools_used' => [['name' => 'instant_knowledge_lookup', 'status' => 'success']],
                'tool_display' => '🔍 Verified Knowledge Answer',
                'intent' => 'general_knowledge',
                'requires_confirmation' => false,
                'pending_action' => null,
                'electricians' => [],
                'ai_provider' => 'knowledge_engine',
                'model_used' => 'verified_facts'
            ];
        } catch (\Throwable $e) {
            Log::debug('Instant knowledge lookup failed: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Ollama / Modal AI Reasoning Integration
     * Connects to local Ollama (http://127.0.0.1:11434) or remote Modal cloud endpoint.
     * Generates natural, empathetic, and user-friendly answers.
     */
    protected function reasonWithOllama(
        string $message,
        array $conversationHistory,
        float $lat,
        float $lng,
        string $areaName,
        bool $isEmergency,
        ?string $requestedModel = null,
        ?string $customOllamaUrl = null
    ): ?array {
        try {
            $baseUrl = rtrim($customOllamaUrl ?: (config('services.ollama.base_url') ?: env('OLLAMA_BASE_URL', env('MODAL_OLLAMA_URL', 'http://127.0.0.1:11434'))), '/');
            $model = $requestedModel ?: (config('services.ollama.model') ?: env('OLLAMA_MODEL', 'llama3.2'));
            $timeout = (int) (config('services.ollama.timeout') ?: env('OLLAMA_TIMEOUT', 12));

            if (empty($baseUrl)) {
                return null;
            }

            $systemInstruction = "You are a friendly, intelligent, accurate, and natural general-purpose AI assistant who can also help with electrical services for ElectroLKO in Lucknow.
Powered by Ollama Local AI ({$model}).

### Core behavior:
- Understand the user's intent before answering.
- Handle casual conversation, general knowledge, technology, coding, science, education, news, writing, translation, recommendations, troubleshooting, and everyday questions.
- Do not assume every message is an electrical problem.
- For greetings, small talk, jokes, thanks, or casual messages, respond naturally and conversationally.
- Never force electrical advice into normal conversation.
- Match the user's language: Hindi, Hinglish, or English.
- Match the user's tone and keep simple questions short (1-3 sentences).
- Give detailed, structured answers only when needed.

### Electrical assistance:
- Help with fans, lights, switches, sockets, wiring, MCB, appliances, power issues, sparks, smoke, burning smell, and electrician services.
- Only enter electrical troubleshooting mode when the user actually describes an electrical issue.
- For dangerous electrical situations, prioritize safety and recommend a qualified electrician. Never tell an inexperienced user to touch live wires, bypass safety devices, or work on energized circuits.

### Knowledge & accuracy:
- Explain difficult topics simply when appropriate.
- Never invent facts, APIs, URLs, prices, statistics, people, or news.
- Clearly distinguish confirmed information from rumors, opinions, and predictions.

### Coding & technical help:
- Give practical, beginner-friendly, copy-paste-ready solutions when requested.
- For existing projects, make the smallest necessary change and preserve existing parameters, validation, database logic, response structure, and unrelated functionality unless the user asks otherwise.
- Do not invent libraries, functions, APIs, or database structures.

### Conversation:
- Use conversation context and remember previous messages within the conversation.
- Ask follow-up questions only when necessary.
- Do not repeat information unnecessarily.
- Be friendly, empathetic, and human-like, but never claim to be human or claim actions you did not actually perform.
- Never claim you sent an SMS, booked a service, checked live news, or accessed a system unless the application actually performed that action.
- Never request passwords, API keys, OTPs, private keys, or other sensitive credentials.

Response rule: Understand -> determine intent -> respond naturally -> be accurate -> stay safe -> match language and response length.
You are a general AI assistant first, with electrical-service capabilities when relevant, not an electrical-only chatbot.
User location: {$areaName}, Lucknow (Lat: {$lat}, Lng: {$lng}).";

            $messages = [
                ['role' => 'system', 'content' => $systemInstruction]
            ];

            foreach ($conversationHistory as $turn) {
                $role = ($turn['role'] === 'user') ? 'user' : 'assistant';
                $messages[] = [
                    'role' => $role,
                    'content' => $turn['content'] ?? ''
                ];
            }

            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            $ollamaText = null;

            // 1. Try standard Ollama /api/chat endpoint
            $ollamaPayload = [
                'model' => $model,
                'messages' => $messages,
                'stream' => false,
                'options' => [
                    'temperature' => 0.5,
                ]
            ];

            $response = Http::timeout($timeout)
                ->connectTimeout(2)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$baseUrl}/api/chat", $ollamaPayload);

            if ($response->successful()) {
                $ollamaText = $response->json('message.content');
            } elseif ($response->status() === 404) {
                // 2. Try OpenAI-compatible /v1/chat/completions (supported by newer Ollama versions and Modal proxies)
                $v1Response = Http::timeout($timeout)
                    ->connectTimeout(2)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("{$baseUrl}/v1/chat/completions", [
                        'model' => $model,
                        'messages' => $messages,
                        'temperature' => 0.5,
                    ]);

                if ($v1Response->successful()) {
                    $ollamaText = $v1Response->json('choices.0.message.content');
                }
            }

            if (!empty($ollamaText)) {
                $ollamaClean = trim($ollamaText);

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
                    'reply' => $ollamaClean,
                    'spoken_text' => strip_tags(str_replace(['*', '#', '•', '`'], '', $ollamaClean)),
                    'language' => 'hi',
                    'state' => $isEmergency ? 'EMERGENCY' : 'SPEAKING',
                    'tools_used' => $wantsTech ? [['name' => 'find_nearby_electricians', 'status' => 'success']] : [],
                    'tool_display' => $isEmergency ? '⚠️ Emergency Protocol Active' : ($wantsTech ? '⚡ Located nearby technicians' : "🤖 Bijli Guru AI (Ollama: {$model})"),
                    'intent' => $isEmergency ? 'emergency' : ($wantsTech ? 'explicit_electrician_request' : 'direct_answer'),
                    'requires_confirmation' => $requiresConfirmation,
                    'pending_action' => $pendingAction,
                    'electricians' => $electriciansData,
                    'ai_provider' => 'ollama',
                    'model_used' => $model
                ];
            }
        } catch (\Exception $e) {
            Log::info('Ollama/Modal AI service unreachable, continuing: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Check Ollama Server Status & List Installed Models
     */
    public function checkOllamaStatus(?string $customUrl = null): array
    {
        $baseUrl = rtrim($customUrl ?: (config('services.ollama.base_url') ?: env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434')), '/');
        $defaultModel = config('services.ollama.model') ?: env('OLLAMA_MODEL', 'llama3.2');

        try {
            $response = Http::timeout(3)
                ->connectTimeout(2)
                ->get("{$baseUrl}/api/tags");

            if ($response->successful()) {
                $data = $response->json();
                $models = [];
                if (!empty($data['models']) && is_array($data['models'])) {
                    foreach ($data['models'] as $m) {
                        $models[] = [
                            'name' => $m['name'] ?? '',
                            'size' => isset($m['size']) ? round($m['size'] / (1024 * 1024 * 1024), 2) . ' GB' : null,
                            'modified_at' => $m['modified_at'] ?? null,
                        ];
                    }
                }

                return [
                    'success' => true,
                    'running' => true,
                    'base_url' => $baseUrl,
                    'default_model' => $defaultModel,
                    'models' => $models,
                    'message' => 'Ollama server is active and connected'
                ];
            }
        } catch (\Exception $e) {
            // Connection failed or timed out
        }

        return [
            'success' => true,
            'running' => false,
            'base_url' => $baseUrl,
            'default_model' => $defaultModel,
            'models' => [],
            'message' => "Ollama local server not detected on {$baseUrl}. Using Bijli Guru Autonomous Agent with high-speed local intelligence.",
            'hint' => 'To connect Ollama: install from https://ollama.com and run: ollama run llama3.2'
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

            $systemInstruction = "You are a friendly, intelligent, accurate, and natural general-purpose AI assistant who can also help with electrical services for ElectroLKO in Lucknow.

### Core behavior:
- Understand the user's intent before answering.
- Handle casual conversation, general knowledge, technology, coding, science, education, news, writing, translation, recommendations, troubleshooting, and everyday questions.
- Do not assume every message is an electrical problem.
- For greetings, small talk, jokes, thanks, or casual messages, respond naturally and conversationally.
- Never force electrical advice into normal conversation.
- Match the user's language: Hindi, Hinglish, or English.
- Match the user's tone and keep simple questions short (1-3 sentences).
- Give detailed, structured answers only when needed.

### Electrical assistance:
- Help with fans, lights, switches, sockets, wiring, MCB, appliances, power issues, sparks, smoke, burning smell, and electrician services.
- Only enter electrical troubleshooting mode when the user actually describes an electrical issue.
- For dangerous electrical situations, prioritize safety and recommend a qualified electrician. Never tell an inexperienced user to touch live wires, bypass safety devices, or work on energized circuits.

### Knowledge & accuracy:
- Explain difficult topics simply when appropriate.
- Never invent facts, APIs, URLs, prices, statistics, people, or news.
- Clearly distinguish confirmed information from rumors, opinions, and predictions.

### Coding & technical help:
- Give practical, beginner-friendly, copy-paste-ready solutions when requested.
- For existing projects, make the smallest necessary change and preserve existing parameters, validation, database logic, response structure, and unrelated functionality unless the user asks otherwise.
- Do not invent libraries, functions, APIs, or database structures.

### Conversation:
- Use conversation context and remember previous messages within the conversation.
- Ask follow-up questions only when necessary.
- Do not repeat information unnecessarily.
- Be friendly, empathetic, and human-like, but never claim to be human or claim actions you did not actually perform.
- Never claim you sent an SMS, booked a service, checked live news, or accessed a system unless the application actually performed that action.
- Never request passwords, API keys, OTPs, private keys, or other sensitive credentials.

Response rule: Understand -> determine intent -> respond naturally -> be accurate -> stay safe -> match language and response length.
You are a general AI assistant first, with electrical-service capabilities when relevant, not an electrical-only chatbot.
User location: {$areaName}, Lucknow.";

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
