-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS gestao_qualidade;
USE gestao_qualidade;

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user', 'manager') DEFAULT 'user',
  created_by VARCHAR(36),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_role (role)
);

-- Criar tabela de items
CREATE TABLE IF NOT EXISTS items (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(100),
  quantity INT DEFAULT 0 CHECK (quantity >= 0),
  minimum_stock INT DEFAULT 0 CHECK (minimum_stock >= 0),
  created_by VARCHAR(36),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_category (category),
  INDEX idx_name (name)
);

-- Criar tabela de toners
CREATE TABLE IF NOT EXISTS toners (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  modelo VARCHAR(255) NOT NULL,
  cor VARCHAR(50) NOT NULL,
  estoque INT DEFAULT 0 CHECK (estoque >= 0),
  minimo INT DEFAULT 0 CHECK (minimo >= 0),
  status ENUM('OK', 'Baixo', 'Crítico') DEFAULT 'OK',
  created_by VARCHAR(36),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_modelo (modelo),
  INDEX idx_status (status)
);

-- Criar tabela de homologações
CREATE TABLE IF NOT EXISTS homologacoes (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  titulo VARCHAR(255) NOT NULL,
  descricao TEXT,
  status ENUM('Pendente', 'Em Análise', 'Aprovado', 'Rejeitado') DEFAULT 'Pendente',
  data_inicio DATE,
  data_fim DATE,
  responsavel VARCHAR(255),
  created_by VARCHAR(36),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_status (status),
  INDEX idx_data_inicio (data_inicio)
);

-- Criar tabela de amostragens
CREATE TABLE IF NOT EXISTS amostragens (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  codigo_amostra VARCHAR(100) UNIQUE NOT NULL,
  produto VARCHAR(255) NOT NULL,
  lote VARCHAR(100),
  data_coleta DATE NOT NULL,
  responsavel_coleta VARCHAR(255),
  status ENUM('Coletada', 'Em Análise', 'Aprovada', 'Reprovada') DEFAULT 'Coletada',
  observacoes TEXT,
  created_by VARCHAR(36),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_codigo_amostra (codigo_amostra),
  INDEX idx_status (status)
);

-- Criar tabela de garantias
CREATE TABLE IF NOT EXISTS garantias (
  id VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
  produto VARCHAR(255) NOT NULL,
  numero_serie VARCHAR(100),
  data_compra DATE,
  data_vencimento DATE,
  fornecedor VARCHAR(255),
  status ENUM('Ativa', 'Vencida', 'Utilizada') DEFAULT 'Ativa',
  observacoes TEXT,
  created_by VARCHAR(36),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_produto (produto),
  INDEX idx_status (status),
  INDEX idx_data_vencimento (data_vencimento)
);

-- Inserir usuário administrador padrão
INSERT INTO users (id, email, password_hash, full_name, role) 
VALUES (
  UUID(),
  'admin@sistema.com',
  '$2a$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
  'Administrador do Sistema',
  'admin'
) ON DUPLICATE KEY UPDATE email = email;

-- Inserir dados de exemplo para toners
INSERT INTO toners (id, modelo, cor, estoque, minimo, status, created_by) VALUES
(UUID(), 'HP CF280A', 'Preto', 15, 5, 'OK', (SELECT id FROM users WHERE email = 'admin@sistema.com' LIMIT 1)),
(UUID(), 'Canon CRG-045', 'Ciano', 3, 5, 'Baixo', (SELECT id FROM users WHERE email = 'admin@sistema.com' LIMIT 1)),
(UUID(), 'Xerox 106R03623', 'Magenta', 8, 5, 'OK', (SELECT id FROM users WHERE email = 'admin@sistema.com' LIMIT 1)),
(UUID(), 'Brother TN-421', 'Amarelo', 2, 5, 'Crítico', (SELECT id FROM users WHERE email = 'admin@sistema.com' LIMIT 1)),
(UUID(), 'Samsung MLT-D111S', 'Preto', 12, 5, 'OK', (SELECT id FROM users WHERE email = 'admin@sistema.com' LIMIT 1))
ON DUPLICATE KEY UPDATE modelo = modelo;
