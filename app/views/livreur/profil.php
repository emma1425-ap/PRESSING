<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil Livreur | Prestige Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #1A1A1A; --beige: #FCFAF7; --brown: #5d4037; }
        body { background: var(--beige); font-family: 'Montserrat', sans-serif; color: var(--dark); }
        
        .profile-header-card { 
            background: var(--dark); border-radius: 25px; padding: 40px; 
            color: white; margin-bottom: 30px; position: relative; overflow: hidden;
            border-bottom: 4px solid var(--gold);
        }
        .avatar-placeholder { 
            width: 90px; height: 90px; background: var(--gold); border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold;
        }
        .settings-card { 
            background: white; border-radius: 20px; border: none; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 30px; margin-bottom: 30px;
        }
        .form-label { font-size: 0.75rem; font-weight: 700; color: var(--gold); text-transform: uppercase; }
        .form-control { border-radius: 12px; padding: 12px; background: #f8f9fa; border: 1px solid #eee; }
        .btn-gold { background: var(--gold); color: white; border-radius: 12px; padding: 12px; font-weight: 600; border: none; width: 100%; transition: 0.3s; }
        .btn-gold:hover { background: var(--dark); transform: translateY(-2px); }
        .status-badge { background: rgba(180, 151, 90, 0.15); color: var(--gold); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body>

<div class="container py-5">
    <div style="max-width: 800px; margin: auto;">
        
        <div class="mb-4">
            <a href="index.php?page=livreur_dashboard" class="text-decoration-none text-muted small fw-bold">
                <i class="bi bi-arrow-left"></i> RETOUR AU COCKPIT
            </a>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-check2-all me-2"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="profile-header-card d-flex align-items-center gap-4">
            <div class="avatar-placeholder"><?= strtoupper(substr($user['nom'] ?? 'L', 0, 1)) ?></div>
            <div>
                <h2 class="mb-1 fw-bold"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></h2>
                <div class="d-flex align-items-center gap-2">
                    <span class="status-badge">LIVREUR ÉLITE</span>
                    <span class="text-white-50 small">Partenaire Prestige</span>
                </div>
            </div>
        </div>

        <form action="index.php?page=gerer_profil_livreur" method="POST">
            <div class="settings-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-person-circle me-2"></i>Identité Professionnelle</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Prénom</label>
                        <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Email de contact</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly style="opacity: 0.6;">
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h5 class="fw-bold mb-4"><i class="bi bi-lock me-2"></i>Sécurité du Compte</h5>
                <div class="row align-items-end g-3">
                    <div class="col-md-7">
                        <label class="form-label">Nouveau Mot de Passe</label>
                        <input type="password" name="nouveau_password" class="form-control" placeholder="Laisser vide pour inchangé">
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn-gold">ENREGISTRER LES MODIFICATIONS</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>