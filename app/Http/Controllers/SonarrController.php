<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SonarrController extends Controller
{
    public function getDiskSpace(): \Illuminate\Http\JsonResponse
    {
        $sonarr = Setting::where('type', 'sonarr')->first();

        if ($sonarr) {
            $client = new Client();
            $response = $client->get(rtrim($sonarr->url, '/') . '/api/v3/diskspace', [
                'headers' => [
                    'X-Api-Key' => $sonarr->api_key,
                ],
                'timeout' => 5
            ]);

            return response()->json(json_decode($response->getBody()->getContents(), true));
        }

        return response()->json('Unable to retrieve disk space');
    }
}
