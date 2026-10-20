<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pressing Luxe — Choix du rôle</title>

    <style>
        :root{
            --marron-fonce:#3b2417;
            --marron:#5a3a2b;
            --beige:#f5efe6;
            --or:#c9a14a;
        }

        *{
            box-sizing: border-box;
            font-family: "Segoe UI", sans-serif;
        }

        body{
            margin:0;
            background: linear-gradient(135deg, var(--marron-fonce), var(--marron));
            height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
        }

        .container{
            background: var(--beige);
            width: 850px;
            max-width: 95%;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 30px 60px rgba(0,0,0,0.4);
            text-align:center;
        }

        h1{
            color: var(--marron-fonce);
            margin-bottom: 10px;
            font-size: 32px;
            letter-spacing: 1px;
        }

        .subtitle{
            color: #6b4a3a;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .roles{
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
            gap:30px;
        }

        .card{
            background: #fff;
            border-radius: 18px;
            padding: 35px 25px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor:pointer;
        }

        .card:hover{
            transform: translateY(-8px);
            box-shadow: 0 25px 45px rgba(0,0,0,0.25);
        }

        .icon{
            font-size: 45px;
            margin-bottom: 15px;
            color: var(--or);
        }

        .card h2{
            color: var(--marron-fonce);
            margin-bottom: 10px;
            font-size: 22px;
        }

        .card p{
            color:#6d5b52;
            font-size:14px;
            margin-bottom:25px;
        }

        .btn{
            display:inline-block;
            padding: 12px 30px;
            border-radius: 30px;
            background: linear-gradient(135deg, var(--marron), var(--marron-fonce));
            color:#fff;
            text-decoration:none;
            font-weight:600;
            transition: background 0.3s, transform 0.2s;
        }

        .btn:hover{
            background: linear-gradient(135deg, var(--or), var(--marron));
            transform: scale(1.05);
        }

        footer{
            margin-top:40px;
            color:#7a665c;
            font-size:13px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Pressing Prestige</h1>
    <p class="subtitle">Sélectionnez votre espace d’accès</p>

    <div class="roles">

        <!-- CLIENT -->
        <div class="card">
            <div class="icon">👗</div>
            <h2>Client</h2>
            <p>Commandez, suivez vos vêtements et consultez vos factures.</p>
            <a href="index.php?page=login_client" class="btn">Accéder</a>
        </div>

        <!-- EMPLOYÉ -->
        <div class="card">
            <div class="icon">🧺</div>
            <h2>Employé</h2>
            <p>Gérez les commandes, les services et la production.</p>
            <a href="index.php?page=employe_login" class="btn">Accéder</a>
        </div>

        <!-- LIVREUR -->
        <div class="card">
            <div class="icon">🚚</div>
            <h2>Livreur</h2>
            <p>Suivez et effectuez les livraisons en toute efficacité.</p>
            <a href="index.php?page=livreur_login" class="btn">Accéder</a>
        </div>

    </div>

    <footer>
        © <?= date('Y') ?> Pressing Prestige — Élégance & Soin
    </footer>
</div>

</body>
</html>
