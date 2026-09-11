#!/bin/bash
set -e

# Configuration
REMOTE_HOST="192.168.1.59"
REMOTE_USER="ubuntu"
REMOTE_DIR="/var/www/charges-website"
PROD_HOST="prel.sepradc.ovh"
HTTPS_PORT="1443"  # Port utilisé par Traefik (avec sslh)

echo "📦 Deploying to production..."

# Copy files to server
echo "📤 Copying files..."
rsync -avz --exclude 'node_modules' --exclude '.git' --exclude '.env' \
  compose-prod.yml \
  $REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR/

# Create/update .env on server
echo "📝 Creating production .env..."
ssh $REMOTE_USER@$REMOTE_HOST "cat > $REMOTE_DIR/.env << 'EOF'
HOST=$PROD_HOST
APP_ENV=prod
APP_SECRET=\${APP_SECRET:-change_me_please}

# Database
DATABASE_URL=\${DATABASE_URL:-mysql://charges:password@db:3306/charges}
MYSQL_DATABASE=\${MYSQL_DATABASE:-charges}
MYSQL_USER=\${MYSQL_USER:-charges}
MYSQL_PASSWORD=\${MYSQL_PASSWORD:-password}
MYSQL_ROOT_PASSWORD=\${MYSQL_ROOT_PASSWORD:-rootpassword}

# Nuxt
NUXT_PUBLIC_API_BASE_URL=https://$PROD_HOST/api

# Timezone
TZ=Europe/Paris
EOF
"

# Execute deployment on remote server
echo "🚀 Starting containers on remote server..."
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_DIR && \
  # Source secrets if they exist
  [ -f .env.secrets ] && . .env.secrets && export \$(cut -d= -f1 .env.secrets); \
  docker compose -f compose-prod.yml pull && \
  docker compose -f compose-prod.yml up -d"

echo "✅ Deployment completed!"
echo ""
echo "🌐 Your app should be available at:"
echo "   https://$PROD_HOST:$HTTPS_PORT"
echo "   https://$PROD_HOST:$HTTPS_PORT/admin"

