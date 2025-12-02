<?php
// Fichier : album.php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';

$albumId = $_GET['id'] ?? null;

try {
    $db = new DatabaseManager(dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4', username: 'lowify', password: 'lowifypassword');

    // 1. Récupération des données de l'album
    $albumData = $db->executeQuery(<<<SQL
        SELECT a.*, ar.name AS artist_name, ar.id AS artist_id
        FROM album a
        JOIN artist ar ON a.artist_id = ar.id
        WHERE a.id = :id
    SQL, ['id' => $albumId]);

    // 2. Vérification de l'existence de l'album
    if (empty($albumData)) {
        exitWith404('album');
    }

    $album = $albumData[0];

    // Données de l'album
    $albumName = htmlspecialchars($album['name']);
    $artistName = htmlspecialchars($album['artist_name']);
    $artistId = htmlspecialchars($album['artist_id']);
    $releaseYear = date('Y', strtotime($album['release_date']));
    $albumCover = getCoverAttributes($album['cover'] ?? '', DEFAULT_ALBUM_COVER);

    $isAlbumLiked = $album['is_liked'] ?? 0;
    $HEART_FULL_SVG_LOCAL = HEART_FULL_SVG;
    $HEART_EMPTY_SVG_LOCAL = HEART_EMPTY_SVG;
    $likeSVG = $isAlbumLiked ? $HEART_FULL_SVG_LOCAL : $HEART_EMPTY_SVG_LOCAL;
    $likeClass = $isAlbumLiked ? 'active' : '';

    $page = new HTMLPage("Lowify - $albumName");

    // 4. Récupération des chansons
    $songs = $db->executeQuery(<<<SQL
        SELECT
            s.id, s.name, s.duration, s.note, s.is_liked,
            a.cover, a.name AS album_name,
            ar.id AS artist_id, ar.name AS artist_name, a.id AS album_id,
            DATE_SUB(NOW(), INTERVAL s.id * 5 HOUR) as added_at
        FROM song s
        JOIN album a ON s.album_id = a.id
        JOIN artist ar ON s.artist_id = ar.id
        WHERE s.album_id = :albumId
        ORDER BY s.id
    SQL, ['albumId' => $albumId]);

    // Stats pour l'en-tête
    $nbSongs = count($songs);
    $totalDurationSeconds = array_reduce($songs, function($carry, $item) {
        return $carry + $item['duration'];
    }, 0);
    $totalDuration = formatDurationHHMMSS($totalDurationSeconds);

} catch (PDOException $ex) {
    // En cas d'erreur technique (BDD plantée), on redirige vers une erreur 500 générique
    // (Le vinyl rouge avec animation cassée)
    header("Location: error.php?code=500&message=" . urlencode("Erreur système : " . $ex->getMessage()));
    exit;
}

// --- RENDU ---

$songsHTML = renderGlobalSongList($songs, $albumId);

$firstSongId = $songs[0]['id'] ?? null;
$playAllLink = $firstSongId ? "play_song.php?id=$firstSongId&context_type=album&context_id=$albumId" : "#";


// --- ASSEMBLAGE FINAL ---

$nowPlayingSongId = $_GET['now_playing'] ?? null;
$sidebarContent = renderSidebar($db, 'album.php');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);

$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    
    <div style="display: flex; gap: 40px; align-items: flex-end; margin-bottom: 50px;">
        
        <div style="position: relative; width: 250px; height: 250px; border-radius: 8px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.5); flex-shrink: 0;">
            <img src="{$albumCover['src']}" alt="$albumName" style="width: 100%; height: 100%; object-fit: cover;" {$albumCover['onerror']}>
        </div>

        <div style="flex-grow: 1;">
            <div style="color: var(--text-secondary); font-size: 12px; font-weight: 700; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Album</div>
            <h1 class="page-title" style="font-size: 60px; margin: 0; line-height: 1.1;">$albumName</h1>
            
            <div style="display: flex; align-items: center; gap: 8px; margin-top: 20px; font-size: 14px; color: var(--text-main);">
                <img alt="" src="{$albumCover['src']}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;" {$albumCover['onerror']}>
                <a href="artist.php?id=$artistId" style="font-weight: 700; text-decoration: none; color: white;">$artistName</a>
                <span style="color: var(--text-secondary);">•</span>
                <span style="color: var(--text-secondary);">$releaseYear</span>
                <span style="color: var(--text-secondary);">•</span>
                <span style="color: var(--text-secondary);">$nbSongs titres, $totalDuration</span>
            </div>
            
            <div style="margin-top: 30px; display: flex; align-items: center; gap: 15px;">
                <a href="$playAllLink" class="big-play-btn" style="background-color: var(--primary); width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; color: black; text-decoration: none; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                    ▶
                </a>
                
                <a href="like_item.php?id=$albumId&type=album" class="header-action-btn $likeClass" title="Ajouter aux favoris">
                    {$likeSVG}
                </a>
                
                <button class="header-action-btn" title="Options">···</button>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 30px;">
        {$songsHTML}
    </div>

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
