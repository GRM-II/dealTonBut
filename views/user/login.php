    <div class="content">
        <div class="login-rectangle">
            <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de connexion" class="log-img">
            <div class="rectangle-title">Connexion</div>
            <form class="input-rectangles" method="POST" action="?controller=user&action=login">
                <label for="username"></label>
                <input type="text" id="username" name="login" placeholder="Nom d'utilisateur ou email" class="input-rectangle" required>

                <label for="password"></label>
                <input type="password" id="password" name="password" placeholder="Mot de passe" class="input-rectangle" required>

                <button type="submit" name="submit" class="input-rectangle" style="background:#1360AA;color:#fff;cursor:pointer;font-size:1.2em;">Connexion</button>
            </form>

            <a href="#" class="text-link">Mot de passe oublié ?</a>
            <a href="index.php?controller=homepage&action=login" class="text-link">Vous ne possédez pas de compte ?</a>
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
    </script>