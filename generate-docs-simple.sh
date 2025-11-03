#!/bin/bash

echo "Génération de la documentation PHPDoc..."

# Créer les dossiers
mkdir -p docs/api
mkdir -p .phpdoc/cache

# Nettoyer
rm -rf docs/api/*
rm -rf .phpdoc/cache/*

# Générer avec des paramètres explicites
php -d memory_limit=512M vendor/bin/phpdoc run \
    --directory=controllers \
    --directory=models \
    --directory=core \
    --directory=views \
    --target=docs/api \
    --cache-folder=.phpdoc/cache \
    --ignore=vendor/,docs/,.git/,.idea/,.phpdoc/ \
    --title="dealTonBut - Documentation API" \
    --visibility=public,protected,private

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Documentation générée avec succès !"
    echo "📁 docs/api/index.html"
    echo ""
    echo "Pour visualiser:"
    echo "  ./serve-docs.sh"
    echo "  ou"
    echo "  xdg-open docs/api/index.html"
else
    echo "❌ Erreur lors de la génération"