document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});

/* Logique Modale */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
        // Forcer le redessin
        modal.offsetHeight;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

// Fermer la modale au clic en dehors
window.onclick = function (event) {
    if (event.target.classList.contains('admin-modal')) {
        closeModal(event.target.id);
    }
}
