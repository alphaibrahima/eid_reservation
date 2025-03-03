<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @yield('scripts')

    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>

        
        <!-- Loader - Remplacez le div existant avec id="loader" -->
        <div id="loader" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
            <div class="bg-white p-6 rounded-lg flex items-center">
                <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-3 text-gray-700">Traitement en cours...</span>
            </div>
        </div>

        <!-- Modal de Confirmation - Remplacez le div existant avec id="confirmationModal" -->
        <div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
            <div class="bg-white p-8 rounded-xl max-w-md w-full">
                <h3 class="text-xl font-bold mb-4">Confirmez votre réservation</h3>
                <p class="mb-4">
                    Vous réservez <span id="selectedQuantity" class="font-semibold"></span> agneau(s) <span id="selectedSize" class="font-semibold"></span><br>
                    le <span id="selectedDate" class="font-semibold">-</span>
                </p>
                <div class="flex justify-end space-x-4">
                    <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Annuler</button>
                    <button onclick="submitReservation()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Confirmer
                    </button>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script>
        
        // Fermer le modal de manière fiable
            // Remplacez la fonction closeModal() existante par celle-ci
        function closeModal() {
            const modal = document.getElementById('confirmationModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                
                // Réinitialiser les variables globales
                window.selectedSlotId = null;
                window.selectedSize = null;
                window.selectedQuantity = null;
            }
        }

        // Remplacez la fonction submitReservation() existante par celle-ci
        async function submitReservation() {
            try {
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.classList.remove('hidden');
                    loader.style.display = 'flex';
                }

                if (!window.selectedSlotId || !window.selectedSize || !window.selectedQuantity) {
                    throw new Error('Données de réservation manquantes');
                }

                const response = await fetch("{{ route('reservations.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        slot_id: window.selectedSlotId,
                        size: window.selectedSize,
                        quantity: parseInt(window.selectedQuantity)
                    })
                });

                // Gérer les réponses non-JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Réponse invalide du serveur');
                }

                const data = await response.json();
                
                if (data.success) {
                    // Succès, recharger la page après un court délai
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    throw new Error(data.message || "Erreur inconnue");
                }

            } catch (error) {
                console.error("Erreur critique :", error);
                alert("Erreur : " + error.message);
            } finally {
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.classList.add('hidden');
                    loader.style.display = 'none';
                }
                
                closeModal(); // Garantir la fermeture
            }
        }
        </script>
    </body>
</html>