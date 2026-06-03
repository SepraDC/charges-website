#!/bin/bash
set -e

echo "🔧 Configuration de Docker Buildx pour ARM64..."

# Remove existing builder if any
docker buildx rm multiarch 2>/dev/null || true

# Create new builder with QEMU support
docker run --privileged --rm tonistiigi/binfmt --install all

# Create builder
docker buildx create --name multiarch \
  --driver docker-container \
  --platform linux/amd64,linux/arm64 \
  --use

# Bootstrap the builder
docker buildx inspect --bootstrap

echo "✅ Buildx configuré avec succès!"
echo ""
docker buildx ls

