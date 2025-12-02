<?php
// Fichier : create_playlist.php

require_once 'inc/page.inc.php';
require_once 'inc/database.inc.php';
require_once 'inc/utils.inc.php';
require_once 'inc/sidebar_template.php';

$page = new HTMLPage("Créer ma playlist");

try {
    $db = new DatabaseManager(
        dsn: 'mysql:host=mysql;dbname=lowify;charset=utf8mb4',
        username: 'lowify',
        password: 'lowifypassword'
    );
} catch (PDOException $ex) {
    exitWith500('db');
}

// --- INITIALISATION DES VARIABLES (Pour éviter les warnings) ---
$message = "";
$playlistName = "";
$description = "";

// --- TRAITEMENT POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $playlistName = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'private');

    // 1. Validation : Nom vide
    if (empty($playlistName)) {
        $message = '<p style="color: #E33E3E; margin-bottom: 15px;">Le nom de la playlist ne peut pas être vide.</p>';
    }
    // 2. Validation : Nom trop long (> 20)
    elseif (mb_strlen($playlistName) > 20) {
        $message = '<p style="color: #E33E3E; margin-bottom: 15px;">Le nom est limité à 20 caractères.</p>';
    }
    // 3. Validation : Description trop longue (> 200)
    elseif (mb_strlen($description) > 200) {
        $message = '<p style="color: #E33E3E; margin-bottom: 15px;">La description est limitée à 200 caractères.</p>';
    }
    else {
        $validStatuses = ['public', 'private', 'collaborative'];
        if (!in_array($status, $validStatuses)) {
            $status = 'private';
        }

        try {
            $sql = "INSERT INTO playlist (name, description, status) VALUES (:name, :description, :status)";
            $db->executeUpdate($sql, [
                'name' => $playlistName,
                'description' => $description,
                'status' => $status
            ]);

            header('Location: profile.php?subfilter=playlists&success=1');
            exit;

        } catch (PDOException $ex) {
            if (strpos($ex->getMessage(), 'Data too long') !== false) {
                $message = '<p style="color: #E33E3E; margin-bottom: 15px;">Données trop longues pour la base de données.</p>';
            } else {
                $message = '<p style="color: #E33E3E; margin-bottom: 15px;">Erreur lors de la création: ' . $ex->getMessage() . '</p>';
            }
        }
    }
}

// --- ASSEMBLAGE FINAL ---

$nowPlayingSongId = $_GET['now_playing'] ?? null;
$sidebarContent = renderSidebar($db, 'create_playlist.php');
$playerHTML = renderPlayerBar($db, $nowPlayingSongId);

// JS : Compteur pour la description (limite 200)
$jsCounter = <<<JS
<script>
function updateCount(val) {
    var len = val.value.length;
    document.getElementById('charCount').innerHTML = len + "/200";
}
</script>
JS;

$mainContentHTML = <<<MAIN_CONTENT
<div class="main-view">
    <div class="page-header" style="padding-bottom: 0;">
        <h1 class="page-title">Créer ma playlist</h1>
    </div>
    
    <div style="max-width: 900px; margin-top: 20px; display: flex; gap: 40px;">

        <div style="flex-shrink: 0; width: 250px; text-align: center;">
            <div style="width: 250px; height: 250px; background-color: #8C52FF; border-radius: 4px; display: flex; justify-content: center; align-items: center; box-shadow: 0 10px 20px rgba(0,0,0,0.5);">
                 <span style="font-size: 30px; font-weight: 700; color: var(--text-main);">Cover</span>
            </div>
            
            <button style="margin-top: 15px; background: var(--text-main); border: none; padding: 8px 15px; border-radius: 20px; font-weight: 600; cursor: pointer;">
                ✏️ Personnaliser
            </button>
        </div>

        <form method="POST" style="flex-grow: 1; max-width: 550px;">
            
            {$message}
            
            <label for="name" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">Nom</label>
            <input type="text" name="name" id="name" required
                   placeholder="Nom de la playlist"
                   value="$playlistName"
                   maxlength="20"
                   style="width: 100%; padding: 12px; border-radius: 4px; border: 1px solid var(--border-color);
                          background-color: var(--bg-card); color: var(--text-main); margin-bottom: 30px; font-size: 16px;">
            
            <div style="margin-bottom: 30px; padding: 20px 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
                
                <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 18px;">Options de visibilité</h3>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <span style="font-size: 20px; margin-right: 15px; color: var(--text-secondary);">🔒</span>
                        <label for="status-private" style="font-weight: 600; cursor: pointer;">Privée</label>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 3px 0 0 35px;">Il n'y a que toi qui puisse voir cette playlist.</p>
                    </div>
                    <input type="radio" id="status-private" name="status" value="private" checked style="transform: scale(1.5);">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <span style="font-size: 20px; margin-right: 15px; color: var(--text-secondary);">🤝</span>
                        <label for="status-collaborative" style="font-weight: 600; cursor: pointer;">Collaborative</label>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 3px 0 0 35px;">Tu peux inviter tes amis à ajouter des titres.</p>
                    </div>
                    <input type="radio" id="status-collaborative" name="status" value="collaborative" style="transform: scale(1.5);">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 20px; margin-right: 15px; color: var(--text-secondary);">🌐</span>
                        <label for="status-public" style="font-weight: 600; cursor: pointer;">Publique</label>
                        <p style="font-size: 13px; color: var(--text-secondary); margin: 3px 0 0 35px;">Tout le monde peut la voir et l'écouter.</p>
                    </div>
                    <input type="radio" id="status-public" name="status" value="public" style="transform: scale(1.5);">
                </div>

            </div>
            
            <div style="display:flex; justify-content: space-between;">
                <label for="description" style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">Description</label>
                <span id="charCount" style="font-size: 12px; color: var(--text-secondary);">0/200</span>
            </div>
            
            <textarea name="description" id="description"
                      placeholder="Description de la playlist (facultatif)"
                      maxlength="200"
                      onkeyup="updateCount(this)"
                      style="width: 100%; padding: 12px; border-radius: 4px; border: 1px solid var(--border-color);
                             background-color: var(--bg-card); color: var(--text-main); font-size: 16px; min-height: 100px; resize: vertical;">$description</textarea>

            <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 40px;">
                <a href="profile.php?subfilter=playlists" style="padding: 12px 25px; border-radius: 50px; color: var(--text-secondary); font-weight: 700; text-decoration: none;">ANNULER</a>
                
                <button type="submit" class="duo-button" style="background: var(--primary); color: var(--bg-dark); padding: 12px 30px; border-radius: 50px; font-size: 16px;">
                    CRÉER
                </button>
            </div>
        </form>
    </div>
</div>
{$jsCounter}
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
