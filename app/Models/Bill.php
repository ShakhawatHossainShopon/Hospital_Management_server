<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Bill extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guarded = [];

    public function patient()
    {
    return $this->belongsTo(Patient::class);
    }
}
