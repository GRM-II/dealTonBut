<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Offres</title>
</head>
<body>
<h1>Gestion des Offres</h1>

<nav>
    <a href="?controller=admin&action=index">Dashboard</a> |
    <a href="?controller=admin&action=users">Utilisateurs</a> |
    <a href="?controller=user&action=logout">Déconnexion</a>
</nav>

<hr>

<?php if (isset($_SESSION['flash'])): ?>
    <p style="color: <?= $_SESSION['flash']['success'] ? 'green' : 'red' ?>">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </p>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (isset($A_view['message'])): ?>
    <p><?= htmlspecialchars($A_view['message']) ?></p>
<?php elseif (empty($A_view['offers'])): ?>
    <p>Aucune offre</p>
<?php else: ?>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Utilisateur</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($A_view['offers'] as $offer): ?>
            <tr>
                <td><?= $offer['id'] ?></td>
                <td><?= htmlspecialchars($offer['title'] ?? '') ?></td>
                <td><?= htmlspecialchars($offer['category'] ?? '') ?></td>
                <td><?= $offer['price'] ?? 0 ?></td>
                <td><?= htmlspecialchars($offer['username'] ?? '') ?></td>
                <td>
                    <a href="?controller=tradeplace&action=index&offer_id=<?= $offer['id'] ?>" target="_blank">Voir</a>

                    <form method="POST" action="?controller=admin&action=deleteOffer" style="display:inline" onsubmit="return confirm('Supprimer?')">
                        <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</body>
</html>