<?php
// Fichier : inc/utils.inc.php

use JetBrains\PhpStorm\NoReturn;

// --- CONSTANTES ---
const DEFAULT_ARTIST_COVER = '../assets/default_artist.png';
const DEFAULT_ALBUM_COVER  = '../assets/default_album.png';
const DEFAULT_SONG_COVER   = '../assets/default_song.png';
const FAVORITES_PLAYLIST_NAME = "Coup de cœur";

// --- SVGS ---
const HEART_FULL_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
const HEART_EMPTY_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';
const TRASH_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14c0 1.1-.9 2-2 2H7c-1.1 0-2-.9-2-2V6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M17 6V4c0-1.1-.9-2-2-2H9c-1.1 0-2 .9-2 2v2"/></svg>';
const CLOCK_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
const STAR_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
const PLUS_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>';
const SEARCH_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M21.71 20.29l-5.01-5.01C17.54 13.68 18 11.91 18 10c0-4.41-3.59-8-8-8S2 5.59 2 10s3.59 8 8 8c1.91 0 3.68-.46 5.28-1.3l5.01 5.01c.39.39 1.02.39 1.41 0 .39-.39.39-1.02.01-1.41zM4 10c0-3.31 2.69-6 6-6s6 2.69 6 6-2.69 6-6 6-6-2.69-6-6z"/></svg>';
const ARTIST_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 00-3 3v7a3 3 0 006 0V5a3 3 0 00-3-3z"/><path d="M19 10v2a7 7 0 01-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>';
const HOME_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12.037 3.07c.433.21 1.4.832 3.555 2.583 1.883 1.531 3.747 3.406 4.535 4.22.331 1.781.57 3.589.713 5.382.164 2.064.201 3.697.113 5.745h-4.958v-4.308A4 4 0 0 0 12 12.697c-2.203 0-4.015 1.792-4.015 3.995V21H3.047a43.615 43.615 0 0 1 .113-5.745c.142-1.793.381-3.6.712-5.381.789-.815 2.653-2.69 4.536-4.22 2.158-1.755 3.21-2.375 3.629-2.585ZM12.005 1h-.012c-.642 0-2.316 1.044-4.848 3.101C4.536 6.223 2.02 8.918 2.02 8.918a53.892 53.892 0 0 0-.853 6.179c-.257 3.23-.185 5.613 0 7.903H10v-6.31a2 2 0 0 1 4 0V23h8.833c.185-2.29.257-4.673 0-7.903a53.892 53.892 0 0 0-.852-6.18s-2.517-2.694-5.128-4.816C14.323 2.044 12.79 1 12.006 1Z"></path></svg>';
const MUSIC_NOTE_SVG = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>';

// --- UTILITAIRES ---
function formatDurationMMSS(int $seconds): string {
    return gmdate("i:s", $seconds);
}

function formatDurationHHMMSS(int $seconds): string {
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = $seconds % 60;
    return ($h > 0) ? sprintf('%02d:%02d:%02d', $h, $m, $s) : sprintf('%02d:%02d', $m, $s);
}

function formatMonthlyListeners(int $listeners): string {
    if ($listeners >= 1000000) {
        return round($listeners / 1000000, 1) . 'M';
    }
    if ($listeners >= 1000) {
        return round($listeners / 1000, 1) . 'k';
    }
    return (string)$listeners;
}

/**
 * Redirige vers la page 404 avec un contexte spécifique (pour l'humour)
 * @param string $context 'artist', 'album', 'playlist', ou vide
 */
#[NoReturn]
function exitWith404(string $context = ''): void {
    header("Location: error.php?code=404&context=" . urlencode($context));
    exit;
}

function exitWith500(string $context = ''): void {
    header("Location: error.php?code=500&context=" . urlencode($context));
}

function getCoverAttributes(string $db_path, string $default_const): array {
    $src = htmlspecialchars($db_path);
    if (empty($src)) {
        $src = $default_const;
    }
    return ['src' => $src, 'onerror' => "onerror=\"this.onerror=null; this.src='$default_const'\""];
}

