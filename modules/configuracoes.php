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
            <h3 class="sap-card-title">Configurações do Sistema</h3>
          </div>
          <div class="sap-card-content">
            <div class="sap-form">
              <div class="sap-form-group">
                <label class="sap-label">Nome do Sistema</label>
                <input type="text" class="sap-input" value="SGQ OTI" readonly>
              </div>
              <div class="sap-form-group">
                <label class="sap-label">Versão</label>
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
</script>
