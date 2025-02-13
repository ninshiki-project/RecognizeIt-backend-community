<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use ninshikiProject\GeneralSettings\Models\GeneralSetting;

class ApiSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        GeneralSetting::truncate();

        GeneralSetting::create([
            'site_name' => config('app.name'),
            'more_configs' => [
                'notifications' => [
                    'mention_user' => true,
                    'invitation' => true,
                    'recognized' => true,
                ],
                'maintenance' => [
                    'maintenance_mode' => false,
                ],
            ],
        ]);
    }
}
