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

<!-- Modal de Registro de Retorno -->
<div id="return-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-[9999]">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 lg:p-6">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl lg:max-w-3xl max-h-[90vh] overflow-y-auto relative z-[10000]">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Registro de Retornados</h3>
                    <button onclick="closeReturnModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="return-form" class="space-y-5" oninput="onReturnInputChanged()" onsubmit="event.preventDefault(); submitReturn();">
                    <input type="hidden" id="return-destino" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selecione um Modelo *</label>
                            <select id="return-toner" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="onReturnInputChanged()">
                                <option value="">Carregando...</option>
                            </select>
                            <small class="text-xs text-gray-500">Modelos cadastrados em toners</small>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Código do Cliente *</label>
                            <input id="return-cliente-codigo" type="text" class="w-full px-3 py-2 border rounded-lg" required placeholder="Ex.: CL12345">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Cliente (opcional)</label>
                            <input id="return-cliente-nome" type="text" class="w-full px-3 py-2 border rounded-lg" placeholder="Ex.: João da Silva">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Filial *</label>
                            <input id="return-filial" list="filiais-datalist" type="text" class="w-full px-3 py-2 border rounded-lg" required placeholder="Ex.: Matriz">
                            <datalist id="filiais-datalist"></datalist>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">Modo de Registro</label>
                        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="return-modo" value="peso" checked onchange="toggleReturnMode(); onReturnInputChanged()">
                                <span class="text-sm">Por Peso</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input type="radio" name="return-modo" value="percent" onchange="toggleReturnMode(); onReturnInputChanged()">
                                <span class="text-sm">Por %</span>
                            </label>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div id="campo-peso" class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Peso do Retornado (g) *</label>
                                <input id="return-peso" type="number" step="0.1" min="0" class="w-full px-3 py-2 border rounded-lg" placeholder="Ex.: 350">
                            </div>
                            <div id="campo-percent" class="md:col-span-1 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">% do Retornado *</label>
                                <input id="return-percent" type="number" step="0.01" min="0" max="100" class="w-full px-3 py-2 border rounded-lg" placeholder="Ex.: 62.5">
                            </div>
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">% Calculada/Informada</label>
                                <div class="px-3 py-2 border rounded-lg bg-white font-semibold text-gray-800" id="percentual-display">0%</div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Orientação</label>
                            <div class="px-3 py-2 border rounded-lg bg-yellow-50 text-yellow-800" id="orientacao-text"></div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-2">Selecione o Destino</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <button type="button" class="destino-btn border rounded-lg px-3 py-2 hover:bg-gray-50" data-destino="descarte" onclick="setDestino('descarte')">
                                <span class="block text-sm font-semibold text-red-700">Descarte</span>
                                <small class="text-xs text-gray-500">Registro simples</small>
                            </button>
                            <button type="button" class="destino-btn border rounded-lg px-3 py-2 hover:bg-gray-50" data-destino="uso_interno" onclick="setDestino('uso_interno')">
                                <span class="block text-sm font-semibold text-blue-700">Uso Interno</span>
                                <small class="text-xs text-gray-500">Registro simples</small>
                            </button>
                            <button type="button" class="destino-btn border rounded-lg px-3 py-2 hover:bg-gray-50" data-destino="garantia" onclick="setDestino('garantia')">
                                <span class="block text-sm font-semibold text-yellow-700">Garantia</span>
                                <small class="text-xs text-gray-500">Registro simples</small>
                            </button>
                            <button type="button" class="destino-btn border rounded-lg px-3 py-2 hover:bg-gray-50" data-destino="estoque" onclick="setDestino('estoque')">
                                <span class="block text-sm font-semibold text-green-700">Estoque</span>
                                <small class="text-xs text-gray-500">Calcula valor recuperado</small>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações (opcional)</label>
                        <textarea id="return-observacoes" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Detalhes adicionais..."></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t mt-2">
                        <button type="button" onclick="closeReturnModal()" class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">Cancelar</button>
                        <button id="return-register-btn" type="submit" disabled class="px-5 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg">
                            <i class="fas fa-check mr-2"></i><span id="return-register-text">Registrar Toner</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>

    <!-- Conteúdo das Abas -->
    <div id="content-cadastro" class="tab-content">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Cadastro de Toners</h3>
                <div class="flex space-x-3">
                    <button onclick="openExportModal()" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>Exportar
                    </button>
                    <button onclick="openImportModal()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Importar Planilha
                    </button>
                    <button onclick="openTonerModal()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Novo Toner
                    </button>
                </div>
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
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Registro de Retornados</h3>
                <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
                    <div class="flex items-center gap-2">
                        <input type="date" id="filter-start" class="border rounded px-2 py-1 text-sm" />
                        <span class="text-gray-400">→</span>
                        <input type="date" id="filter-end" class="border rounded px-2 py-1 text-sm" />
                    </div>
                    <input type="text" id="returns-search" placeholder="Buscar (modelo, cliente, filial)" class="border rounded px-3 py-1.5 text-sm w-full sm:w-64" oninput="loadReturns()" />
                    <button onclick="exportReturns()" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-2 rounded-lg text-sm font-medium"><i class="fas fa-file-export mr-2"></i>Exportar</button>
                    <button onclick="openReturnModal()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-undo mr-2"></i>Registrar Retorno
                    </button>
                </div>
            </div>
            
            <!-- Grid de Retornados -->
            <div id="returns-grid" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filial</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peso (g)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Presente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destino</th>
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
                    <h3 id="modal-title" class="text-lg font-semibold text-gray-800">Cadastro de Toner</h3>
                    <button onclick="closeTonerModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="toner-form" class="space-y-3 sm:space-y-4">
                    <input type="hidden" id="toner-id" name="id" value="">
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
                        <button type="submit" id="submit-btn"
                                class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-sm sm:text-base">
                            <i id="submit-icon" class="fas fa-save mr-2"></i><span id="submit-text">Salvar Toner</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Importação de Planilha -->
