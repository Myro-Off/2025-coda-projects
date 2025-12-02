<?php
// Fichier : remove_from_history.php

require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';

$songId = $_GET['song_id'] ?? null;

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );

    // 1. Trouver l'ID de l'entrée la plus récente pour ce Song
    $entry = $db->executeQuery("SELECT id FROM history WHERE song_id = :sid ORDER BY played_at DESC LIMIT 1", ['sid' => $songId]);

    if (!empty($entry)) {
        $entryId = $entry[0]['id'];
        exitWith404('song');
        // 2. Supprimer cette entrée
        $db->executeUpdate("DELETE FROM history WHERE id = :eid", ['eid' => $entryId]);
    }


} catch (PDOException $ex) {
    exitWith500('db');
}

// Redirection vers la page précédente (l'historique)
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'playlists.php?subfilter=history'));
exit;
