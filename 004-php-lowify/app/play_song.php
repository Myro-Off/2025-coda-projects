<?php
// Fichier : play_song.php
// Description : Gère le démarrage de la lecture d'une chanson. Sauvegarde l'ID et le contexte en session
//               et met à jour l'historique d'écoute avant de rediriger l'utilisateur.

require_once 'inc/database.inc.php';

// Démarrage de session pour sauvegarder l'état de la lecture
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$songId = $_GET['id'] ?? null;
$contextType = $_GET['context_type'] ?? 'all';
$contextId = $_GET['context_id'] ?? null;

if (is_numeric($songId)) {
    // 1. Sauvegarder les informations de lecture en session
    $_SESSION['now_playing'] = $songId;
    $_SESSION['context_type'] = $contextType;
    $_SESSION['context_id'] = $contextId;

    // 2. Mise à jour de l'historique d'écoute (fail-safe)
    try {
        $db = new DatabaseManager(
            dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
            username: 'lowify',
            password: 'lowifypassword'
        );
        // Enregistrement du titre et de la date dans la table history
        $db->executeQuery("INSERT INTO history (song_id, played_at) VALUES (:sid, NOW())", ['sid' => $songId]);
    } catch (PDOException $e) { /* Ignore les erreurs d'historique si la DB n'est pas critique */ }
}

// 3. Redirection vers la page d'origine (HTTP_REFERER)
$referer = $_SERVER['HTTP_REFERER'] ?? 'index.php';

// Nettoyage de l'URL de référence : On retire les paramètres now_playing/context
$baseUrl = strtok($referer, '?');

// Récupération des paramètres existants (pour conserver les filtres, ex: profile.php?subfilter=songs)
$query = parse_url($referer, PHP_URL_QUERY) ?? '';
parse_str($query, $queryParams);

// Suppression des anciens paramètres de lecture de l'URL
unset($queryParams['now_playing']);
unset($queryParams['context_type']);
unset($queryParams['context_id']);

// Reconstruction de la nouvelle URL cible
$newQuery = http_build_query($queryParams);
$target = $newQuery ? $baseUrl . '?' . $newQuery : $baseUrl;

header('Location: ' . $target);
exit;
