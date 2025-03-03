<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContractExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Query untuk mengambil data berdasarkan tanggal.
     */
    public function query()
    {
        return Contract::query()
            ->select([
                'code',
                'name',
                'nama_perusahaan',
                'nama_pekerjaan',
                'status_kontrak',
                'jenis_pekerjaan',
                'nominal_kontrak',
                'tanggal_kontrak',
                'masa_berlaku',
                'status_kontrak',
                'retensi',
                'masa_retensi',
                'status_retensi',
                'pic_sales',
                'pic_pc',
                'pic_customer',
                'mata_uang',
                'bast_1',
                'bast_1_nomor',
                'bast_2',
                'bast_2_nomor',
                'overall_status',
                'kontrak_milik',
                'keterangan',
                'memo',
            ])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->whereNull('deleted_at');
    }

    /**
     * Header kolom yang akan diekspor.
     */
    public function headings(): array
    {
        return [
            'No',
            'Kode Kontrak',
            'Nama Kontrak',
            'Nama Perusahaan',
            'Nama Pekerjaan',
            'Status Kontrak',
            'Jenis Pekerjaan',
            'Nominal Kontrak',
            'Tanggal Kontrak',
            'Masa Berlaku',
            'Retensi',
            'Masa Retensi',
            'Status Retensi',
            'PIC Sales',
            'PIC PC',
            'PIC Customer',
            'Mata Uang',
            'Bast 1',
            'Tanggal Bast 1',
            'Bast 2',
            'Tanggal Bast 2',
            'Overall Status',
            'Kontrak Milik',
            'Keterangan',
            'Memo',
        ];
    }

    private $counter = 0;
    /**
     * Mapping data untuk setiap baris.
     */
    public function map($contract): array
    {
        $this->counter++;

        return [
            $this->counter,
            $contract->code,
            $contract->name,
            $contract->nama_perusahaan,
            $contract->nama_pekerjaan,
            $contract->status_kontrak,
            $contract->jenis_pekerjaan,
            number_format($contract->nominal_kontrak, 2),
            $contract->tanggal_kontrak ? \Carbon\Carbon::parse($contract->tanggal_kontrak)->format('Y-m-d') : '',
            $contract->masa_berlaku,
            $contract->retensi,
            $contract->masa_retensi,
            $contract->status_retensi,
            $contract->pic_sales,
            $contract->pic_pc,
            $contract->pic_customer,
            $contract->mata_uang,
            $contract->bast_1,
            $contract->bast_1_nomor,
            $contract->bast_2,
            $contract->bast_2_nomor,
            $contract->overall_status,
            $contract->kontrak_milik,
            $contract->keterangan,
            $contract->memo,
        ];
    }

    /**
     * Gaya untuk header tabel.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // Baris pertama (header)
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4CAF50']],
            ],
        ];
    }
}
