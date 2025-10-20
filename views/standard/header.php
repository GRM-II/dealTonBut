<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header>
    <div class="header-left">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER] amoU" class="header-logo">
    </div>
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="header-nav">
        <a href="?controller=marketpage&action=index" class="nav-link">Marketplace</a>
        <a href="?controller=profilepage&action=index" class="nav-link">Profil</a>
        <a href="?controller=homepage&action=about" class="nav-link">À propos</a>
        <a href="?controller=homepage&action=help" class="nav-link">Aide</a>
    </nav>
    <?php endif; ?>
    
    <button id="theme-toggle" onclick="toggleTheme()" aria-label="Change theme"></button>
</header>