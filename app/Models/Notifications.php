<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notifications extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notifications';

    protected $appends = ['nameRole'];

    protected $fillable = [
        'name',
        'description',
        'roles',
        'template',
    ];

    public function getNameRoleAttribute()
    {
        $color = [
            'Superadmin' => 'dark',
            'Manager' => 'primary',
            'Area' => 'warning',
            'Admin' => 'success',
            'Staff' => 'info',
            'User' => 'secondary',
        ];

        // Decode roles only if not already decoded
        $roles = is_string($this->roles) ? json_decode($this->roles) : $this->roles;

        // Return empty string if roles is not valid
        if (!is_array($roles)) {
            return '';
        }

        // Generate badges
        $badges = array_map(function ($role) use ($color) {
            // Ambil kata pertama dari role
            $firstWord = strtok($role, ' ');

            // Cocokkan kata pertama dengan daftar warna
            if (isset($color[$firstWord])) {
                return "<span class='badge bg-{$color[$firstWord]} text-white'>" . e($role) . "</span>";
            }

            // Jika tidak ada warna yang cocok, tidak ditampilkan
            return null;
        }, $roles);

        // Remove null values and return badges
        return implode(' ', array_filter($badges));
    }
}
