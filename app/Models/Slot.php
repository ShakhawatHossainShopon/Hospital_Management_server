<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Slot extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guarded = [];

    public function appoinment()
{
    return $this->belongsTo(Appointment::class);
}
}
