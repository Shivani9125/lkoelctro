<?php

namespace App\Http\Controllers;

use App\Services\ElectroFixAgentService;
use Illuminate\Http\Request;

class AiAgentController extends Controller
{
    protected ElectroFixAgentService $agentService;

    public function __construct(ElectroFixAgentService $agentService)
    {
        $this->agentService = $agentService;
    }

  
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'conversation_history' => 'nullable|array',
            'user_location' => 'nullable|array',
            'pending_action' => 'nullable|array',
        ]);

        $message = $request->input('message');
        $history = $request->input('conversation_history', []);
        $location = $request->input('user_location', []);
        $pendingAction = $request->input('pending_action', null);

        $result = $this->agentService->processMessage(
            $message,
            $history,
            $location,
            $pendingAction
        );

        // Normalize response payload for web clients
        $primaryMessage = $result['reply'] ?? ($result['message'] ?? 'Processed.');
        $spokenText = $result['spoken_text'] ?? $primaryMessage;
        $state = $result['state'] ?? 'IDLE';
        $toolUsed = $result['tool_used'] ?? (isset($result['tools_used'][0]['name']) ? $result['tools_used'][0]['name'] : null);

        $dataPayload = [
            'electricians' => $result['electricians'] ?? [],
            'booking' => $result['booking'] ?? null,
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
            'data' => $dataPayload,
            'pending_action' => $result['pending_action'] ?? null,
            'requires_confirmation' => $result['requires_confirmation'] ?? false,
            'history' => $updatedHistory
        ]);
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
            $request->input('customer_name', 'Customer'),
            $request->input('customer_phone', '+91 9812340001'),
            $request->input('customer_address', 'Lucknow Address'),
            $request->input('service_type', 'Electrical Repair'),
            $request->input('time_slot', 'Within 30 mins'),
            $request->input('notes', 'Booked via AI Agent')
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
}
