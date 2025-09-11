# 🚀 Deploy na Vercel - SGQ OTI

## Pré-requisitos
- Conta na [Vercel](https://vercel.com)
- Projeto no GitHub (já configurado)

## 📋 Passos para Deploy

### 1. Acesse a Vercel
- Vá para [vercel.com](https://vercel.com)
- Faça login com sua conta GitHub

### 2. Importe o Projeto
- Clique em "New Project"
- Selecione o repositório `sgqotidj`
- Configure as seguintes opções:
  - **Framework Preset**: Other
  - **Root Directory**: `.` (raiz)
  - **Build Command**: `npm run vercel-build`
  - **Output Directory**: `frontend/dist`

### 3. Configurar Variáveis de Ambiente
Na seção "Environment Variables", adicione:

```
DB_HOST=srv1890.hstgr.io
DB_PORT=3306
DB_NAME=u230868210_sgqoti
DB_USER=u230868210_otiplus
DB_PASSWORD=Pandora@1989
JWT_SECRET=seu_jwt_secret_aqui_muito_seguro_123456789
NODE_ENV=production
FRONTEND_URL=https://sgqotidj.vercel.app
```

### 4. Deploy
- Clique em "Deploy"
- Aguarde o build completar

## 🔧 Estrutura Configurada

### Frontend
- ✅ Build otimizado com Vite
- ✅ Rotas SPA configuradas
- ✅ Variáveis de ambiente para produção
- ✅ API URL dinâmica

### Backend
- ✅ API serverless na Vercel
- ✅ CORS configurado para produção
- ✅ Conexão com MySQL Hostinger
- ✅ Middleware de segurança

### Configurações
- ✅ `vercel.json` - Roteamento e builds
- ✅ `.env.production` - Variáveis do frontend
- ✅ `backend/api/index.js` - Entry point serverless

## 🌐 URLs Após Deploy
- **Frontend**: https://sgqotidj.vercel.app
- **API**: https://sgqotidj.vercel.app/api
- **Health Check**: https://sgqotidj.vercel.app/api/health

## 🔍 Troubleshooting

### Se o build falhar:
1. Verifique se todas as dependências estão no `package.json`
2. Confirme se o Node.js está na versão 18+
3. Verifique os logs de build na Vercel

### Se a API não funcionar:
1. Confirme as variáveis de ambiente
2. Teste a conexão com o banco MySQL
3. Verifique os logs da função serverless

### Se as rotas não funcionarem:
1. Confirme se o `vercel.json` está na raiz
2. Verifique se o build gerou o `index.html` corretamente
3. Teste as rotas SPA localmente primeiro

## 📱 Funcionalidades Disponíveis
- ✅ Sistema de login/autenticação
- ✅ Dashboard principal
- ✅ Controle de Toners completo
- ✅ CRUD de toners com cálculos automáticos
- ✅ Interface responsiva
- ✅ API REST completa
