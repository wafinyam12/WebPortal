<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    use HasFactory;

    protected $table = 'warehouses';

    protected $appends = ['statusName', 'typeName'];

    protected $fillable = [
        'company_id',
        'branch_id',
        'code',
        'name',
        'phone',
        'email',
        'address',
        'description',
        'status',
        'type',
    ];

    public function getStatusNameAttribute()
    {
        $color = [
            'Active' => 'success',
            'Inactive' => 'danger',
        ];

        return '<span class="badge badge-' . $color[$this->status] . '">' . $this->status . '</span>';
    }

    public function getTypeNameAttribute()
    {
        $color = [
            'Warehouse' => 'secondary',
            'Raw Material' => 'primary',
            'Finished Goods' => 'info'
        ];

        return '<span class="badge badge-' . $color[$this->type] . '">' . $this->type . '</span>';
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }
}
