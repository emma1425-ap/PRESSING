<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #5a3a2b; --beige: #fdfaf5; --light-gray: #E9E9EB; }
        body { background-color: var(--beige); font-family: 'Poppins', sans-serif; }
        .luxury-card { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        
        /* Style du Chat */
        .chat-box { background: #f9f9f9; border-radius: 15px; padding: 20px; max-height: 450px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; }
        
        .bubble { max-width: 80%; padding: 12px 16px; border-radius: 18px; font-size: 0.95rem; position: relative; line-height: 1.4; }

        /* VOS MESSAGES (Livreur) -> À DROITE, MARRON */
        .bubble-livreur { 
            align-self: flex-end; 
            background: var(--gold); 
            color: white; 
            border-bottom-right-radius: 2px; 
        }

        /* MESSAGES DE L'EMPLOYÉ -> À GAUCHE, GRIS */
        .bubble-employe { 
            align-self: flex-start; 
            background: var(--light-gray); 
            color: black; 
            border-bottom-left-radius: 2px; 
        }

        .time-stamp { font-size: 0.65rem; margin-top: 5px; opacity: 0.8; }
        .btn-gold { background: var(--gold); color: white; border: none; font-weight: bold; }
        .btn-gold:hover { background: var(--dark); }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold" style="color: var(--dark);">Suivi des Incidents</h3>
        <a href="index.php?page=livreur_dashboard" class="btn btn-outline-dark btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left"></i> Retour missions
        </a>
    </div>

    <?php if (empty($mes_incidents)): ?>
        <div class="luxury-card text-center">Aucun incident signalé.</div>
    <?php else: ?>
        <?php foreach ($mes_incidents as $inc): ?>
            <div class="luxury-card">
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold m-0">Commande #<?= $inc['id_commande'] ?></h6>
                    <span class="badge bg-dark">Logistique Interne</span>
                </div>

                <div class="chat-box mb-3">
                    <div class="bubble bubble-livreur">
                        <small class="d-block fw-bold" style="font-size: 0.7rem; margin-bottom: 3px; opacity: 0.9;">MON SIGNALEMENT</small>
                        <?= nl2br(htmlspecialchars($inc['description'])) ?>
                    </div>

                    <?php foreach ($messages_internes as $m): ?>
                        <?php if ($m['id_reclamation'] == $inc['id_reclamation']): ?>
                            <div class="bubble <?= $m['role'] == 'livreur' ? 'bubble-livreur' : 'bubble-employe' ?>">
                                <?= nl2br(htmlspecialchars($m['message'])) ?>
                                <div class="time-stamp <?= $m['role'] == 'livreur' ? 'text-end' : '' ?>">
                                    <?= date('H:i', strtotime($m['date_envoi'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <form action="index.php?page=repondre_reclamation_livreur" method="POST">
                    <input type="hidden" name="id_reclamation" value="<?= $inc['id_reclamation'] ?>">
                    <div class="input-group shadow-sm rounded">
                        <input type="text" name="message" class="form-control border-0 bg-light" placeholder="Répondre à la conciergerie..." required>
                        <button class="btn btn-gold" type="submit">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>