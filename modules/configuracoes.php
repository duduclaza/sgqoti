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
      <a href="?module=configuracoes&aba=logos" class="sap-tab-link <?= $aba == 'logos' ? 'active' : '' ?>">
        <span class="sap-tab-icon">🖼️</span>Logos
      </a>
    </div>
    
    <div class="sap-tab-content">
      <?php if ($aba == 'sistema'): ?>
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
        
      <?php elseif ($aba == 'logos'): ?>
        <!-- Aba Logos -->
        <div class="sap-card">
          <div class="sap-card-header">
            <h3>Upload e Preview de Logo (PNG)</h3>
          </div>
          <div class="sap-card-content">
            <form id="logo-upload-form" enctype="multipart/form-data" class="logo-form">
              <div class="sap-form-group">
                <label class="sap-label">Selecione um arquivo PNG (máx 2MB)</label>
                <input type="file" name="logo" id="logo-file" accept="image/png" class="sap-input" required>
              </div>
              <button type="submit" class="modern-btn modern-btn-primary">
                <span>📤</span> Enviar Logo
              </button>
            </form>
            <div class="logo-preview-section" style="margin-top:2rem;">
              <h4>Preview Atual</h4>
              <img id="logo-preview" src="/assets/images/logo-preview.png" alt="Logo Preview" style="max-width:200px;max-height:120px;border:1px solid #e2e8f0;border-radius:8px;display:block;">
            </div>
          </div>
        </div>

      <?php elseif ($aba == 'empresa'): ?>
        
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
            // Simplified logo upload flow using backend/api/logo-manager.php
            function initLogoUploadSimple() {
              const form = document.getElementById('logo-upload-form');
              if (!form) return;

              form.addEventListener('submit', function(e) {
                e.preventDefault();
                const input = document.getElementById('logo-file');
                if (!input || !input.files || !input.files[0]) {
                  alert('Selecione um arquivo PNG.');
                  return;
                }
                const file = input.files[0];
                if (file.type !== 'image/png') {
                  alert('Apenas arquivos PNG são permitidos.');
                  return;
                }
                if (file.size > 2 * 1024 * 1024) {
                  alert('Arquivo muito grande. Máx 2MB.');
                  return;
                }

                const fd = new FormData();
                fd.append('logo', file);

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn ? submitBtn.innerHTML : null;
                if (submitBtn) {
                  submitBtn.disabled = true;
                  submitBtn.innerHTML = '<span>⏳</span> Enviando...';
                }

                fetch('backend/api/logo-manager.php', { method: 'POST', body: fd })
                  .then(r => r.json())
                  .then(res => {
                    if (res.success) {
                      alert('Logo enviado com sucesso!');
                      // Atualizar preview local
                      const preview = document.getElementById('logo-preview');
                      if (preview) {
                        preview.src = res.url + '?t=' + new Date().getTime();
                        preview.style.display = 'block';
                      }
                      // Atualizar sidebar global se houver
                      try {
                        const sidebarLogo = window.parent?.document?.getElementById('sidebar-logo') ||
                                            window.top?.document?.getElementById('sidebar-logo') ||
                                            document.getElementById('sidebar-logo');
                        if (sidebarLogo) {
                          sidebarLogo.src = 'backend/api/logo-manager.php?download=1&t=' + new Date().getTime();
                          sidebarLogo.style.display = 'block';
                        }
                      } catch (e) { /* ignore */ }
                      form.reset();
                    } else {
                      alert('Erro: ' + (res.error || 'Falha no upload'));
                    }
                  })
                  .catch(() => alert('Erro ao enviar arquivo'))
                  .finally(() => {
                    if (submitBtn && originalText) {
                      submitBtn.innerHTML = originalText;
                      submitBtn.disabled = false;
                    }
                  });
              });
            }
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
  
  if (file.size > 5 * 1024 * 1024) {
    alert('Arquivo muito grande! Máximo 5MB.');
    return;
  }
  
  const reader = new FileReader();
  reader.onload = function(e) {
    const imgSrc = e.target.result;
    
    // Update preview image
    const previewImg = document.getElementById(type + '-logo-preview');
    if (previewImg) {
      previewImg.src = imgSrc;
      previewImg.style.display = 'block';
    }
    
    // Update preview sections
    if (type === 'menu' || type === 'sidebar') {
      const menuLogo = document.getElementById('preview-menu-logo');
      if (menuLogo) menuLogo.src = imgSrc;
    } else if (type === 'login' || type === 'header') {
      const loginLogo = document.getElementById('preview-login-logo');
      if (loginLogo) loginLogo.src = imgSrc;
    }
    
    // Mapear tipos internos para tipos da API
    const apiType = (type === 'menu') ? 'sidebar' : (type === 'login' ? 'header' : type);
    
    // Upload to server
    uploadLogoToServer(file, apiType);
  };
  reader.readAsDataURL(file);
}

