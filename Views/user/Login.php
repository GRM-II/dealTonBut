<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page de base</title>
    <link rel="stylesheet" href="style.css">
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
    </script>
</head>
<body>
<header>
    <div class="header-left">
        <img src="assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER] Amou" class="header-logo">
    </div>
    <button id="theme-toggle" onclick="toggleTheme()" aria-label="Change theme"></button>
</header>
<div class="content">
    <div class="login-rectangle">
        <img src="assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER]Image de connexion" class="log-img">
        <div class="rectangle-title">Connexion</div>
        <div class="input-rectangles">
            <label for="username"></label>
            <input type="text" id="username" placeholder="Nom d'utilisateur" class="input-rectangle">

            <label for="password"></label>
            <input type="password" id="password" placeholder="Mot de passe" class="input-rectangle">
        </div>
        <a href="#" class="text-link">Mot de passe oublié ?</a>
        <a href="register.html" class="text-link">Vous ne possédez pas de compte ?</a>
    </div>
</div>
<footer>
    <p style="text-align:left; margin-left:50px;">Si tu vois ça je suis un mauvais dev JE SUIS UN FOOTER</p>
</footer>
</body>
</html>