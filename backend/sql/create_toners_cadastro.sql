-- Criar tabela de cadastro de toners
CREATE TABLE IF NOT EXISTS toners_cadastro (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  modelo VARCHAR(255) NOT NULL,
  peso_cheio DECIMAL(10,3) NOT NULL,
  peso_vazio DECIMAL(10,3) NOT NULL,
  capacidade_folhas INT NOT NULL,
  preco_toner DECIMAL(10,2) NOT NULL,
  cor ENUM('Yellow', 'Magenta', 'Cyan', 'Black') NOT NULL,
  tipo ENUM('Original', 'Compativel', 'Remanufaturado') NOT NULL,
  created_by VARCHAR(36),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_modelo (modelo),
  INDEX idx_cor (cor),
  INDEX idx_tipo (tipo)
);
