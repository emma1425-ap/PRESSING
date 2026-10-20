<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Factures | Prestige</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #fdfaf5; font-family: 'Poppins', sans-serif; }
        .facture-item { background: white; border-radius: 15px; border-left: 5px solid #c9a14a; transition: 0.3s; }
        .facture-item:hover { transform: translateX(10px); }
    </style>
</head>
<body>
<div class="container py-5">
    <h1 class="mb-5" style="font-family: 'Playfair Display'; color: #5a3a2b;">Mes Factures</h1>
    
    <div class="row g-3">
        <?php foreach($factures as $f): ?>
        <div class="col-12">
            <div class="facture-item p-4 d-flex justify-content-between align-items-center shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-file-invoice-dollar fs-2 text-warning me-4"></i>
                    <div>
                        <h5 class="mb-0">Facture #<?= $f['id_facture'] ?></h5>
                        <small class="text-muted">Commande #<?= $f['id_commande'] ?></small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="h5 fw-bold mb-0"><?= number_format($f['montant'], 0, ',', ' ') ?> FCFA</div>
                    <a href="#" class="btn btn-sm btn-link text-warning text-decoration-none p-0">Télécharger PDF</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>