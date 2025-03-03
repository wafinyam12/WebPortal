<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_maintenance_record';

    protected $appends = ['statusName'];

    protected $fillable = [
        'vehicle_id',
        'code',
        'mileage',
        'maintenance_date',
        'description',
        'cost',
        'next_maintenance',
        'status',
        'notes',
        'photo'
    ];

    public function getStatusNameAttribute()
    {
        $color = [
            'In Progress' => 'secondary',
            'Completed' => 'success',
            'Cancelled' => 'danger'
        ];

        return '<span class="badge badge-' . $color[$this->status] . '">' . $this->status . '</span>';
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
