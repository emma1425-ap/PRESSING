<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturation Prestige | Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #1A1A1A; --beige: #FCFAF7; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--beige); color: #444; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; border-right: 1px solid #EEE; padding: 40px 20px; }
        .main-content { margin-left: 280px; padding: 60px; }
        .prestige-card { background: white; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: none; }
        .total-banner { background: var(--dark); color: white; border-radius: 15px; padding: 25px; margin-top: 20px; }
        .method-card { border: 2px solid #F0F0F0; border-radius: 12px; padding: 15px; text-align: center; cursor: pointer; transition: 0.3s; display: block; }
        .method-check:checked + .method-card { border-color: var(--gold); background: #FFF9F0; color: var(--gold); }
        .method-check { display: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2 style="font-family: 'Playfair Display'; color: var(--gold);">PRESTIGE</h2>
    <nav class="nav flex-column mt-5">
        <a href="index.php?page=dashboard" class="nav-link text-dark mb-3"><i class="bi bi-grid me-2"></i> Dashboard</a>
        <a href="#" class="nav-link text-dark active fw-bold"><i class="bi bi-receipt me-2"></i> Facturation</a>
    </nav>
</div>

<div class="main-content">
    <h1 style="font-family: 'Playfair Display';" class="mb-4">Encaissement Client</h1>
    
    <div class="row g-4">
        <div class="col-md-8">
            <div class="prestige-card">
                <div class="d-flex justify-content-between mb-4">
                    <h5 class="fw-bold">Commande #<?= $commande['id_commande'] ?></h5>
                    <span class="badge bg-light text-dark p-2 px-3 rounded-pill border">
                        Client: <?= htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']) ?>
                    </span>
                </div>
                
                <table class="table">
                    <thead>
                        <tr class="text-muted small">
                            <th>ARTICLE & SOIN</th>
                            <th class="text-center">QTÉ</th>
                            <th class="text-end">SOUS-TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalGlobal = 0;
                        foreach($items as $item): 
                            $prixUnit = $item['prix_base_nettoyage'] + $item['prix_soin'];
                            $sousTotal = $prixUnit * $item['quantite'];
                            $totalGlobal += $sousTotal;
                        ?>
                        <tr class="align-middle">
                            <td>
                                <div class="fw-bold"><?= $item['nom_article'] ?></div>
                                <div class="text-muted small"><?= $item['nom_soin'] ?></div>
                            </td>
                            <td class="text-center"><?= $item['quantite'] ?></td>
                            <td class="text-end fw-bold"><?= number_format($sousTotal, 0, ',', ' ') ?> F</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="total-banner d-flex justify-content-between align-items-center">
                    <div class="small text-uppercase opacity-75 fw-bold" style="letter-spacing: 1px;">Montant Total</div>
                    <div class="h2 mb-0 fw-bold"><?= number_format($totalGlobal, 0, ',', ' ') ?> CFA</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="prestige-card">
                <h5 class="fw-bold mb-4">Mode de Règlement</h5>
                <form action="index.php?page=valider_paiement" method="POST">
                    <input type="hidden" name="id_commande" value="<?= $commande['id_commande'] ?>">
                    <input type="hidden" name="montant" value="<?= $totalGlobal ?>">

                    <div class="mb-3">
                        <input type="radio" name="methode" value="Espèces" id="c1" class="method-check" checked>
                        <label for="c1" class="method-card">
                            <i class="bi bi-cash-stack fs-3 mb-2 d-block"></i>
                            <span class="fw-bold">Espèces</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <input type="radio" name="methode" value="Mobile Money" id="c2" class="method-check">
                        <label for="c2" class="method-card">
                            <i class="bi bi-phone fs-3 mb-2 d-block"></i>
                            <span class="fw-bold">Mobile Money</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-bold shadow">
                        VALIDER L'ENCAISSEMENT
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>