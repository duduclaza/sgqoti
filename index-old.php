<?php
// SGQ OTI - Sistema de Gestão da Qualidade
// Integração PHP + HTML com design moderno
require_once 'backend/config/database.php';

// Verificar se há sessão ativa (implementar autenticação se necessário)
session_start();

// Conectar ao banco de dados
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log("Erro de conexão: " . $e->getMessage());
}

// Processar login se enviado
if ($_POST && isset($_POST['email']) && isset($_POST['senha'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    // Verificar credenciais no banco
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_name'] = $usuario['nome'];
        $_SESSION['user_email'] = $usuario['email'];
        header('Location: index.php');
        exit;
    } else {
        $erro_login = "Credenciais inválidas";
    }
}

// Verificar se usuário está logado
$usuario_logado = isset($_SESSION['user_id']);

// Buscar dados para exibição
$usuarios = [];
if ($usuario_logado && $pdo) {
    try {
        $stmt = $pdo->query("SELECT id, nome, email, usuario, created_at FROM usuarios ORDER BY created_at DESC");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Erro ao buscar usuários: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="transition-colors duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGQ OTI - Sistema de Gestão da Qualidade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .theme-light { --bg: #f2f2f6; --bg-header: #ffffff; --bg-sidebar: #f9f9fb; --text: #1c1c1e; --input-bg: #ffffff; --input-border: #d1d1d6; --table-header: #e5e5ea; }
        .theme-dark { --bg: #1f1f23; --bg-header: #2a2a2f; --bg-sidebar: #2a2a2f; --text: #e5e5e5; --input-bg: #2f2f35; --input-border: #444; --table-header: #3a3a3f; }
        input, select, textarea { transition: all 0.3s ease; }
        table th, table td { border-bottom: 1px solid var(--input-border); }
        tr:hover { background-color: rgba(37,99,235,0.08); cursor: pointer; }
        aside nav a { position: relative; transition: all 0.3s ease; }
        aside nav a:hover { background-color: rgba(37,99,235,0.15); transform: translateX(4px); }
        #loginPage { background: linear-gradient(135deg, #2563eb, #9333ea); }
        #loginBox { background-color: #fff; }
        .theme-dark #loginBox { background-color: #1f1f23; }
    </style>
</head>
<body class="theme-light font-inter transition-colors duration-300" style="background-color: var(--bg); color: var(--text)">
    <!-- Container Principal -->
    <div class="flex min-h-screen">
        <!-- Menu Lateral Fixo -->
        <aside id="sidebar" class="fixed left-0 top-0 z-40 w-64 h-screen shadow-xl border-r flex flex-col transition-colors duration-300" style="background-color: var(--bg-sidebar); border-color: var(--input-border)">
            <!-- Header do Menu -->
            <div class="flex items-center justify-center h-20 border-b bg-gradient-to-r from-blue-600 to-purple-600" style="border-color: var(--input-border)">
                <div class="text-center">
                    <h1 class="text-xl font-bold text-white">SGQ OTI</h1>
                    <p class="text-sm text-blue-100">Sistema de Gestão da Qualidade</p>
                </div>
            </div>

            <!-- Navegação -->
            <nav class="mt-6 px-3 flex-1 overflow-y-auto scrollbar-hidden">
                <ul class="space-y-1 pb-20">
                    <li>
                        <a href="#" class="sidebar-link active" data-module="toners">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Controle de Toners</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="homologacoes">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Homologações</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="amostragens">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Amostragens</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="garantias">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Garantias</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="pops-its">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">POPs e ITs</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="fluxogramas">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Fluxogramas</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="auditorias">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Auditorias</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="dinamicas">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Dinâmicas</span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="sidebar-link" data-module="configuracoes">
                            <div class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 group">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/30 flex items-center justify-center mr-3 transition-colors duration-200">
                                    <svg class="w-4 h-4 transition-colors duration-200" style="color: var(--text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="font-medium text-sm">Configurações</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer do Menu -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>&copy; 2025 SGQ OTI</p>
                    <p>Versão 1.0.0</p>
                </div>
            </div>
        </aside>

        <!-- Área de Conteúdo Principal -->
        <main class="flex-1 ml-64">
            <!-- Header Superior -->
            <header class="shadow-sm border-b h-20 flex items-center justify-between px-8 transition-colors duration-300" style="background-color: var(--bg-header); border-color: var(--input-border); color: var(--text)">
                <div>
                    <h2 id="page-title" class="text-2xl font-bold" style="color: var(--text)">Controle de Toners</h2>
                    <p id="page-subtitle" class="text-sm opacity-70" style="color: var(--text)">Gerencie o cadastro de toners e cartuchos</p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="px-4 py-2 rounded-lg transition-all duration-300 flex items-center gap-2 shadow-sm" style="background-color: var(--table-header); color: var(--text); border: 1px solid var(--input-border)" title="Alternar tema">
                        <span id="theme-toggle-icon" aria-hidden="true">🌙</span>
                        <span id="theme-toggle-text" class="text-sm font-medium">Modo escuro</span>
                    </button>
                    <div class="text-right">
                        <p class="text-sm font-medium" style="color: var(--text)">Usuário Logado</p>
                        <p class="text-xs opacity-70" style="color: var(--text)" id="current-date"></p>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-semibold">U</span>
                    </div>
                </div>
            </header>

            <!-- Conteúdo da Página -->
            <div class="p-8">
                <div id="content-area" class="rounded-xl shadow-sm border p-6 transition-colors duration-300" style="background-color: var(--bg-header); border-color: var(--input-border); color: var(--text)">
                    <!-- Conteúdo será carregado dinamicamente aqui -->
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2" style="color: var(--text)">Controle de Toners</h3>
                        <p class="opacity-70" style="color: var(--text)">Selecione uma opção no menu lateral para começar</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
