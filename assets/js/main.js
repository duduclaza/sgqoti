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
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Configurações do Sistema</h3>
                        <p class="text-sm text-gray-600">Configure conexão com banco de dados e inicialize o sistema</p>
                    </div>
                </div>
                
                <!-- Status da Conexão -->
                <div id="connection-status" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-400 rounded-full mr-3 animate-pulse"></div>
                        <span class="text-yellow-800 font-medium">Verificando conexão...</span>
                    </div>
                </div>

                <!-- Configuração do Banco de Dados -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-4">Configuração do Banco de Dados</h4>
                    
                    <form id="db-config-form" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Host</label>
                                <input type="text" id="db-host" value="212.85.3.19" class="form-input" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Porta</label>
                                <input type="text" id="db-port" value="3306" class="form-input" readonly>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Banco</label>
                            <input type="text" id="db-name" value="u230868210_sgqoti" class="form-input" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Usuário</label>
                            <input type="text" id="db-user" value="u230868210_dusouza" class="form-input" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                            <input type="password" id="db-password" value="***********" class="form-input" readonly>
                        </div>
                        
                        <div class="flex gap-3 pt-4">
                            <button type="button" id="edit-config-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </span>
                            </button>
                            <button type="button" id="save-config-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors hidden">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Salvar
                                </span>
                            </button>
                            <button type="button" id="cancel-config-btn" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors hidden">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancelar
                                </span>
                            </button>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800 text-sm">
                                <strong>Informação:</strong> As credenciais estão pré-configuradas para o ambiente de produção.
                                Para alterar, edite o arquivo backend/config/database.php
                            </p>
                        </div>
                    </form>
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
                            <button id="test-connection-btn" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Testar Conexão
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h5 class="font-medium text-gray-800">Sincronizar Tabelas</h5>
                                <p class="text-sm text-gray-600">Criar ou atualizar estrutura das tabelas no banco</p>
                            </div>
                            <button id="sync-tables-btn" class="btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Sincronizar
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h5 class="font-medium text-gray-800">Verificar Sistema</h5>
                                <p class="text-sm text-gray-600">Executar diagnóstico completo do sistema</p>
                            </div>
                            <button id="system-check-btn" class="btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                Verificar
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
        
        // Event listeners para botões
        const testConnectionBtn = document.getElementById('test-connection-btn');
        const syncTablesBtn = document.getElementById('sync-tables-btn');
        const systemCheckBtn = document.getElementById('system-check-btn');
        const editConfigBtn = document.getElementById('edit-config-btn');
        const saveConfigBtn = document.getElementById('save-config-btn');
        const cancelConfigBtn = document.getElementById('cancel-config-btn');
        
        if (testConnectionBtn) {
            testConnectionBtn.addEventListener('click', () => this.testConnection());
        }
        
        if (syncTablesBtn) {
            syncTablesBtn.addEventListener('click', () => this.syncTables());
        }
        
        if (systemCheckBtn) {
            systemCheckBtn.addEventListener('click', () => this.performSystemCheck());
        }
        
        if (editConfigBtn) {
            editConfigBtn.addEventListener('click', () => this.enableConfigEdit());
        }
        
        if (saveConfigBtn) {
            saveConfigBtn.addEventListener('click', () => this.saveConfig());
        }
        
        if (cancelConfigBtn) {
            cancelConfigBtn.addEventListener('click', () => this.cancelConfigEdit());
        }
    }

    async checkConnectionStatus() {
        try {
            const response = await fetch('backend/api/config/database-config.php');
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
        } catch (error) {
            console.error('Erro ao verificar status da conexão:', error);
            this.addToLog('Erro ao verificar status da conexão: ' + error.message, 'error');
        }
    }

    async testConnection() {
        this.addToLog('Testando conexão com banco de dados...', 'info');
        
        const button = document.getElementById('test-connection-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<div class="w-4 h-4 animate-spin rounded-full border-2 border-gray-300 border-t-blue-600"></div> Testando...';
        button.disabled = true;
        
        try {
            const response = await fetch('backend/api/config/database-config.php', {
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
            const response = await fetch('backend/api/config/database-config.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'sync_tables' })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.addToLog('✅ Tabelas sincronizadas com sucesso!', 'success');
                result.data.tables_created.forEach(table => {
                    this.addToLog(`📋 Tabela criada/atualizada: ${table}`, 'info');
                });
                this.checkConnectionStatus(); // Atualizar status visual
            } else {
                this.addToLog('❌ Erro ao sincronizar tabelas: ' + result.message, 'error');
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
            const response = await fetch('backend/api/config/database-config.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action: 'system_check' })
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
