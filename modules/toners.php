<?php
// Módulo de Controle de Toners
?>

<div class="p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Controle de Toners</h2>
    
    <!-- Sistema de Abas -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button onclick="switchTab('cadastro')" id="tab-cadastro" 
                        class="tab-button active py-2 px-4 border-b-2 border-blue-500 text-blue-600 font-medium text-sm">
                    Cadastro de Toners
                </button>
                <button onclick="switchTab('retornados')" id="tab-retornados" 
                        class="tab-button py-2 px-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    Registro de Retornados
                </button>
            </nav>
        </div>
    </div>

    <!-- Conteúdo das Abas -->
    <div id="content-cadastro" class="tab-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Cadastro de Toners</h3>
                <button onclick="openTonerModal()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Novo Toner
                </button>
            </div>
            
            <!-- Grid de Toners -->
            <div id="toners-grid" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidade</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gramatura</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="toners-tbody" class="bg-white divide-y divide-gray-200">
                        <!-- Dados serão carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="content-retornados" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Registro de Retornados</h3>
                <button onclick="openReturnModal()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-undo mr-2"></i>Registrar Retorno
                </button>
            </div>
            
            <!-- Grid de Retornados -->
            <div id="returns-grid" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Retorno</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso Retornado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Utilizado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="returns-tbody" class="bg-white divide-y divide-gray-200">
                        <!-- Dados serão carregados via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cadastro de Toner -->
<div id="toner-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-[9999]">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 lg:p-6">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl lg:max-w-4xl max-h-[90vh] overflow-y-auto relative z-[10000]">
            <div class="p-3 sm:p-4 lg:p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Cadastro de Toner</h3>
                    <button onclick="closeTonerModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="toner-form" class="space-y-3 sm:space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-4">
                        <div class="sm:col-span-2 lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Modelo *</label>
                            <input type="text" id="modelo" name="modelo" required
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Cor *</label>
                            <select id="cor" name="cor" required
                                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione...</option>
                                <option value="Black">Black</option>
                                <option value="Cyan">Cyan</option>
                                <option value="Magenta">Magenta</option>
                                <option value="Yellow">Yellow</option>
                            </select>
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Tipo *</label>
                            <select id="tipo" name="tipo" required
                                    class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Selecione...</option>
                                <option value="Compativel">Compatível</option>
                                <option value="Original">Original</option>
                                <option value="Remanufaturado">Remanufaturado</option>
                            </select>
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Capacidade (folhas) *</label>
                            <input type="number" id="capacidade" name="capacidade" required min="1"
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateValues()">
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Peso Cheio (g) *</label>
                            <input type="number" id="peso_cheio" name="peso_cheio" required min="0" step="0.1"
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateValues()">
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Peso Vazio (g) *</label>
                            <input type="number" id="peso_vazio" name="peso_vazio" required min="0" step="0.1"
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateValues()">
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Gramatura (g)</label>
                            <input type="number" id="gramatura" name="gramatura" readonly step="0.1"
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                            <small class="text-xs text-gray-500">Calculado automaticamente</small>
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Preço (R$) *</label>
                            <input type="number" id="preco" name="preco" required min="0" step="0.01"
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   onchange="calculateValues()">
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Gramatura por Folha (g)</label>
                            <input type="number" id="gramatura_folha" name="gramatura_folha" readonly step="0.001"
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                            <small class="text-xs text-gray-500">Calculado automaticamente</small>
                        </div>
                        
                        <div class="lg:col-span-1">
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Preço por Folha (R$)</label>
                            <input type="number" id="preco_folha" name="preco_folha" readonly step="0.001"
                                   class="w-full px-2 sm:px-3 py-1.5 sm:py-2 text-sm border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                            <small class="text-xs text-gray-500">Calculado automaticamente</small>
                        </div>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4 sm:pt-6 border-t mt-4 sm:mt-6">
                        <button type="button" onclick="closeTonerModal()" 
                                class="w-full sm:w-auto px-6 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors text-sm sm:text-base">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm sm:text-base">
                            <i class="fas fa-save mr-2"></i>Salvar Toner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.tab-button.active {
    color: #2563eb;
    border-color: #2563eb;
}

.tab-content.hidden {
    display: none;
}

#toner-modal {
    backdrop-filter: blur(4px);
    z-index: 9999 !important;
}

#toner-modal > div {
    z-index: 10000 !important;
}

/* Modal responsivo adicional */
@media (max-width: 640px) {
    #toner-modal .bg-white {
        margin: 0.5rem;
        max-height: calc(100vh - 1rem);
    }
    
    #toner-modal h3 {
        font-size: 1rem;
    }
    
    #toner-modal .grid {
        gap: 0.75rem;
    }
    
    #toner-modal input, 
    #toner-modal select {
        font-size: 14px;
    }
    
    #toner-modal label {
        font-size: 12px;
        margin-bottom: 0.25rem;
    }
    
    #toner-modal small {
        font-size: 10px;
    }
}

