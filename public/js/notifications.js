/**
 * Logique JavaScript pour la gestion des notifications (Fetch API)
 */
function markRead(id, url) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        if (url && url !== '#') {
            window.location.href = url;
        } else {
            location.reload();
        }
    });
}

function markAllRead() {
    fetch(`/notifications/read-all`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Content-Type': 'application/json'
        }
    }).then(() => {
        location.reload();
    });
}
