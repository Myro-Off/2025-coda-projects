<?php

// --- 1. Configuration & Ressources ---
$choices = ["pierre", "feuille", "ciseaux", "lezard", "spock"];

// SVG Icons
$svgIcons = [
    "pierre" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.rock-base { fill: #95a5a6; stroke: #2c3e50; stroke-width: 4; stroke-linejoin: round; } .rock-detail { fill: none; stroke: #2c3e50; stroke-width: 3; stroke-linecap: round; opacity: 0.5; }</style></defs><path class="rock-base" d="M30,15 C55,5 80,15 90,40 C100,65 90,90 60,95 C30,100 10,85 5,55 C0,25 15,20 30,15 Z" /><path class="rock-detail" d="M35,35 Q50,50 65,40" /><path class="rock-detail" d="M55,70 Q70,80 80,60" /><path class="rock-detail" d="M20,55 Q30,65 25,75" /></svg>',

    "feuille" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.paper-base { fill: #ecf0f1; stroke: #2c3e50; stroke-width: 4; stroke-linejoin: round; } .paper-fold { fill: none; stroke: #bdc3c7; stroke-width: 2; stroke-linecap: round; }</style></defs><polygon class="paper-base" points="10,5 85,10 95,30 90,90 75,95 20,90 5,75 5,30" /><path class="paper-fold" d="M10,30 L80,25" /><path class="paper-fold" d="M15,80 L75,85" /><path class="paper-fold" d="M30,10 L35,90" /><path class="paper-fold" d="M20,50 L85,55" /></svg>',

    "ciseaux" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.ciseaux-contour { fill: none; stroke: #2c3e50; stroke-width: 4; stroke-linecap: round; stroke-linejoin: round; } .ciseaux-metal { fill: #bdc3c7; } .ciseaux-poignee { fill: #e74c3c; } .ciseaux-pivot { fill: #34495e; }</style><g id="demi-ciseau"><path class="ciseaux-metal ciseaux-contour" d="M 35,50 L 95,35 Q 65,55 35,58 Z" /><path class="ciseaux-poignee ciseaux-contour" d="M 35,50 C 25,50 15,55 10,65 C 5,75 5,85 15,92 C 25,99 40,95 45,85 C 50,75 45,60 35,50 Z M 20,70 C 20,65 25,62 30,65 C 35,68 38,75 35,80 C 32,85 25,88 20,85 C 15,82 15,75 20,70 Z" /></g></defs><g transform="translate(50,50)"><g transform="rotate(15) translate(-35, -50)"><use href="#demi-ciseau" transform="scale(1, -1) translate(0, -100)"/></g><g transform="rotate(-15) translate(-35, -50)"><use href="#demi-ciseau"/></g><circle class="ciseaux-pivot ciseaux-contour" cx="0" cy="0" r="5" stroke-width="3"/></g></svg>',

    "lezard" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><style>.lizard-body { fill: #2ecc71; stroke: #2c3e50; stroke-width: 4; stroke-linejoin: round; stroke-linecap: round; } .lizard-eye { fill: #f1c40f; stroke: #2c3e50; stroke-width: 2; }</style></defs><path class="lizard-body" d="M50,15 C60,15 65,25 65,35 C65,45 55,50 55,65 C55,80 75,70 85,55 C90,45 95,50 90,65 C80,90 40,95 30,75 C25,65 45,60 45,35 C45,25 40,15 50,15 Z" /><circle class="lizard-eye" cx="58" cy="28" r="4" /><path class="lizard-body" d="M30,35 L15,25" /><path class="lizard-body" d="M70,35 L85,25" /><path class="lizard-body" d="M30,65 L15,75" /><path class="lizard-body" d="M55,80 L65,95" /></svg>',

    "spock" => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400" width="100" height="100"><defs><style>.k{fill:none;stroke:#2c3e50;stroke-width:4;stroke-linecap:round;stroke-linejoin:round}.s{fill:#f3d2b5}.b{fill:#3498db}</style></defs><g transform="translate(100,250)"><rect class="k" style="fill:#2c3e50" x="0" y="0" width="45" height="100" rx="5"/><rect class="k" style="fill:#2c3e50" x="55" y="0" width="45" height="100" rx="5"/><path class="k" style="fill:#1a252f" d="M-5,100L50,100L55,130L-10,130Z"/><path class="k" style="fill:#1a252f" d="M50,100L105,100L110,130L45,130Z"/></g><g transform="translate(90,130)"><path class="b k" d="M10,0L110,0L120,130L0,130Z"/><path class="k" style="fill:#2c3e50" d="M35,0Q60,20 85,0L110,0L10,0Z"/><path class="k" style="fill:#f1c40f" d="M85,30L100,40L95,55Q85,45 75,55L70,40Z"/></g><g transform="translate(115,30)"><path class="s k" d="M10,60L-15,35L10,45Z"/><path class="s k" d="M60,60L85,35L60,45Z"/><rect class="s k" x="10" y="20" width="50" height="70" rx="20" ry="25"/><path class="k" style="fill:#2c3e50" d="M10,35L60,35L65,20Q35,-10 5,20Z"/><path class="k" d="M15,45Q25,30 33,45"/><path class="k" d="M37,45Q45,30 55,45"/><circle fill="#2c3e50" cx="25" cy="55" r="3"/><circle fill="#2c3e50" cx="45" cy="55" r="3"/><line class="k" x1="25" y1="75" x2="45" y2="75"/></g><g transform="translate(70,135)"><rect class="b k" x="0" y="0" width="30" height="80" rx="10" transform="rotate(10)"/><circle class="s k" cx="0" cy="85" r="15" transform="translate(-5,0)"/></g><g transform="translate(200,140)"><rect class="b k" x="0" y="0" width="30" height="70" rx="10" transform="rotate(-40)"/><circle class="s k" cx="35" cy="55" r="15"/><g transform="translate(20,35) rotate(-10)"><rect class="k" style="fill:#7f8c8d" x="5" y="15" width="25" height="30" rx="5"/><path class="k" style="fill:#95a5a6" d="M0,0L50,0L55,15L-5,15Z"/><rect class="k" style="fill:#95a5a6" x="50" y="2" width="10" height="11"/><rect class="k" style="fill:#e74c3c" x="10" y="-5" width="10" height="5"/></g></g></svg>'
];

$defaultSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80" aria-hidden="true"><line x1="18" y1="18" x2="62" y2="62" stroke="#f87171" stroke-width="8" stroke-linecap="round"/><line x1="62" y1="18" x2="18" y2="62" stroke="#f87171" stroke-width="8" stroke-linecap="round"/></svg>';
$questionSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100"><circle cx="50" cy="50" r="47" stroke-width="5"/><path d="M35,35 C35,20 65,20 65,35 C65,45 50,45 50,60" stroke-width="5"/><line x1="50" y1="75" x2="50" y2="75.01" stroke-width="5"/></svg>';

// --- 2. Logique du Jeu ---
$playerChoice = $_GET['player'] ?? null;
$phpChoice = in_array($playerChoice, $choices) ? $choices[array_rand($choices)] : null;

// Matrice de victoire : Chaque clé bat les valeurs dans le tableau correspondant
$winningRules = [
    "pierre"  => ["ciseaux", "lezard"],
    "feuille" => ["pierre", "spock"],
    "ciseaux" => ["feuille", "lezard"],
    "lezard"  => ["feuille", "spock"],
    "spock"   => ["pierre", "ciseaux"]
];

// Calcul du résultat
if ($playerChoice === null) {
    $result = "";
} elseif (!in_array($playerChoice, $choices)) {
    $result = "";
} elseif ($playerChoice === $phpChoice) {
    $result = "ÉGALITÉ";
} elseif (in_array($phpChoice, $winningRules[$playerChoice])) {
    $result = "VOUS AVEZ GAGNÉ 🎉";
} else {
    $result = "VOUS AVEZ PERDU 😢";
}

// Préparation de l'affichage
if ($playerChoice === null) {
    $playerLabel = 'Faire un Choix';
    $playerSvg = $questionSvg;
    $phpLabel = 'Prêt à Dégainer';
    $phpSvg = $questionSvg;
} elseif (!in_array($playerChoice, $choices)) {
    $playerLabel = 'Choix invalide';
    $playerSvg = $defaultSvg;
    $phpLabel = 'Arrête de Bidouiller';
    $phpSvg = $questionSvg;
} else {
    $playerLabel = ucfirst($playerChoice);
    $playerSvg = $svgIcons[$playerChoice];

    $phpLabel = ucfirst($phpChoice);
    $phpSvg = $svgIcons[$phpChoice];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Pierre Feuille Ciseaux Lézard Spock</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            font-family: 'Poppins', sans-serif;
            color: #f1f5f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        h1 {
            font-weight: 600;
            font-size: 2rem;
            margin-top: 0;
            margin-bottom: 0.5rem;
            text-align: center;
            letter-spacing: 1.2px;
            text-shadow: 0 0 10px #3b82f6;
        }
        p.subtitle { font-size: 1rem; margin-bottom: 2rem; color: #94a3b8; text-align: center; }

        .choices {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 3rem;
            max-width: 800px;
        }
        .choice-btn {
            background: #1e293b;
            border-radius: 15px;
            width: 100px;
            height: 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #f1f5f9;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 0 10px transparent;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            text-decoration: none;
            user-select: none;
        }
        .choice-btn svg {
            width: 40px;
            height: 40px;
            margin-bottom: 5px;
            filter: drop-shadow(0 0 2px rgba(255,255,255,0.3));
            transition: transform 0.3s ease;
        }
        .choice-btn:hover, .choice-btn:focus {
            background: #3b82f6;
            border-color: #60a5fa;
            box-shadow: 0 0 15px #3b82f6;
            outline: none;
            transform: translateY(-5px);
        }
        .choice-btn:hover svg, .choice-btn:focus svg { transform: scale(1.15) rotate(10deg); }

        .result-container {
            max-width: 600px;
            width: 100%;
            background: #1e293b;
            border-radius: 20px;
            padding: 30px 40px;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            text-align: center;
        }
        .players { display: flex; justify-content: space-around; margin-bottom: 2rem; flex-wrap: wrap; gap: 2rem; }
        .player-box {
            background: #334155;
            border-radius: 20px;
            padding: 20px;
            flex: 1 1 150px;
            max-width: 220px;
            box-shadow: inset 0 0 10px #1e293b;
            transition: background 0.3s ease;
        }
        .player-box h2 {
            margin: 0 0 15px;
            font-weight: 600;
            font-size: 1.2rem;
            color: #60a5fa;
            text-shadow: 0 0 5px #2563eb;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            filter: drop-shadow(0 0 5px rgba(96, 165, 250, 0.7));
        }
        .icon-wrapper svg {
            width: 100%;
            height: 100%;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke: #f1f5f9;
            transition: stroke 0.3s ease;
        }
        .player-box p { font-size: 1.1rem; font-weight: 500; color: #e0e7ff; min-height: 2rem; }
        .result-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: #facc15;
            text-shadow: 0 0 10px #fbbf24;
            margin-bottom: 1.5rem;
            min-height: 2.5rem;
        }
        .reset-btn {
            background: transparent;
            border: 2px solid #fbbf24;
            color: #fbbf24;
            font-weight: 600;
            font-size: 1rem;
            padding: 10px 30px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            user-select: none;
        }
        .reset-btn:hover, .reset-btn:focus {
            background: #fbbf24;
            color: #1e293b;
            box-shadow: 0 0 15px #fbbf24;
            outline: none;
        }
    </style>
</head>
<body>
<h1>Pierre, Feuille, Ciseaux,<br>Lézard, Spock</h1>
<p class="subtitle">Choisissez votre arme !</p>

<div class="choices">
    <a href="?player=pierre" class="choice-btn"><?= $svgIcons['pierre'] ?> Pierre</a>
    <a href="?player=feuille" class="choice-btn"><?= $svgIcons['feuille'] ?> Feuille</a>
    <a href="?player=ciseaux" class="choice-btn"><?= $svgIcons['ciseaux'] ?> Ciseaux</a>
    <a href="?player=lezard" class="choice-btn"><?= $svgIcons['lezard'] ?> Lézard</a>
    <a href="?player=spock" class="choice-btn"><?= $svgIcons['spock'] ?> Spock</a>
</div>

<section class="result-container">
    <div class="players">
        <div class="player-box">
            <h2>Vous</h2>
            <div class="icon-wrapper"><?= $playerSvg ?></div>
            <p><?= $playerLabel ?></p>
        </div>
        <div class="player-box">
            <h2>Ordinateur</h2> <div class="icon-wrapper"><?= $phpSvg ?></div>
            <p><?= $phpLabel ?></p>
        </div>
    </div>
    <div class="result-text"><?= $result ?></div>
    <a href="./" class="reset-btn">🔄 Réinitialiser</a>
</section>
</body>
</html>