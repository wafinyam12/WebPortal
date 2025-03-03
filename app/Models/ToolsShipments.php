<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ToolsShipments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tools_transactions_shipments';

    protected $fillable = [
        'transactions_id',
        'tool_id',
        'quantity',
        'unit',
        'last_location',
    ];

    public function tool()
    {
        return $this->belongsTo(Tools::class, 'tool_id', 'id');
    }

    public function transaction()
    {
        return $this->belongsTo(ToolsTransaction::class, 'transactions_id', 'id');
    }
}
