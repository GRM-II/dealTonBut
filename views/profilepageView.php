<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DealTonBut - Profil</title>
    <link rel="stylesheet" href="/public/assets/includes/styles/style.css">
    <link rel="icon" type="image/x-icon" href="/public/assets/img/favicon.ico">
</head>
<body>
<header>
    <div class="header-left">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER] amoU" class="header-logo">
    </div>
    <div style="display: flex; gap: 10px; align-items: center;">
        <a href="?controller=user&action=logout" style="padding: 8px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px;">Se déconnecter</a>
        <button id="theme-toggle" onclick="toggleTheme()" aria-label="Change theme"></button>
    </div>
</header>

<main>
    <div class="content">
        <div class="login-rectangle">
            <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de profil" class="log-img">
            <div class="rectangle-title">Profil utilisateur</div>

            <div style="margin-top:20px;">
                <form id="edit-username-form" method="post" action="?controller=profilepage&action=updateProfile" style="display:inline;">
                    <strong>Nom d'utilisateur :</strong>
                    <span id="username-display"><?php echo htmlspecialchars($A_view['username'] ?? 'N/A'); ?></span>
                    <input type="text" id="username-input" name="new_username" value="<?php echo htmlspecialchars($A_view['username'] ?? ''); ?>" style="display:none;width:140px;" class="input-rectangle" required>
                    <button type="button" id="edit-username-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;">Modifier</button>
                    <button type="submit" id="save-username-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;">Enregistrer</button>
                    <button type="button" id="cancel-username-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;">Annuler</button>
                </form>
                <br>
                <form id="edit-email-form" method="post" action="?controller=profilepage&action=updateProfile" style="display:inline;">
                    <strong>Email :</strong>
                    <span id="email-display"><?php echo htmlspecialchars($A_view['email'] ?? 'N/A'); ?></span>
                    <input type="email" id="email-input" name="new_email" value="<?php echo htmlspecialchars($email ?? ''); ?>" style="display:none;width:180px;" class="input-rectangle" required>
                    <button type="button" id="edit-email-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;">Modifier</button>
                    <button type="submit" id="save-email-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;">Enregistrer</button>
                    <button type="button" id="cancel-email-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;">Annuler</button>
                </form>
            </div>

            <form method="post" action="?controller=profilepage&action=updateProfile" style="margin-top:20px;">
                <label for="bio"><strong>Bio :</strong></label><br>
                <textarea id="bio" name="bio" rows="4" cols="40" class="input-rectangle" placeholder="Présentez-vous..."></textarea><br>
                <button type="submit" class="input-rectangle" style="background:#1360AA;color:#fff;cursor:pointer;">Enregistrer la bio</button>
            </form>
        </div>
    </div>
</main>

<footer>
    <div class="footer-left">
        <img src="/public/assets/img/amU_logo.svg" alt="[PLACEHOLDER] amoU" class="footer-logo">
        <p>Mentions légales :</p>
    </div>
</footer>

<script>
    function toggleTheme() {
        document.body.classList.toggle('dark-theme');
        setThemeIcon();
        const theme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
    }

    function setThemeIcon() {
        const btn = document.getElementById('theme-toggle');
        if (btn) {
            btn.innerHTML = document.body.classList.contains('dark-theme') ? '🌙' : '☀️';
        }
    }

    function applySavedTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        }
        setThemeIcon();
    }

    window.addEventListener('DOMContentLoaded', function() {
        applySavedTheme();

        // Edit username
        const editUsernameBtn = document.getElementById('edit-username-btn');
        const saveUsernameBtn = document.getElementById('save-username-btn');
        const cancelUsernameBtn = document.getElementById('cancel-username-btn');
        const usernameDisplay = document.getElementById('username-display');
        const usernameInput = document.getElementById('username-input');

        editUsernameBtn.addEventListener('click', function() {
            usernameDisplay.style.display = 'none';
            usernameInput.style.display = '';
            saveUsernameBtn.style.display = '';
            cancelUsernameBtn.style.display = '';
            editUsernameBtn.style.display = 'none';
            usernameInput.focus();
        });

        cancelUsernameBtn.addEventListener('click', function() {
            usernameDisplay.style.display = '';
            usernameInput.style.display = 'none';
            saveUsernameBtn.style.display = 'none';
            cancelUsernameBtn.style.display = 'none';
            editUsernameBtn.style.display = '';
            usernameInput.value = usernameDisplay.textContent;
        });

        // Edit email
        const editEmailBtn = document.getElementById('edit-email-btn');
        const saveEmailBtn = document.getElementById('save-email-btn');
        const cancelEmailBtn = document.getElementById('cancel-email-btn');
        const emailDisplay = document.getElementById('email-display');
        const emailInput = document.getElementById('email-input');

        editEmailBtn.addEventListener('click', function() {
            emailDisplay.style.display = 'none';
            emailInput.style.display = '';
            saveEmailBtn.style.display = '';
            cancelEmailBtn.style.display = '';
            editEmailBtn.style.display = 'none';
            emailInput.focus();
        });

        cancelEmailBtn.addEventListener('click', function() {
            emailDisplay.style.display = '';
            emailInput.style.display = 'none';
            saveEmailBtn.style.display = 'none';
            cancelEmailBtn.style.display = 'none';
            editEmailBtn.style.display = '';
            emailInput.value = emailDisplay.textContent;
        });
    });
</script>
</body>
</html>