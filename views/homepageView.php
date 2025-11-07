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

            <a href="#" class="text-link" id="forgot-password-link">Mot de passe oublié ?</a>
            <a href="index.php?controller=user&action=register" class="text-link">Vous ne possédez pas de compte ?</a>
        </div>
    </div>

    <!-- Modal pour mot de passe oublié -->
    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Réinitialiser le mot de passe</h2>
            <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
            <form id="forgot-password-form" method="POST" action="?controller=user&action=forgotPassword">
                <input type="email"
                       name="email"
                       placeholder="Votre adresse email"
                       class="input-rectangle"
                       required>
                <button type="submit"
                        class="input-rectangle"
                        style="background:#1360AA;color:#fff;cursor:pointer;font-size:1.1em;margin-top:10px;">
                    Envoyer
                </button>
            </form>
        </div>
    </div>
</main>