<?php
// Fichier : error.php
// Description : Page personnalisée d'affichage des erreurs HTTP (404, 500, etc.).

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';

// 1. Récupération des paramètres
$errorCode = isset($_GET['code']) ? (int)$_GET['code'] : http_response_code();
// Définit 404 par défaut si la page est appelée sans code HTTP spécifique (code 200)
if ($errorCode === 200) {
    $errorCode = 404;
}
$context = $_GET['context'] ?? 'default'; // Contexte d'erreur (ex: 'artist', 'db', 'overload')

// Force le code de réponse HTTP pour les navigateurs et les moteurs de recherche
http_response_code($errorCode);

$page = new HTMLPage("Lowify - Erreur " . $errorCode);

// 2. Configuration des messages et de l'animation
switch ($errorCode) {
    case 404:
        $animationClass = "error-404"; // Style Vinyle tournant (404)

        // Configuration des messages spécifiques aux entités manquantes
        switch ($context) {
            case 'artist':
                $title = "Artiste fantôme";
                $subTitle = "Cette page ? Disparue. Comme la carrière de l’artiste que tu voulais voir.";
                break;

            case 'album':
                $title = "Album introuvable";
                $subTitle = "Oups ! Cet album a tellement flop qu’il s’est auto-supprimé.";
                break;

            case 'playlist':
                $title = "Playlist perdue";
                $subTitle = "Peut-être qu’elle avait trop mauvais goût pour rester sur nos serveurs.";
                break;

            case 'song':
                $title = "Piste introuvable";
                $subTitle = "Oups, cette piste n’existe plus ou n’a jamais existé.";
                break;

            default:
                $title = "Page introuvable";
                $subTitle = "Oups ! Il semblerait que cette page ne veut pas travailler, est-ce une erreur ? Ou bien un acte maléfique de votre part ???";
                break;
        }
        break;

    case 500:
        $animationClass = "error-generic"; // Style Vinyle qui saute (500)

        // Configuration des messages spécifiques aux erreurs serveur
        switch ($context) {
            case 'db':
                $title = "Erreur serveur";
                $subTitle = "Notre base de données vient de tenter un solo… et elle a explosé l’ampli. On essaie de la réanimer.";
                break;

            case 'delete-from-playlist':
                $title = "Erreur inattendue";
                $subTitle = "Notre messager semble avoir perdu votre demande de suppression de ce titre, réessayez plus tard.";
                break;

            case 'overload':
                $title = "500 Serveur surchargé";
                $subTitle = "Nos serveurs tournent à fond… comme un PC gamer avec 2 fps. On respire, on arrive.";
                break;

            default:
                $title = "Erreur serveur";
                $subTitle = "Oups ! Il semblerait que nos serveurs rencontrent un problème actuellement, réessayez plus tard";
                break;
        }
        break;

    default:
        // Gestion des autres codes d'erreur (ex: 403)
        $title = "Erreur " . $errorCode;
        $subTitle = htmlspecialchars($_GET['message'] ?? "Une fausse note s'est glissée dans le système.");
        $animationClass = "error-generic";
        break;
}

// 3. Connexion DB (Fail-safe)
try {
    // Tente de se connecter à la DB pour afficher la sidebar/player
    $db = new DatabaseManager(dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4', username: 'lowify', password: 'lowifypassword');
    $sidebarContent = renderSidebar($db, 'error.php');
    $playerHTML = renderPlayerBar($db, $_GET['now_playing'] ?? null);
} catch (PDOException $ex) {
    // Si la DB est indisponible, affiche une page minimale sans composants interactifs
    $sidebarContent = '';
    $playerHTML = '';
}

// 4. Construction du HTML
$html = <<<HTML
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<div class="app-container">
    {$sidebarContent}
    
    <div class="main-view $animationClass">
        <div class="error-wrapper">
            <div class="error-code-display">$errorCode</div>

            <div class="error-content-layer">
                <div class="vinyl-record">
                    <div class="vinyl-label">
                        <span>$errorCode</span>
                    </div>
                </div>

                <h1 class="page-title">$title</h1>
                
                <p>$subTitle</p>

                <div class="actions">
                    <a href="index.php" class="duo-button outline" style="background: var(--primary); border: none; color: black;">Retour à l'accueil</a>
                    <a href="javascript:history.back()" class="duo-button outline">Page précédente</a>
                </div>
            </div>
        </div>
    </div>
</div>
{$playerHTML}
HTML;

$page->addContent($html);
echo $page->render();
