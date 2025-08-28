<?php
// Módulo de Configurações com abas
$aba = $_GET['aba'] ?? 'sistema';
?>

<div class="sap-content">
  <!-- Tabs de Configurações -->
  <div class="sap-tabs">
    <div class="sap-tab-nav">
      <a href="?module=configuracoes&aba=sistema" class="sap-tab-link <?= $aba == 'sistema' ? 'active' : '' ?>">
        <span class="sap-tab-icon">🔧</span>Sistema
      </a>
      <a href="?module=configuracoes&aba=empresa" class="sap-tab-link <?= $aba == 'empresa' ? 'active' : '' ?>">
        <span class="sap-tab-icon">🏢</span>Dados da Empresa
      </a>
      <a href="?module=configuracoes&aba=filiais" class="sap-tab-link <?= $aba == 'filiais' ? 'active' : '' ?>">
        <span class="sap-tab-icon">🏬</span>Filiais
      </a>
      <a href="?module=configuracoes&aba=usuarios" class="sap-tab-link <?= $aba == 'usuarios' ? 'active' : '' ?>">
        <span class="sap-tab-icon">👥</span>Usuários
      </a>
      <a href="?module=configuracoes&aba=backup" class="sap-tab-link <?= $aba == 'backup' ? 'active' : '' ?>">
        <span class="sap-tab-icon">💾</span>Backup
      </a>
      <a href="?module=configuracoes&aba=logs" class="sap-tab-link <?= $aba == 'logs' ? 'active' : '' ?>">
        <span class="sap-tab-icon">📋</span>Logs
      </a>
    </div>
    
    <div class="sap-tab-content">
      <?php if ($aba == 'sistema'): ?>
        <!-- Aba Sistema -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h3>Gerenciar Logos</h3>
          </div>
          <div class="sap-card-content">
            <div class="logo-upload-section">
              <h4>Upload de Logo do Sidebar</h4>
              <form id="logo-sidebar-form" enctype="multipart/form-data" class="logo-form">
                <input type="hidden" name="tipo" value="sidebar">
                <input type="hidden" name="nome" value="Logo Sidebar">
                <div class="sap-form-group">
                  <label class="sap-label">Selecionar Imagem (PNG, JPG, SVG - máx 5MB)</label>
                  <input type="file" name="logo" accept="image/*" class="sap-input" required>
                </div>
                <button type="submit" class="modern-btn modern-btn-primary">
                  <span>📤</span> Enviar Logo Sidebar
                </button>
              </form>
            </div>
            
            <div class="logo-upload-section" style="margin-top: 2rem;">
              <h4>Upload de Logo do Header</h4>
              <form id="logo-header-form" enctype="multipart/form-data" class="logo-form">
                <input type="hidden" name="tipo" value="header">
                <input type="hidden" name="nome" value="Logo Header">
                <div class="sap-form-group">
                  <label class="sap-label">Selecionar Imagem (PNG, JPG, SVG - máx 5MB)</label>
                  <input type="file" name="logo" accept="image/*" class="sap-input" required>
                </div>
                <button type="submit" class="modern-btn modern-btn-green">
                  <span>📤</span> Enviar Logo Header
                </button>
              </form>
            </div>
            
            <div class="logos-preview" style="margin-top: 2rem;">
              <h4>Logos Atuais</h4>
              <div id="logos-list" class="logos-grid">
                <!-- Logos serão carregados aqui -->
              </div>
            </div>
          </div>
        </div>
        
        <div class="sap-card" style="margin-top: 2rem;">
          <div class="sap-card-header">
            <h3>Configurações do Sistema</h3>
          </div>
          <div class="sap-card-content">
            <div class="sap-form-group">
              <label class="sap-label">Versão do Sistema</label>
              <input type="text" class="sap-input" value="1.0.0" readonly>
            </div>
            <div class="sap-form-group">
              <label class="sap-label">Status da Conexão</label>
              <div class="sap-status-indicator">
                <span class="sap-status-dot active"></span>
                <span>Conectado</span>
              </div>
            </div>
          </div>
        </div>
        
      <?php elseif ($aba == 'empresa'): ?>
        <!-- Aba Dados da Empresa -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h3 class="sap-card-title">Dados da Empresa</h3>
          </div>
          <div class="sap-card-content">
            <div class="sap-form">
              <div class="sap-form-group">
                <label class="sap-label">Nome da Empresa</label>
                <input type="text" class="sap-input" id="empresa-nome" value="SGQ OTI" onchange="updatePreview()">
              </div>
              <div class="sap-form-group">
                <label class="sap-label">Descrição</label>
                <input type="text" class="sap-input" id="empresa-descricao" value="Sistema de Gestão da Qualidade" onchange="updatePreview()">
              </div>
            </div>
          </div>
        </div>

        <!-- Configuração de Logos -->
        <div class="sap-card" style="margin-top: 20px;">
          <div class="sap-card-header">
            <h3 class="sap-card-title">Configuração de Logos</h3>
          </div>
          <div class="sap-card-content">
            <div class="logo-config-grid">
              <!-- Logo do Menu -->
              <div class="logo-config-section">
                <h4>Logo do Menu Lateral</h4>
                <div class="logo-upload-area" onclick="document.getElementById('menu-logo-input').click()">
                  <img id="menu-logo-preview" src="assets/images/logo.png" alt="Logo Menu" onerror="this.style.display='none'">
                  <div class="upload-placeholder">
                    <span>📁 Clique para enviar logo</span>
                    <small>PNG, JPG, SVG (máx. 2MB)</small>
                  </div>
                </div>
                <input type="file" id="menu-logo-input" accept="image/*" style="display: none;" onchange="handleLogoUpload(this, 'menu')">
                
                <div class="logo-controls">
                  <label class="sap-label">Largura (px)</label>
                  <input type="range" id="menu-logo-width" min="50" max="300" value="180" onchange="updateLogoSize('menu')">
                  <span id="menu-logo-width-value">180px</span>
                  
                  <label class="sap-label">Altura (px)</label>
                  <input type="range" id="menu-logo-height" min="30" max="150" value="60" onchange="updateLogoSize('menu')">
                  <span id="menu-logo-height-value">60px</span>
                </div>
              </div>

              <!-- Logo do Login -->
              <div class="logo-config-section">
                <h4>Logo da Tela de Login</h4>
                <div class="logo-upload-area" onclick="document.getElementById('login-logo-input').click()">
                  <img id="login-logo-preview" src="assets/images/logo.png" alt="Logo Login" onerror="this.style.display='none'">
                  <div class="upload-placeholder">
                    <span>📁 Clique para enviar logo</span>
                    <small>PNG, JPG, SVG (máx. 2MB)</small>
                  </div>
                </div>
                <input type="file" id="login-logo-input" accept="image/*" style="display: none;" onchange="handleLogoUpload(this, 'login')">
                
                <div class="logo-controls">
                  <label class="sap-label">Largura (px)</label>
                  <input type="range" id="login-logo-width" min="100" max="400" value="180" onchange="updateLogoSize('login')">
                  <span id="login-logo-width-value">180px</span>
                  
                  <label class="sap-label">Altura (px)</label>
                  <input type="range" id="login-logo-height" min="50" max="200" value="80" onchange="updateLogoSize('login')">
                  <span id="login-logo-height-value">80px</span>
                </div>
              </div>
            </div>

            <!-- Preview das Telas -->
            <div class="preview-section">
              <h4>Preview das Telas</h4>
              <div class="preview-grid">
                <!-- Preview Menu -->
                <div class="preview-card">
                  <h5>Menu Lateral</h5>
                  <div class="preview-menu">
                    <div class="preview-menu-header">
                      <img id="preview-menu-logo" src="assets/images/logo.png" alt="Logo">
                      <div class="preview-menu-text">
                        <div class="preview-menu-title" id="preview-menu-title">SGQ OTI</div>
                        <div class="preview-menu-subtitle" id="preview-menu-subtitle">Sistema de Gestão da Qualidade</div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Preview Login -->
                <div class="preview-card">
                  <h5>Tela de Login</h5>
                  <div class="preview-login">
                    <div class="preview-login-left">
                      <img id="preview-login-logo" src="assets/images/logo.png" alt="Logo">
                      <div class="preview-login-title" id="preview-login-title">SGQ OTI</div>
                      <div class="preview-login-subtitle" id="preview-login-subtitle">Sistema de Gestão da Qualidade</div>
                    </div>
                    <div class="preview-login-right">
                      <div class="preview-form">Bem-vindo</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="sap-form-actions" style="margin-top: 20px;">
              <button class="sap-button" onclick="saveLogoSettings()">💾 Salvar Configurações</button>
              <button class="sap-button sap-button-secondary" onclick="resetLogos()">🔄 Restaurar Padrão</button>
            </div>
          </div>
        </div>
        
      <?php elseif ($aba == 'filiais'): ?>
        <!-- Aba Filiais -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h3 class="sap-card-title">Cadastro de Filiais</h3>
          </div>
          <div class="sap-card-content">
            <form class="sap-form" onsubmit="event.preventDefault(); saveBranchCfg();">
              <input type="hidden" id="cfg-branch-id" value="">
              <div class="sap-form-group">
                <label class="sap-label">Nome da Filial</label>
                <input id="cfg-branch-name" type="text" class="sap-input" placeholder="Ex.: Matriz" required>
              </div>
              <div class="sap-form-actions">
                <button class="sap-button" id="cfg-branch-submit">
                  <span class="sap-button-icon">💾</span><span id="cfg-branch-submit-text">Adicionar</span>
                </button>
              </div>
            </form>

            <div class="sap-table-container" style="margin-top:16px;">
              <table class="sap-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Filial</th>
                    <th>Criada em</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody id="cfg-branches-tbody">
                  <tr><td colspan="4" class="text-center">Carregando...</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      <?php elseif ($aba == 'usuarios'): ?>
        <!-- Aba Usuários -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h3 class="sap-card-title">Gestão de Usuários</h3>
            <button class="sap-button" onclick="showAddUserModal()">
              <span class="sap-button-icon">➕</span>Novo Usuário
            </button>
          </div>
          <div class="sap-card-content">
            <!-- Lista de Usuários -->
            <div class="sap-table-container">
              <table class="sap-table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Usuário</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody id="usuarios-table">
                  <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $user): ?>
                      <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['nome']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['usuario']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                        <td>
                          <button class="sap-button-small" onclick="editUser(<?= $user['id'] ?>)">✏️</button>
                          <button class="sap-button-small sap-button-danger" onclick="deleteUser(<?= $user['id'] ?>)">🗑️</button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">Nenhum usuário encontrado</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
      <?php elseif ($aba == 'backup'): ?>
        <!-- Aba Backup -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h3 class="sap-card-title">Backup e Restauração</h3>
          </div>
          <div class="sap-card-content">
            <div class="sap-form">
              <div class="sap-form-group">
                <label class="sap-label">Último Backup</label>
                <input type="text" class="sap-input" value="Nunca realizado" readonly>
              </div>
              <div class="sap-form-actions">
                <button class="sap-button">💾 Fazer Backup</button>
                <button class="sap-button sap-button-secondary">📥 Restaurar</button>
              </div>
            </div>
          </div>
        </div>
        
      <?php elseif ($aba == 'logs'): ?>
        <!-- Aba Logs -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h3 class="sap-card-title">Logs do Sistema</h3>
          </div>
          <div class="sap-card-content">
            <div class="sap-log-viewer">
              <div class="sap-log-entry">
                <span class="sap-log-time"><?= date('Y-m-d H:i:s') ?></span>
                <span class="sap-log-level info">INFO</span>
                <span class="sap-log-message">Sistema iniciado com sucesso</span>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal para Adicionar Usuário -->
