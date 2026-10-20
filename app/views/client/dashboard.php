<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['client'])) { header("Location: index.php?page=client_login"); exit; }

$client = $_SESSION['client'];
$nomClient = htmlspecialchars($client['prenom'] . ' ' . $client['nom']);

// --- AJOUT DE LA CONNEXION POUR LE CALCUL DES MONTANTS ---
try {
    $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête qui calcule le total réel au lieu d'afficher 0
    $stmt = $pdo->prepare("
        SELECT c.*, 
        (SELECT SUM((a.prix_base_nettoyage + s.prix_soin) * d.quantite) 
         FROM details_commande d
         JOIN catalogue_articles a ON d.id_article = a.id_article
         JOIN options_soin s ON d.id_soin = s.id_soin
         WHERE d.id_commande = c.id_commande) as montant_reel
        FROM commande c 
        WHERE c.id_client = ? 
        ORDER BY c.date_commande DESC
    ");
    $stmt->execute([$client['id_client']]);
    $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) { $commandes = []; }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Pressing | Tableau de Bord</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --marron-royal: #5a3a2b;
            --or-prestige: #c9a14a;
            --beige-fond: #fdfaf5;
            --blanc-pur: #ffffff;
            --text-gris: #8d6e63;
        }

        body {
            background-color: var(--beige-fond);
            color: var(--marron-royal);
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            margin: 0;
            overflow-x: hidden;
        }

        .luxury-notify {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-left: 4px solid var(--or-prestige); border-radius: 12px;
            padding: 18px 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            display: flex; align-items: center; gap: 15px;
            animation: slideIn 0.5s ease-out forwards;
            border: 1px solid rgba(201, 161, 74, 0.2);
        }
        .notify-reclamation { border-left-color: #d9534f; } 

        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        .sidebar {
            width: 260px; height: 100vh; position: fixed;
            background: var(--blanc-pur); padding: 40px 20px;
            border-right: 1px solid rgba(0,0,0,0.05);
        }
        .sidebar-brand { font-family: 'Playfair Display'; color: var(--or-prestige); letter-spacing: 3px; font-size: 1.4rem; }
        .nav-link { color: var(--marron-royal); padding: 12px 15px; border-radius: 12px; transition: 0.3s; margin-bottom: 5px; }
        .nav-link i { width: 25px; color: var(--or-prestige); }
        .nav-link:hover, .nav-link.active { background: #fffcf5; color: var(--or-prestige) !important; }

        .main-content { margin-left: 260px; padding: 50px 60px; }
        .luxury-card {
            background: var(--blanc-pur); border-radius: 20px; padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.02); border: none;
            transition: transform 0.3s;
        }
        .luxury-card.clickable:hover { transform: translateY(-5px); cursor: pointer; border: 1px solid var(--or-prestige); }

        .btn-gold {
            background: var(--or-prestige); color: white; border: none;
            padding: 10px 25px; border-radius: 12px; font-weight: 500; transition: 0.3s;
        }
        .btn-gold:hover { background: var(--marron-royal); }

        .badge-status { padding: 6px 14px; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }
        .bg-pending { background: #fff9f0; color: #c9a14a; }
        .bg-paid { background: #f0fdf4; color: #16a34a; }
    </style>
</head>
<body>

<?php if (isset($_GET['success']) && $_GET['success'] == 'order_ok'): ?>
    <div id="toast" class="luxury-notify">
        <i class="fa-solid fa-crown" style="color: var(--or-prestige);"></i>
        <div>
            <span style="font-weight: 600; display: block;">Dépôt confirmé</span>
            <span style="font-size: 0.8rem; color: var(--text-gris);">Vos textiles sont entre de bonnes mains.</span>
        </div>
    </div>
<?php endif; ?>

<aside class="sidebar">
    <div class="mb-5 px-3">
        <h4 class="sidebar-brand">PRESTIGE</h4>
        <small class="text-muted text-uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">Conciergerie</small>
    </div>
    
    <nav class="nav flex-column">
        <small class="text-muted px-3 mb-2 fw-bold text-uppercase" style="font-size: 0.65rem;">Services</small>
        <a class="nav-link active" href="index.php?page=client_dashboard"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a class="nav-link" href="index.php?page=nouvelle_commande"><i class="fa-solid fa-plus-circle"></i> Nouveau dépôt</a>
        <a class="nav-link" href="index.php?page=client_commandes"><i class="fa-solid fa-clock-rotate-left"></i> Historique & Paiement</a>
        
        <small class="text-muted px-3 mt-4 mb-2 fw-bold text-uppercase" style="font-size: 0.65rem;">Compte</small>
        <a class="nav-link" href="index.php?page=client_profil"><i class="fa-solid fa-user-circle"></i> Mon Profil</a>
        <a class="nav-link" href="index.php?page=client_settings"><i class="fa-solid fa-shield-halved"></i> Sécurité</a>
        
        <a class="nav-link text-danger mt-5" href="index.php?page=logout"><i class="fa-solid fa-power-off"></i> Déconnexion</a>
    </nav>
</aside>

<main class="main-content">
    <header class="mb-5 d-flex justify-content-between align-items-end">
        <div>
            <span class="text-muted small">Content de vous revoir,</span>
            <h1 class="h3" style="font-family: 'Playfair Display'; margin: 0;"><?= $nomClient ?></h1>
        </div>
        <div class="text-end">
            <span class="d-block small text-muted">Statut Membre</span>
            <span class="badge" style="background: #fffcf5; color: var(--or-prestige); border: 1px solid var(--or-prestige);">Premium Gold</span>
        </div>
    </header>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="luxury-card h-100">
                <h5 class="mb-4">Mes commandes récentes</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>RÉFÉRENCE</th>
                                <th>STATUT</th>
                                <th>MONTANT</th>
                                <th class="text-end">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($commandes)): foreach($commandes as $cmd): ?>
                            <tr>
                                <td class="fw-bold">#ORD-<?= $cmd['id_commande'] ?></td>
                                <td><span class="badge-status bg-pending"><?= $cmd['statut'] ?></span></td>
                                
                                <td class="fw-bold text-dark"><?= number_format($cmd['montant_reel'] ?? 0, 0, ',', ' ') ?> F</td>
                                
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="index.php?page=commande_details&id=<?= $cmd['id_commande'] ?>" class="btn btn-sm btn-light rounded-pill px-3">
                                            <i class="fa-solid fa-eye me-1"></i> Voir
                                        </a>
                                        
                                        <a href="index.php?page=supprimer_commande&id=<?= $cmd['id_commande'] ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Supprimer ce dépôt ?');"
                                           style="border-radius: 50%; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; padding: 0;">
                                            <i class="fa-solid fa-trash-can" style="font-size: 0.8rem;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted small">Aucune commande active.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="luxury-card mb-4" style="border: 1px dashed var(--or-prestige);">
                <h6>Un souci ?</h6>
                <p class="small text-muted">Signalez-nous toute anomalie sur un vêtement ou un service.</p>
                <a href="index.php?page=reclamations_client" class="btn btn-outline-dark btn-sm w-100">Déposer une réclamation</a>
            </div>

            <div class="luxury-card">
                <h6 class="mb-3">Dernières Notifications</h6>
                <div class="d-flex gap-3 mb-3">
                    <div class="bg-light p-2 rounded-circle"><i class="fa-solid fa-bell text-warning"></i></div>
                    <div class="small">Votre costume est prêt pour le retrait.</div>
                </div>
                <div class="d-flex gap-3">
                    <div class="bg-light p-2 rounded-circle"><i class="fa-solid fa-star text-warning"></i></div>
                    <div class="small">Nouveau service disponible : Soie & Cachemire.</div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    setTimeout(() => {
        const t = document.getElementById('toast');
        if(t) {
            t.style.animation = 'fadeOut 1s forwards';
            setTimeout(() => t.remove(), 1000);
        }
    }, 4000);
</script>
</body>
</html>