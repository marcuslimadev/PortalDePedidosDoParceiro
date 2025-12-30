# 📥 Modelos de Importação de Produtos

## Arquivos Gerados

- ✅ `modelo_importacao_produtos.xlsx` - Modelo Excel (4.7 KB)
- ✅ `modelo_importacao_produtos.csv` - Modelo CSV (344 bytes)

## Como Usar

### 1️⃣ Baixar o Modelo
Acesse: https://darkred-wombat-992258.hostingersite.com/products-import

Clique em um dos botões:
- **Baixar Modelo Excel (.xlsx)** - Recomendado para preenchimento manual
- **Baixar Modelo CSV (.csv)** - Recomendado para sistemas automatizados

### 2️⃣ Preencher os Dados

**Campos Obrigatórios:**
- `codigo` - Código único do produto
- `descricao` - Nome/descrição do produto
- `preco` - Preço de venda (formato: 150.00)
- `unidade` - Unidade de medida (UN, CX, KG, etc)
- `tributacao` - Código de tributação
- `estoque` - Quantidade em estoque

**Campos Opcionais:**
- `categoria` - Categoria do produto
- `marca` - Marca do produto
- `embalagem` - Tipo de embalagem
- `peso_liquido` - Peso líquido em kg
- `peso_bruto` - Peso bruto em kg

### 3️⃣ Enviar para Importação
Volte para a tela de importação e faça upload do arquivo preenchido.

## 📋 Exemplos no Modelo

O modelo já vem com 3 produtos de exemplo:
1. PROD001 - Produto Exemplo 1 - R$ 150,00
2. PROD002 - Produto Exemplo 2 - R$ 85,50
3. PROD003 - Produto Exemplo 3 - R$ 45,00

## 🔄 Regenerar Modelos

Para regenerar os arquivos modelo:
```bash
php create_excel_template.php
```

## 📤 Deploy em Produção

Os arquivos estão em `storage/app/public/`:
- Certifique-se de que essa pasta existe no servidor
- Fazer upload via FTP/Git dos arquivos .xlsx e .csv
- As rotas de download já estão configuradas
