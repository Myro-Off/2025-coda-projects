<?php
// Fichier : inc/database.inc.php

class DatabaseManager
{
    private PDO $pdo;

    public function __construct(string $dsn, string $username = '', string $password = '')
    {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->query("SELECT 1");
        } catch (PDOException $e) {
            throw new PDOException("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Exécute une requête SQL de type SELECT avec des paramètres nommés.
     */
    public function executeQuery(string $query, array $params = []): array
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de l'exécution de la requête : " . $e->getMessage());
        }
    }

    /**
     * Exécute une requête SQL de type UPDATE/INSERT/DELETE avec des paramètres nommés.
     */
    public function executeUpdate(string $query, array $params = []): int
    {
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de l'exécution de la requête : " . $e->getMessage());
        }
    }

    /**
     * Récupère l'ID de la dernière ligne insérée.
     */
    public function getLastInsertId(): int
    {
        return (int)$this->pdo->lastInsertId();
    }
}
