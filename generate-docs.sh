echo "========================================="
echo "Documentation PHPDoc - dealTonBut"
echo "Utilisateur: dimitriCrespo"
echo "Date: 2025-11-03"
echo "========================================="

# Télécharger PHPDocumentor si nécessaire
if [ ! -f "phpDocumentor.phar" ]; then
    echo "Téléchargement de PHPDocumentor..."
    wget -q https://phpdoc.org/phpDocumentor.phar
    chmod +x phpDocumentor.phar
    echo "✅ PHPDocumentor téléchargé"
fi

# Créer les dossiers
mkdir -p docs/api
mkdir -p .phpdoc/cache

# Nettoyer
echo "Nettoyage..."
rm -rf docs/api/*
rm -rf .phpdoc/cache/*

# S'assurer qu'il n'y a PAS de phpdoc.xml
if [ -f "phpdoc.xml" ]; then
    echo "⚠️  Renommage de phpdoc.xml (corrompu)"
    mv phpdoc.xml phpdoc.xml.old
fi

# Générer SANS fichier de configuration
echo "Génération de la documentation..."
php -d memory_limit=512M phpDocumentor.phar run \
    -d controllers \
    -d models \
    -d core \
    -d views \
    -t docs/api \
    --cache-folder=.phpdoc/cache \
    --title="dealTonBut - Documentation API"

RESULT=$?

if [ $RESULT -eq 0 ]; then
    echo ""
    echo "========================================="
    echo "✅ Documentation générée avec succès !"
    echo "========================================="
    echo ""
    echo "📁 Emplacement: $(pwd)/docs/api/index.html"
    echo ""
    echo "Pour visualiser:"
    echo "  cd docs/api && php -S localhost:8080"
    echo "  Puis ouvrir: http://localhost:8080"
    echo ""
else
    echo ""
    echo "❌ Erreur lors de la génération (code: $RESULT)"
    echo ""
    echo "Diagnostic:"
    echo "- Version PHP: $(php -v | head -1)"
    echo "- PHPDocumentor: $(php phpDocumentor.phar --version 2>&1 | head -1)"
    echo ""
    exit 1
fi