<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
?>

<div class="content">
    <div class="profile-wrapper">
        <div class="nav-buttons default-nav-buttons">
            <a href="?controller=marketpage&action=index" class="nav-btn nav-btn-market" title="Marché">
                <img id="market-nav-icon" src="/public/assets/img/Market_Day.svg" alt="Marché" class="nav-icon">
            </a>
            <a href="?controller=tradeplace&action=index" class="nav-btn nav-btn-trade" title="Trading">
                <img id="trade-nav-icon" src="/public/assets/img/Trade_Day.svg" alt="Trading" class="nav-icon">
            </a>
            <a href="?controller=sitemap&action=index" class="nav-btn nav-btn-maps" title="Plan du site">
                <img id="maps-nav-icon" src="/public/assets/img/Maps.svg" alt="Plan du site" class="nav-icon">
            </a>
            <button id="scroll-to-top-btn" class="nav-btn scroll-to-top-btn" title="Remonter en haut">
                <img id="scroll-icon" src="/public/assets/img/Blue_Arrow.svg" alt="Remonter" class="nav-icon">
            </button>
        </div>

        <div class="login-rectangle">
        <div class="login-grid">
            <div class="login-left">
                <!-- Image de profil -->
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

                <!-- Informations du compte -->
                <h2 class="section-subtitle">Informations du compte</h2>

                <div class="profile-forms-container">
                    <div class="profile-info-item">
                        <strong>Nom d'utilisateur :</strong>
                        <span id="username-display"><?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        <button type="button" id="edit-username-btn" class="button profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                    </div>

                    <div class="profile-info-item">
                        <strong>Email :</strong>
                        <span id="email-display"><?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        <button type="button" id="edit-email-btn" class="button profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                    </div>

                    <div class="profile-info-item">
                        <strong>Mot de passe :</strong>
                        <span id="password-display">••••••••</span>
                        <button type="button" id="edit-password-btn" class="button profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                    </div>
                </div>

                <!-- Bouton de déconnexion -->
                <div class="profile-logout-container">
                    <form method="post" action="?controller=user&action=logout">
                        <button type="submit" class="button profile-logout-btn">
                            Se déconnecter
                        </button>
                    </form>
                </div>

                <!-- Bouton Admin (visible uniquement pour les admins) -->
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                    <div class="profile-logout-container">
                        <a href="?controller=admin&action=index" class="button profile-logout-btn" style="background-color: #e74c3c; text-decoration: none; display: block; text-align: center;">
                            Panel Administrateur
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Zone de danger -->
                <div class="profile-danger-zone">
                    <strong class="danger-title">⚠️ Zone de danger</strong>
                    <p class="danger-warning-text">La suppression de votre compte est définitive et irréversible. Toutes vos données seront effacées.</p>
                    <button type="button" id="delete-account-btn" class="button profile-delete-btn" <?php echo $disabledAttr; ?>>Je veux supprimer mon compte</button>
                </div>
            </div>

            <div class="login-right">
                <h1 class="rectangle-title">Moyennes du semestre</h1>

                <!-- Graphique des moyennes -->
                <div class="chart-container">
                    <div id="barchart_values"
                         data-maths="<?php echo htmlspecialchars($A_view['maths_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
                         data-programmation="<?php echo htmlspecialchars($A_view['programmation_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
                         data-network="<?php echo htmlspecialchars($A_view['network_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
                         data-db="<?php echo htmlspecialchars($A_view['DB_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"
                         data-other="<?php echo htmlspecialchars($A_view['other_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>

                <!-- Liste des moyennes en grille -->
                <div class="grades-grid">
                    <!-- Mathématiques -->
                    <div class="grade-item">
                        <strong>Mathématiques :</strong>
                        <span id="maths-display"><?php echo htmlspecialchars($A_view['maths_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    </div>

                    <!-- Programmation -->
                    <div class="grade-item">
                        <strong>Programmation :</strong>
                        <span id="prog-display"><?php echo htmlspecialchars($A_view['programmation_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    </div>

                    <!-- Réseaux -->
                    <div class="grade-item">
                        <strong>Réseaux :</strong>
                        <span id="reseaux-display"><?php echo htmlspecialchars($A_view['network_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    </div>

                    <!-- Base de données -->
                    <div class="grade-item">
                        <strong>Base de données :</strong>
                        <span id="bd-display"><?php echo htmlspecialchars($A_view['DB_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    </div>

                    <!-- Autre -->
                    <div class="grade-item">
                        <strong>Autre :</strong>
                        <span id="autre-display"><?php echo htmlspecialchars($A_view['other_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                    </div>
                </div>

                <div class="grades-edit-container">
                    <button type="button" id="edit-all-grades-btn" class="button grades-edit-all-btn" <?php echo $disabledAttr; ?>>Modifier mes moyennes</button>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<div id="delete-modal">
    <div class="modal-content">
        <h3>Confirmer la suppression</h3>
        <p>Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible et votre compte sera triste );</p>
        <form method="post" action="?controller=profilepage&action=deleteAccount">
            <button type="submit" class="button btn-delete">Oui, supprimer</button>
            <button type="button" id="cancel-delete-btn" class="button btn-cancel">Non, j'y tiens</button>
        </form>
    </div>
</div>

<div id="username-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier le nom d'utilisateur</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_username">Nouveau nom d'utilisateur :</label>
            <input type="text" id="modal_new_username" name="new_username" value="<?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" required <?php echo $disabledAttr; ?>>
            <div class="modal-buttons">
                <button type="submit" class="button btn-save">Enregistrer</button>
                <button type="button" class="button btn-cancel cancel-modal" data-modal="username-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<div id="email-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier l'email</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_email">Nouvel email :</label>
            <input type="email" id="modal_new_email" name="new_email" value="<?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" required <?php echo $disabledAttr; ?>>
            <div class="modal-buttons">
                <button type="submit" class="button btn-save">Enregistrer</button>
                <button type="button" class="button btn-cancel cancel-modal" data-modal="email-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<div id="password-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier le mot de passe</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_password">Nouveau mot de passe :</label>
            <input type="password" id="modal_new_password" name="new_password" placeholder="Nouveau mot de passe" class="input-rectangle" required <?php echo $disabledAttr; ?>>
            <div class="modal-buttons">
                <button type="submit" class="button btn-save">Enregistrer</button>
                <button type="button" class="button btn-cancel cancel-modal" data-modal="password-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<div id="all-grades-modal" class="profile-modal">
    <div class="modal-content modal-content-large">
        <h3>Modifier les moyennes</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <div class="grades-modal-grid">
                <div class="grade-modal-item">
                    <label for="modal_new_maths_points">Mathématiques (/20) :</label>
                    <input type="number" id="modal_new_maths_points" name="new_maths_points" value="<?php echo htmlspecialchars($A_view['maths_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                </div>

                <div class="grade-modal-item">
                    <label for="modal_new_programmation_points">Programmation (/20) :</label>
                    <input type="number" id="modal_new_programmation_points" name="new_programmation_points" value="<?php echo htmlspecialchars($A_view['programmation_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                </div>

                <div class="grade-modal-item">
                    <label for="modal_new_network_points">Réseaux (/20) :</label>
                    <input type="number" id="modal_new_network_points" name="new_network_points" value="<?php echo htmlspecialchars($A_view['network_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                </div>

                <div class="grade-modal-item">
                    <label for="modal_new_DB_points">Base de données (/20) :</label>
                    <input type="number" id="modal_new_DB_points" name="new_DB_points" value="<?php echo htmlspecialchars($A_view['DB_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                </div>

                <div class="grade-modal-item">
                    <label for="modal_new_other_points">Autre (/20) :</label>
                    <input type="number" id="modal_new_other_points" name="new_other_points" value="<?php echo htmlspecialchars($A_view['other_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
                </div>
            </div>

            <div class="modal-buttons">
                <button type="submit" class="button btn-save">Enregistrer toutes les moyennes</button>
                <button type="button" class="button btn-cancel cancel-modal" data-modal="all-grades-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