function uploadLogoToServer(file, type) {
  const formData = new FormData();
  formData.append('logo', file);
  formData.append('tipo', type);
  formData.append('nome', 'Logo ' + type.charAt(0).toUpperCase() + type.slice(1));
  
  // Mostrar indicador de carregamento se houver um botão relacionado
  const button = document.querySelector(`button[data-logo-type="${type}"]`);
  const originalText = button ? button.innerHTML : null;
  if (button) {
    button.innerHTML = '<span>⏳</span> Enviando...';
    button.disabled = true;
  }
  
  fetch('backend/api/logo-manager.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(result => {
    if (result.success) {
      console.log('Logo uploaded successfully');
      alert('Logo enviado com sucesso!');
      
      // Recarregar logos na lista
      if (typeof loadLogos === 'function') {
        loadLogos();
      }
      
      // Atualizar logo no sistema principal (sidebar/login) se necessário
      if (type === 'sidebar') {
        // Tentar atualizar logo no sidebar da página principal
        try {
          const sidebarLogo = window.parent.document?.getElementById('sidebar-logo') || 
                              window.top.document?.getElementById('sidebar-logo') ||
                              document.getElementById('sidebar-logo');
          
          if (sidebarLogo) {
            const timestamp = new Date().getTime();
            sidebarLogo.src = result.url + '&t=' + timestamp;
            sidebarLogo.style.display = 'block';
            
            // Esconder fallback se existir
            const logoFallback = window.parent.document?.getElementById('logo-fallback') || 
                                window.top.document?.getElementById('logo-fallback') ||
                                document.getElementById('logo-fallback');
            if (logoFallback) logoFallback.style.display = 'none';
          }
        } catch (e) {
          console.log('Não foi possível atualizar logo do sidebar automaticamente');
        }
        
        // Sugerir reload da página
        setTimeout(() => {
          if (confirm('Logo enviado com sucesso! Deseja recarregar a página para ver as alterações no menu lateral?')) {
            window.top.location.reload();
          }
        }, 1000);
      }
      
      // Atualizar preview com URL do servidor
      const timestamp = new Date().getTime();
      
      // Mapear tipos da API para tipos internos
      const internalTypes = [];
      if (type === 'sidebar') internalTypes.push('menu', 'sidebar');
      else if (type === 'header') internalTypes.push('login', 'header');
      else internalTypes.push(type);
      
      // Atualizar todos os previews relevantes
      internalTypes.forEach(internalType => {
        // Atualizar preview da imagem
        const previewImg = document.getElementById(internalType + '-logo-preview');
        if (previewImg) {
          previewImg.src = result.url + '&t=' + timestamp;
          previewImg.style.display = 'block';
        }
        
        // Atualizar preview sections
        if (internalType === 'menu' || internalType === 'sidebar') {
          const menuLogo = document.getElementById('preview-menu-logo');
          if (menuLogo) menuLogo.src = result.url + '&t=' + timestamp;
        } else if (internalType === 'login' || internalType === 'header') {
          const loginLogo = document.getElementById('preview-login-logo');
          if (loginLogo) loginLogo.src = result.url + '&t=' + timestamp;
        }
      });
    } else {
      alert('Erro ao fazer upload: ' + result.error);
    }
  })
  .catch(error => {
    console.error('Upload error:', error);
    alert('Erro ao fazer upload do logo');
  })
  .finally(() => {
    // Restaurar texto original do botão
    if (button && originalText) {
      button.innerHTML = originalText;
      button.disabled = false;
    }
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

// Comentário para manter a estrutura do código
// O event listener unificado foi movido para o final do arquivo

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

// Event listener unificado para inicialização da página
document.addEventListener('DOMContentLoaded', function() {
  // 1. Inicializar configurações de logo
  // Initialize slider values
  updateLogoSize('menu');
  updateLogoSize('login');
  
  // Update preview text
  updatePreviewText();
  
  // 2. Configurar uploads de logo
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
  
  // Configurar os inputs de upload da aba empresa
  const menuLogoInput = document.getElementById('menu-logo-input');
  if (menuLogoInput) {
    menuLogoInput.onchange = function() {
      handleLogoUpload(this, 'menu');
    };
  }
  
  const loginLogoInput = document.getElementById('login-logo-input');
  if (loginLogoInput) {
    loginLogoInput.onchange = function() {
      handleLogoUpload(this, 'login');
    };
  }
  
  // Carregar logos existentes
  if (document.getElementById('logos-list')) {
    loadLogos();
  }
  
  // 3. Carregar dados específicos da aba
  // Load filiais if on filiais tab
  if (CFG_ABA === 'filiais') {
    loadBranchesCfg();
  }
  
  // Load user data if on users tab
  if (CFG_ABA === 'usuarios') {
    loadUsers();
  }
});

// Função unificada para upload de logo a partir de um formulário
function uploadLogo(form, tipo) {
  const formData = new FormData(form);
  const file = formData.get('logo');
  
  if (!file || file.size === 0) {
    alert('Selecione um arquivo de imagem válido.');
    return;
  }
  
  // Adicionar atributo data-logo-type ao botão para identificação
  const button = form.querySelector('button[type="submit"]');
  if (button) {
    button.setAttribute('data-logo-type', tipo);
  }
  
  // Usar a função unificada de upload
  uploadLogoToServer(file, tipo);
  
  // Resetar o formulário
  form.reset();
}

function loadLogosSimple() {
  // Retorna apenas se houver um elemento para exibir
  const logosList = document.getElementById('logos-list');
  if (!logosList) return;
  fetch('backend/api/logo-manager.php')
    .then(r => r.json())
    .then(res => {
      if (res.success && res.url) {
        logosList.innerHTML = `<div class="logo-item">
          <div class="logo-preview"><img src="${res.url}" style="max-width:100px;max-height:60px;"/></div>
          <div class="logo-info"><strong>Logo atual</strong><br><small>Preview</small></div>
        </div>`;
      } else {
        logosList.innerHTML = '<div class="text-center">Nenhum logo cadastrado</div>';
      }
    })
    .catch(() => { logosList.innerHTML = '<div class="text-center">Erro ao carregar logos</div>'; });
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
