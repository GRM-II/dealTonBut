<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>
<div class="content">
    <div class="login-rectangle" style="position:relative;">
        <div style="position:absolute;top:10px;left:10px;z-index:10;">
            <a href="?controller=marketpage&action=index" class="input-rectangle" style="display:inline-block;background:#1360AA;color:#fff;text-decoration:none;padding:8px 16px;border-radius:4px;">
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

        <!-- Section Informations du compte -->
        <div style="margin-top:20px;text-align:center;">
            <div style="font-size:1.1em;font-weight:bold;color:#1360AA;margin-bottom:15px;border-bottom:2px solid #1360AA;padding-bottom:5px;">
                📋 Informations du compte
            </div>

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

        <!-- Section Moyennes par matière -->
        <div style="margin-top:40px;text-align:center;">
            <div style="font-size:1.1em;font-weight:bold;color:#1360AA;margin-bottom:15px;border-bottom:2px solid #1360AA;padding-bottom:5px;">
                📊 Mes moyennes du semestre
            </div>

            <!-- Mathématiques -->
            <form id="edit-maths-form" method="post" action="?controller=profilepage&action=updateProfile" style="margin-bottom:15px;">
                <div style="display:inline-block;text-align:center;min-width:400px;">
                    <strong>Mathématiques :</strong>
                    <span id="maths-display"><?php echo htmlspecialchars($A_view['points_maths'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    <div id="maths-edit-container" style="display:none;">
                        <input type="number" id="new_points_maths" name="new_points_maths" value="<?php echo htmlspecialchars($A_view['points_maths'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" style="width:120px;" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                        <span style="font-size:0.85em;color:#666;display:block;margin-top:5px;">Entrez ici votre moyenne de ce semestre en mathématiques</span>
                    </div>
                    <button type="button" id="edit-maths-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Modifier</button>
                    <button type="submit" id="save-maths-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;margin-left:10px;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                    <button type="button" id="cancel-maths-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Annuler</button>
                </div>
            </form>

            <!-- Programmation -->
            <form id="edit-prog-form" method="post" action="?controller=profilepage&action=updateProfile" style="margin-bottom:15px;">
                <div style="display:inline-block;text-align:center;min-width:400px;">
                    <strong>Programmation :</strong>
                    <span id="prog-display"><?php echo htmlspecialchars($A_view['points_programmation'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    <div id="prog-edit-container" style="display:none;">
                        <input type="number" id="new_points_programmation" name="new_points_programmation" value="<?php echo htmlspecialchars($A_view['points_programmation'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" style="width:120px;" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                        <span style="font-size:0.85em;color:#666;display:block;margin-top:5px;">Entrez ici votre moyenne de ce semestre en programmation</span>
                    </div>
                    <button type="button" id="edit-prog-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Modifier</button>
                    <button type="submit" id="save-prog-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;margin-left:10px;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                    <button type="button" id="cancel-prog-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Annuler</button>
                </div>
            </form>

            <!-- Réseaux -->
            <form id="edit-reseaux-form" method="post" action="?controller=profilepage&action=updateProfile" style="margin-bottom:15px;">
                <div style="display:inline-block;text-align:center;min-width:400px;">
                    <strong>Réseaux :</strong>
                    <span id="reseaux-display"><?php echo htmlspecialchars($A_view['points_reseaux'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    <div id="reseaux-edit-container" style="display:none;">
                        <input type="number" id="new_points_reseaux" name="new_points_reseaux" value="<?php echo htmlspecialchars($A_view['points_reseaux'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" style="width:120px;" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                        <span style="font-size:0.85em;color:#666;display:block;margin-top:5px;">Entrez ici votre moyenne de ce semestre en réseaux</span>
                    </div>
                    <button type="button" id="edit-reseaux-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Modifier</button>
                    <button type="submit" id="save-reseaux-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;margin-left:10px;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                    <button type="button" id="cancel-reseaux-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Annuler</button>
                </div>
            </form>

            <!-- Base de données -->
            <form id="edit-bd-form" method="post" action="?controller=profilepage&action=updateProfile" style="margin-bottom:15px;">
                <div style="display:inline-block;text-align:center;min-width:400px;">
                    <strong>Base de données :</strong>
                    <span id="bd-display"><?php echo htmlspecialchars($A_view['points_BD'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    <div id="bd-edit-container" style="display:none;">
                        <input type="number" id="new_points_BD" name="new_points_BD" value="<?php echo htmlspecialchars($A_view['points_BD'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" style="width:120px;" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                        <span style="font-size:0.85em;color:#666;display:block;margin-top:5px;">Entrez ici votre moyenne de ce semestre en base de données</span>
                    </div>
                    <button type="button" id="edit-bd-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Modifier</button>
                    <button type="submit" id="save-bd-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;margin-left:10px;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                    <button type="button" id="cancel-bd-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Annuler</button>
                </div>
            </form>

            <!-- Autre -->
            <form id="edit-autre-form" method="post" action="?controller=profilepage&action=updateProfile" style="margin-bottom:15px;">
                <div style="display:inline-block;text-align:center;min-width:400px;">
                    <strong>Autre :</strong>
                    <span id="autre-display"><?php echo htmlspecialchars($A_view['points_autre'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    <div id="autre-edit-container" style="display:none;">
                        <input type="number" id="new_points_autre" name="new_points_autre" value="<?php echo htmlspecialchars($A_view['points_autre'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" style="width:120px;" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                        <span style="font-size:0.85em;color:#666;display:block;margin-top:5px;">Entrez ici votre moyenne de ce semestre pour les autres matières</span>
                    </div>
                    <button type="button" id="edit-autre-btn" class="input-rectangle" style="padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Modifier</button>
                    <button type="submit" id="save-autre-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;background:#1360AA;color:#fff;margin-left:10px;" <?php echo $disabledAttr; ?>>Enregistrer</button>
                    <button type="button" id="cancel-autre-btn" class="input-rectangle" style="display:none;padding:2px 8px;font-size:0.95em;margin-left:10px;" <?php echo $disabledAttr; ?>>Annuler</button>
                </div>
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

<script>
    // JavaScript pour gérer l'édition des champs (username, email, password)
    document.getElementById('edit-username-btn').addEventListener('click', function() {
        document.getElementById('username-display').style.display = 'none';
        document.getElementById('new_username').style.display = 'inline';
        document.getElementById('edit-username-btn').style.display = 'none';
        document.getElementById('save-username-btn').style.display = 'inline';
        document.getElementById('cancel-username-btn').style.display = 'inline';
    });

    document.getElementById('cancel-username-btn').addEventListener('click', function() {
        document.getElementById('username-display').style.display = 'inline';
        document.getElementById('new_username').style.display = 'none';
        document.getElementById('edit-username-btn').style.display = 'inline';
        document.getElementById('save-username-btn').style.display = 'none';
        document.getElementById('cancel-username-btn').style.display = 'none';
    });

    document.getElementById('edit-email-btn').addEventListener('click', function() {
        document.getElementById('email-display').style.display = 'none';
        document.getElementById('new_email').style.display = 'inline';
        document.getElementById('edit-email-btn').style.display = 'none';
        document.getElementById('save-email-btn').style.display = 'inline';
        document.getElementById('cancel-email-btn').style.display = 'inline';
    });

    document.getElementById('cancel-email-btn').addEventListener('click', function() {
        document.getElementById('email-display').style.display = 'inline';
        document.getElementById('new_email').style.display = 'none';
        document.getElementById('edit-email-btn').style.display = 'inline';
        document.getElementById('save-email-btn').style.display = 'none';
        document.getElementById('cancel-email-btn').style.display = 'none';
    });

    document.getElementById('edit-password-btn').addEventListener('click', function() {
        document.getElementById('password-display').style.display = 'none';
        document.getElementById('new_password').style.display = 'inline';
        document.getElementById('edit-password-btn').style.display = 'none';
        document.getElementById('save-password-btn').style.display = 'inline';
        document.getElementById('cancel-password-btn').style.display = 'inline';
    });

    document.getElementById('cancel-password-btn').addEventListener('click', function() {
        document.getElementById('password-display').style.display = 'inline';
        document.getElementById('new_password').style.display = 'none';
        document.getElementById('edit-password-btn').style.display = 'inline';
        document.getElementById('save-password-btn').style.display = 'none';
        document.getElementById('cancel-password-btn').style.display = 'none';
    });

    // JavaScript pour gérer l'édition des moyennes
    const subjects = ['maths', 'prog', 'reseaux', 'bd', 'autre'];

    subjects.forEach(function(subject) {
        document.getElementById('edit-' + subject + '-btn').addEventListener('click', function() {
            document.getElementById(subject + '-display').style.display = 'none';
            document.getElementById(subject + '-edit-container').style.display = 'block';
            document.getElementById('edit-' + subject + '-btn').style.display = 'none';
            document.getElementById('save-' + subject + '-btn').style.display = 'inline';
            document.getElementById('cancel-' + subject + '-btn').style.display = 'inline';
        });

        document.getElementById('cancel-' + subject + '-btn').addEventListener('click', function() {
            document.getElementById(subject + '-display').style.display = 'inline';
            document.getElementById(subject + '-edit-container').style.display = 'none';
            document.getElementById('edit-' + subject + '-btn').style.display = 'inline';
            document.getElementById('save-' + subject + '-btn').style.display = 'none';
            document.getElementById('cancel-' + subject + '-btn').style.display = 'none';
        });
    });

    // Modal de suppression
    document.getElementById('delete-account-btn').addEventListener('click', function() {
        document.getElementById('delete-modal').style.display = 'flex';
    });

    document.getElementById('cancel-delete-btn').addEventListener('click', function() {
        document.getElementById('delete-modal').style.display = 'none';
    });
</script>