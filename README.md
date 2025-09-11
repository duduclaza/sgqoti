# Sistema de Gestão de Qualidade

Uma aplicação fullstack completa para gestão de qualidade com controle de toners, homologações, amostragens e muito mais.

## 🚀 Tecnologias Utilizadas

### Frontend
- **React 18** com **Vite**
- **TailwindCSS** para estilização
- **React Router DOM** para roteamento
- **Lucide React** para ícones
- **Axios** para requisições HTTP

### Backend
- **Node.js** com **Express**
- **JWT** para autenticação
- **MySQL/MariaDB** como banco de dados
- **bcryptjs** para hash de senhas
- **Helmet** para segurança
- **CORS** configurado
- **Rate Limiting** implementado

### Banco de Dados
- **MySQL/MariaDB**
- Tabelas: users, items, toners, homologações, amostragens, garantias

## 📋 Funcionalidades

### Módulos Principais
- ✅ **Controle de Toners** - Gestão completa do estoque de toners
- 🔄 **Homologações** - Controle de processos de homologação
- 🧪 **Amostragens** - Gestão de amostras e análises
- 🛡️ **Garantias** - Controle de garantias de produtos
- 🗑️ **Controle de Descartes** - Gestão de descartes
- ⚠️ **FEMEA** - Análise de modos de falha
- 📄 **POPs e ITs** - Procedimentos operacionais
- 📊 **Fluxogramas** - Visualização de processos
- 📈 **Melhoria Contínua** - Gestão de melhorias
- 📋 **Controle de RC** - Controle de registros
- ⚙️ **Configurações** - Configurações do sistema

### Recursos de Segurança
- Autenticação JWT
- Proteção de rotas
- Rate limiting
- Validação de dados
- CORS configurado
- Helmet para segurança HTTP

## 🛠️ Instalação e Configuração

### Pré-requisitos
- Node.js (versão 16 ou superior)
- npm ou yarn
- Conta no Supabase

### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd sgqotidj
```

### 2. Instale as dependências
```bash
# Instalar dependências do projeto principal
npm install

# Instalar dependências do frontend e backend
npm run install:all
```

### 3. Configuração do MySQL/MariaDB

#### 3.1. Instalar MySQL ou MariaDB
**Opção 1 - MySQL:**
- Baixe e instale: https://dev.mysql.com/downloads/mysql/
- Durante a instalação, defina senha para o usuário `root`

**Opção 2 - MariaDB (Recomendado):**
- Baixe e instale: https://mariadb.org/download/
- Durante a instalação, defina senha para o usuário `root`

**Opção 3 - XAMPP (Mais fácil):**
- Baixe XAMPP: https://www.apachefriends.org/
- Inicie o MySQL através do painel do XAMPP

#### 3.2. Criar o banco de dados
Execute o script SQL localizado em `backend/sql/mysql_create_tables.sql`:

**Via linha de comando:**
```bash
mysql -u root -p < backend/sql/mysql_create_tables.sql
```

**Via phpMyAdmin (se usando XAMPP):**
- Acesse http://localhost/phpmyadmin
- Importe o arquivo `mysql_create_tables.sql`

### 4. Configuração das variáveis de ambiente

#### Backend (.env)
O arquivo `backend/.env` já está configurado:
```env
PORT=5000
NODE_ENV=development

# MySQL/MariaDB Configuration
DB_HOST=localhost
DB_PORT=3306
DB_NAME=gestao_qualidade
DB_USER=root
DB_PASSWORD=

JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
FRONTEND_URL=http://localhost:3000
```

**⚠️ IMPORTANTE:** 
- Altere `DB_PASSWORD` para a senha do seu MySQL/MariaDB
- Altere o `JWT_SECRET` para um valor seguro em produção

## 🚀 Executando a Aplicação

### Desenvolvimento (Frontend + Backend)
```bash
npm run dev
```

### Executar separadamente

#### Frontend apenas
```bash
npm run dev:frontend
```

#### Backend apenas
```bash
npm run dev:backend
```

## 📱 Acesso à Aplicação

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:5000
- **Health Check:** http://localhost:5000/health

## 🔐 Autenticação

### Registro de Usuário
1. Acesse http://localhost:3000/register
2. Preencha os dados (nome, email, senha)
3. Confirme o email se necessário

### Login
1. Acesse http://localhost:3000/login
2. Use suas credenciais
3. Será redirecionado para o dashboard

## 📊 API Endpoints

### Autenticação
- `POST /api/auth/login` - Login
- `POST /api/auth/register` - Registro
- `POST /api/auth/logout` - Logout
- `GET /api/auth/profile` - Perfil do usuário

### Usuários
- `GET /api/users` - Listar usuários
- `GET /api/users/:id` - Buscar usuário
- `POST /api/users` - Criar usuário
- `PUT /api/users/:id` - Atualizar usuário
- `DELETE /api/users/:id` - Deletar usuário

### Items
- `GET /api/items` - Listar items
- `GET /api/items/:id` - Buscar item
- `POST /api/items` - Criar item
- `PUT /api/items/:id` - Atualizar item
- `DELETE /api/items/:id` - Deletar item

## 🗂️ Estrutura do Projeto

```
sgqotidj/
├── frontend/
│   ├── src/
│   │   ├── components/     # Componentes reutilizáveis
│   │   ├── pages/          # Páginas da aplicação
│   │   ├── hooks/          # Custom hooks
│   │   ├── services/       # Serviços (API, Supabase)
│   │   └── ...
│   ├── package.json
│   └── ...
├── backend/
│   ├── controllers/        # Controladores da API
│   ├── middleware/         # Middlewares
│   ├── routes/            # Definição de rotas
│   ├── config/            # Configurações
│   ├── sql/               # Scripts SQL
│   ├── server.js          # Servidor principal
│   └── package.json
└── package.json           # Scripts principais
```

## 🔧 Desenvolvimento

### Adicionando Novas Funcionalidades
1. Crie os componentes no frontend (`frontend/src/pages/`)
2. Adicione as rotas no `App.jsx`
3. Implemente os controllers no backend se necessário
4. Atualize o menu lateral (`Sidebar.jsx`)

### Banco de Dados
- Use o painel do Supabase para gerenciar dados
- Execute queries SQL no SQL Editor
- Monitore logs e métricas

## 🚨 Troubleshooting

### Problemas Comuns

#### Erro de CORS
- Verifique se `FRONTEND_URL` está correto no `.env`
- Confirme se o frontend está rodando na porta 3000

#### Erro de Autenticação
- Verifique as credenciais do Supabase
- Confirme se as tabelas foram criadas
- Verifique se o JWT_SECRET está configurado

#### Erro de Conexão com Banco
- Confirme se as credenciais do Supabase estão corretas
- Verifique se as políticas RLS estão configuradas

## 📝 Próximos Passos

1. Implementar as páginas restantes dos módulos
2. Adicionar testes unitários e de integração
3. Configurar CI/CD
4. Implementar notificações em tempo real
5. Adicionar relatórios e dashboards avançados

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.
