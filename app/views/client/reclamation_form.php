<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #5a3a2b; --beige: #fdfaf5; --light-gray: #E9E9EB; }
        body { background-color: var(--beige); font-family: 'Poppins', sans-serif; }
        .luxury-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Conteneur du chat */
        .chat-box { background: #f9f9f9; border-radius: 15px; padding: 15px; max-height: 400px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
        
        .bubble { max-width: 80%; padding: 12px 16px; border-radius: 18px; font-size: 0.95rem; position: relative; line-height: 1.4; }

        /* MES MESSAGES (Client) -> À DROITE, COULEUR DORÉE */
        .bubble-client { 
            align-self: flex-end; 
            background: var(--gold); 
            color: white; 
            border-bottom-right-radius: 2px; 
        }

        /* MESSAGES DE L'EMPLOYÉ -> À GAUCHE, COULEUR GRISE */
        .bubble-employe { 
            align-self: flex-start; 
            background: var(--light-gray); 
            color: black; 
            border-bottom-left-radius: 2px; 
        }

        .time-stamp { font-size: 0.65rem; margin-top: 5px; opacity: 0.8; text-align: right; }
        .btn-gold { background: var(--gold); color: white; border: none; font-weight: bold; transition: 0.3s; }
        .btn-gold:hover { background: var(--dark); color: white; }
        .form-control:focus, .form-select:focus { border-color: var(--gold); box-shadow: 0 0 0 0.2rem rgba(180, 151, 90, 0.25); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row g-5">
        
        <div class="col-lg-8">
            <h4 class="mb-4" style="color: var(--dark); font-weight: bold;">Vos discussions</h4>
            
            <?php if (empty($reclamations)): ?>
                <div class="luxury-card text-center">Vous n'avez pas encore de réclamations.</div>
            <?php else: ?>
                <?php foreach ($reclamations as $r): ?>
                    <div class="luxury-card mb-4">
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <h6 class="fw-bold m-0">Commande #<?= $r['id_commande'] ?> - <small class="text-muted"><?= str_replace('_', ' ', $r['type_reclamation']) ?></small></h6>
                            <span class="badge rounded-pill <?= $r['statut'] == 'resolu' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                <?= ucfirst($r['statut']) ?>
                            </span>
                        </div>

                        <div class="chat-box mb-3">
                            <?php if (stripos($r['description'], 'LIVREUR') === false): ?>
                                <div class="bubble bubble-client">
                                    <small class="d-block fw-bold" style="font-size: 0.7rem; margin-bottom: 3px; opacity: 0.9;">MA DEMANDE INITIALE</small>
                                    <?= nl2br(htmlspecialchars($r['description'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php foreach ($all_messages as $m): ?>
                                <?php if ($m['id_reclamation'] == $r['id_reclamation']): ?>
                                    <div class="bubble <?= $m['role'] == 'client' ? 'bubble-client' : 'bubble-employe' ?>">
                                        <?= nl2br(htmlspecialchars($m['message'])) ?>
                                        <div class="time-stamp">
                                            <?= date('H:i', strtotime($m['date_envoi'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($r['statut'] != 'resolu'): ?>
                        <form action="index.php?page=repondre_reclamation_client" method="POST">
                            <input type="hidden" name="id_reclamation" value="<?= $r['id_reclamation'] ?>">
                            <div class="input-group">
                                <input type="text" name="message" class="form-control border-0 bg-light" placeholder="Écrire votre réponse..." required>
                                <button class="btn btn-gold" type="submit"><i class="bi bi-send"></i></button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <a href="index.php?page=client_dashboard" class="text-decoration-none" style="color: var(--marron-royal); font-weight: 500; transition: 0.3s;">
                <i class="fa-solid fa-arrow-left-long me-2" style="color: var(--or-prestige);"></i> 
                Retour à l'accueil
            </a>
            <h4 class="mb-4" style="color: var(--dark); font-weight: bold;">Déclarer un souci</h4>
            <div class="luxury-card" style="border-top: 5px solid var(--gold);">
                <form action="index.php?page=creer_reclamation" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">COMMANDE CONCERNÉE</label>
                        <select name="id_commande" class="form-select bg-light border-0" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($mes_commandes as $cmd): ?>
                                <option value="<?= $cmd['id_commande'] ?>">N° <?= $cmd['id_commande'] ?> (<?= date('d/m/y', strtotime($cmd['date_commande'])) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TYPE DE PROBLÈME</label>
                        <select name="type_reclamation" class="form-select bg-light border-0" required>
                            <option value="vetement_endommage">Vêtement endommagé</option>
                            <option value="traitement_mal_effectue">Traitement mal effectué</option>
                            <option value="commande_non_conforme">Commande non conforme</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">VOTRE MESSAGE / EXPLICATIONS</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="4" placeholder="Détaillez le problème ici..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold w-100 py-2">TRANSMETTRE AU SERVICE QUALITÉ</button>
                </form>
            </div>
        </div>

    </div>
</div>

</body>
</html>