# Alpes One API - Documentação

Esta Documentação detalha todos os passos necessários para configurar, executar, testar e implantar a aplicação **Alpes One API**. Aborda o ambiente de desenvolvimento local com Laravel Sail (Docker) e o deploy no ambiente de produção na AWS EC2.

## Contexto do Projeto

O **Alpes One API** foi desenvolvido como parte de um desafio técnico, com o intuito de demonstrar boas práticas em **desenvolvimento backend**, **infraestrutura em nuvem** e **processos de automação**.  

As principais tecnologias e ferramentas utilizadas foram:

- **Laravel (PHP 8+)** – Framework principal para construção da API.  
- **MySQL** – Banco de dados relacional utilizado.  
- **Docker + Laravel Sail** – Gerenciamento de ambiente de desenvolvimento padronizado.  
- **AWS EC2** – Hospedagem em servidor cloud de produção.  
- **Nginx** – Servidor web configurado para rodar a aplicação em produção.  
- **Certbot + Let's Encrypt** – Geração e renovação automática de certificados SSL.  
- **GitHub Actions** – Pipeline de **CI/CD** para deploy automatizado.  
- **Swagger (L5-Swagger)** – Documentação interativa da API.  

### Principais Links

- **Documentação da API (Swagger):** [https://alpesapi.kauarodrigues.dev/api/documentation](https://alpesapi.kauarodrigues.dev/api/documentation)  
- **Endpoint de Produção:** [https://alpesapi.kauarodrigues.dev](https://alpesapi.kauarodrigues.dev)  

### Endpoints da API

#### Logs  
Operações relacionadas aos logs do importador:  

- **GET** `/api/v1/log/vehicles` → Exibe linhas do log do importador  

#### Vehicles  
Operações relacionadas a veículos:  

- **GET** `/api/v1/vehicles` → Listar veículos  
- **POST** `/api/v1/vehicles` → Criar um novo veículo  
- **GET** `/api/v1/vehicles/{id}` → Mostrar um veículo  
- **PUT** `/api/v1/vehicles/{id}` → Atualizar um veículo existente  
- **DELETE** `/api/v1/vehicles/{id}` → Deletar um veículo  

## Objetivo do Projeto

Desenvolver uma API em Laravel para gerenciar veículos importados de um JSON remoto. O projeto demonstra habilidades em desenvolvimento backend, testes automatizados, infraestrutura AWS e práticas de DevOps (CI/CD).

### Pré-requisitos

Antes de começar, garanta que você tenha os seguintes softwares instalados:

#### Para Desenvolvimento Local (com Sail)

* **Docker Desktop** (para Windows/Mac) ou **Docker Engine + Docker Compose** (para Linux).
* **Git** para clonar o repositório.
* **PHP e Composer** para instalação inicial.
* **WSL2** (para usuários Windows), para uma melhor performance com Docker.

#### Para Deploy na AWS

* **Cliente de linha de comando da AWS (AWS CLI)**.
* **Uma conta na AWS**.

---

## 1. Passos para Rodar a Aplicação Localmente (com Laravel Sail)

Siga os passos abaixo para configurar e executar o projeto usando o ambiente de contêineres fornecido pelo Laravel Sail.

**1. Clone o Repositório:**
Primeiro, clone o projeto para sua máquina local.
```bash
git clone https://github.com/rodrigueskaua/alpes-api
cd alpes-api
```

**2. Configure o Arquivo de Ambiente:**
Copie o arquivo de exemplo `.env.example` para `.env`. É neste arquivo que o Docker Compose buscará as credenciais e configurações.
```bash
cp .env.example .env
```
[Link para o `.env` utilizado no projeto e configurado (incluindo variáveis de deploy)](https://drive.google.com/file/d/1OM8wDc1E8RgSlBCydKcmwqHAJa_iIxFe/view?usp=sharing)

**3. Edite as Variáveis de Ambiente no `.env`:**
Abra o arquivo `.env` e certifique-se de que as variáveis de banco de dados estão configuradas para se conectar ao contêiner do MySQL:

* `DB_CONNECTION=mysql`
* `DB_HOST=alpesone-mysql` (**Importante:** use o nome do serviço do Docker, não `localhost`).
* `DB_PORT=3306` (A porta interna do contêiner).
* `DB_DATABASE=alpesone` (ou o nome que você definiu).
* `DB_USERNAME=sail` (ou o usuário que você definiu).
* `DB_PASSWORD=password` (ou a senha que você definiu).

**4. Inicie os Contêineres do Sail:**
Este comando irá baixar as imagens Docker, construir o contêiner da aplicação e iniciar todos os serviços em segundo plano (`-d`).
```bash
./vendor/bin/sail up -d
```

**5. Instale as Dependências do Composer:**
Com os contêineres em execução, execute o Composer dentro do contêiner da aplicação.
```bash
./vendor/bin/sail composer install
```

**6. Execute os Comandos Artisan:**
```bash
# Gere a chave da aplicação
./vendor/bin/sail artisan key:generate

# Execute as migrations para criar as tabelas no banco de dados
./vendor/bin/sail artisan migrate

# Importe os dados iniciais do JSON remoto
./vendor/bin/sail artisan import:vehicles
```

A aplicação agora está rodando e acessível em **`http://localhost`** (ou na porta que especificada em `APP_PORT`).

---

## 2. Rodando os Testes Automatizados

Para garantir a qualidade e o funcionamento correto da aplicação, execute a suíte de testes com o seguinte comando:
```bash
./vendor/bin/sail artisan test
```

---

## 3. Configurando a Aplicação na AWS EC2

Este guia assume o uso de uma instância **Ubuntu** na AWS para o ambiente de produção.

**1. Crie a Instância EC2:**

* No painel da AWS, lance uma instância EC2 (ex: `t2.micro`).
* Associe um par de chaves SSH para acesso seguro.
* Em "Security Groups", libere as portas:
    * **SSH (22):** Para seu IP
    * **HTTP (80):** Aberto para `0.0.0.0/0`
    * **HTTPS (443):** Aberto para `0.0.0.0/0`

**2. Instale a Stack LEMP (Nginx, MySQL, PHP):**
Conecte-se à sua instância via SSH e instale os softwares necessários.
```bash
# Atualizar pacotes
sudo apt update && sudo apt upgrade -y

# Instalar Nginx, MySQL e PHP com extensões
sudo apt install nginx mysql-server php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip -y
```

**3. Instale o Composer Globalmente:**
Execute os seguintes comandos para baixar, verificar e instalar o Composer de forma segura.
```bash
# Baixar o instalador
php -r "copy('[https://getcomposer.org/installer](https://getcomposer.org/installer)', 'composer-setup.php');"

# Verificar a integridade do instalador (substitua o hash pelo mais recente do site oficial)
php -r "if (hash_file('sha344', 'composer-setup.php') === 'a5c698ffe4b8e0c9fbc3a9327629396f60d7398246a27344f8d2aa0f40d5c00b65d49ffd641a22d0a580a2d125e2724d') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"

# Instalar o Composer no diretório de binários globais
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Remover o arquivo de instalação
php -r "unlink('composer-setup.php');"
```

**4. Configure o Nginx:**
Crie um arquivo de configuração para o site em `/etc/nginx/sites-available/alpes-one-api`. Abaixo está um exemplo final do arquivo do projeto configurado com um domínio e SSL (via Certbot).
```nginx
server {
    server_name alpesapi.kauarodrigues.dev;

    root /home/ec2-user/alpes-api/public;
    index index.php index.html index.htm;

    access_log /var/log/nginx/alpesapi.access.log;
    error_log  /var/log/nginx/alpesapi.error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php-fpm/www.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.ht {
        deny all;
    }

    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/alpesapi.kauarodrigues.dev/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/alpesapi.kauarodrigues.dev/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot
}

server {
    if ($host = alpesapi.kauarodrigues.dev) {
        return 301 https://$host$request_uri;
    } # managed by Certbot

    listen 80;
    server_name alpesapi.kauarodrigues.dev;
    return 404; # managed by Certbot
}
```

Depois de criar o arquivo, ative o site e reinicie o Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/alpes-one-api /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

**5. Configure o Agendador (Scheduler):**
Para que a importação seja verificada automaticamente a cada hora, adicione a seguinte entrada no Crontab do servidor (`crontab -e`), ajustando o caminho para do projeto na AWS:
```bash
* * * * * cd /home/ec2-user/alpes-api && php artisan schedule:run >> /dev/null 2>&1
```
---

## 4. Como Rodar o Script de Deploy

O script `deploy.sh` automatiza o processo de atualização do código na instância EC2.

**1. Pré-requisitos:**

* Configure as variáveis no .env (`DEPLOY_USER`, `DEPLOY_HOST`, `DEPLOY_SSH_KEY`, `DEPLOY_DIR`) no início do script `deploy.sh`.
* Garanta que o script tenha permissão de execução (`chmod +x deploy.sh`).

**2. Executando o Deploy:**
Do seu terminal local, na raiz do projeto, execute:
```bash
bash deploy.sh
```

---

## 5. Extras

#### Documentação da API (Swagger)

A documentação completa dos endpoints da API foi gerada com o pacote L5-Swagger e está disponível para consulta nos seguintes endereços:

* **Ambiente Local:** [http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)
* **Ambiente de Produção:** <https://alpesapi.kauarodrigues.dev/api/documentation>

#### Domínio e HTTPS

* **Domínio:** Foi adquirido um domínio (`kauarodrigues.dev`) e configurado um subdomínio (`alpesapi`) no provedor de DNS para apontar para o IP público da instância EC2.
* **HTTPS:** O SSL foi implementada utilizando **Certbot**, que gerou um certificado SSL/TLS gratuito da Let's Encrypt. A configuração do Nginx, detalhada anteriormente, já inclui o redirecionamento automático de HTTP para HTTPS.
* **CORS:** Foi configurado o Cross-Origin Resource Sharing (CORS) na aplicação Laravel para permitir requisições vindas do domínio principal.

#### CI/CD com GitHub Actions

Foi implementado um pipeline de deploy automatizado utilizando GitHub Actions. O workflow é acionado a cada alteração na branch `master`.

O processo executado pelo pipeline é o seguinte:

1.  **Checkout do Código:** Baixa a versão mais recente do código do repositório.
2.  **Configura Chave SSH:** Utiliza uma chave privada, armazenada de *Secrets* do GitHub, para se autenticar no servidor EC2.
3.  **Deploy para EC2:** Conecta-se via SSH ao servidor e executa os seguintes comandos:
    * `git pull origin master`: Atualiza o código na pasta do projeto.
    * `composer install`: Instala as dependências do PHP, otimizadas para produção.
    * `php artisan migrate --force`: Executa as migrations do banco de dados.
    * `php artisan config:cache`: Limpa e cria um cache das configurações para otimizar a performance.
    * `sudo systemctl restart php-fpm nginx`: Reinicia os serviços do servidor para aplicar todas as alterações.

Isso garante que qualquer atualização enviada para a branch principal seja automaticamente implantada no ambiente de produção de forma rápida e segura.

#### Logs de Importação

Foi criada uma rota específica para visualizar os logs gerados pelo comando de importação de veículos. Este endpoint permite verificar o histórico de execuções, o resultado de cada uma (sucesso ou erro), a data de início e a quantidade de veículos que foram inseridos ou atualizados.

* **Ambiente Local:** [http://localhost:8080/api/v1/log/vehicles?lines=all](http://localhost:8080/api/v1/log/vehicles?lines=all)
* **Ambiente de Produção:** [https://alpesapi.kauarodrigues.dev/api/v1/log/vehicles?lines=all](https://alpesapi.kauarodrigues.dev/api/v1/log/vehicles?lines=all)
