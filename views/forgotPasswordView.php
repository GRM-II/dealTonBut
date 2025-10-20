<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<div class="content">
    <div class="login-rectangle">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de réinitialisation" class="log-img">
        <div class="rectangle-title">Mot de passe oublié</div>
        
        <?php if (isset($A_view['success'])): ?>
            <div class="flash-message flash-success">
                Un email de réinitialisation a été envoyé à votre adresse.
            </div>
        <?php endif; ?>
        
        <?php if (isset($A_view['error'])): ?>
            <div class="flash-message flash-error">
                <?php echo htmlspecialchars($A_view['error'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        
        <p style="text-align: center; margin: 20px 0; color: #666;">
            Entrez votre adresse email pour recevoir un lien de réinitialisation.
        </p>
        
        <form class="input-rectangles" method="POST" action="?controller=homepage&action=resetPassword">
            <label for="email"></label>
            <input type="email"
                   id="email"
                   name="email"
                   placeholder="Adresse email"
                   class="input-rectangle"
                   required>
            
            <button type="submit"
                    class="input-rectangle"
                    style="background:#1360AA;color:#fff;cursor:pointer;font-size:1.2em;">
                Réinitialiser le mot de passe
            </button>
        </form>
        
        <a href="?controller=homepage&action=login" class="text-link">Retour à la connexion</a>
    </div>
</div>

<script>
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

    window.addEventListener('DOMContentLoaded', applySavedTheme);
</script>
