// Plain JS interactions for mobile menu and best practice modal
(function () {
  function qs(sel) { return document.querySelector(sel); }
  const mobileBtn = qs('#open-mobile');
  const mobileMenu = qs('#mobile-menu');
  const bpBackdrop = qs('#bp-backdrop');
  const bpDialog = qs('#bp-dialog');
  const bpClose = qs('#bp-close');
  const bpOpen = qs('#bp-open');

  // Mobile menu toggle
  if (mobileBtn && mobileMenu) {
    mobileBtn.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });
  }

  // Show best practice dialog after 1s
  function openDialog() {
    bpBackdrop.classList.remove('hidden');
    bpDialog.classList.remove('hidden');
    if (bpOpen) bpOpen.classList.add('hidden');
  }
  function closeDialog() {
    bpBackdrop.classList.add('hidden');
    bpDialog.classList.add('hidden');
    if (bpOpen) bpOpen.classList.remove('hidden');
  }

  // Auto-open only once per session; otherwise, leave closed and show the reopen button
  if (bpDialog) {
    try {
      const hasShown = sessionStorage.getItem('bpShown');
      if (!hasShown) {
        sessionStorage.setItem('bpShown', '1');
        setTimeout(openDialog, 1000);
      } else {
        // Do not auto-open again on navigation; ensure the open button is visible
        if (bpOpen) bpOpen.classList.remove('hidden');
      }
    } catch (e) {
      // If sessionStorage is unavailable, prefer not to auto-open repeatedly
      if (bpOpen) bpOpen.classList.remove('hidden');
    }
  }

  if (bpClose) {
    bpClose.addEventListener('click', closeDialog);
  }
  if (bpBackdrop) {
    bpBackdrop.addEventListener('click', closeDialog);
  }
  if (bpOpen) {
    bpOpen.addEventListener('click', openDialog);
  }

  // Close on Escape for accessibility
  document.addEventListener('keydown', (e) => {
    if (bpDialog && e.key === 'Escape' && !bpDialog.classList.contains('hidden')) {
      closeDialog();
    }
  });

  // Initialize lucide icons
  if (window.lucide) {
    window.lucide.createIcons();
  } else {
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) { window.lucide.createIcons(); }
    });
  }
})();