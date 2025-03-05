@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="page-container">
        <h1 class="text-center mb-5">Réservation d'Agneau pour l'Eid</h1>
        
        <!-- Indicateur de progression -->
        @include('slots.partials.progress')
        
        <!-- Étape 1: Sélection du jour -->
        <div class="step active" id="step-1">
            <h3 class="mb-4">Étape 1: Choisissez un jour</h3>
            <div class="row row-cols-1 row-cols-md-3 g-4" id="days-container">
                @foreach($availableDays as $day)
                <div class="col">
                    <div class="card creneaux-jour" data-date="{{ $day['date'] }}">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $day['label'] }}</h5>
                            <p class="card-text">
                                <span class="badge bg-{{ $day['status_color'] }}">
                                    {{ $day['slots_count'] }} créne{{ $day['slots_count'] > 1 ? 'aux' : 'au' }} disponible
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Étape 2: Sélection de l'heure -->
        @include('slots.partials.time-selection')

        <!-- Étape 3: Configuration de la commande -->
        @include('slots.partials.order-config')

        <!-- Étape 4: Paiement -->
        @include('slots.partials.payment')
        
        <!-- Modal de confirmation -->
        @include('slots.partials.confirmation-modal')
    </div>
</div>
@endsection

@section('scripts')
{{-- Ensuite charger le script spécifique de réservation --}}
@vite(['resources/js/reservation.js'])

<script>
    // État global minimal
    window.reservationState = {
        currentStep: 1,
        selectedDay: null,
        selectedSlot: null,
        size: 'grand',
        quantity: 1
    };
</script>
@endsection

