<?php if (session_status() === PHP_SESSION_NONE) session_start();
$dbUnavailable = isset($A_view['db_status']) && isset($A_view['db_status']['available']) && !$A_view['db_status']['available'];
$disabledAttr = $dbUnavailable ? 'disabled' : '';
$isLoggedIn = $A_view['isLoggedIn'] ?? false;
$isOwner = $A_view['isOwner'] ?? false;
$offer = $A_view['offer'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'offre - Market place</title>
    <link rel="stylesheet" href="/public/assets/includes/styles.css">
</head>
<body>
<div class="content">
    <!-- Header with navigation -->
    <div class="marketplace-header">
        <div class="header-left">
            <a href="?controller=profilepage&action=index" class="icon-button">
                <img src="/public/assets/img/home-icon.png" alt="Home" class="icon-img">
            </a>
            <a href="?controller=marketpage&action=index" class="marketplace-title-link">
                <h1>Market place</h1>
            </a>
        </div>
        <div class="header-right">
            <button id="theme-toggle" class="icon-button">
                <img id="theme-icon" src="/public/assets/img/moon-icon.png" alt="Toggle theme" class="icon-img">
            </button>
        </div>
    </div>

    <div class="offer-details-container">
        <div class="category-header">
            <h2 class="category-title"><?php echo htmlspecialchars($offer['category'] ?? 'Autre', ENT_QUOTES, 'UTF-8'); ?></h2>
        </div>

        <div class="offer-card-large">
            <div class="offer-header">
                <div class="offer-points">
                    <span class="points-label">Nbr de points :</span>
                    <span class="points-value"><?php echo number_format($offer['price'], 0, ',', ' '); ?></span>
                </div>
                <h2 class="offer-title"><?php echo htmlspecialchars($offer['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>

            <div class="offer-description">
                <h3>Description :</h3>
                <p><?php echo nl2br(htmlspecialchars($offer['description'], ENT_QUOTES, 'UTF-8')); ?></p>
            </div>

            <div class="offer-actions">
                <?php if ($isOwner): ?>
                    <form method="post" action="?controller=marketpage&action=deleteOffer" class="offer-action-form">
                        <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                        <button type="submit" class="btn-validate" onclick="return confirm('Supprimer cette offre ?');" <?php echo $disabledAttr; ?>>
                            ✓
                        </button>
                    </form>
                <?php elseif ($isLoggedIn): ?>
                    <form method="post" action="?controller=marketpage&action=purchaseOffer" class="offer-action-form">
                        <input type="hidden" name="offer_id" value="<?php echo $offer['id']; ?>">
                        <button type="submit" class="btn-purchase" onclick="return confirm('Acheter cette offre ?');" <?php echo $disabledAttr; ?>>
                            Acheter
                        </button>
                    </form>
                <?php else: ?>
                    <a href="?controller=user&action=login" class="btn-purchase">
                        Connectez-vous pour acheter
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <a href="?controller=marketpage&action=index" class="btn-back">← Retour</a>
    </div>
</div>

<script src="/public/assets/includes/theme.js"></script>
</body>
</html>

