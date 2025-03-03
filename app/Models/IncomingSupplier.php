<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingSupplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incoming_suppliers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    public function items()
    {
        return $this->hasMany(IncomingInventory::class, 'supplier_id', 'id');
    }
}
