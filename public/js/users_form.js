/*
 * ====================================================================
 * GESTION INTERACTIVE DU FORMULAIRE DE CRÉATION/ÉDITION D'UTILISATEUR
 * ====================================================================
 * Ce script gère l'affichage dynamique et conditionnel des champs de saisie.
 * 
 * Règle métier :
 * - Le champ "Spécialité" (compétences techniques de réparation) n'a de sens que pour le rôle "Technicien".
 * - Si le rôle choisi est "Technicien", le champ s'affiche de manière fluide.
 * - Si un autre rôle est sélectionné (Administrateur, Agent), le champ spécialité est masqué
 *   pour éviter toute confusion.
 */

// Fonction pour afficher ou masquer le bloc de saisie des spécialités en fonction du rôle sélectionné
function toggleSpecialite(role) {
    const field = document.getElementById('specialiteField');
    if (field) {
        // Affiche si c'est un Technicien (display block), sinon masque complètement (display none)
        if (role === 'Technicien') {
            field.classList.remove('d-none-init');
            field.style.display = 'block';
        } else {
            field.style.display = 'none';
        }
    }
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    if (roleSelect) {
        // Exécute la vérification immédiatement pour s'adapter à la valeur par défaut
        toggleSpecialite(roleSelect.value);
        
        // Écouteur de changement de sélection sur le champ de rôle
        roleSelect.addEventListener('change', function() {
            toggleSpecialite(this.value);
        });
    }
});
