<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

// Handle login
if ($_POST['login'] ?? false) {
    $_SESSION['logged_in'] = true;
    $_SESSION['user'] = $_POST['username'] ?? 'Usuário';
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGQ OTI - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="bg-gradient-to-br from-sap-light-blue to-white min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md border-t-4 border-sap-blue">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-sap-dark-blue mb-2">SGQ OTI</h1>
            <p class="text-sap-dark-gray">Sistema de Gestão da Qualidade</p>
        </div>
        
        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-sap-dark-gray mb-2">
                    Usuário
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent transition duration-200"
                    placeholder="Digite seu usuário"
                    required
                >
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-sap-dark-gray mb-2">
                    Senha
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sap-blue focus:border-transparent transition duration-200"
                    placeholder="Digite sua senha"
                >
            </div>
            
            <button 
                type="submit" 
                name="login" 
                value="1"
                class="w-full bg-sap-blue hover:bg-sap-dark-blue text-white font-semibold py-3 px-4 rounded-lg transition duration-200 transform hover:scale-105"
            >
                Entrar
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-xs text-sap-dark-gray">
                © 2024 SGQ OTI - Sistema de Gestão da Qualidade
            </p>
        </div>
    </div>
</body>
</html>
