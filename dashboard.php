<?php
session_start();
require_once 'config/environment.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit();
}

$current_page = $_GET['page'] ?? 'home';
$user = $_SESSION['user'] ?? 'Usuário';

// Define menu items
$menu_items = [
    'home' => ['title' => 'Dashboard', 'icon' => '🏠'],
    'toners' => ['title' => 'Controle de Toners', 'icon' => '🖨️', 'url' => 'modules/toners/index.php'],
    'homologacoes' => ['title' => 'Homologações', 'icon' => '✅'],
    'amostragens' => ['title' => 'Amostragens', 'icon' => '🧪'],
    'garantias' => ['title' => 'Garantias', 'icon' => '🛡️'],
    'descartes' => ['title' => 'Controle de Descartes', 'icon' => '♻️'],
    'femea' => ['title' => 'FEMEA', 'icon' => '⚠️'],
    'pops' => ['title' => 'POPs e ITs', 'icon' => '📋'],
    'fluxogramas' => ['title' => 'Fluxogramas', 'icon' => '📊'],
    'melhoria' => ['title' => 'Melhoria Continua', 'icon' => '📈'],
    'rc' => ['title' => 'Controle de RC', 'icon' => '🔧'],
    'config' => ['title' => 'Configurações', 'icon' => '⚙️', 'url' => 'modules/config/index.php']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGQ OTI - <?php echo $menu_items[$current_page]['title'] ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom scrollbar styles */
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
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Logo/Header -->
        <div class="flex flex-col items-center justify-center bg-sap-blue text-white border-b p-4">
            <img src="<?php echo Environment::asset('images/Logo.png'); ?>" alt="SGQ OTI Logo" class="w-32 h-32 mb-2 object-contain">
            <h1 class="text-lg font-bold text-center">SGQ OTI</h1>
            <p class="text-xs text-center text-sap-light-blue mt-1">Sistema de Gestão da Qualidade</p>
        </div>
        
        <!-- Navigation Menu with Scroll -->
        <nav class="flex-1 overflow-y-auto py-4 px-4 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <?php foreach ($menu_items as $key => $item): ?>
                <a href="<?php echo isset($item['url']) ? $item['url'] : '?page=' . $key; ?>" 
                   class="flex items-center px-4 py-3 mb-2 text-gray-700 rounded-lg hover:bg-sap-light-blue hover:text-sap-dark-blue transition-colors duration-200 <?php echo $current_page === $key ? 'bg-sap-light-blue text-sap-dark-blue border-r-4 border-sap-blue' : ''; ?>">
                    <span class="text-xl mr-3"><?php echo $item['icon']; ?></span>
                    <span class="font-medium"><?php echo $item['title']; ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        
        <!-- User Info & Logout -->
        <div class="mt-auto p-4 border-t bg-gray-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-sap-blue rounded-full flex items-center justify-center text-white text-sm font-bold">
                        <?php echo strtoupper(substr($user, 0, 1)); ?>
                    </div>
                    <span class="ml-2 text-sm text-gray-700"><?php echo $user; ?></span>
                </div>
                <a href="logout.php" class="text-red-500 hover:text-red-700 text-sm font-medium">
                    Sair
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="ml-64 flex-1">
        <!-- Top Header -->
        <header class="bg-white shadow-sm border-b h-16 flex items-center justify-between px-6">
            <h2 class="text-2xl font-semibold text-sap-dark-blue">
                <?php echo $menu_items[$current_page]['title'] ?? 'Dashboard'; ?>
            </h2>
            <div class="text-sm text-gray-500">
                <?php echo date('d/m/Y H:i'); ?>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="p-6">
            <?php if ($current_page === 'home'): ?>
                <!-- Dashboard Home -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Stats Cards -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-sap-blue">
                        <div class="flex items-center">
                            <div class="text-3xl text-sap-blue mr-4">📊</div>
                            <div>
                                <p class="text-sm text-gray-600">Total de Módulos</p>
                                <p class="text-2xl font-bold text-sap-dark-blue"><?php echo count($menu_items) - 1; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-sap-green">
                        <div class="flex items-center">
                            <div class="text-3xl text-sap-green mr-4">✅</div>
                            <div>
                                <p class="text-sm text-gray-600">Sistema</p>
                                <p class="text-2xl font-bold text-sap-green">Ativo</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-sap-orange">
                        <div class="flex items-center">
                            <div class="text-3xl text-sap-orange mr-4">🚧</div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p class="text-2xl font-bold text-sap-orange">Em Desenvolvimento</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Welcome Message -->
                <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                    <h3 class="text-2xl font-bold text-sap-dark-blue mb-4">
                        Bem-vindo ao SGQ OTI
                    </h3>
                    <p class="text-gray-600 mb-6">
                        Sistema de Gestão da Qualidade - Todos os módulos estão em desenvolvimento
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="modules/toners/index.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-sap-gray hover:text-sap-blue transition-colors duration-200 rounded-lg mx-2">
                            <span class="mr-3">🖨️</span>
                            <span>Controle de Toners</span>
                        </a>
                        <?php foreach (array_slice($menu_items, 2) as $key => $item): ?>
                            <a href="?page=<?php echo $key; ?>" 
                               class="p-4 border rounded-lg hover:bg-sap-light-blue hover:border-sap-blue transition-colors duration-200">
                                <div class="text-2xl mb-2"><?php echo $item['icon']; ?></div>
                                <div class="text-sm font-medium text-gray-700"><?php echo $item['title']; ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Module Pages -->
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <div class="text-6xl mb-6">🚧</div>
                    <h3 class="text-3xl font-bold text-sap-dark-blue mb-4">
                        Em Construção
                    </h3>
                    <p class="text-xl text-gray-600 mb-8">
                        O módulo <strong><?php echo $menu_items[$current_page]['title']; ?></strong> está sendo desenvolvido
                    </p>
                    <div class="bg-sap-light-blue p-6 rounded-lg inline-block">
                        <p class="text-sap-dark-blue font-medium">
                            Este módulo estará disponível em breve!
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
