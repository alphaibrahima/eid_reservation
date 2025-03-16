<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'date', 
        'start_time',
        'end_time',
        'max_reservations', 
        'available' 
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'available' => 'boolean',
    ];


        // Ajoutez ce scope de requête
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('available', true)
            ->whereRaw('(SELECT COUNT(*) FROM reservations WHERE slot_id = slots.id) < slots.max_reservations');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Nouvelle méthode pour vérifier la disponibilité
    public function isAvailable(): bool
    {
        return $this->available && $this->reservations()->count() < $this->max_reservations;
    }

    // Méthode pour formater la plage horaire
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i').' - '.$this->end_time->format('H:i');
    }

    public static function availableDays()
    {
        return Cache::remember('available_days', 300, function () {
            return self::available()
                ->selectRaw('DATE(date) as date, COUNT(*) as slots')
                ->groupBy('date')
                ->get();
        });
    }

    // Dans la classe Slot
    public function association(): BelongsTo
    {
        return $this->belongsTo(Association::class, 'association_id');
    }


}