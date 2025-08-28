<?php
// SGQ OTI - Sistema de Gestão da Qualidade
// Integração PHP + HTML com design moderno
require_once 'backend/config/database.php';

// Verificar se há sessão ativa
session_start();

// Conectar ao banco de dados
try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Criar usuário master se não existir
    $stmt_check = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR usuario = ?");
    $stmt_check->execute(['admin@sgqoti.com', 'admin']);
    $master_exists = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$master_exists) {
        $senha_hash = password_hash('Admin@123', PASSWORD_DEFAULT);
        $stmt_create = $pdo->prepare("INSERT INTO usuarios (nome, email, usuario, senha, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt_create->execute(['Administrador Master', 'admin@sgqoti.com', 'admin', $senha_hash]);
        error_log("Usuário master criado automaticamente");
    }
    
} catch (Exception $e) {
    error_log("Erro de conexão: " . $e->getMessage());
}

// Processar login se enviado
if ($_POST && isset($_POST['email']) && isset($_POST['senha'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    // Debug: Log tentativa de login
    error_log("Tentativa de login: " . $email);
    
    if ($pdo) {
        try {
            // Buscar usuário por email ou username
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? OR usuario = ?");
            $stmt->execute([$email, $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug: Log resultado da busca
            error_log("Login attempt - Email/User: $email");
            error_log("Usuário encontrado: " . ($usuario ? "SIM (ID: {$usuario['id']}, Nome: {$usuario['nome']})" : "NÃO"));
            
            if ($usuario) {
                // Verificar senha
                $senha_valida = password_verify($senha, $usuario['senha']);
                error_log("Verificação de senha: " . ($senha_valida ? "VÁLIDA" : "INVÁLIDA"));
                
                if ($senha_valida) {
                    // Login bem-sucedido
                    $_SESSION['user_id'] = $usuario['id'];
                    $_SESSION['user_name'] = $usuario['nome'];
                    $_SESSION['user_email'] = $usuario['email'];
                    
                    error_log("Login bem-sucedido para usuário: " . $usuario['email']);
                    header('Location: index.php');
                    exit;
                } else {
                    $erro_login = "Senha incorreta";
                }
            } else {
                $erro_login = "Usuário não encontrado";
            }
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            $erro_login = "Erro interno do sistema";
        }
    } else {
        error_log("Erro: Conexão com banco não disponível");
        $erro_login = "Erro de conexão com banco de dados";
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

// Determinar módulo atual
$module = $_GET['module'] ?? 'dashboard';
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
    /* SAP-Inspired Design System */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background-color: #f7f7f7;
      color: #32363a;
      line-height: 1.5;
    }
    
    /* Layout Structure */
    .sap-layout {
      display: flex;
      min-height: 100vh;
    }
    
    /* Modern Sidebar */
    .sap-sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 280px;
      height: 100vh;
      background: linear-gradient(145deg, #0f172a 0%, #1e293b 50%, #334155 100%);
      border-right: 1px solid rgba(148, 163, 184, 0.1);
      padding: 0;
      z-index: 100;
      overflow-y: auto;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      backdrop-filter: blur(16px);
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none; /* IE and Edge */
    }
    
    .sap-sidebar::-webkit-scrollbar {
      display: none; /* Chrome, Safari and Opera */
    }
    
    .sap-sidebar-header {
      padding: 32px 24px;
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
      color: white;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .sap-sidebar-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M 8 0 L 0 0 0 8" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.6;
    }
    
    .sap-logo-container {
      margin-bottom: 12px;
    }
    
    .sap-logo {
      max-width: 180px;
      max-height: 60px;
      width: auto;
      height: auto;
      filter: brightness(0) invert(1);
      position: relative;
      z-index: 1;
    }
    
    .sap-sidebar-subtitle {
      font-size: 13px;
      opacity: 0.95;
      margin: 8px 0 0 0;
      font-weight: 500;
      position: relative;
      z-index: 1;
    }
    
    .sap-nav {
      padding: 16px 0;
      margin: 0;
      list-style: none;
    }
    
    .sap-nav-item {
      margin: 4px 16px;
      border-radius: 12px;
      overflow: hidden;
    }
    
    .sap-nav-link {
      display: flex;
      align-items: center;
      padding: 16px 20px;
      color: rgba(226, 232, 240, 0.8);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border-radius: 12px;
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(8px);
    }
    
    .sap-nav-link::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 0;
      height: 100%;
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      z-index: -1;
      border-radius: 12px;
    }
    
    .sap-nav-link::after {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.05);
      opacity: 0;
      transition: opacity 0.3s ease;
      z-index: -1;
      border-radius: 12px;
    }
    
    .sap-nav-link:hover {
      color: #ffffff;
      transform: translateX(6px) scale(1.02);
      box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }
    
    .sap-nav-link:hover::before {
      width: 4px;
    }
    
    .sap-nav-link:hover::after {
      opacity: 1;
    }
    
    .sap-nav-link.active {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
      color: white;
      transform: translateX(6px) scale(1.02);
      box-shadow: 0 12px 30px rgba(99, 102, 241, 0.5);
    }
    
    .sap-nav-link.active::before {
      width: 100%;
    }
    
    .sap-nav-icon {
      margin-right: 14px;
      font-size: 20px;
      width: 28px;
      text-align: center;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }
    
    .sap-nav-link:hover .sap-nav-icon {
      transform: scale(1.15) rotate(5deg);
    }
    
    .sap-nav-link.active .sap-logo {
      max-width: 120px;
      height: auto;
      filter: brightness(1.1) drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
    }
    
    .sap-logo-fallback {
      font-size: 2.5rem;
      font-weight: 900;
      color: white;
      text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
      letter-spacing: 0.1em;
      background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    /* Main Content Area */
    .sap-main {
      flex: 1;
      margin-left: 280px;
      background-color: #f7f7f7;
    }
    
    /* Header SAP Style */
    .sap-header {
      background: white;
      border-bottom: 1px solid #e5e5e5;
      padding: 16px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .sap-header-title {
      font-size: 24px;
      font-weight: 600;
      color: #32363a;
    }
    
    .sap-header-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }
    
    .sap-user-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .sap-user-avatar {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, #0070f2, #0040a0);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 14px;
    }
    
    .sap-user-details {
      text-align: right;
    }
    
    .sap-user-name {
      font-size: 14px;
      font-weight: 600;
      color: #32363a;
    }
    
    .sap-logout {
      font-size: 12px;
      color: #0070f2;
      text-decoration: none;
    }
    
    .sap-logout:hover {
      text-decoration: underline;
    }
    
    /* Content Area */
    .sap-content {
      padding: 32px;
    }
    
    /* Modern Cards */
    .sap-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(226, 232, 240, 0.5);
      margin-bottom: 32px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    
    .sap-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
    }
    
    .sap-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .sap-card-header {
      padding: 28px 32px 20px;
      border-bottom: 1px solid rgba(226, 232, 240, 0.3);
      background: linear-gradient(135deg, rgba(99, 102, 241, 0.02) 0%, rgba(139, 92, 246, 0.02) 100%);
    }
    
    .sap-card-title {
      font-size: 20px;
      font-weight: 700;
      color: #1e293b;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    
    .sap-card-content {
      padding: 32px;
    }
    
    /* Modern Forms */
    .sap-form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 24px;
    }
    
    .sap-form-group {
      display: flex;
      flex-direction: column;
      position: relative;
    }
    
    .sap-label {
      font-size: 14px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 4px;
    }
    
    .sap-input {
      padding: 16px 20px;
      border: 2px solid #e5e7eb;
      border-radius: 12px;
      font-size: 14px;
      background: white;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }
    
    .sap-input:focus {
      outline: none;
      border-color: #6366f1;
      box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
      transform: translateY(-2px);
    }
    
    .sap-input:hover {
      border-color: #d1d5db;
      transform: translateY(-1px);
    }
    
    .sap-button {
      padding: 16px 32px;
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    
    .sap-button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }
    
    .sap-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }
    
    .sap-button:hover::before {
      left: 100%;
    }
    
    .sap-button:active {
      transform: translateY(0);
    }
    
    .sap-button-secondary {
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      color: #475569;
      border: 2px solid #e2e8f0;
    }
    
    .sap-button-secondary:hover {
      background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
      border-color: #cbd5e1;
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(71, 85, 105, 0.2);
    }
    
    .sap-button-danger {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .sap-button-danger:hover {
      background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
    }
    
    .sap-button-warning {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .sap-button-warning:hover {
      background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
    }
    
    /* Modern Tables */
    .sap-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: white;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .sap-table th {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      padding: 20px 24px;
      text-align: left;
      font-weight: 600;
      color: white;
      border: none;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      position: relative;
    }
    
    .sap-table th:first-child {
      border-top-left-radius: 16px;
    }
    
    .sap-table th:last-child {
      border-top-right-radius: 16px;
    }
    
    .sap-table td {
      padding: 20px 24px;
      border-bottom: 1px solid rgba(226, 232, 240, 0.5);
      font-size: 14px;
      color: #475569;
      transition: all 0.2s ease;
      position: relative;
    }
    
    .sap-table tbody tr {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sap-table tbody tr:hover {
      background: linear-gradient(90deg, rgba(99, 102, 241, 0.05) 0%, rgba(139, 92, 246, 0.05) 100%);
      transform: scale(1.01);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    
    .sap-table tbody tr:last-child td:first-child {
      border-bottom-left-radius: 16px;
    }
    
    .sap-table tbody tr:last-child td:last-child {
      border-bottom-right-radius: 16px;
    }
    
    .sap-table tbody tr:last-child td {
      border-bottom: none;
    }
    
    /* Dashboard Cards */
    .sap-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }
    
    .sap-stat-card {
      background: white;
      padding: 24px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border-left: 4px solid #0070f2;
    }
    
    .sap-stat-number {
      font-size: 32px;
      font-weight: 700;
      color: #0070f2;
      margin-bottom: 8px;
    }
    
    .sap-stat-label {
      font-size: 14px;
      color: #666;
      font-weight: 500;
    }
    
    /* Modern Login Page */
    .sap-login-container {
      min-height: 100vh;
      background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow: hidden;
    }
    
    .sap-login-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: radial-gradient(circle at 30% 20%, rgba(139, 92, 246, 0.3) 0%, transparent 50%),
                  radial-gradient(circle at 70% 80%, rgba(59, 130, 246, 0.3) 0%, transparent 50%);
    }
    
    .sap-login-layout {
      display: flex;
      background: rgba(30, 41, 59, 0.95);
      border-radius: 16px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
      overflow: hidden;
      width: 100%;
      max-width: 1000px;
      height: 600px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sap-login-left {
      flex: 1;
      background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px;
      position: relative;
    }
    
    .sap-login-left::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
      opacity: 0.3;
    }
    
    .sap-login-logo {
      max-width: 180px;
      max-height: 80px;
      margin-bottom: 20px;
      filter: brightness(0) invert(1);
      z-index: 1;
      object-fit: contain;
    }
    
    .sap-login-brand {
      text-align: center;
      color: white;
      z-index: 1;
      margin-top: 10px;
    }
    
    .sap-login-brand h1 {
      font-size: 42px;
      font-weight: 700;
      margin: 0 0 12px 0;
      background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: 2px;
    }
    
    .sap-login-brand p {
      font-size: 16px;
      opacity: 0.8;
      margin: 0 0 20px 0;
    }
    
    .sap-login-version {
      font-size: 12px;
      opacity: 0.6;
      margin-top: auto;
      z-index: 1;
    }
    
    .sap-login-right {
      flex: 1;
      background: rgba(51, 65, 85, 0.8);
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 60px 50px;
    }
    
    .sap-login-welcome {
      color: white;
      margin-bottom: 40px;
    }
    
    .sap-login-welcome h2 {
      font-size: 28px;
      font-weight: 600;
      margin: 0 0 8px 0;
    }
    
    .sap-login-form {
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
    
    .sap-login-form .sap-form-group {
      position: relative;
    }
    
    .sap-login-form .sap-label {
      color: #e2e8f0;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 8px;
      display: block;
    }
    
    .sap-login-form .sap-input {
      width: 100%;
      padding: 16px 20px;
      background: rgba(30, 41, 59, 0.8);
      border: 1px solid rgba(148, 163, 184, 0.3);
      border-radius: 12px;
      color: white;
      font-size: 16px;
      transition: all 0.3s ease;
      backdrop-filter: blur(10px);
    }
    
    .sap-login-form .sap-input:focus {
      outline: none;
      border-color: #8b5cf6;
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
      background: rgba(30, 41, 59, 0.9);
    }
    
    .sap-login-form .sap-input::placeholder {
      color: rgba(148, 163, 184, 0.6);
    }
    
    .sap-login-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 8px 0;
    }
    
    .sap-login-remember {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #e2e8f0;
      font-size: 14px;
    }
    
    .sap-login-forgot {
      color: #8b5cf6;
      text-decoration: none;
      font-size: 14px;
      transition: color 0.3s ease;
    }
    
    .sap-login-forgot:hover {
      color: #a78bfa;
    }
    
    .sap-login-submit {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
      color: white;
      border: none;
      padding: 16px 32px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 8px;
    }
    
    .sap-login-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
    }
    
    .sap-login-footer {
      text-align: center;
      margin-top: 30px;
      color: rgba(148, 163, 184, 0.8);
      font-size: 12px;
    }
    
    .sap-alert {
      padding: 12px 16px;
      border-radius: 4px;
      font-size: 14px;
      margin-bottom: 20px;
    }
    
    .sap-alert-error {
      background: #ffebee;
      color: #c62828;
      border: 1px solid #ffcdd2;
    }
    
    /* Modern Button Components */
    .modern-btn {
      display: inline-flex;
      items-center: center;
      padding: 12px 24px;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      border: none;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .modern-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      transition: left 0.5s;
    }
    
    .modern-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .modern-btn:hover::before {
      left: 100%;
    }
    
    .modern-btn-primary {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      color: white;
    }
    
    .modern-btn-primary:hover {
      box-shadow: 0 10px 25px rgba(99, 102, 241, 0.4);
    }
    
    .modern-btn-green {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
    }
    
    .modern-btn-green:hover {
      box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
    }
    
    .modern-btn-purple {
      background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
      color: white;
    }
    
    .modern-btn-purple:hover {
      box-shadow: 0 10px 25px rgba(139, 92, 246, 0.4);
    }
    
    /* Modern Badges */
    .modern-badge {
      display: inline-flex;
      align-items: center;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .modern-badge-black {
      background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
      color: white;
    }
    
    .modern-badge-cyan {
      background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
      color: white;
    }
    
    .modern-badge-magenta {
      background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
      color: white;
    }
    
    .modern-badge-yellow {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
      color: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .sap-sidebar {
        width: 100%;
        transform: translateX(-100%);
      }
      
      .sap-main {
        margin-left: 0;
      }
      
      .sap-content {
        padding: 16px;
      }
      
      .sap-form {
        grid-template-columns: 1fr;
      }
      
      .modern-btn {
        padding: 10px 20px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>

  <?php if (!$usuario_logado): ?>
  <!-- MODERN LOGIN PAGE -->
  <div class="sap-login-container">
    <div class="sap-login-layout">
      <!-- Left Side - Branding -->
      <div class="sap-login-left">
        <img src="assets/images/logo.png" alt="SGQ OTI" class="sap-login-logo">
        <div class="sap-login-brand">
          <h1>SGQ OTI</h1>
          <p>Sistema de Gestão da Qualidade</p>
        </div>
        <div class="sap-login-version">Versão 1.0</div>
      </div>
      
      <!-- Right Side - Login Form -->
      <div class="sap-login-right">
        <div class="sap-login-welcome">
          <h2>Bem-vindo</h2>
        </div>
        
        <?php if (isset($erro_login)): ?>
          <div class="sap-alert sap-alert-error">
            <?= htmlspecialchars($erro_login) ?>
          </div>
        <?php endif; ?>
        
        <form method="POST" class="sap-login-form">
          <div class="sap-form-group">
            <label class="sap-label">E-mail</label>
            <input type="email" name="email" class="sap-input" placeholder="seu@email.com" required>
          </div>
          <div class="sap-form-group">
            <label class="sap-label">Senha</label>
            <input type="password" name="senha" class="sap-input" placeholder="••••••••" required>
          </div>
          
          <div class="sap-login-options">
            <label class="sap-login-remember">
              <input type="checkbox" name="lembrar">
              <span>Lembrar-me</span>
            </label>
            <a href="#" class="sap-login-forgot">Esqueceu a senha?</a>
          </div>
          
          <button type="submit" class="sap-login-submit">Entrar</button>
        </form>
        
        <div class="sap-login-footer">
          © 2025 SGQ OTI - Todos os direitos reservados.
        </div>
      </div>
    </div>
  </div>
  <?php else: ?>

  <!-- SAP LAYOUT -->
  <div class="sap-layout">
    <!-- SIDEBAR SAP STYLE -->
    <aside class="sap-sidebar">
      <div class="sap-sidebar-header">
        <div class="sap-logo-container">
          <img id="sidebar-logo" src="backend/api/logos.php?tipo=sidebar&download=1" alt="SGQ OTI" class="sap-logo" style="display: none;">
          <div id="logo-fallback" class="sap-logo-fallback">SGQ</div>
        </div>
        <p class="sap-sidebar-subtitle">Sistema de Gestão da Qualidade</p>
      </div>
      
      <nav class="sap-nav">
        <ul class="sap-nav">
          <li class="sap-nav-item">
            <a href="?module=dashboard" class="sap-nav-link <?= $module == 'dashboard' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Dashboard
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=toners" class="sap-nav-link <?= $module == 'toners' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Controle de Toners
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=homologacoes" class="sap-nav-link <?= $module == 'homologacoes' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Homologações
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=amostragens" class="sap-nav-link <?= $module == 'amostragens' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Amostragens
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=garantias" class="sap-nav-link <?= $module == 'garantias' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Garantias
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=pops-its" class="sap-nav-link <?= $module == 'pops-its' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>POPs e ITs
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=fluxogramas" class="sap-nav-link <?= $module == 'fluxogramas' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Fluxogramas
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=auditorias" class="sap-nav-link <?= $module == 'auditorias' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Auditorias
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=dinamicas" class="sap-nav-link <?= $module == 'dinamicas' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Dinâmicas
            </a>
          </li>
          <li class="sap-nav-item">
            <a href="?module=configuracoes" class="sap-nav-link <?= $module == 'configuracoes' ? 'active' : '' ?>">
              <span class="sap-nav-icon">●</span>Configurações
            </a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="sap-main">
      <!-- HEADER SAP STYLE -->
      <header class="sap-header">
        <h1 class="sap-header-title">
          <?php
          $titles = [
            'dashboard' => 'Dashboard',
            'usuarios' => 'Gestão de Usuários',
            'toners' => 'Controle de Toners',
            'homologacoes' => 'Homologações',
            'amostragens' => 'Amostragens',
            'garantias' => 'Garantias',
            'pops-its' => 'POPs e ITs',
            'fluxogramas' => 'Fluxogramas',
            'auditorias' => 'Auditorias',
            'dinamicas' => 'Dinâmicas',
            'configuracoes' => 'Configurações'
          ];
          echo $titles[$module] ?? 'SGQ OTI';
          ?>
        </h1>
        
        <div class="sap-header-actions">
          <div class="sap-user-info">
            <div class="sap-user-details">
              <div class="sap-user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
              <a href="logout.php" class="sap-logout">Sair do Sistema</a>
            </div>
            <div class="sap-user-avatar">
              <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
            </div>
          </div>
        </div>
      </header>

      <!-- CONTENT AREA -->
      <div class="sap-content">
      
        <?php if ($module == 'configuracoes'): ?>
          <?php include 'modules/configuracoes.php'; ?>
          
        <?php elseif ($module == 'toners'): ?>
          <?php include 'modules/toners.php'; ?>
          
        <?php elseif ($module == 'usuarios'): ?>
        <!-- FORMULÁRIO DE USUÁRIOS SAP STYLE -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h2 class="sap-card-title">Cadastro de Novo Usuário</h2>
          </div>
          <div class="sap-card-content">
            <form method="POST" action="backend/api/users.php" class="sap-form">
              <input type="hidden" name="action" value="create_user">
              <div class="sap-form-group">
                <label class="sap-label">Nome Completo</label>
                <input type="text" name="nome" class="sap-input" required>
              </div>
              <div class="sap-form-group">
                <label class="sap-label">E-mail</label>
                <input type="email" name="email" class="sap-input" required>
              </div>
              <div class="sap-form-group">
                <label class="sap-label">Nome de Usuário</label>
                <input type="text" name="usuario" class="sap-input" required>
              </div>
              <div class="sap-form-group">
                <label class="sap-label">Senha</label>
                <input type="password" name="senha" class="sap-input" required>
              </div>
              <div class="sap-form-group" style="grid-column: 1 / -1;">
                <button type="submit" class="sap-button">Cadastrar Usuário</button>
              </div>
            </form>
          </div>
        </div>

        <!-- TABELA DE USUÁRIOS SAP STYLE -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h2 class="sap-card-title">Usuários Cadastrados (<?= count($usuarios) ?>)</h2>
          </div>
          <div class="sap-card-content">
            <div style="overflow-x: auto;">
              <table class="sap-table">
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Usuário</th>
                    <th>Data Cadastro</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($usuarios as $u): ?>
                  <tr>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['usuario']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                    <td>
                      <button class="sap-button sap-button-warning" style="margin-right: 8px; padding: 6px 12px; font-size: 12px;">Editar</button>
                      <button class="sap-button sap-button-danger" style="padding: 6px 12px; font-size: 12px;">Excluir</button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <?php elseif ($module == 'dashboard'): ?>
        <!-- DASHBOARD SAP STYLE -->
        <div class="sap-stats-grid">
          <div class="sap-stat-card">
            <div class="sap-stat-number"><?= count($usuarios) ?></div>
            <div class="sap-stat-label">Usuários Cadastrados</div>
          </div>
          <div class="sap-stat-card">
            <div class="sap-stat-number">11</div>
            <div class="sap-stat-label">Módulos Disponíveis</div>
          </div>
          <div class="sap-stat-card">
            <div class="sap-stat-number">100%</div>
            <div class="sap-stat-label">Sistema Online</div>
          </div>
        </div>
        
        <div class="sap-card">
          <div class="sap-card-header">
            <h2 class="sap-card-title">Atividade do Sistema</h2>
          </div>
          <div class="sap-card-content">
            <canvas id="dashboardChart" style="max-height: 400px;"></canvas>
          </div>
        </div>

        <?php else: ?>
        <!-- MÓDULO GENÉRICO SAP STYLE -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h2 class="sap-card-title">Módulo: <?= ucfirst(str_replace('-', ' ', $module)) ?></h2>
          </div>
          <div class="sap-card-content">
            <div style="text-align: center; padding: 64px 32px;">
              <div style="width: 64px; height: 64px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 24px;">🚧</div>
              <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 12px; color: #32363a;">Em Desenvolvimento</h3>
              <p style="color: #666; font-size: 14px;">Este módulo está sendo desenvolvido e estará disponível em breve.</p>
            </div>
          </div>
        </div>
        <?php endif; ?>

    </main>
  </div>

  <script>
    // Gráfico do Dashboard SAP Style
    <?php if ($module == 'dashboard'): ?>
    const ctx = document.getElementById('dashboardChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
          datasets: [{
            label: 'Usuários Ativos',
            data: [<?= count($usuarios) ?>, <?= count($usuarios) + 2 ?>, <?= count($usuarios) + 1 ?>, <?= count($usuarios) + 3 ?>, <?= count($usuarios) + 2 ?>, <?= count($usuarios) + 4 ?>],
            borderColor: '#0070f2',
            backgroundColor: 'rgba(0,112,242,0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { 
            legend: { 
              display: true,
              position: 'top',
              labels: {
                font: {
                  family: 'Inter',
                  size: 12
                }
              }
            }
          },
          scales: { 
            y: { 
              beginAtZero: true,
              grid: {
                color: '#f0f0f0'
              },
              ticks: {
                font: {
                  family: 'Inter',
                  size: 11
                }
              }
            },
            x: {
              grid: {
                color: '#f0f0f0'
              },
              ticks: {
                font: {
                  family: 'Inter',
                  size: 11
                }
              }
            }
          }
        }
      });
    }
    <?php endif; ?>
  </script>

  <?php endif; ?>
  <script>
    // Verificar se logo existe e mostrar fallback se necessário
    document.addEventListener('DOMContentLoaded', function() {
      const logoImg = document.getElementById('sidebar-logo');
      const logoFallback = document.getElementById('logo-fallback');
      
      if (logoImg) {
        // Adicionar timestamp para evitar cache
        const timestamp = new Date().getTime();
        logoImg.src = logoImg.src + '&t=' + timestamp;
        
        logoImg.onload = function() {
          logoImg.style.display = 'block';
          if (logoFallback) logoFallback.style.display = 'none';
        };
        
        logoImg.onerror = function() {
          logoImg.style.display = 'none';
          if (logoFallback) logoFallback.style.display = 'block';
        };
        
        // Forçar reload da imagem
        logoImg.src = logoImg.src;
      }
    });
  </script>
  </body>
</html>
