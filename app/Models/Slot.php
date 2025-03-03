<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function reservations(): HasMany // Type correct
    {
        return $this->hasMany(Reservation::class);
    }
}
