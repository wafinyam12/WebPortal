<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsuransiProject extends Model
{
    use HasFactory;
    protected $table = 'asuransi_project';
    protected $fillable = [
       'project_id',
       'name',
       'tanggal_mulai',
       'masa_berlaku',
       'tanggal_jatuh_tempo',
       'status',
       'catatan'
    ];  
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id', 'id');
    }
}
