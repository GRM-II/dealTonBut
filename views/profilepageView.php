<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>
<div class="content">
    <div class="login-rectangle" style="position:relative;">
        <div style="position:absolute;top:10px;left:10px;z-index:10;">
            <a href="?controller=marketpage" class="input-rectangle" style="display:inline-block;background:#1360AA;color:#fff;text-decoration:none;padding:8px 16px;border-radius:4px;">
                🛒 Marketplace
            </a>
        </div>

        <img src="/public/assets/img/placeholder-meme.jpeg" alt="Image de profil" class="log-img">
        <div class="rectangle-title">Profil utilisateur</div>

        <?php if ($dbUnavailable): ?>
            <div style="margin:10px 0;padding:10px;border-radius:6px;background:#fff4e5;color:#92400e;border:1px solid #f6ad55;">
                <?php echo htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($A_view['flash'])): ?>
            <div style="margin:10px 0;padding:10px;border-radius:6px;<?php echo $A_view['flash']['success'] ? 'background:#e6ffed;color:#03543f;border:1px solid #84e1bc;' : 'background:#ffe6e6;color:#9b1c1c;border:1px solid #f5a4a4;'; ?>">
                <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div style="margin-top:20px;text-align:center;">
            <form id="edit-username-form" method="post" action="?controller=profilepage&action=updateProfile" style="display:inline;">
                <strong>Nom d'utilisateur :</strong>
                <span id="username-display"><?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="display:none;width:140px;" class="input-rectangle" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-username-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-username-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-username-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
            <br><br>
            <form id="edit-email-form" method="post" action="?controller=profilepage&action=updateProfile" style="display:inline;">
                <strong>Email :</strong>
                <span id="email-display"><?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <input type="email" id="new_email" name="new_email" value="<?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="display:none;width:180px;" class="input-rectangle" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-email-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-email-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-email-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
            <br><br>
            <form id="edit-password-form" method="post" action="?controller=profilepage&action=updateProfile" style="display:inline;">
                <strong>Mot de passe :</strong>
                <span id="password-display">••••••••</span>
                <input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe" style="display:none;width:180px;" class="input-rectangle" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-password-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-password-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-password-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
        </div>


        <div style="margin-top:30px;text-align:center;">
            <form method="post" action="?controller=user&action=logout">
                <button type="submit" class="input-rectangle" style="background:#1360AA;color:#fff;cursor:pointer;padding:10px 20px;">
                    Se déconnecter
                </button>
            </form>
        </div>

        <div style="margin-top:30px;padding-top:20px;border-top:1px solid #ddd;text-align:center;">
            <strong class="danger-title">Oh là jeune ménestrel</strong><br>
            <p class="danger-warning-text">La suppression de votre compte est définitive et irréversible.</p>
            <button type="button" id="delete-account-btn" class="input-rectangle" style="background:#dc2626;color:#fff;cursor:pointer;margin-top:10px;" <?php echo $disabledAttr; ?>>Supprimer mon compte</button>
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

