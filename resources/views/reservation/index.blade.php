@extends('layouts.reservation')

@section('reservation-content')
    @if($currentStep == 1)
        @include('reservation.partials.step1-days')
    @elseif($currentStep == 2)
        @include('reservation.partials.step2-hours')
    @elseif($currentStep == 3)
        @include('reservation.partials.step3-config')
    @elseif($currentStep == 4)
        @include('reservation.partials.step4-payment')
    @endif

    @push('scripts')
        <script>

            window.reservationState = {
                    currentStep: 1,
                    selectedDay: null,
                    selectedSlot: null,
                    size: 'grand',
                    quantity: 1
                };
            // Logique JS spécifique à la réservation
            class ReservationFlow {
                static selectDay(day) {
                    fetch('/reservation/select-day', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ day })
                    }).then(() => window.location.href = '/reservation/time-slots');
                }

                static goToStep(step) {
                    // Logique de navigation entre étapes
                    if(step < 1 || step > 4) return;
                    
                    fetch(`/reservation/step/${step}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    }).then(response => {
                        window.location.href = `/reservation/${step}`;
                    });
                }
            }

            // Initialisation Stripe
            const stripe = Stripe('{{ config('services.stripe.key') }}');
            const elements = stripe.elements();
            const cardElement = elements.create('card');
            cardElement.mount('#card-element');
        </script>
    @endpush
@endsection