@extends('layouts.app')

@section('title', 'Réservation d\'Agneau')

@push('styles')
    <style>
        /* Tous les styles CSS spécifiques à la réservation */
        .page-container { width: 66.66%; margin: 0 auto; }
        .creneaux-jour { cursor: pointer; transition: all 0.3s; }
        /* ... (coller ici TOUS les styles du fichier HTML) ... */
    </style>
@endpush

@section('content')
    <div class="page-container">
        <h1 class="text-center mb-5">Réservation d'Agneau pour l'Eid</h1>
        
        <!-- Indicateur de progression -->
        @include('reservation.partials.step-indicator')

        <!-- Contenu dynamique des étapes -->
        @yield('reservation-content')

        <!-- Modal de confirmation (commun à toutes les étapes) -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
            <!-- ... (copier exactement le code modal du HTML) ... -->
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Variables globales de réservation
        window.Reservation = {
            selectedDay: @json(session('reservation.day')),
            selectedTime: @json(session('reservation.time')),
            // ... autres variables de session
        };
    </script>
@endpush