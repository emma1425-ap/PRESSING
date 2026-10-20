<?php

class Facture
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByClient(int $clientId)
    {
        $sql = "SELECT * FROM factures WHERE client_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
