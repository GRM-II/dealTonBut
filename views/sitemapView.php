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
                <h2 class="sitemap-section-title">Liste des pages</h2>

                <a href="?controller=homepage&action=login" class="sitemap-item sitemap-item-link">
                    <h3 class="sitemap-page-title">
                        Connexion / Index
                    </h3>
                    <p class="sitemap-page-description">
                        Page d'accueil du site qui permet de se connecter et de comprendre quel est l'intérêt du site.
                    </p>
                </a>

                <a href="?controller=user&action=register" class="sitemap-item sitemap-item-link">
                    <h3 class="sitemap-page-title">
                        Création de compte
                    </h3>
                    <p class="sitemap-page-description">
                        Page permettant de rejoindre l'aventure DealTonBut.
                    </p>
                </a>

                <a href="?controller=legalnotice&action=index" class="sitemap-item sitemap-item-link">
                    <h3 class="sitemap-page-title">
                        Mentions légales
                    </h3>
                    <p class="sitemap-page-description">
                        Page regroupant les mentions légales du site.
                    </p>
                </a>

                <?php if (isset($A_view['isLoggedIn']) && $A_view['isLoggedIn']): ?>
                    <a href="?controller=profilepage&action=index" class="sitemap-item sitemap-item-link">
                        <h3 class="sitemap-page-title">
                            Profilepage
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant de voir les informations de votre compte.
                        </p>
                    </a>

                    <a href="?controller=marketpage&action=index" class="sitemap-item sitemap-item-link">
                        <h3 class="sitemap-page-title">
                            Market
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant l'achat de service.
                        </p>
                    </a>

                    <a href="?controller=tradeplace&action=index" class="sitemap-item sitemap-item-link">
                        <h3 class="sitemap-page-title">
                            Trade
                        </h3>
                        <p class="sitemap-page-description">
                            Page permettant l'échange de service.
                        </p>
                    </a>
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
    </div>
</div>