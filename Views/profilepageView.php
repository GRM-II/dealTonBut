<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' — ' . $A_view['db_status']['details'] : '')) : '';
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
            <form id="edit-username-form" method="post" action="?controller=profile&action=updateUsername" style="display:inline;">
                <strong>Nom d'utilisateur :</strong>
                <span id="username-display"><?php echo htmlspecialchars($A_view['user']['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <input type="text" id="username-input" name="username" value="<?php echo htmlspecialchars($A_view['user']['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="display:none;width:140px;" class="input-rectangle" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-username-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-username-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-username-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
            <br>
            <form id="edit-email-form" method="post" action="?controller=profile&action=updateEmail" style="display:inline;">
                <strong>Email :</strong>
                <span id="email-display"><?php echo htmlspecialchars($A_view['user']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                <input type="email" id="email-input" name="email" value="<?php echo htmlspecialchars($A_view['user']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="display:none;width:180px;" class="input-rectangle" required <?php echo $disabledAttr; ?>>
                <button type="button" id="edit-email-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Modifier</button>
                <button type="submit" id="save-email-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                <button type="button" id="cancel-email-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;" <?php echo $disabledAttr; ?>>Annuler</button>
            </form>
        </div>

        <form method="post" action="?controller=profile&action=updateBio" style="margin-top:20px;">
            <label for="bio"><strong>Bio :</strong></label><br>
            <textarea id="bio" name="bio" rows="4" cols="40" class="input-rectangle" placeholder="Présentez-vous..." <?php echo $disabledAttr; ?>><?php echo htmlspecialchars($A_view['user']['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea><br>
            <button type="submit" class="input-rectangle" style="background:#1360AA;color:#fff;cursor:pointer;" <?php echo $disabledAttr; ?>>Enregistrer la bio</button>
        </form>
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
    window.addEventListener('DOMContentLoaded', function() {
        applySavedTheme();

        //edit username
        const editUsernameBtn = document.getElementById('edit-username-btn');
        const saveUsernameBtn = document.getElementById('save-username-btn');
        const cancelUsernameBtn = document.getElementById('cancel-username-btn');
        const usernameDisplay = document.getElementById('username-display');
        const usernameInput = document.getElementById('username-input');
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
        //edit email
        const editEmailBtn = document.getElementById('edit-email-btn');
        const saveEmailBtn = document.getElementById('save-email-btn');
        const cancelEmailBtn = document.getElementById('cancel-email-btn');
        const emailDisplay = document.getElementById('email-display');
        const emailInput = document.getElementById('email-input');
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
    });
</script>
