<?php
// 1. Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Récupération de l'ID depuis l'URL
    $id_commande = $_GET['id'] ?? null;

    if ($id_commande) {
        // 3. Récupération des infos générales de la commande
        $stmt = $pdo->prepare("SELECT * FROM commande WHERE id_commande = ?");
        $stmt->execute([$id_commande]);
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si la commande n'existe pas en base
        if (!$commande) {
            die("<div class='container py-5'><div class='alert alert-danger'>Commande #$id_commande introuvable.</div></div>");
        }

        // 4. Récupération des articles (JOIN) + Calcul automatique du sous-total
        $stmtItems = $pdo->prepare("
            SELECT 
                d.quantite,
                a.nom_article, 
                s.nom_soin,
                ((a.prix_base_nettoyage + s.prix_soin) * d.quantite) as sous_total
            FROM details_commande d
            JOIN catalogue_articles a ON d.id_article = a.id_article
            JOIN options_soin s ON d.id_soin = s.id_soin
            WHERE d.id_commande = ?
        ");
        $stmtItems->execute([$id_commande]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    } else {
        header("Location: index.php?page=client_dashboard");
        exit;
    }
} catch (Exception $e) {
    die("Erreur système : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Dépôt | Prestige Pressing</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --marron-royal: #422d25;
            --marron-clair: #5a3a2b;
            --or-prestige: #c9a14a;
            --beige-fond: #f8f4f0;
            --blanc-pur: #ffffff;
        }

        body {
            background-color: var(--beige-fond);
            color: var(--marron-royal);
            font-family: 'Poppins', sans-serif;
            font-size: 0.9rem;
        }

        .luxury-card {
            background: var(--blanc-pur);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 45px rgba(66, 45, 37, 0.08);
            border: 1px solid rgba(66, 45, 37, 0.05);
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            color: var(--or-prestige);
        }

        .item-list-header {
            border-bottom: 2px solid var(--marron-royal);
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .item-row {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
            transition: 0.3s;
        }

        .price-font {
            font-family: 'Playfair Display';
            font-weight: 700;
            color: var(--marron-royal);
        }

        .badge-soin {
            background: #fdfaf7;
            border: 1px solid var(--or-prestige);
            color: var(--marron-clair);
            padding: 2px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
        }

        .status-pill {
            background: var(--marron-royal);
            color: white;
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <a href="index.php?page=client_dashboard" class="text-decoration-none mb-4 d-inline-block" style="color: var(--marron-royal);">
        <i class="fa-solid fa-chevron-left me-2"></i> Retour à mes commandes
    </a>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="luxury-card">
                <div class="d-flex justify-content-between align-items-start mb-5">
                    <div>
                        <span class="info-label">Récapitulatif de soin</span>
                        <h2 style="font-family: 'Playfair Display'; font-weight: 700;">Commande #ORD-<?= htmlspecialchars($commande['id_commande']) ?></h2>
                        <p class="text-muted mb-0">Déposé le <?= date('d/m/Y', strtotime($commande['date_commande'])) ?></p>
                    </div>
                    <div class="text-end">
                        <span class="status-pill mb-2"><?= strtoupper($commande['statut']) ?></span>
                        
                        <?php if ($commande['statut'] == 'En attente'): ?>
                            <br>
                            <a href="index.php?page=supprimer_commande&id=<?= $commande['id_commande'] ?>" 
                               class="btn btn-sm btn-outline-danger mt-2" 
                               style="font-size: 0.7rem; border-radius: 8px;"
                               onclick="return confirm('Voulez-vous vraiment annuler ce dépôt ?');">
                                <i class="fa-solid fa-trash-can me-1"></i> ANNULER LA COMMANDE
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row item-list-header text-muted">
                    <div class="col-6">Article & Service</div>
                    <div class="col-2 text-center">Qté</div>
                    <div class="col-4 text-end">Total</div>
                </div>

                <?php 
                $grandTotal = 0;
                if (!empty($items)): 
                    foreach($items as $item): 
                        $grandTotal += $item['sous_total'];
                ?>
                <div class="row item-row align-items-center">
                    <div class="col-6">
                        <div class="fw-bold" style="color: var(--marron-royal);"><?= htmlspecialchars($item['nom_article']) ?></div>
                        <div class="mt-1">
                            <span class="badge-soin">
                                <i class="fa-solid fa-sparkles me-1" style="font-size: 0.6rem;"></i>
                                <?= htmlspecialchars($item['nom_soin']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-2 text-center fw-bold">
                        × <?= $item['quantite'] ?>
                    </div>
                    <div class="col-4 text-end price-font">
                        <?= number_format($item['sous_total'], 0, ',', ' ') ?> F
                    </div>
                </div>
                <?php 
                    endforeach; 
                else:
                    echo "<p class='text-center py-4'>Aucun article trouvé.</p>";
                endif; 
                ?>

                <div class="mt-5 p-4 rounded-4" style="background: #fdfaf7; border: 1px dashed var(--or-prestige);">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0 fw-bold text-uppercase" style="letter-spacing: 1px;">Montant Total</span>
                        <span class="h2 mb-0 price-font" style="color: var(--marron-royal);">
                            <?= number_format($grandTotal, 0, ',', ' ') ?> FCFA
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="luxury-card mb-4" style="background: var(--marron-royal); color: white;">
                <h5 class="mb-4" style="font-family: 'Playfair Display';">Suivi Logistique</h5>
                <div class="d-flex mb-3">
                    <div class="me-3"><i class="fa-solid fa-truck-fast text-warning"></i></div>
                    <div>
                        <small class="d-block opacity-50 text-uppercase" style="font-size: 0.6rem;">Délai estimé</small>
                        <span class="fw-bold">48h à 72h ouvrables</span>
                    </div>
                </div>
                <hr class="opacity-25">
                <button class="btn w-100 mt-2" onclick="window.print()" style="background: var(--or-prestige); color: white; font-weight: 600; border-radius: 10px;">
                    <i class="fa-solid fa-print me-2"></i> Imprimer le Bon
                </button>
            </div>

            <div class="luxury-card">
                <span class="info-label">Assistance</span>
                <p class="small text-muted mt-2">Une question sur ce dépôt ? Nos majordomes sont à votre disposition.</p>
                <a href="index.php?page=client_reclamation" class="text-decoration-none fw-bold" style="color: var(--marron-royal);">
                    <i class="fa-solid fa-headset me-2"></i> Service Conciergerie
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>