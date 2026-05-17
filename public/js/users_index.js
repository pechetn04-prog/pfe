/*
 * ====================================================================
 * CHANGEMENT ASYNCHRONE DU STATUT COMPTE (ACTIF / INACTIF)
 * ====================================================================
 * Ce script gère l'activation ou la désactivation asynchrone des comptes utilisateurs.
 * 
 * Fonctionnement :
 * - Un clic sur le commutateur (switch) envoie une requête PATCH en arrière-plan.
 * - Évite de recharger entièrement la page web.
 * - Intègre la protection par jeton CSRF obligatoire de Laravel pour sécuriser l'appel.
 */

// Fonction pour commuter l'état d'activité (Actif / Inactif) d'un compte utilisateur
function toggleUserStatus(userId) {
    // Appel AJAX asynchrone (Fetch API) vers la route Laravel de changement d'état
    fetch(`/gestion-clients/${userId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            // Jeton de sécurité CSRF obligatoire exigé par Laravel
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    }).then(response => {
        // Alerte l'utilisateur en cas d'échec de la requête
        if (!response.ok) {
            alert('Erreur réseau ou droits insuffisants lors de la modification du statut.');
        }
    });
}
