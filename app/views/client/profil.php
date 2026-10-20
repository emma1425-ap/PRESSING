<?php
// On suppose que la variable $client est déjà chargée (ex: depuis la session ou une requête PDO)
// $client = $_SESSION['client'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Profil | Prestige Pressing</title>
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

        /* Centrage vertical et horizontal de tout le contenu */
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
            max-width: 850px; /* Largeur optimale pour le centrage */
            padding: 20px;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* En-tête de la page */
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
            font-size: 1.8rem;
            margin: 0;
        }

        .header-titles span {
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 2px;
            color: #a08d84;
            display: block;
            margin-bottom: 2px;
        }

        /* Bouton de retour style "Pill" */
        .btn-return {
            text-decoration: none;
            color: var(--marron-royal);
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 18px;
            border: 1px solid rgba(90, 58, 43, 0.1);
            border-radius: 50px;
            transition: all 0.3s ease;
            background: white;
            display: flex;
            align-items: center;
        }

        .btn-return:hover {
            background: var(--marron-royal);
            color: white;
            transform: translateX(-3px);
        }

        /* Carte de profil massive et chic */
        .main-card {
            background: var(--blanc);
            border-radius: 30px;
            box-shadow: 0 25px 60px rgba(90, 58, 43, 0.06);
            padding: 45px;
            border: 1px solid rgba(201, 161, 74, 0.08);
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            color: var(--or-prestige);
            font-size: 1.1rem;
            margin-bottom: 35px;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.5px;
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
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(201, 161, 74, 0.05);
            outline: none;
        }

        /* Bouton Centré */
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-prestige {
            background: var(--or-prestige);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 15px 50px;
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

        /* Footer de la carte */
        .info-footer {
            margin-top: 35px;
            padding-top: 20px;
            border-top: 1px solid #f9f4f0;
            text-align: center;
        }

        .info-footer p {
            font-size: 0.7rem;
            color: #b0a098;
            margin: 0;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="prestige-container">
    <div class="prestige-header">
        <div class="header-titles">
            <span>Votre espace personnel</span>
            <h1>Mon Profil</h1>
        </div>
        <a href="index.php?page=client_dashboard" class="btn-return">
            <i class="fa-solid fa-arrow-left me-2"></i> Dashboard
        </a>
    </div>

    <div class="main-card">
        <div class="card-title">Modification des Coordonnées</div>
        
        <form action="index.php?page=update_settings" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="luxury-input" 
                           value="<?= htmlspecialchars($client['prenom'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nom de famille</label>
                    <input type="text" name="nom" class="luxury-input" 
                           value="<?= htmlspecialchars($client['nom'] ?? '') ?>" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Adresse E-mail</label>
                    <input type="email" name="email" class="luxury-input" 
                           value="<?= htmlspecialchars($client['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn-prestige">
                    Enregistrer les modifications
                </button>
            </div>
        </form>

        <div class="info-footer">
            <p>
                <i class="fa-solid fa-shield-halved me-2"></i> 
                Vos informations sont sécurisées selon les standards Prestige.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>