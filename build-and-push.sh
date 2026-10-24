#!/bin/bash
set -e

# Registry config
REGISTRY="registry.sepradc.ovh"
PROJECT="charge"

# Le registry demande une authentification (htpasswd) : réutilise les identifiants
# déjà enregistrés, ne demande le mot de passe que la première fois
echo "🔐 Login to registry..."
docker login $REGISTRY

# Build et push API
echo "🏗️  Building API for ARM64..."
docker buildx build \
  --platform linux/arm64 \
  --push \
  --target prod \
  -t $REGISTRY/$PROJECT/api:arm64 \
  -t $REGISTRY/$PROJECT/api:latest \
  ./api

# Build et push Front
echo "🏗️  Building Front for ARM64..."
docker buildx build \
  --platform linux/arm64 \
  --push \
  --target prod \
  -t $REGISTRY/$PROJECT/front:arm64 \
  -t $REGISTRY/$PROJECT/front:latest \
  ./front

echo "✅ Images pushed successfully!"
echo ""
echo "API:   $REGISTRY/$PROJECT/api:arm64"
echo "Front: $REGISTRY/$PROJECT/front:arm64"

# Redéploiement via le webhook de la stack Portainer (joignable depuis le réseau local ou le VPN).
# L'URL n'est pas commitée : PORTAINER_WEBHOOK_URL ou fichier .portainer-webhook à la racine du dépôt.
WEBHOOK_URL="${PORTAINER_WEBHOOK_URL:-$(cat .portainer-webhook 2>/dev/null || true)}"
echo ""
if [ -n "$WEBHOOK_URL" ]; then
  echo "🚀 Triggering Portainer redeploy..."
  # -k : Portainer utilise un certificat auto-signé
  curl -fsSk -X POST "$WEBHOOK_URL" && echo "✅ Redeploy triggered"
else
  echo "ℹ️  No Portainer webhook configured: redeploy the stack from Portainer"
  echo "   (or put the webhook URL in .portainer-webhook)"
fi
