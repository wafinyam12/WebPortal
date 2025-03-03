<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToolsTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tools_transactions';

    protected $appends = ['typeName', 'statusName'];

    protected $fillable = [
        'user_id',
        'source_project_id',
        'destination_project_id',
        'document_code',
        'document_date',
        'delivery_date',
        'driver',
        'driver_phone',
        'transportation',
        'plate_number',
        'status',
        'type',
        'notes',
    ];

    protected static function booted()
    {
        static::saving(function ($transaction) {
            if ($transaction->delivery_date <= now()->toDateString()) {
                $transaction->status = 'Completed';
            }
        });
    }

    public function gettypeNameAttribute()
    {
        $color = [
            'Return' => 'secondary',
            'Delivery Note' => 'success',
            'Transfer' => 'primary',
        ];

        return '<span class="badge badge-' . $color[$this->type] . '">' . $this->type . '</span>';
    }

    public function getstatusNameAttribute()
    {
        $color = [
            'In Progress' => 'secondary',
            'Completed' => 'success',
            'Cancelled' => 'danger'
        ];

        return '<span class="badge badge-' . $color[$this->status] . '">' . $this->status . '</span>';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tools()
    {
        return $this->hasMany(ToolsShipments::class, 'transactions_id', 'id');
    }

    public function sourceTransactions()
    {
        return $this->belongsTo(Projects::class, 'source_project_id', 'id');
    }

    public function destinationTransactions()
    {
        return $this->belongsTo(Projects::class, 'destination_project_id', 'id');
    }

    public function shipments()
    {
        return $this->hasMany(ToolsShipments::class, 'id', 'transactions_id');
    }
}
