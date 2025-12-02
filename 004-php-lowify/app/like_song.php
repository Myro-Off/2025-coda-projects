<?php
// Fichier : like_item.php
// Description : Script d'action pour basculer l'état "liké" (is_liked) d'une entité (chanson, album, artiste)
//               et synchroniser l'ajout/la suppression des chansons associées dans la playlist "Coups de cœur".

require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';

$itemId = $_GET['id'] ?? null;
$type = $_GET['type'] ?? 'song'; // Entité ciblée : 'song', 'album', 'artist'

// Vérification basique de l'ID
if (!is_numeric($itemId)) {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
    exit;
}

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );

    // --- 1. INITIALISATION ET VÉRIFICATION DE L'ÉTAT ACTUEL ---

    // Trouver ou créer la playlist "Favoris" (Coups de cœur)
    $favPlaylistData = $db->executeQuery("SELECT id FROM playlist WHERE name = :name", ['name' => FAVORITES_PLAYLIST_NAME]);
    if (empty($favPlaylistData)) {
        // Création de la playlist si elle n'existe pas
        $db->executeUpdate("INSERT INTO playlist (name, nb_song, duration) VALUES (:name, 0, 0)", ['name' => FAVORITES_PLAYLIST_NAME]);
        $favPlaylistId = $db->getLastInsertId();
    } else {
        $favPlaylistId = $favPlaylistData[0]['id'];
    }

    // Déterminer l'état actuel de l'entité principale
    $table = $type;
    $currentStatusData = $db->executeQuery("SELECT is_liked FROM $table WHERE id = :id", ['id' => $itemId]);

    // Redirection 404 si l'élément n'existe pas
    if (empty($currentStatusData)) {
        exitWith404();
    }

    $isCurrentlyLiked = $currentStatusData[0]['is_liked'];
    $newStatus = $isCurrentlyLiked ? 0 : 1; // 0 (Unlike) ou 1 (Like)

    $songsToToggle = [];

    // --- 2. IDENTIFICATION DES CHANSONS À AJOUTER/SUPPRIMER ---
    if ($type === 'song') {
        // Cas chanson : on sélectionne uniquement cette chanson
        $songsToToggle = $db->executeQuery("SELECT id, duration FROM song WHERE id = :id", ['id' => $itemId]);

    } elseif ($type === 'album') {
        // Cas album : on sélectionne toutes les chansons de cet album
        $songsToToggle = $db->executeQuery("SELECT id, duration FROM song WHERE album_id = :id", ['id' => $itemId]);

    } elseif ($type === 'artist') {
        // Cas artiste : on sélectionne toutes les chansons de tous les albums de cet artiste
        $songsToToggle = $db->executeQuery("SELECT s.id, s.duration FROM song s JOIN album a ON s.album_id = a.id WHERE a.artist_id = :id", ['id' => $itemId]);
    }

    // --- 3. MISE À JOUR DE L'ENTITÉ PRINCIPALE ET SYNCHRONISATION DES FAVORIS ---

    // Mise à jour de l'état is_liked de l'entité principale
    $db->executeUpdate("UPDATE $table SET is_liked = :status WHERE id = :id", [
        'status' => $newStatus,
        'id' => $itemId
    ]);

    $songsCountChange = 0;
    $durationChange = 0;

    // Basculer l'état des chansons concernées et mettre à jour la playlist Favoris
    foreach ($songsToToggle as $song) {
        $songId = $song['id'];
        $songDuration = $song['duration'];

        // Vérification de l'existence de la chanson dans les Favoris
        $existsInFavs = $db->executeQuery(
            "SELECT COUNT(*) as count FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid",
            ['pid' => $favPlaylistId, 'sid' => $songId]
        )[0]['count'];

        if ($newStatus === 0) {
            // ACTION UNLIKE : Suppression de la chanson des Favoris
            if ($existsInFavs > 0) {
                $db->executeUpdate("DELETE FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid", ['pid' => $favPlaylistId, 'sid' => $songId]);
                $db->executeUpdate("UPDATE song SET is_liked = 0 WHERE id = :sid", ['sid' => $songId]);
                $songsCountChange--;
                $durationChange -= $songDuration;
            }
        } else {
            // ACTION LIKE : Ajout de la chanson aux Favoris
            if ($existsInFavs === 0) {
                $db->executeUpdate("INSERT INTO x_playlist_song (playlist_id, song_id) VALUES (:pid, :sid)", ['pid' => $favPlaylistId, 'sid' => $songId]);
                $db->executeUpdate("UPDATE song SET is_liked = 1 WHERE id = :sid", ['sid' => $songId]);
                $songsCountChange++;
                $durationChange += $songDuration;
            }
        }
    }

    // 4. Mise à jour finale des totaux de la playlist Favoris
    if ($songsCountChange !== 0) {
        $db->executeUpdate("UPDATE playlist SET nb_song = nb_song + :count, duration = duration + :duration WHERE id = :pid", [
            'count' => $songsCountChange,
            'duration' => $durationChange,
            'pid' => $favPlaylistId
        ]);
    }


} catch (PDOException $ex) {
    // Gestion des erreurs de base de données (ex: erreur de requête, DB down)
    if (str_contains($ex->getMessage(), 'Duplicate entry') === false) {
        exitWith500('db');
    }
}

// Redirection vers la page précédente de l'utilisateur
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
