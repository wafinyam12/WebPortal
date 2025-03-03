<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $appends = ['badgeClass', 'fullName'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'username',
        'last_name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        if (is_null($this->last_name)) {
            return "{$this->name}";
        }

        return "{$this->name} {$this->last_name}";
    }

    public function getBadgeClassAttribute()
    {
        $statusColor = [
            'Superadmin' => 'dark',
            'Manager' => 'warning',
            'Admin' => 'success',
            'Staff' => 'info',
            'User' => 'secondary',
        ];

        // Ambil nama peran pertama
        $role = $this->getRoleNames()[0];

        // Jika peran mengandung kata 'Admin', ubah menjadi 'Admin'
        if (str_contains($role, 'Admin')) {
            $role = 'Admin';
        }

        if (str_contains($role, 'Staff')) {
            $role = 'Staff';
        }

        // Berikan warna default jika status tidak ditemukan
        return $statusColor[$role] ?? 'primary';
    }

    public function employe()
    {
        return $this->hasOne(Employe::class, 'user_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(VehicleAssignment::class, 'vehicle_id');
    }
}
