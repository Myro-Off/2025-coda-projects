<?php
session_start();

// --- 1. Configuration & Données ---
$moves = ["pierre", "feuille", "ciseaux", "lezard", "spock"];

// Règles : clé gagne contre valeurs
$rules = [
        "pierre"  => ["ciseaux", "lezard"],
        "feuille" => ["pierre", "spock"],
        "ciseaux" => ["feuille", "lezard"],
        "lezard"  => ["feuille", "spock"],
        "spock"   => ["pierre", "ciseaux"]
];

// Définition des icônes SVG
$svgs = [
        "pierre" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.rock-base { fill: #95a5a6; stroke: #2c3e50; stroke-width: 4; stroke-linejoin: round; } .rock-detail { fill: none; stroke: #2c3e50; stroke-width: 3; stroke-linecap: round; opacity: 0.5; }</style></defs><path class="rock-base" d="M30,15 C55,5 80,15 90,40 C100,65 90,90 60,95 C30,100 10,85 5,55 C0,25 15,20 30,15 Z" /><path class="rock-detail" d="M35,35 Q50,50 65,40" /><path class="rock-detail" d="M55,70 Q70,80 80,60" /><path class="rock-detail" d="M20,55 Q30,65 25,75" /></svg>',
        "feuille" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.paper-base { fill: #ecf0f1; stroke: #2c3e50; stroke-width: 4; stroke-linejoin: round; } .paper-fold { fill: none; stroke: #bdc3c7; stroke-width: 2; stroke-linecap: round; }</style></defs><polygon class="paper-base" points="10,5 85,10 95,30 90,90 75,95 20,90 5,75 5,30" /><path class="paper-fold" d="M10,30 L80,25" /><path class="paper-fold" d="M15,80 L75,85" /><path class="paper-fold" d="M30,10 L35,90" /><path class="paper-fold" d="M20,50 L85,55" /></svg>',
        "ciseaux" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.ciseaux-contour { fill: none; stroke: #2c3e50; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; } .ciseaux-metal { fill: #bdc3c7; } .ciseaux-poignee { fill: #e74c3c; } .ciseaux-pivot { fill: #34495e; }</style><g id="demi-ciseau"><path class="ciseaux-metal ciseaux-contour" d="M 35,50 L 95,35 Q 65,55 35,58 Z" /><path class="ciseaux-poignee ciseaux-contour" d="M 35,50 C 25,50 15,55 10,65 C 5,75 5,85 15,92 C 25,99 40,95 45,85 C 50,75 45,60 35,50 Z M 20,70 C 20,65 25,62 30,65 C 35,68 38,75 35,80 C 32,85 25,88 20,85 C 15,82 15,75 20,70 Z" /></g></defs><g transform="translate(50,50)"><g transform="rotate(15) translate(-35, -50)"><use href="#demi-ciseau" transform="scale(1, -1) translate(0, -100)"/></g><g transform="rotate(-15) translate(-35, -50)"><use href="#demi-ciseau"/></g><circle class="ciseaux-pivot ciseaux-contour" cx="0" cy="0" r="5" stroke-width="3"/></g></svg>',
        "lezard" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.lizard-body { fill: #2ecc71; stroke: #2c3e50; stroke-width: 4; stroke-linejoin: round; stroke-linecap: round; } .lizard-eye { fill: #f1c40f; stroke: #2c3e50; stroke-width: 2; }</style></defs><path class="lizard-body" d="M50,15 C60,15 65,25 65,35 C65,45 55,50 55,65 C55,80 75,70 85,55 C90,45 95,50 90,65 C80,90 40,95 30,75 C25,65 45,60 45,35 C45,25 40,15 50,15 Z" /><circle class="lizard-eye" cx="58" cy="28" r="4" /><path class="lizard-body" d="M30,35 L15,25" /><path class="lizard-body" d="M70,35 L85,25" /><path class="lizard-body" d="M30,65 L15,75" /><path class="lizard-body" d="M55,80 L65,95" /></svg>',
        "spock" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400"><defs><style>.k{fill:none;stroke:#2c3e50;stroke-width:4;stroke-linecap:round;stroke-linejoin:round}.s{fill:#f3d2b5}.b{fill:#3498db}</style></defs><g transform="translate(100,250)"><rect class="k" style="fill:#2c3e50" x="0" y="0" width="45" height="100" rx="5"/><rect class="k" style="fill:#2c3e50" x="55" y="0" width="45" height="100" rx="5"/><path class="k" style="fill:#1a252f" d="M-5,100L50,100L55,130L-10,130Z"/><path class="k" style="fill:#1a252f" d="M50,100L105,100L110,130L45,130Z"/></g><g transform="translate(90,130)"><path class="b k" d="M10,0L110,0L120,130L0,130Z"/><path class="k" style="fill:#2c3e50" d="M35,0Q60,20 85,0L110,0L10,0Z"/><path class="k" style="fill:#f1c40f" d="M85,30L100,40L95,55Q85,45 75,55L70,40Z"/></g><g transform="translate(115,30)"><path class="s k" d="M10,60L-15,35L10,45Z"/><path class="s k" d="M60,60L85,35L60,45Z"/><rect class="s k" x="10" y="20" width="50" height="70" rx="20" ry="25"/><path class="k" style="fill:#2c3e50" d="M10,35L60,35L65,20Q35,-10 5,20Z"/><path class="k" d="M15,45Q25,30 33,45"/><path class="k" d="M37,45Q45,30 55,45"/><circle fill="#2c3e50" cx="25" cy="55" r="3"/><circle fill="#2c3e50" cx="45" cy="55" r="3"/><line class="k" x1="25" y1="75" x2="45" y2="75"/></g><g transform="translate(70,135)"><rect class="b k" x="0" y="0" width="30" height="80" rx="10" transform="rotate(10)"/><circle class="s k" cx="0" cy="85" r="15" transform="translate(-5,0)"/></g><g transform="translate(200,140)"><rect class="b k" x="0" y="0" width="30" height="70" rx="10" transform="rotate(-40)"/><circle class="s k" cx="35" cy="55" r="15"/><g transform="translate(20,35) rotate(-10)"><rect class="k" style="fill:#7f8c8d" x="5" y="15" width="25" height="30" rx="5"/><path class="k" style="fill:#95a5a6" d="M0,0L50,0L55,15L-5,15Z"/><rect class="k" style="fill:#95a5a6" x="50" y="2" width="10" height="11"/><rect class="k" style="fill:#e74c3c" x="10" y="-5" width="10" height="5"/></g></g></svg>',
        "trophy" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="#Cca530" d="M25,85 L75,85 L72,92 C72,92 70,95 65,95 L35,95 C30,95 28,92 28,92 Z" /><rect fill="#Cca530" x="35" y="75" width="30" height="12" rx="2" /><path fill="#FCD34D" d="M20,20 Q20,60 35,75 L65,75 Q80,60 80,20 L20,20 Z" /><path fill="none" stroke="#FCD34D" stroke-width="6" stroke-linecap="round" d="M22,25 C5,25 5,55 22,55" /><path fill="none" stroke="#FCD34D" stroke-width="6" stroke-linecap="round" d="M78,25 C95,25 95,55 78,55" /><path fill="rgba(255,255,255,0.4)" d="M30,20 L40,20 L35,70 Z" /></svg>',
        "fire" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="#F59E0B" d="M50,5 C50,5 20,40 20,65 C20,85 35,95 50,95 C65,95 80,85 80,65 C80,40 50,5 50,5 Z" stroke="#ffffff" stroke-width="3" /><path fill="#FCD34D" d="M50,45 C50,45 38,62 38,72 C38,80 42,85 50,85 C58,85 62,80 62,72 C62,62 50,45 50,45 Z" /></svg>',
        "question" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="45" fill="var(--card-bg)" stroke="var(--border-color)" stroke-width="5"/><path d="M35,35 C35,20 65,20 65,35 C65,45 50,45 50,60" stroke="var(--border-color)" fill="none" stroke-width="8" stroke-linecap="round"/><line x1="50" y1="78" x2="50" y2="78.01" stroke="var(--border-color)" stroke-width="8" stroke-linecap="round"/></svg>',
        "error" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.error-cross { stroke: #e74c3c; stroke-width: 10; stroke-linecap: round; }</style></defs><circle cx="50" cy="50" r="45" fill="none" stroke="#e74c3c" stroke-width="5"/><line class="error-cross" x1="25" y1="25" x2="75" y2="75" /><line class="error-cross" x1="75" y1="25" x2="25" y2="75" /></svg>'
];


// --- 2. Gestion de l'état (Actions) ---

// Changement de thème
if (isset($_GET['toggle_theme'])) {
    $newTheme = ($_COOKIE['theme'] ?? 'light') === 'dark' ? 'light' : 'dark';
    setcookie('theme', $newTheme, time() + 31536000, "/"); // 1 an
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}
$theme = $_COOKIE['theme'] ?? 'light';

// Reset complet
if (isset($_GET['reset'])) {
    session_destroy();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Initialisation stats
if (!isset($_SESSION['stats'])) {
    $_SESSION['stats'] = ['total' => 0, 'wins' => 0, 'losses' => 0, 'ties' => 0, 'streak' => 0, 'best_streak' => 0];
    $_SESSION['history'] = [];
}

// --- 3. Moteur de Jeu ---
$playerMove = $_GET['player'] ?? null;
$computerMove = null;
$result = 'neutral'; // neutral, win, lose, tie, error
$msg = "Choisissez une attaque !";

// Si un coup est soumis (présent dans GET)
if (isset($_GET['player'])) {
    if (in_array($playerMove, $moves)) {
        // --- Coup VALIDE : Exécution du jeu ---
        $computerMove = $moves[array_rand($moves)];
        $_SESSION['stats']['total']++;

        if ($playerMove === $computerMove) {
            $result = 'tie';
            $msg = "Égalité !";
            $_SESSION['stats']['ties']++;
        } elseif (in_array($computerMove, $rules[$playerMove])) {
            $result = 'win';
            $msg = "Victoire !";
            $_SESSION['stats']['wins']++;
            $_SESSION['stats']['streak']++;
            if ($_SESSION['stats']['streak'] > $_SESSION['stats']['best_streak']) {
                $_SESSION['stats']['best_streak'] = $_SESSION['stats']['streak'];
            }
        } else {
            $result = 'lose';
            $msg = "Défaite...";
            $_SESSION['stats']['losses']++;
            $_SESSION['stats']['streak'] = 0;
        }

        // Historique (Garder les 5 derniers)
        array_unshift($_SESSION['history'], ['p' => $playerMove, 'c' => $computerMove, 'res' => $result]);
        $_SESSION['history'] = array_slice($_SESSION['history'], 0, 5);

    } else {
        // --- Coup INVALIDE : Gestion de l'erreur ---
        $result = 'error';
        $msg = "Entrée Invalide";
    }
}


// --- Variables d'affichage ---

$playerIcon = $playerMove && $result !== 'error' ? $svgs[$playerMove] : ($result === 'error' ? $svgs['error'] : $svgs['question']);
$computerIcon = $computerMove ? $svgs[$computerMove] : ($result === 'error' ? $svgs['error'] : $svgs['question']);
$playerLabel = $playerMove && $result !== 'error' ? ucfirst($playerMove) : ($result === 'error' ? 'Erreur' : 'En attente');
$computerLabel = $computerMove ? ucfirst($computerMove) : ($result === 'error' ? 'Erreur' : 'En attente');

$resultClass = $result === 'win' ? 'win' : ($result === 'lose' ? 'lose' : ($result === 'tie' ? 'tie' : ($result === 'error' ? 'lose' : '')));
$msgClass = $result === 'win' ? 'text-win' : ($result === 'lose' ? 'text-lose' : ($result === 'tie' ? 'text-tie' : ($result === 'error' ? 'text-lose' : '')));

$playerBadgeHtml = '';
if ($result === 'win') {
    $playerBadgeHtml = '<div class="corner-badge">' . $svgs['trophy'] . '</div>';
}

$computerBadgeHtml = '';
if ($result === 'lose') {
    $computerBadgeHtml = '<div class="corner-badge">' . $svgs['trophy'] . '</div>';
}

$themeToggleLabel = $theme === 'dark' ? '☀️ Clair' : '🌙 Sombre';
$animationClass = $playerMove ? 'animate-pop' : '';


// --- Génération des cartes de choix ---
$choicesHtml = '';
foreach($moves as $moveName) {
    $svg = $svgs[$moveName];
    $label = ucfirst($moveName);
    $choicesHtml .= <<<CHOICE
        <a href="?player={$moveName}" class="choice-card">
            {$svg}
            <span>{$label}</span>
        </a>
    CHOICE;
}

// --- Génération des règles du jeu ---
$rulesHtml = '';
foreach($rules as $winner => $losers) {
    $winnerSvg = $svgs[$winner];
    $losersSvg = '';
    foreach($losers as $loserMove) {
        $losersSvg .= $svgs[$loserMove];
    }
    $rulesHtml .= <<<RULE
        <div class="rule-item">
            <div class="rule-vs">{$winnerSvg}</div>
            <span style="font-size:0.7rem; font-weight:700; margin:5px 0;">bat</span>
            <div class="rule-vs" style="display:flex; gap:5px;">
                {$losersSvg}
            </div>
        </div>
    RULE;
}

// --- Génération du tableau de bord (Stats) ---
$stats = $_SESSION['stats'];
$statsHtml = <<<STATS
<div class="stats-box">
    <h3 class="box-title">Tableau de bord</h3>
    <div class="stats-grid">
        <div class="stat-item win">
            <span class="stat-label">Victoires</span> <span class="stat-val">{$stats['wins']}</span>
        </div>
        <div class="stat-item lose">
            <span class="stat-label">Défaites</span> <span class="stat-val">{$stats['losses']}</span>
        </div>
        <div class="stat-item tie">
            <span class="stat-label">Égalités</span> <span class="stat-val">{$stats['ties']}</span>
        </div>
        <div class="stat-item total">
            <span class="stat-label">Total</span> <span class="stat-val">{$stats['total']}</span>
        </div>
        <div class="stat-item special">
            <span class="stat-label"><span class="mini-icon">{$svgs['fire']}</span> Série</span>
            <span class="stat-val">{$stats['streak']}</span>
        </div>
        <div class="stat-item special">
            <span class="stat-label"><span class="mini-icon">{$svgs['trophy']}</span> Record Série</span>
            <span class="stat-val">{$stats['best_streak']}</span>
        </div>
    </div>
    <a href="?reset=1" class="reset-btn">Réinitialiser</a>
</div>
STATS;

// --- Génération de l'Historique ---
$historyHtml = '';
if(!empty($_SESSION['history'])) {
    $historyListItems = '';
    foreach($_SESSION['history'] as $historyEntry) {
        $color = ($historyEntry['res'] === 'win') ? 'var(--green)' : (($historyEntry['res'] === 'lose') ? 'var(--red)' : 'var(--yellow)');
        $label = ($historyEntry['res'] === 'win') ? 'Gagné' : (($historyEntry['res'] === 'lose') ? 'Perdu' : 'Égalité');
        $pSvg = $svgs[$historyEntry['p']];
        $cSvg = $svgs[$historyEntry['c']];

        $historyListItems .= <<<HISTORY_ITEM
        <div class="history-row" style="border-left-color: {$color}">
            <div class="h-icons">
                {$pSvg}
                <span style="font-size:0.8rem; font-weight:800; color:var(--subtext-color);">vs</span>
                {$cSvg}
            </div>
            <div style="font-weight:800; text-transform:uppercase; font-size:0.8rem; color:{$color}">
                {$label}
            </div>
        </div>
        HISTORY_ITEM;
    }

    $historyHtml = <<<HISTORY_BOX
        <div class="stats-box">
            <h3 class="box-title">Derniers combats</h3>
            <div class="history-list">
                {$historyListItems}
            </div>
        </div>
    HISTORY_BOX;
}


// --- Rendu HTML ---
$html_content = <<<HTML
<!DOCTYPE html>
<html lang="fr" data-theme="{$theme}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu PHP: Pierre Feuille Ciseaux</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<a href="?toggle_theme=1" class="theme-btn">{$themeToggleLabel}</a>

<div class="main-layout">

    <main class="game-section">
        <h1>Les Armes du Destin</h1>
        <p class="subtitle">Sélectionnez votre arme</p>

        <div class="choices-grid">
            {$choicesHtml}
        </div>

        <section class="result-box {$resultClass} {$animationClass}">
            <div class="result-msg {$msgClass}">{$msg}</div>

            <div class="duel-display">
                <div class="fighter">
                    {$playerBadgeHtml}
                    <h2>Vous</h2>
                    <div class="fighter-icon">{$playerIcon}</div>
                    <p>{$playerLabel}</p>
                </div>

                <div style="font-weight:900; font-size:1.2rem;">VS</div>

                <div class="fighter">
                    {$computerBadgeHtml}
                    <h2>Ordi</h2>
                    <div class="fighter-icon">{$computerIcon}</div>
                    <p>{$computerLabel}</p>
                </div>
            </div>
        </section>

        <div class="rules-area">
            <h3 class="box-title">Règles du jeu</h3>
            <div class="rules-grid">
                {$rulesHtml}
            </div>
        </div>
    </main>

    <aside class="stats-section">
        {$statsHtml}
        {$historyHtml}
    </aside>
</div>
</body>
</html>
HTML;

echo $html_content;