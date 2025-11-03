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
            <div class="flash-message flash-warning">
                <?php echo htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8'); ?>
                <div class="warning-details">
                    Pour corriger ce problème, activez l'une des extensions MySQL de PHP sur votre serveur :
                    <ul>
                        <li>Sur Windows (XAMPP/WAMP) : ouvrez php.ini et décommentez extension=pdo_mysql et/ou extension=mysqli puis redémarrez Apache.</li>
                        <li>Sur Linux : installez php-mysql (ou php8.x-mysql) et redémarrez votre serveur web.</li>
                    </ul>
                    Consultez la page de diagnostics pour voir l'état exact de votre environnement :
                    <a href="?controller=homepage&action=diagnostics">Diagnostics MySQL</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($A_view['flash'])): ?>
            <div class="flash-message <?php echo $A_view['flash']['success'] ? 'flash-success' : 'flash-error'; ?>">
                <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form class="input-rectangles" id="register-form" method="post" action="?controller=user&action=register"
              data-db-unavailable="<?php echo $dbUnavailable ? 'true' : 'false'; ?>"
              data-db-message="<?php echo htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8'); ?>">
            <label for="username"></label>
            <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <label for="email"></label>
            <input type="email" id="email" name="email" placeholder="Email" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <label for="password"></label>
            <input type="password" id="password" name="password" placeholder="Mot de passe" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <label for="confirm-password"></label>
            <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirmer" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <button type="submit" class="input-rectangle btn-submit" <?php echo $disabledAttr; ?>>Créer le compte</button>
        </form>
        <a href="index.php?controller=user&action=defaultAction" class="text-link">Vous possédez déjà un compte ?</a>
    </div>
</div>
