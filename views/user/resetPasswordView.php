<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<div class="content">
    <div class="login-rectangle">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="Réinitialisation" class="log-img">
        <div class="rectangle-title">Nouveau mot de passe</div>

        <?php if (isset($A_view['error'])): ?>
            <div style="margin:10px 0;padding:10px;border-radius:6px;background:#ffe6e6;color:#9b1c1c;border:1px solid #f5a4a4;">
                <?php echo htmlspecialchars($A_view['error'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($A_view['success'])): ?>
            <div style="margin:10px 0;padding:10px;border-radius:6px;background:#e6ffed;color:#03543f;border:1px solid #84e1bc;">
                <?php echo htmlspecialchars($A_view['success'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <a href="?controller=user&action=login" class="text-link" style="display:block;text-align:center;margin-top:15px;">Retour à la connexion</a>
        <?php else: ?>
            <form class="input-rectangles" method="POST" action="?controller=user&action=resetPassword&token=<?php echo htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <label for="password"></label>
                <input type="password"
                       id="password"
                       name="password"
                       placeholder="Nouveau mot de passe"
                       class="input-rectangle"
                       minlength="6"
                       required>

                <label for="confirm_password"></label>
                <input type="password"
                       id="confirm_password"
                       name="confirm_password"
                       placeholder="Confirmer le mot de passe"
                       class="input-rectangle"
                       minlength="6"
                       required>

                <button type="submit"
                        class="input-rectangle"
                        style="background:#1360AA;color:#fff;cursor:pointer;font-size:1.2em;">
                    Réinitialiser
                </button>
            </form>
            <a href="?controller=user&action=login" class="text-link">Retour à la connexion</a>
        <?php endif; ?>
    </div>
</div>

