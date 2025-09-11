# 🚀 Configuração Rápida do MySQL

## Opção 1: XAMPP (Mais Fácil)

1. **Baixar XAMPP**: https://www.apachefriends.org/
2. **Instalar** e abrir o painel de controle
3. **Iniciar MySQL** clicando em "Start"
4. **Acessar phpMyAdmin**: http://localhost/phpmyadmin
5. **Criar banco**: Clique em "New" → Nome: `gestao_qualidade` → Create
6. **Importar SQL**: 
   - Clique na aba "Import"
   - Escolha o arquivo `backend/sql/mysql_create_tables.sql`
   - Clique "Go"

## Opção 2: MySQL Standalone

1. **Baixar MySQL**: https://dev.mysql.com/downloads/mysql/
2. **Instalar** (definir senha para root)
3. **Executar no terminal**:
```bash
mysql -u root -p
CREATE DATABASE gestao_qualidade;
USE gestao_qualidade;
SOURCE C:/Users/Clayton/Desktop/sgqotidj/backend/sql/mysql_create_tables.sql;
```

## ⚙️ Configurar Senha

Se você definiu uma senha para o MySQL, edite o arquivo `backend/.env`:

```env
DB_PASSWORD=sua_senha_aqui
```

## 🧪 Testar Conexão

Execute o backend para testar:
```bash
cd backend
npm run dev
```

Se aparecer "✅ Conectado ao MySQL/MariaDB com sucesso!", está funcionando!

## 👤 Usuário Padrão

O sistema cria automaticamente um usuário administrador:
- **Email**: admin@sistema.com  
- **Senha**: password

## 🎯 Dados de Exemplo

O script também insere alguns toners de exemplo para testar o sistema.