<div id="addUserModal" class="sap-modal" style="display: none;">
  <div class="sap-modal-content">
    <div class="sap-modal-header">
      <h3>Novo Usuário</h3>
      <button class="sap-modal-close" onclick="hideAddUserModal()">&times;</button>
    </div>
    <div class="sap-modal-body">
      <form id="addUserForm" class="sap-form">
        <div class="sap-form-group">
          <label class="sap-label">Nome Completo</label>
          <input type="text" name="nome" class="sap-input" required>
        </div>
        <div class="sap-form-group">
          <label class="sap-label">Email</label>
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
        <div class="sap-form-actions">
          <button type="submit" class="sap-button">Criar Usuário</button>
          <button type="button" class="sap-button sap-button-secondary" onclick="hideAddUserModal()">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
/* Estilos para as abas */
.sap-tabs {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  overflow: hidden;
}

.sap-tab-nav {
  display: flex;
  background: #f8f9fa;
  border-bottom: 1px solid #e0e0e0;
}

.sap-tab-link {
  display: flex;
  align-items: center;
  padding: 16px 24px;
  text-decoration: none;
  color: #666;
  font-weight: 500;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
}

.sap-tab-link:hover {
  background: rgba(0,112,242,0.1);
  color: #0070f2;
}

.sap-tab-link.active {
  background: white;
  color: #0070f2;
  border-bottom-color: #0070f2;
}

