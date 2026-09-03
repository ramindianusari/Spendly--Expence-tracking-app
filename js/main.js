/**
 * Expense Tracker — Frontend JS
 * Handles: form validation, logout confirm, dynamic UI
 */

/* ============================================================
   LOGIN PAGE — Client-side validation
   ============================================================ */
(function () {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;

    loginForm.addEventListener('submit', function (e) {
        const email    = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const errEl    = document.getElementById('loginError');

        // Clear previous error
        if (errEl) errEl.textContent = '';

        // Basic client-side checks
        if (!email || !password) {
            e.preventDefault();
            showError(errEl, 'Please enter your email and password.');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            showError(errEl, 'Please enter a valid email address.');
            return;
        }

        if (password.length < 6) {
            e.preventDefault();
            showError(errEl, 'Password must be at least 6 characters.');
            return;
        }

        // Show loading state on the button
        const btn = loginForm.querySelector('button[type="submit"]');
        if (btn) {
            btn.textContent = 'Logging in…';
            btn.disabled    = true;
        }
    });

    function showError(el, msg) {
        if (el) {
            el.textContent = msg;
            el.style.display = 'block';
        }
    }
})();

/* ============================================================
   HOME PAGE — Logout confirmation
   ============================================================ */
(function () {
    const logoutBtn = document.getElementById('logoutBtn');
    if (!logoutBtn) return;

    logoutBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (confirm('Are you sure you want to log out?')) {
            window.location.href = 'logout.php';
        }
    });
})();

/* ============================================================
   HOME PAGE — Animate balance on page load
   ============================================================ */
(function () {
    const balanceEl = document.getElementById('totalBalance');
    if (!balanceEl) return;

    const target   = parseFloat(balanceEl.dataset.value || '0');
    const duration = 800; // ms
    const start    = performance.now();

    function step(now) {
        const elapsed  = now - start;
        const progress = Math.min(elapsed / duration, 1);
        // Ease-out cubic
        const eased    = 1 - Math.pow(1 - progress, 3);
        const current  = Math.round(eased * target);
        balanceEl.textContent = 'Rs. ' + current.toLocaleString('en-IN');
        if (progress < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
})();

/* ============================================================
   HOME PAGE — Category icon mapping
   ============================================================ */
const CATEGORY_ICONS = {
    'salary':     '💰',
    'freelance':  '💰',
    'food':       '🍔',
    'transport':  '🚌',
    'shopping':   '🛍️',
    'health':     '💊',
    'bills':      '📄',
    'education':  '📚',
    'travel':     '✈️',
    'investment': '📈',
    'other':      '📦',
};

function getCategoryIcon(category) {
    const key = (category || '').toLowerCase();
    return CATEGORY_ICONS[key] || '💳';
}

// Expose globally for PHP-rendered pages that inject category values
window.getCategoryIcon = getCategoryIcon;
