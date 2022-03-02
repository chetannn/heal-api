<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class CheckupNote extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function checkedBy() : BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient() : BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment() : BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'resource')->orderBy('id', 'DESC');
    }

}
