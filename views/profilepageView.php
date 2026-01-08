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
                    <div class="profile-info-item">
                        <strong>Nom d'utilisateur :</strong>
                        <span id="username-display"><?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        <button type="button" id="edit-username-btn" class="input-rectangle profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                    </div>
                    <br><br>
                    <div class="profile-info-item">
                        <strong>Email :</strong>
                        <span id="email-display"><?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        <button type="button" id="edit-email-btn" class="input-rectangle profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                    </div>
                    <br><br>
                    <div class="profile-info-item">
                        <strong>Mot de passe :</strong>
                        <span id="password-display">••••••••</span>
                        <button type="button" id="edit-password-btn" class="input-rectangle profile-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                    </div>
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
                    <!-- Section Moyennes par matière -->
                    <div class="grades-section">
                        <h1 class="rectangle-title"><u>Moyennes du semestre par matière</u></h1>

                        <!-- Mathématiques -->
                        <div class="grade-item">
                            <strong>Mathématiques :</strong>
                            <span id="maths-display"><?php echo htmlspecialchars($A_view['maths_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                            <button type="button" id="edit-maths-btn" class="input-rectangle grade-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        </div>

                        <!-- Programmation -->
                        <div class="grade-item">
                            <strong>Programmation :</strong>
                            <span id="prog-display"><?php echo htmlspecialchars($A_view['programmation_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                            <button type="button" id="edit-prog-btn" class="input-rectangle grade-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        </div>

                        <!-- Réseaux -->
                        <div class="grade-item">
                            <strong>Réseaux :</strong>
                            <span id="reseaux-display"><?php echo htmlspecialchars($A_view['network_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                            <button type="button" id="edit-reseaux-btn" class="input-rectangle grade-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        </div>

                        <!-- Base de données -->
                        <div class="grade-item">
                            <strong>Base de données :</strong>
                            <span id="bd-display"><?php echo htmlspecialchars($A_view['DB_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                            <button type="button" id="edit-bd-btn" class="input-rectangle grade-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        </div>

                        <!-- Autre -->
                        <div class="grade-item">
                            <strong>Autre :</strong>
                            <span id="autre-display"><?php echo htmlspecialchars($A_view['other_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/20</span>
                            <button type="button" id="edit-autre-btn" class="input-rectangle grade-edit-btn" <?php echo $disabledAttr; ?>>Modifier</button>
                        </div>
                    </div>
                    <?php
                    $dataPoints = array(
	                array("label"=> "Education", "y"=> 284935),
	                array("label"=> "Entertainment", "y"=> 256548),
	                array("label"=> "Lifestyle", "y"=> 245214),
	                array("label"=> "Business", "y"=> 233464),
	                array("label"=> "Music & Audio", "y"=> 200285),
	                array("label"=> "Personalization", "y"=> 194422),
	                array("label"=> "Tools", "y"=> 180337),
	                array("label"=> "Books & Reference", "y"=> 172340),
	                array("label"=> "Travel & Local", "y"=> 118187),
	                array("label"=> "Puzzle", "y"=> 107530)
                    );?>
                    <script>
                        window.onload = function () {

                            var chart = new CanvasJS.Chart("chartContainer", {
                                animationEnabled: true,
                                theme: "light2", // "light1", "light2", "dark1", "dark2"
                                title: {
                                    text: "Top 10 Google Play Categories - till 2017"
                                },
                                axisY: {
                                    title: "Number of Apps"
                                },
                                data: [{
                                    type: "column",
                                    dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
                                }]
                            });
                            chart.render();
                        }
                    </script>
                    <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
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

<!-- Modal modification nom d'utilisateur -->
<div id="username-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier le nom d'utilisateur</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_username">Nouveau nom d'utilisateur :</label>
            <input type="text" id="modal_new_username" name="new_username" value="<?php echo htmlspecialchars($A_view['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" required <?php echo $disabledAttr; ?>>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="username-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal modification email -->
<div id="email-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier l'email</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_email">Nouvel email :</label>
            <input type="email" id="modal_new_email" name="new_email" value="<?php echo htmlspecialchars($A_view['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" required <?php echo $disabledAttr; ?>>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="email-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal modification mot de passe -->
<div id="password-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier le mot de passe</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_password">Nouveau mot de passe :</label>
            <input type="password" id="modal_new_password" name="new_password" placeholder="Nouveau mot de passe" class="input-rectangle" required <?php echo $disabledAttr; ?>>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="password-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal modification Mathématiques -->
<div id="maths-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier la moyenne en Mathématiques</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_maths_points">Moyenne (/20) :</label>
            <input type="number" id="modal_new_maths_points" name="new_maths_points" value="<?php echo htmlspecialchars($A_view['maths_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
            <span class="grade-help-text">Entrez ici votre moyenne de ce semestre en mathématiques</span>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="maths-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal modification Programmation -->
<div id="prog-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier la moyenne en Programmation</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_programmation_points">Moyenne (/20) :</label>
            <input type="number" id="modal_new_programmation_points" name="new_programmation_points" value="<?php echo htmlspecialchars($A_view['programmation_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
            <span class="grade-help-text">Entrez ici votre moyenne de ce semestre en programmation</span>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="prog-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal modification Réseaux -->
<div id="reseaux-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier la moyenne en Réseaux</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_network_points">Moyenne (/20) :</label>
            <input type="number" id="modal_new_network_points" name="new_network_points" value="<?php echo htmlspecialchars($A_view['network_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
            <span class="grade-help-text">Entrez ici votre moyenne de ce semestre en réseaux</span>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="reseaux-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal modification Base de données -->
<div id="bd-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier la moyenne en Base de données</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_DB_points">Moyenne (/20) :</label>
            <input type="number" id="modal_new_DB_points" name="new_DB_points" value="<?php echo htmlspecialchars($A_view['DB_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
            <span class="grade-help-text">Entrez ici votre moyenne de ce semestre en base de données</span>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="bd-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal modification Autre -->
<div id="autre-modal" class="profile-modal">
    <div class="modal-content">
        <h3>Modifier la moyenne - Autre</h3>
        <form method="post" action="?controller=profilepage&action=updateProfile">
            <label for="modal_new_other_points">Moyenne (/20) :</label>
            <input type="number" id="modal_new_other_points" name="new_other_points" value="<?php echo htmlspecialchars($A_view['other_points'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>" class="input-rectangle" min="0" max="20" step="0.01" required <?php echo $disabledAttr; ?>>
            <span class="grade-help-text">Entrez ici votre moyenne de ce semestre pour les autres matières</span>
            <div class="modal-buttons">
                <button type="submit" class="input-rectangle btn-save">Enregistrer</button>
                <button type="button" class="input-rectangle btn-cancel cancel-modal" data-modal="autre-modal">Annuler</button>
            </div>
        </form>
    </div>
</div>

