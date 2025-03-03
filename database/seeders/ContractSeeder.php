<?php

namespace Database\Seeders;

use App\Models\Contract;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Seeder untuk tabel 'contract'
        Contract::create([
            'created_by' => 1,
            'code' => 'CONTRACT001',
            'name' => 'Contract ABC',
            'nama_perusahaan' => 'PT. Example Company',
            'nama_pekerjaan' => 'Construction of Office Building',
            'status_kontrak' => 'Active',
            'jenis_pekerjaan' => 'Construction',
            'nominal_kontrak' => 100000000.00,
            'tanggal_kontrak' => '2025-01-01',
            'masa_berlaku' => '2025-12-31',
            'status_proyek' => 'OPEN',
            'retensi' => '5%',
            'masa_retensi' => '12 Months',
            'status_retensi' => 'Completed',
            'pic_sales' => 'John Doe',
            'pic_pc' => 'Jane Smith',
            'pic_customer' => 'PT. Customer Corp',
            'mata_uang' => 'IDR',
            'bast_1' => '2025-06-01',
            'bast_1_nomor' => 'BAST/2025/001',
            'bast_2' => '2025-12-01',
            'bast_2_nomor' => 'BAST/2025/002',
            'overall_status' => 'In Progress',
            'kontrak_milik' => 'PT. Example Company',
            'keterangan' => 'This is a construction project for office buildings.',
            'memo' => 'Additional note: Project progress is on schedule.',
        ]);

        Contract::create([
            'created_by' => 2,
            'code' => 'CONTRACT002',
            'name' => 'Contract XYZ',
            'nama_perusahaan' => 'PT. XYZ',
            'nama_pekerjaan' => 'Renovation of Warehouse',
            'status_kontrak' => 'Completed',
            'jenis_pekerjaan' => 'Renovation',
            'nominal_kontrak' => 50000000.00,
            'tanggal_kontrak' => '2025-02-01',
            'masa_berlaku' => '2025-08-01',
            'status_proyek' => 'CLOSED',
            'retensi' => '10%',
            'masa_retensi' => '6 Months',
            'status_retensi' => 'Pending',
            'pic_sales' => 'Alice Johnson',
            'pic_pc' => 'Bob Williams',
            'pic_customer' => 'PT. Warehouse Corp',
            'mata_uang' => 'IDR',
            'bast_1' => '2025-04-01',
            'bast_1_nomor' => 'BAST/2025/003',
            'bast_2' => '2025-07-01',
            'bast_2_nomor' => 'BAST/2025/004',
            'overall_status' => 'Completed',
            'kontrak_milik' => 'PT. XYZ',
            'keterangan' => 'This contract covers the renovation of a warehouse.',
            'memo' => 'Project completed ahead of schedule, pending retensi.',
        ]);

        Contract::create([
            'created_by' => 3,
            'code' => 'CONTRACT003',
            'name' => 'Contract DEF',
            'nama_perusahaan' => 'PT. DEF Industries',
            'nama_pekerjaan' => 'Factory Expansion',
            'status_kontrak' => 'Active',
            'jenis_pekerjaan' => 'Construction',
            'nominal_kontrak' => 200000000.00,
            'tanggal_kontrak' => '2025-03-01',
            'masa_berlaku' => '2026-03-01',
            'status_proyek' => 'OPEN',
            'retensi' => '7%',
            'masa_retensi' => '12 Months',
            'status_retensi' => 'Active',
            'pic_sales' => 'Charlie Brown',
            'pic_pc' => 'David Green',
            'pic_customer' => 'PT. DEF Industries',
            'mata_uang' => 'IDR',
            'bast_1' => '2025-06-15',
            'bast_1_nomor' => 'BAST/2025/005',
            'bast_2' => '2025-12-15',
            'bast_2_nomor' => 'BAST/2025/006',
            'overall_status' => 'In Progress',
            'kontrak_milik' => 'PT. DEF Industries',
            'keterangan' => 'Expanding the existing factory building for production increase.',
            'memo' => 'Project is progressing as per schedule with some delays on material procurement.',
        ]);

        Contract::create([
            'created_by' => 4,
            'code' => 'CONTRACT004',
            'name' => 'Contract GHI',
            'nama_perusahaan' => 'PT. GHI',
            'nama_pekerjaan' => 'Office Renovation',
            'status_kontrak' => 'In Progress',
            'jenis_pekerjaan' => 'Renovation',
            'nominal_kontrak' => 75000000.00,
            'tanggal_kontrak' => '2025-04-01',
            'masa_berlaku' => '2025-09-01',
            'status_proyek' => 'OPEN',
            'retensi' => '5%',
            'masa_retensi' => '6 Months',
            'status_retensi' => 'Pending',
            'pic_sales' => 'Emma White',
            'pic_pc' => 'Frank Black',
            'pic_customer' => 'PT. GHI Corp',
            'mata_uang' => 'IDR',
            'bast_1' => '2025-06-01',
            'bast_1_nomor' => 'BAST/2025/007',
            'bast_2' => '2025-08-01',
            'bast_2_nomor' => 'BAST/2025/008',
            'overall_status' => 'In Progress',
            'kontrak_milik' => 'PT. GHI',
            'keterangan' => 'Office renovation project in the central area.',
            'memo' => 'Progress slowed due to supply chain issues.',
        ]);

        Contract::create([
            'created_by' => 5,
            'code' => 'CONTRACT005',
            'name' => 'Contract JKL',
            'nama_perusahaan' => 'PT. JKL',
            'nama_pekerjaan' => 'Retail Store Renovation',
            'status_kontrak' => 'Completed',
            'jenis_pekerjaan' => 'Renovation',
            'nominal_kontrak' => 25000000.00,
            'tanggal_kontrak' => '2025-05-01',
            'masa_berlaku' => '2025-11-01',
            'status_proyek' => 'CLOSED',
            'retensi' => '10%',
            'masa_retensi' => '3 Months',
            'status_retensi' => 'Completed',
            'pic_sales' => 'George Grey',
            'pic_pc' => 'Helen Blue',
            'pic_customer' => 'PT. JKL Retail',
            'mata_uang' => 'IDR',
            'bast_1' => '2025-07-01',
            'bast_1_nomor' => 'BAST/2025/009',
            'bast_2' => '2025-10-01',
            'bast_2_nomor' => 'BAST/2025/010',
            'overall_status' => 'Completed',
            'kontrak_milik' => 'PT. JKL',
            'keterangan' => 'Renovation of retail stores across major cities.',
            'memo' => 'Project completed successfully with all deliverables met.',
        ]);
    }
}
