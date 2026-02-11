<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
$isLoggedIn = $A_view['isLoggedIn'] ?? false;
$offers = $A_view['offers'] ?? [];
$selectedOffer = $A_view['selectedOffer'] ?? null;
?>

// Le pas de 0.05 pour le slider des points ne marche pas, je sais pas pourquoi

<div class="content">
        <div class="nav-buttons trade-nav-buttons">
            <a href="?controller=marketpage&action=index" class="nav-btn nav-btn-market" title="Marché">
                <img id="market-nav-icon" src="/public/assets/img/Market_Day.svg" alt="Marché" class="nav-icon">
            </a>
            <a href="?controller=profilepage&action=index" class="nav-btn nav-btn-profile" title="Accueil">
                <img id="home-nav-icon" src="/public/assets/img/Home_Day.svg" alt="Accueil" class="nav-icon">
            </a>
            <a href="?controller=sitemap&action=index" class="nav-btn nav-btn-maps" title="Plan du site">
                <img id="maps-nav-icon" src="/public/assets/img/Maps.svg" alt="Plan du site" class="nav-icon">
            </a>
            <button id="scroll-to-top-btn" class="nav-btn scroll-to-top-btn" title="Remonter en haut">
                <img id="scroll-icon" src="/public/assets/img/Blue_Arrow.svg" alt="Remonter" class="nav-icon">
            </button>
        </div>
    <div class="tradeplace-title-container">
        <h1 class="tradeplace-title">Trade place</h1>
    </div>

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

    <div class="trade-place-container">
        <aside class="trade-place-sidebar">
            <?php if (empty($offers)): ?>
                <div class="empty-offers">
                    <p>Aucune offre disponible</p>
                </div>
            <?php else: ?>
                <?php foreach ($offers as $offer): ?>
                    <a href="?controller=tradeplace&action=index&offer_id=<?php echo htmlspecialchars($offer['id'], ENT_QUOTES, 'UTF-8'); ?>"
                       class="trade-offer-item <?php echo ($selectedOffer && $selectedOffer['id'] == $offer['id']) ? 'active' : ''; ?>">
                        <span class="offer-category">Offre de <?php echo htmlspecialchars($offer['username'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($offer['category'] ?? 'Autre', ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="offer-arrow"></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>

        <main class="trade-place-main">
            <?php if ($selectedOffer): ?>
                <div class="trade-window">
                    <div class="trade-header">
                        <h2>Échange avec <?php echo htmlspecialchars($selectedOffer['username'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?></h2>
                    </div>

                    <div class="trade-content">

                        <div class="trade-section your-offerings">
                            <h3>Votre offre :</h3>
                            <p class="offerings-subtitle">Vos conditions de réalisation.</p>
                            <div class="offerings-grid" id="your-offerings-grid">
                                <div class="offering-slot" data-slot="0"></div>
                                <div class="offering-slot" data-slot="1"></div>
                                <div class="offering-slot" data-slot="2"></div>
                                <div class="offering-slot" data-slot="3"></div>
                                <div class="offering-slot" data-slot="4"></div>
                                <div class="offering-slot" data-slot="5"></div>
                                <div class="offering-slot" data-slot="6"></div>
                                <div class="offering-slot" data-slot="7"></div>
                            </div>
                            <div class="trade-actions">
                                <button type="button" class="btn-add-points" id="btn-add-points">Ajouter points</button>
                                <button type="button" class="btn-add-custom" id="btn-add-custom">Ajouter une faveur</button>
                            </div>
                        </div>

                        <div class="trade-separator">
                            <div class="separator-line"></div>
                            <div class="separator-icon">⇄</div>
                            <div class="separator-line"></div>
                        </div>

                        <div class="trade-section their-offerings">
                            <h3>Offre de <?php echo htmlspecialchars($selectedOffer['username'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?> :</h3>
                            <p class="offerings-subtitle">Ce que vous êtes prêt à céder, que ce soit des points ou autre.</p>
                            <div class="offerings-grid" id="their-offerings-grid">
                                <div class="offering-slot" data-slot="0" data-initial-offer="<?php echo htmlspecialchars($selectedOffer['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                <div class="offering-slot" data-slot="1"></div>
                                <div class="offering-slot" data-slot="2"></div>
                                <div class="offering-slot" data-slot="3"></div>
                                <div class="offering-slot" data-slot="4"></div>
                                <div class="offering-slot" data-slot="5"></div>
                                <div class="offering-slot" data-slot="6"></div>
                                <div class="offering-slot" data-slot="7"></div>
                            </div>
                            <div class="trade-actions">
                                <button type="button" class="btn-add-points" id="btn-add-points-their">Ajouter points</button>
                                <button type="button" class="btn-add-custom" id="btn-add-custom-their">Ajouter une faveur</button>
                            </div>
                        </div>
                    </div>

                    <div class="trade-footer">
                        <button type="button" class="btn-confirm-trade" id="btn-confirm-trade">Confirmer l'échange</button>
                    </div>

                </div>
            <?php else: ?>
                <div class="no-offer-selected">
                    <p>Sélectionnez une offre pour voir les détails</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<div id="points-modal">
    <div class="modal-content">
        <p>Sélectionnez le montant de points à ajouter :</p>
        <div class="points-slider-container">
            <label for="points-slider" style="display: none;">Montant de points</label>
            <input type="range" id="points-slider" min="0.1" max="1" value="0.5" step="0.05" class="slider">
            <div class="slider-value-display">
                <span id="slider-value-text">0.5</span> point<span id="plural-s">s</span>
            </div>
        </div>
        <div class="modal-buttons">
            <button type="button" id="confirm-points-btn" class="button btn-confirm">Ajouter</button>
            <button type="button" id="cancel-points-btn" class="button btn-cancel">Annuler</button>
        </div>
    </div>
</div>

<div id="custom-message-modal">
    <div class="modal-content">
        <p>Quel objet ou tâche êtes vous prêt à échanger ?</p>
        <form id="custom-message-form">
            <label for="custom-message-input" style="display: none;">Custom message</label>
            <input id="custom-message-input" class="input-rectangle" placeholder="Aucun remboursement possible.">
            <div class="modal-buttons">
                <button type="submit" class="button btn-confirm">Add</button>
                <button type="button" id="cancel-custom-message-btn" class="button btn-cancel">Cancel</button>
            </div>
        </form>
    </div>
</div>

