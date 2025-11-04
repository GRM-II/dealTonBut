#!/usr/bin/env bash

set -euo pipefail

echo "========================================="
echo "Documentation PHPDoc - dealTonBut"
echo "Utilisateur: $(whoami)"
echo "Date: $(date '+%Y-%m-%d %H:%M:%S')"
echo "========================================="

# Vérifier phpDocumentor via Composer
if [ ! -x "vendor/bin/phpdoc" ]; then
    echo "❌ phpDocumentor introuvable dans vendor/bin/phpdoc"
    echo "Installez-le avec: composer require --dev phpdocumentor/phpdocumentor"
    exit 1
fi

# Créer les dossiers
mkdir -p docs/api
mkdir -p .phpdoc/cache

# Nettoyer
echo "Nettoyage..."
rm -rf docs/api/*
rm -rf .phpdoc/cache/*

# S'assurer qu'il n'y a PAS de phpdoc.xml local (si corrompu)
if [ -f "phpdoc.xml" ]; then
    echo "⚠️  Renommage de phpdoc.xml (corrompu?)"
    mv phpdoc.xml phpdoc.xml.old
fi

# Générer avec l'exécutable Composer
echo "Génération de la documentation (Composer)..."
php -d memory_limit=512M vendor/bin/phpdoc run \
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
    echo "- PHPDocumentor (Composer): $(vendor/bin/phpdoc --version 2>&1 | head -1)"
    echo ""
    exit 1
fi
