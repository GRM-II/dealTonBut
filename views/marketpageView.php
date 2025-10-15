<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$dbMessage = $dbUnavailable ? ($A_view['db_status']['message'] . (isset($A_view['db_status']['details']) ? ' – ' . $A_view['db_status']['details'] : '')) : '';
$disabledAttr = $dbUnavailable ? 'disabled' : '';
$isLoggedIn = $A_view['isLoggedIn'] ?? false;

// Organiser les offres par catégorie
$offersByCategory = [];
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
    <h1>Market place</h1>

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

    <!-- Barre de recherche -->
    <div class="market-search">
        <input type="text" id="search-input" placeholder="Recherche" class="input-rectangle">
    </div>

    <div class="market-container">
        <?php if (empty($offersByCategory)): ?>
            <div class="empty-state">
                <p>Aucune offre disponible pour le moment.</p>
                <?php if ($isLoggedIn): ?>
                    <p class="empty-state-cta">Soyez le premier à publier une offre !</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($offersByCategory as $category => $offers): ?>
                <div class="category-section">
                    <div class="category-header">
                        <h2 class="category-title"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <div class="category-nav">
                            <button class="nav-arrow nav-left" data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">◀</button>
                            <button class="nav-arrow nav-right" data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">▶</button>
                        </div>
                    </div>
                    <div class="carousel-wrapper">
                        <div class="carousel-container" data-carousel="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php foreach ($offers as $offer): ?>
                                <div class="product-card" data-offer-id="<?php echo $offer['id']; ?>">
                                    <div class="product-info">
                                        <h3 class="product-name" title="<?php echo htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8'); ?>
                                        </h3>
                                        <p class="product-points"><?php echo number_format($offer['price'], 2, ',', ' '); ?> points</p>
                                    </div>
                                    <?php if ($isLoggedIn && $offer['user_id'] == $_SESSION['user_id']): ?>
                                        <form method="post" action="?controller=marketpage&action=deleteOffer" class="product-delete-form">
                                            <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                                            <button type="submit" class="input-rectangle btn-delete" onclick="return confirm('Supprimer cette offre ?');" <?php echo $disabledAttr; ?>>
                                                Supprimer
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Bouton flottant pour ajouter une offre -->
    <?php if ($isLoggedIn): ?>
        <button class="add-offer-btn" id="open-modal-btn" <?php echo $disabledAttr; ?>>+</button>
    <?php endif; ?>
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

                    <input type="number" name="price" class="input-rectangle" placeholder="Prix (points)" step="0.01" min="0" required <?php echo $disabledAttr; ?>>

                    <select name="category" class="input-rectangle" required <?php echo $disabledAttr; ?>>
                        <option value="">-- Catégorie --</option>
                        <option value="Services">Services</option>
                        <option value="Maths">Maths</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Électronique">Électronique</option>
                        <option value="Mode">Mode</option>
                        <option value="Maison">Maison</option>
                        <option value="Sports">Sports</option>
                        <option value="Alimentation">Alimentation</option>
                        <option value="Autre">Autre</option>
                    </select>

                    <button type="submit" class="input-rectangle btn-submit">Publier l'offre</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

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

        // Gestion du modal
        const modal = document.getElementById('offer-modal');
        const openBtn = document.getElementById('open-modal-btn');
        const closeBtn = document.getElementById('close-modal');

        if (openBtn && modal) {
            openBtn.addEventListener('click', function() {
                modal.style.display = 'flex';
            });
        }

        if (closeBtn && modal) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
        }

        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // Navigation carrousel
        document.querySelectorAll('.nav-left').forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                const carousel = document.querySelector(`[data-carousel="${category}"]`);
                if (carousel) {
                    carousel.scrollBy({ left: -400, behavior: 'smooth' });
                }
            });
        });

        document.querySelectorAll('.nav-right').forEach(btn => {
            btn.addEventListener('click', function() {
                const category = this.getAttribute('data-category');
                const carousel = document.querySelector(`[data-carousel="${category}"]`);
                if (carousel) {
                    carousel.scrollBy({ left: 400, behavior: 'smooth' });
                }
            });
        });

        // Recherche
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.product-card').forEach(card => {
                    const title = card.querySelector('.product-name').textContent.toLowerCase();
                    if (title.includes(searchTerm)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>