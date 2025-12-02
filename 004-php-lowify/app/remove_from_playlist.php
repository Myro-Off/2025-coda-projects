<?php
// Fichier : remove_from_playlist.php

require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';

$playlistId = $_GET['playlist_id'] ?? null;
$songId = $_GET['song_id'] ?? null;

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );

    $songDurationData = $db->executeQuery("SELECT duration FROM song WHERE id = :sid", ['sid' => $songId]);
    if (empty($songDurationData)) {
        exitWith404('song');
    }
    $songDuration = $songDurationData[0]['duration'];

    // Supprimer l'association
    $db->executeQuery("DELETE FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid", [
        'pid' => $playlistId,
        'sid' => $songId
    ]);

    // Mettre à jour la playlist
    $db->executeQuery("UPDATE playlist SET nb_song = nb_song - 1, duration = duration - :duration WHERE id = :pid", [
        'duration' => $songDuration,
        'pid' => $playlistId
    ]);

} catch (PDOException $ex) {
    exitWith500('delete-from-playlist');
}

header("Location: playlist.php?id=$playlistId");
exit;
