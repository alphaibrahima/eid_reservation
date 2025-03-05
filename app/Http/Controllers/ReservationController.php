<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'selectedDay' => 'required|date',
            'selectedSlot' => 'required|exists:slots,id',
            'size' => 'required|in:petit,moyen,grand',
            'quantity' => 'required|integer|min:1|max:5',
            'payment_method_id' => 'required|string',
            'amount' => 'required|integer|min:20000' // 200€ minimum
        ]);

        try {
            // Configuration Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // Créer un PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $validatedData['amount'], // Montant en centimes
                'currency' => 'eur',
                'payment_method' => $validatedData['payment_method_id'],
                'confirm' => true,
                'description' => 'Acompte Réservation Agneau'
            ]);

            // Vérifier le statut du paiement
            if ($paymentIntent->status !== 'succeeded') {
                throw new \Exception('Le paiement a échoué');
            }

            // Créer la réservation
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'slot_id' => $validatedData['selectedSlot'],
                'size' => $validatedData['size'],
                'quantity' => $validatedData['quantity'],
                'date' => $validatedData['selectedDay'],
                'payment_status' => 'paid',
                'payment_intent_id' => $paymentIntent->id,
                'total_amount' => $validatedData['amount'] / 100 // Convertir en euros
            ]);

            return response()->json([
                'success' => true,
                'reservation_number' => 'R-' . $reservation->id,
                'message' => 'Réservation confirmée avec succès'
            ]);

        } catch (\Exception $e) {
            // Gérer les erreurs
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}