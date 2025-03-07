<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKU extends Model
{
    use HasFactory;

    protected $table = 'sku';

    protected $fillable = [
        'sku',
        'keterangan',
        'item_group_id',
    ];

    public  function itemGroup()
    {
        return $this->belongsTo(ItemGroup::class , 'item_group_id', 'id');
    }
}
