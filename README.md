# SGQ OTI - Sistema de Gestão da Qualidade

Sistema de Gestão da Qualidade desenvolvido com HTML5, CSS3, Tailwind CSS, JavaScript e PHP 8.4 com MariaDB remoto (Hostinger).

## 🚀 Tecnologias

### Frontend
- **HTML5** - Estrutura semântica moderna
- **CSS3** - Estilos customizados e responsivos
- **Tailwind CSS** - Framework CSS utilitário
- **JavaScript ES6+** - Interatividade e funcionalidades dinâmicas

### Backend
- **PHP 8.4** - Linguagem servidor com PDO
- **MariaDB** - Banco de dados remoto (Hostinger)
- **APIs REST** - Comunicação frontend/backend
- **Sistema de sincronização inteligente** - Análise automática do banco

## 📋 Módulos do Sistema

O SGQ OTI possui **9 módulos principais**:

1. **🖨️ Controle de Toners** - Gerenciamento de estoque de toners
2. **✅ Homologações** - Controle de homologações de equipamentos
3. **🧪 Amostragens** - Gerenciamento de amostragens e testes
4. **🛡️ Garantias** - Controle de garantias de produtos
5. **📋 POPs e ITs** - Procedimentos Operacionais e Instruções de Trabalho
6. **📊 Fluxogramas** - Diagramas de processos e fluxos
7. **🔍 Auditorias** - Controle e acompanhamento de auditorias
8. **⚡ Dinâmicas** - Atividades dinâmicas e treinamentos
9. **⚙️ Configurações** - Configurações do sistema

## 🗂️ Estrutura do Projeto

```
SGQ/
├── index.html                 # Página principal
├── assets/
│   ├── css/
│   │   └── style.css         # Estilos customizados
│   └── js/
│       └── main.js           # JavaScript principal
├── backend/
│   ├── config/
│   │   ├── database.php      # Configuração do banco
│   │   └── cors.php          # Configuração CORS
│   └── api/
│       └── test-connection.php # Teste de conexão
└── README.md                 # Documentação
```

## 🔧 Configuração

### Banco de Dados (Hostinger)
- **Host:** srv2020.hstgr.io:3306
- **Banco:** u230868210_sgqoti
- **Usuário:** u230868210_dusouza
- **Senha:** Pandora@1989

### Tabelas Criadas Automaticamente
- `toners` - Cadastro completo de toners com estoque
- `movimentacoes_estoque` - Histórico de movimentações
- `usuarios` - Sistema de usuários com perfis (admin/user/viewer)

## 🚀 Como Usar

1. **Abrir o sistema:**
   - Abra o arquivo `index.html` no navegador
   - Ou configure um servidor web local

2. **Testar conexão:**
   - Acesse: `backend/api/test-connection.php`
   - Verifica conexão e cria tabelas automaticamente

3. **Navegar pelos módulos:**
   - Use o menu lateral para alternar entre módulos
   - Interface responsiva para desktop e mobile

## 📱 Responsividade

- **Desktop:** Menu lateral fixo de 320px
- **Tablet:** Menu lateral adaptativo
- **Mobile:** Menu lateral recolhível com botão hambúrguer

## 🎨 Interface

- **Design moderno** com Tailwind CSS
- **Tema azul** como cor primária
- **Ícones emoji** para identificação visual
- **Animações suaves** para melhor UX
- **Cards informativos** com gradientes

## 🔒 Segurança

- **PDO com prepared statements**
- **Validação de entrada**
- **Headers CORS configurados**
- **Tratamento de erros**

## 📈 Status Atual

- ✅ **Interface completa** - Menu lateral responsivo com 9 módulos
- ✅ **Conexão com banco** - MariaDB remoto (Hostinger) configurado
- ✅ **Sistema de configurações** - Abas para banco de dados e usuários
- ✅ **Modal de usuários** - Cadastro completo com validação
- ✅ **Sincronização inteligente** - Análise automática do esquema
- ✅ **APIs REST** - Comunicação segura frontend/backend
- 🔄 **Em desenvolvimento** - Funcionalidades específicas dos módulos

## 🆕 Últimas Atualizações

### v2.0.0 - 27/08/2025
- 🎯 **Modal de cadastro de usuários** com formulário completo
- 🔍 **Sistema de sincronização inteligente** que analisa o banco
- 🗄️ **Tabela de usuários** com perfis (admin/user/viewer)
- 🔧 **Correção do erro SQL** na conexão remota
- 📊 **Interface com abas** em Configurações
- 🔒 **Segurança aprimorada** - credenciais apenas no backend

## 🛠️ Próximos Passos

1. Implementar backend para cadastro de usuários
2. Sistema de autenticação e login
3. CRUD completo para Controle de Toners
4. Desenvolver funcionalidades dos demais módulos
5. Relatórios e dashboards

## 📞 Suporte

Sistema desenvolvido para SGQ OTI - Gestão da Qualidade.

---

**Versão:** 2.0.0  
**Data:** 27/08/2025  
**Desenvolvido com:** ❤️ e ☕
