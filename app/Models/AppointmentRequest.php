<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

//    protected $casts = [
//        'accepted_at' => 'timestamp'
//    ];

    public function doctor() : BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }


}
