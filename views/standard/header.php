<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header>
    <div class="header-left">
        <span class="nav-icon" onclick="toggleNavMenu()" title="Afficher le menu de navigation">
            <img src="/public/assets/img/HamburgerMenu.svg" alt="Menu" class="nav-icon">
        </span>
    </div>
    <button id="theme-toggle" onclick="toggleTheme()" aria-label="Change theme"></button>
</header>