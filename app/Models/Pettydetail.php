<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PettyCash;

class Pettydetail extends Model
{
    use HasFactory;
    protected $table = 'petty_details';

    protected $fillable = [
        'petty_id',
        'sku',
        'qty',
        'coa',
        'keterangan',
        'debet',
        'kredit',
    ];

    public function petty()
    {
        return $this->belongsTo(PettyCash::class , 'petty_id', 'id');
    }
}
