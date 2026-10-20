<?php
$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=pressing_db", "root", "pwroot");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // On commence une transaction pour être sûr que tout se passe bien
        $pdo->beginTransaction();

        // 1. Supprimer d'abord les lignes dans 'details_commande' (si la table existe)
        $pdo->prepare("DELETE FROM details_commande WHERE id_commande = ?")->execute([$id]);

        // 2. Supprimer ensuite les lignes dans 'vetement' (c'est ici que ça bloquait)
        $pdo->prepare("DELETE FROM vetement WHERE id_commande = ?")->execute([$id]);

        // 3. Enfin, supprimer la commande elle-même
        $pdo->prepare("DELETE FROM commande WHERE id_commande = ?")->execute([$id]);

        // On valide les changements
        $pdo->commit();

        header("Location: index.php?page=client_dashboard&success=del_ok");
        exit;

    } catch (Exception $e) {
        // En cas d'erreur, on annule tout pour ne pas corrompre les données
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
}