<div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-[9999]">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 lg:p-6">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative z-[10000]">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Importar Planilha de Toners</h3>
                    <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="space-y-6">
                    <!-- Seção de Download da Planilha Exemplo -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-download text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 mb-2">1. Baixar Planilha Exemplo</h4>
                                <p class="text-sm text-blue-700 mb-3">
                                    Baixe a planilha CSV formatada para Excel com exemplos e instruções de preenchimento.
                                </p>
                                <button onclick="downloadExampleSpreadsheet()" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-file-csv mr-2"></i>Baixar Planilha CSV
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção de Upload da Planilha -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-upload text-green-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-green-900 mb-2">2. Selecionar Planilha Preenchida</h4>
                                <p class="text-sm text-green-700 mb-3">
                                    Selecione a planilha preenchida para importar os dados dos toners.
                                </p>
                                
                                <div class="space-y-3">
                                    <input type="file" id="import-file" accept=".xlsx,.xls,.csv" 
                                           class="hidden" onchange="handleFileSelect(this)">
                                    
                                    <div id="file-drop-zone" 
                                         class="border-2 border-dashed border-green-300 rounded-lg p-6 text-center cursor-pointer hover:border-green-400 transition-colors"
                                         onclick="document.getElementById('import-file').click()">
                                        <i class="fas fa-cloud-upload-alt text-green-500 text-2xl mb-2"></i>
                                        <p class="text-sm text-green-700">
                                            <span class="font-medium">Clique para selecionar</span> ou arraste a planilha aqui
                                        </p>
                                        <p class="text-xs text-green-600 mt-1">
                                            Formatos: .xlsx, .xls, .csv (máx. 5MB)
                                        </p>
                                    </div>
                                    
                                    <div id="selected-file" class="hidden bg-gray-50 border rounded-lg p-3">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-file-excel text-green-600"></i>
                                            <span id="file-name" class="text-sm text-gray-700 flex-1"></span>
                                            <button onclick="clearSelectedFile()" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progresso da Importação -->
                    <div id="import-progress" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="animate-spin">
                                <i class="fas fa-spinner text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-900">Importando dados...</p>
                                <p class="text-xs text-yellow-700">Aguarde enquanto processamos a planilha.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeImportModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                        Cancelar
                    </button>
                    <button id="import-btn" onclick="importSpreadsheet()" disabled
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-upload mr-2"></i>Importar Dados
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exportação -->
<div id="export-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-[9999]">
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 lg:p-6">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto relative z-[10000]">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Exportar Dados dos Toners</h3>
                    <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="space-y-6">
                    <!-- Seção de Formato -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-export text-blue-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 mb-3">Escolha o Formato</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="export-format" value="xlsx" checked 
                                               class="mr-2 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-blue-700">
                                            <i class="fas fa-file-excel mr-2"></i>Excel (.xlsx) - Recomendado
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="export-format" value="csv" 
                                               class="mr-2 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-blue-700">
                                            <i class="fas fa-file-csv mr-2"></i>CSV (.csv) - Compatível
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seção de Opções -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-cog text-green-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-green-900 mb-3">Opções de Exportação</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="include-calculated" checked 
                                               class="mr-2 text-green-600 focus:ring-green-500">
                                        <span class="text-sm text-green-700">Incluir campos calculados (gramatura, preços por folha)</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" id="include-dates" checked 
                                               class="mr-2 text-green-600 focus:ring-green-500">
                                        <span class="text-sm text-green-700">Incluir datas de criação e atualização</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informações -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-info-circle"></i>
                            <span id="export-info">Exportando <strong id="total-toners">0</strong> toners cadastrados</span>
                        </div>
                    </div>
                    
                    <!-- Progresso da Exportação -->
                    <div id="export-progress" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center space-x-3">
                            <div class="animate-spin">
                                <i class="fas fa-spinner text-yellow-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-900">Gerando arquivo...</p>
                                <p class="text-xs text-yellow-700">Aguarde enquanto preparamos a exportação.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeExportModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                        Cancelar
                    </button>
                    <button id="export-btn" onclick="exportToners()" 
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-download mr-2"></i>Exportar Dados
                    </button>
                </div>
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
let editingTonerId = null;
let returnsData = [];
let editingReturnId = null;

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
    editingTonerId = null;
    document.getElementById('modal-title').textContent = 'Cadastro de Toner';
    document.getElementById('submit-text').textContent = 'Salvar Toner';
    document.getElementById('submit-icon').className = 'fas fa-save mr-2';
    document.getElementById('toner-id').value = '';
    document.getElementById('toner-modal').classList.remove('hidden');
    document.getElementById('toner-form').reset();
}

