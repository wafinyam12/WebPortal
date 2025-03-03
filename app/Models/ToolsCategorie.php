<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolsCategorie extends Model
{
    use HasFactory;

    protected $table = 'tools_categories';

    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function tools()
    {
        return $this->hasMany(Tools::class);
    }
}
