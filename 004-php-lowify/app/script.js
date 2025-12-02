document.addEventListener('DOMContentLoaded', () => {

    // Fonction pour gérer la navigation sans rechargement
    function handleNavigation(e) {
        // Trouver le lien le plus proche (au cas où on clique sur une image dans le lien)
        const link = e.target.closest('a');

        // Si pas de lien, ou lien externe, ou lien d'action (play/like/delete), on laisse faire le navigateur
        if (!link ||
            link.getAttribute('target') === '_blank' ||
            link.href.includes('play_song.php') ||
            link.href.includes('like_item.php') ||
            link.href.includes('add_to_playlist.php') ||
            link.href.includes('delete_playlist.php') ||
            link.href.includes('remove_from_playlist.php')
        ) {
            return;
        }

        // Empêcher le rechargement standard
        e.preventDefault();
        const url = link.href;

        // Charger la nouvelle page via AJAX
        fetch(url)
            .then(response => response.text())
            .then(html => {
                // Créer un document temporaire pour extraire le contenu
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extraire le nouveau contenu de .main-view
                const newMainContent = doc.querySelector('.main-view');
                const currentMainContent = document.querySelector('.main-view');

                if (newMainContent && currentMainContent) {
                    // Remplacer le contenu
                    currentMainContent.innerHTML = newMainContent.innerHTML;

                    // Mettre à jour l'URL et le titre
                    globalThis.history.pushState({}, '', url);
                    document.title = doc.title;

                    // Remonter en haut de la page
                    currentMainContent.scrollTop = 0;

                    // Mettre à jour la classe 'active' de la sidebar
                    updateSidebarActiveState(url);
                } else {
                    // Fallback si la structure est différente
                    globalThis.location.href = url;
                }
            })
            .catch(err => {
                console.error('Erreur de navigation:', err);
                globalThis.location.href = url;
            });
    }

    // Fonction pour mettre à jour visuellement la sidebar
    function updateSidebarActiveState(url) {
        const navLinks = document.querySelectorAll('.sidebar .nav-link');
        navLinks.forEach(link => {
            // Logique simple de comparaison d'URL
            if (url.includes(link.getAttribute('href'))) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }

            // Cas particulier pour l'accueil
            if (url.endsWith('index.php') || url.endsWith('/')) {
                if(link.getAttribute('href') === 'index.php') link.classList.add('active');
            }
        });
    }

    // Attacher l'événement de clic global (délégation d'événement)
    document.body.addEventListener('click', handleNavigation);

    // Gérer le bouton "Retour" du navigateur
    globalThis.addEventListener('popstate', () => {
        // Recharger la page actuelle pour simplifier la gestion du retour
        globalThis.location.reload();
    });
});