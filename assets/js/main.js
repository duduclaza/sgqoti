/**
 * SGQ OTI - Sistema de Gestão da Qualidade
 * JavaScript Principal
 */

class SGQApp {
    constructor() {
        this.currentModule = 'toners';
        this.modules = {
            'toners': {
                title: 'Controle de Toners',
                subtitle: 'Gerencie o cadastro de toners e cartuchos',
                icon: '🖨️'
            },
            'homologacoes': {
                title: 'Homologações',
                subtitle: 'Controle de homologações de equipamentos',
                icon: '✅'
            },
            'amostragens': {
                title: 'Amostragens',
                subtitle: 'Gerenciamento de amostragens e testes',
                icon: '🧪'
            },
            'garantias': {
                title: 'Garantias',
                subtitle: 'Controle de garantias de produtos',
                icon: '🛡️'
            },
            'pops-its': {
                title: 'POPs e ITs',
                subtitle: 'Procedimentos Operacionais e Instruções de Trabalho',
                icon: '📋'
            },
            'fluxogramas': {
                title: 'Fluxogramas',
                subtitle: 'Diagramas de processos e fluxos',
                icon: '📊'
            },
            'auditorias': {
                title: 'Auditorias',
                subtitle: 'Controle e acompanhamento de auditorias',
                icon: '🔍'
            },
            'dinamicas': {
                title: 'Dinâmicas',
                subtitle: 'Atividades dinâmicas e treinamentos',
                icon: '⚡'
            },
            'configuracoes': {
                title: 'Configurações',
                subtitle: 'Configurações do sistema',
                icon: '⚙️'
            }
        };
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateDateTime();
        this.loadModule(this.currentModule);
        
        // Atualizar data/hora a cada minuto
        setInterval(() => this.updateDateTime(), 60000);
    }

