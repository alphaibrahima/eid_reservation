<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Reservation;
use App\Models\Slot;
use App\Models\Association;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'selectedDay' => 'required|date',
            'selectedSlot' => 'required|exists:slots,id,association_id,NOT_NULL',
            'size' => 'required|in:petit,moyen,grand',
            'quantity' => 'required|integer|min:1|max:5',
            'payment_method_id' => 'required|string',
            'amount' => 'required|integer|min:20000'
        ]);

        try {
            DB::beginTransaction();

            // Configuration Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // Création du PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $validatedData['amount'],
                'currency' => 'eur',
                'payment_method' => $validatedData['payment_method_id'],
                'description' => 'Acompte Réservation Agneau',
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ]
            ]);

            if (!in_array($paymentIntent->status, ['succeeded', 'requires_capture'])) {
                throw new \Exception('Paiement échoué : ' . $paymentIntent->status);
            }

            // Récupération et validation du slot
            $slot = Slot::with('association')->findOrFail($validatedData['selectedSlot']);
            
            if (!$slot->association) {
                throw new \Exception('Ce créneau n\'est associé à aucune organisation');
            }

            // Création de la réservation
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'slot_id' => $slot->id,
                'association_id' => $slot->association_id, // Ajout crucial
                'size' => $validatedData['size'],
                'quantity' => $validatedData['quantity'],
                'date' => $validatedData['selectedDay'],
                'payment_status' => 'paid',
                'payment_intent_id' => $paymentIntent->id,
                'total_amount' => $validatedData['amount'] / 100
            ]);

            // Mise à jour des quotas
            $this->updateQuota($reservation);

            DB::commit();

            return response()->json([
                'success' => true,
                'reservation_number' => 'R-' . $reservation->id,
                'message' => 'Réservation confirmée',
                'details' => [
                    'day' => $reservation->date->format('l d F Y'),
                    'time' => $slot->start_time,
                    'size' => $reservation->size,
                    'quantity' => $reservation->quantity,
                    'association' => $slot->association->name
                ]
            ]);

        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur de paiement : ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Erreur réservation : {$e->getMessage()}\n{$e->getTraceAsString()}");
            return $this->errorResponse('Erreur interne : ' . $e->getMessage(), 500);
        }
    }

    private function updateQuota(Reservation $reservation)
    {
        $association = Association::findOrFail($reservation->association_id);
        $quota = $association->quota()->firstOrCreate();

        $sizeField = match($reservation->size) {
            'petit' => 'petit',
            'moyen' => 'moyen',
            'grand' => 'grand',
            default => throw new \Exception('Taille invalide')
        };

        if ($quota->$sizeField < $reservation->quantity) {
            throw new \Exception("Quota insuffisant pour {$reservation->size} ({$quota->$sizeField} restants)");
        }

        $quota->decrement($sizeField, $reservation->quantity);
    }

    private function errorResponse($message, $statusCode)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $statusCode);
    }
}