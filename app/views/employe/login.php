<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Pressing | Espace Professionnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --royal-gold: #c5a059;
            --soft-gold: #e2c286;
            --pure-white: #ffffff;
            --light-bg: #f8f9fa;
            --text-dark: #2c2c2c;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: var(--text-dark);
        }

        /* Effet de motif de luxe en arrière-plan */
        body::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
            opacity: 0.03;
            z-index: -1;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(197, 160, 89, 0.2);
            border-radius: 30px;
            padding: 60px 45px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 480px;
            text-align: center;
            position: relative;
        }

        /* Liseré doré décoratif */
        .login-card::after {
            content: "";
            position: absolute;
            top: 15px; left: 15px; right: 15px; bottom: 15px;
            border: 1px solid var(--soft-gold);
            border-radius: 20px;
            pointer-events: none;
            opacity: 0.3;
        }

        .logo-title {
            font-family: 'Cinzel', serif;
            color: var(--royal-gold);
            font-size: 2.5rem;
            letter-spacing: 6px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .subtitle {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #888;
            margin-bottom: 45px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--royal-gold);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            text-align: left;
            margin-bottom: 10px;
        }

        .form-control {
            border: none;
            border-bottom: 2px solid #eee;
            border-radius: 0;
            padding: 12px 5px;
            background: transparent;
            font-size: 1rem;
            transition: 0.4s;
            margin-bottom: 30px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--royal-gold);
            background: transparent;
        }

        .btn-elite {
            background: linear-gradient(135deg, var(--royal-gold), var(--soft-gold));
            color: white;
            border: none;
            border-radius: 50px;
            padding: 16px;
            width: 100%;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 0 10px 20px rgba(197, 160, 89, 0.3);
            transition: 0.3s;
            margin-top: 15px;
        }

        .btn-elite:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(197, 160, 89, 0.4);
            color: white;
        }

        .error-box {
            background: #fff5f5;
            color: #e53e3e;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 25px;
            border-left: 4px solid #e53e3e;
        }

        .footer-text {
            margin-top: 40px;
            font-size: 0.75rem;
            color: #aaa;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-title">ELITE</div>
    <div class="subtitle">Cockpit Opérationnel</div>

    <?php if (isset($error)): ?>
        <div class="error-box">
            <i class="bi bi-shield-lock me-2"></i> <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?page=login_employe_submit" method="POST">
        <div class="mb-2 text-start">
            <label class="form-label">Identifiant Expert</label>
            <input type="email" name="email" class="form-control" placeholder="prenom.nom@elite.com" required>
        </div>

        <div class="mb-4 text-start">
            <label class="form-label">Clef d'accès</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-elite">Ouvrir la session</button>
    </form>

    <div class="footer-text">
        <i class="bi bi-patch-check me-1"></i> Connexion sécurisée haute-fidélité
    </div>
</div>

</body>
</html>