// dossiers/show.js - Gestion des onglets et de l'affichage du dossier
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab) {
        const tabBtn = document.querySelector('[data-bs-target="#' + tab + '"]');
        if (tabBtn) {
            tabBtn.click();
            // Scroll vers la zone de messages si on revient de l'envoi
            if (tab === 'communication') {
                setTimeout(() => {
                    const msgBox = document.getElementById('section-messages');
                    if (msgBox) {
                        msgBox.scrollTop = msgBox.scrollHeight;
                    }
                }, 300);
            }
        }
    }
});
