function setThemeIcon() {
    const btn = document.getElementById('theme-toggle');
    if (btn) {
        if (document.body.classList.contains('dark-theme')) {
            btn.innerHTML = '🌙';
        } else {
            btn.innerHTML = '☀️';
        }
    }
}

function applySavedTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    } else {
        document.body.classList.remove('dark-theme');
    }
    setThemeIcon();
}

function toggleTheme() {
    document.body.classList.toggle('dark-theme');
    localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
    setThemeIcon();
}

function initRegisterForm(dbUnavailable, dbMessage) {
    applySavedTheme();
    var form = document.getElementById('register-form');
    if (!form) return;

    if (dbUnavailable) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            alert(dbMessage);
        });
        return;
    }

    form.addEventListener('submit', function(e) {
        const pwd = document.getElementById('password').value;
        const confirm = document.getElementById('confirm-password').value;
        if (pwd !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
        }
    });
}

window.addEventListener('DOMContentLoaded', function() {
    applySavedTheme();
});