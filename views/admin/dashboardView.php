<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<div id="nav-menu" class="overlay">
    <div class="overlay-content">
        <span id="scroll-to-top-btn" class="button nav scroll-to-top-btn" title="Remonter en haut">
            <img id="scroll-icon" src="/public/assets/img/Arrow.svg" alt="Remonter">
        </span>
        <a href="?controller=profilepage&action=index" class="button nav nav-btn-profile" title="Profil">
            <img src="/public/assets/img/Profile.svg" alt="Profil">
        </a>
        <a href="?controller=marketpage&action=index" class="button nav nav-btn-market" title="Marché">
            <img src="/public/assets/img/Market.svg" alt="Marché">
        </a>
        <a href="?controller=tradeplace&action=index" class="button nav nav-btn-trade" title="Deals">
            <img src="/public/assets/img/Trade.svg" alt="Trading">
        </a>
        <a href="?controller=sitemap&action=index" class="button nav nav-btn-maps" title="Plan du site">
            <img src="/public/assets/img/Maps.svg" alt="Plan du site">
        </a>
        <a href="?controller=user&action=logout" class="button nav nav-btn-logout" title="Se déconnecter">
            <img src="/public/assets/img/Disconnect.svg" alt="Déconnexion">
        </a>
    </div>
</div>

<div class="content">
    <div class="profile-wrapper">
        <div class="login-rectangle">
            <h1>Dashboard Admin</h1>
            <p>Connecté en tant que: <?= htmlspecialchars($_SESSION['user']['username']) ?></p>

            <div class="admin-nav-buttons">
                <a href="?controller=admin&action=users" class="button admin-nav-btn">
                    Gérer les utilisateurs
                </a>
                <a href="?controller=admin&action=offers" class="button admin-nav-btn">
                    Gérer les offres
                </a>
            </div>

            <hr>

            <h2>Statistiques</h2>
            <div class="admin-stats">
                <div class="stat-item">
                    <strong>Utilisateurs :</strong>
                    <span><?= $A_view['stats']['total_users'] ?></span>
                </div>
                <div class="stat-item">
                    <strong>Offres :</strong>
                    <span><?= $A_view['stats']['total_offers'] ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
