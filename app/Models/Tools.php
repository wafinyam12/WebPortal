<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tools extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tools';

    protected $appends = ['conditionName', 'statusName'];

    protected $fillable = [
        'owner_id',
        'category_id',
        'code',
        'serial_number',
        'name',
        'brand',
        'type',
        'model',
        'year',
        'origin',
        'quantity',
        'unit',
        'condition',
        'status',
        'description',
        'purchase_date',
        'purchase_price',
        'warranty',
        'warranty_start',
        'warranty_end',
        'photo',
    ];

    public function getConditionNameAttribute()
    {
        $statusColor = [
            'New' => 'success',
            'Used' => 'primary',
            'Broken' => 'danger'
        ];

        return '<span class="badge badge-' . $statusColor[$this->condition] . '">' . $this->condition . '</span>';
    }

    public function getStatusNameAttribute()
    {
        $statusColor = [
            'Maintenance' => 'danger',
            'Active' => 'success',
            'Inactive' => 'secondary',
        ];

        return '<span class="badge badge-' . $statusColor[$this->status] . '">' . $this->status . '</span>';
    }

    public function categorie()
    {
        return $this->belongsTo(ToolsCategorie::class, 'category_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(Branch::class, 'owner_id', 'id');
    }

    public function maintenance()
    {
        return $this->hasMany(ToolsMaintenance::class, 'tool_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(ToolsShipments::class, 'tool_id', 'id');
    }
}
