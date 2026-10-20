<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Expédition | Prestige Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #1A1A1A; --beige: #FCFAF7; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--beige); }
        .prestige-card { background: white; border-radius: 25px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: none; max-width: 600px; margin: 60px auto; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; border: 1px solid #E0E0E0; background-color: #FBFBFB; }
        .btn-submit { background: var(--dark); color: white; border-radius: 12px; padding: 15px; width: 100%; font-weight: 600; border: none; transition: 0.3s; }
        .btn-submit:hover { background: var(--gold); transform: translateY(-2px); }
    </style>
</head>
<body>

<div class="container">
    <div class="prestige-card">
        <div class="text-center mb-4">
            <div class="display-6 text-warning mb-2"><i class="bi bi-box-seam"></i></div>
            <h2 style="font-family: 'Playfair Display';">Assigner la Livraison</h2>
            <p class="text-muted small">Commande #<?= $commande['id_commande'] ?> — <?= htmlspecialchars($commande['nom'] . ' ' . $commande['prenom']) ?></p>
        </div>

        <form action="index.php?page=valider_livraison" method="POST">
            <input type="hidden" name="id_commande" value="<?= $commande['id_commande'] ?>">

            <div class="mb-4">
                <label class="form-label small fw-bold">ADRESSE DE DESTINATION</label>
                <textarea name="adresse_livraison" class="form-control" rows="3" required><?= htmlspecialchars($commande['adresse'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">LIVREUR RESPONSABLE</label>
                <select name="id_livreur" class="form-select" required>
                    <option value="">Sélectionner un coursier...</option>
                    <?php if(!empty($livreurs)): ?>
                        <?php foreach($livreurs as $l): ?>
                            <option value="<?= $l['id_livreur'] ?>">
                                <?= htmlspecialchars($l['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-truck me-2"></i> CONFIRMER L'EXPÉDITION
            </button>
            
            <div class="text-center mt-3">
                <a href="index.php?page=dashboard" class="text-muted small text-decoration-none">Annuler et retourner</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>