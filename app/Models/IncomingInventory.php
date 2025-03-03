<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingInventory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incoming_inventory';

    protected $fillable = [
        'shipment_id',
        'item_name',
        'quantity',
    ];

    public function supplier()
    {
        return $this->belongsTo(IncomingSupplier::class, 'supplier_id', 'id');
    }
}
