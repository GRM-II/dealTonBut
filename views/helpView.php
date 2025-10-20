<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<div class="content">
    <div class="login-rectangle about-container">
        <div class="rectangle-title">Aide & FAQ</div>
        
        <div class="about-content">
            <h2>Questions fréquentes</h2>
            
            <div class="faq-item">
                <h3>Comment créer un compte ?</h3>
                <p>
                    Cliquez sur "Vous ne possédez pas de compte ?" sur la page de connexion, 
                    remplissez le formulaire d'inscription avec votre nom d'utilisateur, 
                    votre email et un mot de passe sécurisé.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Comment publier une offre ?</h3>
                <p>
                    Une fois connecté, rendez-vous sur le Marketplace et cliquez sur le bouton "+" 
                    en bas à droite de l'écran. Remplissez le formulaire avec le titre, 
                    la description, le prix en points et la catégorie de votre offre.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Comment fonctionne le système de points ?</h3>
                <p>
                    Les points sont la monnaie d'échange sur DealTonBut. Vous gagnez des points 
                    en vendant vos services ou biens, et vous pouvez les dépenser pour acheter 
                    les offres d'autres utilisateurs.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>J'ai oublié mon mot de passe, que faire ?</h3>
                <p>
                    Cliquez sur "Mot de passe oublié ?" sur la page de connexion. 
                    Entrez votre adresse email et suivez les instructions pour réinitialiser 
                    votre mot de passe.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Comment modifier mes informations de profil ?</h3>
                <p>
                    Accédez à votre profil via le menu de navigation. Vous pourrez modifier 
                    votre nom d'utilisateur, votre email et votre mot de passe en cliquant 
                    sur les boutons "Modifier" correspondants.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Comment supprimer une offre ?</h3>
                <p>
                    Sur le Marketplace, vous verrez un bouton "Supprimer" sur vos propres offres. 
                    Cliquez dessus et confirmez la suppression. Attention, cette action est irréversible.
                </p>
            </div>
            
            <h3>Besoin d'aide supplémentaire ?</h3>
            <p>
                Si vous ne trouvez pas de réponse à votre question, n'hésitez pas à nous contacter 
                via notre page Instagram (lien dans le pied de page).
            </p>
            
            <div class="about-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="?controller=marketpage&action=index" class="input-rectangle btn-primary">
                        Retour au Marketplace
                    </a>
                <?php else: ?>
                    <a href="?controller=homepage&action=login" class="input-rectangle btn-primary">
                        Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
