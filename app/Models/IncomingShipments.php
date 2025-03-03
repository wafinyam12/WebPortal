<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingShipments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incoming_shipments';

    protected $appends = ['statusName'];

    protected $fillable = [
        'code',
        'created_by',
        'branch_id',
        'supplier_id',
        'warehouse_id',
        'drop_site',
        'phone_drop_site',
        'email_drop_site',
        'eta',
        'notes',
        'attachment',
        'status',
    ];

    protected static function booted()
    {
        static::saving(function ($update) {
            if ($update->eta <= now()->toDateString()) {
                $update->status = 'Received';
            }
        });
    }

    public function getStatusNameAttribute()
    {
        $color = [
            'On Progress' => 'secondary',
            'Received' => 'success',
        ];

        return '<span class="badge badge-' . $color[$this->status] . '">' . $this->status . '</span>';
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(IncomingSupplier::class, 'supplier_id', 'id');
    }

    public function item()
    {
        return $this->hasMany(IncomingInventory::class, 'shipment_id', 'id');
    }

    public function drop()
    {
        return $this->belongsTo(Warehouses::class, 'warehouse_id', 'id');
    }
}
