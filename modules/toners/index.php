<?php
// Debug para identificar erro 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../index.php');
    exit;
}

// Verificar se arquivo existe antes de incluir
if (!file_exists('../../config/environment.php')) {
    die('Arquivo environment.php não encontrado');
}

require_once '../../config/environment.php';

// Verificar se classe existe
if (!class_exists('Environment')) {
    die('Classe Environment não encontrada');
}

try {
    // Usar métodos estáticos da classe Environment (não há getInstance)
    $activeTab = $_GET['tab'] ?? 'cadastro';
} catch (Exception $e) {
    die('Erro ao preparar ambiente: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Toners - SGQ OTI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?php echo Environment::asset('js/app.js'); ?>"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'sap-blue': '#0f4c75',
                        'sap-light-blue': '#3282b8',
                        'sap-gray': '#bbe1fa',
                        'sap-light-gray': '#f8f9fa'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-sap-light-gray">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg">
            <!-- Header -->
            <div class="bg-sap-blue text-white px-6 py-4 rounded-t-lg">
                <h1 class="text-2xl font-bold">Controle de Toners</h1>
                <p class="text-sap-gray">Gerenciamento completo de toners e retornados</p>
            </div>

    <!-- Modal de Novo Toner -->
    <div id="create-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Cadastrar Novo Toner</h3>
                </div>
                <div class="px-6 py-4">
                    <form id="create-form-toner" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                                <input type="text" name="modelo" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                                <select name="cor" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                                    <option value="">Selecione...</option>
                                    <option value="Yellow">Yellow</option>
                                    <option value="Magenta">Magenta</option>
                                    <option value="Cyan">Cyan</option>
                                    <option value="Black">Black</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Peso Cheio (g)</label>
                                <input type="number" name="peso_cheio" step="0.001" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent" onchange="calcularGramatura()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Peso Vazio (g)</label>
                                <input type="number" name="peso_vazio" step="0.001" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent" onchange="calcularGramatura()">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gramatura (g)</label>
                            <input type="number" name="gramatura" step="0.001" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                            <p class="text-xs text-gray-500 mt-1">Calculado automaticamente (Peso Cheio - Peso Vazio)</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacidade de Folhas</label>
                                <input type="number" name="capacidade_folhas" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent" onchange="calcularCustos()">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Preço do Toner (R$)</label>
                                <input type="number" name="preco_toner" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent" onchange="calcularCustos()">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gramatura por Folha (g)</label>
                                <input type="number" name="gramatura_por_folha" step="0.0001" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                <p class="text-xs text-gray-500 mt-1">Calculado automaticamente (Gramatura ÷ Capacidade)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Custo por Folha (R$)</label>
                                <input type="number" name="custo_por_folha" step="0.0001" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-600">
                                <p class="text-xs text-gray-500 mt-1">Calculado automaticamente (Preço ÷ Capacidade)</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                                <option value="">Selecione...</option>
                                <option value="Original">Original</option>
                                <option value="Compativel">Compatível</option>
                                <option value="Remanufaturado">Remanufaturado</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button onclick="closeCreateModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-200">Cancelar</button>
                    <button id="btn-save-toner" class="px-4 py-2 bg-sap-blue text-white rounded-md hover:bg-sap-light-blue transition duration-200">Salvar</button>
                </div>
            </div>
        </div>
    </div>

            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6">
                    <a href="?tab=cadastro" 
                       class="py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'cadastro' ? 'border-sap-blue text-sap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                        Cadastro de Toners
                    </a>
                    <a href="?tab=retornados" 
                       class="py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'retornados' ? 'border-sap-blue text-sap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                        Registro de Retornados
                    </a>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <?php if ($activeTab === 'cadastro'): ?>
                    <!-- Cadastro de Toners -->
                    <div id="cadastro-tab">
                        <!-- Ações acima do grid -->
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-sap-blue">Toners Cadastrados</h3>
                            <div class="space-x-2">
                                <button onclick="openCreateModal()" 
                                        class="bg-sap-blue text-white px-4 py-2 rounded-md hover:bg-sap-light-blue transition duration-200">
                                    Novo Toner
                                </button>
                                <button onclick="openImportModal()" 
                                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-200">
                                    Importar Excel
                                </button>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="max-h-96 overflow-y-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modelo</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cor</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="toners-list" class="bg-white divide-y divide-gray-200">
                                        <!-- Dados carregados via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php elseif ($activeTab === 'retornados'): ?>
                    <!-- Registro de Retornados -->
                    <div id="retornados-tab">
                        <div class="text-center py-12">
                            <div class="text-6xl text-gray-300 mb-4">🚧</div>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Em Construção</h3>
                            <p class="text-gray-500">Esta funcionalidade será implementada em breve.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Importação -->
    <div id="import-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Importar Toners</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo Excel</label>
                            <input type="file" id="excel-file" accept=".xlsx,.xls" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-sap-blue focus:border-transparent">
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-2">Ou baixe o template:</p>
                            <button onclick="downloadTemplate()" 
                                    class="text-sap-blue hover:text-sap-light-blue font-medium">
                                📥 Baixar Template Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button onclick="closeImportModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-200">
                        Cancelar
                    </button>
                    <button onclick="importExcel()" 
                            class="px-4 py-2 bg-sap-blue text-white rounded-md hover:bg-sap-light-blue transition duration-200">
                        Importar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Editar Toner</h3>
                </div>
                <div class="px-6 py-4">
                    <form id="edit-form">
                        <input type="hidden" name="id">
                        <input type="hidden" name="type" value="toner">
                        <!-- Campos do formulário serão preenchidos dinamicamente -->
                        <div id="edit-form-content"></div>
                    </form>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button onclick="closeEditModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition duration-200">
                        Cancelar
                    </button>
                    <button onclick="document.getElementById('edit-form').dispatchEvent(new Event('submit'))" 
                            class="px-4 py-2 bg-sap-blue text-white rounded-md hover:bg-sap-light-blue transition duration-200">
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagens -->
    <div id="message-container" class="fixed top-4 right-4 z-50"></div>

    <script>
        // Funções de cálculo automático
        function calcularGramatura() {
            const pesoCheio = parseFloat(document.querySelector('input[name="peso_cheio"]').value) || 0;
            const pesoVazio = parseFloat(document.querySelector('input[name="peso_vazio"]').value) || 0;
            const gramatura = pesoCheio - pesoVazio;
            
            document.querySelector('input[name="gramatura"]').value = gramatura.toFixed(3);
            calcularCustos();
        }

        function calcularCustos() {
            const gramatura = parseFloat(document.querySelector('input[name="gramatura"]').value) || 0;
            const capacidade = parseFloat(document.querySelector('input[name="capacidade_folhas"]').value) || 0;
            const preco = parseFloat(document.querySelector('input[name="preco_toner"]').value) || 0;
            
            if (capacidade > 0) {
                const gramaturaPorFolha = gramatura / capacidade;
                const custoPorFolha = preco / capacidade;
                
                document.querySelector('input[name="gramatura_por_folha"]').value = gramaturaPorFolha.toFixed(4);
                document.querySelector('input[name="custo_por_folha"]').value = custoPorFolha.toFixed(4);
            }
        }

        // Funções de modal
        function openImportModal() {
            document.getElementById('import-modal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('import-modal').classList.add('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        function openCreateModal() {
            document.getElementById('create-modal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('create-modal').classList.add('hidden');
        }

        function downloadTemplate() {
            window.location.href = '../../api/toners_api.php?action=download_template';
        }

        function importExcel() {
            const fileInput = document.getElementById('excel-file');
            if (!fileInput.files[0]) {
                showMessage('Selecione um arquivo Excel', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('excel_file', fileInput.files[0]);
            formData.append('action', 'import_excel');

            sgqFetch('toners_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    closeImportModal();
                    loadToners();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showMessage('Erro ao importar arquivo', 'error');
            });
        }

        // Função para mostrar mensagens
        function showMessage(message, type) {
            const container = document.getElementById('message-container');
            const messageDiv = document.createElement('div');
            messageDiv.className = `px-4 py-3 rounded-md mb-2 ${type === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300'}`;
            messageDiv.textContent = message;
            
            container.appendChild(messageDiv);
            
            setTimeout(() => {
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'translateX(100%)';
                messageDiv.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.parentNode.removeChild(messageDiv);
                    }
                }, 300);
            }, 5000);
        }

        // Event listeners - criar toner
        document.getElementById('btn-save-toner')?.addEventListener('click', function() {
            const form = document.getElementById('create-form-toner');
            const formData = new FormData(form);
            formData.append('action', 'save_toner');

            sgqFetch('toners_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    form.reset();
                    closeCreateModal();
                    loadToners();
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showMessage('Erro ao salvar toner', 'error');
            });
        });

        // Função para carregar toners
        function loadToners() {
            const container = document.getElementById('toners-list');
            if (!container) return;
            
            sgqFetch('toners_api.php?action=get_toners')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = '';
                    
                    if (data.data.length === 0) {
                        container.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Nenhum toner cadastrado</td></tr>';
                        return;
                    }
                    
                    data.data.forEach(toner => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td class="px-4 py-3 text-sm text-gray-900">${toner.modelo}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getCorColor(toner.cor)}">
                                    ${toner.cor}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">${toner.tipo}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">R$ ${parseFloat(toner.preco_toner).toFixed(2)}</td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="editToner(${toner.id})" 
                                        class="text-sap-blue hover:text-sap-light-blue mr-3">
                                    ✏️ Editar
                                </button>
                                <button onclick="deleteToner(${toner.id})" 
                                        class="text-red-600 hover:text-red-800">
                                    🗑️ Excluir
                                </button>
                            </td>
                        `;
                        container.appendChild(tr);
                    });
                }
            })
            .catch(error => {
                console.error('Erro ao carregar toners:', error);
                showMessage('Erro ao carregar toners', 'error');
            });
        }

        function getCorColor(cor) {
            const colors = {
                'Yellow': 'bg-yellow-100 text-yellow-800',
                'Magenta': 'bg-pink-100 text-pink-800',
                'Cyan': 'bg-blue-100 text-blue-800',
                'Black': 'bg-gray-100 text-gray-800'
            };
            return colors[cor] || 'bg-gray-100 text-gray-800';
        }

        function editToner(id) {
            // Implementar edição
            showMessage('Funcionalidade de edição será implementada', 'info');
        }

        function deleteToner(id) {
            if (confirm('Tem certeza que deseja excluir este toner?')) {
                const formData = new FormData();
                formData.append('action', 'delete_toner');
                formData.append('id', id);
                
                sgqFetch('toners_api.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        loadToners();
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showMessage('Erro ao excluir toner', 'error');
                });
            }
        }

        // Carregar dados ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            loadToners();
        });

        // Fechar modal ao clicar fora dele
        document.getElementById('import-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });

        document.getElementById('edit-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
