<?php

namespace App\Http\Controllers;

use App\Models\Slot;
use App\Models\Quota;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SlotController extends Controller
{
    // Afficher les créneaux disponibles
    public function index()
    {
        // Récupérer les créneaux disponibles
        $slots = Slot::where('available', true)->get();

        // Récupérer les associations actives
        $associations = User::where('role', 'association')->where('is_active', true)->get();

        // Récupérer les quotas de l'association de l'acheteur
        $user = Auth::user();
        $quota = Quota::where('user_id', $user->association_id)->first();

        // Si aucun quota n'est trouvé, initialiser un objet Quota vide
        if (!$quota) {
            $quota = new Quota([
                'grand' => 0,
                'moyen' => 0,
                'petit' => 0,
            ]);
        }

        return view('slots.index', compact('slots', 'quota', 'associations'));
    }
}