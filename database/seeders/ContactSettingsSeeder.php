<?php

namespace Database\Seeders;

use App\Models\ContactSettings;
use Illuminate\Database\Seeder;

class ContactSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactSettings::query()->firstOrCreate(['id' => 1], ContactSettings::defaults());
    }
}
