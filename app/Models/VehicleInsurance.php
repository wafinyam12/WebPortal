<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleInsurance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_insurance_policy';

    protected $appends = ['status'];

    protected $fillable = [
        'vehicle_id',
        'code',
        'insurance_provider',
        'policy_number',
        'coverage_start',
        'coverage_end',
        'premium',
        'notes',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getStatusAttribute()
    {
        return $this->coverage_end > now() ? 'Active' : 'Expired';
    }
}
