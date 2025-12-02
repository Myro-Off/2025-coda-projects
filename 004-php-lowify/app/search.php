<?php
// Fichier: search.php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';
require_once 'inc/search_bar_template.php';

$page = new HTMLPage("Lowify - Recherche");

try {
    $db = new DatabaseManager(dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4', username: 'lowify', password: 'lowifypassword');

    // Récupérer l'ID des Favoris (nécessaire pour les liens de playlist/like)
    $favData = $db->executeQuery("SELECT id FROM playlist WHERE name = :name", ['name' => FAVORITES_PLAYLIST_NAME]);
    $favId = $favData[0]['id'] ?? null;

} catch (PDOException $ex) {
    exitWith500('db');
}

$query = $_GET['query'] ?? '';
$filter = $_GET['filter'] ?? 'all';
$search_term = "%" . trim($query) . "%";
$nowPlayingSongId = $_GET['now_playing'] ?? null;
$query_encoded = urlencode($query);


// --- LOGIQUE PRINCIPALE ---

$displayHTML = '';
$searchBarHTML = renderSearchBar($query, $filter);
$hasResults = false;

if (empty(trim($query))) {
    // CAS 1: Pas de recherche -> Affichage "Vide"
    $displayHTML = <<<HTML
        <div style="text-align: center; margin-top: 100px; color: var(--text-secondary);">
            <div style="font-size: 60px; margin-bottom: 20px;">🔍</div>
            <h2 style="color: var(--text-main);">Lancez une recherche</h2>
            <p>Trouvez vos artistes, albums ou titres préférés.</p>
        </div>
HTML;
} else {
    // CAS 2: Recherche active -> Affichage des résultats

    // Titre des résultats
    $displayHTML .= "<h1 class='page-title' style='margin-bottom: 10px;'>Résultats pour \"" . htmlspecialchars($query) . "\"</h1>";

    // 1. On prépare les classes actives AVANT le bloc HTML
    $clsAll = ($filter === 'all') ? 'active' : '';
    $clsArtist = ($filter === 'artist') ? 'active' : '';
    $clsAlbum = ($filter === 'album') ? 'active' : '';
    $clsSong = ($filter === 'song') ? 'active' : '';

    // 2. On injecte simplement les variables
    $displayHTML .= <<<NAV
        <div class="tabs-container">
            <a href="search.php?query=$query_encoded&filter=all" class="tab-link $clsAll">Tout</a>
            <a href="search.php?query=$query_encoded&filter=artist" class="tab-link $clsArtist">Artistes</a>
            <a href="search.php?query=$query_encoded&filter=album" class="tab-link $clsAlbum">Albums</a>
            <a href="search.php?query=$query_encoded&filter=song" class="tab-link $clsSong">Titres</a>
        </div>
    NAV;

    // --- GESTION DES LIMITES ---
    // Si on est sur l'onglet "Tout", on limite. Sinon, on affiche tout.
    $limitArtistsAlbums = ($filter === 'all') ? "LIMIT 5" : "";
    $limitSongs = ($filter === 'all') ? "LIMIT 10" : "";


    // 1. Artistes
    if ($filter === 'all' || $filter === 'artist') {
        $sql = "SELECT id, name, cover, monthly_listeners, is_liked FROM artist WHERE name LIKE :search_term " . $limitArtistsAlbums;
        $artists = $db->executeQuery($sql, ['search_term' => $search_term]);

        if (!empty($artists)) {
            // Afficher le header "Voir tout" seulement si on est en mode "Tout"
            $header = ($filter === 'all')
                ? '<div class="section-header"><h2 class="section-title">🎤 Artistes</h2><a href="search.php?query=' . $query_encoded . '&filter=artist" class="see-all-btn">Voir tout</a></div>'
                : '<h2 class="section-title">🎤 Artistes</h2>';

            $displayHTML .= $header . renderArtistGrid($artists);
            $hasResults = true;
        }
    }

    // 2. Albums
    if ($filter === 'all' || $filter === 'album') {
        $sql = "SELECT a.id, a.name, a.cover, ar.name AS artist_name, a.is_liked FROM album a JOIN artist ar ON a.artist_id = ar.id WHERE a.name LIKE :search_term " . $limitArtistsAlbums;
        $albums = $db->executeQuery($sql, ['search_term' => $search_term]);

        if (!empty($albums)) {
            $header = ($filter === 'all')
                ? '<div class="section-header"><h2 class="section-title">💿 Albums</h2><a href="search.php?query=' . $query_encoded . '&filter=album" class="see-all-btn">Voir tout</a></div>'
                : '<h2 class="section-title">💿 Albums</h2>';

            $displayHTML .= $header . renderAlbumGrid($albums);
            $hasResults = true;
        }
    }

    // 3. Chansons
    if ($filter === 'all' || $filter === 'song') {
        $songs = $db->executeQuery(<<<SQL
            SELECT s.id, s.name, s.duration, s.is_liked, s.note, a.name AS album_name, a.cover, ar.name AS artist_name, ar.id AS artist_id, a.id AS album_id,
            DATE_SUB(NOW(), INTERVAL s.id * 5 HOUR) as added_at
            FROM song s JOIN album a ON s.album_id = a.id JOIN artist ar ON s.artist_id = ar.id
            WHERE s.name LIKE :search_term
            $limitSongs
        SQL, ['search_term' => $search_term]);

        if (!empty($songs)) {
            $header = ($filter === 'all')
                ? '<div class="section-header"><h2 class="section-title">🎵 Titres</h2><a href="search.php?query=' . $query_encoded . '&filter=song" class="see-all-btn">Voir tout</a></div>'
                : '<h2 class="section-title">🎵 Titres</h2>';

            $displayHTML .= $header . renderGlobalSongList($songs, $favId);
            $hasResults = true;
        }
    }
}

// --- ASSEMBLAGE FINAL ---

$sidebarContent = renderSidebar($db, 'search.php');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);

$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    {$searchBarHTML}
    {$displayHTML}
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
