<?php
// Fichier : install/database_init.php

require_once __DIR__ . '/../inc/database.inc.php';

class DatabaseInitializer
{
    private const string SUCCESS_MESSAGE = '✅ OK';
    private const string ERROR_MESSAGE = '❌ Erreur';
    private const string SKIPPED_MESSAGE = '⚠️ Ignoré (Pas de connexion)';

    // On autorise null au cas où la connexion échoue
    private ?DatabaseManager $dbm = null;

    public function initialize(): array
    {
        // L'ordre est important : on connecte d'abord, on crée le schéma ensuite
        return [
            'Connexion à la base de données' => $this->stepConnect(),
            'Création du schéma & import des données' => $this->stepCreateSchema(),
        ];
    }

    private function stepConnect(): string
    {
        try {
            // On instancie ici pour capturer l'erreur si la DB est down
            $this->dbm = new DatabaseManager(
                dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
                username: 'lowify',
                password: 'lowifypassword'
            );

            // Test simple
            $this->dbm->executeQuery('SELECT 1');
            return self::SUCCESS_MESSAGE;

        } catch (Exception $e) {
            // Capture PDOException ou toute autre erreur venant du constructeur
            return self::ERROR_MESSAGE . ' : ' . $e->getMessage();
        }
    }

    private function stepCreateSchema(): string
    {
        // Si la connexion a échoué à l'étape précédente, on ne peut pas continuer
        if ($this->dbm === null) {
            return self::SKIPPED_MESSAGE;
        }

        $sqlFile = __DIR__ . '/db.sql';

        if (!file_exists($sqlFile)) {
            return self::ERROR_MESSAGE . ' : Fichier db.sql introuvable.';
        }

        try {
            $sql = file_get_contents($sqlFile);

            // Note: Assurez-vous que votre DatabaseManager possède bien une méthode executeUpdate
            // ou qu'il gère les requêtes multiples correctement.
            // Si executeUpdate n'existe pas, utilisez executeQuery($sql)
            if (method_exists($this->dbm, 'executeUpdate')) {
                $this->dbm->executeUpdate($sql);
            } else {
                $this->dbm->executeQuery($sql);
            }

            return self::SUCCESS_MESSAGE;

        } catch (Exception $e) {
            return self::ERROR_MESSAGE . ' : ' . $e->getMessage();
        }
    }
}