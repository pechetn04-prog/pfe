/**
 * Gestion du statut des utilisateurs (Actif/Inactif) via Fetch API
 */
function toggleUserStatus(userId) {
    fetch(`/gestion-clients/${userId}/toggle-status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json'
        }
    }).then(response => {
        if (!response.ok) {
            alert('Erreur lors du changement de statut');
        }
    });
}
