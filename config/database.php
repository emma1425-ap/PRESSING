<?php

class Database
{
    private static string $host = "localhost";
    private static string $db_name = "pressing_db";
    private static string $username = "root";
    private static string $password = "pwroot";
    private static ?PDO $conn = null;

    // Ajout du mot-clé "static" pour permettre l'appel direct
    public static function getConnection(): PDO
    {
        if (self::$conn === null) {
            try {
                // Utilisation de self:: pour accéder aux propriétés statiques
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8",
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erreur connexion DB : " . $e->getMessage());
            }
        }

        return self::$conn;
    }
}