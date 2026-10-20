<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Livreur - Elite Logistique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* On utilise le fond marron chocolat de ta première image */
            background-color: #5d4037; 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }
        .login-card {
            /* On utilise le fond crème de tes captures */
            background-color: #fcf3e8; 
            border-radius: 25px;
            padding: 50px 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            text-align: center;
        }
        .brand-title {
            /* Titre en marron foncé pour le contraste */
            color: #4e342e; 
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #795548;
            font-size: 0.9rem;
            margin-bottom: 35px;
        }
        .form-label {
            display: block;
            text-align: left;
            color: #4e342e;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 8px;
        }
        .form-control {
            /* Fond blanc cassé pour les champs (plus de bleu) */
            background-color: #ffffff; 
            border: 1px solid #d7ccc8;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 20px;
            color: #333;
        }
        .form-control:focus {
            border-color: #bfa36d;
            box-shadow: none;
            background-color: #fff;
        }
        .btn-session {
            /* Bouton marron comme sur la capture client */
            background-color: #5d4037; 
            color: #fcf3e8;
            border: none;
            border-radius: 40px;
            padding: 14px;
            width: 100%;
            font-weight: 600;
            margin-top: 10px;
            transition: 0.3s;
        }
        .btn-session:hover {
            background-color: #4e342e;
            transform: scale(1.02);
        }
        .footer-link {
            margin-top: 25px;
            color: #795548;
            font-size: 0.9rem;
            text-decoration: none;
            display: block;
        }
        .footer-link:hover {
            color: #4e342e;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h2 class="brand-title">Connexion Livreur</h2>
    <p class="subtitle">Accédez à votre cockpit de livraison</p>

    <form action="index.php?page=login_process" method="POST">
        <div class="mb-1">
            <label class="form-label">Identifiant Expert</label>
            <input type="email" name="email" class="form-control" placeholder="livreur@pressing.com" required>
        </div>

        <div class="mb-4">
            <label class="form-label">Clef d'accès</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-session">Ouvrir la session</button>
    </form>

    <a href="index.php" class="footer-link">
        ← Retour
    </a>
</div>

</body>
</html>