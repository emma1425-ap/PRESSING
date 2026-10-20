<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if(!isset($_SESSION['client']['id_client'])){
    header("Location: login.php");
    exit;
}

$id_client = $_SESSION['client']['id_client'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête améliorée : 
    // On essaie de prendre prix_unitaire_applique, sinon on calcule via le catalogue
    $sql = "SELECT c.id_commande, c.date_commande, c.statut, 
            SUM(COALESCE(d.prix_unitaire_applique, (a.prix_base_nettoyage + s.prix_soin)) * d.quantite) as montant_total
            FROM commande c
            LEFT JOIN details_commande d ON c.id_commande = d.id_commande
            LEFT JOIN catalogue_articles a ON d.id_article = a.id_article
            LEFT JOIN options_soin s ON d.id_soin = s.id_soin
            WHERE c.id_client = ?
            GROUP BY c.id_commande, c.date_commande, c.statut
            ORDER BY c.date_commande DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_client]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erreur système : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes | Prestige Pressing</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --or-prestige: #c9a14a;
            --marron-royal: #5a3a2b;
            --beige-clair: #fdfaf5;
            --blanc: #ffffff;
        }

        body { 
            background: var(--beige-clair); 
            font-family: 'Poppins', sans-serif; 
            color: var(--marron-royal); 
            font-size: 0.82rem; 
        }

        h1 { font-family: 'Playfair Display', serif; font-weight: 700; color: var(--marron-royal); font-size: 1.6rem; }

        /* Style pour le bouton retour */
        .btn-back {
            color: var(--marron-royal);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-back:hover { color: var(--or-prestige); }

        .main-card { 
            background: var(--blanc); 
            border-radius: 25px; 
            box-shadow: 0 15px 40px rgba(90, 58, 43, 0.05); 
            padding: 25px; 
            border: 1px solid rgba(201, 161, 74, 0.1); 
        }

        .table thead th {
            border: none;
            text-transform: uppercase;
            font-size: 0.65rem; 
            letter-spacing: 1.2px;
            font-weight: 600;
        }

        .status-badge { 
            padding: 4px 12px; 
            border-radius: 50px; 
            font-size: 0.65rem; 
            font-weight: 600; 
            text-transform: uppercase;
        }
        
        .bg-encours { background: #fff4e1; color: #d68100; border: 1px solid #ffe0b2; }
        .bg-pret { background: #e7f7ed; color: #218838; border: 1px solid #c3e6cb; }

        .btn-gold { 
            background: var(--or-prestige); 
            color: white; 
            border-radius: 10px; 
            font-weight: 600; 
            border: none; 
            padding: 7px 18px; 
            font-size: 0.75rem;
        }

        .btn-action {
            width: 32px; height: 32px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: 1px solid #eee; background: white; color: var(--marron-royal);
            text-decoration: none;
        }

        .price-tag { font-weight: 700; color: var(--marron-royal); font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="mb-3">
        <a href="index.php?page=client_dashboard" class="btn-back">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour au Dashboard
        </a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="text-muted small text-uppercase" style="letter-spacing: 1px;">Espace Prestige</span>
            <h1>Historique & Paiements</h1>
        </div>
        <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#modalCommande">
            <i class="fa-solid fa-plus me-1"></i> Nouveau Dépôt
        </button>
    </div>

    <div class="main-card">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-muted">
                        <th>Référence</th>
                        <th>Date de dépôt</th>
                        <th>Statut</th>
                        <th>Montant Total</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($commandes)): ?>
                        <?php foreach($commandes as $cmd): ?>
                        <tr>
                            <td class="fw-bold">#ORD-<?= str_pad($cmd['id_commande'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <i class="fa-regular fa-calendar me-2 text-muted" style="font-size: 0.7rem;"></i>
                                <?= date('d/m/Y', strtotime($cmd['date_commande'])) ?>
                            </td>
                            <td>
                                <?php 
                                    $s = strtolower($cmd['statut'] ?? 'en attente');
                                    $class = ($s == 'prêt' || $s == 'livré') ? 'bg-pret' : 'bg-encours';
                                ?>
                                <span class="status-badge <?= $class ?>">
                                    <?= htmlspecialchars($cmd['statut'] ?? 'En attente') ?>
                                </span>
                            </td>
                            <td class="price-tag">
                                <?php 
                                    $total = (float)$cmd['montant_total'];
                                    // Si le total est 0, on affiche "En attente", sinon le montant
                                    echo ($total > 0) ? number_format($total, 0, ',', ' ') . " F" : "À définir"; 
                                ?>
                            </td>
                            <td class="text-end">
                                <a href="index.php?page=commande_details&id=<?= $cmd['id_commande'] ?>" class="btn-action">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Aucun historique.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>