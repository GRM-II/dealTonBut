<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DealTonBut - Connexion</title>
    <link rel="stylesheet" href="/public/assets/includes/styles/style.css">
    <link rel="icon" type="image/x-icon" href="/public/assets/img/favicon.ico">
</head>
<body>
<header>
    <div class="header-left">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER] amoU" class="header-logo">
    </div>
    <button id="theme-toggle" onclick="toggleTheme()" aria-label="Change theme"></button>
</header>

<main>
    <div class="content">
        <div class="login-rectangle">
            <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de connexion" class="log-img">
            <div class="rectangle-title">Connexion</div>

            <?php if (isset($error)): ?>
                <div style="color: red; padding: 10px; background: #ffe0e0; margin: 10px 0; border-radius: 5px;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form class="input-rectangles" method="POST" action="?controller=user&action=login">
                <label for="username"></label>
                <input type="text"
                       id="username"
                       name="login"
                       placeholder="Nom d'utilisateur ou email"
                       class="input-rectangle"
                       required>

                <label for="password"></label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Mot de passe"
                       class="input-rectangle"
                       required>

                <button type="submit"
                        name="submit"
                        class="input-rectangle"
                        style="background:#1360AA;color:#fff;cursor:pointer;font-size:1.2em;">
                    Connexion
                </button>
            </form>

            <a href="#" class="text-link">Mot de passe oublié ?</a>
            <a href="index.php?controller=homepage&action=login" class="text-link">Vous ne possédez pas de compte ?</a>
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

    // IMPORTANT : Pas de hashing côté client !
    // Le mot de passe est envoyé en clair via HTTPS et hashé côté serveur
</script>
</body>
</html>