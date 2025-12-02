<?php
// Fichier : artists.php
// Description : Affiche la liste complète de tous les artistes disponibles, triés par popularité.

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';
require_once 'inc/search_bar_template.php';

$page = new HTMLPage("Lowify - Tous les Artistes");

// Tentative de connexion à la base de données
try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );
} catch (PDOException $ex) {
    // Gestion de l'erreur de connexion (500)
    exitWith500('db');
}

$nowPlayingSongId = $_GET['now_playing'] ?? null;
$query = $_GET['query'] ?? '';

// Redirection vers la page de recherche si un terme est présent dans l'URL
if (!empty(trim($query))) {
    header('Location: search.php?query=' . urlencode($query) . '&filter=artist');
    exit;
}

// EXÉCUTION DE LA REQUÊTE : Sélection de tous les artistes, triés par nombre d'écoutes mensuelles
$allArtists = $db->executeQuery("SELECT id, name, cover, monthly_listeners, is_liked FROM artist ORDER BY monthly_listeners DESC");
// Rendu de la grille d'artistes via la fonction utilitaire
$artistsHTML = renderArtistGrid($allArtists);


// --- ASSEMBLAGE FINAL DE L'INTERFACE ---

// Rendu des composants de l'interface
$sidebarContent = renderSidebar($db, 'artists.php');
// La barre de recherche est rendue sans valeur de recherche active ici
$searchBarHTML = renderSearchBar($query, 'artist');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);


// Construction du contenu principal
$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    {$searchBarHTML}
    <div class="page-header">
        <h1 class="page-title">Tous les Artistes</h1>
    </div>

    {$artistsHTML}
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
