<div class="step">
    <h3 class="mb-4">Étape 2: Choisissez un créneau <small class="text-muted">{{ $selectedDayFormatted }}</small></h3>
    
    <div class="d-flex mb-3">
        <button class="btn btn-outline-secondary me-2" onclick="Reservation.goToStep(1)">
            <i class="bi bi-arrow-left"></i> Retour
        </button>
    </div>

    <div class="row row-cols-2 row-cols-md-4 g-3" id="time-slots-container">
        @foreach($timeSlots as $slot)
            <div class="col">
                <div class="card creneaux-heure 
                     {{ $selectedSlotId == $slot->id ? 'selected' : '' }}" 
                     onclick="Reservation.selectSlot({{ $slot->id }})">
                    <div class="card-body text-center">
                        <h5 class="card-title">{{ $slot->start_time->format('H:i') }}</h5>
                        <p class="card-text">
                            <small class="text-muted">{{ $slot->available_capacity }} places</small>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>