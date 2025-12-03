document.addEventListener('DOMContentLoaded', () => {
    // Initialize Lucide Icons
    lucide.createIcons();

    // Navigation Logic
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.view-section');

    function navigateTo(viewId) {
        // Update Nav State
        navItems.forEach(item => {
            if (item.dataset.view === viewId) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        // Update View State
        sections.forEach(section => {
            section.classList.remove('active');
            if (section.id === viewId) {
                // Small delay to allow fade out if we were doing complex transitions
                // For now, just switch
                setTimeout(() => {
                    section.classList.add('active');
                }, 50);
            }
        });
    }

    // Add Click Listeners
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            const viewId = item.dataset.view;
            navigateTo(viewId);
        });
    });

    // Interactive Cards (3D Tilt Effect)
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = ((y - centerY) / centerY) * -5; // Max rotation deg
            const rotateY = ((x - centerX) / centerX) * 5;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
        });
    });

    console.log('System Initialized. Welcome, Agent.');

    // Modal Logic
    const modalOverlay = document.getElementById('modal-overlay');
    const modal = document.getElementById('modal-bp');
    const closeBtn = document.getElementById('close-modal-btn');
    const ackBtn = document.getElementById('ack-btn');
    const openBtn = document.getElementById('open-bp-btn');

    function openModal() {
        modalOverlay.classList.add('active');
        // Small delay for animation
        setTimeout(() => {
            modal.classList.add('active');
        }, 10);
    }

    function closeModal() {
        modal.classList.remove('active');
        setTimeout(() => {
            modalOverlay.classList.remove('active');
        }, 300);
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (ackBtn) ackBtn.addEventListener('click', closeModal);
    if (modalOverlay) modalOverlay.addEventListener('click', closeModal);
    if (openBtn) openBtn.addEventListener('click', openModal);

    // Auto-open logic - Always open on launch
    setTimeout(openModal, 500);
});
