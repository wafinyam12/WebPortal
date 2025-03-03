<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;
    protected $table = 'saldo';

    protected $fillable = [
        'owner_id',
        'saldo',
    ];

    //make this saldo belongsto user
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }
}
