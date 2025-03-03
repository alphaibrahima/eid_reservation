@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Réserver votre Agneau</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($slots as $slot)
        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow" data-slot-id="{{ $slot->id }}">
            <!-- ... -->
            
            <!-- Ajouter les champs -->
            <div class="mt-4">
                <div class="flex items-center gap-2 mb-3">
                    <button onclick="document.getElementById('quantity-{{ $slot->id }}').stepDown()" class="px-3 py-1 bg-gray-200 rounded">-</button>
                    <input id="quantity-{{ $slot->id }}" type="number" value="1" min="1" class="w-20 text-center border rounded">
                    <button onclick="document.getElementById('quantity-{{ $slot->id }}').stepUp()" class="px-3 py-1 bg-gray-200 rounded">+</button>
                </div>

                <select id="size-{{ $slot->id }}" class="w-full p-2 border rounded mb-3">
                    <option value="grand">Grand</option>
                    <option value="moyen">Moyen</option>
                    <option value="petit">Petit</option>
                </select>

                <button id="reserveButton-{{ $slot->id }}" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                    Réserver
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- js --}}

    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Récupérer tous les boutons de réservation
                const slotCards = document.querySelectorAll('[data-slot-id]');
                
                slotCards.forEach(card => {
                    const slotId = card.getAttribute('data-slot-id');
                    const reserveButton = document.getElementById(`reserveButton-${slotId}`);
                    
                    if (reserveButton) {
                        reserveButton.addEventListener('click', function() {
                            openReservationModal(slotId);
                        });
                    }
                });
            });
            
            // Fonction pour ouvrir le modal de réservation
            function openReservationModal(slotId) {
                const quantityInput = document.getElementById(`quantity-${slotId}`);
                const sizeSelect = document.getElementById(`size-${slotId}`);
                
                if (!quantityInput || !sizeSelect) {
                    alert('Erreur: Éléments du formulaire non trouvés');
                    return;
                }
                
                // Récupérer les valeurs
                const quantity = quantityInput.value;
                const size = sizeSelect.value;
                
                // Stocker les données dans des variables globales pour la soumission
                window.selectedSlotId = slotId;
                window.selectedQuantity = quantity;
                window.selectedSize = size;
                
                // Mettre à jour le texte dans le modal
                document.getElementById('selectedQuantity').textContent = quantity;
                document.getElementById('selectedSize').textContent = size;
                
                // Afficher le modal
                const modal = document.getElementById('confirmationModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.style.display = 'flex';
                }
            }
        </script>
    @endsection

{{-- js --}}

@endsection