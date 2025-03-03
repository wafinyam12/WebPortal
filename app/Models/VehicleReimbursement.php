<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleReimbursement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_reimbursement';

    protected $appends = ['statusName', 'typeName'];

    protected $fillable = [
        'vehicle_id',
        'date_recorded',
        'user_by',
        'fuel',
        'amount',
        'price',
        'first_mileage',
        'last_mileage',
        'attachment_mileage',
        'attachment_receipt',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'type',
        'reason',
    ];

    public function getStatusNameAttribute()
    {
        $color = [
            'Approved' => 'success',
            'Rejected' => 'danger',
            'Pending' => 'warning',
        ];

        return '<span class="badge badge-' . $color[$this->status] . '">' . $this->status . '</span>';
    }

    public function getTypeNameAttribute()
    {
        $color = [
            'Refueling' => 'success',
            'Parking' => 'secondary',
            'E-Toll' => 'primary',
        ];

        return '<span class="badge badge-' . $color[$this->type] . '">' . $this->type . '</span>';
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_by', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by', 'id');
    }
}
