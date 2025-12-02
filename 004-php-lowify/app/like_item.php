<?php
// Fichier : like_item.php
// Description : Script d'action pour basculer l'état "liké" (is_liked) d'une entité (chanson, album, artiste)
//               et synchroniser l'ajout/la suppression des chansons associées dans la playlist "Coups de cœur".

require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';

$itemId = $_GET['id'] ?? null;
$type = $_GET['type'] ?? 'song';

// Redirection si l'ID n'est pas un nombre
if (!is_numeric($itemId)) {
    header('Location: index.php');
    exit;
}

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );

    // 1. Initialisation et vérification de la playlist "Coups de cœur"
    $favPlaylistData = $db->executeQuery("SELECT id FROM playlist WHERE name = :name", ['name' => FAVORITES_PLAYLIST_NAME]);

    if (empty($favPlaylistData)) {
        // Création de la playlist si elle n'existe pas
        $db->executeUpdate("INSERT INTO playlist (name, nb_song, duration) VALUES (:name, 0, 0)", ['name' => FAVORITES_PLAYLIST_NAME]);
        $favPlaylistId = $db->getLastInsertId();
    } else {
        $favPlaylistId = $favPlaylistData[0]['id'];
    }

    // 2. Déterminer l'entité principale et son nouvel état
    $table = match ($type) {
        'artist' => 'artist',
        'album' => 'album',
        default => 'song',
    };

    $currentData = $db->executeQuery("SELECT is_liked FROM $table WHERE id = :id", ['id' => $itemId]);

    // Si l'entité n'existe pas ou est introuvable
    if (empty($currentData)) {
        exitWith404($type);
    }

    $isCurrentlyLiked = $currentData[0]['is_liked'];
    $newStatus = $isCurrentlyLiked ? 0 : 1; // 0 pour Unlike, 1 pour Like

    // Mise à jour du flag is_liked sur l'élément (Artiste, Album ou Chanson)
    $db->executeUpdate("UPDATE $table SET is_liked = :val WHERE id = :id", [
        'val' => $newStatus,
        'id' => $itemId
    ]);

    // --- 3. SYNCHRONISATION DES FAVORIS (Chansons concernées) ---

    // Requête pour récupérer toutes les chansons associées à l'entité likée
    $songQuery = '';
    $params = ['id' => $itemId];

    if ($type === 'song') {
        $songQuery = "SELECT id, duration FROM song WHERE id = :id";
    } elseif ($type === 'album') {
        $songQuery = "SELECT id, duration FROM song WHERE album_id = :id";
    } elseif ($type === 'artist') {
        // Pour un artiste, on pourrait ne prendre que ses 50 meilleurs titres pour éviter l'abus de favoris.
        $songQuery = "SELECT s.id, s.duration FROM song s JOIN album a ON s.album_id = a.id WHERE a.artist_id = :id LIMIT 50";
    }

    if (!empty($songQuery)) {
        $songsToToggle = $db->executeQuery($songQuery, $params);
        $songsCountChange = 0;
        $durationChange = 0;

        foreach ($songsToToggle as $song) {
            $songId = $song['id'];
            $songDuration = $song['duration'];

            // Vérification de l'existence dans la playlist Favoris
            $existsInFavs = $db->executeQuery(
                "SELECT COUNT(*) as c FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid",
                ['pid' => $favPlaylistId, 'sid' => $songId]
            )[0]['c'];

            if ($newStatus == 1) {
                // ACTION: LIKE - AJOUTER la chanson si elle n'est pas déjà là
                if ($existsInFavs == 0) {
                    $db->executeUpdate("INSERT INTO x_playlist_song (playlist_id, song_id) VALUES (:pid, :sid)", ['pid' => $favPlaylistId, 'sid' => $songId]);
                    $db->executeUpdate("UPDATE song SET is_liked = 1 WHERE id = :sid", ['sid' => $songId]);
                    $songsCountChange++;
                    $durationChange += $songDuration;
                }
            } else {
                // ACTION: UNLIKE - RETIRER la chanson si elle est présente
                if ($existsInFavs > 0) {
                    $db->executeUpdate("DELETE FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid", ['pid' => $favPlaylistId, 'sid' => $songId]);
                    $db->executeUpdate("UPDATE song SET is_liked = 0 WHERE id = :sid", ['sid' => $songId]);
                    $songsCountChange--;
                    $durationChange -= $songDuration;
                }
            }
        }

        // 4. Mise à jour finale des compteurs de la playlist Favoris
        if ($songsCountChange !== 0) {
            $db->executeUpdate("UPDATE playlist SET nb_song = GREATEST(0, nb_song + :count), duration = GREATEST(0, duration + :dur) WHERE id = :pid", [
                'count' => $songsCountChange,
                'dur' => $durationChange,
                'pid' => $favPlaylistId
            ]);
        }
    }

} catch (PDOException $ex) {
    // Redirection vers l'erreur 500 en cas de problème critique de base de données.
    exitWith500('db');
}

// Redirection vers la page d'où l'on vient (HTTP_REFERER)
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
