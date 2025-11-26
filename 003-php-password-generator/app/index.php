<?php

// --- Character Set Configuration ---
$characterSets = [
    'uppercase' => implode('', range('A', 'Z')),
    'lowercase' => implode('', range('a', 'z')),
    'numbers'   => implode('', range(0, 9)),
    'symbols'   => "!@#$%^&*()-_+=[]{}|:;'\.<>?/~`"
];

// Génère les options HTML pour le sélecteur de longueur (8 à 42).
function generateSelectOptions(int $selectedValue): string {
    $optionsHtml = '';
    for ($i = 8; $i <= 42; $i++) {
        $selected = ($i === $selectedValue) ? 'selected' : '';
        $optionsHtml .= "<option value=\"$i\" $selected>$i</option>";
    }
    return $optionsHtml;
}

// Retourne un caractère aléatoire cryptographiquement sûr à partir d'une chaîne.
function generateRandomCharacter(string $charSet): string {
    if (empty($charSet)) {
        return '';
    }
    $charSetLength = strlen($charSet);
    try {
        $randomIndex = random_int(0, $charSetLength - 1);
    } catch (Exception $e) {
        // Fallback sûr si random_int échoue
        $randomIndex = mt_rand(0, $charSetLength - 1);
    }
    return $charSet[$randomIndex];
}

// Génère le mot de passe, garantissant au moins un caractère de chaque type sélectionné.
function generatePassword(int $length, bool $includeUpper, bool $includeLower, bool $includeNum, bool $includeSym, array $characterSets): string {

    $password = "";
    $chosenSequences = [];
    $requiredCharsCount = 0;

    // 1. Define sequences and count required characters
    if ($includeUpper) {
        $chosenSequences[] = $characterSets['uppercase'];
        $requiredCharsCount++;
    }
    if ($includeLower) {
        $chosenSequences[] = $characterSets['lowercase'];
        $requiredCharsCount++;
    }
    if ($includeNum) {
        $chosenSequences[] = $characterSets['numbers'];
        $requiredCharsCount++;
    }
    if ($includeSym) {
        $chosenSequences[] = $characterSets['symbols'];
        $requiredCharsCount++;
    }

    if (empty($chosenSequences)) {
        return "Sélectionnez au moins un type de caractère.";
    }

    // Ajuster la longueur si elle est inférieure au nombre de types requis
    if ($length < $requiredCharsCount) {
        $length = $requiredCharsCount;
    }

    $remainingCharsToGenerate = $length - $requiredCharsCount;

    // 2. Ajouter un caractère obligatoire de chaque type sélectionné
    foreach ($chosenSequences as $charSet) {
        $password .= generateRandomCharacter($charSet);
    }

    // 3. Remplir le reste du mot de passe aléatoirement
    if ($remainingCharsToGenerate > 0) {
        $sequencesCount = count($chosenSequences);
        for ($i = 0; $i < $remainingCharsToGenerate; $i++) {
            // Choisir une séquence au hasard
            try {
                $randomSeqIndex = random_int(0, $sequencesCount - 1);
            } catch (Exception $e) {
                $randomSeqIndex = mt_rand(0, $sequencesCount - 1);
            }
            $randomSequence = $chosenSequences[$randomSeqIndex];

            $password .= generateRandomCharacter($randomSequence);
        }
    }

    // 4. Mélanger le mot de passe pour masquer les positions obligatoires
    return str_shuffle($password);
}

// --- Form State Control ---

$displayedPassword = "Cliquez sur Générer";
$passwordLength = 12;

// Options par défaut
$formOptions = [
    'uppercase' => 1,
    'lowercase' => 1,
    'numbers'   => 1,
    'symbols'   => 0
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des données POST et validation de la longueur
    $passwordLength = $_POST['password_length'] ?? 12;
    $passwordLength = (int) max(8, min(42, $passwordLength));

    // Récupération des options des cases à cocher (0 si non cochée)
    $formOptions['uppercase'] = $_POST['include_uppercase'] ?? 0;
    $formOptions['lowercase'] = $_POST['include_lowercase'] ?? 0;
    $formOptions['numbers']   = $_POST['include_numbers']   ?? 0;
    $formOptions['symbols']   = $_POST['include_symbols']   ?? 0;

    // Génération du mot de passe
    $displayedPassword = generatePassword(
        $passwordLength,
        (bool)$formOptions['uppercase'],
        (bool)$formOptions['lowercase'],
        (bool)$formOptions['numbers'],
        (bool)$formOptions['symbols'],
        $characterSets
    );
}

// --- Prepare HTML Variables for Persistence ---

$isCheckedUppercase  = $formOptions['uppercase'] ? 'checked' : '';
$isCheckedLowercase  = $formOptions['lowercase'] ? 'checked' : '';
$isCheckedNumbers    = $formOptions['numbers']   ? 'checked' : '';
$isCheckedSymbols    = $formOptions['symbols']   ? 'checked' : '';

$optionsLengthHtml = generateSelectOptions($passwordLength);


// --- HTML Render (Heredoc) ---

$html_content = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de mots de passe Sécurisé</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

<main class="container">
    <nav class="app-nav">
        <a href="index.php" class="nav-item active">Générateur</a>
        <a href="validator.php" class="nav-item">Validateur</a>
    </nav>
    
    <h1>🔑 Générateur de Mots de Passe</h1>
    
    <form action="" method="POST" class="password-form">
        
        <div class="password-display">
            <input 
                type="text" 
                id="generated-password" 
                value="$displayedPassword" 
                readonly
                aria-label="Mot de passe généré"
            >
        </div>
        
        <div class="options-group">
            <h2 class="options-title">Options de sécurisation</h2>
            
            <div class="select-item">
                <label for="password_length">Longueur du mot de passe</label>
                <select name="password_length" id="password_length">
                    $optionsLengthHtml
                </select>
            </div>

            <div class="checkbox-item">
                <input type="checkbox" id="include_uppercase" name="include_uppercase" value="1" $isCheckedUppercase>
                <label for="include_uppercase">Majuscules (A-Z)</label>
            </div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="include_lowercase" name="include_lowercase" value="1" $isCheckedLowercase>   
                <label for="include_lowercase">Minuscules (a-z)</label>
            </div> 
            <div class="checkbox-item">
                <input type="checkbox" id="include_numbers" name="include_numbers" value="1" $isCheckedNumbers>
                <label for="include_numbers">Chiffres (0-9)</label>
            </div>
            
            <div class="checkbox-item">
                <input type="checkbox" id="include_symbols" name="include_symbols" value="1" $isCheckedSymbols>
                <label for="include_symbols">Symboles (!@#$)</label>
            </div>
        </div>
            
        <button type="submit" class="generate-btn">Générer le Mot de Passe</button>

    </form>
</main>

</body>
</html>
HTML;

echo $html_content;