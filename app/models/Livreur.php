<?php
class Livreur {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function login(string $email, string $password) {
        // On cherche dans 'utilisateur' car 'livreur' n'a pas de colonne email
        $sql = "SELECT * FROM utilisateur WHERE email = :email AND role = 'livreur' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification du mot de passe (en clair comme dans ta base)
        if ($user && $password === $user['mot_de_passe']) {
            return $user;
        }
        return null;
    }
}