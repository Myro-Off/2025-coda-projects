<?php
// Fichier : index.php
// Description : Page d'accueil affichant les tops artistes et albums.

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';
require_once 'inc/search_bar_template.php';

// Création de l'objet page HTML
$page = new HTMLPage("Lowify - Accueil");

// Tentative de connexion à la base de données
try {
    $db = new DatabaseManager(dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4', username: 'lowify', password: 'lowifypassword');
} catch (PDOException $ex) {
    // Gestion de l'erreur de connexion via le gestionnaire 500
    exitWith500('db');
}

// Récupération de l'ID de la chanson en cours de lecture (pour le player bar)
$nowPlayingSongId = $_GET['now_playing'] ?? null;


// --- LOGIQUE D'AFFICHAGE DES SECTIONS (GRILLES) ---

$displayHTML = '<h1 class="page-title">Bienvenue sur Lowify</h1>';

// 1. Top Trending Artistes : classés par nombre d'écoutes mensuelles (Top 5)
$trendingArtists = $db->executeQuery("SELECT id, name, cover, monthly_listeners, is_liked FROM artist ORDER BY monthly_listeners DESC LIMIT 5");
$displayHTML .= '
    <div class="section-header">
        <h2 class="section-title">🔥 Top Trending Artistes</h2>
        <a href="artists.php" class="see-all-btn">Voir tout</a>
    </div>' . renderArtistGrid($trendingArtists);

// 2. Top Sorties : Albums les plus récents (Top 5)
$recentAlbums = $db->executeQuery("SELECT id, name, cover, YEAR(release_date) as year, is_liked FROM album ORDER BY release_date DESC LIMIT 5");
$displayHTML .= '
    <div class="section-header">
        <h2 class="section-title">💿 Top Sorties Récentes</h2>
    </div>' . renderAlbumGrid($recentAlbums);

// 3. Top Albums Notés : classés par note moyenne des chansons de l'album (Top 5)
$topAlbums = $db->executeQuery("
    SELECT
        a.id,
        a.name,
        a.cover,
        AVG(s.note) AS avg_note,
        ar.name AS artist_name,
        a.is_liked
    FROM album a
    JOIN song s ON a.id = s.album_id
    JOIN artist ar ON a.artist_id = ar.id
    GROUP BY a.id, a.name, a.cover, ar.name, a.is_liked
    ORDER BY avg_note DESC
    LIMIT 5
");
$displayHTML .= '
    <div class="section-header">
        <h2 class="section-title">⭐ Top Albums Notés</h2>
    </div>' . renderAlbumGrid($topAlbums);


// --- ASSEMBLAGE ET RENDU FINAL ---

// Rendu des composants de l'interface
$sidebarContent = renderSidebar($db);
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);
$searchBarHTML = renderSearchBar();

// Construction du contenu principal
$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    {$searchBarHTML}
    {$displayHTML}
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
