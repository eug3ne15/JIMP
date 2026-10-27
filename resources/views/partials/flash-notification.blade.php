@php
    $flashStatus = session('status') ?? session('success') ?? session('message');
@endphp
@if ($flashStatus)
    <div class="site-notification-toast" id="siteNotificationToast" role="alert" aria-live="polite" data-duration="5500">
        <div class="toast-indicator"></div>
        <div class="toast-icon">
            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <div class="toast-body">
            <span class="toast-label">Notification Update</span>
            <p class="toast-message">{{ $flashStatus }}</p>
        </div>
        <button type="button" class="toast-close-btn" id="toastCloseBtn" aria-label="Close notification" title="Dismiss">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
        <div class="toast-progress-bar">
            <div class="toast-progress-fill" id="toastProgressFill"></div>
        </div>
    </div>

    <script>
        (function() {
            const toast = document.getElementById('siteNotificationToast');
            if (!toast) return;

            const duration = parseInt(toast.dataset.duration || '5500', 10);
            let timer = null;
            let startTime = Date.now();
            let remaining = duration;
            let isPaused = false;
            const progressFill = document.getElementById('toastProgressFill');

            function dismissToast() {
                if (!toast || toast.classList.contains('dismissing')) return;
                toast.classList.add('dismissing');
                setTimeout(function() {
                    if (toast && toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 400);
            }

            function startTimer() {
                startTime = Date.now();
                timer = setTimeout(dismissToast, remaining);
            }

            function pauseTimer() {
                clearTimeout(timer);
                remaining -= Date.now() - startTime;
                isPaused = true;
                if (progressFill) {
                    progressFill.style.animationPlayState = 'paused';
                }
            }

            function resumeTimer() {
                if (remaining <= 0) {
                    dismissToast();
                    return;
                }
                startTime = Date.now();
                timer = setTimeout(dismissToast, remaining);
                isPaused = false;
                if (progressFill) {
                    progressFill.style.animationPlayState = 'running';
                }
            }

            toast.addEventListener('mouseenter', pauseTimer);
            toast.addEventListener('mouseleave', resumeTimer);

            const closeBtn = document.getElementById('toastCloseBtn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    clearTimeout(timer);
                    dismissToast();
                });
            }

            startTimer();
        })();
    </script>
@endif
