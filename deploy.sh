#!/bin/bash
# -------------------------------------------------------------------
# Script de Deploy Laravel EC2
# -------------------------------------------------------------------

USER="ec2-user"
HOST="3.20.235.172"
APP_DIR="/home/ec2-user/alpes-api"
SSH_KEY="/home/kauarodrigues/.ssh/ec2.pem"

erro() {
    echo "[ERRO] $1"
    exit 1
}

info() {
    echo "[INFO] $1"
}

info "Iniciando deploy"

if [ ! -f "$SSH_KEY" ]; then
    erro "Chave SSH não encontrada em $SSH_KEY"
fi

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

info "Copiando arquivos para $HOST..."
rsync -avz --delete --exclude-from="$EXCLUDE_FILE" ./ -e "ssh -i $SSH_KEY" $USER@$HOST:$APP_DIR
if [ $? -ne 0 ]; then
    erro "Falha ao copiar arquivos para o servidor"
fi

rm -f $EXCLUDE_FILE

info "Conectando ao servidor $HOST..."
ssh -i $SSH_KEY $USER@$HOST << EOF
    set -e  # interrompe execução em caso de erro
    cd $APP_DIR || { echo "Diretório $APP_DIR não encontrado"; exit 1; }

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

info "Deploy finalizado."
