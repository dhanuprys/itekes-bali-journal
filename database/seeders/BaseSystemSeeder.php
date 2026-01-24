<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaseSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $researchTargets = [
            'Jurnal Nasional Terakreditasi',
            'Jurnal Nasional',
            'Jurnal Internasional',
            'Conference Internasional',
            'Laporan yang tersimpan pada Perpustakaan (Skema Penelitian dan PkM Penugasan)',
        ];

        foreach ($researchTargets as $item) {
            \App\Models\ResearchTarget::updateOrCreate(
                ['title' => $item],
                ['description' => $item]
            );
        }

        $researchSchemas = [
            'Penelitian Dosen Pemula',
            'Penelitian Unggulan Program Studi (PUPS)',
            'Penelitian Inovasi (PI)',
            'Penelitian Kerjasama (PK)',
            'Penelitian Mandiri (PM)',
            'Penelitian Penugasan (PP)',
        ];

        foreach ($researchSchemas as $item) {
            \App\Models\ResearchSchema::updateOrCreate(
                ['title' => $item],
                ['description' => $item]
            );
        }

        $communityServiceTargets = [
            'Jurnal Nasional Terakreditasi',
            'Jurnal Nasional',
            'Jurnal Internasional',
            'Conference Internasional',
            'Prosiding ISSN',
            'Laporan yang tersimpan pada Perpustakaan (Skema Penelitian dan PkM Penugasan)',
        ];

        foreach ($communityServiceTargets as $item) {
            \App\Models\CommunityServiceTarget::updateOrCreate(
                ['title' => $item],
                ['description' => $item]
            );
        }

        $communityServiceSchemas = [
            'Program ITEKES Bali Mengabdi (PIM)',
            'PkM Mandiri (PkM-M)',
        ];

        foreach ($communityServiceSchemas as $item) {
            \App\Models\CommunityServiceSchema::updateOrCreate(
                ['title' => $item],
                ['description' => $item]
            );
        }
    }
}
