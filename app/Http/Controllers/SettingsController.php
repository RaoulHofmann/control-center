<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsCreateRequest;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller
{
    public function test(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:sonarr,radarr',
            'url' => 'required|url',
            'apiKey' => 'required|string'
        ]);

        try {
            $client = new Client();
            $response = $client->get(rtrim($validated['url'], '/') . '/api/v3/system/status', [
                'headers' => [
                    'X-Api-Key' => $validated['apiKey']
                ],
                'timeout' => 5
            ]);

            if ($response->getStatusCode() === 200) {
                return response()->json(['success' => true]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect: ' . $e->getMessage()
            ], 400);
        }

        return response()->json(['success' => false], 400);
    }

    public function save(SettingsCreateRequest $request): \Illuminate\Http\RedirectResponse
    {
        Log::info('Saving setup', ['request' => $request->validated()]);
        $setup = Setting::updateOrCreate(
            [
                'type' => $request->validated('type'),
            ],
            [
                'url' => $request->validated('url'),
                'api_key' => $request->validated('apiKey'),
                'is_active' => $request->validated('is_active', true),
                'last_tested_at' => null
            ]
        );

        Log::info('Setting saved', ['setup' => $setup]);

        return to_route('settings')->with('success', 'Settings saved.');
    }

}