.sap-tab-icon {
  margin-right: 8px;
  font-size: 16px;
}

.sap-tab-content {
  padding: 24px;
}

/* Estilos para status */
.sap-status-indicator {
  display: flex;
  align-items: center;
  gap: 8px;
}

.sap-status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #ccc;
}

.sap-status-dot.active {
  background: #4caf50;
}

/* Estilos para botões pequenos */
.sap-button-small {
  padding: 4px 8px;
  font-size: 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin: 0 2px;
  background: #f0f0f0;
  transition: all 0.2s ease;
}

.sap-button-small:hover {
  background: #e0e0e0;
}

.sap-button-danger {
  background: #ffebee !important;
  color: #c62828;
}

.sap-button-danger:hover {
  background: #ffcdd2 !important;
}

/* Modal styles */
.sap-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sap-modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
}

.sap-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e0e0e0;
}

.sap-modal-close {
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #666;
}

.sap-modal-body {
  padding: 24px;
}

/* Logo Configuration Styles */
.logo-config-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
  margin-bottom: 30px;
}

.logo-config-section h4 {
  margin: 0 0 15px 0;
  color: #32363a;
  font-weight: 600;
}

.logo-upload-area {
  border: 2px dashed #e0e0e0;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
  background: #fafafa;
  min-height: 120px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  position: relative;
}

