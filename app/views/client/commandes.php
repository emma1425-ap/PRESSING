<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes | Prestige Pressing</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #fdfaf5; font-family: 'Poppins', sans-serif; color: #5a3a2b; }
        .main-card { background: white; border-radius: 25px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); padding: 30px; border: none; }
        .btn-gold { background: #c9a14a; color: white; border-radius: 12px; font-weight: 600; border: none; padding: 10px 25px; }
        .status-badge { padding: 5px 15px; border-radius: 50px; font-size: 0.8rem; font-weight: 600; }
        .bg-encours { background: #fff3e0; color: #ef6c00; }
        .bg-pret { background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h1 style="font-family: 'Playfair Display';">Historique de mes soins</h1>
        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#modalCommande">
            <i class="fa-solid fa-plus me-2"></i> Nouvelle Commande
        </button>
    </div>

    <div class="main-card">
        <table class="table align-middle">
            <thead>
                <tr class="text-muted">
                    <th>Référence</th>
                    <th>Date</th>
                    <th>Articles</th>
                    <th>Statut</th>
                    <th>Total</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($commandes as $cmd): ?>
                <tr>
                    <td class="fw-bold">#<?= str_pad($cmd['id_commande'], 4, '0', STR_PAD_LEFT) ?></td>
                    <td><?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></td>
                    <td>Soin Premium</td>
                    <td>
                        <span class="status-badge <?= $cmd['statut'] == 'en cours' ? 'bg-encours' : 'bg-pret' ?>">
                            <?= ucfirst($cmd['statut']) ?>
                        </span>
                    </td>
                    <td class="fw-bold"><?= number_format($cmd['montant_total'], 0, ',', ' ') ?> F</td>
                    <td class="text-end">
                        <a href="index.php?page=commande_edit&id=<?= $cmd['id_commande'] ?>" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="fa-solid fa-pen"></i></a>
                        <a href="index.php?page=commande_delete&id=<?= $cmd['id_commande'] ?>" class="btn btn-sm btn-outline-danger rounded-circle ms-2" onclick="return confirm('Annuler cette commande ?')"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalCommande" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <h3 class="mb-4" style="font-family: 'Playfair Display';">Nouveau Dépôt</h3>
                <form action="index.php?page=save_commande" method="POST">
                    <select class="form-select mb-3 p-3" name="service" style="border-radius: 12px;">
                        <option>Nettoyage à sec</option>
                        <option>Lavage & Repassage</option>
                        <option>Repassage uniquement</option>
                    </select>
                    <textarea class="form-control mb-3 p-3" name="notes" placeholder="Détails (ex: 3 chemises, 1 veste...)" style="border-radius: 12px;"></textarea>
                    <button type="submit" class="btn btn-gold w-100">Confirmer le dépôt</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>