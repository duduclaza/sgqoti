const fs = require('fs');
const path = require('path');

/**
 * Script para limpar arquivos CSS com hash antigos
 * Mantém apenas o arquivo CSS referenciado no HTML
 */

function cleanupOldCSSFiles() {
  const projectDir = __dirname;
  const htmlFile = path.join(projectDir, 'index.html');
  
  try {
    // Lê o arquivo HTML para encontrar qual CSS está sendo usado
    const htmlContent = fs.readFileSync(htmlFile, 'utf8');
    const cssMatch = htmlContent.match(/href="(styles\.[a-f0-9]+\.css)"/);
    
    if (!cssMatch) {
      console.log('❌ Nenhum arquivo CSS com hash encontrado no HTML');
      return;
    }
    
    const currentCSSFile = cssMatch[1];
    console.log(`✅ CSS atual em uso: ${currentCSSFile}`);
    
    // Lista todos os arquivos CSS com hash no diretório
    const files = fs.readdirSync(projectDir);
    const hashedCSSFiles = files.filter(file => 
      file.match(/^styles\.[a-f0-9]+\.css$/) && file !== currentCSSFile
    );
    
    if (hashedCSSFiles.length === 0) {
      console.log('✅ Nenhum arquivo CSS antigo para remover');
      return;
    }
    
    // Remove arquivos CSS antigos
    hashedCSSFiles.forEach(file => {
      const filePath = path.join(projectDir, file);
      fs.unlinkSync(filePath);
      console.log(`🗑️  Removido: ${file}`);
    });
    
    console.log(`✅ Limpeza concluída! ${hashedCSSFiles.length} arquivo(s) removido(s)`);
    
  } catch (error) {
    console.error('❌ Erro durante a limpeza:', error.message);
  }
}

// Executa a limpeza se chamado diretamente
if (require.main === module) {
  cleanupOldCSSFiles();
}

module.exports = { cleanupOldCSSFiles };
