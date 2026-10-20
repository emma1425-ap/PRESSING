<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau Dépôt | Elite Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --prestige-beige: #FCFAF7;
            --prestige-gold: #B4975A;
            --prestige-text: #4A3F35;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--prestige-beige); color: var(--prestige-text); display: flex; min-height: 100vh; margin: 0; }
        .sidebar { width: 280px; background: white; height: 100vh; position: fixed; padding: 40px 25px; border-right: 1px solid rgba(180, 151, 90, 0.1); }
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 60px; }
        .prestige-card { background: white; border-radius: 25px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); border: none; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; border: 1px solid #EEE; background: #FDFDFD; }
        .btn-gold { background: var(--prestige-gold); color: white; border-radius: 12px; padding: 12px 25px; border: none; font-weight: 600; transition: 0.3s; }
        .btn-gold:hover { background: #967d4a; transform: translateY(-2px); }
        .item-row { background: #F8F9FA; border-radius: 15px; padding: 20px; margin-bottom: 15px; position: relative; border-left: 5px solid var(--prestige-gold); }
        .remove-btn { position: absolute; top: 10px; right: 10px; color: #D9534F; cursor: pointer; font-size: 1.2rem; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <div style="font-family: 'Playfair Display'; font-size: 1.8rem; color: var(--prestige-gold); text-transform: uppercase; letter-spacing: 2px;">Prestige</div>
        </div>
        <nav>
            <a href="index.php?page=employe_dashboard" class="nav-link text-dark py-3 d-block text-decoration-none"><i class="bi bi-grid me-2"></i> Dashboard</a>
            <a href="index.php?page=nouveau_depot_employe" class="fw-bold py-3 d-block text-decoration-none" style="color: var(--prestige-gold);"><i class="bi bi-plus-circle me-2"></i> Nouveau dépôt</a>
            <a href="index.php?page=caisse" class="nav-link text-dark py-3 d-block text-decoration-none"><i class="bi bi-receipt me-2"></i> Caisse</a>
        </nav>
    </div>

    <div class="main-content">
        <h1 style="font-family: 'Playfair Display'; margin-bottom: 35px;">Enregistrer un Nouveau Dépôt</h1>

        <form action="index.php?page=enregistrer_depot" method="POST">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="prestige-card">
                        <h5 class="fw-bold mb-4"><i class="bi bi-person me-2"></i>Informations Client</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <label class="small fw-bold mb-2">Sélectionner un client existant</label>
                                <select name="id_client" class="form-select" required>
                                    <option value="">-- Rechercher un client --</option>
                                    <?php foreach($clients as $c): ?>
                                        <option value="<?= $c['id_client'] ?>"><?= htmlspecialchars($c['nom'] . ' ' . $c['prenom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small fw-bold mb-2">Urgence</label>
                                <select name="statut" class="form-select">
                                    <option value="Normal">Normal (48h)</option>
                                    <option value="Express">Express (24h)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="prestige-card">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Articles déposés</h5>
                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" onclick="ajouterArticle()">
                                <i class="bi bi-plus"></i> Ajouter un vêtement
                            </button>
                        </div>

                        <div id="articles-container">
                            <div class="item-row">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="small text-muted">Type d'article</label>
                                        <select name="articles[0][id_article]" class="form-select select-article" onchange="calculerTotal()" required>
                                            <option value="">Choisir...</option>
                                            <?php foreach($articles as $a): ?>
                                                <option value="<?= $a['id_article'] ?>" data-prix="<?= $a['prix_base_nettoyage'] ?>">
                                                    <?= htmlspecialchars($a['nom_article']) ?> (<?= number_format($a['prix_base_nettoyage'], 0, '.', ' ') ?> F)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small text-muted">Soin spécifique</label>
                                        <select name="articles[0][id_soin]" class="form-select select-soin" onchange="calculerTotal()">
                                            <?php foreach($soins as $s): ?>
                                                <option value="<?= $s['id_soin'] ?>" data-prix="<?= $s['prix_soin'] ?>">
                                                    <?= htmlspecialchars($s['categorie_soin'] . ' - ' . $s['nom_soin']) ?> (+<?= number_format($s['prix_soin'], 0, '.', ' ') ?> F)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small text-muted">Quantité</label>
                                        <input type="number" name="articles[0][quantite]" class="form-control input-qte" value="1" min="1" onchange="calculerTotal()">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="prestige-card">
                        <h5 class="fw-bold mb-4">Résumé</h5>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total estimé</span>
                            <span class="fw-bold fs-4" id="total-display" style="color: var(--prestige-gold);">0 F</span>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold mb-2">Note / Observations</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Taches, boutons fragiles..."></textarea>
                        </div>
                        <button type="submit" class="btn-gold w-100 shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> Valider le dépôt
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        let articleIndex = 1;

        function ajouterArticle() {
            const container = document.getElementById('articles-container');
            const newRow = document.createElement('div');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <i class="bi bi-x-circle remove-btn" onclick="this.parentElement.remove(); calculerTotal();"></i>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="small text-muted">Type d'article</label>
                        <select name="articles[${articleIndex}][id_article]" class="form-select select-article" onchange="calculerTotal()" required>
                            <option value="">Choisir...</option>
                            <?php foreach($articles as $a): ?>
                                <option value="<?= $a['id_article'] ?>" data-prix="<?= $a['prix_base_nettoyage'] ?>">
                                    <?= htmlspecialchars($a['nom_article']) ?> (<?= number_format($a['prix_base_nettoyage'], 0, '.', ' ') ?> F)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small text-muted">Soin spécifique</label>
                        <select name="articles[${articleIndex}][id_soin]" class="form-select select-soin" onchange="calculerTotal()">
                            <?php foreach($soins as $s): ?>
                                <option value="<?= $s['id_soin'] ?>" data-prix="<?= $s['prix_soin'] ?>">
                                    <?= htmlspecialchars($s['categorie_soin'] . ' - ' . $s['nom_soin']) ?> (+<?= number_format($s['prix_soin'], 0, '.', ' ') ?> F)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small text-muted">Quantité</label>
                        <input type="number" name="articles[${articleIndex}][quantite]" class="form-control input-qte" value="1" min="1" onchange="calculerTotal()">
                    </div>
                </div>
            `;
            container.appendChild(newRow);
            articleIndex++;
        }

        function calculerTotal() {
            let total = 0;
            const rows = document.querySelectorAll('.item-row');
            
            rows.forEach(row => {
                const artSelect = row.querySelector('.select-article');
                const soinSelect = row.querySelector('.select-soin');
                const qteInput = row.querySelector('.input-qte');

                if (artSelect && artSelect.value) {
                    const prixArt = parseFloat(artSelect.options[artSelect.selectedIndex].dataset.prix) || 0;
                    const prixSoin = parseFloat(soinSelect.options[soinSelect.selectedIndex].dataset.prix) || 0;
                    const qte = parseInt(qteInput.value) || 0;
                    
                    total += (prixArt + prixSoin) * qte;
                }
            });

            document.getElementById('total-display').innerText = total.toLocaleString('fr-FR') + ' F';
        }

        // Initialisation
        window.onload = calculerTotal;
    </script>
</body>
</html>