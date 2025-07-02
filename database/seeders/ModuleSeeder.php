<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleInstance;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the disk usage module if it doesn't exist
        $module = Module::firstOrCreate(
            ['type' => 'disk_usage'],
            [
                'name' => 'Disk Usage',
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
            ]
        );

        $sonarr = Setting::where('type', 'sonarr')->first();

        if ($sonarr) {
            ModuleInstance::firstOrCreate(
                [
                    'module_id' => $module->id,
                    'name' => 'Sonarr Disk Usage',
                ],
                [
                    'is_active' => true,
                    'display_order' => 0,
                    'config' => [
                        'source_type' => 'sonarr',
                        'selected_disks' => [],
                    ],
                ]
            );
        }

        $radarr = Setting::where('type', 'radarr')->first();

        if ($radarr) {
            ModuleInstance::firstOrCreate(
                [
                    'module_id' => $module->id,
                    'name' => 'Radarr Disk Usage',
                ],
                [
                    'is_active' => true,
                    'display_order' => 1,
                    'config' => [
                        'source_type' => 'radarr',
                        'selected_disks' => [],
                    ],
                ]
            );
        }
    }
}
