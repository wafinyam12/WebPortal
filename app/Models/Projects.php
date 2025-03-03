<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projects extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects';

    protected $appends = ['statusName'];

    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'ppic',
        'description',
        'status',
    ];

    public function getStatusNameAttribute()
    {
        return $this->status == 'Active' ? 'success' : 'danger';
    }
}
