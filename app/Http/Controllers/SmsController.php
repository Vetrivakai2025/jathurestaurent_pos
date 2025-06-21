<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    public function index()
    {
        return view('sms.sms');
    }

    public function getClients()
    {
        return Client::whereNotNull('phone')
            ->select('id', 'username', 'phone')
            ->get()
            ->map(function($client) {
                return [
                    'id' => $client->id,
                    'label' => $client->username . ' (' . $client->phone . ')',
                    'phone' => $client->phone
                ];
            });
    }

  public function send(Request $request)
{
    $validated = $request->validate([
        'message' => 'required|string|max:160',
        'phone' => 'required_without:client_id|nullable|digits:10',
        'client_id' => 'required_without:phone|nullable'
    ]);

    try {
        $recipients = [];

        if (isset($validated['phone'])) {
            // Format single phone
            $recipients[] = '94' . ltrim($validated['phone'], '0');
        } elseif ($validated['client_id'] === 'all') {
            // Send to all clients
            $clients = Client::whereNotNull('phone')->get();
            foreach ($clients as $client) {
                $recipients[] = '94' . ltrim($client->phone, '0');
            }
        } else {
            // Send to one client
            $client = Client::find($validated['client_id']);
            if (!$client || !$client->phone) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Client not found or missing phone number.'
                ], 404);
            }
            $recipients[] = '94' . ltrim($client->phone, '0');
        }

        // Convert to comma-separated format
        $to = implode(',', $recipients);

        // Send via Text.lk API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('TEXTLK_API_KEY'),
            'Accept' => 'application/json',
        ])->post('https://app.text.lk/api/v3/sms/send', [
            'sender_id' => env('TEXTLK_SENDER_ID', 'TextIT'),
            'message' => $validated['message'],
            'recipient' => $to,
        ]);

        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'SMS sent successfully',
                'response' => $response->json()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'SMS sending failed',
            'api_error' => $response->json()
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send SMS',
            'error' => $e->getMessage()
        ], 500);
    }
}

}