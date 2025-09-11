<?php
session_start();
require_once '../../config/environment.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../index.php');
    exit();
}

$active_tab = $_GET['tab'] ?? 'filiais';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGQ OTI - Configurações</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?php echo Environment::url('config/app.js'); ?>"></script>
    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'sap-blue': '#0070f3',
                        'sap-dark-blue': '#003d82',
                        'sap-light-blue': '#e6f3ff',
                        'sap-gray': '#f5f5f5',
                        'sap-dark-gray': '#666666',
                        'sap-green': '#107e3e',
                        'sap-orange': '#ff6600'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-sap-gray min-h-screen">
    <div class="container mx-auto p-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-sap-dark-blue mb-2">Configurações</h1>
            <p class="text-gray-600">Gerencie as configurações do sistema SGQ OTI</p>
            <a href="../../dashboard.php" class="inline-flex items-center mt-4 text-sap-blue hover:text-sap-dark-blue">
                ← Voltar ao Dashboard
            </a>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <a href="?tab=filiais" 
                       class="<?php echo $active_tab === 'filiais' ? 'border-sap-blue text-sap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Cadastro de Filiais
                    </a>
                    <a href="?tab=departamentos" 
                       class="<?php echo $active_tab === 'departamentos' ? 'border-sap-blue text-sap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Cadastro de Departamentos
                    </a>
                    <a href="?tab=fornecedores" 
                       class="<?php echo $active_tab === 'fornecedores' ? 'border-sap-blue text-sap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Cadastro de Fornecedores
                    </a>
                    <a href="?tab=parametros" 
                       class="<?php echo $active_tab === 'parametros' ? 'border-sap-blue text-sap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Parâmetros de Retornados
                    </a>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <?php if ($active_tab === 'filiais'): ?>
                    <!-- Cadastro de Filiais -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">Cadastro de Filiais</h2>
                            <button class="bg-sap-blue hover:bg-sap-dark-blue text-white px-4 py-2 rounded-lg">
                                + Nova Filial
                            </button>
                        </div>
                        
                        <!-- Form -->
                        <form id="form-filial" class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Filial</label>
                                    <input type="text" name="nome" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent" placeholder="Digite o nome da filial">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="bg-sap-green hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                                        Salvar
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Lista de Filiais -->
                        <div class="bg-white border rounded-lg">
                            <div class="px-4 py-3 border-b bg-gray-50">
                                <h3 class="text-lg font-medium">Filiais Cadastradas</h3>
                            </div>
                            <div id="filiais-list" class="divide-y">
                                <!-- Carregado dinamicamente via JavaScript -->
                            </div>
                        </div>
                    </div>

                <?php elseif ($active_tab === 'departamentos'): ?>
                    <!-- Cadastro de Departamentos -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">Cadastro de Departamentos</h2>
                            <button class="bg-sap-blue hover:bg-sap-dark-blue text-white px-4 py-2 rounded-lg">
                                + Novo Departamento
                            </button>
                        </div>
                        
                        <!-- Form -->
                        <form id="form-departamento" class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Departamento</label>
                                    <input type="text" name="nome" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent" placeholder="Digite o nome do departamento">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="bg-sap-green hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                                        Salvar
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Lista de Departamentos -->
                        <div class="bg-white border rounded-lg">
                            <div class="px-4 py-3 border-b bg-gray-50">
                                <h3 class="text-lg font-medium">Departamentos Cadastrados</h3>
                            </div>
                            <div id="departamentos-list" class="divide-y max-h-96 overflow-y-auto scrollbar-thin">
                                <!-- Carregado dinamicamente via JavaScript -->
                            </div>
                        </div>
                    </div>

                <?php elseif ($active_tab === 'fornecedores'): ?>
                    <!-- Cadastro de Fornecedores -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">Cadastro de Fornecedores</h2>
                            <button class="bg-sap-blue hover:bg-sap-dark-blue text-white px-4 py-2 rounded-lg">
                                + Novo Fornecedor
                            </button>
                        </div>
                        
                        <!-- Form -->
                        <form id="form-fornecedor" class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Fornecedor *</label>
                                    <input type="text" name="nome" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent" placeholder="Nome do fornecedor">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Contato</label>
                                    <input type="text" name="contato" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent" placeholder="Telefone/Email">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">RMA (Link/Email/Telefone)</label>
                                    <input type="text" name="rma" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent" placeholder="RMA">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="bg-sap-green hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                                    Salvar Fornecedor
                                </button>
                            </div>
                        </form>

                        <!-- Lista de Fornecedores -->
                        <div class="bg-white border rounded-lg">
                            <div class="px-4 py-3 border-b bg-gray-50">
                                <h3 class="text-lg font-medium">Fornecedores Cadastrados</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contato</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RMA</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fornecedores-list" class="bg-white divide-y divide-gray-200">
                                        <!-- Carregado dinamicamente via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php elseif ($active_tab === 'parametros'): ?>
                    <!-- Parâmetros de Retornados -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold text-gray-900">Parâmetros de Retornados</h2>
                            <button class="bg-sap-blue hover:bg-sap-dark-blue text-white px-4 py-2 rounded-lg">
                                + Novo Parâmetro
                            </button>
                        </div>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Importante:</strong> Estes parâmetros são fundamentais para a lógica dos retornados funcionar corretamente.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Parâmetros Padrão -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Destino Descarte -->
                            <div class="bg-white border rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                                    <h3 class="text-lg font-semibold text-gray-900">Destino Descarte</h3>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Percentual:</span>
                                        <span class="font-medium">≤ 5%</span>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Orientação:</strong> Descarte o Toner.
                                    </div>
                                    <button class="text-sap-blue hover:text-sap-dark-blue text-sm">Editar</button>
                                </div>
                            </div>

                            <!-- Uso Interno -->
                            <div class="bg-white border rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                                    <h3 class="text-lg font-semibold text-gray-900">Uso Interno</h3>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Percentual:</span>
                                        <span class="font-medium">6% - 39%</span>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Orientação:</strong> Teste o Toner. Se a qualidade estiver boa, utilize internamente para testes. Se estiver ruim, descarte.
                                    </div>
                                    <button class="text-sap-blue hover:text-sap-dark-blue text-sm">Editar</button>
                                </div>
                            </div>

                            <!-- Estoque Semi Novo -->
                            <div class="bg-white border rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                                    <h3 class="text-lg font-semibold text-gray-900">Estoque Semi Novo</h3>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Percentual:</span>
                                        <span class="font-medium">40% - 89%</span>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Orientação:</strong> Teste o Toner. Se a qualidade estiver boa, envie para o estoque como seminovo e marque a % na caixa para a logística ver. Se estiver ruim, solicite garantia.
                                    </div>
                                    <button class="text-sap-blue hover:text-sap-dark-blue text-sm">Editar</button>
                                </div>
                            </div>

                            <!-- Estoque Novo -->
                            <div class="bg-white border rounded-lg p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                    <h3 class="text-lg font-semibold text-gray-900">Estoque Novo</h3>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Percentual:</span>
                                        <span class="font-medium">≥ 90%</span>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Orientação:</strong> Teste o Toner. Se a qualidade estiver boa, envie para o estoque como novo e marque na caixa que é novo para a logística ver. Se estiver ruim, solicite garantia.
                                    </div>
                                    <button class="text-sap-blue hover:text-sap-dark-blue text-sm">Editar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Success/Error Messages -->
    <div id="message-container" class="fixed top-4 right-4 z-50"></div>

    <!-- Modal para Edição -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="modal-title" class="text-lg font-medium text-gray-900">Editar Item</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="sr-only">Fechar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="edit-form">
                    <div id="modal-content">
                        <!-- Conteúdo dinâmico do modal -->
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancelar
                        </button>
                        <button type="submit" class="px-4 py-2 bg-sap-blue text-white rounded-lg hover:bg-sap-dark-blue">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Função para mostrar mensagens
        function showMessage(message, type = 'success') {
            const container = document.getElementById('message-container');
            const messageDiv = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            messageDiv.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg mb-2 transform transition-all duration-300 translate-x-full`;
            messageDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        ×
                    </button>
                </div>
            `;
            
            container.appendChild(messageDiv);
            
            // Animação de entrada
            setTimeout(() => {
                messageDiv.classList.remove('translate-x-full');
            }, 100);
            
            // Auto remover após 5 segundos
            setTimeout(() => {
                messageDiv.classList.add('translate-x-full');
                setTimeout(() => {
                    if (messageDiv.parentElement) {
                        messageDiv.remove();
                    }
                }, 300);
            }, 5000);
        }

        // Função para enviar formulário via AJAX
        function submitForm(formId, action) {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            formData.append('action', action);

            sgqFetch('config_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    form.reset();
                    // Recarregar lista se necessário
                    if (action === 'save_filial') loadFiliais();
                    if (action === 'save_departamento') loadDepartamentos();
                    if (action === 'save_fornecedor') loadFornecedores();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showMessage('Erro ao processar solicitação', 'error');
            });
        }

        // Event listeners para os formulários
        document.getElementById('form-filial')?.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm('form-filial', 'save_filial');
        });

        document.getElementById('form-departamento')?.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm('form-departamento', 'save_departamento');
        });

        document.getElementById('form-fornecedor')?.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm('form-fornecedor', 'save_fornecedor');
        });

        // Funções para carregar dados dinamicamente
        function loadFiliais() {
            const container = document.getElementById('filiais-list');
            if (!container) return; // Elemento não existe na aba atual
            
            sgqFetch('config_api.php?action=get_filiais')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = '';
                    
                    if (data.data.length === 0) {
                        container.innerHTML = '<div class="px-4 py-8 text-center text-gray-500">Nenhuma filial cadastrada</div>';
                        return;
                    }
                    
                    data.data.forEach(filial => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-3 flex justify-between items-center';
                        div.innerHTML = `
                            <span class="font-medium">${filial.nome}</span>
                            <div class="space-x-2">
                                <button onclick="editItem(${filial.id}, 'filial')" class="text-sap-blue hover:text-sap-dark-blue text-sm">Editar</button>
                                <button onclick="deleteItem(${filial.id}, 'filial')" class="text-red-500 hover:text-red-700 text-sm">Excluir</button>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao carregar filiais:', error);
                showMessage('Erro ao carregar filiais', 'error');
            });
        }

        function loadDepartamentos() {
            const container = document.getElementById('departamentos-list');
            if (!container) return; // Elemento não existe na aba atual
            
            sgqFetch('config_api.php?action=get_departamentos')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = '';
                    
                    if (data.data.length === 0) {
                        container.innerHTML = '<div class="px-4 py-8 text-center text-gray-500">Nenhum departamento cadastrado</div>';
                        return;
                    }
                    
                    data.data.forEach(departamento => {
                        const div = document.createElement('div');
                        div.className = 'px-4 py-3 flex justify-between items-center';
                        div.innerHTML = `
                            <span class="font-medium">${departamento.nome}</span>
                            <div class="space-x-2">
                                <button onclick="editItem(${departamento.id}, 'departamento')" class="text-sap-blue hover:text-sap-dark-blue text-sm">Editar</button>
                                <button onclick="deleteItem(${departamento.id}, 'departamento')" class="text-red-500 hover:text-red-700 text-sm">Excluir</button>
                            </div>
                        `;
                        container.appendChild(div);
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao carregar departamentos:', error);
                showMessage('Erro ao carregar departamentos', 'error');
            });
        }

        function loadFornecedores() {
            const container = document.getElementById('fornecedores-list');
            if (!container) return; // Elemento não existe na aba atual
            
            sgqFetch('config_api.php?action=get_fornecedores')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = '';
                    
                    if (data.data.length === 0) {
                        container.innerHTML = '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Nenhum fornecedor cadastrado</td></tr>';
                        return;
                    }
                    
                    data.data.forEach(fornecedor => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${fornecedor.nome}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${fornecedor.contato || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${fornecedor.rma || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="editItem(${fornecedor.id}, 'fornecedor')" class="text-sap-blue hover:text-sap-dark-blue mr-3">Editar</button>
                                <button onclick="deleteItem(${fornecedor.id}, 'fornecedor')" class="text-red-500 hover:text-red-700">Excluir</button>
                            </td>
                        `;
                        container.appendChild(tr);
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao carregar fornecedores:', error);
                showMessage('Erro ao carregar fornecedores', 'error');
            });
        }

        // Função para deletar itens
        function deleteItem(id, type) {
            if (confirm('Tem certeza que deseja excluir este item?')) {
                const formData = new FormData();
                formData.append('action', `delete_${type}`);
                formData.append('id', id);

                sgqFetch('config_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        // Recarregar lista
                        if (type === 'filial') loadFiliais();
                        if (type === 'departamento') loadDepartamentos();
                        if (type === 'fornecedor') loadFornecedores();
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showMessage('Erro ao excluir item', 'error');
                });
            }
        }

        // Função para editar itens
        function editItem(id, type) {
            sgqFetch(`config_api.php?action=get_item&type=${type}&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    openEditModal(data.data, type);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showMessage('Erro ao carregar dados do item', 'error');
            });
        }

        // Função para abrir modal de edição
        function openEditModal(item, type) {
            const modal = document.getElementById('edit-modal');
            const title = document.getElementById('modal-title');
            const content = document.getElementById('modal-content');
            
            let titleText = '';
            let formContent = '';
            
            switch (type) {
                case 'filial':
                    titleText = 'Editar Filial';
                    formContent = `
                        <input type="hidden" name="id" value="${item.id}">
                        <input type="hidden" name="type" value="filial">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome da Filial</label>
                            <input type="text" name="nome" value="${item.nome}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                        </div>
                    `;
                    break;
                case 'departamento':
                    titleText = 'Editar Departamento';
                    formContent = `
                        <input type="hidden" name="id" value="${item.id}">
                        <input type="hidden" name="type" value="departamento">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Departamento</label>
                            <input type="text" name="nome" value="${item.nome}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                        </div>
                    `;
                    break;
                case 'fornecedor':
                    titleText = 'Editar Fornecedor';
                    formContent = `
                        <input type="hidden" name="id" value="${item.id}">
                        <input type="hidden" name="type" value="fornecedor">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Fornecedor</label>
                            <input type="text" name="nome" value="${item.nome}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contato</label>
                            <input type="text" name="contato" value="${item.contato || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">RMA</label>
                            <input type="text" name="rma" value="${item.rma || ''}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                        </div>
                    `;
                    break;
            }
            
            title.textContent = titleText;
            content.innerHTML = formContent;
            modal.classList.remove('hidden');
        }

        // Função para fechar modal
        function closeModal() {
            const modal = document.getElementById('edit-modal');
            modal.classList.add('hidden');
        }

        // Event listener para o formulário de edição
        document.getElementById('edit-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const type = formData.get('type');
            formData.append('action', `update_${type}`);
            
            sgqFetch('config_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    closeModal();
                    // Recarregar lista
                    if (type === 'filial') loadFiliais();
                    if (type === 'departamento') loadDepartamentos();
                    if (type === 'fornecedor') loadFornecedores();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showMessage('Erro ao atualizar item', 'error');
            });
        });

        // Carregar dados ao carregar a página baseado na aba ativa
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentTabData();
        });
        
        // Função para carregar dados da aba atual
        function loadCurrentTabData() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || 'filiais';
            
            switch(activeTab) {
                case 'filiais':
                    loadFiliais();
                    break;
                case 'departamentos':
                    loadDepartamentos();
                    break;
                case 'fornecedores':
                    loadFornecedores();
                    break;
            }
        }

        // Fechar modal ao clicar fora dele
        document.getElementById('edit-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
