<?php

namespace Database\Seeders;
use App\Models\PettyCash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class pettycashSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       PettyCash::insert([
           'owner_id' => 4,
           'tanggal' => now(),
           'transaksi'=>'beli balok kayu',
           'keterangan'=>'beli balok kayu',
           'debet'=>100000,
           'kredit'=>null,
           'saldo_id'=>1,
           'file_name'=>null,
           'status'=>"OPEN",
           'approved_date' => null,
           'approved_by' => null,
           'balance'=>100000,
       ]);


    }
}
