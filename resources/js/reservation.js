// Gestion des étapes
function goToStep(step) {
    if (step < 1 || step > 4) return;

    window.reservationState.currentStep = step;
    document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
    document.getElementById(`step-${step}`).classList.add('active');
    
    // Mise à jour de la progression
    const progress = ((step - 1) / 3) * 100;
    document.getElementById('step-progress').style.width = `${progress}%`;
}

// Sélection du jour
async function handleDaySelection(date) {
    try {
        const url = window.SLOTS_URL.replace('__date__', encodeURIComponent(date));
        const response = await fetch(url);
        if (!response.ok) throw new Error('Erreur réseau');
        
        const slots = await response.json();
        if (!slots.length) throw new Error('Aucun créneau disponible');
        
        window.reservationState.selectedDay = date;
        renderTimeSlots(slots);
        goToStep(2);
        
        // Mise à jour de l'affichage
        document.getElementById('selected-day').textContent = 
            new Date(date).toLocaleDateString('fr-FR', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });

    } catch (error) {
        alert(error.message);
        console.error(error);
    }
}

// Rendu des créneaux horaires
function renderTimeSlots(slots) {
    const container = document.getElementById('time-slots-container');
    container.innerHTML = slots.map(slot => `
        <div class="col">
            <div class="card creneaux-heure" 
                 onclick="handleSlotSelection(${slot.id}, '${slot.start_time}')">
                <div class="card-body text-center">
                    <h5>${slot.start_time}</h5>
                    <p class="text-muted mb-0">${slot.max_reservations} places</p>
                </div>
            </div>
        </div>
    `).join('');
}

// Sélection du créneau
function handleSlotSelection(slotId, startTime) {
    window.reservationState.selectedSlot = slotId;
    document.getElementById('selected-time').textContent = startTime;
    goToStep(3);
}

// Initialisation Stripe
let stripe, cardElement;

document.addEventListener('DOMContentLoaded', async () => {
    try {
        stripe = Stripe(window.STRIPE_PUBLISHABLE_KEY);
        const elements = stripe.elements();
        
        cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSize: '16px',
                    '::placeholder': { color: '#aab7c4' }
                },
                invalid: { color: '#fa755a' }
            }
        });
        
        cardElement.mount('#card-element');
    } catch (error) {
        console.error('Erreur Stripe:', error);
        document.getElementById('payment-section').innerHTML = `
            <div class="alert alert-danger">
                Impossible de charger le système de paiement
            </div>
        `;
    }
});

// Exposition globale
window.handleDaySelection = handleDaySelection;
window.handleSlotSelection = handleSlotSelection;
window.goToStep = goToStep;