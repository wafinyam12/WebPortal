<?php

namespace App\Exports;

use App\Models\VehicleReimbursement;
use Maatwebsite\Excel\Concerns\FromCollection;

class VehicleReimbursementExport implements FromCollection
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = VehicleReimbursement::with(['vehicle.assigned']);

        // Add user name filter if needed
        if (!empty($this->request->user_name)) {
            $query->where('user_by', $this->request->user_name);
        }

        // Add date filter if needed
        if (!empty($this->request->start_date) && !empty($this->request->end_date)) {
            $query->whereBetween('date_recorded', [$this->request->start_date, $this->request->end_date]);
        }

        // Add status filter if needed
        if (!empty($this->request->export_type)) {
            $query->where('status', $this->request->export_type);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Transaksi',
            'Nama Karyawan',
            'Tipe',
            'Bahan Bakar',
            'Jumlah',
            'Harga',
            'Status',
            'Kendaraan'
        ];
    }

    private $counter = 0;

    public function map($reimbursement): array
    {
        $this->counter++;

        // Memastikan hubungan assigned dan employe ada
        $assigned = $reimbursement->vehicle->assigned->last(); // Ambil yang terakhir jika ada lebih dari satu
        $employeeName = ($assigned && $assigned->employe) ? $assigned->employe->full_name : '-'; // Mengambil nama karyawan jika ada, jika tidak '-'

        return [
            $this->counter,
            $reimbursement->date_recorded,
            $employeeName,
            $reimbursement->type,
            $reimbursement->fuel,
            $reimbursement->amount,
            $reimbursement->price,
            $reimbursement->status,
            $reimbursement->vehicle->model ?? '-' // Menggunakan fallback jika tidak ada data kendaraan
        ];
    }
}
