<?php

namespace Database\Seeders\Patients;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pathology;

class PathologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Pathology::count() > 0) return;

        Pathology::create([
            'name' => 'Patología no especificada',
            'description' => 'No se ha especificado la patología del paciente.',
        ]);
    }
}
