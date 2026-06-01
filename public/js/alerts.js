// Disparition automatique des alertes flash après 4 secondes avec animation fluide
$(document).ready(function() {
    setTimeout(function() {
        $('.alert-dismissible').fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 4000); // 4 secondes d'affichage + 1 seconde de transition
});
