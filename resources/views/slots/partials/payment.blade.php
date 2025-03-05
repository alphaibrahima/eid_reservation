<div class="step" id="step-4">
    <!-- ... Garde le même code pour le paiement ... -->

    <h3 class="mb-4">Étape 4: Confirmation & Paiement d'acompte</h3>
    <div class="d-flex mb-3">
        <button class="btn btn-outline-secondary me-2" onclick="goToStep(3)">
            <i class="bi bi-arrow-left"></i> Retour
        </button>
    </div>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Récapitulatif de votre réservation</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Jour:</strong> <span id="recap-day">Mardi 11 Mars 2025</span></p>
                    <p><strong>Heure:</strong> <span id="recap-time">09:30</span></p>
                    <p><strong>Association/Mosquée:</strong> <span id="recap-assoc">Mosquée de la Paix</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Taille:</strong> <span id="recap-size">Grand (~25kg)</span></p>
                    <p><strong>Quantité:</strong> <span id="recap-quantity">1</span></p>
                    <p><strong>Acompte à payer:</strong> <span class="text-primary fw-bold">200,00 €</span></p>
                </div>
            </div>
            <hr>
            
            <!-- Formulaire de paiement Stripe -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="mb-3">Informations de paiement</h5>
                    <form id="payment-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="cardholder-name" class="form-label">Nom sur la carte</label>
                                <input type="text" class="form-control" id="cardholder-name" placeholder="Jean Dupont" required>
                            </div>
                            <div class="col-md-6">
                                <label for="cardholder-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="cardholder-email" placeholder="exemple@email.com" required>
                            </div>
                            <div class="col-12">
                                <label for="card-element" class="form-label">Carte de crédit</label>
                                <div id="card-element" class="form-control p-3" style="height: auto; min-height: 40px;"></div>
                                <div id="card-errors" class="text-danger mt-2" role="alert"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="alert alert-info mt-3">
                <small>
                    <i class="bi bi-info-circle"></i> En confirmant cette réservation, vous vous engagez à verser un acompte de 200€. 
                    Le solde sera à régler le jour de la récupération. Une notification vous sera envoyée par email et SMS avec tous les détails.
                </small>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-grid">
                <button id="submit-payment" class="btn btn-success py-3">
                    Confirmer et payer l'acompte de 200€
                </button>
            </div>
        </div>
    </div>

</div>