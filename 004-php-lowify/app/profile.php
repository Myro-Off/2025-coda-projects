<?php
// Fichier : profile.php
// Description : Affiche la page des favoris de l'utilisateur, incluant les tops, les playlists likées, et l'historique d'écoute.

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';

$page = new HTMLPage("Lowify - Favoris");
$subfilter = $_GET['subfilter'] ?? 'overview';

// --- 1. GESTION DES FAVORIS ET MISE À JOUR DES STATS ---

try {
    $db = new DatabaseManager(dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4', username: 'lowify', password: 'lowifypassword');

    // Récupérer l'ID de la playlist Favoris (constante FAVORITES_PLAYLIST_NAME)
    $favData = $db->executeQuery("SELECT id FROM playlist WHERE name = :name", ['name' => FAVORITES_PLAYLIST_NAME]);

    if (empty($favData)) {
        // Création de la playlist Favoris si elle n'existe pas
        $db->executeQuery(
            "INSERT INTO playlist (name, nb_song, duration, nb_album, nb_artist, nb_playlist) VALUES (:name, 0, 0, 0, 0, 0)",
            ['name' => FAVORITES_PLAYLIST_NAME]
        );
        $favId = $db->getLastInsertId();
    } else {
        $favId = $favData[0]['id'];
    }

    // CALCUL DES COMPTEURS (Données likées par l'utilisateur)

    // Total des chansons dans Favoris (via table de liaison x_playlist_song)
    $countSongs = $db->executeQuery("SELECT COUNT(*) as total FROM x_playlist_song WHERE playlist_id = :id", ['id' => $favId]);
    $songsCount = $countSongs[0]['total'];

    // Total des albums marqués comme likés (is_liked = 1)
    $countAlbums = $db->executeQuery("SELECT COUNT(*) as total FROM album WHERE is_liked = 1");
    $albumsCount = $countAlbums[0]['total'];

    // Total des artistes marqués comme likés (is_liked = 1)
    $countArtists = $db->executeQuery("SELECT COUNT(*) as total FROM artist WHERE is_liked = 1");
    $artistsCount = $countArtists[0]['total'];

    // Total des playlists créées par l'utilisateur (toutes sauf "Favoris")
    $countPlaylists = $db->executeQuery("SELECT COUNT(*) as total FROM playlist WHERE name != :name", ['name' => FAVORITES_PLAYLIST_NAME]);
    $playlistsCount = $countPlaylists[0]['total'];


    // MISE À JOUR DE LA TABLE PLAYLIST avec les nouveaux compteurs
    $db->executeQuery(
        "UPDATE playlist SET nb_song = :nbs, nb_album = :nba, nb_artist = :nbart, nb_playlist = :nbp WHERE id = :id",
        [
            'nbs'   => $songsCount,
            'nba'   => $albumsCount,
            'nbart' => $artistsCount,
            'nbp'   => $playlistsCount,
            'id'    => $favId
        ]
    );

} catch (PDOException $e) {
    // Gestion de l'erreur de connexion ou de requête
    exitWith500('db');
}


// --- 2. LOGIQUE D'AFFICHAGE ET FILTRAGE DU CONTENU ---

$contentHTML = "";
$emptyMessage = '<p class="card-subtitle" style="text-align:center; margin-top: 50px;">Aucun élément trouvé dans cette catégorie.</p>';

// Affichage des onglets de navigation
$tabsHTML = '
    <div class="tabs-container">
        <a href="profile.php?subfilter=overview" class="tab-link ' . ($subfilter === 'overview' ? 'active' : '') . '">Vue d\'ensemble</a>
        <a href="profile.php?subfilter=songs" class="tab-link ' . ($subfilter === 'songs' ? 'active' : '') . '">Coups de cœur</a>
        <a href="profile.php?subfilter=playlists" class="tab-link ' . ($subfilter === 'playlists' ? 'active' : '') . '">Playlists</a>
        <a href="profile.php?subfilter=albums" class="tab-link ' . ($subfilter === 'albums' ? 'active' : '') . '">Albums</a>
        <a href="profile.php?subfilter=artists" class="tab-link ' . ($subfilter === 'artists' ? 'active' : '') . '">Artistes</a>
        <a href="profile.php?subfilter=history" class="tab-link ' . ($subfilter === 'history' ? 'active' : '') . '">Historique</a>
    </div>
';

// Affichage du contenu selon le filtre sélectionné (subfilter)
if ($subfilter === 'overview') {
    // VUE D'ENSEMBLE (Top 5 des dernières activités)
    $songs = $db->executeQuery(<<<SQL
        -- Sélectionne les 5 dernières chansons ajoutées aux favoris
        SELECT s.id, s.name, s.duration, s.is_liked, s.note, ar.name as artist_name, al.name as album_name, al.cover, ar.id AS artist_id, al.id AS album_id
        FROM x_playlist_song xps JOIN song s ON xps.song_id = s.id
        JOIN album al ON s.album_id = al.id JOIN artist ar ON s.artist_id = ar.id
        WHERE xps.playlist_id = :fid ORDER BY xps.id DESC LIMIT 5
    SQL, ['fid' => $favId]);

    $favArtists = $db->executeQuery("
        -- Sélectionne les 5 artistes préférés (par écoutes descendantes)
        SELECT id, name, cover, monthly_listeners, is_liked FROM artist WHERE is_liked = 1 ORDER BY monthly_listeners DESC LIMIT 5");

    $favAlbums = $db->executeQuery("
        -- Sélectionne les 5 derniers albums likés
        SELECT al.id, al.name, al.cover, ar.name AS artist_name, al.is_liked FROM album al JOIN artist ar ON al.artist_id = ar.id WHERE al.is_liked = 1 ORDER BY al.release_date DESC LIMIT 5");

    // Rendu des grilles si les données existent
    if (!empty($favArtists)) {
        $contentHTML .= '<h2 class="section-title">🎤 Artistes</h2>' . renderArtistGrid($favArtists);
    }
    if (!empty($favAlbums)) {
        $contentHTML .= '<h2 class="section-title">💿 Albums</h2>' . renderAlbumGrid($favAlbums);
    }
    if (!empty($songs)) {
        $contentHTML .= '<h2 class="section-title">🎵 Derniers Coups de cœur</h2>' . renderGlobalSongList($songs, $favId);
    }

    // Affiche un message si toutes les catégories sont vides
    if (empty($favArtists) && empty($favAlbums) && empty($songs)) {
        $contentHTML = $emptyMessage;
    }


} elseif ($subfilter === 'songs') {
    // COUPS DE CŒUR (Liste complète des titres)
    $songs = $db->executeQuery(<<<SQL
        SELECT s.id, s.name, s.duration, s.is_liked, s.note, ar.name as artist_name, al.name as album_name, al.cover, ar.id AS artist_id, al.id AS album_id
        FROM x_playlist_song xps
        JOIN song s ON xps.song_id = s.id
        JOIN album al ON s.album_id = al.id
        JOIN artist ar ON s.artist_id = ar.id
        WHERE xps.playlist_id = :fid
        ORDER BY xps.id DESC
    SQL, ['fid' => $favId]);

    // Gestion du pluriel pour le titre
    $titleSuffix = ($songsCount > 1) ? ' Coups de cœur' : ' Coup de cœur';
    $contentHTML .= '<h2 class="section-title">' . $songsCount . $titleSuffix . '</h2>';

    if (!empty($songs)) {
        // Utilise renderGlobalSongList pour l'affichage en tableau
        $contentHTML .= renderGlobalSongList($songs, $favId);
    } else {
        $contentHTML = $emptyMessage;
    }


} elseif ($subfilter === 'albums') {
    // ALBUMS FAVORIS (Liste complète)
    $albums = $db->executeQuery("SELECT al.id, al.name, al.cover, ar.name AS artist_name, al.is_liked FROM album al JOIN artist ar ON al.artist_id = ar.id WHERE al.is_liked = 1 ORDER BY al.release_date DESC");

    // Gestion du pluriel et affichage
    $titleSuffix = ($albumsCount > 1) ? ' Albums' : ' Album';
    $contentHTML .= '<h2 class="section-title">' . $albumsCount . $titleSuffix . '</h2>';

    if (!empty($albums)) {
        $contentHTML .= renderAlbumGrid($albums);
    } else {
        $contentHTML = $emptyMessage;
    }


} elseif ($subfilter === 'artists') {
    // ARTISTES FAVORIS (Liste complète)
    $artists = $db->executeQuery("SELECT id, name, cover, monthly_listeners, is_liked FROM artist WHERE is_liked = 1 ORDER BY monthly_listeners DESC");

    // Gestion du pluriel et affichage
    $titleSuffix = ($artistsCount > 1) ? ' Artistes' : ' Artiste';
    $contentHTML .= '<h2 class="section-title">' . $artistsCount . $titleSuffix . '</h2>';

    if (!empty($artists)) {
        $contentHTML .= renderArtistGrid($artists);
    } else {
        $contentHTML = $emptyMessage;
    }


} elseif ($subfilter === 'playlists') {
    // MES PLAYLISTS (Playlists utilisateur créées)
    $playlists = $db->executeQuery("SELECT * FROM playlist WHERE name != :favName ORDER BY id DESC", ['favName' => FAVORITES_PLAYLIST_NAME]);

    // Gestion du pluriel et affichage
    $titleSuffix = ($playlistsCount > 1) ? ' Playlists' : ' Playlist';
    $contentHTML .= '<h2 class="section-title">' . $playlistsCount . $titleSuffix . '</h2>';

    if (!empty($playlists)) {
        $contentHTML .= '<div class="grid-container">';

        // Carte d'action : Créer une playlist
        $contentHTML .= '
            <a href="create_playlist.php" class="card" style="justify-content:center; align-items:center; border: 2px dashed var(--border-color); background: transparent; text-align:center;">
                <div style="font-size: 40px; color: var(--primary);">+</div>
                <div style="font-weight:700; margin-top:10px;">Créer une playlist</div>
            </a>';

        // Affichage des playlists utilisateur
        foreach ($playlists as $p) {
            $contentHTML .= <<<HTML
            <a href="playlist.php?id={$p['id']}" class="card">
                <div class="card-img-container">
                    <img alt="Couverture de playlist" src="../assets/default_album.png" class="card-img">
                    <object><a href="playlist.php?id={$p['id']}&now_playing=album_{$p['id']}" class="play-btn-hover">▶</a></object>
                </div>
                <div class="card-title">{$p['name']}</div>
                <div class="card-subtitle">{$p['nb_song']} titres</div>
            </a>
HTML;
        }
        $contentHTML .= '</div>';
    } else {
        $contentHTML = $emptyMessage;
    }


} elseif ($subfilter === 'history') {
    // HISTORIQUE (Liste des 20 dernières écoutes)
    $history = $db->executeQuery(<<<SQL
        SELECT h.played_at, s.id, s.name, s.duration, s.is_liked, s.note, ar.name as artist_name, al.name as album_name, al.cover, al.id as album_id
        FROM history h
        JOIN song s ON h.song_id = s.id
        JOIN album al ON s.album_id = al.id
        JOIN artist ar ON s.artist_id = ar.id
        ORDER BY h.played_at DESC
        LIMIT 20
    SQL);

    if (!empty($history)) {
        // renderPlaylistSongList est utilisé pour afficher l'historique (mode tableau)
        $contentHTML .= '<h2 class="section-title">Historique des 20 dernières écoutes</h2>' . renderPlaylistSongList($history, $favId);
    } else {
        $contentHTML = $emptyMessage;
    }
}


// --- 3. ASSEMBLAGE FINAL DE L'INTERFACE ---

$nowPlayingSongId = $_GET['now_playing'] ?? null;
$sidebarContent = renderSidebar($db, 'profile.php');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);

$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    <div class="page-header">
        <h1 class="page-title">Favoris</h1>
    </div>

    {$tabsHTML}

    <div style="margin-top: 20px;">
        {$contentHTML}
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
