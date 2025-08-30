<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Prescription extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guarded = [];

    // App/Models/Prescription.php

public function patient()
{
    return $this->belongsTo(Patient::class);
}

public function appointment()
{
    return $this->belongsTo(Appointment::class);
}

}
