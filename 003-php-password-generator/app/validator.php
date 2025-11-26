<?php

function validatePassword(string $password, int &$score, array &$messages): void {

    // Règle 1: Longueur minimale (au moins 8 caractères)
    if (strlen($password) >= 12) {
        $messages['length'] = "✅ Longueur: Le mot de passe a au moins 12 caractères.";
        $score += 20;
    } else {
        $messages['length'] = "❌ Longueur: Le mot de passe doit avoir au moins 12 caractères.";
    }

    // Règle 2: Contient des majuscules (/[A-Z]/)
    if (preg_match('/[A-Z]/', $password)) {
        $messages['uppercase'] = "✅ Majuscules: Contient des lettres majuscules.";
        $score += 20;
    } else {
        $messages['uppercase'] = "❌ Majuscules: Manque de lettres majuscules (A-Z).";
    }

    // Règle 3: Contient des minuscules (/[a-z]/)
    if (preg_match('/[a-z]/', $password)) {
        $messages['lowercase'] = "✅ Minuscules: Contient des lettres minuscules.";
        $score += 20;
    } else {
        $messages['lowercase'] = "❌ Minuscules: Manque de lettres minuscules (a-z).";
    }

    // Règle 4: Contient des chiffres (/[\d]/)
    if (preg_match('/\d/', $password)) { // \d est équivalent à [0-9]
        $messages['numbers'] = "✅ Chiffres: Contient des chiffres.";
        $score += 20;
    } else {
        $messages['numbers'] = "❌ Chiffres: Manque de chiffres (0-9).";
    }

    // Règle 5: Contient des symboles (/[\W]/)
    // Note: \W correspond à tout ce qui n'est pas [a-zA-Z0-9_].
    if (preg_match('/[\W_]/', $password)) {
        $messages['symbols'] = "✅ Symboles: Contient des symboles.";
        $score += 20;
    } else {
        $messages['symbols'] = "❌ Symboles: Manque de symboles ou caractères spéciaux.";
    }

}

// --- Form State Control ---

$passwordToValidate = "";
$validationScore = "-";
$validationMessages = ["-" => "Saisissez un mot de passe pour commencer la validation."]; // Initialisation avec un message

// Récupération du mot de passe saisi uniquement si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération du mot de passe saisi depuis $_POST
    $passwordToValidate = $_POST['password_input'] ?? '';

    // Initialisation des résultats pour une nouvelle validation
    $validationScore = 0;
    $validationMessages = [];

    // --- LOGIQUE DE VALIDATION REGEX ---

    if (empty($passwordToValidate)) {
        $validationMessages['empty'] = "Veuillez saisir un mot de passe.";
        $validationScore = '-';
    } else {
        // Appel de la fonction de validation par référence
        validatePassword($passwordToValidate, $validationScore, $validationMessages);
    }
}

// --- Préparation du rendu HTML (Persistance) ---

// Valeur de la zone de texte conservée
$passwordValue = htmlspecialchars($passwordToValidate);

// Couleur du score
$scoreColor = 'var(--color-primary)';
if (is_numeric($validationScore)) {
    if ($validationScore < 40) {
        $scoreColor = 'var(--color-red)';
    } elseif ($validationScore >= 80) {
        $scoreColor = 'var(--color-secondary)';
    } else {
        $scoreColor = 'var(--color-yellow)';
    }
}


// --- Rendu HTML (Heredoc) ---

$html_content = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validateur de Mot de Passe Sécurisé</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<main class="container">
    <nav class="app-nav">
        <a href="index.php" class="nav-item">Générateur</a>
        <a href="validator.php" class="nav-item active">Validateur</a>
    </nav>
    
    <h1>🛡️ Validateur de Sécurité</h1>
    
    <form action="" method="POST" class="validation-form">
        
        <div class="password-input-area">
            <label for="password_input" class="input-label">Mot de passe à valider :</label>
            <input 
                type="text" 
                id="password_input" 
                name="password_input"
                value="$passwordValue" 
                placeholder="Entrez votre mot de passe"
                required
            >
        </div>
        
        <button type="submit" class="validate-btn">Valider la Sécurité</button>

    </form>

    <section class="validation-results">
        <h2 class="score-title">Score de Sécurité : <span class="score-display" style="color:$scoreColor;">$validationScore / 100</span></h2>
        
        <div class="messages-list">
            <ul>
HTML;

// Affichage des messages de validation
foreach ($validationMessages as $messageKey => $messageText) {
    // Détermine la classe CSS en fonction du message (Vérifie la présence de "❌" ou "✅")
    if (strpos($messageText, '✅') !== false) {
        $class = 'valid';
    } elseif (strpos($messageText, '❌') !== false) {
        $class = 'invalid';
    } else {
        $class = 'neutral';
    }

    $html_content .= "<li class='message-item $class'>$messageText</li>";
}

$html_content .= <<<HTML
            </ul>
        </div>
    </section>

</main>

</body>
</html>
HTML;

echo $html_content;