function closeTonerModal() {
    document.getElementById('toner-modal').classList.add('hidden');
}

function openReturnModal(existing = null) {
    editingReturnId = existing ? existing.id : null;
    // Reset
    document.getElementById('return-form').reset();
    document.getElementById('return-register-btn').disabled = true;
    document.querySelectorAll('.destino-btn').forEach(b=>b.classList.remove('ring-2','ring-offset-2','ring-green-500'));
    document.getElementById('return-destino').value = '';
    document.getElementById('percentual-display').textContent = '0%';
    document.getElementById('orientacao-text').textContent = '';
    // Popular modelos
    const sel = document.getElementById('return-toner');
    sel.innerHTML = '<option value="">Selecione...</option>' + toners.map(t=>`<option value="${t.id}">${t.modelo} (${t.cor} • ${t.tipo})</option>`).join('');
    // Popular filiais (datalist) usando valores já registrados
    const dl = document.getElementById('filiais-datalist');
    const unique = [...new Set(returnsData.map(r=>r.filial).filter(Boolean))];
    dl.innerHTML = unique.map(f=>`<option value="${f}"></option>`).join('');
    // Se edição, preencher
    if (existing){
        sel.value = existing.toner_id;
        document.getElementById('return-cliente-codigo').value = existing.cliente_codigo;
        document.getElementById('return-cliente-nome').value = existing.cliente_nome || '';
        document.getElementById('return-filial').value = existing.filial;
        document.querySelector(`input[name="return-modo"][value="${existing.modo}"]`).checked = true;
        toggleReturnMode();
        if (existing.modo==='peso') document.getElementById('return-peso').value = existing.peso_retornado || '';
        if (existing.modo==='percent') document.getElementById('return-percent').value = existing.percentual;
        document.getElementById('percentual-display').textContent = `${Number(existing.percentual).toFixed(2)}%`;
        document.getElementById('orientacao-text').textContent = getOrientation(Number(existing.percentual));
        setDestino(existing.destino);
        document.getElementById('return-observacoes').value = existing.observacoes || '';
        document.getElementById('return-register-text').textContent = 'Salvar Alterações';
    } else {
        document.querySelector('input[name="return-modo"][value="peso"]').checked = true;
        toggleReturnMode();
        document.getElementById('return-register-text').textContent = 'Registrar Toner';
    }
    document.getElementById('return-modal').classList.remove('hidden');
}

function closeReturnModal(){
    document.getElementById('return-modal').classList.add('hidden');
}

function toggleReturnMode(){
    const mode = document.querySelector('input[name="return-modo"]:checked').value;
    document.getElementById('campo-peso').classList.toggle('hidden', mode!== 'peso');
    document.getElementById('campo-percent').classList.toggle('hidden', mode!== 'percent');
}

