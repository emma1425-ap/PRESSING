<?php
// 1. Connexion à ta base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. On récupère les articles et on les classe par catégorie
    $sql = "SELECT a.*, c.nom_categorie 
            FROM catalogue_articles a 
            JOIN categories_vetement c ON a.id_categorie = c.id_categorie 
            ORDER BY c.nom_categorie, a.nom_article";
    
    $stmt = $pdo->query($sql);
    $all_articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $articles_par_cat = [];
    foreach ($all_articles as $article) {
        $articles_par_cat[$article['nom_categorie']][] = $article;
    }

    // 3. On récupère les types de soins
    $stmtSoins = $pdo->query("SELECT * FROM options_soin");
    $soins = $stmtSoins->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Dépôt | Prestige Pressing</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --marron-royal: #422d25;
            --or-prestige: #c9a14a;
            --beige-fond: #f8f4f0;
            --blanc-pur: #ffffff;
        }

        body {
            background-color: var(--beige-fond);
            color: var(--marron-royal);
            font-family: 'Poppins', sans-serif;
        }

        .luxury-card {
            background: var(--blanc-pur);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 45px rgba(66, 45, 37, 0.08);
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            color: var(--or-prestige);
            margin-bottom: 8px;
            display: block;
        }

        .item-row {
            background: #fdfaf7;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(201, 161, 74, 0.1);
            position: relative;
            transition: 0.3s;
        }

        .item-row:hover {
            border-color: var(--or-prestige);
        }

        .form-select, .form-control {
            border: 1px solid #eeebe9;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.9rem;
        }

        .btn-add {
            color: var(--marron-royal);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
        }

        .btn-add:hover { color: var(--or-prestige); }

        .total-banner {
            background: var(--marron-royal);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
        }

        .price-display {
            font-family: 'Playfair Display';
            font-size: 2rem;
            font-weight: 700;
        }

        .btn-submit {
            background: var(--or-prestige);
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #b08d3e;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="container mt-4">
        <a href="index.php?page=client_dashboard" class="text-decoration-none" style="color: var(--marron-royal); font-weight: 500; transition: 0.3s;">
            <i class="fa-solid fa-arrow-left-long me-2" style="color: var(--or-prestige);"></i> 
            Retour à l'accueil
        </a>
   </div> <br> <br>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="luxury-card">
                <h2 class="mb-4" style="font-family: 'Playfair Display'; font-weight: 700;">Confier vos textiles</h2>
                <p class="text-muted mb-5">Sélectionnez vos articles pour un soin sur-mesure par nos artisans.</p>

                <form action="index.php?page=traitement_commande" method="POST" id="orderForm">
                    <div id="items-container">
                        <div class="item-row">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="info-label">Vêtement / Article</label>
                                    <select name="articles[]" class="form-select article-select" required>
                                        <option value="" data-price="0">-- Choisir un article --</option>
                                        <?php if(!empty($articles_par_cat)): ?>
                                            <?php foreach($articles_par_cat as $cat => $items): ?>
                                                <optgroup label="<?= htmlspecialchars($cat) ?>">
                                                    <?php foreach($items as $item): ?>
                                                        <option value="<?= $item['id_article'] ?>" data-price="<?= $item['prix_base_nettoyage'] ?>">
                                                            <?= htmlspecialchars($item['nom_article']) ?> (<?= number_format($item['prix_base_nettoyage'], 0, '', ' ') ?> F)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="info-label">Soin Signature</label>
                                    <select name="soins[]" class="form-select soin-select">
                                        <?php if(!empty($soins)): ?>
                                            <?php foreach($soins as $s): ?>
                                                <option value="<?= $s['id_soin'] ?>" data-extra="<?= $s['prix_soin'] ?>">
                                                    <?= htmlspecialchars($s['nom_soin']) ?> (+ <?= number_format($s['prix_soin'], 0, '', ' ') ?> F)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="info-label">Quantité</label>
                                    <input type="number" name="qtys[]" class="form-control qty-input" value="1" min="1">
                                </div>
                                <div class="col-md-1 d-flex align-items-end justify-content-center">
                                    <button type="button" class="btn btn-link text-danger remove-item p-0 mb-2" title="Supprimer cet article">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-item" class="btn-add mt-2 border-0 bg-transparent">
                        <i class="fa-solid fa-circle-plus"></i> Ajouter une autre pièce
                    </button>

                    <div class="total-banner">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h5 class="mb-1" style="font-family: 'Playfair Display';">Estimation du Devis</h5>
                                <p class="small opacity-75 mb-0 text-white">Le tarif final sera validé après expertise lors de la réception.</p>
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <div class="price-display mb-3">
                                    <span id="total-amount">0</span> <small style="font-size: 1rem;">FCFA</small>
                                </div>
                                <button type="submit" class="btn-submit">Confirmer mon dépôt</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    const totalDisplay = document.getElementById('total-amount');

    // Fonction de calcul globale
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const articleSelect = row.querySelector('.article-select');
            const soinSelect = row.querySelector('.soin-select');
            const qtyInput = row.querySelector('.qty-input');

            // Récupération sécurisée des prix
            const artPrice = parseFloat(articleSelect.selectedOptions[0]?.dataset.price || 0);
            const soinExtra = parseFloat(soinSelect.selectedOptions[0]?.dataset.extra || 0);
            const qty = parseInt(qtyInput.value || 1);
            
            total += (artPrice + soinExtra) * qty;
        });
        totalDisplay.innerText = total.toLocaleString('fr-FR');
    }

    // Ajouter une pièce
    addButton.addEventListener('click', () => {
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        
        // Réinitialisation des valeurs
        newRow.querySelector('.qty-input').value = 1;
        newRow.querySelector('.article-select').selectedIndex = 0;
        
        container.appendChild(newRow);
        calculateTotal();
    });

    // Supprimer une pièce (Délégation d'événement)
    container.addEventListener('click', (e) => {
        if(e.target.closest('.remove-item')) {
            const rows = document.querySelectorAll('.item-row');
            if(rows.length > 1) {
                e.target.closest('.item-row').remove();
                calculateTotal();
            } else {
                alert("Vous devez confier au moins un article.");
            }
        }
    });

    // Écouteurs pour le calcul en temps réel
    container.addEventListener('change', (e) => {
        if (e.target.classList.contains('article-select') || e.target.classList.contains('soin-select')) {
            calculateTotal();
        }
    });

    container.addEventListener('input', (e) => {
        if (e.target.classList.contains('qty-input')) {
            calculateTotal();
        }
    });

    // Initialisation au chargement
    calculateTotal();
});
</script>

</body>
</html>