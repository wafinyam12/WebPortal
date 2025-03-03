<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KodeProject extends Model
{
    use HasFactory;

    protected $table = 'project_petty';

    protected $fillable = [
        'code_project',
        'keterangan'
    ];
}
