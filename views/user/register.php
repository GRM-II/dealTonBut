<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' — ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>
<div class="content">
    <div class="login-rectangle">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de connexion" class="log-img">
        <div class="rectangle-title">Créer un compte</div>

        <?php if ($dbUnavailable): ?>
            <div style="margin:10px 0;padding:10px;border-radius:6px;background:#fff4e5;color:#92400e;border:1px solid #f6ad55;">
                <?php echo htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8'); ?>
                <div style="margin-top:8px;font-size:0.95em;">
                    Pour corriger ce problème, activez l'une des extensions MySQL de PHP sur votre serveur :
                    <ul style="margin:6px 0 0 18px;">
                        <li>Sur Windows (XAMPP/WAMP) : ouvrez php.ini et décommentez extension=pdo_mysql et/ou extension=mysqli puis redémarrez Apache.</li>
                        <li>Sur Linux : installez php-mysql (ou php8.x-mysql) et redémarrez votre serveur web.</li>
                    </ul>
                    Consultez la page de diagnostics pour voir l'état exact de votre environnement :
                    <a href="?controller=homepage&action=diagnostics">Diagnostics MySQL</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($A_view['flash'])): ?>
            <div style="margin:10px 0;padding:10px;border-radius:6px;<?php echo $A_view['flash']['success'] ? 'background:#e6ffed;color:#03543f;border:1px solid #84e1bc;' : 'background:#ffe6e6;color:#9b1c1c;border:1px solid #f5a4a4;'; ?>">
                <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form class="input-rectangles" id="register-form" method="post" action="?controller=user&action=register">
            <label for="username"></label>
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <label for="email"></label>
            <input type="email" id="email" name="email" placeholder="Email" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <label for="password"></label>
            <input type="password" id="password" name="password" placeholder="Mot de passe" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <label for="confirm-password"></label>
            <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirmer" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <button type="submit" class="input-rectangle" style="background:#1360AA;color:#fff;cursor:pointer;font-size:1.2em;" <?php echo $disabledAttr; ?>>Créer le compte</button>
        </form>
        <a href="index.php?controller=user&action=defaultAction" class="text-link">Vous possédez déjà un compte ?</a>
    </div>
</div>

<script>
    function setThemeIcon() {
        const btn = document.getElementById('theme-toggle');
        if (btn) {
            if (document.body.classList.contains('dark-theme')) {
                btn.innerHTML = '🌙';
            } else {
                btn.innerHTML = '☀️';
            }
        }
    }
    function applySavedTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        } else {
            document.body.classList.remove('dark-theme');
        }
        setThemeIcon();
    }
    function toggleTheme() {
        document.body.classList.toggle('dark-theme');
        localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
        setThemeIcon();
    }
    window.addEventListener('DOMContentLoaded', function() {
        applySavedTheme();
        var form = document.getElementById('register-form');
        if (<?php echo $dbUnavailable ? 'true' : 'false'; ?>) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                alert(<?php echo json_encode($dbMessage, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>);
            });
            return;
        }
        form.addEventListener('submit', function(e) {
            const pwd = document.getElementById('password').value;
            const confirm = document.getElementById('confirm-password').value;
            if (pwd !== confirm) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
            }
        });
    });
</script>