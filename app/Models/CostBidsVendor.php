<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBidsVendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_bids_vendor';

    protected $fillable = [
        'cost_bids_id',
        'name',
        'phone',
        'email',
        'address',
        'grand_total',
        'discount',
        'final_total',
        'terms_of_payment',
        'lead_time',
        'notes',
    ];

    public function costBids()
    {
        return $this->belongsTo(CostBids::class, 'cost_bids_id', 'id');
    }
}
