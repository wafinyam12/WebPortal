<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\PettyCash;

class PettyExport implements FromQuery, WithHeadings, WithStyles, WithMapping, WithEvents
{
    protected $start;
    protected $end;
    protected $user;

    private $totalDebet = 0;
    private $totalKredit = 0;

    public function __construct($startDate, $endDate, $user)
    {
        $this->start = $startDate;
        $this->end = $endDate;
        $this->user = $user;
    }

    public function query()
    {
        return PettyCash::query()
            ->where('owner_id', $this->user)
            ->whereBetween('tanggal', [$this->start, $this->end]);
    }

    public function headings(): array
    {
        return [
            'No',
            'User',
            'Tanggal',
            'Tanggal Nota',
            'SKU',
            'COA',
            'Keterangan',
            'Debet',
            'Kredit',
            // 'File Bukti',
            'Status Budget Control',
            'Approved Date Budget',
            'Approved By Budget',
            'Status AP',
            'Approved Date AP',
            'Approved By AP',
        ];
    }

    public function map($pettycash): array
    {
        // Menyimpan total debet & kredit
        $this->totalDebet += $pettycash->debet ?? 0;
        $this->totalKredit += $pettycash->kredit ?? 0;

        return [
            $pettycash->id,
            $pettycash->owner->fullName,
            $pettycash->tanggal,
            $pettycash->tanggal_nota,
            $pettycash->sku,
            $pettycash->coa,
            $pettycash->keterangan,
            $pettycash->debet ?? 0,
            $pettycash->kredit ?? 0,
            // $pettycash->file_name,
            $pettycash->status_budget_control,
            $pettycash->approved_date,
            $pettycash->approved_by,
            $pettycash->status_ap,
            $pettycash->approved_ap_date,
            $pettycash->approved_ap_by,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // Header (Baris pertama)
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow(); // Ambil jumlah baris terakhir

                // Tambahkan baris total di bawah data
                $totalRow = $highestRow + 1;
                // Merge cell untuk "TOTAL:"
                $sheet->mergeCells("A{$totalRow}:B{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", "TOTAL:");
                $sheet->setCellValue("H{$totalRow}", $this->totalDebet);
                $sheet->setCellValue("I{$totalRow}", $this->totalKredit);

                // Format total menjadi bold
                $sheet->getStyle("A{$totalRow}:I{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF99']], // Warna kuning
                ]);

                // Loop untuk mewarnai baris yang statusnya "Rejected"
                for ($row = 2; $row <= $highestRow; $row++) { // Mulai dari baris ke-2 (data pertama)
                    $statusBudget = $sheet->getCell("J{$row}")->getValue(); // Kolom Status Budget Control
                    $statusAP = $sheet->getCell("M{$row}")->getValue(); // Kolom Status AP

                    if ($statusBudget === "Rejected" || $statusAP === "Rejected") {
                        $sheet->getStyle("A{$row}:O{$row}")->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'FF0000'], // Warna merah
                            ],
                            'font' => ['color' => ['rgb' => 'FFFFFF']], // Warna teks putih
                        ]);
                    }
                }
            },
        ];
    }
}
