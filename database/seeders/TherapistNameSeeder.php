<?php

namespace Database\Seeders;

use App\Models\TherapistName;
use Illuminate\Database\Seeder;

class TherapistNameSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Morin',
            'Naomi',
            'Angelin',
            'Indri',
            'Shelin',
            'Dita',
            'Chesa',
            'Ivhy',
            'Bella',
            'Mia',
            'Sasa',
            'Dara',
            'Jesika',
            'Alya',
            'Della',
            'Karin',
            'Kiki',
            'Tiara',
            'Fany',
            'Queen',
            'Ocha',
            'Dhea',
            'Jenny',
        ];

        foreach ($names as $name) {
            TherapistName::firstOrCreate(['name' => $name], ['active' => true]);
        }
    }
}
