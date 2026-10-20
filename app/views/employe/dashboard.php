<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Elite Management | Prestige</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --prestige-beige: #FCFAF7;
            --prestige-gold: #B4975A;
            --prestige-text: #4A3F35;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--prestige-beige);
            color: var(--prestige-text);
            margin: 0; display: flex;
        }

        /* SIDEBAR PRESTIGE */
        .sidebar {
            width: 280px; background: white; height: 100vh;
            position: fixed; padding: 40px 25px;
            border-right: 1px solid rgba(180, 151, 90, 0.1);
        }
        .brand-logo { font-family: 'Playfair Display', serif; font-size: 1.8rem; color: var(--prestige-gold); letter-spacing: 2px; text-transform: uppercase; }
        .nav-link-prestige {
            display: flex; align-items: center; padding: 12px 18px;
            color: var(--prestige-text); text-decoration: none;
            border-radius: 14px; margin-bottom: 5px; font-weight: 500; transition: 0.3s;
        }
        .nav-link-prestige:hover, .nav-link-prestige.active { background-color: #FFF9F0; color: var(--prestige-gold); }

        /* MAIN CONTENT */
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 60px; }
        .welcome-title { font-family: 'Playfair Display', serif; font-size: 2.2rem; margin-bottom: 35px; }

        /* CARTES ET TABLEAUX */
        .prestige-card { background: white; border-radius: 25px; padding: 35px; box-shadow: var(--card-shadow); margin-bottom: 30px; border: none; }
        .stat-card { background: white; border-radius: 20px; padding: 25px; box-shadow: var(--card-shadow); text-align: center; border: none; }
        
        /* BOUTONS D'ACTION RÉELS */
        .btn-action-real {
            padding: 8px 16px; border-radius: 12px; font-size: 0.75rem; 
            font-weight: 600; border: none; transition: 0.2s; 
            text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
        }
        .btn-voir { background: #F8F9FA; color: var(--prestige-text); }
        .btn-voir:hover { background: #EEE; }
        .btn-facture { background: var(--prestige-gold); color: white; }
        .btn-facture:hover { background: #967d4a; color: white; }
        .btn-livrer { background: #4A3F35; color: white; }
        .btn-livrer:hover { background: #332b24; color: white; }

        .badge-attente { background: #FFF8EE; color: #D4A373; padding: 6px 14px; border-radius: 30px; font-size: 0.7rem; font-weight: 700; }
        
        /* STYLE PROFIL */
        .profile-avatar { width: 60px; height: 60px; background: var(--prestige-gold); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="mb-5">
            <div class="brand-logo">Prestige</div>
            <div style="font-size: 0.7rem; letter-spacing: 3px; color: #A0A0A0;">CONCIERGERIE</div>
        </div>
        <nav>
            <a href="index.php?page=dashboard" class="nav-link-prestige active"><i class="bi bi-grid"></i> Dashboard</a>
            <a href="index.php?page=nouveau_depot_employe" class="nav-link-prestige"> <i class="bi bi-plus-circle"></i> Nouveau dépôt</a>
            <a href="index.php?page=caisse" class="nav-link-prestige"><i class="bi bi-receipt"></i> Caisse</a>
            <a href="index.php?page=logistique" class="nav-link-prestige"><i class="bi bi-truck"></i> Logistique</a>
            <a href="index.php?page=reclamations" class="nav-link-prestige"><i class="bi bi-exclamation-octagon"></i> Incidents</a>
            <a href="index.php?page=profil_employe" class="nav-link-prestige mt-4"><i class="bi bi-person-gear"></i> Mon Profil</a>
            <a href="index.php?page=logout" class="nav-link-prestige mt-2 text-danger"><i class="bi bi-power"></i> Déconnexion</a>
        </nav>
    </div>

    <div class="main-content">
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success_depot'): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px; background-color: #d4edda; color: #155724;">
                    <i class="bi bi-check-circle-fill me-2"></i> 
                    <strong>Succès !</strong> Le dépôt a été enregistré et le client a été notifié.
                </div>
            <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="welcome-title mb-0">Tableau de Bord</h1>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <div class="fw-bold"><?= htmlspecialchars($_SESSION['nom'] ?? 'Employé') ?></div>
                    <div class="small text-muted"><?= htmlspecialchars($_SESSION['poste'] ?? 'Agent') ?></div>
                </div>
                <div class="profile-avatar mb-0" style="width: 45px; height: 45px; font-size: 1rem;">
                    <?= strtoupper(substr($_SESSION['nom'] ?? 'E', 0, 1)) ?>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div style="font-size: 0.7rem; color: #A0A0A0; font-weight: 700; letter-spacing: 1px;">RECETTE JOUR</div>
                    <div class="h4 fw-bold mt-2"><?= number_format($stats['ca_du_jour'] ?? 0, 0, ',', ' '); ?> F</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div style="font-size: 0.7rem; color: #A0A0A0; font-weight: 700; letter-spacing: 1px;">À ENCAISSER</div>
                    <div class="h4 fw-bold mt-2 text-warning"><?= $stats['factures_impayer'] ?? 0; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div style="font-size: 0.7rem; color: #A0A0A0; font-weight: 700; letter-spacing: 1px;">PRODUCTION</div>
                    <div class="h4 fw-bold mt-2"><?= $stats['commandes_jour'] ?? 0; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div style="font-size: 0.7rem; color: #A0A0A0; font-weight: 700; letter-spacing: 1px;">LITIGES</div>
                    <div class="h4 fw-bold mt-2 text-danger"><?= $stats['reclamations_neuves'] ?? 0; ?></div>
                </div>
            </div>
        </div>

        <div class="prestige-card">
            <h5 class="fw-bold mb-4">Commandes en cours</h5>
            <table class="table align-middle">
                <thead>
                    <tr class="small text-muted">
                        <th>CLIENT</th>
                        <th>DATE & HEURE</th>
                        <th>ETAT</th>
                        <th class="text-end">ACTIONS RÉELLES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($commandes_actives)): foreach($commandes_actives as $cmd): ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($cmd['nom'] . ' ' . $cmd['prenom']); ?></div>
                            <div class="small text-muted"><?= htmlspecialchars($cmd['telephone']); ?></div>
                        </td>
                        <td>
                            <div class="small fw-bold"><?= date('d/m/Y', strtotime($cmd['date_commande'])); ?></div>
                            <div class="small text-muted"><?= date('H:i', strtotime($cmd['date_commande'])); ?></div>
                        </td>
                        <td><span class="badge-attente text-uppercase"><?= htmlspecialchars($cmd['statut']); ?></span></td>
                        <td class="text-end">
                            <button onclick="ouvrirFiche(<?= $cmd['id_commande']; ?>)" class="btn-action-real btn-voir">
                                <i class="bi bi-eye"></i> Voir
                            </button>
                            
                            <a href="index.php?page=facturation&id=<?= $cmd['id_commande'] ?>" class="btn-action-real btn-facture">
                                <i class="bi bi-cash-stack"></i> Facturer
                            </a>

                            <a href="index.php?page=livraison&action=preparer&id=<?= $cmd['id_commande']; ?>" class="btn-action-real btn-livrer">
                                <i class="bi bi-truck"></i> Livrer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" class="text-center p-4 text-muted">Aucune commande active.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalFiche" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg border-0" style="border-radius: 25px;">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="fw-bold" style="font-family: 'Playfair Display', serif;">Gestion Commande #<span id="cmd_id_display"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-7" id="items_loader">
                            <div class="text-center p-5"><div class="spinner-border text-gold"></div></div>
                        </div>
                        
                        <div class="col-md-5 bg-light p-4 rounded-4">
                            <form action="index.php?page=update_statut" method="POST">
                                    <input type="hidden" name="id_commande" id="form_id_statut">
                                    
                                    <label class="small fw-bold mb-2">Changer le statut</label>
                                    <select name="statut" class="form-select border-0 mb-3 shadow-sm rounded-3">
                                        <option value="En attente">En attente</option>
                                        <option value="En préparation">En préparation</option>
                                        <option value="Prêt">Prêt</option>
                                        <option value="En cours de livraison">En cours de livraison</option>
                                        <option value="Livrée">Livrée</option>
                                        <option value="Problème">Problème</option>
                                    </select>
                                    
                                    <button type="submit" class="btn btn-dark w-100 rounded-pill">Mettre à jour</button>
                            </form>

                            <hr>

                            <form action="index.php?page=assigner_livraison" method="POST">
                                <input type="hidden" name="id_commande" id="form_id_livraison">
                                <label class="small fw-bold mb-2">Assigner un livreur</label>
                                <select name="id_livreur" class="form-select border-0 mb-3 shadow-sm rounded-3" required>
                                    <option value="">Choisir un coursier...</option>
                                    <?php if(!empty($livreurs)): foreach($livreurs as $l): ?>
                                        <option value="<?= $l['id_utilisateur']; ?>"><?= htmlspecialchars($l['nom']); ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                                <button type="submit" class="btn btn-outline-dark w-100 rounded-pill py-2 fw-bold small">Expédier le colis</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function ouvrirFiche(id) {
        document.getElementById('cmd_id_display').innerText = id;
        document.getElementById('form_id_statut').value = id;
        document.getElementById('form_id_livraison').value = id;

        const loader = document.getElementById('items_loader');
        loader.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-gold" style="color: #B4975A;"></div><p class="mt-2 small text-muted">Chargement des détails...</p></div>';

        fetch('index.php?page=get_commande_full_details&id=' + id)
            .then(response => {
                if (!response.ok) throw new Error("Erreur réseau");
                return response.json();
            })
            .then(data => {
                let html = '<h6 class="text-muted small mb-3 fw-bold text-uppercase">Articles du dépôt</h6>';
                let totalGeneral = 0;

                if (!data.items || data.items.length === 0) {
                    html += '<div class="alert alert-info py-3 rounded-4 border-0">Aucun article enregistré pour cette commande.</div>';
                } else {
                    data.items.forEach(i => {
                        let nomArt   = i.nom_article || "Vêtement #" + (i.id_article || 'Inconnu');
                        let nomSoin  = i.nom_soin || "Traitement Standard";
                        let prixBase = parseFloat(i.prix_base_nettoyage || 0);
                        let prixSoin = parseFloat(i.prix_soin || 0);
                        let qte      = parseInt(i.quantite || 1);

                        let sousTotal = (prixBase + prixSoin) * qte;
                        totalGeneral += sousTotal;

                        html += `
                            <div class="p-3 bg-white border-0 shadow-sm rounded-4 mb-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">${nomArt}</div>
                                    <div class="small text-muted">${nomSoin} <span class="badge bg-light text-dark ms-1">x${qte}</span></div>
                                </div>
                                <div class="fw-bold" style="color: #B4975A; font-size: 1rem;">
                                    ${sousTotal.toLocaleString('fr-FR')} F
                                </div>
                            </div>`;
                    });

                    html += `
                        <div class="p-3 bg-dark text-white rounded-4 mt-3 d-flex justify-content-between align-items-center shadow-lg">
                            <span class="small opacity-75 fw-bold text-uppercase" style="letter-spacing: 1px;">Montant Total</span>
                            <span class="fw-bold fs-5">${totalGeneral.toLocaleString('fr-FR')} F</span>
                        </div>`;
                }
                loader.innerHTML = html;
            })
            .catch(err => {
                console.error("Erreur:", err);
                loader.innerHTML = `<div class="alert alert-danger">Erreur de chargement.</div>`;
            });

        const modalElement = document.getElementById('modalFiche');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.show();
    }
    </script>
</body>
</html>