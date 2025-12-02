<?php
// Fichier : inc/search_bar_template.php
function renderSearchBar($currentQuery = '', $currentFilter = 'all') {
    $currentQuery = htmlspecialchars($currentQuery);

    // On génère un ID unique pour éviter les conflits si le script est chargé plusieurs fois
    $uniqueId = uniqid();

    $searchSvg = SEARCH_SVG;

    return <<<HTML
    <form action="search.php" method="GET" style="margin-bottom: 30px; width: 100%; max-width: 600px;">
        
        <div style="display: flex; align-items: center; background-color: #23232D; border-radius: 4px; padding: 4px 12px; height: 32px; border: 1px solid transparent; transition: background-color 0.2s; position: relative;">
            
            <button type="submit" style="background: none; border: none; padding: 0; margin-right: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; color: #A2A2AD; opacity: 0.7;">
                $searchSvg
            </button>
            
            <input type="text"
                   id="search-input-$uniqueId"
                   name="query"
                   placeholder="Artistes, titres, podcasts..."
                   value="$currentQuery"
                   autocomplete="off"
                   style="background: transparent; border: none; outline: none; width: 100%; color: #FFFFFF; font-family: inherit; font-size: 14px; font-weight: 500; padding-right: 30px;">
            
            <button type="button"
                    id="search-clear-$uniqueId"
                    style="display: none; background: none; border: none; padding: 0; cursor: pointer; align-items: center; justify-content: center; color: #A2A2AD; position: absolute; right: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>

            <input type="hidden" name="filter" value="$currentFilter">
        </div>
    </form>

    <script>
    (function() {
        var input = document.getElementById('search-input-$uniqueId');
        var clearBtn = document.getElementById('search-clear-$uniqueId');

        function toggleClearBtn() {
            if (input.value.length > 0) {
                clearBtn.style.display = 'flex';
            } else {
                clearBtn.style.display = 'none';
            }
        }

        // Vérifier au chargement (si une recherche est déjà faite)
        toggleClearBtn();

        // Vérifier quand on tape
        input.addEventListener('input', toggleClearBtn);

        // Action quand on clique sur la croix
        clearBtn.addEventListener('click', function() {
            input.value = '';
            toggleClearBtn();
            input.focus(); // Redonne le focus au champ pour retaper direct
        });
    })();
    </script>
HTML;
}
