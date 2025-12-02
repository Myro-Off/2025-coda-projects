<?php
// Fichier : delete_playlist.php

require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';

$playlistId = $_GET['id'] ?? null;

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );

    // Supprimer toutes les associations
    $db->executeQuery("DELETE FROM x_playlist_song WHERE playlist_id = :pid", ['pid' => $playlistId]);

    // Supprimer la playlist
    $db->executeQuery("DELETE FROM playlist WHERE id = :pid", ['pid' => $playlistId]);

} catch (PDOException $ex) {
    exitWith500('db');
}

// Redirection vers l'onglet Playlists de la page Favoris
header('Location: profile.php?filter=favorites&subfilter=playlists');
exit;
