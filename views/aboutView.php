<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<div class="content">
    <div class="login-rectangle about-container">
        <div class="rectangle-title">À propos de DealTonBut</div>
        
        <div class="about-content">
            <h2>Bienvenue sur DealTonBut</h2>
            <p>
                DealTonBut est une plateforme d'échange et de marketplace conçue pour faciliter 
                les transactions entre étudiants. Notre mission est de créer un environnement 
                sécurisé et convivial où chacun peut acheter et vendre des services ou des biens.
            </p>
            
            <h3>Notre système de points</h3>
            <p>
                Les transactions sur DealTonBut se font via un système de points. 
                Chaque utilisateur peut gagner des points en vendant des services ou des biens, 
                et les dépenser pour acquérir ce dont il a besoin.
            </p>
            
            <h3>Catégories disponibles</h3>
            <ul class="about-list">
                <li>Services</li>
                <li>Maths</li>
                <li>Informatique</li>
                <li>Électronique</li>
                <li>Mode</li>
                <li>Maison</li>
                <li>Sports</li>
                <li>Alimentation</li>
            </ul>
            
            <h3>L'équipe</h3>
            <p>
                Développé par : GRM - Kiko - Akcyl - mimojeej - Louloute - Olivier G.
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
