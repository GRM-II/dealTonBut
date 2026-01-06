<main>
    <div class="content">
        <div class="login-rectangle">
            <div class="login-grid">
                <div class="login-left">
                    <img src="/public/assets/img/placeholder-meme.jpeg" alt="Logo Deal Ton BUT" class="log-img">
                    <div class="rectangle-title">Connexion</div>

                    <?php if (isset($error)): ?>
                        <div class="login-error-message">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="<?= $_SESSION['flash_message']['success'] ? 'login-success-message' : 'login-error-message' ?>">
                            <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
                        </div>
                        <?php unset($_SESSION['flash_message']); ?>
                    <?php endif; ?>

                    <form class="input-rectangles" method="POST" action="?controller=user&action=login">
                        <label class="login-text" for="username">Identifiant</label>
                        <input type="text"
                               id="username"
                               placeholder="Nom d'utilisateur ou adresse mail"
                               name="login"
                               class="input-rectangle"
                               required>

                        <label class="login-text" for="password">Mot de passe</label>
                        <input type="password"
                               id="password"
                               placeholder="********"
                               name="password"
                               class="input-rectangle"
                               required>

                        <button type="submit"
                                name="submit"
                                class="input-rectangle">
                            Connexion
                        </button>
                    </form>

                    <a href="#" class="text-link" id="forgot-password-link">Mot de passe oublié ?</a>
                    <a href="/index.php?controller=user&action=register" class="text-link">Vous ne possédez pas de compte ?</a>
                </div>

                <div class="login-right">
                    <div class="login-right-placeholder">
                        <h1>Deal tom BIOUT</h1>
                        <h2>Commence ton nouveau chapitre de comédien !</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour mot de passe oublié -->
    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Réinitialiser le mot de passe</h2>
            <p>C'est quand même balot, alors donnez nous votre adresse email pour recevoir un lien de réinitialisation.</p>
            <form id="forgot-password-form" method="POST" action="?controller=user&action=forgotPassword">
                <label>
                    <input type="email"
                           name="email"
                           placeholder="Votre adresse email"
                           class="input-rectangle"
                           required>
                </label>
                <button type="submit"
                        class="input-rectangle">
                    Envoyer
                </button>
            </form>
        </div>
    </div>
</main>