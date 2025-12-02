<?php
// Fichier : like_item.php

require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';

$itemId = $_GET['id'] ?? null;
$type = $_GET['type'] ?? 'song'; // song, album, artist

if (!is_numeric($itemId)) {
    // Redirection de sécurité si l'ID n'est pas valide
    header('Location: index.php');
    exit;
}

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );

    // 1. Gestion de la playlist "Coup de cœur" (Nécessaire uniquement si on like une chanson)
    // On récupère ou crée l'ID de cette playlist spéciale
    $favPlaylistData = $db->executeQuery("SELECT id FROM playlist WHERE name = :name", ['name' => FAVORITES_PLAYLIST_NAME]);

    if (empty($favPlaylistData)) {
        $db->executeUpdate("INSERT INTO playlist (name, nb_song, duration) VALUES (:name, 0, 0)", ['name' => FAVORITES_PLAYLIST_NAME]);
        $favPlaylistId = $db->getLastInsertId();
    } else {
        $favPlaylistId = $favPlaylistData[0]['id'];
    }

    // 2. Déterminer la table et mettre à jour le statut de l'élément principal
    // On sécurise le nom de la table
    $table = match ($type) {
        'artist' => 'artist',
        'album' => 'album',
        default => 'song',
    };

    // On récupère l'état actuel (Liké ou pas ?)
    $currentData = $db->executeQuery("SELECT is_liked FROM $table WHERE id = :id", ['id' => $itemId]);

    if (!empty($currentData)) {
        $isLiked = $currentData[0]['is_liked'];
        $newStatus = $isLiked ? 0 : 1;

        // Mise à jour du flag is_liked sur l'élément (Artiste, Album ou Chanson)
        $db->executeUpdate("UPDATE $table SET is_liked = :val WHERE id = :id", [
            'val' => $newStatus,
            'id' => $itemId
        ]);

        // 3. LOGIQUE SPÉCIFIQUE POUR LES CHANSONS
        // Si c'est une chanson, on doit aussi gérer la playlist "Coup de cœur"
        if ($type === 'song') {
            // Récupérer la durée pour mettre à jour les stats de la playlist
            $songData = $db->executeQuery("SELECT duration FROM song WHERE id = :id", ['id' => $itemId]);
            $songDuration = $songData[0]['duration'] ?? 0;

            if ($newStatus == 1) {
                // ACTION: AJOUTER AUX FAVORIS

                // Vérifier si déjà présent pour éviter les doublons
                $exists = $db->executeQuery(
                    "SELECT COUNT(*) as c FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid",
                    ['pid' => $favPlaylistId, 'sid' => $itemId]
                )[0]['c'];

                if ($exists == 0) {
                    // Ajout de la liaison
                    $db->executeUpdate(
                        "INSERT INTO x_playlist_song (playlist_id, song_id) VALUES (:pid, :sid)",
                        ['pid' => $favPlaylistId, 'sid' => $itemId]
                    );

                    // Mise à jour des compteurs de la playlist (+1 song, +durée)
                    $db->executeUpdate(
                        "UPDATE playlist SET nb_song = nb_song + 1, duration = duration + :dur WHERE id = :pid",
                        ['dur' => $songDuration, 'pid' => $favPlaylistId]
                    );
                }
            } else {
                // ACTION: RETIRER DES FAVORIS

                // Suppression de la liaison
                $db->executeUpdate(
                    "DELETE FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid",
                    ['pid' => $favPlaylistId, 'sid' => $itemId]
                );

                // On s'assure de ne pas avoir de nombres négatifs avec GREATEST
                $db->executeUpdate(
                    "UPDATE playlist SET nb_song = GREATEST(0, nb_song - 1), duration = GREATEST(0, duration - :dur) WHERE id = :pid",
                    ['dur' => $songDuration, 'pid' => $favPlaylistId]
                );
            }
        }
    }

} catch (PDOException $ex) {
    exitWith500('db');
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
