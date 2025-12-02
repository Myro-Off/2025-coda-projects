<?php
// Fichier : add_to_playlist.php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';

// --- 1. INITIALISATION & DONNÉES ---
$page = new HTMLPage("Ajouter à Playlist");
$songId = $_REQUEST['id'] ?? null;

try {
    $db = new DatabaseManager(dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4', username: 'lowify', password: 'lowifypassword');
    $songData = $db->executeQuery("SELECT name, duration FROM song WHERE id = :id", ['id' => $songId]);
    if (empty($songData)) {
        exitWith404('song');
    }
    $song = $songData[0];
    // Exclure la playlist Favoris des options d'ajout manuel
    $allPlaylists = $db->executeQuery("SELECT id, name FROM playlist WHERE name != :favName ORDER BY name", ['favName' => FAVORITES_PLAYLIST_NAME]);
} catch (PDOException $ex) {
    exitWith500('db');
}

$message = "";

// --- 2. TRAITEMENT POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playlistId = $_POST['playlist_id'] ?? null;

    if (empty($playlistId) || !is_numeric($playlistId)) {
        $message = '<p style="color: #E33E3E;">Veuillez sélectionner une playlist.</p>';
    } else {
        try {
            // 1. Vérifier si la chanson est déjà dans la playlist
            $exists = $db->executeQuery("SELECT COUNT(*) FROM x_playlist_song WHERE playlist_id = :pid AND song_id = :sid", ['pid' => $playlistId, 'sid' => $songId])[0]['COUNT(*)'];

            if ($exists == 0) {
                // 2. Ajouter la chanson à x_playlist_song
                $db->executeQuery("INSERT INTO x_playlist_song (playlist_id, song_id) VALUES (:pid, :sid)", ['pid' => $playlistId, 'sid' => $songId]);

                // 3. Mettre à jour la playlist (nb_song et duration)
                $db->executeQuery("UPDATE playlist SET nb_song = nb_song + 1, duration = duration + :duration WHERE id = :pid", ['duration' => $song['duration'], 'pid' => $playlistId]);

                header("Location: playlist.php?id=$playlistId");
                exit;
            } else {
                $message = '<p style="color: #E33E3E;">Cette chanson est déjà dans cette playlist.</p>';
            }

        } catch (PDOException $ex) {
            $message = '<p style="color: #E33E3E;">Erreur lors de l\'ajout: ' . $ex->getMessage() . '</p>';
        }
    }
}

// --- 3. PRÉPARATION DES MORCEAUX HTML RÉUTILISABLES ---
$songName = htmlspecialchars($song['name']);

$optionsHTML = "";
if (!empty($allPlaylists)) {
    foreach ($allPlaylists as $p) {
        $optionsHTML .= '<option value="' . $p['id'] . '">' . htmlspecialchars($p['name']) . '</option>';
    }
} else {
    $optionsHTML = '<option value="">Aucune playlist trouvée (Veuillez en créer une)</option>';
}

$disabledAttribute = empty($allPlaylists) ? 'disabled' : '';

// --- ASSEMBLAGE FINAL ---
$nowPlayingSongId = $_GET['now_playing'] ?? null;
$sidebarContent = renderSidebar($db, 'add_to_playlist.php');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);

$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    <div class="page-header">
        <h1 class="page-title">Ajouter "$songName" à une Playlist</h1>
    </div>
    
    <div style="max-width: 500px; padding: 30px; background-color: var(--bg-card); border-radius: var(--radius-l); box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
        {$message}
        <form method="POST">
            <input type="hidden" name="id" value="$songId">
            
            <label for="playlist_id" style="display: block; margin-bottom: 10px; font-weight: 600;">Sélectionner une playlist :</label>
            <select name="playlist_id" id="playlist_id" required
                   style="width: 100%; padding: 12px; border-radius: var(--radius-s); border: none;
                          background-color: var(--bg-dark); color: var(--text-main); margin-bottom: 20px; font-size: 16px;"
                          $disabledAttribute>
                <option value="">-- Choisir une playlist --</option>
                {$optionsHTML}
            </select>
            
            <button type="submit" class="duo-button" style="width: 100%; padding: 12px; border-radius: 50px; font-size: 16px;" $disabledAttribute>
                Ajouter à la Playlist
            </button>
        </form>
    </div>
    
    <a href="javascript:history.back()" style="display: block; margin-top: 30px; color: var(--text-secondary); text-decoration: none;">&larr; Retour</a>
</div>
MAIN_CONTENT;


$html = <<<HTML
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<div class="app-container">
    {$sidebarContent}
    {$mainContentHTML}
</div>
{$playerHTML}
HTML;

$page->addContent($html);
echo $page->render();
