<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>
<div class="content">
    <a href="?controller=marketpage&action=index" id="marketplace-btn" class="marketplace-link-btn" style="background: #ff00ff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 8px; display: inline-block; margin-bottom: 20px; font-weight: bold; cursor: pointer;">
        Accéder à la Marketplace
    </a>
    <div class="login-rectangle">
        <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de profil" class="log-img">
        <div class="rectangle-title">Profil utilisateur</div>

        <?php if ($dbUnavailable): ?>
            <div class="flash-message flash-warning">
                <?php echo htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($A_view['flash'])): ?>
            <div class="flash-message <?php echo $A_view['flash']['success'] ? 'flash-success' : 'flash-error'; ?>">
                <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="profile-form">
            <form id="edit-username-form" method="post" action="?controller=profilepage&action=updateProfile">
                <strong>Nom d'utilisateur :</strong>
                <span id="username-display"><?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle not-displayed" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-username-btn" class="input-rectangle" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-username-btn" class="blue input-rectangle not-displayed" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-username-btn" class="input-rectangle not-displayed" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
            <br><br>
            <form id="edit-email-form" method="post" action="?controller=profilepage&action=updateProfile">
                <strong>Email :</strong>
                <span id="email-display"><?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <input type="email" id="new_email" name="new_email" value="<?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle not-displayed" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-email-btn" class="input-rectangle" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-email-btn" class="blue input-rectangle not-displayed" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-email-btn" class="input-rectangle not-displayed" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
            <br><br>
            <form id="edit-password-form" method="post" action="?controller=profilepage&action=updateProfile">
                <strong>Mot de passe :</strong>
                <span id="password-display">••••••••</span>
                <input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe" class="input-rectangle not-displayed" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-password-btn" class="input-rectangle" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-password-btn" class="blue input-rectangle not-displayed" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-password-btn" class="input-rectangle not-displayed" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
        </div>


        <div class="profile-form">
            <form method="post" action="?controller=user&action=logout">
                <button type="submit" class="blue input-rectangle">
                    Se déconnecter
                </button>
            </form>
        </div>

        <div class="danger-zone">
            <strong>Zone de danger</strong><br>
            <p>La suppression de votre compte est définitive et irréversible.</p>
            <button type="button" id="delete-account-btn" class="red input-rectangle" <?php echo $disabledAttr; ?>>Supprimer mon compte</button>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="delete-modal" class="delete-modal">
    <div class="delete-rectangle">
        <h3>Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>
        <form class="profile-form" method="post" action="?controller=profilepage&action=deleteAccount">
            <button type="submit" class="red input-rectangle">Oui, supprimer</button>
            <button type="button" id="cancel-delete-btn" class="input-rectangle">Annuler</button>
        </form>
    </div>
</div>

