<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
$isLoggedIn = $A_view['isLoggedIn'] ?? false;

// Organiser les offres par catégorie
$offersByCategory = [];
$categories = ['Maths', 'Programmation', 'Réseau', 'BD', 'Autre'];
foreach ($categories as $cat) {
    $offersByCategory[$cat] = [];
}

if (!empty($A_view['offers'])) {
    foreach ($A_view['offers'] as $offer) {
        $category = $offer['category'] ?? 'Autre';
        if (!isset($offersByCategory[$category])) {
            $offersByCategory[$category] = [];
        }
        $offersByCategory[$category][] = $offer;
    }
}
?>

<div class="content">
    <!-- Boutons de navigation -->
    <div class="nav-buttons market-nav-buttons">
        <a href="?controller=tradeplace&action=index" class="nav-btn nav-btn-trade" title="Trade Place">
            <img id="trade-nav-icon" src="/public/assets/img/Trade_Day.svg" alt="Trade Place" class="nav-icon">
        </a>
        <a href="?controller=profilepage&action=index" class="nav-btn nav-btn-profile" title="Profil">
            <img id="home-nav-icon" src="/public/assets/img/Home_Day.svg" alt="Profil" class="nav-icon">
        </a>
        <a href="?controller=sitemap&action=index" class="nav-btn nav-btn-maps" title="Plan du site">
            <img id="maps-nav-icon" src="/public/assets/img/Maps.svg" alt="Plan du site" class="nav-icon">
        </a>
        <button id="scroll-to-top-btn" class="nav-btn scroll-to-top-btn" title="Remonter en haut">
            <img id="scroll-icon" src="/public/assets/img/Blue_Arrow.svg" alt="Remonter" class="nav-icon">
        </button>
    </div>

    <div class="marketplace-wrapper">
        <div class="marketplace-title-container">
            <h1 class="marketplace-title">Market place</h1>
        </div>

        <?php if ($dbUnavailable): ?>
            <div class="dbunavailable">
                <?php echo htmlspecialchars($dbMessage, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($A_view['flash'])): ?>
            <div class="<?php echo $A_view['flash']['success'] ? 'flash-success' : 'flash-error'; ?>">
                <?php echo htmlspecialchars($A_view['flash']['message'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Barre de recherche -->
        <div class="market-search-container">
            <input type="text" id="search-input" placeholder="Recherche" class="input-rectangle">
        </div>

        <div class="market-layout">
            <aside class="market-sidebar">
                <h3 class="sidebar-title">Filtres</h3>
                <div class="sidebar-section">
                    <h4>Catégories</h4>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" name="category" value="all" checked>
                            <span>Toutes</span>
                        </label>
                        <?php foreach ($categories as $cat): ?>
                        <label class="filter-option">
                            <input type="checkbox" name="category" value="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>">
                            <span><?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="sidebar-section">
                    <h4>Prix</h4>
                    <div class="price-range">
                        <input type="number" placeholder="Min" class="price-input">
                        <span>-</span>
                        <input type="number" placeholder="Max" class="price-input">
                    </div>
                </div>
            </aside>

            <!-- Contenu principal -->
            <div class="market-container">
        <?php foreach ($offersByCategory as $category => $offers): ?>
            <div class="category-section">
                <div class="category-header-row">
                    <h2 class="category-name"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <div class="category-arrows">
                        <button class="arrow-btn arrow-left" data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">◀</button>
                        <button class="arrow-btn arrow-right" data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">▶</button>
                    </div>
                </div>

                <div class="products-carousel-wrapper">
                    <div class="products-carousel" data-carousel="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (empty($offers)): ?>
                            <div class="empty-category">
                                <p>Aucune offre dans cette catégorie</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($offers as $offer): ?>
                                <div class="offer-card" data-offer-id="<?php echo $offer['id']; ?>" onclick="if(!event.target.closest('.product-delete-form')) window.location.href='?controller=tradeplace&action=index&offer_id=<?php echo $offer['id']; ?>'">
                                    <div class="offer-card-content">
                                        <h3 class="offer-title"><?php echo htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                        <p class="offer-price"><?php echo number_format($offer['price'], 0, ',', ' '); ?> points</p>
                                    </div>
                                    <?php if ($isLoggedIn && $offer['user_id'] == $_SESSION['user_id']): ?>
                                        <form method="post" action="?controller=marketpage&action=deleteOffer" class="product-delete-form" onclick="event.stopPropagation();">
                                            <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                                            <button type="submit" class="button delete-offer" onclick="return confirm('Supprimer cette offre ?');" <?php echo $disabledAttr; ?>>
                                                ✕
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
            </div>
        </div>

        <!-- Bouton flottant pour ajouter une offre -->
        <?php if ($isLoggedIn): ?>
            <button class="add-offer-btn" id="open-modal-btn" <?php echo $disabledAttr; ?>>+</button>
        <?php endif; ?>
    </div>
</div>

<!-- Modal pour créer une offre -->
<?php if ($isLoggedIn): ?>
    <div id="offer-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="close-modal">&times;</span>
            <h2 class="rectangle-title">Créer une offre</h2>
            <form method="post" action="?controller=marketpage&action=createOffer">
                <div class="input-rectangles">
                    <input type="text" name="title" class="input-rectangle" placeholder="Titre de l'offre" required <?php echo $disabledAttr; ?>>

                    <textarea name="description" class="input-rectangle textarea-field" placeholder="Description de l'offre" rows="4" required <?php echo $disabledAttr; ?>></textarea>

                    <input type="number" name="price" class="input-rectangle" placeholder="Prix (points)" step="0.01" min="0" max="20" required <?php echo $disabledAttr; ?>>

                    <select name="category" class="input-rectangle" required <?php echo $disabledAttr; ?>>
                        <option value="">-- Catégorie --</option>
                        <option value="Maths">Maths</option>
                        <option value="Programmation">Programmation</option>
                        <option value="Network">Réseau</option>
                        <option value="DB">BD</option>
                        <option value="Other">Autre</option>
                    </select>

                    <button type="submit" class="button btn-submit">Publier l'offre</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

