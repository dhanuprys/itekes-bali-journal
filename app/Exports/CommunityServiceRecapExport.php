<?php

namespace App\Exports;

use App\Models\CommunityServiceSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CommunityServiceRecapExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    private $submissions;

    public function __construct()
    {
        $this->submissions = CommunityServiceSubmission::with(['latestDetail.members', 'latestDetail.schema', 'latestDetail.target'])->get();
    }

    public function collection()
    {
        return $this->submissions;
    }

    public function headings(): array
    {
        return [
            'Tanggal Pengajuan',
            'Nama Ketua',
            'Anggota',
            'NIDN',
            'Judul PKM',
            'Skema PKM',
            'Dana yang Diajukan',
            'Target Luaran',
            'Status',
        ];
    }

    public function map($submission): array
    {
        $detail = $submission->latestDetail;
        if (!$detail) {
            return [];
        }

        $leaderName = $detail->final_leader_name ?: $detail->leader_name;
        
        $members = $detail->members->pluck('name')->toArray();
        $membersString = empty($members) ? '-' : implode("\n", $members);

        $title = $detail->final_title ?: $detail->title;
        $schema = $detail->schema ? $detail->schema->title : '-';
        $target = $detail->target ? $detail->target->title : '-';

        return [
            $submission->created_at->format('d-m-Y H:i'),
            $leaderName,
            $membersString,
            $detail->leader_nidn ?: '-',
            $title,
            $schema,
            $detail->budget,
            $target,
            str_replace('_', ' ', strtoupper($submission->status)),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Tanggal Pengajuan
            'B' => 25, // Nama Ketua
            'C' => 25, // Anggota
            'D' => 15, // NIDN
            'E' => 60, // Judul
            'F' => 30, // Skema
            'G' => 20, // Dana
            'H' => 30, // Target Luaran
            'I' => 20, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        return [
            1    => ['font' => ['bold' => true]],
            "A:{$highestColumn}" => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                ]
            ],
        ];
    }
}