function onReturnInputChanged(){
    const tonerId = Number(document.getElementById('return-toner').value);
    const mode = document.querySelector('input[name="return-modo"]:checked').value;
    let percent = 0;
    const t = toners.find(x=>x.id==tonerId);
    if (t){
        if (mode==='peso'){
            const peso = parseFloat(document.getElementById('return-peso').value)||0;
            const pesoVazio = parseFloat(t.peso_vazio)||0;
            const gramatura = parseFloat(t.gramatura)||0;
            const presente = Math.max(0, peso - pesoVazio);
            percent = gramatura>0 ? (presente/gramatura)*100 : 0;
        } else {
            percent = parseFloat(document.getElementById('return-percent').value)||0;
        }
    }
    percent = Math.max(0, Math.min(100, percent));
    document.getElementById('percentual-display').textContent = `${percent.toFixed(2)}%`;
    document.getElementById('orientacao-text').textContent = getOrientation(percent);
    // Habilitar registrar somente quando destino estiver escolhido e campos obrigatórios preenchidos
    validateReturnForm();
}

function getOrientation(p){
    if (p <= 5) return 'Descarte o toner.';
    if (p <= 40) return 'Teste o toner se estiver com qualidade boa use internamente se não descarte o toner.';
    if (p <= 80) return 'Teste o toner se estiver com qualidade boa envie para o estoque como semi novo e com % descrita na caixa e envie para a garantia.';
    return 'Teste o toner se estiver com qualidade boa envie para o estoque como novo se não envie para garantia.';
}

function setDestino(dest){
    document.getElementById('return-destino').value = dest;
    document.querySelectorAll('.destino-btn').forEach(b=>{
        if (b.dataset.destino===dest){ b.classList.add('ring-2','ring-offset-2','ring-green-500'); }
        else { b.classList.remove('ring-2','ring-offset-2','ring-green-500'); }
    });
    validateReturnForm();
}

function validateReturnForm(){
    const tonerId = document.getElementById('return-toner').value;
    const clienteCodigo = document.getElementById('return-cliente-codigo').value.trim();
    const filial = document.getElementById('return-filial').value.trim();
    const dest = document.getElementById('return-destino').value;
    const mode = document.querySelector('input[name="return-modo"]:checked').value;
    let okCampos = !!(tonerId && clienteCodigo && filial && dest);
    if (mode==='peso') okCampos = okCampos && !!document.getElementById('return-peso').value;
    if (mode==='percent') okCampos = okCampos && !!document.getElementById('return-percent').value;
    document.getElementById('return-register-btn').disabled = !okCampos;
}

function submitReturn(){
    const tonerId = Number(document.getElementById('return-toner').value);
    const clienteCodigo = document.getElementById('return-cliente-codigo').value.trim();
    const clienteNome = document.getElementById('return-cliente-nome').value.trim();
    const filial = document.getElementById('return-filial').value.trim();
    const mode = document.querySelector('input[name="return-modo"]:checked').value;
    const peso = document.getElementById('return-peso').value;
    const percent = document.getElementById('return-percent').value;
    const destino = document.getElementById('return-destino').value;
    const observacoes = document.getElementById('return-observacoes').value.trim();

    const payload = {
        toner_id: tonerId,
        cliente_codigo: clienteCodigo,
        cliente_nome: clienteNome,
        filial: filial,
        modo: mode,
        destino: destino,
        observacoes: observacoes
    };
    if (mode==='peso') payload.peso_retornado = Number(peso);
    if (mode==='percent') payload.percentual = Number(percent);

    const url = editingReturnId ? `backend/api/returns.php?id=${editingReturnId}` : 'backend/api/returns.php';
    const method = editingReturnId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    }).then(r=>r.json()).then(res=>{
        if (res.success){
            alert(editingReturnId ? 'Registro atualizado!' : 'Retorno registrado!');
            closeReturnModal();
            loadReturns();
        } else {
            alert('Erro: ' + (res.message || JSON.stringify(res)));
        }
    }).catch(err=>{
        console.error(err);
        alert('Erro ao enviar retorno');
    });
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
    
    const isEditing = editingTonerId !== null;
    const method = isEditing ? 'PUT' : 'POST';
    const url = isEditing ? `backend/api/toners.php?id=${editingTonerId}` : 'backend/api/toners.php';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(tonerData)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert(isEditing ? 'Toner atualizado com sucesso!' : 'Toner cadastrado com sucesso!');
            closeTonerModal();
            loadToners();
        } else {
            alert('Erro ao ' + (isEditing ? 'atualizar' : 'cadastrar') + ': ' + result.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao ' + (isEditing ? 'atualizar' : 'cadastrar') + ' toner');
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
            updateExportInfo();
        }
    })
    .catch(error => {
        console.error('Erro ao carregar toners:', error);
    });
}

