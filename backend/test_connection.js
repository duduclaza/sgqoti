import mysql from 'mysql2/promise';
import dotenv from 'dotenv';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

dotenv.config({ path: path.join(__dirname, '.env') });

async function testConnection() {
  try {
    console.log('🔄 Testando conexão com:', process.env.DB_HOST);
    console.log('📊 Banco:', process.env.DB_NAME);
    console.log('👤 Usuário:', process.env.DB_USER);
    
    const connection = await mysql.createConnection({
      host: process.env.DB_HOST,
      port: parseInt(process.env.DB_PORT) || 3306,
      user: process.env.DB_USER,
      password: process.env.DB_PASSWORD,
      database: process.env.DB_NAME,
      connectTimeout: 10000,
      acquireTimeout: 10000,
      timeout: 10000
    });

    console.log('✅ Conexão estabelecida com sucesso!');
    
    // Testar uma query simples
    const [rows] = await connection.execute('SELECT 1 as test');
    console.log('✅ Query de teste executada:', rows);
    
    await connection.end();
    console.log('🔌 Conexão fechada.');
    
  } catch (error) {
    console.error('❌ Erro de conexão:');
    console.error('Código:', error.code);
    console.error('Mensagem:', error.message);
    console.error('Host:', error.address);
    console.error('Porta:', error.port);
  }
}

testConnection();