function getNextPrevSongs(DatabaseManager $db, $currentSongId, $contextType = 'all', $contextId = null) {
    $query = "SELECT id FROM song ORDER BY id LIMIT 500";
    $params = [];

    if ($contextType === 'album' && $contextId) {
        $query = "SELECT id FROM song WHERE album_id = :cid ORDER BY id";
        $params = ['cid' => $contextId];
    } elseif ($contextType === 'artist' && $contextId) {
        $query = "SELECT id FROM song WHERE artist_id = :cid ORDER BY note DESC LIMIT 50";
        $params = ['cid' => $contextId];
    } elseif ($contextType === 'playlist' && $contextId) {
        $query = "SELECT s.id FROM x_playlist_song xps JOIN song s ON xps.song_id = s.id WHERE xps.playlist_id = :cid ORDER BY xps.id";
        $params = ['cid' => $contextId];
    }

    $allIds = array_column($db->executeQuery($query, $params), 'id');
    $currentIndex = array_search($currentSongId, $allIds);

    $nextId = $allIds[$currentIndex + 1] ?? ($currentIndex !== false && empty($allIds) ? $allIds : null);
    $prevId = $allIds[$currentIndex - 1] ?? ($currentIndex !== false && empty($allIds) ? -1 : null);

    return ['next' => $nextId, 'prev' => $prevId];
}

// --- FONCTIONS DE RENDU REFACTORISÉES ---

/**
 * [INTERNE] Génère une grille de cartes générique (pour artistes ou albums)
 */
/**
 * [INTERNE] Génère une grille de cartes générique (pour artistes ou albums)
 */
function _renderGenericGrid(array $items, string $type): string {
    $html = '<div class="grid-container">';

    foreach ($items as $item) {
        $id = htmlspecialchars($item['id']);
        $name = htmlspecialchars($item['name']);
        $isLiked = $item['is_liked'] ?? 0;

        // Configuration spécifique selon le type
        if ($type === 'artist') {
            $defaultCover = DEFAULT_ARTIST_COVER;
            $subtitle = 'Artiste';
            $cssClass = 'card artist-mode'; // Classe CSS de base
            $pageLink = "artist.php?id=$id";
            // Pour un artiste, le play lance ses titres populaires (ou le premier album)
            // On suppose ici que play_song gère le contexte artist
            $playLink = "play_song.php?id=$id&context_type=artist&context_id=$id";
        } else {
            $defaultCover = DEFAULT_ALBUM_COVER;
            $subtitle = htmlspecialchars($item['artist_name'] ?? 'Album');
            $cssClass = 'card';
            $pageLink = "album.php?id=$id";
            // Pour un album, on lance la première piste de l'album
            // (Note: play_song.php devra être assez intelligent pour trouver la 1ᵉ chanson si on lui donne juste l'ID album,
            // ou alors, il faut faire une requête ici). Pour l'instant, on garde votre logique.
            $playLink = "play_song.php?context_type=album&context_id=$id";
        }

        $cover = getCoverAttributes($item['cover'] ?? '', $defaultCover);
        $likeSVG = $isLiked ? HEART_FULL_SVG : HEART_EMPTY_SVG;

        // Si liké => bouton vert ou rouge, sinon => contour blanc
        // Ici j'utilise une classe 'liked' si c'est déjà aimé pour pouvoir le styliser en CSS (ex: coeur rouge)
        $likeBtnClass = $isLiked ? 'action-btn like active' : 'action-btn like outline';
        // Note: pour le coeur plein, on peut vouloir changer la couleur inline ou via CSS

        $html .= <<<HTML
            <a href="$pageLink" class="$cssClass">
                <div class="card-img-container">
                    <img alt="$name" src="{$cover['src']}" class="card-img" loading="lazy" {$cover['onerror']}>
                    
                    <div class="card-overlay-actions">
                        <object>
                            <a href="like_item.php?id=$id&type=$type" class="$likeBtnClass" title="J'aime">
                                $likeSVG
                            </a>
                        </object>
                        <object>
                            <a href="$playLink" class="action-btn play" title="Lecture">
                                ▶
                            </a>
                        </object>
                    </div>
                </div>
                
                <div class="card-info">
                    <div class="card-title" title="$name">$name</div>
                    <div class="card-subtitle">$subtitle</div>
                </div>
            </a>
        HTML;
    }
    $html .= '</div>';
    return $html;
}

