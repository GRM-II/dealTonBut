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
    <?php view::show('standard/header'); ?>
    <main>
        <?php if (!empty($A_view)) {
            echo $A_view['body'];
        } ?>
    </main>
    <?php view::show('standard/footer'); ?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="/public/assets/includes/scripts/script.js"></script>
</body>
</html>
