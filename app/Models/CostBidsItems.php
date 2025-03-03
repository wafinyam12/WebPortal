<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBidsItems extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_bids_items';

    protected $fillable = [
        'cost_bids_id',
        'description',
        'quantity',
        'uom',
    ];

    public function costBids()
    {
        return $this->belongsTo(CostBids::class, 'cost_bids_id', 'id');
    }

    public function costBidsAnalysis()
    {
        return $this->hasMany(CostBidsAnalysis::class, 'cost_bids_item_id', 'id');
    }
}
