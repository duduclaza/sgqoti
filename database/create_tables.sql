-- =====================================================
-- SGQ OTI - Queries para Criação das Tabelas
-- Sistema de Gestão da Qualidade
-- =====================================================

-- Tabela de Filiais
CREATE TABLE filiais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir filiais padrão
INSERT INTO filiais (nome) VALUES 
('Jundiaí'),
('Franca'),
('Santos'),
('Caçapava'),
('Uberlândia'),
('Uberaba');

-- =====================================================

-- Tabela de Departamentos
CREATE TABLE departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir departamentos padrão
INSERT INTO departamentos (nome) VALUES 
('Financeiro'),
('Faturamento'),
('Logística'),
('Compras'),
('Área Técnica'),
('Área Técnica ADM'),
('Comercial'),
('Implantação'),
('Implantação ADM'),
('Qualidade'),
('RH'),
('Licitações'),
('Gerência'),
('Limpeza'),
('Atendimento'),
('Controladoria'),
('Monitoramento');

-- =====================================================

-- Tabela de Fornecedores
CREATE TABLE fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    contato VARCHAR(200) NULL,
    rma TEXT NULL COMMENT 'Link, email ou telefone para RMA',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_ativo (ativo)
);

-- =====================================================

-- Tabela de Parâmetros de Retornados
CREATE TABLE parametros_retornados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    percentual_min DECIMAL(5,2) NOT NULL COMMENT 'Percentual mínimo',
    percentual_max DECIMAL(5,2) NULL COMMENT 'Percentual máximo (NULL para sem limite superior)',
    orientacao TEXT NOT NULL COMMENT 'Orientação para este parâmetro',
    cor_indicador VARCHAR(20) DEFAULT '#666666' COMMENT 'Cor para identificação visual',
    ordem_exibicao INT DEFAULT 0 COMMENT 'Ordem de exibição',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_percentual (percentual_min, percentual_max),
    INDEX idx_ativo (ativo)
);

-- Inserir parâmetros padrão de retornados
INSERT INTO parametros_retornados (nome, percentual_min, percentual_max, orientacao, cor_indicador, ordem_exibicao) VALUES 
(
    'Destino Descarte', 
    0.00, 
    5.00, 
    'Descarte o Toner.', 
    '#ef4444', 
    1
),
(
    'Uso Interno', 
    6.00, 
    39.00, 
    'Teste o Toner. Se a qualidade estiver boa, utilize internamente para testes. Se estiver ruim, descarte.', 
    '#eab308', 
    2
),
(
    'Estoque Semi Novo', 
    40.00, 
    89.00, 
    'Teste o Toner. Se a qualidade estiver boa, envie para o estoque como seminovo e marque a % na caixa para a logística ver. Se estiver ruim, solicite garantia.', 
    '#3b82f6', 
    3
),
(
    'Estoque Novo', 
    90.00, 
    NULL, 
    'Teste o Toner. Se a qualidade estiver boa, envie para o estoque como novo e marque na caixa que é novo para a logística ver. Se estiver ruim, solicite garantia.', 
    '#22c55e', 
    4
);

-- =====================================================

-- Tabela de Usuários (para futuro sistema de autenticação)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    senha VARCHAR(255) NULL COMMENT 'Hash da senha',
    filial_id INT NULL,
    departamento_id INT NULL,
    nivel_acesso ENUM('admin', 'gerente', 'usuario') DEFAULT 'usuario',
    ativo BOOLEAN DEFAULT TRUE,
    ultimo_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (filial_id) REFERENCES filiais(id) ON DELETE SET NULL,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL,
    INDEX idx_email (email),
    INDEX idx_ativo (ativo)
);

-- =====================================================

-- Tabela de Logs do Sistema (para auditoria)
CREATE TABLE logs_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    acao VARCHAR(100) NOT NULL,
    tabela_afetada VARCHAR(50) NULL,
    registro_id INT NULL,
    dados_anteriores JSON NULL,
    dados_novos JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario_id),
    INDEX idx_acao (acao),
    INDEX idx_data (created_at)
);

-- =====================================================
-- Views úteis para relatórios
-- =====================================================

-- View para parâmetros ativos ordenados
CREATE VIEW vw_parametros_ativos AS
SELECT 
    id,
    nome,
    percentual_min,
    percentual_max,
    orientacao,
    cor_indicador,
    CASE 
        WHEN percentual_max IS NULL THEN CONCAT('≥ ', percentual_min, '%')
        WHEN percentual_min = percentual_max THEN CONCAT(percentual_min, '%')
        ELSE CONCAT(percentual_min, '% - ', percentual_max, '%')
    END as faixa_percentual
FROM parametros_retornados 
WHERE ativo = TRUE 
ORDER BY ordem_exibicao;

-- View para fornecedores ativos
CREATE VIEW vw_fornecedores_ativos AS
SELECT 
    id,
    nome,
    contato,
    rma,
    created_at
FROM fornecedores 
WHERE ativo = TRUE 
ORDER BY nome;

-- =====================================================
-- Função para determinar parâmetro baseado no percentual
-- =====================================================

DELIMITER //
CREATE FUNCTION obter_parametro_por_percentual(percentual DECIMAL(5,2))
RETURNS VARCHAR(100)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE parametro_nome VARCHAR(100) DEFAULT 'Não definido';
    
    SELECT nome INTO parametro_nome
    FROM parametros_retornados
    WHERE ativo = TRUE
    AND percentual >= percentual_min
    AND (percentual_max IS NULL OR percentual <= percentual_max)
    ORDER BY ordem_exibicao
    LIMIT 1;
    
    RETURN parametro_nome;
END //
DELIMITER ;

-- =====================================================
-- Índices adicionais para performance
-- =====================================================

-- Índices compostos para consultas frequentes
CREATE INDEX idx_filiais_nome_ativo ON filiais(nome, ativo);
CREATE INDEX idx_departamentos_nome_ativo ON departamentos(nome, ativo);
CREATE INDEX idx_fornecedores_nome_ativo ON fornecedores(nome, ativo);

-- =====================================================
-- Comentários nas tabelas
-- =====================================================

ALTER TABLE filiais COMMENT = 'Cadastro das filiais da empresa';
ALTER TABLE departamentos COMMENT = 'Cadastro dos departamentos da empresa';
ALTER TABLE fornecedores COMMENT = 'Cadastro de fornecedores com informações de contato e RMA';
ALTER TABLE parametros_retornados COMMENT = 'Parâmetros para classificação de toners retornados baseado no percentual';
ALTER TABLE usuarios COMMENT = 'Usuários do sistema com controle de acesso';
ALTER TABLE logs_sistema COMMENT = 'Log de auditoria das ações realizadas no sistema';
