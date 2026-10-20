<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion de Caisse | Prestige</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #1A1A1A; --beige: #FCFAF7; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--beige); color: #444; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; border-right: 1px solid #EEE; padding: 40px 20px; }
        .main-content { margin-left: 280px; padding: 60px; }
        .stat-card { background: white; border-radius: 20px; padding: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
        .bg-gold-light { background: #FFF9F0; color: var(--gold); }
        .table-prestige { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2 style="font-family: 'Playfair Display'; color: var(--gold);">PRESTIGE</h2>
    <nav class="nav flex-column mt-5">
        <a href="index.php?page=employe_dashboard" class="nav-link text-dark mb-3"><i class="bi bi-grid me-2"></i> Dashboard</a>
        <a href="index.php?page=caisse" class="nav-link text-gold fw-bold mb-3"><i class="bi bi-wallet2 me-2"></i> Caisse</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 style="font-family: 'Playfair Display';">Journal de Caisse</h1>
            <p class="text-muted">Recettes du <?= date('d/m/Y') ?></p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-dark rounded-pill px-4">
            <i class="bi bi-printer me-2"></i> Imprimer le rapport
        </button>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-gold-light"><i class="bi bi-cash-stack fs-4"></i></div>
                <div class="text-muted small fw-bold text-uppercase">Espèces</div>
                <h3 class="fw-bold mt-1"><?= number_format($totaux['especes'] ?? 0, 0, ',', ' ') ?> F</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="icon-box bg-primary-subtle text-primary"><i class="bi bi-phone fs-4"></i></div>
                <div class="text-muted small fw-bold text-uppercase">Mobile Money</div>
                <h3 class="fw-bold mt-1"><?= number_format($totaux['mobile_money'] ?? 0, 0, ',', ' ') ?> F</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: var(--dark); color: white;">
                <div class="icon-box" style="background: rgba(255,255,255,0.1);"><i class="bi bi-bank fs-4"></i></div>
                <div class="text-white-50 small fw-bold text-uppercase">Total Journée</div>
                <h3 class="fw-bold mt-1 text-white"><?= number_format(array_sum($totaux), 0, ',', ' ') ?> F</h3>
            </div>
        </div>
    </div>

    <div class="table-prestige">
        <div class="p-4 border-bottom bg-white">
            <h5 class="mb-0 fw-bold">Dernières Transactions</h5>
        </div>
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Heure</th>
                    <th>Client</th>
                    <th>Commande</th>
                    <th>Mode</th>
                    <th class="text-end pe-4">Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($paiements)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">Aucun encaissement aujourd'hui.</td></tr>
                <?php else: ?>
                    <?php foreach($paiements as $p): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= date('H:i', strtotime($p['date_paiement'])) ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></td>
                        <td>#<?= $p['id_commande'] ?></td>
                        <td>
                            <span class="badge rounded-pill <?= $p['mode_paiement'] == 'especes' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info' ?>">
                                <?= ucfirst(str_replace('_', ' ', $p['mode_paiement'])) ?>
                            </span>
                        </td>
                        <td class="text-end pe-4 fw-bold"><?= number_format($p['montant'], 0, ',', ' ') ?> F</td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>