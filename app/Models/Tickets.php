<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tickets extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets';

    protected $appends = ['badgeClass', 'priorityClass'];

    protected $dates = ['closed_date'];

    protected $fillable = [
        'user_id',
        'category_id',
        'assigned_id',
        'user_by',
        'code',
        'title',
        'description',
        'priority',
        'status',
        'solution',
        'attachment',
        'closed_date',
        'reason',
    ];

    public function getBadgeClassAttribute()
    {
        return $this->status == 'Open' ? 'info' : ($this->status == 'Closed' ? 'success' : ($this->status == 'In Progress' ? 'secondary' : 'danger'));
    }

    public function getPriorityClassAttribute()
    {
        return $this->priority == 'High' ? 'danger' : ($this->priority == 'Medium' ? 'warning' : ($this->priority == 'Low' ? 'primary' : ($this->priority == 'Urgent' ? 'dark' : 'secondary')));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(TicketsCategories::class, 'category_id', 'id');
    }

    public function assignee()
    {
        return $this->belongsTo(Department::class, 'assigned_id', 'id');
    }

    public function fixed()
    {
        return $this->belongsTo(User::class, 'user_by', 'id');
    }

    public function comments()
    {
        return $this->hasMany(TicketsComments::class, 'ticket_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'assigned_id', 'id');
    }
}
