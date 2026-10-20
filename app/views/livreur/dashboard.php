<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite - Gestion Livraisons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --elite-cream: #fcf3e8;
            --elite-brown: #5d4037;
            --elite-gold: #bfa36d;
            --elite-dark: #4e342e;
        }

        body { 
            background-color: var(--elite-cream); 
            color: var(--elite-brown); 
            font-family: 'Montserrat', sans-serif; 
        }

        /* Navbar Elite */
        .navbar-elite { 
            background: white; 
            border-bottom: 2px solid var(--elite-gold); 
            padding: 15px 0; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .brand-text {
            letter-spacing: 4px; 
            color: var(--elite-gold); 
            font-weight: bold;
            font-family: 'Playfair Display', serif;
            text-decoration: none;
        }

        .nav-link-elite {
            color: var(--elite-brown);
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-decoration: none;
            transition: 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-link-elite:hover {
            color: var(--elite-gold);
        }

        /* Cartes de mission */
        .mission-card { 
            background: white; 
            border-radius: 20px; 
            border: none; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            margin-bottom: 20px; 
            transition: transform 0.2s;
        }

        .mission-card:hover { transform: translateY(-3px); }

        .btn-elite { 
            background: var(--elite-brown); 
            color: var(--elite-cream); 
            border-radius: 12px; 
            border: none; 
            padding: 12px 20px; 
            font-weight: 600; 
            width: 100%; 
            transition: 0.3s;
        }

        .btn-elite:hover { 
            background: var(--elite-dark); 
            color: white; 
        }

        .badge-status { 
            background: #e0d4c3; 
            color: var(--elite-brown); 
            padding: 6px 14px; 
            border-radius: 8px; 
            font-size: 0.75rem; 
            font-weight: bold; 
            text-transform: uppercase;
        }

        /* Style pour le champ réclamation */
        .reclamation-box {
            display: none; /* Masqué par défaut */
            margin-top: 10px;
        }

        .reclamation-box textarea {
            border: 1px solid var(--elite-gold);
            border-radius: 10px;
            font-size: 0.85rem;
            background-color: #fffaf5;
        }
    </style>
</head>
<body>

<nav class="navbar-elite mb-5">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="index.php?page=livreur_dashboard" class="brand-text">ELITE LOGISTIQUE</a>
        
        <div class="d-flex align-items-center gap-4">
            <a href="index.php?page=messages_livreur" class="nav-link me-3 fw-bold text-uppercase" style="font-size: 0.85rem; color: var(--dark);">
                <i class="bi bi-chat-left-dots me-1"></i> Messages
            </a>
            <a href="index.php?page=profil_livreur" class="nav-link-elite">MON PROFIL</a>
            <a href="index.php?page=logout" class="nav-link-elite" title="Déconnexion">
                <span class="d-none d-md-inline">DÉCONNEXION</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="ms-2" viewBox="0 0 16 16">
                    <path d="M7.5 1v7h1V1h-1z"/>
                    <path d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z"/>
                </svg>
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="font-family: 'Playfair Display', serif;">Missions de Livraison</h2>
            <p class="text-muted small">Bienvenue, <?= htmlspecialchars($_SESSION['nom'] ?? 'Expert') ?>. Voici vos courses assignées.</p>
        </div>
    </div>

    <?php if (empty($missions)): ?>
        <div class="bg-white p-5 rounded-4 text-center shadow-sm">
            <p class="text-muted">Aucune livraison n'est assignée à votre cockpit pour le moment.</p>
        </div>
    <?php else: ?>
        <?php foreach ($missions as $m): ?>
            <div class="mission-card p-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0" style="color: var(--elite-dark);">
                                <?= htmlspecialchars($m['client_prenom'] . ' ' . $m['client_nom']) ?>
                            </h5>
                            <span class="badge-status"><?= htmlspecialchars($m['statut']) ?></span>
                        </div>
                        <p class="small text-muted mb-1">📍 <?= htmlspecialchars($m['adresse_livraison']) ?></p>
                        <p class="small mb-0"><strong>📅 Prévue le :</strong> <?= date('d/m/Y', strtotime($m['date_livraison'])) ?></p>
                        <p class="small text-uppercase mt-2" style="font-size: 0.7rem; color: var(--elite-gold); letter-spacing: 1px;">
                            Réf Commande : #<?= $m['id_commande'] ?>
                        </p>
                    </div>
                    
                    <div class="col-md-5 border-start ps-4">
                        <form action="index.php?page=update_statut_livraison" method="POST">
                            <input type="hidden" name="id_livraison" value="<?= $m['id_livraison'] ?>">
                            <input type="hidden" name="id_commande" value="<?= $m['id_commande'] ?>">
                            
                            <label class="small fw-bold mb-1" style="font-size: 0.75rem;">METTRE À JOUR L'ÉTAT</label>
                            
                            <select name="statut" class="form-select mb-3 shadow-sm statut-selector" style="border-radius: 10px;">
                                <option value="En cours" <?= $m['statut'] == 'En cours' ? 'selected' : '' ?>>🚗 En cours de route</option>
                                <option value="Livrée">✅ Livrée au client</option>
                                <option value="Problème">⚠️ Problème rencontré</option>
                            </select>

                            <div class="reclamation-box mb-3">
                                <label class="small fw-bold mb-1" style="font-size: 0.7rem; color: #d9534f;">DÉTAILS DU PROBLÈME</label>
                                <textarea name="description_probleme" class="form-control" rows="2" placeholder="Expliquez la situation à l'employé..."></textarea>
                            </div>

                            <button type="submit" class="btn-elite shadow-sm">CONFIRMER</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
    // Script pour afficher/masquer le formulaire de problème
    document.querySelectorAll('.statut-selector').forEach(select => {
        select.addEventListener('change', function() {
            // On cherche la boîte de réclamation dans le même parent (le formulaire)
            const reclamationBox = this.closest('form').querySelector('.reclamation-box');
            
            if (this.value === 'Problème') {
                reclamationBox.style.display = 'block';
                reclamationBox.querySelector('textarea').setAttribute('required', 'required');
            } else {
                reclamationBox.style.display = 'none';
                reclamationBox.querySelector('textarea').removeAttribute('required');
            }
        });
    });
</script>

</body>
</html>