function updateExportInfo() {
    const totalElement = document.getElementById('total-toners');
    if (totalElement) {
        totalElement.textContent = toners.length;
    }
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
    const params = new URLSearchParams();
    const q = document.getElementById('returns-search')?.value.trim();
    const start = document.getElementById('filter-start')?.value;
    const end = document.getElementById('filter-end')?.value;
    if (q) params.set('q', q);
    if (start) params.set('start', start);
    if (end) params.set('end', end);
    const url = params.toString() ? `backend/api/returns.php?${params.toString()}` : 'backend/api/returns.php';
    fetch(url).then(r=>r.json()).then(data=>{
        returnsData = Array.isArray(data) ? data : [];
        renderReturnsGrid();
    }).catch(err=>console.error('Erro ao carregar retornos', err));
}

function renderReturnsGrid(){
    const tbody = document.getElementById('returns-tbody');
    if (!tbody) return;
    if (!returnsData.length){
        tbody.innerHTML = `<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">Nenhum retorno registrado</td></tr>`;
        return;
    }
    tbody.innerHTML = returnsData.map(r=>{
        const badge = r.destino === 'descarte' ? 'bg-red-100 text-red-800' : r.destino==='estoque' ? 'bg-green-100 text-green-800' : r.destino==='garantia' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800';
        return `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-3 text-sm text-gray-700">${new Date(r.created_at).toLocaleString()}</td>
            <td class="px-6 py-3 text-sm font-medium text-gray-900">${r.modelo}</td>
            <td class="px-6 py-3 text-sm text-gray-700">${r.cliente_codigo} ${r.cliente_nome?('- '+r.cliente_nome):''}</td>
            <td class="px-6 py-3 text-sm text-gray-700">${r.filial}</td>
            <td class="px-6 py-3 text-sm text-gray-700">${r.modo==='peso'?'Peso':'%'} </td>
            <td class="px-6 py-3 text-sm text-gray-700">${r.peso_retornado ?? '-'}</td>
            <td class="px-6 py-3 text-sm text-gray-700">${Number(r.percentual||0).toFixed(2)}%</td>
            <td class="px-6 py-3 text-sm"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${badge}">${r.destino}</span></td>
            <td class="px-6 py-3 text-sm font-medium">
                <button class="text-blue-600 hover:text-blue-900 mr-3" onclick='handleEditReturn(${JSON.stringify(r).replace(/'/g,"&apos;")})'>Editar</button>
                <button class="text-red-600 hover:text-red-900" onclick="handleDeleteReturn(${r.id})">Excluir</button>
            </td>
        </tr>`;
    }).join('');
}

function handleEditReturn(r){
    openReturnModal(r);
}

function handleDeleteReturn(id){
    if (!confirm('Deseja excluir este registro?')) return;
    fetch(`backend/api/returns.php?id=${id}`, { method: 'DELETE' }).then(r=>r.json()).then(res=>{
        if (res.success){ loadReturns(); }
        else alert('Erro ao excluir');
    }).catch(()=>alert('Erro ao excluir'));
}

function exportReturns(){
    const start = document.getElementById('filter-start')?.value;
    const end = document.getElementById('filter-end')?.value;
    const params = new URLSearchParams();
    if (start) params.set('start', start);
    if (end) params.set('end', end);
    const a = document.createElement('a');
    a.href = `backend/api/export-returns.php${params.toString()?('?'+params.toString()):''}`;
    a.style.display='none';
    document.body.appendChild(a); a.click(); document.body.removeChild(a);
}

