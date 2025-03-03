<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CostBids extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cost_bids';

    protected $appends = ['statusName'];

    protected $fillable = [
        'code',
        'branch_id',
        'project_name',
        'document_date',
        'bid_date',
        'selected_vendor',
        'attachment',
        'status',
        'notes',
        'token',
        'created_by',
        'approved_by',
        'rejected_by',
        'reason',
        'approved_at',
        'rejected_at',
    ];

    public function getstatusNameAttribute()
    {
        $color = [
            'Open' => 'secondary',
            'Approved' => 'success',
            'Rejected' => 'danger',
        ];

        return '<span class="badge badge-' . $color[$this->status] . '">' . $this->status . '</span>';
    }

    public function vendors()
    {
        return $this->hasMany(CostBidsVendor::class, 'cost_bids_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(CostBidsItems::class, 'cost_bids_id', 'id');
    }

    public function analysis()
    {
        return $this->hasMany(CostBidsAnalysis::class, 'cost_bids_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
