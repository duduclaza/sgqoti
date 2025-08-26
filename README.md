# SGQ Hello World Project

Projeto simples de HTML + CSS com sistema de cache-busting usando fingerprinting.

## 🚀 Funcionalidades

- **Hello World** responsivo com frase "Deus é fiel"
- **CSS Fingerprinting** para cache-busting automático
- **Limpeza automática** de arquivos CSS antigos

## 📁 Estrutura dos Arquivos

```
├── index.html              # Página principal
├── styles.2563eb9a.css     # CSS atual (com hash)
├── cleanup.js              # Script de limpeza
├── cleanup.bat             # Script Windows
└── package.json            # Configuração npm
```

## 🧹 Gerenciamento de Arquivos CSS

### Problema
O fingerprinting cria novos arquivos CSS a cada mudança:
- `styles.ea580c7f.css` (laranja)
- `styles.2563eb9a.css` (azul) ← atual
- `styles.abc123de.css` (próxima mudança)

### Solução Automática

**Opção 1: Script Node.js**
```bash
npm run cleanup
```

**Opção 2: Script Windows**
```bash
cleanup.bat
```

**Opção 3: Manual**
```bash
node cleanup.js
```

### Como Funciona

1. **Detecta** qual CSS está sendo usado no HTML
2. **Lista** todos os arquivos CSS com hash
3. **Remove** apenas os arquivos antigos não utilizados
4. **Mantém** o arquivo CSS atual

## 🔄 Fluxo de Trabalho Recomendado

1. Faça mudanças no CSS
2. Gere novo hash: `styles.novoHash.css`
3. Atualize referência no HTML
4. Execute limpeza: `npm run cleanup`
5. Commit e push

## 🛠️ Automação com Build Tools

Para projetos maiores, use ferramentas como:
- **Webpack** - `[contenthash]`
- **Vite** - `build.rollupOptions.output.assetFileNames`
- **Parcel** - automático
- **Gulp** - `gulp-rev`

Essas ferramentas fazem tudo automaticamente!