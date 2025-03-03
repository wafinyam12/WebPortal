<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemGroup extends Model
{
    use HasFactory;

    protected $table = 'item_group';

    protected $fillable = [
        'code',
        'keterangan',
        'coa_id',
    ];

    public function coa()
    {
        return $this->belongsTo(COA::class , 'coa_id', 'id');
    }
}
