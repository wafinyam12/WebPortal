<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBidsAnalysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_bids_analysis';

    protected $fillable = [
        'cost_bids_item_id',
        'cost_bids_vendor_id',
        'price',
    ];

    public function costBidsItem()
    {
        return $this->belongsTo(CostBidsItems::class, 'cost_bids_item_id', 'id');
    }

    public function costBidsVendor()
    {
        return $this->belongsTo(CostBidsVendor::class, 'cost_bids_vendor_id', 'id');
    }
}
