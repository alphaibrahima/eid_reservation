<?php

namespace App\Http\Controllers;


use App\Models\Reservation;
use App\Models\Slot;
use App\Models\User;

use App\Models\Quota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReservationController extends Controller
{

    // Enregistrer une réservation
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'slot_id' => 'required|exists:slots,id',
    //         'size' => 'required|in:grand,moyen,petit',
    //     ]);
    
    //     $user = Auth::user();
    //     $slot = Slot::findOrFail($request->slot_id);
    //     $quota = Quota::where('user_id', $user->association_id)->first();
    
    //     // Vérifier si l'association de l'acheteur est active
    //     $association = User::where('id', $user->association_id)
    //                        ->where('role', 'association')
    //                        ->where('is_active', true)
    //                        ->first();
    
    //     if (!$association) {
    //         return redirect()->route('slots.index')->with('error', 'Votre association n\'est pas active.');
    //     }
    
    //     // Vérifier les quotas disponibles
    //     if ($quota->{$request->size} <= 0) {
    //         return redirect()->route('slots.index')->with('error', 'Quota épuisé pour cette taille.');
    //     }
    
    //     // Vérifier la capacité du créneau
    //     if ($slot->reservations->count() >= $slot->max_capacity) {
    //         return redirect()->route('slots.index')->with('error', 'Ce créneau est complet.');
    //     }
    
    //     // Créer la réservation
    //     Reservation::create([
    //         'user_id' => $user->id,
    //         'slot_id' => $slot->id,
    //         'size' => $request->size,
    //     ]);
    
    //     // Mettre à jour les quotas
    //     $quota->{$request->size} -= 1;
    //     $quota->save();
    
    //     return redirect()->route('slots.index')->with('success', 'Réservation réussie !');
    // }
    public function store(Request $request)
    
    {
        try {
            $validated = $request->validate([
                'slot_id' => 'required|exists:slots,id',
                'size' => 'required|in:grand,moyen,petit',
                'quantity' => 'required|integer|min:1'
            ]);

            // Ajouter l'ID de l'utilisateur connecté
            $validated['user_id'] = Auth::id();

            Reservation::create($validated);

            return response()->json(['success' => true]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }

}
