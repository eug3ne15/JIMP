<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = [
            ['name' => 'University of Nairobi (UoN)', 'location' => 'Nairobi, Kenya', 'status' => 'registered'],
            ['name' => 'Kenyatta University (KU)', 'location' => 'Nairobi, Kenya', 'status' => 'registered'],
            ['name' => 'Strathmore University', 'location' => 'Madaraka, Nairobi', 'status' => 'registered'],
            ['name' => 'Jomo Kenyatta University of Agriculture and Technology (JKUAT)', 'location' => 'Juja, Kiambu', 'status' => 'registered'],
            ['name' => 'Moi University', 'location' => 'Eldoret, Kenya', 'status' => 'registered'],
            ['name' => 'Egerton University', 'location' => 'Njoro, Nakuru', 'status' => 'registered'],
            ['name' => 'United States International University Africa (USIU)', 'location' => 'Roysambu, Nairobi', 'status' => 'registered'],
            ['name' => 'Catholic University of Eastern Africa (CUEA)', 'location' => 'Karen, Nairobi', 'status' => 'registered'],
        ];

        foreach ($universities as $uni) {
            University::firstOrCreate(['name' => $uni['name']], $uni);
        }
    }
}
