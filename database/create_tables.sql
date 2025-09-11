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

-- =====================================================
-- Tabelas para Controle de Toners
-- =====================================================

-- Tabela para controle de toners
CREATE TABLE IF NOT EXISTS toners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    peso_cheio DECIMAL(8,3) NOT NULL COMMENT 'Peso em gramas',
    peso_vazio DECIMAL(8,3) NOT NULL COMMENT 'Peso em gramas',
    gramatura DECIMAL(8,3) GENERATED ALWAYS AS (peso_cheio - peso_vazio) STORED COMMENT 'Calculado automaticamente',
    capacidade_folhas INT NOT NULL,
    preco_toner DECIMAL(10,2) NOT NULL,
    gramatura_por_folha DECIMAL(8,4) GENERATED ALWAYS AS (gramatura / capacidade_folhas) STORED COMMENT 'Calculado automaticamente',
    custo_por_folha DECIMAL(8,4) GENERATED ALWAYS AS (preco_toner / capacidade_folhas) STORED COMMENT 'Calculado automaticamente',
    cor ENUM('Yellow', 'Magenta', 'Cyan', 'Black') NOT NULL,
    tipo ENUM('Original', 'Compativel', 'Remanufaturado') NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    usuario_cadastro INT DEFAULT 1,
    FOREIGN KEY (usuario_cadastro) REFERENCES usuarios(id),
    INDEX idx_modelo (modelo),
    INDEX idx_cor (cor),
    INDEX idx_tipo (tipo),
    INDEX idx_ativo (ativo)
);

-- Inserir alguns toners de exemplo
INSERT INTO toners (modelo, peso_cheio, peso_vazio, capacidade_folhas, preco_toner, cor, tipo) VALUES
('HP CF410A', 850.5, 125.2, 2300, 89.90, 'Black', 'Original'),
('HP CF411A', 820.3, 120.1, 2300, 95.50, 'Cyan', 'Original'),
('HP CF412A', 815.7, 118.9, 2300, 95.50, 'Yellow', 'Original'),
('HP CF413A', 818.2, 119.5, 2300, 95.50, 'Magenta', 'Original'),
('Samsung MLT-D111S', 650.8, 95.3, 1000, 45.90, 'Black', 'Compativel'),
('Canon 045H', 920.4, 140.2, 2200, 125.00, 'Black', 'Original');

-- Tabela para registro de retornados
CREATE TABLE IF NOT EXISTS toners_retornados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    toner_id INT NOT NULL,
    data_retorno DATE NOT NULL,
    peso_retornado DECIMAL(8,3) NOT NULL COMMENT 'Peso em gramas',
    percentual_uso DECIMAL(5,2) GENERATED ALWAYS AS (
        CASE 
            WHEN (SELECT gramatura FROM toners WHERE id = toner_id) > 0 
            THEN ((SELECT peso_cheio FROM toners WHERE id = toner_id) - peso_retornado) / (SELECT gramatura FROM toners WHERE id = toner_id) * 100
            ELSE 0 
        END
    ) STORED COMMENT 'Calculado automaticamente',
    folhas_utilizadas INT GENERATED ALWAYS AS (
        CASE 
            WHEN (SELECT gramatura_por_folha FROM toners WHERE id = toner_id) > 0 
            THEN ROUND(((SELECT peso_cheio FROM toners WHERE id = toner_id) - peso_retornado) / (SELECT gramatura_por_folha FROM toners WHERE id = toner_id))
            ELSE 0 
        END
    ) STORED COMMENT 'Calculado automaticamente',
    custo_utilizado DECIMAL(10,2) GENERATED ALWAYS AS (
        CASE 
            WHEN (SELECT capacidade_folhas FROM toners WHERE id = toner_id) > 0 
            THEN folhas_utilizadas * (SELECT custo_por_folha FROM toners WHERE id = toner_id)
            ELSE 0 
        END
    ) STORED COMMENT 'Calculado automaticamente',
    observacoes TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_cadastro INT DEFAULT 1,
    FOREIGN KEY (toner_id) REFERENCES toners(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_cadastro) REFERENCES usuarios(id),
    INDEX idx_data_retorno (data_retorno),
    INDEX idx_toner_id (toner_id)
);

ALTER TABLE filiais COMMENT = 'Cadastro das filiais da empresa';
ALTER TABLE departamentos COMMENT = 'Cadastro dos departamentos da empresa';
ALTER TABLE fornecedores COMMENT = 'Cadastro de fornecedores com informações de contato e RMA';
ALTER TABLE parametros_retornados COMMENT = 'Parâmetros para classificação de toners retornados baseado no percentual';
ALTER TABLE usuarios COMMENT = 'Usuários do sistema com controle de acesso';
ALTER TABLE logs_sistema COMMENT = 'Log de auditoria das ações realizadas no sistema';
ALTER TABLE toners COMMENT = 'Cadastro de toners com cálculos automáticos de gramatura e custos';
ALTER TABLE toners_retornados COMMENT = 'Registro de toners retornados com cálculos de uso e custos';
