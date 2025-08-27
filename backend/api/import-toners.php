<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    if (!isset($_FILES['file'])) {
        throw new Exception('Nenhum arquivo enviado');
    }
    
    $file = $_FILES['file'];
    
    // Validar arquivo
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erro no upload do arquivo');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Arquivo muito grande. Máximo 5MB');
    }
    
    $allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, ['csv', 'xls', 'xlsx'])) {
        throw new Exception('Tipo de arquivo não permitido. Use CSV, XLS ou XLSX');
    }
    
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Processar arquivo baseado na extensão
    $data = [];
    
    if ($fileExtension === 'csv') {
        $data = processCSV($file['tmp_name']);
    } else {
        // Para XLS/XLSX seria necessário uma biblioteca como PhpSpreadsheet
        // Por simplicidade, vamos focar no CSV por enquanto
        throw new Exception('Formato XLS/XLSX não implementado ainda. Use CSV por favor.');
    }
    
    if (empty($data)) {
        throw new Exception('Nenhum dado válido encontrado no arquivo');
    }
    
    // Importar dados
    $imported = 0;
    $errors = [];
    
    foreach ($data as $index => $row) {
        try {
            // Validar campos obrigatórios
            if (empty($row['modelo']) || empty($row['cor']) || empty($row['tipo']) || 
                empty($row['capacidade']) || empty($row['peso_cheio']) || 
                empty($row['peso_vazio']) || empty($row['preco'])) {
                throw new Exception("Linha " . ($index + 2) . ": Campos obrigatórios em branco");
            }
            
            // Validar valores numéricos
            if (!is_numeric($row['capacidade']) || $row['capacidade'] <= 0) {
                throw new Exception("Linha " . ($index + 2) . ": Capacidade deve ser um número maior que zero");
            }
            
            if (!is_numeric($row['peso_cheio']) || !is_numeric($row['peso_vazio'])) {
                throw new Exception("Linha " . ($index + 2) . ": Pesos devem ser números");
            }
            
            if ($row['peso_cheio'] <= $row['peso_vazio']) {
                throw new Exception("Linha " . ($index + 2) . ": Peso cheio deve ser maior que peso vazio");
            }
            
            if (!is_numeric($row['preco']) || $row['preco'] <= 0) {
                throw new Exception("Linha " . ($index + 2) . ": Preço deve ser um número maior que zero");
            }
            
            // Validar valores de enum
            $coresValidas = ['Black', 'Cyan', 'Magenta', 'Yellow'];
            if (!in_array($row['cor'], $coresValidas)) {
                throw new Exception("Linha " . ($index + 2) . ": Cor deve ser: " . implode(', ', $coresValidas));
            }
            
            $tiposValidos = ['Compativel', 'Original', 'Remanufaturado'];
            if (!in_array($row['tipo'], $tiposValidos)) {
                throw new Exception("Linha " . ($index + 2) . ": Tipo deve ser: " . implode(', ', $tiposValidos));
            }
            
            // Inserir no banco
            $stmt = $pdo->prepare("
                INSERT INTO toners (modelo, peso_cheio, peso_vazio, capacidade, preco, cor, tipo) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $row['modelo'],
                $row['peso_cheio'],
                $row['peso_vazio'],
                $row['capacidade'],
                $row['preco'],
                $row['cor'],
                $row['tipo']
            ]);
            
            $imported++;
            
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
    
    if ($imported === 0 && !empty($errors)) {
        throw new Exception('Nenhum registro foi importado. Erros: ' . implode('; ', array_slice($errors, 0, 3)));
    }
    
    $response = [
        'success' => true,
        'message' => 'Importação concluída',
        'imported' => $imported,
        'total_rows' => count($data)
    ];
    
    if (!empty($errors)) {
        $response['warnings'] = array_slice($errors, 0, 5); // Máximo 5 avisos
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function processCSV($filePath) {
    $data = [];
    
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        $headers = [];
        $rowIndex = 0;
        
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if ($rowIndex === 0) {
                // Primeira linha são os cabeçalhos
                $headers = array_map('strtolower', $row);
                $headers = array_map('trim', $headers);
                
                // Mapear cabeçalhos para campos esperados
                $headerMap = [
                    'modelo' => 'modelo',
                    'cor' => 'cor',
                    'tipo' => 'tipo',
                    'capacidade' => 'capacidade',
                    'peso cheio (g)' => 'peso_cheio',
                    'peso cheio' => 'peso_cheio',
                    'peso vazio (g)' => 'peso_vazio',
                    'peso vazio' => 'peso_vazio',
                    'preço (r$)' => 'preco',
                    'preço' => 'preco',
                    'preco' => 'preco'
                ];
                
                $mappedHeaders = [];
                foreach ($headers as $header) {
                    $mappedHeaders[] = $headerMap[$header] ?? $header;
                }
                $headers = $mappedHeaders;
                
            } else {
                // Processar dados
                if (count($row) >= 7) { // Mínimo de colunas esperadas
                    $rowData = [];
                    for ($i = 0; $i < count($headers) && $i < count($row); $i++) {
                        $rowData[$headers[$i]] = trim($row[$i]);
                    }
                    
                    // Pular linhas vazias
                    if (!empty(array_filter($rowData))) {
                        $data[] = $rowData;
                    }
                }
            }
            $rowIndex++;
        }
        fclose($handle);
    }
    
    return $data;
}
?>
