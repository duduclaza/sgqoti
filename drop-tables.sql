-- Script para remover tabelas toners e movimentacoes_estoque
-- Execute este script no banco u230868210_sgqoti para limpar as tabelas antigas

-- Remover tabela movimentacoes_estoque (tem foreign key, deve ser removida primeiro)
DROP TABLE IF EXISTS movimentacoes_estoque;

-- Remover tabela toners
DROP TABLE IF EXISTS toners;

-- Verificar tabelas restantes
SHOW TABLES;
