<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messages Incidents | Prestige Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --gold: #B4975A; --dark: #1A1A1A; --beige: #FCFAF7; }
        body { background: var(--beige); font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; height: 100vh; }

        /* Structure Split Screen */
        .messenger-container { height: calc(100vh - 40px); margin: 20px; background: white; border-radius: 25px; display: flex; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }

        /* Colonne Gauche : Liste des incidents */
        .contacts-list { width: 350px; border-right: 1px solid #F0F0F0; display: flex; flex-direction: column; background: #fff; }
        .list-header { padding: 25px; border-bottom: 1px solid #F0F0F0; background: #fff; z-index: 10; }
        .contact-item { padding: 20px; border-bottom: 1px solid #F8F8F8; cursor: pointer; transition: 0.3s; text-decoration: none; color: inherit; display: block; }
        .contact-item:hover { background: #FFF9F0; }
        .contact-item.active { background: #F8F4EB; border-left: 4px solid var(--gold); }

        /* Colonne Droite : Chat */
        .chat-window { flex-grow: 1; display: flex; flex-direction: column; background: #FFF; }
        .chat-header { padding: 20px 30px; border-bottom: 1px solid #F0F0F0; display: flex; align-items: center; justify-content: space-between; background: #fff; }
        .chat-body { flex-grow: 1; padding: 30px; overflow-y: auto; background: #FBFAF8; display: flex; flex-direction: column; gap: 15px; }

        /* Bulles de message Style Instagram/WhatsApp */
        .message-bubble { max-width: 70%; padding: 12px 18px; border-radius: 20px; font-size: 0.9rem; line-height: 1.4; position: relative; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
        .msg-client { align-self: flex-start; background: #E9E9EB; color: var(--dark); border-bottom-left-radius: 5px; }
        .msg-employe { align-self: flex-end; background: var(--gold); color: white; border-bottom-right-radius: 5px; }
        
        .msg-time { font-size: 0.65rem; opacity: 0.7; margin-top: 5px; display: block; }

        /* Input area */
        .chat-input-area { padding: 20px 30px; border-top: 1px solid #F0F0F0; background: #fff; }
        .input-wrapper { background: #F5F5F5; border-radius: 30px; padding: 5px 15px; display: flex; align-items: center; }
        .input-wrapper input { border: none; background: transparent; padding: 10px; flex-grow: 1; outline: none; font-size: 0.95rem; }
        .btn-send { background: var(--gold); color: white; border: none; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.3s; cursor: pointer; }
        .btn-send:hover { background: var(--dark); transform: scale(1.1); }

        /* Scrollbar custom */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E0E0E0; border-radius: 10px; }
    </style>
</head>
<body>

<div class="messenger-container">
    <div class="contacts-list">
        <div class="list-header d-flex justify-content-between align-items-center">
            <span class="fw-bold fs-5">Incidents</span>
            <a href="index.php?page=employe_dashboard" class="btn btn-sm btn-light rounded-pill"><i class="bi bi-house"></i></a>
        </div>
        <div style="overflow-y: auto; flex-grow: 1;">
            <?php if(empty($incidents)): ?>
                <div class="p-4 text-center text-muted small">Aucune réclamation.</div>
            <?php else: ?>
                <?php foreach($incidents as $i): ?>
                    <a href="index.php?page=reclamations&id=<?= $i['id_reclamation'] ?>" class="contact-item <?= (isset($_GET['id']) && $_GET['id'] == $i['id_reclamation']) ? 'active' : '' ?>">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-bold small"><?= htmlspecialchars($i['nom'] . ' ' . $i['prenom']) ?></span>
                            <span style="font-size: 0.7rem;" class="badge <?= $i['statut'] == 'resolu' ? 'bg-success' : 'bg-warning' ?> opacity-75">
                                <?= $i['statut'] ?? 'ouvert' ?>
                            </span>
                        </div>
                        <div class="small text-muted text-truncate"><?= htmlspecialchars_decode($i['description']) ?></div>
                        <div class="text-end" style="font-size: 0.65rem; opacity: 0.5;">CMD #<?= $i['id_commande'] ?></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="chat-window">
        <?php if(isset($ticket_actuel) && $ticket_actuel): ?>
            <div class="chat-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; font-weight: bold; background: var(--gold) !important;">
                        <?= strtoupper(substr($ticket_actuel['nom'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-bold"><?= htmlspecialchars($ticket_actuel['nom'] . ' ' . $ticket_actuel['prenom']) ?></div>
                        <div class="small <?= $ticket_actuel['statut'] == 'resolu' ? 'text-success' : 'text-warning' ?>">
                            ● Ticket <?= ucfirst($ticket_actuel['statut']) ?>
                        </div>
                    </div>
                </div>
                
                <?php if($ticket_actuel['statut'] !== 'resolu'): ?>
                    <a href="index.php?page=cloturer_incident&id=<?= $ticket_actuel['id_reclamation'] ?>" 
                       class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" 
                       onclick="return confirm('Marquer cet incident comme résolu ?')">
                        <i class="bi bi-check2-all me-1"></i> Résolu
                    </a>
                <?php endif; ?>
            </div>

            <div class="chat-body" id="chatBody">
                <div class="message-bubble msg-client">
                    <small class="fw-bold d-block mb-1 text-uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> 
                        Objet : <?= str_replace('_', ' ', $ticket_actuel['type_reclamation']) ?>
                    </small>
                    <?= nl2br(htmlspecialchars_decode($ticket_actuel['description'])) ?>
                   <span class="msg-time text-end">Ticket ouvert</span>
                </div>

                <?php foreach($messages as $m): ?>
                    <div class="message-bubble <?= $m['role'] == 'client' ? 'msg-client' : 'msg-employe' ?>">
                        <?= nl2br(htmlspecialchars_decode($m['message'])) ?>
                        <span class="msg-time <?= $m['role'] == 'client' ? '' : 'text-white' ?> text-end">
                            <?= date('H:i', strtotime($m['date_envoi'])) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="chat-input-area">
                <?php if($ticket_actuel['statut'] !== 'resolu'): ?>
                    <form action="index.php?page=repondre_reclamation" method="POST">
                        <input type="hidden" name="id_reclamation" value="<?= $ticket_actuel['id_reclamation'] ?>">
                        <div class="input-wrapper">
                            <input type="text" name="message" placeholder="Écrivez votre réponse prestige..." required autocomplete="off">
                            <button type="submit" class="btn-send">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center text-muted small py-2 italic">
                        <i class="bi bi-lock-fill me-1"></i> Cette conversation est clôturée.
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background: #FBFAF8;">
                <div class="mb-4" style="font-size: 5rem; opacity: 0.1;"><i class="bi bi-chat-quote-fill"></i></div>
                <h5 class="fw-bold" style="color: var(--dark);">Service Client Prestige</h5>
                <p class="small">Sélectionnez une discussion pour commencer à échanger.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    const chatBody = document.getElementById('chatBody');
    if(chatBody) {
        chatBody.scrollTop = chatBody.scrollHeight;
    }
</script>

</body>
</html>