    setupEventListeners() {
        // Menu lateral
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const module = link.getAttribute('data-module');
                this.switchModule(module);
            });
        });

        // Responsividade mobile
        this.setupMobileMenu();
    }

    setupMobileMenu() {
        // Adicionar botão de menu mobile se necessário
        if (window.innerWidth <= 768) {
            this.createMobileMenuButton();
        }

        // Listener para redimensionamento
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                this.createMobileMenuButton();
            } else {
                this.removeMobileMenuButton();
            }
        });
    }

    createMobileMenuButton() {
        if (document.getElementById('mobile-menu-btn')) return;

        const button = document.createElement('button');
        button.id = 'mobile-menu-btn';
        button.className = 'fixed top-4 left-4 z-50 bg-primary-600 text-white p-2 rounded-lg shadow-lg md:hidden';
        button.innerHTML = '☰';
        
        button.addEventListener('click', () => {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('mobile-open');
        });

        document.body.appendChild(button);
    }

    removeMobileMenuButton() {
        const button = document.getElementById('mobile-menu-btn');
        if (button) {
            button.remove();
        }
    }

    switchModule(moduleId) {
        if (!this.modules[moduleId]) return;

        // Atualizar estado atual
        this.currentModule = moduleId;

        // Atualizar menu ativo
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.classList.remove('active');
        });
        
        const activeLink = document.querySelector(`[data-module="${moduleId}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }

        // Carregar módulo
        this.loadModule(moduleId);

        // Fechar menu mobile se aberto
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.remove('mobile-open');
        }
    }

    loadModule(moduleId) {
        const module = this.modules[moduleId];
        if (!module) return;

        // Atualizar header
        document.getElementById('page-title').textContent = module.title;
        document.getElementById('page-subtitle').textContent = module.subtitle;

        // Carregar conteúdo do módulo
        this.loadModuleContent(moduleId);
    }

    loadModuleContent(moduleId) {
        const contentArea = document.getElementById('content-area');
        
        // Adicionar animação de loading
        contentArea.classList.add('loading');
        
        setTimeout(() => {
            contentArea.classList.remove('loading');
            contentArea.innerHTML = this.getModuleContent(moduleId);
            contentArea.classList.add('fade-in');
            
            // Configurar event listeners específicos do módulo
            if (moduleId === 'configuracoes') {
                this.setupConfigurationModule();
            }
            
            // Remover classe de animação após completar
            setTimeout(() => {
                contentArea.classList.remove('fade-in');
            }, 300);
        }, 200);
    }

    getModuleContent(moduleId) {
        const module = this.modules[moduleId];
        
        switch (moduleId) {
            case 'toners':
                return this.getTonerContent();
            case 'homologacoes':
                return this.getHomologacaoContent();
            case 'amostragens':
                return this.getAmostragemContent();
            case 'garantias':
                return this.getGarantiaContent();
            case 'pops-its':
                return this.getPopsItsContent();
            case 'fluxogramas':
                return this.getFluxogramaContent();
            case 'auditorias':
                return this.getAuditoriaContent();
            case 'dinamicas':
                return this.getDinamicaContent();
            case 'configuracoes':
                return this.getConfiguracoesContent();
            default:
                return this.getDefaultContent(module);
        }
    }

    getTonerContent() {
        return `
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Controle de Toners</h3>
                        <p class="text-sm text-gray-600">Gerencie o estoque e cadastro de toners</p>
                    </div>
                    <button class="btn-primary">
                        <span>➕</span>
                        Novo Toner
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100">Total de Toners</p>
                                <p class="text-2xl font-bold">0</p>
                            </div>
                            <div class="text-3xl opacity-80">🖨️</div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100">Em Estoque</p>
                                <p class="text-2xl font-bold">0</p>
                            </div>
                            <div class="text-3xl opacity-80">📦</div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-red-100">Baixo Estoque</p>
                                <p class="text-2xl font-bold">0</p>
                            </div>
                            <div class="text-3xl opacity-80">⚠️</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-800">Lista de Toners</h4>
                    </div>
                    <div class="p-4">
                        <div class="text-center py-8 text-gray-500">
                            <div class="text-4xl mb-2">📋</div>
                            <p>Nenhum toner cadastrado ainda</p>
                            <p class="text-sm">Clique em "Novo Toner" para começar</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    getDefaultContent(module) {
        const iconMap = {
            'homologacoes': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
            'amostragens': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>',
            'garantias': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
            'pops-its': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>',
            'fluxogramas': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>',
            'auditorias': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>',
            'dinamicas': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>',
            'configuracoes': '<svg class="w-12 h-12 text-totvs-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
        };

        const moduleKey = module.title.toLowerCase().includes('toners') ? 'toners' : 
                         module.title.toLowerCase().includes('homolog') ? 'homologacoes' :
                         module.title.toLowerCase().includes('amostra') ? 'amostragens' :
                         module.title.toLowerCase().includes('garantia') ? 'garantias' :
                         module.title.toLowerCase().includes('pops') ? 'pops-its' :
                         module.title.toLowerCase().includes('fluxo') ? 'fluxogramas' :
                         module.title.toLowerCase().includes('audit') ? 'auditorias' :
                         module.title.toLowerCase().includes('dinâm') ? 'dinamicas' :
                         'configuracoes';

        const icon = iconMap[moduleKey] || iconMap['configuracoes'];

        return `
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    ${icon}
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">${module.title}</h3>
                <p class="text-gray-600 mb-6">${module.subtitle}</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 max-w-md mx-auto">
                    <p class="text-blue-800 text-sm">
                        <strong>Em Desenvolvimento</strong><br>
                        Este módulo será implementado em breve.
                    </p>
                </div>
            </div>
        `;
    }

    // Métodos para outros módulos (placeholder)
    getHomologacaoContent() { 
        return this.getDefaultContent(this.modules.homologacoes); 
    }
    
    getAmostragemContent() { 
        return this.getDefaultContent(this.modules.amostragens); 
    }
    
    getGarantiaContent() { 
        return this.getDefaultContent(this.modules.garantias); 
    }
    
    getPopsItsContent() { 
        return this.getDefaultContent(this.modules['pops-its']); 
    }
    
    getFluxogramaContent() { 
        return this.getDefaultContent(this.modules.fluxogramas); 
    }
    
    getAuditoriaContent() { 
        return this.getDefaultContent(this.modules.auditorias); 
    }
    
    getDinamicaContent() { 
        return this.getDefaultContent(this.modules.dinamicas); 
    }
    
    getConfiguracoesContent() { 
        return `
            <div class="space-y-6">
                <!-- Abas de Navegação -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button class="config-tab active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600" data-tab="database">
                            Banco de Dados
                        </button>
                        <button class="config-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="users">
                            Cadastro de Usuários
                        </button>
                    </nav>
                </div>

                <!-- Conteúdo da Aba Banco de Dados -->
                <div id="database-tab" class="config-tab-content">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Configurações do Banco de Dados</h3>
                            <p class="text-sm text-gray-600">Teste conexão e sincronize tabelas do sistema</p>
                        </div>
                    </div>
                    
                    <!-- Status da Conexão -->
                    <div id="connection-status" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-400 rounded-full mr-3 animate-pulse"></div>
                            <span class="text-yellow-800 font-medium">Verificando conexão...</span>
                        </div>
                    </div>

                    <!-- Ações do Sistema -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-4">Ações do Sistema</h4>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h5 class="font-medium text-gray-800">Testar Conexão</h5>
                                    <p class="text-sm text-gray-600">Verificar se a conexão com o banco está funcionando</p>
                                </div>
                                <button id="test-connection-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Testar
                                    </span>
                                </button>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h5 class="font-medium text-gray-800">Sincronizar Tabelas</h5>
                                    <p class="text-sm text-gray-600">Criar/atualizar estrutura das tabelas no banco</p>
                                </div>
                                <button id="sync-tables-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Sincronizar
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Log de Atividades -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-4">Log de Atividades</h4>
                        <div id="activity-log" class="bg-gray-50 rounded-lg p-4 h-32 overflow-y-auto text-sm font-mono">
                            <div class="text-gray-600">Sistema iniciado. Aguardando comandos...</div>
                        </div>
                    </div>
                </div>

                <!-- Conteúdo da Aba Cadastro de Usuários -->
                <div id="users-tab" class="config-tab-content hidden">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Cadastro de Usuários</h3>
                            <p class="text-sm text-gray-600">Gerencie usuários do sistema SGQ OTI</p>
                        </div>
                        <button id="add-user-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Novo Usuário
                            </span>
                        </button>
                    </div>

                    <!-- Modal de Cadastro de Usuário -->
                    <div id="user-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
                        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-semibold text-gray-800">Cadastro de Usuário</h4>
                                <button id="close-modal-btn" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <form id="user-registration-form" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                                        <input type="text" id="user-name" class="form-input" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" id="user-email" class="form-input" required>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Usuário</label>
                                        <input type="text" id="user-username" class="form-input" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                                        <input type="password" id="user-password" class="form-input" required>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Perfil</label>
                                        <select id="user-role" class="form-input" required>
                                            <option value="">Selecione um perfil</option>
                                            <option value="admin">Administrador</option>
                                            <option value="user">Usuário</option>
                                            <option value="viewer">Visualizador</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                        <select id="user-status" class="form-input" required>
                                            <option value="active">Ativo</option>
                                            <option value="inactive">Inativo</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="flex gap-3 pt-6 border-t">
                                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Salvar Usuário
                                    </button>
                                    <button type="button" id="cancel-user-btn" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Usuários -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-4">Usuários Cadastrados</h4>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perfil</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="users-table-body" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum usuário cadastrado
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    updateDateTime() {
        const now = new Date();
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        
        const dateElement = document.getElementById('current-date');
        if (dateElement) {
            dateElement.textContent = now.toLocaleDateString('pt-BR', options);
        }
    }

    // Utilitários
    showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-sm`;
        alertDiv.textContent = message;
        
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    showLoading(element) {
        element.classList.add('loading');
    }

    hideLoading(element) {
        element.classList.remove('loading');
    }

    // Métodos específicos do módulo de configurações
    setupConfigurationModule() {
        // Verificar conexão automaticamente ao carregar
        this.checkConnectionStatus();
        
        // Event listeners para abas
        const configTabs = document.querySelectorAll('.config-tab');
        configTabs.forEach(tab => {
            tab.addEventListener('click', (e) => this.switchConfigTab(e.target.dataset.tab));
        });
        
        // Event listeners para botões do banco de dados
        const testConnectionBtn = document.getElementById('test-connection-btn');
        const syncTablesBtn = document.getElementById('sync-tables-btn');
        
        if (testConnectionBtn) {
            testConnectionBtn.addEventListener('click', () => this.testConnection());
        }
        
        if (syncTablesBtn) {
            syncTablesBtn.addEventListener('click', () => this.syncTables());
        }
        
        // Event listeners para cadastro de usuários
        const addUserBtn = document.getElementById('add-user-btn');
        const cancelUserBtn = document.getElementById('cancel-user-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const userForm = document.getElementById('user-registration-form');
        const userModal = document.getElementById('user-modal');
        
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => this.showUserModal());
        }
        
        if (cancelUserBtn) {
            cancelUserBtn.addEventListener('click', () => this.hideUserModal());
        }
        
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => this.hideUserModal());
        }
        
        if (userModal) {
            userModal.addEventListener('click', (e) => {
                if (e.target === userModal) {
                    this.hideUserModal();
                }
            });
        }
        
        if (userForm) {
            userForm.addEventListener('submit', (e) => this.saveUser(e));
        }
        
        // Carregar usuários existentes ao abrir a aba
        this.loadExistingUsers();
    }

    switchConfigTab(tabName) {
        // Remover classe active de todas as abas
        const tabs = document.querySelectorAll('.config-tab');
        tabs.forEach(tab => {
            tab.classList.remove('active', 'border-blue-500', 'text-blue-600');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Adicionar classe active na aba clicada
        const activeTab = document.querySelector(`[data-tab="${tabName}"]`);
        if (activeTab) {
            activeTab.classList.add('active', 'border-blue-500', 'text-blue-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500');
        }
        
        // Esconder todos os conteúdos
        const contents = document.querySelectorAll('.config-tab-content');
        contents.forEach(content => {
            content.classList.add('hidden');
        });
        
        // Mostrar conteúdo da aba ativa
        const activeContent = document.getElementById(`${tabName}-tab`);
        if (activeContent) {
            activeContent.classList.remove('hidden');
            
            // Se for a aba de usuários, carregar dados
            if (tabName === 'users') {
                this.loadExistingUsers();
            }
        }
    }

    showUserModal() {
        const userModal = document.getElementById('user-modal');
        if (userModal) {
            userModal.classList.remove('hidden');
            // Focar no primeiro campo
            setTimeout(() => {
                const firstInput = document.getElementById('user-name');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 100);
        }
    }

    hideUserModal() {
        const userModal = document.getElementById('user-modal');
        const form = document.getElementById('user-registration-form');
        
        if (userModal) {
            userModal.classList.add('hidden');
        }
        
        if (form) {
            form.reset();
        }
    }

    async saveUser(event) {
        event.preventDefault();
        
        const formData = {
            action: 'create_user',
            name: document.getElementById('user-name').value,
            email: document.getElementById('user-email').value,
            username: document.getElementById('user-username').value,
            password: document.getElementById('user-password').value,
            role: document.getElementById('user-role').value,
            status: document.getElementById('user-status').value
        };
        
        // Debug: Log dos dados enviados
        console.log('Dados enviados:', formData);
        this.addToLog('🔄 Enviando dados: ' + JSON.stringify(formData), 'info');
        
        const apiUrl = 'backend/api/users.php';
            
        console.log('URL da API:', apiUrl);
        console.log('Hostname detectado:', window.location.hostname);
        this.addToLog('🌐 URL da API: ' + apiUrl, 'info');
        this.addToLog('🖥️ Hostname: ' + window.location.hostname, 'info');
        
        try {
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            // Debug: Log da resposta HTTP
            console.log('Status da resposta:', response.status);
            this.addToLog('📡 Status HTTP: ' + response.status, 'info');
            
            const responseText = await response.text();
            console.log('Resposta bruta:', responseText);
            this.addToLog('📄 Resposta: ' + responseText.substring(0, 200), 'info');
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (parseError) {
                throw new Error('Resposta não é JSON válido: ' + responseText);
            }
            
            if (result.success) {
                // NÃO adicionar à tabela localmente - apenas recarregar do banco
                this.hideUserModal();
                this.showAlert('Usuário cadastrado com sucesso!', 'success');
                this.addToLog('✅ Usuário cadastrado: ' + result.data.name, 'success');
                
                // Limpar formulário
                document.getElementById('user-registration-form').reset();
                
                // Recarregar lista do banco para garantir sincronização
                setTimeout(() => {
                    this.loadExistingUsers();
                }, 500);
            } else {
                this.showAlert('Erro ao cadastrar usuário: ' + result.message, 'error');
                this.addToLog('❌ Erro no cadastro: ' + result.message, 'error');
            }
            
        } catch (error) {
            console.error('Erro completo:', error);
            this.showAlert('Erro de conexão: ' + error.message, 'error');
            this.addToLog('❌ Erro de conexão: ' + error.message, 'error');
        }
    }

    addUserToTable(userData) {
        const tableBody = document.getElementById('users-table-body');
        if (!tableBody) return;
        
        // Remover mensagem de "nenhum usuário"
        if (tableBody.children.length === 1 && tableBody.children[0].children.length === 1) {
            tableBody.innerHTML = '';
        }
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${userData.name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${userData.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${userData.username}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${userData.role === 'admin' ? 'bg-red-100 text-red-800' : userData.role === 'user' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'}">
                    ${userData.role === 'admin' ? 'Administrador' : userData.role === 'user' ? 'Usuário' : 'Visualizador'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${userData.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${userData.status === 'active' ? 'Ativo' : 'Inativo'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
                <button class="text-red-600 hover:text-red-900">Excluir</button>
            </td>
        `;
        
        tableBody.appendChild(row);
    }

    async loadExistingUsers() {
        try {
            const apiUrl = 'backend/api/users.php';
                
            const response = await fetch(apiUrl, {
                method: 'GET'
            });
            
            const result = await response.json();
            
            if (result.success && result.data && result.data.length > 0) {
                const tableBody = document.getElementById('users-table-body');
                if (tableBody) {
                    tableBody.innerHTML = ''; // Limpar tabela
                    
                    result.data.forEach(user => {
                        this.addUserToTable({
                            id: user.id,
                            name: user.nome,
                            email: user.email,
                            username: user.usuario,
                            role: user.perfil,
                            status: user.status
                        });
                    });
                    
                    this.addToLog(`✅ ${result.data.length} usuários carregados`, 'success');
                }
            }
        } catch (error) {
            console.log('Nenhum usuário encontrado ou erro na conexão:', error.message);
        }
    }

    async checkConnectionStatus() {
        try {
            const apiUrl = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
                ? 'backend/api/config/database-config.php' 
                : 'https://lightseagreen-cobra-261680.hostingersite.com/backend/api/config/database-config.php';
                
            const response = await fetch(apiUrl);
            const result = await response.json();
            
            const statusElement = document.getElementById('connection-status');
            if (statusElement) {
                if (result.success && result.data.connection_status) {
                    statusElement.innerHTML = `
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                            <span class="text-green-800 font-medium">Conexão ativa - ${result.data.server_time}</span>
                        </div>
                    `;
                    statusElement.className = 'bg-green-50 border border-green-200 rounded-lg p-4';
                } else {
                    statusElement.innerHTML = `
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-400 rounded-full mr-3"></div>
                            <span class="text-red-800 font-medium">Conexão falhou</span>
                        </div>
                    `;
                    statusElement.className = 'bg-red-50 border border-red-200 rounded-lg p-4';
                }
            }
            
            // Carregar configurações nos campos
            this.loadConfigFromBackend();
            
        } catch (error) {
            console.error('Erro ao verificar status da conexão:', error);
            this.addToLog('Erro ao verificar status da conexão: ' + error.message, 'error');
        }
    }

    async loadConfigFromBackend() {
        try {
            const apiUrl = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
                ? 'backend/api/config/database-config.php' 
                : 'https://lightseagreen-cobra-261680.hostingersite.com/backend/api/config/database-config.php';
                
            const response = await fetch(apiUrl);
            const result = await response.json();
            
            if (result.success && result.data) {
                const data = result.data;
                
                // Preencher campos com dados do backend
                const hostField = document.getElementById('db-host');
                const portField = document.getElementById('db-port');
                const nameField = document.getElementById('db-name');
                const userField = document.getElementById('db-user');
                
                if (hostField) hostField.value = data.host || '';
                if (portField) portField.value = data.port || '';
                if (nameField) nameField.value = data.database || '';
                if (userField) userField.value = data.username || '';
                
                this.addToLog('✅ Configurações carregadas do backend', 'success');
            }
        } catch (error) {
            console.error('Erro ao carregar configurações:', error);
            this.addToLog('❌ Erro ao carregar configurações: ' + error.message, 'error');
        }
    }

    async testConnection() {
        this.addToLog('Testando conexão com banco de dados...', 'info');
        
        const button = document.getElementById('test-connection-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<div class="w-4 h-4 animate-spin rounded-full border-2 border-gray-300 border-t-blue-600"></div> Testando...';
        button.disabled = true;
        
        try {
            const apiUrl = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
                ? 'backend/api/config/database-config.php' 
                : 'https://lightseagreen-cobra-261680.hostingersite.com/backend/api/config/database-config.php';
                
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'test_connection' })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.addToLog('✅ Conexão estabelecida com sucesso!', 'success');
                this.addToLog(`Servidor: ${result.data.server_time}`, 'info');
                this.checkConnectionStatus(); // Atualizar status visual
            } else {
                this.addToLog('❌ Falha na conexão: ' + result.message, 'error');
            }
            
        } catch (error) {
            this.addToLog('❌ Erro ao testar conexão: ' + error.message, 'error');
        } finally {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }

    async syncTables() {
        this.addToLog('Sincronizando tabelas do banco de dados...', 'info');
        const button = document.getElementById('sync-tables-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<div class="w-4 h-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div> Sincronizando...';
        button.disabled = true;
        
        try {
            const apiUrl = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
                ? 'backend/api/config/database-config.php' 
                : 'https://lightseagreen-cobra-261680.hostingersite.com/backend/api/config/database-config.php';
                
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'sync_tables' })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.addToLog('✅ Tabelas sincronizadas com sucesso!', 'success');
                
                // Verificar se há detalhes para mostrar
                if (result.data && result.data.details && Array.isArray(result.data.details)) {
                    result.data.details.forEach(detail => {
                        this.addToLog(`📋 ${detail}`, 'info');
                    });
                } else if (result.details && Array.isArray(result.details)) {
                    result.details.forEach(detail => {
                        this.addToLog(`📋 ${detail}`, 'info');
                    });
                }
                
                // Mostrar número de mudanças aplicadas
                const changesApplied = result.data?.changes_applied || result.changes_applied || 0;
                if (changesApplied > 0) {
                    this.addToLog(`🔧 ${changesApplied} alterações aplicadas`, 'success');
                }
                
                this.checkConnectionStatus(); // Atualizar status visual
            } else {
                this.addToLog('❌ Erro ao sincronizar tabelas: ' + result.message, 'error');
                
                // Mostrar detalhes do erro se disponível
                if (result.details && Array.isArray(result.details)) {
                    result.details.forEach(detail => {
                        this.addToLog(`📋 ${detail}`, 'error');
                    });
                }
            }
            
        } catch (error) {
            this.addToLog('❌ Erro ao sincronizar tabelas: ' + error.message, 'error');
        } finally {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }

    async performSystemCheck() {
        this.addToLog('Executando verificação completa do sistema...', 'info');
        
        const button = document.getElementById('system-check-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<div class="w-4 h-4 animate-spin rounded-full border-2 border-gray-300 border-t-blue-600"></div> Verificando...';
        button.disabled = true;
        
        try {
            const apiUrl = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' 
                ? 'backend/api/config/database-config.php' 
                : 'https://lightseagreen-cobra-261680.hostingersite.com/backend/api/config/database-config.php';
                
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'check_system' })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.addToLog('✅ Sistema funcionando corretamente!', 'success');
            } else {
                this.addToLog('⚠️ Alguns problemas foram detectados', 'warning');
            }
            
            // Mostrar detalhes da verificação
            const checks = result.data;
            this.addToLog(`🔌 Conexão BD: ${checks.database_connection ? '✅' : '❌'}`, 'info');
            this.addToLog(`🐘 PHP ${checks.php_version}`, 'info');
            
            Object.entries(checks.php_extensions).forEach(([ext, status]) => {
                this.addToLog(`📦 ${ext}: ${status ? '✅' : '❌'}`, 'info');
            });
            
            if (checks.tables) {
                Object.entries(checks.tables).forEach(([table, exists]) => {
                    this.addToLog(`📋 Tabela ${table}: ${exists ? '✅' : '❌'}`, 'info');
                });
            }
            
        } catch (error) {
            this.addToLog('❌ Erro ao verificar sistema: ' + error.message, 'error');
        } finally {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }

    addToLog(message, type = 'info') {
        const logElement = document.getElementById('activity-log');
        if (!logElement) return;
        
        const timestamp = new Date().toLocaleTimeString('pt-BR');
        const logEntry = document.createElement('div');
        
        const colors = {
            'info': 'text-gray-600',
            'success': 'text-green-600',
            'error': 'text-red-600',
            'warning': 'text-yellow-600'
        };
        
        logEntry.className = colors[type] || colors['info'];
        logEntry.innerHTML = `[${timestamp}] ${message}`;
        
        logElement.appendChild(logEntry);
        logElement.scrollTop = logElement.scrollHeight;
    }

    // Métodos para edição de configurações
    enableConfigEdit() {
        const fields = ['db-host', 'db-port', 'db-name', 'db-user', 'db-password'];
        const editBtn = document.getElementById('edit-config-btn');
        const saveBtn = document.getElementById('save-config-btn');
        const cancelBtn = document.getElementById('cancel-config-btn');
        
        // Salvar valores originais
        this.originalConfig = {};
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                this.originalConfig[fieldId] = field.value;
                field.removeAttribute('readonly');
                field.classList.add('border-blue-300', 'focus:border-blue-500');
                
                // Mostrar senha real para edição
                if (fieldId === 'db-password') {
                    field.value = 'Pandor@1989'; // Valor real
                }
            }
        });
        
        // Alternar botões
        editBtn.classList.add('hidden');
        saveBtn.classList.remove('hidden');
        cancelBtn.classList.remove('hidden');
        
        this.addToLog('Modo de edição ativado', 'info');
    }

    cancelConfigEdit() {
        const fields = ['db-host', 'db-port', 'db-name', 'db-user', 'db-password'];
        const editBtn = document.getElementById('edit-config-btn');
        const saveBtn = document.getElementById('save-config-btn');
        const cancelBtn = document.getElementById('cancel-config-btn');
        
        // Restaurar valores originais
        fields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field && this.originalConfig) {
                field.value = this.originalConfig[fieldId];
                field.setAttribute('readonly', true);
                field.classList.remove('border-blue-300', 'focus:border-blue-500');
                
                // Mascarar senha novamente
                if (fieldId === 'db-password') {
                    field.value = '***********';
                }
            }
        });
        
        // Alternar botões
        editBtn.classList.remove('hidden');
        saveBtn.classList.add('hidden');
        cancelBtn.classList.add('hidden');
        
        this.addToLog('Edição cancelada', 'info');
    }

    async saveConfig() {
        const configData = {
            host: document.getElementById('db-host').value,
            port: document.getElementById('db-port').value,
            database: document.getElementById('db-name').value,
            username: document.getElementById('db-user').value,
            password: document.getElementById('db-password').value
        };
        
        // Validar campos obrigatórios
        if (!configData.host || !configData.database || !configData.username || !configData.password) {
            this.addToLog('❌ Todos os campos são obrigatórios', 'error');
            return;
        }
        
        this.addToLog('Salvando configurações do banco de dados...', 'info');
        
        const button = document.getElementById('save-config-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<div class="w-4 h-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div> Salvando...';
        button.disabled = true;
        
        try {
            const response = await fetch('backend/api/config/database-config.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    action: 'save_config',
                    config: configData
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.addToLog('✅ Configurações salvas com sucesso!', 'success');
                
                // Desabilitar modo de edição
                this.cancelConfigEdit();
                
                // Testar nova conexão
                setTimeout(() => {
                    this.checkConnectionStatus();
                }, 1000);
                
            } else {
                this.addToLog('❌ Erro ao salvar: ' + result.message, 'error');
            }
            
        } catch (error) {
            this.addToLog('❌ Erro ao salvar configurações: ' + error.message, 'error');
        } finally {
            button.innerHTML = originalText;
            button.disabled = false;
        }
    }
}

// Inicializar aplicação quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.sgqApp = new SGQApp();
});

// Exportar para uso global
window.SGQApp = SGQApp;
