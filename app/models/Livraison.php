<?php

class Livraison
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByLivreur(int $livreurId)
    {
        $sql = "SELECT * FROM livraisons WHERE livreur_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $livreurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
