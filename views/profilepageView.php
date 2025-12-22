<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>
<div class="content">
    <div class="profile-wrapper">
        <div class="nav-buttons profile-nav-buttons">
            <a href="?controller=marketpage&action=index" class="nav-btn nav-btn-market" title="Marché">
                <img id="market-nav-icon" src="/public/assets/img/Market_Day.svg" alt="Marché" class="nav-icon">
            </a>
            <a href="?controller=tradeplace&action=index" class="nav-btn nav-btn-trade" title="Trading">
                <img id="trade-nav-icon" src="/public/assets/img/Trade_Day.svg" alt="Trading" class="nav-icon">
            </a>
            <button id="scroll-to-top-btn" class="nav-btn scroll-to-top-btn" title="Remonter en haut">
                <img src="/public/assets/img/placeholder-meme.jpeg" alt="Remonter" class="nav-icon">
            </button>
        </div>

        <div class="login-rectangle">
        <div class="login-grid">
            <div class="login-left">

                <!-- L'image doit être l'image de profil de l'utilisateur, placeholder pour l'instant -->
                <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de profil" class="log-img">

                <div class="rectangle-title">Profil utilisateur</div>

                <?php if ($dbUnavailable): ?>
                    <div class="db-unavailable-message">
                        <?php echo htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($A_view['flash'])): ?>
                    <div class="flash-message <?php echo $A_view['flash']['success'] ? 'flash-success' : 'flash-error'; ?>">
                        <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <div class="profile-forms-container">
                    <form id="edit-username-form" method="post" action="?controller=profilepage&action=updateProfile" class="profile-edit-form">
                        <strong>Nom d'utilisateur :</strong>
                        <span id="username-display"><?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        <label for="new_username"></label><input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle profile-edit-input" required <?php echo $disabledAttr; ?>>
                        <button type="button" id="edit-username-btn" class="input-rectangle profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        <button type="submit" id="save-username-btn" class="input-rectangle profile-save-btn" <?php echo $disabledAttr; ?>>Enregistrer</button>
                        <button type="button" id="cancel-username-btn" class="input-rectangle profile-cancel-btn" <?php echo $disabledAttr; ?>>Annuler</button>
                    </form>
                    <br><br>
                    <form id="edit-email-form" method="post" action="?controller=profilepage&action=updateProfile" class="profile-edit-form">
                        <strong>Email :</strong>
                        <span id="email-display"><?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        <label for="new_email"></label><input type="email" id="new_email" name="new_email" value="<?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle profile-edit-input" required <?php echo $disabledAttr; ?>>
                        <button type="button" id="edit-email-btn" class="input-rectangle profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        <button type="submit" id="save-email-btn" class="input-rectangle profile-save-btn" <?php echo $disabledAttr; ?>>Enregistrer</button>
                        <button type="button" id="cancel-email-btn" class="input-rectangle profile-cancel-btn" <?php echo $disabledAttr; ?>>Annuler</button>
                    </form>
                    <br><br>
                    <form id="edit-password-form" method="post" action="?controller=profilepage&action=updateProfile" class="profile-edit-form">
                        <strong>Mot de passe :</strong>
                        <span id="password-display">••••••••</span>
                        <label for="new_password"></label><input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe" class="input-rectangle profile-edit-input" required <?php echo $disabledAttr; ?>>
                        <button type="button" id="edit-password-btn" class="input-rectangle profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        <button type="submit" id="save-password-btn" class="input-rectangle profile-save-btn" <?php echo $disabledAttr; ?>>Enregistrer</button>
                        <button type="button" id="cancel-password-btn" class="input-rectangle profile-cancel-btn" <?php echo $disabledAttr; ?>>Annuler</button>
                    </form>
                </div>

                <div class="profile-logout-container">
                    <form method="post" action="?controller=user&action=logout">
                        <button type="submit" class="input-rectangle profile-logout-btn">
                            Se déconnecter
                        </button>
                    </form>
                </div>

                <div class="profile-danger-zone">
                    <strong class="danger-title">Oh là jeune ménestrel</strong><br>
                    <p class="danger-warning-text">La suppression de votre compte est définitive et irréversible.</p>
                    <button type="button" id="delete-account-btn" class="input-rectangle profile-delete-btn" <?php echo $disabledAttr; ?>>Supprimer mon compte</button>
                </div>
            </div>
            <div class="login-right">
                <div class="login-right-top">
                    <h3>Note de dev : espace pour savoir son solde de points</h3>
                </div>
                <div class="login-right-separator"></div>
                <div class="login-right-bottom">
                    <h3>Note de dev : espace pour l'historique des transactions ou autre</h3>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="delete-modal">
    <div class="modal-content">
        <h3>Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible et votre compte sera triste );</p>
        <form method="post" action="?controller=profilepage&action=deleteAccount">
            <button type="submit" class="input-rectangle btn-delete">Oui, supprimer</button>
            <button type="button" id="cancel-delete-btn" class="input-rectangle btn-cancel">Non, j'y tiens</button>
        </form>
    </div>
</div>

