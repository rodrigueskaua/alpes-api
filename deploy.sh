#!/bin/bash
# -------------------------------------------------------------------
# Script de Deploy Laravel EC2
# -------------------------------------------------------------------

set -e

DEPLOY_USER=$(grep '^DEPLOY_USER=' .env | cut -d '=' -f2-)
DEPLOY_HOST=$(grep '^DEPLOY_HOST=' .env | cut -d '=' -f2-)
DEPLOY_DIR=$(grep '^DEPLOY_DIR=' .env | cut -d '=' -f2-)
DEPLOY_SSH_KEY=$(grep '^DEPLOY_SSH_KEY=' .env | cut -d '=' -f2- | sed 's/\\n/\n/g')

erro() {
    echo "[ERRO] $1"
    exit 1
}

info() {
    echo "[INFO] $1"
}
info "Iniciando deploy"

[ -z "$DEPLOY_USER" ] && erro "DEPLOY_USER não definido"
[ -z "$DEPLOY_HOST" ] && erro "DEPLOY_HOST não definido"
[ -z "$DEPLOY_DIR" ] && erro "DEPLOY_DIR não definido"
[ -z "$DEPLOY_SSH_KEY" ] && erro "DEPLOY_SSH_KEY não definido"

TEMP_KEY=$(mktemp)
CLEAN_KEY=${DEPLOY_SSH_KEY#\"}
CLEAN_KEY=${CLEAN_KEY%\"}
printf "%b" "$CLEAN_KEY" > "$TEMP_KEY"
chmod 600 "$TEMP_KEY"
echo "[INFO] Arquivo temporário de chave criado em: $TEMP_KEY"

if ! command -v rsync &> /dev/null; then
    erro "rsync não está instalado. Instale com: sudo yum install -y rsync"
fi

EXCLUDE_FILE=$(mktemp)
cat <<EOL > $EXCLUDE_FILE
*.log
.DS_Store
.env
.env.backup
.env.production
.phpactor.json
.phpunit.result.cache
/.fleet
/.idea
/.nova
/.phpunit.cache
/.vscode
/.zed
/auth.json
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/storage/pail
/vendor
Homestead.json
Homestead.yaml
Thumbs.db
EOL

info "Copiando arquivos para $DEPLOY_HOST."
rsync -avz --delete --exclude-from="$EXCLUDE_FILE" ./ -e "ssh -i $TEMP_KEY" $DEPLOY_USER@$DEPLOY_HOST:$DEPLOY_DIR || erro "Falha ao copiar arquivos"

rm -f $EXCLUDE_FILE

info "Conectando ao servidor $DEPLOY_HOST"
ssh -i $TEMP_KEY $DEPLOY_USER@$DEPLOY_HOST << EOF
    set -e
    cd $DEPLOY_DIR || { echo "Diretório $DEPLOY_DIR não encontrado"; exit 1; }

    echo "[SERVER] Instalando dependências PHP..."
    composer install --no-dev --optimize-autoloader || { echo "[SERVER][ERRO] Falha ao instalar dependências"; exit 1; }

    echo "[SERVER] Executando migrations..."
    php artisan migrate --force || { echo "[SERVER][ERRO] Falha ao executar migrations"; exit 1; }

    echo "[SERVER] Limpando caches Laravel..."
    php artisan config:clear
    php artisan route:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan config:cache

    echo "[SERVER] Reiniciando serviços..."
    sudo systemctl restart php-fpm || echo "[SERVER][AVISO] php-fpm não encontrado ou não reiniciado"
    sudo systemctl restart nginx || echo "[SERVER][AVISO] nginx não encontrado ou não reiniciado"

    echo "[SERVER] Deploy concluído com sucesso."
EOF

rm -f $TEMP_KEY
info "Deploy finalizado."
