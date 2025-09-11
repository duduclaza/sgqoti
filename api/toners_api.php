<?php
session_start();
header('Content-Type: application/json');

// Verificar se o usuário está logado (mesma chave usada no sistema)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

require_once '../config/database.php';

// Carrega PhpSpreadsheet apenas para ações que precisam
$needsSpreadsheet = in_array($_REQUEST['action'] ?? '', ['download_template', 'import_excel', 'export_excel']);
if ($needsSpreadsheet && file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}

$db = Database::getInstance();
$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'get_toners':
            $query = "SELECT * FROM toners WHERE ativo = 1 ORDER BY modelo";
            $result = $db->query($query);
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'get_toner':
            $id = $_GET['id'] ?? 0;
            $query = "SELECT * FROM toners WHERE id = ? AND ativo = 1";
            $result = $db->query($query, [$id]);
            if (!empty($result)) {
                echo json_encode(['success' => true, 'data' => $result[0]]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Toner não encontrado']);
            }
            break;

        case 'save_toner':
            $modelo = $_POST['modelo'] ?? '';
            $peso_cheio = $_POST['peso_cheio'] ?? 0;
            $peso_vazio = $_POST['peso_vazio'] ?? 0;
            $capacidade_folhas = $_POST['capacidade_folhas'] ?? 0;
            $preco_toner = $_POST['preco_toner'] ?? 0;
            $cor = $_POST['cor'] ?? '';
            $tipo = $_POST['tipo'] ?? '';

            if (empty($modelo) || empty($cor) || empty($tipo) || $peso_cheio <= 0 || $peso_vazio <= 0 || $capacidade_folhas <= 0 || $preco_toner <= 0) {
                echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
                break;
            }

            if ($peso_cheio <= $peso_vazio) {
                echo json_encode(['success' => false, 'message' => 'Peso cheio deve ser maior que peso vazio']);
                break;
            }

            // Verificar se já existe um toner com o mesmo modelo
            $checkQuery = "SELECT id FROM toners WHERE modelo = ? AND ativo = 1";
            $existing = $db->query($checkQuery, [$modelo]);
            if (!empty($existing)) {
                echo json_encode(['success' => false, 'message' => 'Já existe um toner cadastrado com este modelo']);
                break;
            }

            $query = "INSERT INTO toners (modelo, peso_cheio, peso_vazio, capacidade_folhas, preco_toner, cor, tipo, usuario_cadastro) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $result = $db->insert($query, [
                $modelo, $peso_cheio, $peso_vazio, $capacidade_folhas, 
                $preco_toner, $cor, $tipo, $_SESSION['usuario_id'] ?? 1
            ]);

            if ($result) {
                // Log da ação
                $logQuery = "INSERT INTO logs_sistema (usuario_id, acao, tabela_afetada, descricao) VALUES (?, 'CREATE', 'toners', ?)";
                $db->insert($logQuery, [$_SESSION['usuario_id'] ?? 1, "Toner cadastrado: {$modelo}"]);
                
                echo json_encode(['success' => true, 'message' => 'Toner cadastrado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar toner']);
            }
            break;

        case 'update_toner':
            $id = $_POST['id'] ?? 0;
            $modelo = $_POST['modelo'] ?? '';
            $peso_cheio = $_POST['peso_cheio'] ?? 0;
            $peso_vazio = $_POST['peso_vazio'] ?? 0;
            $capacidade_folhas = $_POST['capacidade_folhas'] ?? 0;
            $preco_toner = $_POST['preco_toner'] ?? 0;
            $cor = $_POST['cor'] ?? '';
            $tipo = $_POST['tipo'] ?? '';

            if (empty($modelo) || empty($cor) || empty($tipo) || $peso_cheio <= 0 || $peso_vazio <= 0 || $capacidade_folhas <= 0 || $preco_toner <= 0) {
                echo json_encode(['success' => false, 'message' => 'Todos os campos são obrigatórios']);
                break;
            }

            if ($peso_cheio <= $peso_vazio) {
                echo json_encode(['success' => false, 'message' => 'Peso cheio deve ser maior que peso vazio']);
                break;
            }

            // Verificar se já existe outro toner com o mesmo modelo
            $checkQuery = "SELECT id FROM toners WHERE modelo = ? AND id != ? AND ativo = 1";
            $existing = $db->query($checkQuery, [$modelo, $id]);
            if (!empty($existing)) {
                echo json_encode(['success' => false, 'message' => 'Já existe outro toner cadastrado com este modelo']);
                break;
            }

            $query = "UPDATE toners SET modelo = ?, peso_cheio = ?, peso_vazio = ?, capacidade_folhas = ?, 
                     preco_toner = ?, cor = ?, tipo = ?, data_atualizacao = CURRENT_TIMESTAMP 
                     WHERE id = ? AND ativo = 1";
            
            $result = $db->update($query, [
                $modelo, $peso_cheio, $peso_vazio, $capacidade_folhas, 
                $preco_toner, $cor, $tipo, $id
            ]);

            if ($result) {
                // Log da ação
                $logQuery = "INSERT INTO logs_sistema (usuario_id, acao, tabela_afetada, descricao) VALUES (?, 'UPDATE', 'toners', ?)";
                $db->insert($logQuery, [$_SESSION['usuario_id'] ?? 1, "Toner atualizado: {$modelo}"]);
                
                echo json_encode(['success' => true, 'message' => 'Toner atualizado com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao atualizar toner']);
            }
            break;

        case 'delete_toner':
            $id = $_POST['id'] ?? 0;
            
            // Verificar se o toner existe
            $checkQuery = "SELECT modelo FROM toners WHERE id = ? AND ativo = 1";
            $toner = $db->query($checkQuery, [$id]);
            if (empty($toner)) {
                echo json_encode(['success' => false, 'message' => 'Toner não encontrado']);
                break;
            }

            // Verificar se existem retornados vinculados
            $retornadosQuery = "SELECT COUNT(*) as total FROM toners_retornados WHERE toner_id = ?";
            $retornados = $db->query($retornadosQuery, [$id]);
            if ($retornados[0]['total'] > 0) {
                echo json_encode(['success' => false, 'message' => 'Não é possível excluir. Existem registros de retornados vinculados a este toner']);
                break;
            }

            // Soft delete
            $query = "UPDATE toners SET ativo = 0, data_atualizacao = CURRENT_TIMESTAMP WHERE id = ?";
            $result = $db->update($query, [$id]);

            if ($result) {
                // Log da ação
                $logQuery = "INSERT INTO logs_sistema (usuario_id, acao, tabela_afetada, descricao) VALUES (?, 'DELETE', 'toners', ?)";
                $db->insert($logQuery, [$_SESSION['usuario_id'] ?? 1, "Toner excluído: {$toner[0]['modelo']}"]);
                
                echo json_encode(['success' => true, 'message' => 'Toner excluído com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao excluir toner']);
            }
            break;

        case 'download_template':
            if (!$needsSpreadsheet) {
                echo json_encode(['success' => false, 'message' => 'PhpSpreadsheet não disponível']);
                break;
            }
            // Criar planilha template
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Cabeçalhos
            $headers = [
                'A1' => 'Modelo',
                'B1' => 'Peso Cheio (g)',
                'C1' => 'Peso Vazio (g)',
                'D1' => 'Capacidade Folhas',
                'E1' => 'Preço Toner (R$)',
                'F1' => 'Cor',
                'G1' => 'Tipo'
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle($cell)->getFill()->getStartColor()->setRGB('E3F2FD');
            }
            
            // Exemplos
            $sheet->setCellValue('A2', 'HP CF410A');
            $sheet->setCellValue('B2', '850.5');
            $sheet->setCellValue('C2', '125.2');
            $sheet->setCellValue('D2', '2300');
            $sheet->setCellValue('E2', '89.90');
            $sheet->setCellValue('F2', 'Black');
            $sheet->setCellValue('G2', 'Original');
            
            // Ajustar largura das colunas
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Adicionar validação de dados
            $validation = $sheet->getCell('F2')->getDataValidation();
            $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Erro de entrada');
            $validation->setError('Valor não está na lista');
            $validation->setPromptTitle('Selecione a cor');
            $validation->setPrompt('Escolha uma das opções disponíveis');
            $validation->setFormula1('"Yellow,Magenta,Cyan,Black"');
            
            $validation2 = $sheet->getCell('G2')->getDataValidation();
            $validation2->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $validation2->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $validation2->setAllowBlank(false);
            $validation2->setShowInputMessage(true);
            $validation2->setShowErrorMessage(true);
            $validation2->setShowDropDown(true);
            $validation2->setErrorTitle('Erro de entrada');
            $validation2->setError('Valor não está na lista');
            $validation2->setPromptTitle('Selecione o tipo');
            $validation2->setPrompt('Escolha uma das opções disponíveis');
            $validation2->setFormula1('"Original,Compativel,Remanufaturado"');
            
            // Headers para download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="template_toners.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        case 'import_excel':
            if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'message' => 'Erro no upload do arquivo']);
                break;
            }
            
            $uploadedFile = $_FILES['excel_file']['tmp_name'];
            
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($uploadedFile);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                
                $imported = 0;
                $errors = [];
                
                for ($row = 2; $row <= $highestRow; $row++) {
                    $modelo = trim($sheet->getCell("A{$row}")->getValue());
                    $peso_cheio = $sheet->getCell("B{$row}")->getValue();
                    $peso_vazio = $sheet->getCell("C{$row}")->getValue();
                    $capacidade_folhas = $sheet->getCell("D{$row}")->getValue();
                    $preco_toner = $sheet->getCell("E{$row}")->getValue();
                    $cor = trim($sheet->getCell("F{$row}")->getValue());
                    $tipo = trim($sheet->getCell("G{$row}")->getValue());
                    
                    // Validações
                    if (empty($modelo)) continue;
                    
                    if (empty($cor) || !in_array($cor, ['Yellow', 'Magenta', 'Cyan', 'Black'])) {
                        $errors[] = "Linha {$row}: Cor inválida ({$cor})";
                        continue;
                    }
                    
                    if (empty($tipo) || !in_array($tipo, ['Original', 'Compativel', 'Remanufaturado'])) {
                        $errors[] = "Linha {$row}: Tipo inválido ({$tipo})";
                        continue;
                    }
                    
                    if ($peso_cheio <= 0 || $peso_vazio <= 0 || $capacidade_folhas <= 0 || $preco_toner <= 0) {
                        $errors[] = "Linha {$row}: Valores numéricos devem ser maiores que zero";
                        continue;
                    }
                    
                    if ($peso_cheio <= $peso_vazio) {
                        $errors[] = "Linha {$row}: Peso cheio deve ser maior que peso vazio";
                        continue;
                    }
                    
                    // Verificar se já existe
                    $checkQuery = "SELECT id FROM toners WHERE modelo = ? AND ativo = 1";
                    $existing = $db->query($checkQuery, [$modelo]);
                    if (!empty($existing)) {
                        $errors[] = "Linha {$row}: Modelo {$modelo} já existe";
                        continue;
                    }
                    
                    // Inserir
                    $query = "INSERT INTO toners (modelo, peso_cheio, peso_vazio, capacidade_folhas, preco_toner, cor, tipo, usuario_cadastro) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $result = $db->insert($query, [
                        $modelo, $peso_cheio, $peso_vazio, $capacidade_folhas, 
                        $preco_toner, $cor, $tipo, $_SESSION['usuario_id'] ?? 1
                    ]);
                    
                    if ($result) {
                        $imported++;
                    } else {
                        $errors[] = "Linha {$row}: Erro ao inserir {$modelo}";
                    }
                }
                
                // Log da ação
                $logQuery = "INSERT INTO logs_sistema (usuario_id, acao, tabela_afetada, descricao) VALUES (?, 'IMPORT', 'toners', ?)";
                $db->insert($logQuery, [$_SESSION['usuario_id'] ?? 1, "Importação Excel: {$imported} toners importados"]);
                
                $message = "Importação concluída: {$imported} toners importados";
                if (!empty($errors)) {
                    $message .= ". Erros: " . implode('; ', array_slice($errors, 0, 5));
                    if (count($errors) > 5) {
                        $message .= " e mais " . (count($errors) - 5) . " erros";
                    }
                }
                
                echo json_encode(['success' => true, 'message' => $message]);
                
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Erro ao processar arquivo: ' . $e->getMessage()]);
            }
            break;

        case 'export_excel':
            $query = "SELECT modelo, peso_cheio, peso_vazio, gramatura, capacidade_folhas, preco_toner, 
                     gramatura_por_folha, custo_por_folha, cor, tipo, data_cadastro 
                     FROM toners WHERE ativo = 1 ORDER BY modelo";
            $toners = $db->query($query);
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Cabeçalhos
            $headers = [
                'A1' => 'Modelo',
                'B1' => 'Peso Cheio (g)',
                'C1' => 'Peso Vazio (g)',
                'D1' => 'Gramatura (g)',
                'E1' => 'Capacidade Folhas',
                'F1' => 'Preço Toner (R$)',
                'G1' => 'Gramatura/Folha (g)',
                'H1' => 'Custo/Folha (R$)',
                'I1' => 'Cor',
                'J1' => 'Tipo',
                'K1' => 'Data Cadastro'
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle($cell)->getFill()->getStartColor()->setRGB('E3F2FD');
            }
            
            // Dados
            $row = 2;
            foreach ($toners as $toner) {
                $sheet->setCellValue("A{$row}", $toner['modelo']);
                $sheet->setCellValue("B{$row}", $toner['peso_cheio']);
                $sheet->setCellValue("C{$row}", $toner['peso_vazio']);
                $sheet->setCellValue("D{$row}", $toner['gramatura']);
                $sheet->setCellValue("E{$row}", $toner['capacidade_folhas']);
                $sheet->setCellValue("F{$row}", $toner['preco_toner']);
                $sheet->setCellValue("G{$row}", $toner['gramatura_por_folha']);
                $sheet->setCellValue("H{$row}", $toner['custo_por_folha']);
                $sheet->setCellValue("I{$row}", $toner['cor']);
                $sheet->setCellValue("J{$row}", $toner['tipo']);
                $sheet->setCellValue("K{$row}", date('d/m/Y H:i', strtotime($toner['data_cadastro'])));
                $row++;
            }
            
            // Ajustar largura das colunas
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Headers para download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="toners_' . date('Y-m-d_H-i-s') . '.xlsx"');
            header('Cache-Control: max-age=0');
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;

        default:
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
            break;
    }

} catch (Exception $e) {
    error_log("Erro na API de toners: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Erro interno do servidor']);
}
?>
