<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
    <link rel="icon" type="image/x-icon" href="/public/assets/img/favgolem.ico">
    <link rel="stylesheet" href="/public/assets/includes/styles/style.css">
</head>
<body>
<div class="content">
    <div class="login-rectangle">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER]Image de connexion" class="log-img">
        <div class="rectangle-title">Créer un compte</div>
        <form class="input-rectangles" id="register-form">
            <label for="username"></label>
            <input type="text" id="username" placeholder="Nom d'utilisateur" class="input-rectangle" required>
            <label for="email"></label>
            <input type="email" id="email" placeholder="Email" class="input-rectangle" required>
            <label for="password"></label>
            <input type="password" id="password" placeholder="Mot de passe" class="input-rectangle" required>
            <label for="confirm-password"></label>
            <input type="password" id="confirm-password" placeholder="Confirmer" class="input-rectangle" required>
            <button type="submit" class="input-rectangle" style="background:#1360AA;color:#fff;cursor:pointer;font-size:1.2em;">Créer le compte</button>
        </form>
        <a href="login.php" class="text-link">Vous possédez déjà un compte ?</a>
    </div>
</div>

<script>
    function setThemeIcon() {
        const btn = document.getElementById('theme-toggle');
        if (document.body.classList.contains('dark-theme')) {
            btn.innerHTML = '🌙';
        } else {
            btn.innerHTML = '☀️';
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
    window.addEventListener('DOMContentLoaded', applySavedTheme);

    document.getElementById('register-form').addEventListener('submit', function(e) {
        const pwd = document.getElementById('password').value;
        const confirm = document.getElementById('confirm-password').value;
        if (pwd !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
        }
    });
</script>
</body>
</html>