function editToner(id) {
    const toner = toners.find(t => t.id == id);
    if (!toner) {
        alert('Toner não encontrado');
        return;
    }
    
    editingTonerId = id;
    
    // Atualizar título e botão do modal
    document.getElementById('modal-title').textContent = 'Editar Toner';
    document.getElementById('submit-text').textContent = 'Atualizar Toner';
    document.getElementById('submit-icon').className = 'fas fa-edit mr-2';
    
    // Preencher campos com dados do toner
    document.getElementById('toner-id').value = toner.id;
    document.getElementById('modelo').value = toner.modelo;
    document.getElementById('cor').value = toner.cor;
    document.getElementById('tipo').value = toner.tipo;
    document.getElementById('capacidade').value = toner.capacidade;
    document.getElementById('peso_cheio').value = toner.peso_cheio;
    document.getElementById('peso_vazio').value = toner.peso_vazio;
    document.getElementById('preco').value = toner.preco;
    
    // Calcular valores automaticamente
    calculateValues();
    
    // Abrir modal
    document.getElementById('toner-modal').classList.remove('hidden');
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

// Funções do modal de importação
function openImportModal() {
    document.getElementById('import-modal').classList.remove('hidden');
    clearSelectedFile();
}

function closeImportModal() {
    document.getElementById('import-modal').classList.add('hidden');
    clearSelectedFile();
    document.getElementById('import-progress').classList.add('hidden');
}

function downloadExampleSpreadsheet() {
    // Baixar planilha CSV formatada do backend
    const link = document.createElement('a');
    link.href = 'backend/api/download-example.php';
    link.download = 'exemplo_toners.csv';
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function handleFileSelect(input) {
    const file = input.files[0];
    if (file) {
        // Validar tamanho (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Arquivo muito grande. Máximo 5MB permitido.');
            input.value = '';
            return;
        }
        
        // Validar tipo
        const allowedTypes = ['.xlsx', '.xls', '.csv'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        if (!allowedTypes.includes(fileExtension)) {
            alert('Tipo de arquivo não permitido. Use .xlsx, .xls ou .csv');
            input.value = '';
            return;
        }
        
        // Mostrar arquivo selecionado
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('selected-file').classList.remove('hidden');
        document.getElementById('import-btn').disabled = false;
    }
}

function clearSelectedFile() {
    document.getElementById('import-file').value = '';
    document.getElementById('selected-file').classList.add('hidden');
    document.getElementById('import-btn').disabled = true;
}

function importSpreadsheet() {
    const fileInput = document.getElementById('import-file');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Selecione um arquivo para importar');
        return;
    }
    
    // Mostrar progresso
    document.getElementById('import-progress').classList.remove('hidden');
    document.getElementById('import-btn').disabled = true;
    
    // Criar FormData para envio
    const formData = new FormData();
    formData.append('file', file);
    
    fetch('backend/api/import-toners.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        document.getElementById('import-progress').classList.add('hidden');
        
        if (result.success) {
            alert(`Importação concluída! ${result.imported} toners importados com sucesso.`);
            closeImportModal();
            loadToners();
        } else {
            alert('Erro na importação: ' + result.message);
            document.getElementById('import-btn').disabled = false;
        }
    })
    .catch(error => {
        document.getElementById('import-progress').classList.add('hidden');
        document.getElementById('import-btn').disabled = false;
        console.error('Erro:', error);
        alert('Erro ao importar planilha');
    });
}

// Drag and drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('file-drop-zone');
    
    if (dropZone) {
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropZone.classList.add('border-green-400', 'bg-green-50');
        });
        
        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-green-400', 'bg-green-50');
        });
        
        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            dropZone.classList.remove('border-green-400', 'bg-green-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('import-file').files = files;
                handleFileSelect(document.getElementById('import-file'));
            }
        });
    }
});

// Funções do modal de exportação
function openExportModal() {
    document.getElementById('export-modal').classList.remove('hidden');
    updateExportInfo();
}

function closeExportModal() {
    document.getElementById('export-modal').classList.add('hidden');
    document.getElementById('export-progress').classList.add('hidden');
}

function exportToners() {
    const format = document.querySelector('input[name="export-format"]:checked').value;
    const includeCalculated = document.getElementById('include-calculated').checked;
    const includeDates = document.getElementById('include-dates').checked;
    
    if (toners.length === 0) {
        alert('Não há toners para exportar');
        return;
    }
    
    // Mostrar progresso
    document.getElementById('export-progress').classList.remove('hidden');
    document.getElementById('export-btn').disabled = true;
    
    // Construir URL com parâmetros
    const params = new URLSearchParams({
        format: format,
        include_calculated: includeCalculated ? '1' : '0',
        include_dates: includeDates ? '1' : '0'
    });
    
    // Criar link de download
    const link = document.createElement('a');
    link.href = `backend/api/export-toners.php?${params.toString()}`;
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    
    // Simular clique para download
    link.click();
    document.body.removeChild(link);
    
    // Esconder progresso após um tempo
    setTimeout(() => {
        document.getElementById('export-progress').classList.add('hidden');
        document.getElementById('export-btn').disabled = false;
        closeExportModal();
    }, 2000);
}
</script>