@media (min-width: 1024px) {
    #toner-modal .grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (min-width: 1280px) {
    #toner-modal .grid {
        grid-template-columns: repeat(5, 1fr);
    }
}

/* Melhor visibilidade dos campos calculados */
#toner-modal input[readonly] {
    background-color: #f8fafc;
    border-color: #e2e8f0;
    color: #64748b;
    font-weight: 500;
}

/* Scroll suave no modal */
#toner-modal .overflow-y-auto {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

#toner-modal .overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

#toner-modal .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

#toner-modal .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#toner-modal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
// Variáveis globais
let currentTab = 'cadastro';
let toners = [];

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    loadToners();
});

// Funções de navegação entre abas
function switchTab(tab) {
    // Atualizar botões
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('text-gray-500', 'border-transparent');
        btn.classList.remove('text-blue-600', 'border-blue-500');
    });
    
    document.getElementById(`tab-${tab}`).classList.add('active');
    document.getElementById(`tab-${tab}`).classList.remove('text-gray-500', 'border-transparent');
    document.getElementById(`tab-${tab}`).classList.add('text-blue-600', 'border-blue-500');
    
    // Atualizar conteúdo
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.getElementById(`content-${tab}`).classList.remove('hidden');
    currentTab = tab;
    
    if (tab === 'retornados') {
        loadReturns();
    }
}

// Funções do modal
function openTonerModal() {
    document.getElementById('toner-modal').classList.remove('hidden');
    document.getElementById('toner-form').reset();
}

function closeTonerModal() {
    document.getElementById('toner-modal').classList.add('hidden');
}

function openReturnModal() {
    // TODO: Implementar modal de retorno
    alert('Modal de retorno será implementado');
}

// Cálculos automáticos
function calculateValues() {
    const pesoCheio = parseFloat(document.getElementById('peso_cheio').value) || 0;
    const pesoVazio = parseFloat(document.getElementById('peso_vazio').value) || 0;
    const capacidade = parseFloat(document.getElementById('capacidade').value) || 0;
    const preco = parseFloat(document.getElementById('preco').value) || 0;
    
    // Calcular gramatura (peso cheio - peso vazio)
    const gramatura = pesoCheio - pesoVazio;
    document.getElementById('gramatura').value = gramatura.toFixed(1);
    
    if (capacidade > 0) {
        // Calcular gramatura por folha
        const gramaturaFolha = gramatura / capacidade;
        document.getElementById('gramatura_folha').value = gramaturaFolha.toFixed(3);
        
        // Calcular preço por folha
        const precoFolha = preco / capacidade;
        document.getElementById('preco_folha').value = precoFolha.toFixed(3);
    }
}

// Submissão do formulário
document.getElementById('toner-form').addEventListener('submit', function(e) {
    e.preventDefault();
    saveToner();
});

function saveToner() {
    const formData = new FormData(document.getElementById('toner-form'));
    const tonerData = Object.fromEntries(formData.entries());
    
    // Adicionar campos calculados
    tonerData.gramatura = document.getElementById('gramatura').value;
    tonerData.gramatura_folha = document.getElementById('gramatura_folha').value;
    tonerData.preco_folha = document.getElementById('preco_folha').value;
    
    fetch('backend/api/toners.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(tonerData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Toner cadastrado com sucesso!');
            closeTonerModal();
            loadToners();
        } else {
            alert('Erro ao cadastrar: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao cadastrar toner');
    });
}

// Carregar toners
function loadToners() {
    fetch('backend/api/toners.php')
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            toners = result.data;
            renderTonersGrid();
        }
    })
    .catch(error => {
        console.error('Erro ao carregar toners:', error);
    });
}

// Renderizar grid de toners
function renderTonersGrid() {
    const tbody = document.getElementById('toners-tbody');
    
    if (toners.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                    Nenhum toner cadastrado
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = toners.map(toner => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${toner.modelo}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${getColorClass(toner.cor)}-100 text-${getColorClass(toner.cor)}-800">
                    ${toner.cor}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${toner.tipo}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${toner.capacidade}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${toner.gramatura}g</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ ${parseFloat(toner.preco).toFixed(2)}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editToner(${toner.id})" class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
                <button onclick="deleteToner(${toner.id})" class="text-red-600 hover:text-red-900">Excluir</button>
            </td>
        </tr>
    `).join('');
}

function getColorClass(cor) {
    const colorMap = {
        'Black': 'gray',
        'Cyan': 'blue',
        'Magenta': 'pink',
        'Yellow': 'yellow'
    };
    return colorMap[cor] || 'gray';
}

function loadReturns() {
    // TODO: Implementar carregamento de retornos
    console.log('Carregando retornos...');
}

function editToner(id) {
    // TODO: Implementar edição
    alert('Edição será implementada');
}

function deleteToner(id) {
    if (confirm('Tem certeza que deseja excluir este toner?')) {
        fetch(`backend/api/toners.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Toner excluído com sucesso!');
                loadToners();
            } else {
                alert('Erro ao excluir: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir toner');
        });
    }
}
</script>
