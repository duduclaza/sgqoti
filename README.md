# SGQ OTI - Sistema de Gestão da Qualidade

Sistema de Gestão da Qualidade desenvolvido com React + Vite.

## Estrutura do Projeto

Este projeto está configurado para manter apenas os arquivos estáticos compilados no repositório:

- **Arquivos de desenvolvimento** (src/, public/, package.json, etc.) são ignorados pelo Git
- **Apenas a pasta `dist/`** com os arquivos compilados é versionada

## Scripts Disponíveis

```bash
# Instalar dependências
npm install

# Executar em modo desenvolvimento
npm run dev

# Compilar para produção
npm run build

# Visualizar build de produção
npm run preview
```

## Fluxo de Deploy

1. Desenvolva normalmente na pasta `src/`
2. Execute `npm run build` para gerar os arquivos estáticos em `dist/`
3. Commit apenas os arquivos da pasta `dist/`
4. Os arquivos estáticos estarão prontos para deploy

## Tecnologias

- React 18
- Vite 4
- ESLint
