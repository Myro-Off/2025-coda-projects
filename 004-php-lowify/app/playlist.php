<?php
// Fichier : playlist.php
// Description : Affiche les détails d'une playlist (métadonnées et liste des chansons).

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';

$playlistId = $_GET['id'] ?? null;

$safePlaylistId = htmlspecialchars((string)$playlistId);

// --- 0. CONNEXION ET VÉRIFICATION ---

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );

    // 1. Récupération des données de la playlist
    $playlistData = $db->executeQuery("SELECT id, name, duration, nb_song, description, status FROM playlist WHERE id = :id", ['id' => $safePlaylistId]);

    // Gestion 404 si la playlist n'existe pas
    if (empty($playlistData)) {
        exitWith404('playlist');
    }
    $playlist = $playlistData[0];

    // Redirection si l'utilisateur tente d'accéder à la playlist Favoris via cet ID
    if ($playlist['name'] === FAVORITES_PLAYLIST_NAME) {
        header('Location: profile.php?filter=favorites&subfilter=songs');
        exit;
    }

    // Initialisation des variables et formatage des données
    $playlistName = htmlspecialchars($playlist['name']);
    $nb_song = htmlspecialchars($playlist['nb_song']);
    $duration = formatDurationMMSS($playlist['duration']);
    $playlistDescription = htmlspecialchars($playlist['description'] ?? 'Aucune description fournie.');
    $status = htmlspecialchars($playlist['status'] ?? 'private');

    // Détermination de l'affichage du statut (icône et texte)
    switch ($status) {
        case 'private':
            $statusText = "Privée";
            $statusIcon = "🔒";
            break;
        case 'collaborative':
            $statusText = "Collaborative";
            $statusIcon = "🤝";
            break;
        case 'public':
        default:
            $statusText = "Publique";
            $statusIcon = "🌐";
            break;
    }

    $page = new HTMLPage("Lowify - $playlistName");

    // 2. Récupération des chansons de la playlist
    $songs = $db->executeQuery(<<<SQL
        SELECT
            s.id, s.name, s.duration, s.is_liked, s.note, a.cover, a.name AS album_name, ar.name AS artist_name, ar.id AS artist_id, a.id AS album_id,
            DATE_SUB(NOW(), INTERVAL s.id * 5 HOUR) as added_at
        FROM x_playlist_song xps
        JOIN song s ON xps.song_id = s.id
        JOIN album a ON s.album_id = a.id
        JOIN artist ar ON s.artist_id = ar.id
        WHERE xps.playlist_id = :playlistId
        ORDER BY xps.id
    SQL, ['playlistId' => $safePlaylistId]);

} catch (PDOException $ex) {
    // Gestion de l'erreur de connexion ou de requête
    exitWith500('db');
}

// --- PRÉPARATION DES VARIABLES POUR L'AFFICHAGE ---

$TRASH_SVG_LOCAL = TRASH_SVG;

// Liens de lecture et d'action
$firstSongId = $songs[0]['id'] ?? null;
// Lien 'Tout écouter' avec le contexte actuel (playlist)
$playAllLink = $firstSongId ? "play_song.php?id={$firstSongId}&context_type=playlist&context_id=$safePlaylistId" : "#";

$editPlaylistLink = "edit_playlist.php?id=$safePlaylistId";
$deletePlaylistLink = "delete_playlist.php?id=$safePlaylistId";
$onClickDeletePlaylist = "return confirm('Êtes-vous sûr de vouloir supprimer la playlist &quot;$playlistName&quot; ? Cette action est définitive.')";

// Rendu de la liste des chansons (utilise l'utilitaire de rendu de tableau)
$songsListHTML = renderPlaylistSongList($songs, $safePlaylistId);


// --- GESTION DE L'AFFICHAGE PLAYLIST VIDE OU REMPLIE ---

$songsSectionHTML = "";

if (empty($songs)) {
    // CAS PLAYLIST VIDE : Affiche un CTA pour ajouter des titres
    $songsSectionHTML = <<<HTML
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin-top: 60px; text-align: center;">
            <div style="font-size: 60px; margin-bottom: 20px;">🎵</div>
            <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 10px; color: var(--text-main);">C'est bien vide ici...</h2>
            <p style="color: var(--text-secondary); font-size: 14px; max-width: 400px; margin-bottom: 30px;">
                Explore nos recommandations et ajoute des titres à ta playlist pour commencer l'écoute.
            </p>
            <a href="search.php" class="duo-button" style="background-color: var(--text-main); color: var(--bg-dark); padding: 15px 30px; border-radius: 50px; font-weight: 700; font-size: 16px; text-transform: uppercase;">
                Rechercher des titres
            </a>
        </div>
HTML;

} else {
    // CAS PLAYLIST REMPLIE : Affiche la barre de contrôle et la liste des titres
    $songsSectionHTML = <<<HTML
        <div class="controls-bar" style="margin-bottom: 20px;">
            <a href="search.php" class="duo-button" style="background:none; border:1px solid var(--text-secondary); color:var(--text-main); font-size:14px; padding: 10px 15px;">
               ➕ Ajouter des titres
            </a>
            <input type="text" placeholder="Rechercher (non fonctionnel)" class="search-in-playlist">
        </div>
        {$songsListHTML}
HTML;
}


// --- ASSEMBLAGE FINAL DE L'INTERFACE ---

$nowPlayingSongId = $_GET['now_playing'] ?? null;
$sidebarContent = renderSidebar($db, 'playlist.php');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);

$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    
    <div style="display: flex; gap: 40px; align-items: flex-end; margin-bottom: 50px;">
        
        <div style="width: 250px; height: 250px; border-radius: 4px; flex-shrink: 0; background: #8C52FF; display: flex; justify-content: center; align-items: center; box-shadow: 0 8px 24px rgba(0,0,0,0.5);">
             <div style="font-size: 80px; color: white;">🎵</div>
        </div>

        <div>
            <div style="color: var(--text-secondary); font-size: 12px; font-weight: 700; margin-bottom: 10px; text-transform: uppercase;">Playlist</div>
            <h1 class="page-title" style="font-size: 70px; font-weight: 900; margin: 0; line-height: 1;">$playlistName</h1>
            
            <p style="font-size: 14px; color: var(--text-secondary); margin-top: 15px;">
                {$statusIcon} <span style="font-weight: 600; color: var(--text-main);">{$statusText}</span> • Créée par Adam
            </p>
            <p style="font-size: 14px; color: var(--text-secondary); margin-top: 5px; max-width: 600px;">
                {$playlistDescription}
            </p>
            
            <p style="font-size: 14px; color: var(--text-secondary); margin-top: 10px;">
                <span style="font-weight: 600; color: var(--text-main);">$nb_song titres</span> • {$duration}
            </p>
            
            <div style="margin-top: 30px; display: flex; gap: 15px; align-items: center;">
                 <a href="{$playAllLink}" class="big-play-btn" style="background-color: var(--primary); width: 55px; height: 55px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: black; font-size: 24px; text-decoration: none; pointer-events: auto;">
                    ▶
                 </a>
                 
                 <a href="{$editPlaylistLink}" class="header-edit-button">
                    ✏️ Modifier
                 </a>
                 
                 <a href="{$deletePlaylistLink}" onclick="{$onClickDeletePlaylist}" class="header-delete-btn" title="Supprimer la playlist">
                    {$TRASH_SVG_LOCAL}
                 </a>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 40px;">
        {$songsSectionHTML}
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
