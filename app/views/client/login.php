<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirection si déjà connecté
if (isset($_SESSION['client'])) {
    header("Location: index.php?page=client_dashboard");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Client | Pressing Prestige</title>
    <style>
        :root{ --marron-fonce:#3b2417; --marron:#5a3a2b; --beige:#f5efe6; --or:#c9a14a; }
        *{ box-sizing: border-box; font-family: "Segoe UI", sans-serif; }
        body{ margin:0; background: linear-gradient(135deg, var(--marron-fonce), var(--marron)); height:100vh; display:flex; align-items:center; justify-content:center; }
        .login-box{ background: var(--beige); width: 380px; padding: 40px 35px; border-radius: 18px; box-shadow: 0 25px 50px rgba(0,0,0,0.4); text-align:center; }
        .login-box h1{ color: var(--marron-fonce); margin-bottom: 8px; font-size: 26px; }
        .login-box input{ width: 100%; padding: 14px; margin-bottom: 15px; border-radius: 10px; border: 1px solid #d7ccc8; }
        .login-box button{ width: 100%; padding: 14px; background: var(--marron); border: none; border-radius: 30px; color: white; cursor: pointer; font-weight: 600; }
        .error{ background: #f8d7da; color: #842029; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="login-box">
    <h1>Connexion Client</h1>
    <p>Accédez à votre espace pressing premium</p>

    <?php 
    $error = $_SESSION['error_client'] ?? null;
    if ($error): 
    ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php unset($_SESSION['error_client']); ?>
    <?php endif; ?>

    <form method="POST" action="index.php?page=login_client">
        <input type="email" name="email" placeholder="Adresse email" required>
        
        <input type="password" name="password" placeholder="Mot de passe" required>

        <button type="submit">Se connecter</button>
    </form>

    <div style="margin-top: 20px;">
        <a href="index.php?page=choix_role" style="color: var(--marron); text-decoration: none;">← Retour</a>
    </div>
</div>

</body>
</html>