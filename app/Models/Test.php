<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Test extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guarded = [];

    public function groupe(){
         return $this->belongsTo(Groupe::class, 'groupe_id');
    }
}
