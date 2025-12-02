<?php
// Fichier : inc/sidebar_template.php

require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';

/**
 * Récupère la liste des playlists créées par l'utilisateur.
 * Exclut la playlist système "Favoris".
 *
 * @param DatabaseManager $db Instance du gestionnaire de base de données
 * @return array Liste des playlists (id, name)
 */
function getSidebarPlaylists(DatabaseManager $db): array {
    try {
        return $db->executeQuery(
            "SELECT id, name FROM playlist WHERE name != :favName ORDER BY name",
            ['favName' => FAVORITES_PLAYLIST_NAME]
        );
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Génère le code HTML de la barre latérale (Sidebar).
 *
 * @param DatabaseManager $db Instance du gestionnaire de base de données
 * @param string $currentPage Nom de la page actuelle pour gérer la classe 'active'
 * @return string HTML complet de la sidebar
 */
function renderSidebar($db, string $currentPage = 'index.php'): string {

    // --- 1. Récupération des données ---
    $userPlaylists = getSidebarPlaylists($db);

    // On récupère les paramètres d'URL pour savoir exactement où on est
    $currentId = $_GET['id'] ?? null;
    $currentSubfilter = $_GET['subfilter'] ?? '';

    $heartSvg = HEART_FULL_SVG;
    $searchSvg = SEARCH_SVG;
    $artistSvg = ARTIST_SVG;
    $homeSvg = HOME_SVG;
    $musicSvg = MUSIC_NOTE_SVG;
    // --- 2. Configuration du Menu Principal ---
    // C'est ici qu'on définit l'ordre des boutons du haut
    $mainMenu = [
        [
            'label'  => "$homeSvg Accueil",
            'url'    => 'index.php',
            'active' => $currentPage === 'index.php'
        ],
        [
            'label'  => "$artistSvg Artistes",
            'url'    => 'artists.php',
            'active' => in_array($currentPage, ['artists.php', 'artist.php'])
        ],
        [
            'label'  => "$heartSvg favoris",
            'url'    => 'profile.php',
            'active' => $currentPage === 'profile.php' && $currentSubfilter !== 'songs'
        ],
        [
            'label'  => "$searchSvg Recherche",
            'url'    => 'search.php',
            'active' => $currentPage === 'search.php'
        ]
    ];


    // --- 3. Construction du HTML du Menu Principal ---
    $mainMenuHTML = '';
    foreach ($mainMenu as $item) {
        $activeClass = $item['active'] ? 'active' : '';

        $mainMenuHTML .= sprintf(
            '<a href="%s" class="nav-link %s">%s</a>',
            $item['url'],
            $activeClass,
            $item['label']
        );
    }


    // --- 4. Construction de la section Playlists ---
    $playlistsHTML = "";

    // A. Lien spécial "Coups de cœur" (La playlist des chansons likées)
    // On considère ce lien actif uniquement si on regarde spécifiquement la liste des chansons
    $isLovedTracksActive = ($currentPage === 'profile.php' && $currentSubfilter === 'songs');
    $lovedTracksClass = $isLovedTracksActive ? 'active' : '';

    $playlistsHTML .= <<<HTML
        <a href="profile.php?filter=favorites&subfilter=songs" class="nav-link {$lovedTracksClass}">
            <span style="transform: translateY(2px)">$heartSvg</span><span> Coups de cœur</span>
        </a>
HTML;

    // B. Liste des playlists persos de l'utilisateur
    foreach ($userPlaylists as $p) {
        $pName = htmlspecialchars($p['name']);
        $pId = htmlspecialchars($p['id']);

        // Active si on est sur la page playlist.php avec le bon ID
        $isActive = ($currentPage === 'playlist.php' && $currentId == $pId) ? 'active' : '';

        $playlistsHTML .= <<<HTML
            <a href="playlist.php?id=$pId" class="nav-link $isActive">
                <span style="transform: translateY(2px)">$musicSvg</span><span>$pName</span>
            </a>
HTML;
    }


    // --- 5. Assemblage et Rendu Final ---
    return <<<HTML
    <div class="sidebar">
        <div class="logo">
            <span style="color:var(--primary)">IIII</span> LOWIFY
        </div>
        
        <nav>
            {$mainMenuHTML}
        </nav>

        <div class="nav-section-title" style="display: flex; justify-content: space-between; align-items: center; padding-right: 10px;">
            <span>VOS PLAYLISTS</span>
            <a href="create_playlist.php" class="add-playlist-btn" title="Créer une playlist">＋</a>
        </div>
        
        <nav>
            {$playlistsHTML}
        </nav>
    </div>
HTML;
}

/**
 * Génère le code HTML de la barre de lecture (Player) fixé en bas.
 * Utilise la SESSION pour persister l'état entre les pages.
 *
 * @param DatabaseManager $db Instance du gestionnaire de base de données
 * @param mixed $ignored Paramètre obsolète (gardé pour compatibilité)
 * @return string HTML du player
 */
function renderPlayerBar($db, $ignored = null): string {

    // 1. Initialisation des variables avec des valeurs par défaut (Player vide)
    $songId = $_SESSION['now_playing'] ?? null;

    // État par défaut (Pas de musique)
    $playerData = [
        'name'      => "Aucune lecture",
        'artist'    => "Sélectionnez un titre",
        'cover'     => '../assets/default_song.png',
        'duration'  => "--:--",
        'likeClass' => "",
        'likeSVG'   => HEART_EMPTY_SVG,
        'isPlaying' => false,
        'prevLink'  => "#",
        'nextLink'  => "#"
    ];

    // 2. Si une chanson est en cours, on récupère ses infos en BDD
    if ($songId) {
        try {
            $sql = <<<SQL
                SELECT s.*, al.cover, ar.name AS artist_name
                FROM song s
                JOIN album al ON s.album_id = al.id
                JOIN artist ar ON s.artist_id = ar.id
                WHERE s.id = :id
            SQL;

            $result = $db->executeQuery($sql, ['id' => $songId]);

            if (!empty($result)) {
                $song = $result[0];

                // Traitement de la couverture
                $coverAttrs = getCoverAttributes($song['cover'] ?? '', DEFAULT_SONG_COVER);

                // Mise à jour des données du player
                $playerData['name']      = htmlspecialchars($song['name']);
                $playerData['artist']    = htmlspecialchars($song['artist_name']);
                $playerData['cover']     = $coverAttrs['src'];
                $playerData['duration']  = formatDurationMMSS($song['duration']);
                $playerData['likeClass'] = $song['is_liked'] ? 'active' : '';
                $playerData['likeSVG']   = $song['is_liked'] ? HEART_FULL_SVG : HEART_EMPTY_SVG;
                $playerData['isPlaying'] = true;

                // Gestion de la navigation (Précédent / Suivant) via le Contexte en Session
                $ctxType = $_SESSION['context_type'] ?? 'all';
                $ctxId   = $_SESSION['context_id'] ?? null;

                $nav = getNextPrevSongs($db, $songId, $ctxType, $ctxId);

                // Construction des liens de navigation
                $baseNavUrl = "play_song.php?context_type={$ctxType}&context_id={$ctxId}&id=";
                $playerData['prevLink'] = $nav['prev'] ? $baseNavUrl . $nav['prev'] : "#";
                $playerData['nextLink'] = $nav['next'] ? $baseNavUrl . $nav['next'] : "#";
            }
        } catch (PDOException $e) {
            // Ignorer silencieusement les erreurs de lecture pour ne pas casser le footer
        }
    }

    // 3. Préparation des classes CSS dynamiques
    // Si aucune musique, on grise les contrôles (classe 'disabled')
    $linkOpacity   = $playerData['isPlaying'] ? "" : "opacity: 0.5; pointer-events: none;";
    $playBtnClass  = $playerData['isPlaying'] ? "ctrl-btn play" : "ctrl-btn play disabled";
    $likeActionUrl = $playerData['isPlaying'] ? "like_item.php?id={$songId}&type=song" : "#";

    // 4. Rendu HTML du Player
    return <<<PLAYER
    <div class="player-bar">
        <div class="player-left">
            <img alt="Cover" src="{$playerData['cover']}" class="player-cover" onerror="this.src='../assets/default_song.png'">
            <div class="player-info">
                <h4>{$playerData['name']}</h4>
                <p>{$playerData['artist']}</p>
            </div>
            <a href="{$likeActionUrl}" class="player-like {$playerData['likeClass']}" style="{$linkOpacity}">
                {$playerData['likeSVG']}
            </a>
        </div>

        <div class="player-center" style="{$linkOpacity}">
            <div class="player-controls">
                <button class="ctrl-btn" onclick="window.location.href='{$playerData['prevLink']}'">⏮</button>
                <button class="{$playBtnClass}">▶</button>
                <button class="ctrl-btn" onclick="window.location.href='{$playerData['nextLink']}'">⏭</button>
            </div>
            <div class="progress-container">
                <span>0:00</span>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0;"></div>
                </div>
                <span>{$playerData['duration']}</span>
            </div>
        </div>
        
        <div style="width: 30%;"></div>
    </div>
PLAYER;
}
