<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees';

    protected $appends = ['activeUsers'];

    protected $fillable = [
        'user_id',
        'company_id',
        'branch_id',
        'department_id',
        'code',
        'nik',
        'full_name',
        'gender',
        'age',
        'phone',
        'position',
        'address',
        'status',
        'photo',
    ];

    protected function getActiveUsersAttribute()
    {
        $color = [
            'Active' => 'success',
            'Inactive' => 'danger',
        ];

        return '<span class="badge badge-' . $color[$this->status] . '">' . $this->status . '</span>';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
