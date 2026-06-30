<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Magister Keperawatan',
            'Sarjana Keperawatan dan Profesi Ners',
            'Sarjana Kebidanan dan Profesi Bidan',
            'D-IV Keperawatan Anestesiologi',
            'Sarjana Farmasi Klinik dan Komunitas',
            'Sarjana Teknologi Pangan',
            'D-IV Akupuntur dan Pengobatan Herbal',
            'Sarjana Sistem Teknologi dan Informasi',
        ];

        foreach ($data as $item) {
            \App\Models\StudyProgram::updateOrCreate([
                'name' => $item,
            ]);
        }
    }
}
