import mysql from 'mysql2/promise';
import dotenv from 'dotenv';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

dotenv.config({ path: path.join(__dirname, '..', '.env') });

// Configuração da conexão com o banco
const dbConfig = {
  host: process.env.DB_HOST,
  port: process.env.DB_PORT,
  user: process.env.DB_USER,
  password: process.env.DB_PASSWORD,
  database: process.env.DB_NAME
};

// Listar todos os toners
export const getToners = async (req, res) => {
  let connection;
  
  try {
    connection = await mysql.createConnection(dbConfig);
    
    const [rows] = await connection.execute(`
      SELECT 
        id,
        modelo,
        peso_cheio,
        peso_vazio,
        (peso_cheio - peso_vazio) as gramatura,
        capacidade_folhas,
        preco_toner,
        ((peso_cheio - peso_vazio) / capacidade_folhas) as gramatura_por_folha,
        (preco_toner / capacidade_folhas) as custo_por_folha,
        cor,
        tipo,
        created_at,
        updated_at
      FROM toners_cadastro 
      ORDER BY created_at DESC
    `);
    
    res.json({
      success: true,
      data: rows
    });
    
  } catch (error) {
    console.error('Erro ao buscar toners:', error);
    res.status(500).json({
      success: false,
      message: 'Erro interno do servidor',
      error: error.message
    });
  } finally {
    if (connection) {
      await connection.end();
    }
  }
};

// Buscar toner por ID
export const getTonerById = async (req, res) => {
  let connection;
  
  try {
    const { id } = req.params;
    connection = await mysql.createConnection(dbConfig);
    
    const [rows] = await connection.execute(`
      SELECT 
        id,
        modelo,
        peso_cheio,
        peso_vazio,
        (peso_cheio - peso_vazio) as gramatura,
        capacidade_folhas,
        preco_toner,
        ((peso_cheio - peso_vazio) / capacidade_folhas) as gramatura_por_folha,
        (preco_toner / capacidade_folhas) as custo_por_folha,
        cor,
        tipo,
        created_at,
        updated_at
      FROM toners_cadastro 
      WHERE id = ?
    `, [id]);
    
    if (rows.length === 0) {
      return res.status(404).json({
        success: false,
        message: 'Toner não encontrado'
      });
    }
    
    res.json({
      success: true,
      data: rows[0]
    });
    
  } catch (error) {
    console.error('Erro ao buscar toner:', error);
    res.status(500).json({
      success: false,
      message: 'Erro interno do servidor',
      error: error.message
    });
  } finally {
    if (connection) {
      await connection.end();
    }
  }
};

// Criar novo toner
export const createToner = async (req, res) => {
  let connection;
  
  try {
    const {
      modelo,
      peso_cheio,
      peso_vazio,
      capacidade_folhas,
      preco_toner,
      cor,
      tipo
    } = req.body;
    
    // Validações básicas
    if (!modelo || !peso_cheio || !peso_vazio || !capacidade_folhas || !preco_toner || !cor || !tipo) {
      return res.status(400).json({
        success: false,
        message: 'Todos os campos são obrigatórios'
      });
    }
    
    connection = await mysql.createConnection(dbConfig);
    
    const [result] = await connection.execute(`
      INSERT INTO toners_cadastro (
        id, modelo, peso_cheio, peso_vazio, capacidade_folhas, 
        preco_toner, cor, tipo, created_by
      ) VALUES (
        UUID(), ?, ?, ?, ?, ?, ?, ?, ?
      )
    `, [modelo, peso_cheio, peso_vazio, capacidade_folhas, preco_toner, cor, tipo, req.user?.id || null]);
    
    res.status(201).json({
      success: true,
      message: 'Toner cadastrado com sucesso',
      data: {
        id: result.insertId,
        modelo,
        peso_cheio,
        peso_vazio,
        gramatura: peso_cheio - peso_vazio,
        capacidade_folhas,
        preco_toner,
        gramatura_por_folha: (peso_cheio - peso_vazio) / capacidade_folhas,
        custo_por_folha: preco_toner / capacidade_folhas,
        cor,
        tipo
      }
    });
    
  } catch (error) {
    console.error('Erro ao criar toner:', error);
    res.status(500).json({
      success: false,
      message: 'Erro interno do servidor',
      error: error.message
    });
  } finally {
    if (connection) {
      await connection.end();
    }
  }
};

// Atualizar toner
export const updateToner = async (req, res) => {
  let connection;
  
  try {
    const { id } = req.params;
    const {
      modelo,
      peso_cheio,
      peso_vazio,
      capacidade_folhas,
      preco_toner,
      cor,
      tipo
    } = req.body;
    
    connection = await mysql.createConnection(dbConfig);
    
    const [result] = await connection.execute(`
      UPDATE toners_cadastro 
      SET modelo = ?, peso_cheio = ?, peso_vazio = ?, capacidade_folhas = ?,
          preco_toner = ?, cor = ?, tipo = ?, updated_at = CURRENT_TIMESTAMP
      WHERE id = ?
    `, [modelo, peso_cheio, peso_vazio, capacidade_folhas, preco_toner, cor, tipo, id]);
    
    if (result.affectedRows === 0) {
      return res.status(404).json({
        success: false,
        message: 'Toner não encontrado'
      });
    }
    
    res.json({
      success: true,
      message: 'Toner atualizado com sucesso'
    });
    
  } catch (error) {
    console.error('Erro ao atualizar toner:', error);
    res.status(500).json({
      success: false,
      message: 'Erro interno do servidor',
      error: error.message
    });
  } finally {
    if (connection) {
      await connection.end();
    }
  }
};

// Excluir toner
export const deleteToner = async (req, res) => {
  let connection;
  
  try {
    const { id } = req.params;
    connection = await mysql.createConnection(dbConfig);
    
    const [result] = await connection.execute(`
      DELETE FROM toners_cadastro WHERE id = ?
    `, [id]);
    
    if (result.affectedRows === 0) {
      return res.status(404).json({
        success: false,
        message: 'Toner não encontrado'
      });
    }
    
    res.json({
      success: true,
      message: 'Toner excluído com sucesso'
    });
    
  } catch (error) {
    console.error('Erro ao excluir toner:', error);
    res.status(500).json({
      success: false,
      message: 'Erro interno do servidor',
      error: error.message
    });
  } finally {
    if (connection) {
      await connection.end();
    }
  }
};
