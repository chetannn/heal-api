<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Doctor extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $guarded = [];

    public function appointmentRequests(): HasMany
    {
        return $this->hasMany(AppointmentRequest::class);
    }

    public function appointments() : HasMany
    {
        return $this->hasMany(Appointment::class);
    }

}
