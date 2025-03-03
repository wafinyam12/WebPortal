<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyRecap extends Model
{
    use HasFactory;

    protected $table = 'petty_recap';

    protected $fillable = [
        'tanggal',
        'tanggal_awal',
        'tanggal_akhir',
        'owner_id',
        
    ];

    //koneksikan owner_id ke users
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
}
