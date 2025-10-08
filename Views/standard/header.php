<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<!DOCTYPE html>
<html lang="fr">
    <header>
        <div class="header-left">
            <img src="/public/assets/img/placeholder-meme.jpeg" alt="[PLACEHOLDER] Amou" class="header-logo">
        </div>
        <button id="theme-toggle" onclick="toggleTheme()" aria-label="Change theme"></button>
    </header>
</html>