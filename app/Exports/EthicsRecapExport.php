<?php

namespace App\Exports;

use App\Models\EthicalClearanceSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class EthicsRecapExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    private $submissions;

    public function __construct()
    {
        $this->submissions = EthicalClearanceSubmission::with(['latestDetail.members', 'user'])->get();
    }

    public function collection()
    {
        return $this->submissions;
    }

    public function headings(): array
    {
        return [
            'Tanggal Pengajuan',
            'No. Dokumen',
            'Nama Ketua / Pengusul',
            'Anggota',
            'Kategori Pengusul',
            'NIM',
            'Institusi',
            'Judul Penelitian',
            'Lokasi Penelitian',
            'Status',
        ];
    }

    public function map($submission): array
    {
        $detail = $submission->latestDetail;
        if (!$detail) {
            return [];
        }

        $leaderName = $detail->leader_name ?: ($submission->user ? $submission->user->name : '-');
        
        $members = $detail->members->pluck('name')->toArray();
        $membersString = empty($members) ? '-' : implode("\n", $members);

        $kategori = $submission->is_student ? 'Mahasiswa' : 'Umum / Dosen';

        return [
            $submission->created_at->format('d-m-Y H:i'),
            $submission->formatted_document_number ?: '-',
            $leaderName,
            $membersString,
            $kategori,
            $submission->student_nim ?: '-',
            $detail->institution_details ?: '-',
            $detail->research_title ?: '-',
            $detail->research_location ?: '-',
            str_replace('_', ' ', strtoupper($submission->status)),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Tanggal
            'B' => 25, // No. Dokumen
            'C' => 25, // Nama Ketua
            'D' => 25, // Anggota
            'E' => 18, // Kategori
            'F' => 15, // NIM
            'G' => 30, // Institusi
            'H' => 50, // Judul
            'I' => 30, // Lokasi
            'J' => 20, // Status
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
