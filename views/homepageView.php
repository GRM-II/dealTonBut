<main>
    <div class="content">
        <div class="login-rectangle">
            <div class="login-grid">
                <div class="login-left">
                    <img src="/public/assets/img/placeholder-meme.jpeg" alt="Logo Deal Ton BUT" class="log-img">
                    <h1 class="title">Connexion</h1>

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
                        <label for="username">Identifiant</label>
                        <input type="text"
                               id="username"
                               placeholder="Pseudo ou email"
                               name="login"
                               class="input-rectangle"
                               required>

                        <label for="password">Mot de passe</label>
                        <input type="password"
                               id="password"
                               placeholder="********"
                               name="password"
                               class="input-rectangle"
                               required>

                        <button type="submit"
                                name="submit"
                                class="button">
                            Connexion
                        </button>
                    </form>

                    <a href="#" class="text-link" id="forgot-password-link">Mot de passe oublié ?</a>
                    <a href="?controller=user&action=register" class="text-link">Vous ne possédez pas de compte ?</a>
                </div>

                <div class="login-right">
                    <h1>Bienvenue sur Deal ton BUT !</h1>
                    <div class="homepage-meme-container" id="homepage-meme-container">
                        <p>Chargement...</p>
                    </div>

                    <?php
                    $directory = 'public/assets/homepageMemes';
                    $mediaExtensions = ['webp', 'webm'];
                    $mediaFiles = [];

                    if (is_dir($directory)) {
                        $allFiles = scandir($directory);
                        foreach ($allFiles as $file) {
                            if ($file === '.' || $file === '..') continue;
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (in_array($ext, $mediaExtensions)) {
                                $mediaFiles[] = [
                                    'filename' => $file,
                                    'path' => '/' . $directory . '/' . $file,
                                    'type' => in_array($ext, ['webp', 'webm']) ? 'video' : 'image'
                                ];
                            }
                        }
                    }
                    ?>

                    <script>
                        window.homepageMemes = <?= json_encode($mediaFiles) ?>;
                    </script>
                </div>
            </div>
        </div>
    </div>

    <div id="forgot-password-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Réinitialiser le mot de passe</h2>
            <p>C'est quand même balot, alors donnez nous votre adresse email pour recevoir un lien de réinitialisation.</p>
            <p>L'envoi peut prendre jusqu'à 3 minutes.</p>
            <form id="forgot-password-form" method="POST" action="?controller=user&action=forgotPassword">
                <label>
                    <input type="email"
                           name="email"
                           placeholder="Votre adresse email"
                           class="input-rectangle"
                           required>
                </label>
                <button type="submit"
                        class="button"
                        id="submit-forgot-password">
                    Envoyer
                </button>
                <div id="loading-indicator" class="loading-indicator">
                    <div class="loading-spinner"></div>
                    <p class="loading-text">Envoi en cours, veuillez patienter...</p>
                </div>
            </form>
        </div>
    </div>
</main>

