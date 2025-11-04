#!/bin/bash

set -euo pipefail


DOC_DIR="docs/api"
PORT_DEFAULT=8080
PORT="${1:-${PORT:-$PORT_DEFAULT}}"
BIND_ADDR="${BIND_ADDR:-127.0.0.1}"

GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

if [ ! -d "$DOC_DIR" ]; then
    echo -e "${RED}Le dossier $DOC_DIR n'existe pas.${NC}"
    echo "Générez d'abord la documentation avec: ./generate-docs.sh"
    exit 1
fi

if ! command -v php >/dev/null 2>&1; then
    echo -e "${RED}PHP introuvable dans le PATH.${NC}"
    exit 1
fi

if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo -e "${RED}Le port $PORT est déjà utilisé${NC}"
    echo "Essayez un autre port: ex. ./serve-docs.sh 8000"
    exit 1
fi

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}Serveur de documentation démarré${NC}"
echo -e "${GREEN}=========================================${NC}"
echo ""
echo -e "URL: ${BLUE}http://$BIND_ADDR:$PORT${NC}"
echo -e "(alias: http://localhost:$PORT si localhost → $BIND_ADDR)"
echo ""
echo -e "${YELLOW}Appuyez sur Ctrl+C pour arrêter le serveur${NC}"
echo ""

php -S "$BIND_ADDR:$PORT" -t "$DOC_DIR"
