<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCash extends Model
{
    use HasFactory;

    protected $table = 'petty_cash';
    protected $fillable = [
        'id',
        'owner_id',
        'no_nota',
        'tanggal',
        'tanggal_nota',
        'file_name',
        'saldo_id',
        'status_budget_control',
        'status_ap',
        'approved_date',
        'approved_ap_date',
        'approved_by',
        'approved_ap_by',
        'balance',
        'reject_reason'
    ];

    //koneksikan saldo_id ke saldo
    public function saldo()
    {
        return $this->belongsTo(Saldo::class, 'saldo_id', 'id');
    }

    //koneksikan owner_id ke users
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
}
