<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Models\Quota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class SlotController extends Controller
{

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        $availableDays = Slot::where('available', true)
            ->where('max_reservations', '>', 0)
            ->get()
            ->groupBy('date')
            ->map(function($slots, $date) {
                $carbonDate = \Carbon\Carbon::parse($date);
                return [
                    'date' => $carbonDate->format('Y-m-d'), // Formatage correct
                    'label' => $carbonDate->translatedFormat('l j F'),
                    'slots_count' => $slots->count(),
                    'status_color' => $slots->count() > 5 ? 'success' : ($slots->count() > 2 ? 'warning' : 'danger')
                ];
            })
            ->values();
    
        return view('slots.index', [
            'availableDays' => $availableDays,
            // ... reste inchangé
        ]);
    }

    public function getAvailableSlots($date)
    {
        try {
            // Valider le format de date
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return response()->json(['error' => 'Format de date invalide'], 400);
            }
    
            $slots = Slot::where('date', $date)
                       ->where('available', true)
                       ->where('max_reservations', '>', 0)
                       ->get();
    
            if($slots->isEmpty()) {
                return response()->json(['error' => 'Aucun créneau disponible'], 404);
            }
    
            return response()->json($slots);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }
}