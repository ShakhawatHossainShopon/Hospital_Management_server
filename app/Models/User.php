<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'user_id');
    }
    public function patients()
    {
        return $this->hasMany(Patient::class, 'user_id');
    }
    public function appoinments()
    {
        return $this->hasMany(Appointment::class, 'user_id');
    }
    public function tests()
    {
        return $this->hasMany(Test::class, 'user_id');
    }
    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'user_id');
    }

    public function references()
    {
        return $this->hasMany(Reference::class, 'user_id');
    }
    public function services()
    {
        return $this->hasMany(Service::class, 'user_id');
    }
    public function bills()
    {
        return $this->hasMany(Bill::class, 'user_id');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class, 'user_id');
    }
    
}
