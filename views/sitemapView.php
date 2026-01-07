<div class="content">
    <div class="sitemap-container">
        <h1 class="sitemap-title">Plan du site</h1>

        <div class="sitemap-description">
            <p>Bienvenue sur le plan du site DealTonBut. Vous trouverez ci-dessous toutes les pages accessibles.</p>
            <?php if (isset($A_view)) {
                if (!$A_view['isLoggedIn']): ?>
                    <p class="sitemap-notice">Certaines pages nécessitent une authentification préalable.</p>
                <?php endif;
            } ?>
        </div>

        <div class="sitemap-sections">

            <div class="sitemap-section">
                <h2 class="sitemap-section-title">Pages publiques</h2>

                <div class="sitemap-item">
                    <h3 class="sitemap-page-title">
                        <a href="?controller=homepage&action=login">Connexion / Index</a>
                    </h3>
                    <p class="sitemap-page-description">
                        Page d'accueil du site qui permet de se connecter et de comprendre quel est l'intérêt du site.
                    </p>
                </div>

                <div class="sitemap-item">
                    <h3 class="sitemap-page-title">
                        <a href="?controller=user&action=register">Création de compte</a>
                    </h3>
                    <p class="sitemap-page-description">
                        Page permettant de rejoindre l'aventure DealTonBut.
                    </p>
                </div>

                <div class="sitemap-item">
                    <h3 class="sitemap-page-title">
                        Mentions légales
                    </h3>
                    <p class="sitemap-page-description">
                        Page regroupant les mentions légales du site.
                    </p>
                </div>
            </div>

            <div class="sitemap-section">
                <h2 class="sitemap-section-title">
                    Pages nécessitant une authentification
                </h2>

                <?php if (isset($A_view['isLoggedIn'])): ?>

                    <div class="sitemap-item">
                        <h3 class="sitemap-page-title">
                            <a href="?controller=profilepage&action=index">Profilepage</a>
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant de voir les informations de votre compte.
                        </p>
                    </div>

                    <div class="sitemap-item">
                        <h3 class="sitemap-page-title">
                            <a href="?controller=marketpage&action=index">Market</a>
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant l'achat de service.
                        </p>
                    </div>

                    <div class="sitemap-item">
                        <h3 class="sitemap-page-title">
                            <a href="?controller=tradeplace&action=index">Trade</a>
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant l'échange de service.
                        </p>
                    </div>

                <?php else: ?>

                    <div class="sitemap-item sitemap-item-locked">
                        <h3 class="sitemap-page-title">
                            Profilepage 🔒
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant de voir les informations de votre compte.
                        </p>
                        <p class="sitemap-auth-message">Vous devez être connecté pour accéder à cette page.</p>
                    </div>

                    <div class="sitemap-item sitemap-item-locked">
                        <h3 class="sitemap-page-title">
                            Market 🔒
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant l'achat de service.
                        </p>
                        <p class="sitemap-auth-message">Vous devez être connecté pour accéder à cette page.</p>
                    </div>

                    <div class="sitemap-item sitemap-item-locked">
                        <h3 class="sitemap-page-title">
                            Trade 🔒
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant l'échange de service.
                        </p>
                        <p class="sitemap-auth-message">Vous devez être connecté pour accéder à cette page.</p>
                    </div>

                <?php endif; ?>
            </div>

        </div>

        <div class="sitemap-footer">
            <p>Pour toute question, n'hésitez pas à nous contacter.</p>
        </div>

    </div>
</div>

