<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Tabel yang digunakan oleh model ini.
     *
     * @var string
     */
    protected $table = 'contract';

    /**
     * Kolom-kolom yang dapat diisi secara massal.
     *
     * @var array
     */

    protected $fillable = [
        'created_by',
        'code',
        'name',
        'nama_perusahaan',
        'nama_pekerjaan',
        'status_kontrak',
        'jenis_pekerjaan',
        'nominal_kontrak',
        'tanggal_kontrak',
        'masa_berlaku',
        'status_proyek',
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
        'memo'
    ];
}
