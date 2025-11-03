#!/bin/bash

# Script pour servir la documentation localement
# Sans droits admin

PORT=8080
DOC_DIR="docs/api"

# Couleurs
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

# Vérifier que le dossier existe
if [ ! -d "$DOC_DIR" ]; then
    echo -e "${RED}Le dossier $DOC_DIR n'existe pas.${NC}"
    echo "Générez d'abord la documentation avec: ./generate-docs.sh"
    exit 1
fi

# Vérifier si le port est déjà utilisé
if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo -e "${RED}Le port $PORT est déjà utilisé${NC}"
    echo "Essayez un autre port ou arrêtez le processus qui l'utilise"
    exit 1
fi

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}Serveur de documentation démarré${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""
echo -e "URL: ${BLUE}http://localhost:$PORT${NC}"
echo ""
echo -e "${YELLOW}Appuyez sur Ctrl+C pour arrêter le serveur${NC}"
echo ""

cd "$DOC_DIR"
php -S localhost:$PORT