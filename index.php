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
    
    /* Sidebar SAP Style */
    .sap-sidebar {
      width: 280px;
      background: linear-gradient(180deg, #0070f2 0%, #0040a0 100%);
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      z-index: 1000;
      box-shadow: 2px 0 8px rgba(0,0,0,0.1);
    }
    
    .sap-sidebar-header {
      padding: 24px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.2);
      text-align: center;
    }
    
    .sap-sidebar-title {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 4px;
    }
    
    .sap-sidebar-subtitle {
      font-size: 12px;
      opacity: 0.8;
      font-weight: 400;
    }
    
    .sap-nav {
      padding: 16px 0;
    }
    
    .sap-nav-item {
      display: block;
      padding: 12px 24px;
      color: rgba(255,255,255,0.9);
      text-decoration: none;
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
      font-size: 14px;
      font-weight: 500;
    }
    
    .sap-nav-item:hover {
      background-color: rgba(255,255,255,0.1);
      color: white;
      border-left-color: #00d4ff;
    }
    
    .sap-nav-item.active {
      background-color: rgba(255,255,255,0.15);
      border-left-color: #00d4ff;
      color: white;
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
    
    /* Cards SAP Style */
    .sap-card {
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      border: 1px solid #e5e5e5;
      margin-bottom: 24px;
    }
    
    .sap-card-header {
      padding: 20px 24px;
      border-bottom: 1px solid #f0f0f0;
    }
    
    .sap-card-title {
      font-size: 18px;
      font-weight: 600;
      color: #32363a;
      margin: 0;
    }
    
    .sap-card-content {
      padding: 24px;
    }
    
    /* Forms SAP Style */
    .sap-form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
    }
    
    .sap-form-group {
      display: flex;
      flex-direction: column;
    }
    
    .sap-label {
      font-size: 14px;
      font-weight: 500;
      color: #32363a;
      margin-bottom: 6px;
    }
    
    .sap-input {
      padding: 12px 16px;
      border: 1px solid #d9d9d9;
      border-radius: 4px;
      font-size: 14px;
      background: white;
      transition: all 0.2s ease;
    }
    
    .sap-input:focus {
      outline: none;
      border-color: #0070f2;
      box-shadow: 0 0 0 2px rgba(0,112,242,0.1);
    }
    
    .sap-button {
      padding: 12px 24px;
      background: #0070f2;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .sap-button:hover {
      background: #0040a0;
    }
    
    .sap-button-secondary {
      background: #f0f0f0;
      color: #32363a;
    }
    
    .sap-button-secondary:hover {
      background: #e0e0e0;
    }
    
    .sap-button-danger {
      background: #d32f2f;
    }
    
    .sap-button-danger:hover {
      background: #b71c1c;
    }
    
    .sap-button-warning {
      background: #ff9800;
    }
    
    .sap-button-warning:hover {
      background: #f57c00;
    }
    
    /* Tables SAP Style */
    .sap-table {
      width: 100%;
      border-collapse: collapse;
      background: white;
    }
    
    .sap-table th {
      background: #f8f9fa;
      padding: 16px;
      text-align: left;
      font-weight: 600;
      color: #32363a;
      border-bottom: 2px solid #e5e5e5;
      font-size: 14px;
    }
    
    .sap-table td {
      padding: 16px;
      border-bottom: 1px solid #f0f0f0;
      font-size: 14px;
      color: #32363a;
    }
    
    .sap-table tr:hover {
      background-color: #f8f9fa;
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
    
    /* Login Page SAP Style */
    .sap-login-container {
      min-height: 100vh;
      background: linear-gradient(135deg, #0070f2 0%, #0040a0 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .sap-login-card {
      background: white;
      padding: 48px;
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 400px;
    }
    
    .sap-login-title {
      font-size: 28px;
      font-weight: 700;
      color: #32363a;
      text-align: center;
      margin-bottom: 8px;
    }
    
    .sap-login-subtitle {
      font-size: 14px;
      color: #666;
      text-align: center;
      margin-bottom: 32px;
    }
    
    .sap-login-form {
      display: flex;
      flex-direction: column;
      gap: 20px;
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
    }
  </style>
</head>
<body>

  <?php if (!$usuario_logado): ?>
  <!-- LOGIN PAGE SAP STYLE -->
  <div class="sap-login-container">
    <div class="sap-login-card">
      <h1 class="sap-login-title">SGQ OTI</h1>
      <p class="sap-login-subtitle">Sistema de Gestão da Qualidade</p>
      
      <?php if (isset($erro_login)): ?>
        <div class="sap-alert sap-alert-error">
          <?= htmlspecialchars($erro_login) ?>
        </div>
      <?php endif; ?>
      
      <form method="POST" class="sap-login-form">
        <div class="sap-form-group">
          <label class="sap-label">E-mail</label>
          <input type="email" name="email" class="sap-input" required>
        </div>
        <div class="sap-form-group">
          <label class="sap-label">Senha</label>
          <input type="password" name="senha" class="sap-input" required>
        </div>
        <button type="submit" class="sap-button">Entrar no Sistema</button>
      </form>
    </div>
  </div>
  <?php else: ?>

  <!-- SAP LAYOUT -->
  <div class="sap-layout">
    <!-- SIDEBAR SAP STYLE -->
    <aside class="sap-sidebar">
      <div class="sap-sidebar-header">
        <h1 class="sap-sidebar-title">SGQ OTI</h1>
        <p class="sap-sidebar-subtitle">Sistema de Gestão da Qualidade</p>
      </div>
      
      <nav class="sap-nav">
        <a href="?module=dashboard" class="sap-nav-item <?= $module == 'dashboard' ? 'active' : '' ?>">📊 Dashboard</a>
        <a href="?module=usuarios" class="sap-nav-item <?= $module == 'usuarios' ? 'active' : '' ?>">👥 Usuários</a>
        <a href="?module=toners" class="sap-nav-item <?= $module == 'toners' ? 'active' : '' ?>">🖨️ Controle de Toners</a>
        <a href="?module=homologacoes" class="sap-nav-item <?= $module == 'homologacoes' ? 'active' : '' ?>">✅ Homologações</a>
        <a href="?module=amostragens" class="sap-nav-item <?= $module == 'amostragens' ? 'active' : '' ?>">🧪 Amostragens</a>
        <a href="?module=garantias" class="sap-nav-item <?= $module == 'garantias' ? 'active' : '' ?>">🛡️ Garantias</a>
        <a href="?module=pops-its" class="sap-nav-item <?= $module == 'pops-its' ? 'active' : '' ?>">📋 POPs e ITs</a>
        <a href="?module=fluxogramas" class="sap-nav-item <?= $module == 'fluxogramas' ? 'active' : '' ?>">📊 Fluxogramas</a>
        <a href="?module=auditorias" class="sap-nav-item <?= $module == 'auditorias' ? 'active' : '' ?>">🔍 Auditorias</a>
        <a href="?module=dinamicas" class="sap-nav-item <?= $module == 'dinamicas' ? 'active' : '' ?>">⚡ Dinâmicas</a>
        <a href="?module=configuracoes" class="sap-nav-item <?= $module == 'configuracoes' ? 'active' : '' ?>">⚙️ Configurações</a>
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
      
        <?php if ($module == 'usuarios'): ?>
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
</body>
</html>
