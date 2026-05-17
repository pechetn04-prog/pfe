/*
 * ====================================================================
 * GESTION ASYNCHRONE DE L'ÉTAT DES NOTIFICATIONS (FETCH API)
 * ====================================================================
 * Ce script gère la mise à jour en arrière-plan des notifications de l'utilisateur.
 * Il comprend deux fonctionnalités majeures :
 * 1. markRead : Marquer une notification comme lue et rediriger l'utilisateur.
 * 2. markAllRead : Marquer d'un coup toutes les alertes comme lues.
 * 
 * Sécurité : Transmission obligatoire du jeton de sécurité CSRF Laravel.
 */

// Marquer une notification spécifique comme lue au clic de l'utilisateur
function markRead(id, url) {
    // Requête POST en arrière-plan vers l'API Laravel
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            // Inclusion obligatoire du jeton de sécurité de session Laravel
            'X-CSRF-TOKEN': window.csrfToken, 
            'Content-Type': 'application/json'
        }
    }).then(() => {
        // Redirige vers le lien associé (ex: fiche du dossier) ou rafraîchit la page
        if (url && url !== '#') {
            window.location.href = url;
        } else {
            location.reload();
        }
    });
}

// Marquer toutes les notifications de l'utilisateur connecté comme lues
function markAllRead() {
    // Requête POST globale vers l'API
    fetch(`/notifications/read-all`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        // Recharge la page courante pour réinitialiser les compteurs visuels et effacer les alertes
        location.reload();
    });
}
