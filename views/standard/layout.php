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
        <?php echo $A_view['body']; ?>
    </main>
    <?php view::show('standard/footer'); ?>
    
    <script>
        // Global theme management functions
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

        function toggleTheme() {
            document.body.classList.toggle('dark-theme');
            localStorage.setItem('theme', document.body.classList.contains('dark-theme') ? 'dark' : 'light');
            setThemeIcon();
        }

        // Apply theme on page load
        window.addEventListener('DOMContentLoaded', applySavedTheme);
    </script>
</body>
</html>
