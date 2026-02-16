<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
<h1>Dashboard Admin</h1>
<p>Connecté en tant que: <?= htmlspecialchars($_SESSION['user']['username']) ?></p>

<nav>
    <a href="?controller=admin&action=users">Gérer les utilisateurs</a> |
    <a href="?controller=admin&action=offers">Gérer les offres</a> |
    <a href="?controller=profilepage&action=index">Mon profil</a> |
    <a href="?controller=user&action=logout">Déconnexion</a>
</nav>

<hr>

<h2>Statistiques</h2>
<p>Utilisateurs: <?= $A_view['stats']['total_users'] ?></p>
<p>Offres: <?= $A_view['stats']['total_offers'] ?></p>
</body>
</html>