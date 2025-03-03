<?php

namespace App\Imports;

use App\Models\contract;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ContractImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Helper function to convert date string to proper format
        $formatDate = function ($date) {
            if (!$date) return null;
            try {
                // Parse date from dd/mm/yyyy format
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    // Fallback: try to parse Excel serial number
                    return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                } catch (\Exception $e) {
                    return null;
                }
            }
        };

        // Convert number to proper format
        $formatNumber = function ($number) {
            return $number ? floatval(str_replace(',', '', $number)) : 0;
        };

        return new Contract([
            'code' => $row['code'] ?? null,
            'name' => $row['name'] ?? null,
            'nama_perusahaan' => $row['nama_perusahaan'] ?? null,
            'nama_pekerjaan' => $row['nama_pekerjaan'] ?? null,
            'status_kontrak' => $row['status_kontrak'] ?? null,
            'jenis_pekerjaan' => $row['jenis_pekerjaan'] ?? null,
            'nominal_kontrak' => $formatNumber($row['nominal_kontrak'] ?? 0),
            'tanggal_kontrak' => $formatDate($row['tanggal_kontrak']),
            'masa_berlaku' => $formatDate($row['masa_berlaku']),
            'status_proyek' => $row['status_proyek'] ?? 'OPEN',
            'retensi' => $row['retensi'] ?? null,
            'masa_retensi' => $row['masa_retensi'] ?? null,
            'status_retensi' => $row['status_retensi'] ?? null,
            'pic_sales' => $row['pic_sales'] ?? null,
            'pic_pc' => $row['pic_pc'] ?? null,
            'pic_customer' => $row['pic_customer'] ?? null,
            'mata_uang' => $row['mata_uang'] ?? 'IDR',
            'bast_1' => $formatDate($row['tanggal_bast_1']),
            'bast_1_nomor' => $row['bast_1_nomor'] ?? null,
            'bast_2' => $formatDate($row['tanggal_bast_2']),
            'bast_2_nomor' => $row['bast_2_nomor'] ?? null,
            'overall_status' => $row['overall_status'] ?? null,
            'kontrak_milik' => $row['kontrak_milik'] ?? null,
            'keterangan' => $row['keterangan'] ?? null,
            'memo' => $row['memo'] ?? null,
        ]);
    }
}
