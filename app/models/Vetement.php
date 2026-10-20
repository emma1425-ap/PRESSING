<?php

class Vetement
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByCommande(int $commandeId)
    {
        $sql = "SELECT * FROM vetements WHERE commande_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $commandeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
