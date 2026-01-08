<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<main>
    <div class="content">
        <div class="login-rectangle">
            <div class="rectangle-title">Mentions légales</div>

            <?php if (isset($A_view['flash'])): ?>
                <div class="flash-message <?php echo $A_view['flash']['success'] ? 'flash-success' : 'flash-error'; ?>">
                    <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <div class="input-rectangles">
                <section>
                    <h2>1. Éditeur du site</h2>
                    <p>
                        <strong>Nom du site :</strong> Deal Ton BUT<br>
                        <strong>URL :</strong> dealtonbut.feyli.dev<br>
                        <strong>Responsable de publication :</strong> Kiko<br>
                        <strong>Contact :</strong> kiko.gamingxd@gmail.com
                    </p>
                </section>

                <section>
                    <h2>2. Hébergement</h2>
                    <p>
                        <strong>Hébergeur :</strong> Mr. Feyli<br>
                        <strong>Adresse :</strong> Pays des Furrys<br>
                        <strong>Téléphone :</strong> 3630
                    </p>
                </section>

                <section>
                    <h2>3. Propriété intellectuelle</h2>
                    <p>
                        L'ensemble du contenu de ce site (textes, images, logos, vidéos) est la propriété exclusive de amoU et de ses contributeurs (GRM, Kiko, Akcyl, mimojeej, Louloute, Olivier G.), sauf mention contraire.
                    </p>
                    <p>
                        Toute reproduction, distribution, modification ou utilisation des contenus du site sans autorisation préalable est strictement interdite et constitue une violation des droits d'auteur.
                    </p>
                </section>

                <section>
                    <h2>4. Protection des données personnelles</h2>
                    <p>
                        Conformément au Règlement Général sur la Protection des Données (RGPD) et à la loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification et de suppression des données vous concernant.
                    </p>
                    <p>
                        Les données collectées sur ce site sont destinées uniquement à la gestion des comptes utilisateurs et ne sont en aucun cas transmises à des tiers.
                    </p>
                    <p>
                        Pour exercer vos droits, contactez-nous à : [votre-email@exemple.com]
                    </p>
                </section>

                <section>
                    <h2>5. Cookies</h2>
                    <p>
                        Ce site utilise des cookies de session nécessaires à son bon fonctionnement. Aucun cookie de tracking ou publicitaire n'est utilisé.
                    </p>
                </section>

                <section>
                    <h2>6. Responsabilité</h2>
                    <p>
                        L'éditeur s'efforce d'assurer l'exactitude et la mise à jour des informations diffusées sur ce site, mais ne peut garantir l'exactitude, la précision ou l'exhaustivité des informations mises à disposition.
                    </p>
                    <p>
                        L'éditeur ne saurait être tenu responsable des dommages directs ou indirects résultant de l'utilisation du site.
                    </p>
                </section>

                <section>
                    <h2>7. Crédits</h2>
                    <p>
                        <strong>Équipe :</strong> Thomas.A - Akcyl.B - Dimitri.C - Lou.D - Olivier.G - Francisco Packet Tracer. S<br>
                        <strong>Slogan :</strong> Là où la comédie commence, les points suivent.
                    </p>
                </section>

                <section>
                    <h2>8. Loi applicable</h2>
                    <p>
                        Les présentes mentions légales sont régies par la loi française. En cas de litige, les tribunaux français seront seuls compétents.
                    </p>
                </section>

                <p>
                    <em>Dernière mise à jour : <?php echo date('d/m/Y'); ?></em>
                </p>
            </div>
            <div style="text-align: center; width: 100%; margin-top: 2vh;">
                <button onclick="window.location.href='?controller=homepage&action=index'" class="input-rectangle login-submit-btn" type="button" style="width: auto; padding: 0 35px;">
                    Retour à l'accueil
                </button>
            </div>
        </div>
    </div>
</main>