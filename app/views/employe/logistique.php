<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Logistique | Prestige</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #1A1A1A; --beige: #FCFAF7; }
        body { background: var(--beige); font-family: 'Plus Jakarta Sans', sans-serif; }
        .logistique-card { background: white; border-radius: 20px; padding: 25px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .badge-status { padding: 6px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .bg-en-cours { background: #E3F2FD; color: #1976D2; }
        .bg-livre { background: #E8F5E9; color: #2E7D32; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="font-family: 'Playfair Display';">Suivi des Expéditions</h2>
        <a href="index.php?page=employe_dashboard" class="btn btn-outline-dark btn-sm rounded-pill px-3">Retour Dashboard</a>
    </div>

    <div class="logistique-card">
        <table class="table align-middle">
            <thead class="table-light">
                <tr class="small text-muted">
                    <th>COMMANDE</th>
                    <th>CLIENT</th>
                    <th>DESTINATION</th>
                    <th>LIVREUR</th>
                    <th>STATUT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($livraisons as $l): ?>
                <tr>
                    <td class="fw-bold">#<?= $l['id_commande'] ?></td>
                    <td><?= htmlspecialchars($l['client_nom'] . ' ' . $l['client_prenom']) ?></td>
                    <td class="small text-muted"><?= htmlspecialchars($l['adresse_livraison']) ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-badge me-2 text-gold"></i>
                            <?= htmlspecialchars($l['livreur_nom']) ?>
                        </div>
                    </td>
                    <td>
                        <span class="badge-status <?= $l['statut'] == 'En cours' ? 'bg-en-cours' : 'bg-livre' ?>">
                            <?= $l['statut'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>