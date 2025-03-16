<div class="step active">
    <h3 class="mb-4">Étape 1: Choisissez un jour</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($availableDays as $date => $day)
            <div class="col">
                <div class="card creneaux-jour" data-date="{{ $date }}">
                    <div class="card-body text-center">
                        <!-- Utilisez directement la date formatée du tableau -->
                        <h5 class="card-title">{{ $day['formatted_date'] }}</h5>
                        <p class="card-text">
                            <!-- Passez le statut au lieu de l'objet slot -->
                            @include('reservation.partials.availability-badge', [
                                'status' => $day['status'],
                                'available_slots' => $day['available_slots']
                            ])
                        </p>
                        <form method="POST" action="{{ route('reservation.select-day') }}">
                            @csrf
                            <input type="hidden" name="day" value="{{ $date }}">
                            <button type="submit" class="btn btn-primary">Choisir</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>