.logo-upload-area:hover {
  border-color: #0070f2;
  background: #f0f8ff;
}

.logo-upload-area img {
  max-width: 100px;
  max-height: 60px;
  object-fit: contain;
  margin-bottom: 10px;
}

.upload-placeholder {
  color: #666;
}

.upload-placeholder span {
  display: block;
  font-weight: 500;
  margin-bottom: 5px;
}

.upload-placeholder small {
  color: #999;
  font-size: 12px;
}

.logo-controls {
  margin-top: 15px;
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 10px;
  align-items: center;
}

.logo-controls input[type="range"] {
  width: 100%;
}

.logo-controls span {
  font-weight: 500;
  color: #0070f2;
  min-width: 60px;
  text-align: right;
}

/* Preview Styles */
.preview-section {
  margin-top: 30px;
  padding-top: 30px;
  border-top: 1px solid #e0e0e0;
}

.preview-section h4 {
  margin: 0 0 20px 0;
  color: #32363a;
  font-weight: 600;
}

.preview-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.preview-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 15px;
  background: white;
}

.preview-card h5 {
  margin: 0 0 15px 0;
  color: #32363a;
  font-size: 14px;
  font-weight: 600;
}

/* Preview Menu */
.preview-menu {
  background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
  border-radius: 6px;
  padding: 15px;
  min-height: 100px;
}

