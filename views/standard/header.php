<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<header>
    <div class="header-left">
        <a href="?controller=homepage&action=index">
            <img src="/public/assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER] amoU" class="header-logo">
        </a>
    </div>
    <button id="theme-toggle" onclick="toggleTheme()" aria-label="Change theme"></button>
</header>