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
            // Validation de la date
            \Log::info("Tentative de récupération des créneaux pour : ".$date);
            $date = Carbon::createFromFormat('Y-m-d', $date);
            
            $slots = Slot::where('date', $date->format('Y-m-d'))
                       ->where('available', true)
                       ->where('max_reservations', '>', 0)
                       ->get(['id', 'start_time', 'max_reservations']);

            return response()->json($slots);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Date invalide ou erreur serveur : ' . $e->getMessage()
            ], 400);
        }
    }
}