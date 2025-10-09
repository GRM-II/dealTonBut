<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DealTonBut</title>
    <link rel="stylesheet" href="/public/assets/includes/styles/style.css">
    <link rel="icon" type="image/x-icon" href="/public/assets/img/favicon.ico">
</head>
<body>
    <?php View::show('standard/header'); ?>

    <main>
        <?php echo $A_view['body']; ?>
    </main>

    <?php View::show('standard/footer'); ?>
</body>
</html>
