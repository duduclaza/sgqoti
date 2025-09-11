import mysql from 'mysql2/promise';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import dotenv from 'dotenv';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

dotenv.config({ path: path.join(__dirname, '.env') });

async function createTonersTable() {
  let connection;
  
  try {
    // Conectar ao banco
    connection = await mysql.createConnection({
      host: process.env.DB_HOST,
      port: process.env.DB_PORT,
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      database: process.env.DB_NAME,
      multipleStatements: true
    });

    console.log('✅ Conectado ao banco MySQL da Hostinger!');

    // Ler o arquivo SQL
    const sqlFile = path.join(__dirname, 'sql', 'create_toners_cadastro.sql');
    const sqlScript = fs.readFileSync(sqlFile, 'utf8');

    // Executar o script SQL
    console.log('🔄 Criando tabela toners_cadastro...');
    await connection.execute(sqlScript);

    console.log('✅ Tabela toners_cadastro criada com sucesso!');
    console.log('📊 Dados de exemplo inseridos.');

  } catch (error) {
    console.error('❌ Erro ao criar tabela:');
    console.error('Código:', error.code);
    console.error('Mensagem:', error.message);
    console.error('SQL State:', error.sqlState);
    process.exit(1);
  } finally {
    if (connection) {
      await connection.end();
      console.log('🔌 Conexão fechada.');
    }
  }
}

createTonersTable();
