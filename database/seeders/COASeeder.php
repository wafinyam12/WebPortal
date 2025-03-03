<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\COA;

class COASeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        COA::insert([
            [
                'coa' => '601010201',
                'keterangan' => 'Klaim AM/sales untuk BBM Operasional'
            ],
            [
                'coa' => '601010202',
                'keterangan' => 'Klaim AM/sales/armada internal untuk tol dan parkir'
            ],
            [
                'coa' => '601010206',
                'keterangan' => 'FEE/Insentif AM/sales'
            ],
            [
                'coa' => '501010306',
                'keterangan' => 'Listrik kantor/gudang/pabrik'
            ],
            [
                'coa' => '501010311',
                'keterangan' => 'BBM untuk forklift pabrik/truk kirim penjualan/armada internal'
            ],
            [
                'coa' => '601020303',
                'keterangan' => 'Pembayaran Air'
            ],
            [
                'coa' => '601020304',
                'keterangan' => 'Bayar Wifi/indihome'
            ],
            [
                'coa' => '601020302',
                'keterangan' => 'Bayar telpon'
            ],
            [
                'coa' => '601020305',
                'keterangan' => 'Pengiriman dokumen lewat pos'
            ],
            [
                'coa' => '601020502',
                'keterangan' => 'Beli alat tulis kantor/materai'
            ],
            [
                'coa' => '601020503',
                'keterangan' => 'Iuran sekitar/sampah/17 Agustus/sumbangan lain'
            ],
            [
                'coa' => '601020312',
                'keterangan' => 'air minum/alat kebersihan/peralatan rumah tangga/dll'
            ],
            [
                'coa' => '501010305',
                'keterangan' => 'beli alat tukang untuk produksi'
            ],
            [
                'coa' => '601010699',
                'keterangan' => 'perjalanan dinas AM/Sales: hotel/transport/BBM/Tol'
            ],
            [
                'coa' => '601020203',
                'keterangan' => 'perjalanan dinas non AM/Sales: hotel/transport/BBM/Tol'
            ],
            [
                'coa' => '601010302',
                'keterangan' => 'cetak flayer/banner/pasang iklan'
            ],
            [
                'coa' => '501010303',
                'keterangan' => 'Sewa Gudang/kantor/showroom'
            ],
            [
                'coa' => '601010401',
                'keterangan' => 'Sewa armada eksternal'
            ],
            [
                'coa' => '601020905',
                'keterangan' => 'Service kendaraan AM/Sales/Truk'
            ],
            [
                'coa' => '601020907',
                'keterangan' => 'Pembelian alat tukang dan perbaikan gedung/pabrik/kantor'
            ],
            [
                'coa' => '601020504',
                'keterangan' => 'Pengurusan SIM/KIR/Kendaraan AM/Truk'
            ]
        ]);
    }
}
