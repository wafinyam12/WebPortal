<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'vehicle';

    protected $appends = ['badgeClass'];

    protected $fillable = [
        'owner_id',
        'type_id',
        'code',
        'brand',
        'model',
        'color',
        'license_plate',
        'transmission',
        'fuel',
        'year',
        'tax_year',
        'tax_five_year',
        'inspected',
        'purchase_date',
        'purchase_price',
        'description',
        'origin',
        'status',
        'photo',
    ];

    public function getBadgeClassAttribute()
    {
        $statusColor = [
            'Active' => 'success',
            'Maintenance' => 'warning',
            'Inactive' => 'danger',
        ];

        return '<span class="badge badge-' . $statusColor[$this->status] . '">' . $this->status . '</span>';
    }

    public function type()
    {
        return $this->belongsTo(VehicleType::class, 'type_id');
    }

    public function ownership()
    {
        return $this->belongsTo(Branch::class, 'owner_id', 'id');
    }

    public function assigned()
    {
        return $this->hasMany(VehicleAssignment::class, 'vehicle_id', 'id');
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(VehicleMaintenance::class, 'id');
    }

    public function insurancePolicies()
    {
        return $this->hasOne(VehicleInsurance::class, 'id');
    }
}
