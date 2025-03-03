<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketsComments extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets_comments';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function ticket()
    {
        return $this->belongsTo(Tickets::class, 'ticket_id', 'id');
    }
}
