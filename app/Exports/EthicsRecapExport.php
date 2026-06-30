<?php

namespace App\Exports;

use App\Models\EthicalClearanceSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EthicsRecapExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles
{
    private $submissions;

    public function __construct()
    {
        $this->submissions = EthicalClearanceSubmission::with([
            'user',
            'studyProgram',
            'reviewers.user',
            'latestDetail.files',
        ])->get();
    }

    public function collection()
    {
        return $this->submissions;
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pengajuan',
            'No. Dokumen EC',
            'Nama Pengusul',
            'Jenis Pengusul',
            'Kategori Etik',
            'NIM',
            'Program Studi',
            'Nama Wali',
            'Jumlah Dokumen',
            'Reviewer',
            'Tahap',
            'Status',
        ];
    }

    public function map($submission): array
    {
        static $no = 0;
        $no++;

        // Jenis Pengusul
        $jenisPengusul = $submission->is_student ? 'Mahasiswa' : 'Umum / Dosen';

        // Kategori Etik
        $kategoriEtik = $submission->category === 'clinical' ? 'Klinik / Uji Coba Hewan' : 'Non-Klinis';

        // Jumlah Dokumen Terunggah
        $fileCount = 0;
        if ($submission->latestDetail) {
            $fileCount = $submission->latestDetail->files->count();
        }

        // Assigned Reviewers
        $reviewers = '-';
        if ($submission->reviewers->count() > 0) {
            $reviewers = $submission->reviewers->map(fn ($r) => $r->user?->name ?? '-')->implode("\n");
        }

        // Tahap
        $stageMap = [
            'proposal' => 'Proposal',
            'output' => 'Output / EC',
            'verification' => 'Verifikasi',
        ];
        $stage = $stageMap[$submission->stage] ?? ucfirst($submission->stage);

        // Status (human-readable)
        $status = str_replace('_', ' ', ucwords(str_replace('_', ' ', $submission->status)));

        return [
            $no,
            $submission->created_at->format('d-m-Y H:i'),
            $submission->formatted_document_number ?: '-',
            $submission->user ? $submission->user->name : '-',
            $jenisPengusul,
            $kategoriEtik,
            $submission->student_nim ?: '-',
            $submission->studyProgram ? $submission->studyProgram->name : '-',
            $submission->wali_name ?: '-',
            $fileCount . ' file',
            $reviewers,
            $stage,
            $status,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // Tanggal
            'C' => 30,  // No. Dokumen
            'D' => 28,  // Nama Pengusul
            'E' => 18,  // Jenis Pengusul
            'F' => 25,  // Kategori Etik
            'G' => 15,  // NIM
            'H' => 30,  // Program Studi
            'I' => 25,  // Nama Wali
            'J' => 15,  // Jumlah Dokumen
            'K' => 25,  // Reviewer
            'L' => 18,  // Tahap
            'M' => 20,  // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();

        return [
            1 => ['font' => ['bold' => true]],
            "A:{$highestColumn}" => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                ],
            ],
        ];
    }
}
