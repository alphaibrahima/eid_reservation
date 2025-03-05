// Configuration globale et état de la réservation
window.reservationState = {
    currentStep: 1,
    selectedDay: null,
    selectedSlot: null,
    size: 'grand',
    quantity: 1
};

// Fonction de validation des étapes
function validateStep(step) {
    switch(step) {
        case 1:
            return true; // Toujours accessible
        
        case 2:
            if (!window.reservationState.selectedDay) {
                alert('Veuillez sélectionner un jour');
                return false;
            }
            return true;
            
        case 3:
            if (!window.reservationState.selectedSlot) {
                alert('Veuillez sélectionner un créneau horaire');
                return false;
            }
            return true;
            
        case 4:
            if (window.reservationState.quantity < 1 || window.reservationState.quantity > 5) {
                alert('Quantité invalide');
                return false;
            }
            return true;
            
        default:
            return false;
    }
}

// Gestion de la progression des étapes
function goToStep(step) {
    // Autoriser le retour en arrière sans validation
    if (step < window.reservationState.currentStep) {
        updateProgress(step);
        showStep(step);
        window.reservationState.currentStep = step;
        return;
    }
    
    // Valider seulement si on avance
    if (validateStep(window.reservationState.currentStep)) {
        updateProgress(step);
        showStep(step);
        window.reservationState.currentStep = step;
    }
}

// Mise à jour de la barre de progression
function updateProgress(step) {
    const progress = ((step - 1) / 3) * 100;
    document.getElementById('step-progress').style.width = `${progress}%`;
    
    document.querySelectorAll('.step-dot').forEach(dot => {
        const dotStep = parseInt(dot.dataset.step);
        dot.classList.remove('active', 'completed');
        if (dotStep < step) dot.classList.add('completed');
        if (dotStep === step) dot.classList.add('active');
    });
}

// Affichage de l'étape courante
function showStep(step) {
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    document.getElementById(`step-${step}`).classList.add('active');
}

// Sélection du jour
async function handleDaySelection(fullDate) {
    try {
        // Extraire uniquement la partie date (YYYY-MM-DD)
        const date = fullDate.split('T')[0]; 
        
        // Validation manuelle
        if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) {
            throw new Error('Format de date invalide');
        }
        const response = await fetch(window.SLOTS_URL.replace('__date__', date));
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        if (!data.length) {
            throw new Error('Aucun créneau disponible');
        }
        window.reservationState.selectedDay = date;
        renderTimeSlots(data);
        updateSelectedDayDisplay(date);
        goToStep(2);
    } catch (error) {
        console.error('Erreur:', error);
        showErrorAlert(error.message);
    }
}

// Mise à jour de l'affichage du jour sélectionné
function updateSelectedDayDisplay(date) {
    const dateParts = date.split('-');
    const jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    };
    
    document.getElementById('selected-day').textContent = 
        jsDate.toLocaleDateString('fr-FR', options);
}

// Affichage des erreurs
function showErrorAlert(message) {
    alert(`Erreur : ${message}`);
}

