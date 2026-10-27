import './bootstrap';
import './university-select';

function initSidebarToggle() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    const closeBtn = document.getElementById('sidebarCloseBtn');
    const body = document.body;

    if (!toggleBtn && !backdrop && !closeBtn) return;

    const isDesktop = () => window.innerWidth > 900;

    // Restore desktop preference on load
    if (isDesktop()) {
        const savedState = localStorage.getItem('jimp_sidebar_closed');
        if (savedState === '1') {
            body.classList.add('sidebar-closed');
            document.documentElement.classList.add('sidebar-closed');
        }
    }

    const toggleSidebar = () => {
        if (isDesktop()) {
            body.classList.toggle('sidebar-closed');
            document.documentElement.classList.toggle('sidebar-closed');
            const isClosed = body.classList.contains('sidebar-closed');
            localStorage.setItem('jimp_sidebar_closed', isClosed ? '1' : '0');
        } else {
            body.classList.toggle('sidebar-mobile-open');
        }
    };

    const closeMobileSidebar = () => {
        body.classList.remove('sidebar-mobile-open');
    };

    if (toggleBtn) {
        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            closeMobileSidebar();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeMobileSidebar);
    }

    // Close mobile drawer when clicking a link
    const navLinks = document.querySelectorAll('.upper-sidebar .sidebar-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (!isDesktop()) {
                closeMobileSidebar();
            }
        });
    });

    // Close with Escape key
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    // Reset mobile state on window resize to desktop
    window.addEventListener('resize', () => {
        if (isDesktop()) {
            body.classList.remove('sidebar-mobile-open');
        }
    });
}

function initNotificationDismissal() {
    // Automatically dismiss any legacy notices after at least 5.5 seconds
    const legacyNotices = document.querySelectorAll('.hr-notice, .admin-status');
    legacyNotices.forEach(notice => {
        if (notice.dataset.timerAttached) return;
        notice.dataset.timerAttached = 'true';
        setTimeout(() => {
            notice.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            notice.style.opacity = '0';
            notice.style.transform = 'translateY(-6px)';
            setTimeout(() => {
                if (notice.parentNode) notice.parentNode.removeChild(notice);
            }, 400);
        }, 5500);
    });
}

function initAll() {
    initSidebarToggle();
    initNotificationDismissal();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
} else {
    initAll();
}

(function() {
            // Department filtering
            const deptButtons = document.querySelectorAll('.js-filter-dept');
            const roleCards = document.querySelectorAll('.js-role-card');

            deptButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    deptButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const deptId = this.dataset.deptId;
                    roleCards.forEach(card => {
                        if (deptId === 'all' || card.dataset.deptId === deptId) {
                            card.style.display = 'flex';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            // Flashcard Modal
            const modal = document.getElementById('roleFlashcardModal');
            const closeBtn = document.getElementById('closeFlashcardBtn');
            const dismissBtn = document.getElementById('btnDismissFlashcard');

            function openFlashcard(card) {
                document.getElementById('flashcardTitle').textContent = card.dataset.roleName;
                document.getElementById('flashcardDeptBadge').textContent = card.dataset.deptName;
                document.getElementById('flashcardFieldStudy').textContent = card.dataset.fieldStudy;
                document.getElementById('flashcardDescription').textContent = card.dataset.desc;
                document.getElementById('flashcardRequirements').textContent = card.dataset.reqs;

                modal.style.display = 'flex';
                modal.setAttribute('aria-hidden', 'false');
            }

            function closeFlashcard() {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            }

            roleCards.forEach(card => {
                card.addEventListener('click', function(e) {
                    openFlashcard(this);
                });
            });

            if (closeBtn) closeBtn.addEventListener('click', closeFlashcard);
            if (dismissBtn) dismissBtn.addEventListener('click', closeFlashcard);

            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeFlashcard();
            });

            window.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    closeFlashcard();
                }
            });
        })();
