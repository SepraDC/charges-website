#!/bin/bash
set -e

# Registry config
REGISTRY="registry.sepradc.ovh"
PROJECT="charge"

# Check if logged in to registry
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