/**
 * Wrapper pour afficher la grille des artistes
 */
function renderArtistGrid(array $artists): string {
    return _renderGenericGrid($artists, 'artist');
}

/**
 * Wrapper pour afficher la grille des albums
 */
function renderAlbumGrid(array $albums): string {
    return _renderGenericGrid($albums, 'album');
}

/**
 * [INTERNE] Génère un tableau de chansons générique.
 * @param array $songs Liste des chansons
 * @param array $options Options d'affichage ['show_delete' => bool, 'show_date' => bool, 'context_type' => string, 'context_id' => int]
 */
function _renderGenericSongTable(array $songs, array $options = []): string {
    if (empty($songs)) {
        return isset($options['empty_message'])
            ? '<p class="card-subtitle" style="text-align: center; margin-top: 50px;">' . $options['empty_message'] . '</p>'
            : '';
    }

    // Valeurs par défaut
    $showDelete = $options['show_delete'] ?? false;
    $showDate   = $options['show_date'] ?? false;
    $ctxType    = $options['context_type'] ?? 'all';
    $ctxId      = $options['context_id'] ?? null;
    $isHistory  = ($options['delete_mode'] ?? '') === 'history';

    // En-têtes
    $html = '<table class="deezer-table"><thead><tr>';
    $html .= '<th style="width: 35%;">TITRE</th>';
    $html .= '<th style="width: 25%; padding-left: 6rem;">ARTISTE</th>';
    $html .= '<th style="width: 20%;">ALBUM</th>';
    $html .= '<th style="width: 7%;">NOTE</th>';
    if ($showDate) {
        $html .= '<th style="width: 10%;">AJOUTÉ</th>';
    }
    $html .= '<th style="width: 5%; text-align:center;">' . CLOCK_SVG . '</th>';
    if ($showDelete) {
        $html .= '<th style="width: 5%;"></th>';
    }
    $html .= '</tr></thead><tbody>';

    foreach ($songs as $s) {
        $id = htmlspecialchars($s['id']);
        $name = htmlspecialchars($s['name']);
        $artist = htmlspecialchars($s['artist_name'] ?? 'Inconnu');
        $album = htmlspecialchars($s['album_name'] ?? 'Inconnu');
        $albumId = $s['album_id'] ?? '#';
        $artistId = $s['artist_id'] ?? $ctxId; // Fallback sur le contexte si pas d'ID artiste direct

        $duration = formatDurationMMSS($s['duration']);
        $cover = getCoverAttributes($s['cover'] ?? '', DEFAULT_SONG_COVER);

        // Note
        $note = round($s['note'] ?? 0, 1);
        $noteDisplay = $note > 0 ? "$note " . STAR_SVG : "-";

        // Like & Actions
        $isLiked = $s['is_liked'] ?? 0;
        $likeSVG = $isLiked ? HEART_FULL_SVG : HEART_EMPTY_SVG;
        $likeClass = $isLiked ? 'active' : '';

        // Ligne HTML
        $html .= '<tr>';

        // Col 1: Titre + Cover + Play
        $html .= '<td><div class="col-title-content">';
        $html .= "<div class=\"table-cover-container\">
            <img alt='' src='{$cover['src']}' class='table-cover' {$cover['onerror']}>
            <a href='play_song.php?id=$id&context_type=$ctxType&context_id=$ctxId' class='mini-play-overlay'>▶</a>
          </div>";
        $html .= "<a href='#' class='song-name-link'>$name</a>";
        $html .= '</div></td>';

        // Col 2: Artiste + Actions (Like/Add)
        $html .= '<td><div style="display:flex; align-items:center;">';
        $html .= '<div class="row-actions-group" style="margin-right: 15px;">';
        $html .= "<a href='like_item.php?id=$id&type=song' class='table-heart-btn $likeClass' title='J&#39;aime'>$likeSVG</a>";
        $html .= "<a href='add_to_playlist.php?id=$id' class='table-plus-btn' title='Ajouter à une playlist'>" . PLUS_SVG . "</a>";
        $html .= '</div>';
        $html .= "<a href='artist.php?id=$artistId' style='color:var(--text-main);'>$artist</a>";
        $html .= '</div></td>';

        // Col 3: Album
        $html .= "<td><a href='album.php?id=$albumId' style='color:var(--text-secondary);'>$album</a></td>";

        // Col 4: Note
        $html .= "<td style='font-weight:700;'>$noteDisplay</td>";

        // Col 5 (Optionnelle): Date
        if ($showDate) {
            $date = date('Y-m-d', strtotime($s['added_at'] ?? 'now'));
            $html .= "<td>$date</td>";
        }

        // Col 6: Durée
        $html .= "<td style='text-align:right;'>$duration</td>";

        // Col 7 (Optionnelle): Delete
        if ($showDelete) {
            if ($isHistory) {
                $delLink = "remove_from_history.php?song_id=$id";
                $confirm = "return confirm('Retirer &quot;$name&quot; de l\\'historique ?')";
            } else {
                $delLink = "remove_from_playlist.php?playlist_id=$ctxId&song_id=$id";
                $confirm = "return confirm('Retirer &quot;$name&quot; de la playlist ?')";
            }
            $html .= "<td style='text-align:center;'><a href='$delLink' onclick=\"$confirm\" class='table-delete-btn' title='Retirer'>" . TRASH_SVG . "</a></td>";
        }

        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

/**
 * Wrapper pour la liste des titres d'une playlist (avec suppression et date)
 */
function renderPlaylistSongList(array $songs, $playlistId): string {
    $isHistory = ($_GET['subfilter'] ?? '') === 'history';

    return _renderGenericSongTable($songs, [
        'show_delete' => true,
        'show_date' => true,
        'context_type' => 'playlist',
        'context_id' => $playlistId,
        'delete_mode' => $isHistory ? 'history' : 'playlist',
        'empty_message' => 'Cette playlist est vide.'
    ]);
}

/**
 * Wrapper pour la liste globale (Artistes/Albums) sans suppression ni date
 */
function renderGlobalSongList(array $songs, $contextId): string {
    return _renderGenericSongTable($songs, [
        'show_delete' => false,
        'show_date' => false,
        'context_type' => 'artist',
        'context_id' => $contextId,
        'empty_message' => 'Aucun titre populaire trouvé.'
    ]);
}

// 1. Démarrer la temporisation de sortie (Capture tout le HTML avant envoi)
// Cela empêche l'erreur "Headers already sent"
ob_start();

// 2. Cacher les erreurs brutes à l'écran (Indispensable en Prod)
ini_set('display_errors', 0);
ini_set('log_errors', 1); // On garde les logs serveur pour le débug
error_reporting(E_ALL);

// 3. Convertir les WARNINGS et NOTICES en Exceptions
// (C'est ça qui va attraper votre erreur de typo "monthly_listeners")
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// 4. Gestionnaire d'Exceptions Global (Le Try/Catch géant)
set_exception_handler(function ($e) {
    // On vide le tampon HTML existant pour ne pas afficher une page à moitié chargée
    if (ob_get_length()) {
        ob_clean();
    }

    // Détection du contexte (Optionnel, pour affiner le message)
    $context = 'default'; // Par défaut : Erreur système
    if (str_contains($e->getMessage(), 'SQL') || str_contains($e->getMessage(), 'PDO')) {
        $context = 'db';
    }

    // Redirection propre vers la page 500
    // Note: On utilise header() ici car c'est le dernier recours
    header("Location: error.php?code=500&context=$context&message=" . urlencode(substr($e->getMessage(), 0, 100)));
    exit;
});

// 5. Gestionnaire d'Erreurs Fatales (Crash critique qui arrête le script)
register_shutdown_function(function () {
    $error = error_get_last();
    // Si c'est une erreur fatale qui n'a pas été attrapée par le handler précédent
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR)) {
        if (ob_get_length()) {
            ob_clean();
        }
        header("Location: error.php?code=500&context=default");
        exit;
    }
    // Si tout va bien, on envoie le tampon HTML au navigateur
    ob_end_flush();
});