.preview-menu-header {
  background: linear-gradient(135deg, #0070f2 0%, #0040a0 100%);
  padding: 15px;
  border-radius: 4px;
  text-align: center;
  color: white;
}

.preview-menu-header img {
  max-width: 60px;
  max-height: 30px;
  object-fit: contain;
  filter: brightness(0) invert(1);
  margin-bottom: 8px;
}

.preview-menu-title {
  font-size: 14px;
  font-weight: 700;
  margin-bottom: 4px;
}

.preview-menu-subtitle {
  font-size: 10px;
  opacity: 0.9;
}

/* Preview Login */
.preview-login {
  display: flex;
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
  border-radius: 6px;
  overflow: hidden;
  min-height: 120px;
}

.preview-login-left {
  flex: 1;
  background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
  padding: 15px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
}

.preview-login-left img {
  max-width: 50px;
  max-height: 25px;
  object-fit: contain;
  filter: brightness(0) invert(1);
  margin-bottom: 8px;
}

.preview-login-title {
  font-size: 12px;
  font-weight: 700;
  margin-bottom: 4px;
}

.preview-login-subtitle {
  font-size: 8px;
  opacity: 0.8;
}

.preview-login-right {
  flex: 1;
  background: rgba(51, 65, 85, 0.8);
  padding: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.preview-form {
  font-size: 12px;
  font-weight: 600;
}

/* Log viewer */
.sap-log-viewer {
  background: #f8f9fa;
  border-radius: 4px;
  padding: 16px;
  font-family: 'Courier New', monospace;
  font-size: 12px;
  max-height: 400px;
  overflow-y: auto;
}

.sap-log-entry {
  display: flex;
  gap: 12px;
  margin-bottom: 8px;
}

.sap-log-time {
  color: #666;
}

.sap-log-level {
  padding: 2px 6px;
  border-radius: 3px;
  font-weight: bold;
  font-size: 10px;
}

.sap-log-level.info {
  background: #e3f2fd;
  color: #1976d2;
}
</style>

<script>
function showAddUserModal() {
  document.getElementById('addUserModal').style.display = 'flex';
}

function hideAddUserModal() {
  document.getElementById('addUserModal').style.display = 'none';
  document.getElementById('addUserForm').reset();
}

function editUser(id) {
  alert('Editar usuário ID: ' + id);
}

function deleteUser(id) {
  if (confirm('Tem certeza que deseja excluir este usuário?')) {
    // Implementar exclusão via AJAX
    alert('Usuário ID ' + id + ' excluído');
  }
}

// Logo Management Functions
function updatePreview() {
  const nome = document.getElementById('empresa-nome').value;
  const descricao = document.getElementById('empresa-descricao').value;
  
  // Update menu preview
  document.getElementById('preview-menu-title').textContent = nome;
  document.getElementById('preview-menu-subtitle').textContent = descricao;
  
  // Update login preview
  document.getElementById('preview-login-title').textContent = nome;
  document.getElementById('preview-login-subtitle').textContent = descricao;
}

function handleLogoUpload(input, type) {
  const file = input.files[0];
  if (!file) return;
  
  if (file.size > 2 * 1024 * 1024) {
    alert('Arquivo muito grande! Máximo 2MB.');
    return;
  }
  
  const reader = new FileReader();
  reader.onload = function(e) {
    const imgSrc = e.target.result;
    
    // Update preview image
    document.getElementById(type + '-logo-preview').src = imgSrc;
    document.getElementById(type + '-logo-preview').style.display = 'block';
    
    // Update preview sections
    if (type === 'menu') {
      document.getElementById('preview-menu-logo').src = imgSrc;
    } else {
      document.getElementById('preview-login-logo').src = imgSrc;
    }
    
    // Upload to server
    uploadLogoToServer(file, type);
  };
  reader.readAsDataURL(file);
}

function uploadLogoToServer(file, type) {
  const formData = new FormData();
  formData.append('logo', file);
  formData.append('tipo', type);
  formData.append('nome', 'Logo ' + type.charAt(0).toUpperCase() + type.slice(1));
  
  fetch('backend/api/logos.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      console.log('Logo uploaded successfully');
      alert('Logo enviado com sucesso!');
      // Recarregar logos
      if (typeof loadLogos === 'function') {
        loadLogos();
      }
    } else {
      alert('Erro ao fazer upload: ' + result.error);
    }
  })
  .catch(error => {
    console.error('Upload error:', error);
    alert('Erro ao fazer upload do logo');
  });

// ==============================
// Filiais (Configurações)
// ==============================
const CFG_ABA = '<?= $aba ?>';
let cfgBranches = [];
let cfgEditingBranchId = null;

function loadBranchesCfg(){
  fetch('backend/api/branches.php')
    .then(r=>r.json())
    .then(res=>{ if(res.success){ cfgBranches = res.data||[]; renderBranchesGridCfg(); } else { renderBranchesGridCfg(true); }})
    .catch(()=>renderBranchesGridCfg(true));
}

function renderBranchesGridCfg(error=false){
  const tbody = document.getElementById('cfg-branches-tbody');
  if (!tbody) return;
  if (error){
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Erro ao carregar filiais</td></tr>';
    return;
  }
  if (!cfgBranches.length){
    tbody.innerHTML = '<tr><td colspan="4" class="text-center">Nenhuma filial cadastrada</td></tr>';
    return;
  }
  tbody.innerHTML = cfgBranches.map(b=>`
    <tr>
      <td>${b.id}</td>
      <td>${b.nome}</td>
      <td>${b.created_at ? new Date(b.created_at).toLocaleString() : '-'}</td>
      <td>
        <button class="sap-button-small" onclick="startEditBranchCfg(${b.id})">✏️</button>
        <button class="sap-button-small sap-button-danger" onclick="deleteBranchCfg(${b.id})">🗑️</button>
      </td>
    </tr>
  `).join('');
}

function saveBranchCfg(){
  const id = cfgEditingBranchId;
  const nome = document.getElementById('cfg-branch-name').value.trim();
  if (!nome) return;
  const url = id ? `backend/api/branches.php?id=${id}` : 'backend/api/branches.php';
  const method = id ? 'PUT' : 'POST';
  fetch(url, { method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ nome }) })
    .then(r=>r.json())
    .then(res=>{
      if (res.success){
        document.getElementById('cfg-branch-name').value = '';
        cfgEditingBranchId = null;
        const btnText = document.getElementById('cfg-branch-submit-text');
        if (btnText) btnText.textContent = 'Adicionar';
        loadBranchesCfg();
      } else {
        alert('Erro ao salvar filial');
      }
    })
    .catch(()=>alert('Erro ao salvar filial'));
}

