<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<div class="content">
    <div class="login-rectangle">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de connexion" class="log-img">
        <div class="rectangle-title">Connexion</div>

        <?php if (isset($A_view['success'])): ?>
            <div class="flash-message flash-success">
                <?php echo htmlspecialchars($A_view['success'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($A_view['error'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($A_view['error'], ENT_QUOTES, 'UTF-8'); ?>
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
                    class="input-rectangle btn-submit">
                Connexion
            </button>
        </form>

        <a href="#" class="text-link">Mot de passe oublié ?</a>
        <a href="?controller=user&action=register" class="text-link">Vous ne possédez pas de compte ?</a>
    </div>
</div>