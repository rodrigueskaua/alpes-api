# Alpes One API

## Objetivo do Projeto

Projeto API Laravel para gerenciar veículos importados de um JSON remoto fornecido pelo hub da Alpes One.
O foco é demonstrar habilidades em:

- Desenvolvimento backend com Laravel
- Testes unitários e de integração
- Preparação para deploy em AWS (EC2) e CI/CD

## Planejamento da Aplicação

### 1. Model

**Vehicle** – representa um veículo/anúncio importado do JSON.

**Campos principais:**
- external_id
- type
- brand
- model
- version
- year_model
- year_build
- optionals
- doors
- board
- chassi
- transmission
- km
- description
- created_json
- updated_json
- sold
- category
- url_car
- old_price
- price
- color
- fuel
- photos

### 2. Migration

- Criação da tabela `vehicles` com todos os campos relevantes do JSON.

### 3. Service

**VehicleImportService** responsável por:
- Baixar JSON da URL
- Inserir/atualizar registros no banco
- Manter lógica de importação separada do Controller e Command

### 4. Command Artisan

**ImportVehicles** – executa a importação via CLI:

```bash
php artisan import:vehicles
```