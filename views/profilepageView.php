<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>
<div class="content">
    <div class="login-rectangle">
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

        <div style="margin-top:20px;">
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

        <div style="margin-top:30px;padding-top:20px;border-top:1px solid #ddd;">
            <strong style="color:#9b1c1c;">Zone de danger</strong><br>
            <p style="font-size:0.9em;color:#666;">La suppression de votre compte est définitive et irréversible.</p>
            <button type="button" id="delete-account-btn" class="input-rectangle" style="background:#dc2626;color:#fff;cursor:pointer;margin-top:10px;" <?php echo $disabledAttr; ?>>Supprimer mon compte</button>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="delete-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1000;justify-content:center;align-items:center;">
    <div style="background:white;padding:30px;border-radius:8px;max-width:400px;text-align:center;">
        <h3 style="color:#9b1c1c;margin-top:0;">Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.</p>
        <form method="post" action="?controller=profilepage&action=deleteAccount" style="display:inline;">
            <button type="submit" class="input-rectangle" style="background:#dc2626;color:#fff;margin-right:10px;">Oui, supprimer</button>
            <button type="button" id="cancel-delete-btn" class="input-rectangle">Annuler</button>
        </form>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function() {
        // Edit username
        const editUsernameBtn = document.getElementById('edit-username-btn');
        const saveUsernameBtn = document.getElementById('save-username-btn');
        const cancelUsernameBtn = document.getElementById('cancel-username-btn');
        const usernameDisplay = document.getElementById('username-display');
        const usernameInput = document.getElementById('new_username');

        editUsernameBtn.addEventListener('click', function() {
            usernameDisplay.style.display = 'none';
            usernameInput.style.display = '';
            saveUsernameBtn.style.display = '';
            cancelUsernameBtn.style.display = '';
            editUsernameBtn.style.display = 'none';
            usernameInput.focus();
        });

        cancelUsernameBtn.addEventListener('click', function() {
            usernameDisplay.style.display = '';
            usernameInput.style.display = 'none';
            saveUsernameBtn.style.display = 'none';
            cancelUsernameBtn.style.display = 'none';
            editUsernameBtn.style.display = '';
            usernameInput.value = usernameDisplay.textContent;
        });

        // Edit email
        const editEmailBtn = document.getElementById('edit-email-btn');
        const saveEmailBtn = document.getElementById('save-email-btn');
        const cancelEmailBtn = document.getElementById('cancel-email-btn');
        const emailDisplay = document.getElementById('email-display');
        const emailInput = document.getElementById('new_email');

        editEmailBtn.addEventListener('click', function() {
            emailDisplay.style.display = 'none';
            emailInput.style.display = '';
            saveEmailBtn.style.display = '';
            cancelEmailBtn.style.display = '';
            editEmailBtn.style.display = 'none';
            emailInput.focus();
        });

        cancelEmailBtn.addEventListener('click', function() {
            emailDisplay.style.display = '';
            emailInput.style.display = 'none';
            saveEmailBtn.style.display = 'none';
            cancelEmailBtn.style.display = 'none';
            editEmailBtn.style.display = '';
            emailInput.value = emailDisplay.textContent;
        });

        // Edit password
        const editPasswordBtn = document.getElementById('edit-password-btn');
        const savePasswordBtn = document.getElementById('save-password-btn');
        const cancelPasswordBtn = document.getElementById('cancel-password-btn');
        const passwordDisplay = document.getElementById('password-display');
        const passwordInput = document.getElementById('new_password');

        editPasswordBtn.addEventListener('click', function() {
            passwordDisplay.style.display = 'none';
            passwordInput.style.display = '';
            savePasswordBtn.style.display = '';
            cancelPasswordBtn.style.display = '';
            editPasswordBtn.style.display = 'none';
            passwordInput.focus();
        });

        cancelPasswordBtn.addEventListener('click', function() {
            passwordDisplay.style.display = '';
            passwordInput.style.display = 'none';
            savePasswordBtn.style.display = 'none';
            cancelPasswordBtn.style.display = 'none';
            editPasswordBtn.style.display = '';
            passwordInput.value = '';
        });

        // Delete account modal
        const deleteAccountBtn = document.getElementById('delete-account-btn');
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');

        deleteAccountBtn.addEventListener('click', function() {
            deleteModal.style.display = 'flex';
        });

        cancelDeleteBtn.addEventListener('click', function() {
            deleteModal.style.display = 'none';
        });

        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.style.display = 'none';
            }
        });
    });
</script>