function startEditBranchCfg(id){
  const b = cfgBranches.find(x=>x.id==id); if(!b) return;
  cfgEditingBranchId = id;
  document.getElementById('cfg-branch-name').value = b.nome;
  const btnText = document.getElementById('cfg-branch-submit-text');
  if (btnText) btnText.textContent = 'Salvar';
}

function deleteBranchCfg(id){
  if (!confirm('Excluir filial?')) return;
  fetch(`backend/api/branches.php?id=${id}`, { method: 'DELETE' })
    .then(r=>r.json())
    .then(res=>{ if(res.success){ loadBranchesCfg(); } else { alert('Erro ao excluir filial'); } })
    .catch(()=>alert('Erro ao excluir filial'));
}

// Auto-load filiais list when Filiais tab is active
document.addEventListener('DOMContentLoaded', function(){
  if (CFG_ABA === 'filiais'){
    loadBranchesCfg();
  }
  
  // Auto-load logos when Sistema tab is active
  if (CFG_ABA === 'sistema'){
    if (typeof loadLogos === 'function') {
      loadLogos();
    }
  }
});

function updateLogoSize(type) {
  const width = document.getElementById(type + '-logo-width').value;
  const height = document.getElementById(type + '-logo-height').value;
  
  // Update value displays
  document.getElementById(type + '-logo-width-value').textContent = width + 'px';
  document.getElementById(type + '-logo-height-value').textContent = height + 'px';
  
  // Update preview images
  const previewImg = document.getElementById('preview-' + type + '-logo');
  previewImg.style.maxWidth = width + 'px';
  previewImg.style.maxHeight = height + 'px';
  
  // Update actual preview image
  const actualImg = document.getElementById(type + '-logo-preview');
  actualImg.style.maxWidth = width + 'px';
  actualImg.style.maxHeight = height + 'px';
}

function saveLogoSettings() {
  const settings = {
    empresa_nome: document.getElementById('empresa-nome').value,
    empresa_descricao: document.getElementById('empresa-descricao').value,
    menu_logo_width: document.getElementById('menu-logo-width').value,
    menu_logo_height: document.getElementById('menu-logo-height').value,
    login_logo_width: document.getElementById('login-logo-width').value,
    login_logo_height: document.getElementById('login-logo-height').value
  };
  
  fetch('backend/api/save-settings.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(settings)
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Erro na requisição: ' + response.status);
    }
    return response.text();
  })
  .then(text => {
    try {
      const result = JSON.parse(text);
      if (result.success) {
        alert('Configurações salvas com sucesso!');
        location.reload();
      } else {
        alert('Erro ao salvar: ' + result.message);
      }
    } catch (e) {
      console.error('Resposta não é JSON válido:', text);
      alert('Erro: Resposta inválida do servidor');
    }
  })
  .catch(error => {
    console.error('Erro na requisição:', error);
    alert('Erro: ' + error.message);
  });
}

function resetLogos() {
  if (confirm('Tem certeza que deseja restaurar as configurações padrão?')) {
    // Reset form values
    document.getElementById('empresa-nome').value = 'SGQ OTI';
    document.getElementById('empresa-descricao').value = 'Sistema de Gestão da Qualidade';
    
    // Reset sliders
    document.getElementById('menu-logo-width').value = 180;
    document.getElementById('menu-logo-height').value = 60;
    document.getElementById('login-logo-width').value = 180;
    document.getElementById('login-logo-height').value = 80;
    
    // Update displays
    updateLogoSize('menu');
    updateLogoSize('login');
    updatePreview();
    
    alert('Configurações restauradas!');
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  // Initialize slider values
  updateLogoSize('menu');
  updateLogoSize('login');
  updatePreview();
});

