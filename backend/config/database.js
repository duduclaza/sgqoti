import mysql from 'mysql2/promise'
import dotenv from 'dotenv'

dotenv.config()

const dbConfig = {
  host: process.env.DB_HOST || 'localhost',
  port: process.env.DB_PORT || 3306,
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'gestao_qualidade',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
}

// Criar pool de conexões
export const pool = mysql.createPool(dbConfig)

// Função para testar conexão
export const testConnection = async () => {
  try {
    const connection = await pool.getConnection()
    console.log('✅ Conectado ao MySQL/MariaDB com sucesso!')
    connection.release()
    return true
  } catch (error) {
    console.error('❌ Erro ao conectar com MySQL/MariaDB:', error.message)
    return false
  }
}

// Função para executar queries
export const query = async (sql, params = []) => {
  try {
    const [results] = await pool.execute(sql, params)
    return results
  } catch (error) {
    console.error('Erro na query:', error.message)
    throw error
  }
}

export default { pool, testConnection, query }
