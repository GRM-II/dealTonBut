<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion Utilisateurs</title>
</head>
<body>
<h1>Gestion des Utilisateurs</h1>

<nav>
    <a href="?controller=admin&action=index">Dashboard</a> |
    <a href="?controller=admin&action=offers">Offres</a> |
    <a href="?controller=user&action=logout">Déconnexion</a>
</nav>

<hr>

<?php if (isset($_SESSION['flash'])): ?>
    <p style="color: <?= $_SESSION['flash']['success'] ? 'green' : 'red' ?>">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </p>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($A_view['users'] as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['role'] ?></td>
            <td>
                <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                    <form method="POST" action="?controller=admin&action=toggleAdmin" style="display:inline">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="role" value="<?= $user['role'] === 'admin' ? 'user' : 'admin' ?>">
                        <button type="submit">
                            <?= $user['role'] === 'admin' ? 'Rétrograder' : 'Promouvoir Admin' ?>
                        </button>
                    </form>

                    <form method="POST" action="?controller=admin&action=deleteUser" style="display:inline" onsubmit="return confirm('Supprimer?')">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                <?php else: ?>
                    (Vous)
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>