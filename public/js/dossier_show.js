/*
 * ====================================================================
 * GESTION INTERACTIVE DE L'AFFICHAGE DU DOSSIER SAV (ONGLETS ET SCROLL)
 * ====================================================================
 * Ce script gère l'état et l'interactivité de la fiche détaillée d'un dossier.
 * Il s'occupe en particulier de :
 * 1. Détecter l'onglet actif demandé via l'URL (paramètre `?tab=nom_onglet`).
 * 2. Activer programmatiquement l'onglet Bootstrap correspondant (ex: Historique, Communication).
 * 3. Faire défiler automatiquement la boîte de dialogue vers le message le plus récent
 *    (bas du scroll) après l'envoi d'un message pour le confort d'utilisation.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Lecture des paramètres de la barre d'adresse
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab'); // Récupère le nom de l'onglet actif demandé
    
    if (tab) {
        // Activation automatique du bouton d'onglet correspondant
        const tabBtn = document.querySelector('[data-bs-target="#' + tab + '"]');
        if (tabBtn) {
            // Déclenche le clic natif sur le bouton Bootstrap
            tabBtn.click();
            
            // Défilement automatique au bas de la discussion si l'onglet est "communication"
            if (tab === 'communication') {
                // Temporisation légère de 300ms pour laisser le DOM se déployer
                setTimeout(() => {
                    const msgBox = document.getElementById('section-messages');
                    if (msgBox) {
                        // Positionne l'ascenseur de défilement tout en bas de la boîte
                        msgBox.scrollTop = msgBox.scrollHeight;
                    }
                }, 300);
            }
        }
    }
});
