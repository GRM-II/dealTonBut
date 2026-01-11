<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$isLoggedIn = $A_view['isLoggedIn'] ?? false;
$offers = $A_view['offers'] ?? [];
$selectedOffer = $A_view['selectedOffer'] ?? null;
?>

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
                        <span class="offer-category"><?php echo htmlspecialchars(substr($offer['category'] ?? 'Autre', 0, 20), ENT_QUOTES, 'UTF-8'); ?></span>
                        <span class="offer-arrow"></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>

        <main class="trade-place-main">
            <?php if ($selectedOffer): ?>
                <div class="offer-detail-card">
                    <h2 class="offer-title"><?php echo htmlspecialchars($selectedOffer['title'] ?? 'Offre pour le G', ENT_QUOTES, 'UTF-8'); ?></h2>

                    <div class="offer-description-box">
                        <p class="offer-description">
                            <?php echo nl2br(htmlspecialchars($selectedOffer['description'] ?? 'Aucune description disponible.', ENT_QUOTES, 'UTF-8')); ?>
                        </p>
                    </div>

                    <div class="offer-meta">
                        <?php if (isset($selectedOffer['username'])): ?>
                            <div class="offer-meta-item">
                                <span class="meta-label">Proposé par :</span>
                                <span class="meta-value"><?php echo htmlspecialchars($selectedOffer['username'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($selectedOffer['created_at'])): ?>
                            <div class="offer-meta-item">
                                <span class="meta-label">Publié le :</span>
                                <span class="meta-value"><?php echo htmlspecialchars(date('d/m/Y', strtotime($selectedOffer['created_at'])), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="offer-details-grid">
                        <?php if (isset($selectedOffer['price']) && $selectedOffer['price'] > 0): ?>
                            <div class="detail-box">
                                <span class="detail-label">Prix :</span>
                                <span class="detail-value"><?php echo htmlspecialchars($selectedOffer['price'], ENT_QUOTES, 'UTF-8'); ?> points</span>
                            </div>
                        <?php endif; ?>

                        <div class="detail-box">
                            <span class="detail-label">Catégorie</span>
                            <span class="detail-value"><?php echo htmlspecialchars($selectedOffer['category'] ?? 'Autre', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                    <div class="offer-purchase">
                        <button type="button" id="purchase-offer-btn" class="button offer-purchase-btn">Effectuer la transaction</button>
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
</div>

<!-- Modal de confirmation de transaction -->
<div id="purchase-modal">
    <div class="modal-content">
        <h3>Confirmer la transaction</h3>
        <p>Êtes-vous sûr de vouloir conclure la transaction ?</p>
        <form method="post" action="?controller=tradeplace&action=purchaseOffer">
            <button type="submit" class="button btn-purchase">Oui</button>
            <button type="button" id="cancel-purchase-btn" class="button btn-cancel">Non</button>
        </form>
    </div>
</div>


