<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Quota;
use App\Models\Slot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function processReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            // 1. Création de la réservation
            $reservation = Reservation::create([
                'user_id' => $data['user_id'],
                'slot_id' => $data['slot_id'],
                'association_id' => $data['association_id'],
                'quantity' => $data['quantity'],
                'size' => $data['size'],
                'code' => $data['code'],
                'payment_intent_id' => $data['payment_intent_id'],
                'status' => 'confirmed'
            ]);

            // 2. Mise à jour atomique du quota avec verrouillage
            Quota::where('association_id', $data['association_id'])
                ->lockForUpdate()
                ->decrement($data['size'], $data['quantity']);

            // 3. Mise à jour de la capacité du créneau
            Slot::where('id', $data['slot_id'])
                ->decrement('available_capacity', $data['quantity']);

            return $reservation->load(['slot.association', 'user']);
        });
    }

    public function prepareReservationData(array $sessionData, string $paymentIntentId): array
    {
        return [
            'user_id' => auth()->id(),
            'slot_id' => $sessionData['slot_id'],
            'association_id' => $sessionData['association_id'],
            'quantity' => $sessionData['quantity'],
            'size' => $sessionData['size'],
            'code' => Str::upper(Str::random(6)),
            'payment_intent_id' => $paymentIntentId
        ];
    }
}