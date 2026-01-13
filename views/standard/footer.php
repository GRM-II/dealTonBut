<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<footer>
    <div class="footer">
        <img src="public/assets/img/amU.png" alt="[PLACEHOLDER] amoU" class="footer-logo">
        <button onclick="window.open('https://www.instagram.com/iutaixmars', '_blank')" class="footer-instagram-btn">
            📷 Instagram
        </button>
        <p class="footer-text">⠀|⠀</p>
        <p class="footer-text">Thomas.A - Akcyl.B - Dimitri.C - Lou.D - Olivier.G - Francisco. S</p>
        <p class="footer-text">⠀|⠀</p>
        <button onclick="window.location.href='?controller=legalnotice&action=index'" class="footer-legal-btn">Mentions légales</button>
    </div>
</footer>