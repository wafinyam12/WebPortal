<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VehicleAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle_assignment';

    protected $appends = ['badgeClass'];

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'code',
        'assignment_date',
        'return_date',
        'notes'
    ];

    public function getBadgeClassAttribute()
    {
        $color = [
            'return' => 'success',
            'used' => 'primary',
        ];

        return $this->return_date ? $color['return'] : $color['used'];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'user_id');
    }
}
