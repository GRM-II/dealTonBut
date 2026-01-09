<main>
    <div class="content">
        <div class="login-rectangle">
            <div class="login-grid">
                <div class="login-left">
                    <img src="/public/assets/img/placeholder-meme.jpeg" alt="Réinitialisation" class="log-img">
                    <div class="rectangle-title">Nouveau mot de passe</div>

                    <?php if (isset($A_view['error'])): ?>
                        <div class="login-error-message">
                            <?= htmlspecialchars($A_view['error']) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($A_view['success'])): ?>
                        <div class="login-success-message">
                            <?= htmlspecialchars($A_view['success']) ?>
                        </div>
                        <a href="?controller=user&action=login" class="text-link">Retour à la connexion</a>
                    <?php else: ?>
                        <form class="input-rectangles" method="POST" action="?controller=user&action=resetPassword&token=<?= htmlspecialchars($_GET['token'] ?? '') ?>">
                            <label class="login-text" for="password">Nouveau mot de passe</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   placeholder="Nouveau mot de passe"
                                   class="input-rectangle"
                                   minlength="6"
                                   required>

                            <label class="login-text" for="confirm_password">Confirmer le mot de passe</label>
                            <input type="password"
                                   id="confirm_password"
                                   name="confirm_password"
                                   placeholder="Confirmer le mot de passe"
                                   class="input-rectangle"
                                   minlength="6"
                                   required>

                            <button type="submit" class="button">
                                Réinitialiser
                            </button>
                        </form>
                        <a href="?controller=user&action=login" class="text-link">Retour à la connexion</a>
                    <?php endif; ?>
                </div>

                <div class="login-right">
                    <div class="login-right-placeholder">
                        <h1>Deal tom BIOUT</h1>
                        <h2>Réinitialisation sécurisée de votre mot de passe</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

