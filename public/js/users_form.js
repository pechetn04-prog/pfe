/**
 * Gestion de l'affichage du champ spécialité selon le rôle
 */
function toggleSpecialite(role) {
    const field = document.getElementById('specialiteField');
    if (field) {
        field.style.display = role === 'Technicien' ? 'block' : 'none';
    }
}

// Initialisation au chargement si un rôle est déjà sélectionné
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.querySelector('select[name="role"]');
    if (roleSelect) {
        toggleSpecialite(roleSelect.value);
        roleSelect.addEventListener('change', function() {
            toggleSpecialite(this.value);
        });
    }
});
