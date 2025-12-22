<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' — ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>
<main>
    <div class="content">
        <div class="login-rectangle">
            <div class="login-grid">
                <div class="login-left">
                    <div class="rectangle-title">Créer un compte</div>

                    <?php if ($dbUnavailable): ?>
                        <div class="db-unavailable-message register-db-warning">
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

                    <form class="input-rectangles" id="register-form" method="post" action="?controller=user&action=register">
                        <label class="login-text" for="username">Nom d'utilisateur</label>
                        <input type="text" id="username" name="username" placeholder="Nom d'utilisateur" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">

                        <label class="login-text" for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Email" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">

                        <label class="login-text" for="password">Mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="Mot de passe" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">

                        <label class="login-text" for="confirm-password">Confirmer le mot de passe</label>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirmer" class="input-rectangle" required <?php echo $disabledAttr; ?> title="<?php echo $dbUnavailable ? htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8') : ''; ?>">

                        <button type="submit" class="input-rectangle register-submit-btn" <?php echo $disabledAttr; ?>>Créer le compte</button>
                    </form>

                    <a href="?controller=user&action=login" class="text-link">Vous possédez déjà un compte ?</a>
                </div>

                <div class="login-right">
                    <div class="login-right-placeholder">
                        <h1>Deal tom BIOUT</h1>
                        <h2>Là où la comédie commence, les points suivent.</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        initRegisterForm(
                <?php echo $dbUnavailable ? 'true' : 'false'; ?>,
                <?php echo json_encode($dbMessage, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT); ?>
        );
    });
</script>