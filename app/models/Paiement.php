<?php

class Paiement
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $data)
    {
        $sql = "INSERT INTO paiements (facture_id, montant, methode)
                VALUES (:facture_id, :montant, :methode)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
}
