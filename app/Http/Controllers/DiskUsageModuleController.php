<?php

namespace App\Http\Controllers;

use App\Interfaces\DiskUsage\DiskUsageInterface;
use App\Models\Module;
use App\Models\ModuleInstance;
use App\Models\Setting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiskUsageModuleController extends Controller
{
    /**
     * Get disk space data for all disk usage module instances.
     */
    public function getDiskSpaceData()
    {
        $instances = ModuleInstance::with('module')
            ->whereHas('module', function ($query) {
                $query->where('type', 'disk_usage');
            })
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        $data = [];

        foreach ($instances as $instance) {
            $sourceType = $instance->config['source_type'] ?? null;

            if (!$sourceType) {
                continue;
            }

            $diskSpaceData = $this->fetchDiskSpaceData($sourceType);

            if ($diskSpaceData) {
                if (isset($instance->config['selected_disks']) && is_array($instance->config['selected_disks']) && !empty($instance->config['selected_disks'])) {
                    $diskSpaceData = array_filter($diskSpaceData, function($disk) use ($instance) {
                        return in_array($disk['path'], $instance->config['selected_disks']);
                    });
                }

                $instance->cached_data = $diskSpaceData;
                $instance->last_updated_at = now();
                $instance->save();

                $data[] = [
                    'instance' => $instance,
                    'disk_space' => array_values($diskSpaceData),
                ];
            }
        }

        return response()->json($data);
    }

    /**
     * Fetch disk space data from the specified source.
     *
     * @return DiskUsageInterface[]|null
     */
    private function fetchDiskSpaceData($sourceType): ?array
    {
        $setting = Setting::where('type', $sourceType)->first();

        if (!$setting) {
            return null;
        }

        try {
            $client = new Client();
            $response = $client->get(rtrim($setting->url, '/') . '/api/v3/diskspace', [
                'headers' => [
                    'X-Api-Key' => $setting->api_key,
                ],
                'timeout' => 5
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Create a disk usage module if it doesn't exist.
     */
    public function createDiskUsageModule()
    {
        $module = Module::where('type', 'disk_usage')->first();

        if (!$module) {
            $module = Module::create([
                'name' => 'Disk Usage',
                'type' => 'disk_usage',
                'description' => 'Displays disk usage information from Sonarr or Radarr.',
                'is_active' => true,
                'config_schema' => [
                    'source_type' => [
                        'type' => 'select',
                        'options' => ['sonarr', 'radarr'],
                        'required' => true,
                        'label' => 'Source',
                        'description' => 'The source of the disk usage data.',
                    ],
                    'selected_disks' => [
                        'type' => 'multiselect',
                        'options' => [],
                        'required' => false,
                        'label' => 'Disks to Display',
                        'description' => 'Select which disks to display. If none selected, all disks will be shown.',
                        'dynamic_options' => true,
                    ],
                ],
            ]);
        } else {
            $configSchema = $module->config_schema;
            if (!isset($configSchema['selected_disks'])) {
                $configSchema['selected_disks'] = [
                    'type' => 'multiselect',
                    'options' => [],
                    'required' => false,
                    'label' => 'Disks to Display',
                    'description' => 'Select which disks to display. If none selected, all disks will be shown.',
                    'dynamic_options' => true,
                ];
                $module->config_schema = $configSchema;
                $module->save();
            }
        }

        return response()->json($module);
    }

    /**
     * Get available disk paths for a specific source type.
     */
    public function getAvailableDiskPaths(Request $request)
    {
        $sourceType = $request->input('source_type');

        if (!$sourceType) {
            return response()->json(['error' => 'Source type is required'], 400);
        }

        $diskSpaceData = $this->fetchDiskSpaceData($sourceType);

        if (!$diskSpaceData) {
            return response()->json(['error' => 'Failed to fetch disk space data'], 500);
        }

        $diskPaths = array_map(function($disk) {
            return $disk['path'];
        }, $diskSpaceData);

        return response()->json($diskPaths);
    }
}
