<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil | Prestige Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --gold: #B4975A; --dark: #1A1A1A; --beige: #FCFAF7; }
        body { background: var(--beige); font-family: 'Plus Jakarta Sans', sans-serif; color: var(--dark); }
        
        .main-content { padding: 40px 0; }
        .profile-container { max-width: 900px; margin: auto; }

        .profile-header-card { 
            background: var(--dark); border-radius: 25px; padding: 40px; 
            color: white; margin-bottom: 30px; position: relative; overflow: hidden;
        }
        .profile-header-card::after {
            content: ""; position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
            background: var(--gold); opacity: 0.1; border-radius: 50%;
        }

        .avatar-placeholder { 
            width: 100px; height: 100px; background: var(--gold); border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: bold;
        }

        .settings-card { 
            background: white; border-radius: 20px; border: none; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 30px; margin-bottom: 30px;
        }
        .card-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .card-title i { color: var(--gold); }

        .form-label { font-size: 0.85rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { 
            border-radius: 12px; border: 1px solid #E0E0E0; padding: 12px 15px; 
            background: #F9F9F9; transition: 0.3s; 
        }
        .form-control:focus { background: white; border-color: var(--gold); box-shadow: 0 0 0 4px rgba(180, 151, 90, 0.1); }

        .btn-gold { 
            background: var(--gold); color: white; border: none; border-radius: 12px; 
            padding: 12px 30px; font-weight: 600; transition: 0.3s;
        }
        .btn-gold:hover { background: var(--dark); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        
        .status-badge { background: rgba(180, 151, 90, 0.2); color: var(--gold); padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body>

<div class="container main-content">
    <div class="profile-container">
        
        <div class="mb-4">
            <a href="index.php?page=employe_dashboard" class="text-decoration-none text-muted small fw-bold">
                <i class="bi bi-chevron-left"></i> RETOUR AU TABLEAU DE BORD
            </a>
        </div>

        <?php if(isset($success) && $success): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <div class="profile-header-card d-flex align-items-center gap-4">
            <div class="avatar-section">
                <div class="avatar-placeholder"><?= strtoupper(substr($user['nom'] ?? 'U', 0, 1)) ?></div>
            </div>
            <div>
                <h2 class="mb-1 fw-bold"><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></h2>
                <div class="d-flex align-items-center gap-2">
                    <span class="status-badge text-uppercase"><?= htmlspecialchars($user['role'] ?? 'Employé') ?> Prestige</span>
                    <span class="text-white-50 small">Membre depuis 2024</span>
                </div>
            </div>
        </div>

        <form action="index.php?page=profil_employe" method="POST">
            <div class="row">
                <div class="col-md-12">
                    <div class="settings-card">
                        <div class="card-title"><i class="bi bi-person-badge"></i> Informations Personnelles</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom'] ?? '') ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Adresse Email Professionnelle</label>
                                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" readonly style="cursor: not-allowed; opacity: 0.7;">
                                <div class="form-text">L'email ne peut être modifié que par l'administrateur.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="settings-card">
                        <div class="card-title"><i class="bi bi-shield-lock"></i> Sécurité du Compte</div>
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Nouveau Mot de Passe</label>
                                <input type="password" name="nouveau_password" class="form-control" placeholder="••••••••••••">
                                <p class="form-text mb-0 mt-2 text-muted">Laissez vide si vous ne souhaitez pas modifier votre mot de passe actuel.</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="submit" class="btn btn-gold w-100">
                                    Mettre à jour le profil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>

</body>
</html>