// Submissão do formulário de usuário
document.getElementById('addUserForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const data = Object.fromEntries(formData);
  
  fetch('backend/api/users.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      action: 'create_user',
      ...data
    })
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      alert('Usuário criado com sucesso!');
      hideAddUserModal();
      location.reload();
    } else {
      alert('Erro: ' + result.message);
    }
  })
  .catch(error => {
    alert('Erro ao criar usuário: ' + error.message);
  });
});

// Sistema de upload de logos
document.addEventListener('DOMContentLoaded', function() {
  // Upload de logo sidebar
  const sidebarForm = document.getElementById('logo-sidebar-form');
  if (sidebarForm) {
    sidebarForm.addEventListener('submit', function(e) {
      e.preventDefault();
      uploadLogo(this, 'sidebar');
    });
  }
  
  // Upload de logo header
  const headerForm = document.getElementById('logo-header-form');
  if (headerForm) {
    headerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      uploadLogo(this, 'header');
    });
  }
  
  // Carregar logos existentes
  if (document.getElementById('logos-list')) {
    loadLogos();
  }
});

function uploadLogo(form, tipo) {
  const formData = new FormData(form);
  const button = form.querySelector('button[type="submit"]');
  const originalText = button.innerHTML;
  
  button.innerHTML = '<span>⏳</span> Enviando...';
  button.disabled = true;
  
  fetch('backend/api/logos.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Logo enviado com sucesso!');
      form.reset();
      loadLogos();
      
      // Atualizar logo no sidebar se for do tipo sidebar
      if (tipo === 'sidebar') {
        const sidebarLogo = document.getElementById('sidebar-logo');
        const logoFallback = document.getElementById('logo-fallback');
        if (sidebarLogo) {
          sidebarLogo.src = data.url + '&t=' + Date.now();
          sidebarLogo.style.display = 'block';
          if (logoFallback) logoFallback.style.display = 'none';
        }
      }
    } else {
      alert('Erro: ' + data.error);
    }
  })
  .catch(error => {
    console.error('Erro:', error);
    alert('Erro ao enviar logo');
  })
  .finally(() => {
    button.innerHTML = originalText;
    button.disabled = false;
  });
}

function loadLogos() {
  fetch('backend/api/logos.php')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const logosList = document.getElementById('logos-list');
      if (logosList) {
        logosList.innerHTML = '';
        
        data.logos.forEach(logo => {
          const logoDiv = document.createElement('div');
          logoDiv.className = 'logo-item';
          logoDiv.innerHTML = `
            <div class="logo-preview">
              <img src="${logo.url}" alt="${logo.nome}" style="max-width: 100px; max-height: 60px;">
            </div>
            <div class="logo-info">
              <strong>${logo.nome}</strong><br>
              <small>Tipo: ${logo.tipo}</small><br>
              <small>Tamanho: ${(logo.tamanho / 1024).toFixed(1)} KB</small>
            </div>
            <button onclick="deleteLogo(${logo.id})" class="modern-btn" style="background: #ef4444; padding: 0.5rem;">
              🗑️
            </button>
          `;
          logosList.appendChild(logoDiv);
        });
      }
    }
  })
  .catch(error => console.error('Erro ao carregar logos:', error));
}

function deleteLogo(id) {
  if (confirm('Tem certeza que deseja remover este logo?')) {
    fetch(`backend/api/logos.php?id=${id}`, {
      method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Logo removido com sucesso!');
        loadLogos();
      } else {
        alert('Erro: ' + data.error);
      }
    })
    .catch(error => {
      console.error('Erro:', error);
      alert('Erro ao remover logo');
    });
  }
}
</script>

<style>
.logo-upload-section {
  border: 2px dashed #e2e8f0;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
}

.logos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.logo-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
}

.logo-preview {
  flex-shrink: 0;
}

.logo-info {
  flex-grow: 1;
}

.logo-form {
  background: #f8f9fa;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.logo-form .sap-form-group {
  margin-bottom: 1rem;
}

.logo-form .sap-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #374151;
}

.logo-form .sap-input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
}

.logo-form .modern-btn {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.logo-form .modern-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.logo-form .modern-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.logo-form .modern-btn-green {
  background: linear-gradient(135deg, #10b981 0%, #047857 100%);
}

.logo-form .modern-btn-green:hover {
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
}
</style>
