<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;

    protected $guard = ['admin', 'customer'];
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'address',
        'phone'
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function rents(){
        return $this->hasMany(Rental::class);
    }
}
