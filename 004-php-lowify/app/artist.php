<?php
// Fichier : artist.php
// Description : Affiche la page détaillée d'un artiste, incluant sa biographie, ses top titres et sa discographie.

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';

$artistId = $_GET['id'] ?? null;

// Tentative de connexion et récupération des données de l'artiste
try {
    $db = new DatabaseManager(dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4', username: 'lowify', password: 'lowifypassword');

    // Récupération des données de l'artiste
    $artistData = $db->executeQuery("SELECT * FROM artist WHERE id = :id", ['id' => $artistId]);

    // Gestion 404 si l'ID n'est pas trouvé
    if (empty($artistData)) {
        exitWith404('artist');
    }
    $artist = $artistData[0];

} catch (PDOException $ex) {
    // Gestion de l'erreur de connexion ou de requête critique (500)
    exitWith500('db');
}

// --- 1. DONNÉES ARTISTE ---
$artistName = htmlspecialchars($artist['name']);
$page = new HTMLPage("Lowify - $artistName");

// Formatage des auditeurs et préparation de la couverture
$listeners = formatMonthlyListeners($artist['monthly_listeners']);
$artistCover = getCoverAttributes($artist['cover'] ?? '', DEFAULT_ARTIST_COVER);
$bio = nl2br(htmlspecialchars($artist['biography'] ?? ''));

// Détermination de l'état "Liké"
$isArtistLiked = $artist['is_liked'] ?? 0;
$HEART_FULL_SVG_LOCAL = HEART_FULL_SVG;
$HEART_EMPTY_SVG_LOCAL = HEART_EMPTY_SVG;
$likeSVG = $isArtistLiked ? $HEART_FULL_SVG_LOCAL : $HEART_EMPTY_SVG_LOCAL;
$likeClass = $isArtistLiked ? 'active' : '';

// --- 2. RÉCUPÉRATION DES DONNÉES SECONDAIRES ---

// Top Titres (Max 5, triés par note/popularité)
$topSongs = [];
try {
    $topSongs = $db->executeQuery(<<<SQL
        SELECT s.id, s.name, s.duration, s.note, s.is_liked, a.cover, a.name AS album_name, ar.id AS artist_id, ar.name AS artist_name, a.id AS album_id
        FROM song s
        JOIN album a ON s.album_id = a.id
        JOIN artist ar ON s.artist_id = ar.id
        WHERE ar.id = :artistId
        ORDER BY s.note DESC
        LIMIT 5
    SQL, ['artistId' => $artistId]);
} catch (PDOException $ex) { /* Erreur silencieuse de requête */ }

// Albums de l'artiste (Discographie complète)
$albums = [];
try {
    $albums = $db->executeQuery(<<<SQL
        SELECT
            album.id,
            album.name,
            album.cover,
            YEAR(album.release_date) as release_year,
            album.is_liked,
            ar.name as artist_name
        FROM album
        JOIN artist ar ON album.artist_id = ar.id
        WHERE album.artist_id = :id
        ORDER BY album.release_date DESC
    SQL, ['id' => $artistId]);
} catch (PDOException $ex) { /* Erreur silencieuse de requête */ }


// --- 3. RENDU VIA UTILS ---

// Rendu du tableau des top titres (contexte: artiste)
$topSongsHTML = renderGlobalSongList($topSongs, $artistId);
// Rendu de la grille des albums
$albumsHTML = renderAlbumGrid($albums);

// Lien de lecture principale (lance le premier titre populaire)
$firstSongId = $topSongs[0]['id'] ?? null;
$playAllLink = $firstSongId ? "play_song.php?id=$firstSongId&context_type=artist&context_id=$artistId" : "#";


// --- ASSEMBLAGE FINAL DE L'INTERFACE ---
$nowPlayingSongId = $_GET['now_playing'] ?? null;
$sidebarContent = renderSidebar($db, 'artist.php');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);

$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    
    <div style="display: flex; gap: 40px; align-items: flex-end; margin-bottom: 50px;">
        <div style="position: relative; width: 220px; height: 220px; border-radius: 50%; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.5); flex-shrink: 0;">
            <img src="{$artistCover['src']}" alt="$artistName" style="width: 100%; height: 100%; object-fit: cover;" {$artistCover['onerror']}>
        </div>

        <div style="flex-grow: 1;">
            <div style="color: var(--text-secondary); font-size: 12px; font-weight: 700; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Artiste</div>
            <h1 class="page-title" style="font-size: 80px; margin: 0; line-height: 1;">$artistName</h1>
            
            <p style="color: var(--text-secondary); font-size: 14px; margin-top: 10px; margin-bottom: 15px;">
                <span style="color: var(--text-main); font-weight: 700;">$listeners</span> auditeurs mensuels
            </p>
            
            <div style="font-size: 14px; color: var(--text-secondary); max-width: 800px; line-height: 1.5; margin-bottom: 25px;">
                {$bio}
            </div>
            
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="{$playAllLink}" class="big-play-btn" style="background-color: var(--primary);">
                    ▶
                </a>
                
                <a href="like_item.php?id={$artistId}&type=artist" class="header-action-btn {$likeClass}" title="Ajouter aux favoris">
                    {$likeSVG}
                </a>
                
                <button class="header-action-btn" title="Options">···</button>
            </div>
        </div>
    </div>
    
    <h2 class="section-title" style="margin-bottom: 20px;">Populaires</h2>
    {$topSongsHTML}

    <h2 class="section-title" style="margin-top: 50px;">Discographie</h2>
    {$albumsHTML}

</div>
MAIN_CONTENT;

// Assemblage des éléments de la page
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
