<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketsCategories extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function tickets()
    {
        return $this->hasMany(Tickets::class, 'category_id', 'id');
    }
}