// Rendu des créneaux horaires
function renderTimeSlots(slots) {
    const container = document.getElementById('time-slots-container');
    
    if (slots.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center py-4">
                <div class="alert alert-warning">
                    Aucun créneau disponible pour cette date
                </div>
            </div>`;
        return;
    }
    
    container.innerHTML = slots.map(slot => `
        <div class="col">
            <div class="card creneaux-heure" 
                 data-slot-id="${slot.id}"
                 onclick="handleSlotSelection('${slot.id}', '${slot.start_time}')">
                <div class="card-body text-center">
                    <h5 class="card-title">${slot.start_time}</h5>
                    <p class="card-text">
                        <small class="text-muted">${slot.max_reservations} places restantes</small>
                    </p>
                </div>
            </div>
        </div>
    `).join('');
}

// Sélection du créneau horaire
function handleSlotSelection(slotId, startTime) {
    window.reservationState.selectedSlot = slotId;
    document.getElementById('selected-time').textContent = startTime;
    goToStep(3);
}

// Gestion de la quantité
// function updateQuantity(change) {
//     const newValue = window.reservationState.quantity + change;
//     if (newValue >= 1 && newValue <= 5) {
//         window.reservationState.quantity = newValue;
//         document.getElementById('quantity').value = newValue;
//         document.getElementById('recap-quantity').textContent = newValue;
//     }
// }

// Mise à jour de la quantité avec incrémentation et décrémentation
function updateQuantity(change) {
    const newValue = window.reservationState.quantity + change;
    if (newValue >= 1 && newValue <= 5) {
        window.reservationState.quantity = newValue;
        document.getElementById('quantity').value = newValue;
        document.getElementById('recap-quantity').textContent = newValue;
    }
}

// Ajout des fonctions spécifiques pour l'incrémentation et la décrémentation
function incrementQuantity() {
    updateQuantity(1);
}

function decrementQuantity() {
    updateQuantity(-1);
}

// Exposition des fonctions globales
window.incrementQuantity = incrementQuantity;
window.decrementQuantity = decrementQuantity;

// Sélection de la taille
function selectSize(size) {
    window.reservationState.size = size;
    document.querySelectorAll('.taille-option').forEach(opt => 
        opt.classList.toggle('selected', opt.dataset.size === size)
    );
}

// Initialisation Stripe
// let stripe;
// let card;

// function initializeStripeElements() {
//     if (typeof Stripe === 'undefined') {
//         console.error('Stripe.js not loaded');
//         return;
//     }
    
//     stripe = Stripe(window.STRIPE_KEY);
//     const elements = stripe.elements();
    
//     card = elements.create('card', {
//         style: {
//             base: {
//                 color: '#32325d',
//                 fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
//                 fontSmoothing: 'antialiased',
//                 fontSize: '16px',
//                 '::placeholder': { color: '#aab7c4' }
//             },
//             invalid: { color: '#fa755a' }
//         }
//     });
    
//     card.mount('#card-element');
//     card.addEventListener('change', handleStripeErrors);
// }

// // Gestion des erreurs Stripe
// function handleStripeErrors(event) {
//     const displayError = document.getElementById('card-errors');
//     displayError.textContent = event.error?.message || '';
// }

// // Traitement du paiement
// async function processPayment() {
//     if (!stripe || !card) {
//         alert('Stripe non initialisé');
//         return;
//     }
    
//     const { error, paymentMethod } = await stripe.createPaymentMethod({
//         type: 'card',
//         card: card,
//         billing_details: {
//             name: document.getElementById('cardholder-name').value,
//             email: document.getElementById('cardholder-email').value
//         }
//     });
    
//     if (error) {
//         handleStripeErrors({ error });
//         return;
//     }
    
//     // Envoyer les données au backend
//     try {
//         const response = await fetch(window.STORE_RESERVATION_URL, {
//             method: 'POST',
//             headers: { 
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
//             },
//             body: JSON.stringify({
//                 ...window.reservationState,
//                 payment_method_id: paymentMethod.id
//             })
//         });
        
//         if (response.ok) {
//             showConfirmationModal();
//         } else {
//             const errorData = await response.json();
//             throw new Error(errorData.message || 'Échec du paiement');
//         }
//     } catch (error) {
//         alert('Erreur: ' + error.message);
//     }
// }


// Initialisation Stripe avec gestion des erreurs
let stripe;
let cardElement;

function initializeStripeElements() {
    // Vérifier si Stripe est chargé
    if (typeof Stripe === 'undefined') {
        console.error('Stripe.js non chargé');
        return;
    }

    try {
        // Initialiser Stripe avec votre clé publique
        stripe = Stripe(window.STRIPE_KEY);
        const elements = stripe.elements({
            mode: 'payment',
            currency: 'eur',
            amount: 20000 // 200€ en centimes
        });

        // Créer l'élément de carte
        cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': { 
                        color: '#aab7c4' 
                    }
                },
                invalid: { 
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            },
            // Options supplémentaires
            hidePostalCode: false
        });

        // Monter l'élément de carte
        cardElement.mount('#card-element');

        // Gérer les erreurs de validation en temps réel
        cardElement.addEventListener('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
                displayError.classList.add('alert', 'alert-danger');
            } else {
                displayError.textContent = '';
                displayError.classList.remove('alert', 'alert-danger');
            }
        });
    } catch (error) {
        console.error('Erreur Stripe:', error);
        alert('Erreur d\'initialisation du paiement. Veuillez réessayer.');
    }
}

// Processus de paiement
async function processPayment() {
    // Validation des informations
    const nomTitulaire = document.getElementById('cardholder-name').value;
    const emailTitulaire = document.getElementById('cardholder-email').value;

    if (!nomTitulaire || !emailTitulaire) {
        alert('Veuillez remplir le nom et l\'email du titulaire');
        return;
    }

    // Désactiver le bouton de paiement pendant le traitement
    const paymentButton = document.getElementById('confirm-payment-btn');
    paymentButton.disabled = true;
    paymentButton.innerHTML = 'Traitement en cours...';

    try {
        // Créer le méthode de paiement
        const {error, paymentMethod} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
            billing_details: {
                name: nomTitulaire,
                email: emailTitulaire
            }
        });

        if (error) {
            // Gérer les erreurs Stripe
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
            errorElement.classList.add('alert', 'alert-danger');
            
            // Réactiver le bouton
            paymentButton.disabled = false;
            paymentButton.innerHTML = 'Confirmer et Payer';
            return;
        }

        // Envoi des données de réservation et de paiement
        const response = await fetch(window.STORE_RESERVATION_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                ...window.reservationState,
                payment_method_id: paymentMethod.id,
                amount: 20000, // 200€ en centimes
                description: 'Acompte Réservation Agneau'
            })
        });

        const result = await response.json();

        if (response.ok) {
            // Succès du paiement
            showConfirmationModal(result.reservation_number);
        } else {
            // Erreur côté serveur
            throw new Error(result.message || 'Échec du paiement');
        }
    } catch (error) {
        console.error('Erreur de paiement:', error);
        alert('Erreur de paiement : ' + error.message);
    } finally {
        // Réactiver le bouton
        paymentButton.disabled = false;
        paymentButton.innerHTML = 'Confirmer et Payer';
    }
}

// Initialisation au chargement du document
document.addEventListener('DOMContentLoaded', () => {
    // Charger Stripe uniquement si disponible
    if (window.Stripe) {
        initializeStripeElements();
    } else {
        console.warn('Stripe.js non chargé');
    }
});

// Exposer les fonctions globalement
window.processPayment = processPayment;


// Affichage de la modal de confirmation
function showConfirmationModal() {
    const modal = new bootstrap.Modal('#confirmationModal');
    document.getElementById('confirmation-number').textContent = 'R-' + Date.now();
    modal.show();
}

// Écouteurs d'événements au chargement du document
document.addEventListener('DOMContentLoaded', () => {
    // Initialisation Stripe conditionnelle
    if (typeof Stripe !== 'undefined') {
        initializeStripeElements();
    }
    
    // Événements délégués pour les jours
    document.body.addEventListener('click', function(e) {
        const dayCard = e.target.closest('.creneaux-jour');
        if (dayCard) {
            const fullDate = dayCard.dataset.date;
            handleDaySelection(fullDate);
        }
    });
});

// Exposition des fonctions globales
window.goToStep = goToStep;
window.handleSlotSelection = handleSlotSelection;
window.updateQuantity = updateQuantity;
window.selectSize = selectSize;
window.processPayment = processPayment;