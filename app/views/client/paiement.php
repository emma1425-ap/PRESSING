<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Sécurisé | Prestige</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #fdfaf5; }
        .pay-card { background: white; border-radius: 20px; padding: 40px; text-align: center; border: 1px solid #eee; }
        .pay-option { border: 2px solid #f5f5f5; border-radius: 15px; padding: 20px; cursor: pointer; transition: 0.3s; }
        .pay-option:hover { border-color: #c9a14a; background: #fffdf9; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="pay-card shadow-lg">
                <h2 class="mb-4" style="font-family: 'Playfair Display';">Règlement de votre facture</h2>
                <p class="text-muted mb-5">Choisissez votre mode de paiement sécurisé</p>
                
                <div class="pay-option mb-3 d-flex align-items-center">
                    <i class="fa-solid fa-credit-card fs-3 text-warning me-3"></i>
                    <span class="fw-bold">Carte Bancaire (Visa / Mastercard)</span>
                </div>
                
                <div class="pay-option mb-3 d-flex align-items-center">
                    <i class="fa-solid fa-mobile-screen-button fs-3 text-primary me-3"></i>
                    <span class="fw-bold">Mobile Money (OM / MoMo)</span>
                </div>

                <div class="pay-option mb-5 d-flex align-items-center">
                    <i class="fa-solid fa-money-bill-transfer fs-3 text-success me-3"></i>
                    <span class="fw-bold">Paiement à la livraison</span>
                </div>

                <button class="btn btn-dark w-100 p-3 rounded-pill fw-bold">Valider le paiement</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>