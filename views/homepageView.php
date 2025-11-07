<main>
    <div class="content">
        <div class="project-description-rectangle">
            <img src="/public/assets/img/placeholder-meme.jpeg" alt="Logo du projet" class="project-logo">
            <div class="project-text">
                <h2 class="project-title">Nom du Projet</h2>
                <button id="scroll-to-description" class="btn-learn-more">En savoir plus</button>
            </div>
        </div>

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
            <a href="index.php?controller=user&action=register" class="text-link">Vous ne possédez pas de compte ?</a>
        </div>

        <div id="project-description-section" class="project-description-detailed">
            <h2 class="project-description-title">À propos du projet</h2>
            <p class="project-description-text">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            </p>
            <p class="project-description-text">
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam,
                eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
            </p>
            <p class="project-description-text">
                Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui
                ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.
            </p>
        </div>
    </div>
</main>

<script>
    document.getElementById('scroll-to-description').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('project-description-section').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });
</script>

