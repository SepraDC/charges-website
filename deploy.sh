#!/bin/bash
set -e

# Configuration
REMOTE_HOST="192.168.1.59"
REMOTE_USER="ubuntu"
REMOTE_DIR="/var/www/charges-website"
PROD_HOST="prel.sepradc.ovh"
HTTPS_PORT="1443"  # Port utilisé par Traefik (avec sslh)

# Sans ces variables la prod ne démarre pas (ou démarre avec des secrets publics).
# Elles vivent uniquement sur le serveur, dans .env ou .env.secrets : ce script ne les écrit jamais.
REQUIRED_VARS="HOST APP_SECRET JWT_PASSPHRASE DATABASE_URL MYSQL_ROOT_PASSWORD MYSQL_PASSWORD"

echo "📦 Deploying to production..."

# Copy files to server
echo "📤 Copying files..."
rsync -avz --exclude 'node_modules' --exclude '.git' --exclude '.env' \
  compose-prod.yml \
  $REMOTE_USER@$REMOTE_HOST:$REMOTE_DIR/

# Check the production .env on server
echo "🔐 Checking production .env..."
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_DIR && missing='' && \
  for var in $REQUIRED_VARS; do \
    grep -Eqs \"^\$var=.+\" .env .env.secrets || missing=\"\$missing \$var\"; \
  done; \
  if [ -n \"\$missing\" ]; then \
    echo \"❌ Missing in $REMOTE_DIR/.env:\$missing\" >&2; \
    echo \"   Generate secrets with: openssl rand -hex 32\" >&2; \
    exit 1; \
  fi"

# The api container runs Doctrine migrations on start: dump the database first
echo "💾 Backing up database..."
ssh $REMOTE_USER@$REMOTE_HOST "cd $REMOTE_DIR && mkdir -p ~/backups && \
  if [ -n \"\$(docker compose -f compose-prod.yml ps -q db)\" ]; then \
    docker compose -f compose-prod.yml exec -T db sh -c 'mariadb-dump -u root -p\"\$MYSQL_ROOT_PASSWORD\" --databases \"\$MYSQL_DATABASE\" --single-transaction --triggers' \
      > ~/backups/charges-website-\$(date +%F-%H%M).sql; \
  else \
    echo '   db not running, skipping backup'; \
  fi"

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

