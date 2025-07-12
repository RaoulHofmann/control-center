<?php

namespace App\Http\Controllers;

use App\Interfaces\Series\SeriesInterface;
use App\Models\Module;
use App\Models\ModuleInstance;
use App\Models\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SeriesModuleController extends Controller
{
    /**
     * Get series data for all active series_list module instances.
     */
    public function getSeriesData()
    {
        try {
            $instances = ModuleInstance::with('module')
                ->whereHas('module', function ($query) {
                    $query->where('type', 'series_list');
                })
                ->where('is_active', true)
                ->orderBy('display_order')
                ->get();

            $data = [];

            foreach ($instances as $instance) {
                $series = $this->fetchSeriesData($instances);

                if ($series) {
                    $instance->cached_data = $series;
                    $instance->last_updated_at = now();
                    $instance->save();

                    $data[] = [
                        'instance' => $instance,
                        'series' => array_values($series),
                    ];
                }
            }

            return response()->json($data);
        } catch (GuzzleException $e) {
            return response()->json([]);
        }
    }

    /**
     * @return SeriesInterface[] | null
     * @throws GuzzleException
     */
    private function fetchSeriesData($instances): ?array
    {
        $setting = Setting::where('type', 'sonarr')->first();

        if (!$setting) {
            return null;
        }

        try {
            $client = new Client();
            $response = $client->get(rtrim($setting->url, '/') . '/api/v3/series', [
                'headers' => [
                    'X-Api-Key' => $setting->api_key,
                ],
                'timeout' => 5
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return $instances->cached_data;
        }
    }

    /**
     * Create a disk usage module if it doesn't exist.
     */
    public function createSeriesModule()
    {
        $module = Module::where('type', 'series_list')->first();

        if (!$module) {
            $module = Module::create([
                'name' => 'Series List',
                'description' => 'Displays series list information from Sonarr',
                'is_active' => true,
                'config_schema' => null
            ]);
        }

        return response()->json($module);
    }
}
