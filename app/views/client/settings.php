<?php
// On récupère le statut passé dans l'URL par l'index.php
$status = $_GET['status'] ?? null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sécurité | Prestige Pressing</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --or-prestige: #c9a14a;
            --marron-royal: #5a3a2b;
            --beige-clair: #fdfaf5;
            --blanc: #ffffff;
            --succes-bg: #e7f7ed;
            --succes-texte: #218838;
            --erreur-bg: #f8d7da;
            --erreur-texte: #dc3545;
        }

        /* Centrage vertical et horizontal */
        body, html {
            height: 100%;
            margin: 0;
            background-color: var(--beige-clair);
            display: flex;
            align-items: center; 
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            color: var(--marron-royal);
        }

        .prestige-container {
            width: 100%;
            max-width: 500px; 
            padding: 20px;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* --- NOTIFICATIONS GLAMOUR --- */
        .glamour-alert {
            border-radius: 15px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-size: 0.75rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            border: 1px solid transparent;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success-prestige {
            background-color: var(--succes-bg);
            color: var(--succes-texte);
            border-color: rgba(33, 136, 56, 0.1);
        }

        .alert-error-prestige {
            background-color: var(--erreur-bg);
            color: var(--erreur-texte);
            border-color: rgba(220, 53, 69, 0.1);
        }

        /* En-tête */
        .prestige-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 25px;
            padding: 0 10px;
        }

        .header-titles h1 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: var(--marron-royal);
            font-size: 1.6rem;
            margin: 0;
        }

        .header-titles span {
            text-transform: uppercase;
            font-size: 0.6rem;
            letter-spacing: 2px;
            color: #a08d84;
            display: block;
            margin-bottom: 2px;
        }

        .btn-return {
            text-decoration: none;
            color: var(--marron-royal);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 15px;
            border: 1px solid rgba(90, 58, 43, 0.1);
            border-radius: 50px;
            transition: all 0.3s ease;
            background: white;
        }

        .btn-return:hover {
            background: var(--marron-royal);
            color: white;
        }

        /* Carte de sécurité */
        .main-card {
            background: var(--blanc);
            border-radius: 30px;
            box-shadow: 0 25px 60px rgba(90, 58, 43, 0.06);
            padding: 40px;
            border: 1px solid rgba(201, 161, 74, 0.08);
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            color: var(--or-prestige);
            font-size: 1.1rem;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
        }

        /* Formulaire */
        .form-label {
            font-size: 0.6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #999;
            margin-bottom: 8px;
            display: block;
        }

        .luxury-input {
            width: 100%;
            border: 1px solid #f0ece9;
            background-color: #fdfdfd;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 0.85rem;
            color: var(--marron-royal);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .luxury-input:focus {
            border-color: var(--or-prestige);
            outline: none;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(201, 161, 74, 0.05);
        }

        /* Bouton Large */
        .btn-prestige {
            background: var(--or-prestige);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px;
            width: 100%;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 8px 20px rgba(201, 161, 74, 0.2);
        }

        .btn-prestige:hover {
            background: var(--marron-royal);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(90, 58, 43, 0.15);
        }

        .info-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.7rem;
            color: #b0a098;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="prestige-container">

    <?php if ($status === 'success'): ?>
        <div class="glamour-alert alert-success-prestige">
            <i class="fa-solid fa-circle-check me-2"></i>
            Votre mot de passe a été modifié avec succès.
        </div>
    <?php elseif ($status === 'error_old_pw'): ?>
        <div class="glamour-alert alert-error-prestige">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            L'ancien mot de passe est incorrect.
        </div>
    <?php endif; ?>

    <div class="prestige-header">
        <div class="header-titles">
            <span>Confidentialité</span>
            <h1>Sécurité</h1>
        </div>
        <a href="index.php?page=client_dashboard" class="btn-return">
            <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="main-card">
        <div class="card-title">Changer le mot de passe</div>

        <form action="index.php?page=update_password_action" method="POST">
            <div class="mb-2">
                <label class="form-label">Mot de passe actuel</label>
                <input type="password" name="old_password" class="luxury-input" placeholder="••••••••" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="new_password" class="luxury-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-prestige">
                Confirmer le changement
            </button>
        </form>

        <div class="info-footer">
            <p><i class="fa-solid fa-key me-2"></i> Utilisez un mot de passe robuste pour votre sécurité.</p>
        </div>
    </div>
</div>

<script>
    // Fait disparaître la notification après 5 secondes
    setTimeout(function() {
        let alert = document.querySelector('.glamour-alert');
        if (alert) {
            alert.style.transition = "opacity 0.6s ease";
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 600);
        }
    }, 5000);
</script>

</body>
</html>