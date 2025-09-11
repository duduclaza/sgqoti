<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit();
}

require_once '../config/database.php';

try {
    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'save_filial':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $nome = trim($_POST['nome'] ?? '');
            if (empty($nome)) {
                throw new Exception('Nome da filial é obrigatório');
            }
            
            $id = $db->insert('filiais', ['nome' => $nome]);
            echo json_encode([
                'success' => true, 
                'message' => 'Filial cadastrada com sucesso!',
                'data' => ['id' => $id, 'nome' => $nome]
            ]);
            break;

        case 'save_departamento':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $nome = trim($_POST['nome'] ?? '');
            if (empty($nome)) {
                throw new Exception('Nome do departamento é obrigatório');
            }
            
            $id = $db->insert('departamentos', ['nome' => $nome]);
            echo json_encode([
                'success' => true, 
                'message' => 'Departamento cadastrado com sucesso!',
                'data' => ['id' => $id, 'nome' => $nome]
            ]);
            break;

        case 'save_fornecedor':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $nome = trim($_POST['nome'] ?? '');
            $contato = trim($_POST['contato'] ?? '');
            $rma = trim($_POST['rma'] ?? '');
            
            if (empty($nome)) {
                throw new Exception('Nome do fornecedor é obrigatório');
            }
            
            $data = ['nome' => $nome];
            if (!empty($contato)) $data['contato'] = $contato;
            if (!empty($rma)) $data['rma'] = $rma;
            
            $id = $db->insert('fornecedores', $data);
            echo json_encode([
                'success' => true, 
                'message' => 'Fornecedor cadastrado com sucesso!',
                'data' => array_merge(['id' => $id], $data)
            ]);
            break;

        case 'save_parametro':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $nome = trim($_POST['nome'] ?? '');
            $percentual_min = floatval($_POST['percentual_min'] ?? 0);
            $percentual_max = $_POST['percentual_max'] !== '' ? floatval($_POST['percentual_max']) : null;
            $orientacao = trim($_POST['orientacao'] ?? '');
            $cor_indicador = trim($_POST['cor_indicador'] ?? '#666666');
            
            if (empty($nome) || empty($orientacao)) {
                throw new Exception('Nome e orientação são obrigatórios');
            }
            
            $data = [
                'nome' => $nome,
                'percentual_min' => $percentual_min,
                'percentual_max' => $percentual_max,
                'orientacao' => $orientacao,
                'cor_indicador' => $cor_indicador
            ];
            
            $id = $db->insert('parametros_retornados', $data);
            echo json_encode([
                'success' => true, 
                'message' => 'Parâmetro cadastrado com sucesso!',
                'data' => array_merge(['id' => $id], $data)
            ]);
            break;

        case 'delete_filial':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            
            $affected = $db->delete('filiais', 'id = :id', ['id' => $id]);
            if ($affected > 0) {
                echo json_encode(['success' => true, 'message' => 'Filial excluída com sucesso!']);
            } else {
                throw new Exception('Filial não encontrada');
            }
            break;

        case 'delete_departamento':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            
            $affected = $db->delete('departamentos', 'id = :id', ['id' => $id]);
            if ($affected > 0) {
                echo json_encode(['success' => true, 'message' => 'Departamento excluído com sucesso!']);
            } else {
                throw new Exception('Departamento não encontrado');
            }
            break;

        case 'delete_fornecedor':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $id = intval($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            
            $affected = $db->delete('fornecedores', 'id = :id', ['id' => $id]);
            if ($affected > 0) {
                echo json_encode(['success' => true, 'message' => 'Fornecedor excluído com sucesso!']);
            } else {
                throw new Exception('Fornecedor não encontrado');
            }
            break;

        case 'update_filial':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $id = intval($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            if (empty($nome)) {
                throw new Exception('Nome da filial é obrigatório');
            }
            
            $affected = $db->update('filiais', ['nome' => $nome], 'id = :id', ['id' => $id]);
            if ($affected > 0) {
                echo json_encode(['success' => true, 'message' => 'Filial atualizada com sucesso!']);
            } else {
                throw new Exception('Filial não encontrada');
            }
            break;

        case 'update_departamento':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $id = intval($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            if (empty($nome)) {
                throw new Exception('Nome do departamento é obrigatório');
            }
            
            $affected = $db->update('departamentos', ['nome' => $nome], 'id = :id', ['id' => $id]);
            if ($affected > 0) {
                echo json_encode(['success' => true, 'message' => 'Departamento atualizado com sucesso!']);
            } else {
                throw new Exception('Departamento não encontrado');
            }
            break;

        case 'update_fornecedor':
            if ($method !== 'POST') {
                throw new Exception('Método não permitido');
            }
            
            $id = intval($_POST['id'] ?? 0);
            $nome = trim($_POST['nome'] ?? '');
            $contato = trim($_POST['contato'] ?? '');
            $rma = trim($_POST['rma'] ?? '');
            
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            if (empty($nome)) {
                throw new Exception('Nome do fornecedor é obrigatório');
            }
            
            $data = ['nome' => $nome, 'contato' => $contato, 'rma' => $rma];
            $affected = $db->update('fornecedores', $data, 'id = :id', ['id' => $id]);
            if ($affected > 0) {
                echo json_encode(['success' => true, 'message' => 'Fornecedor atualizado com sucesso!']);
            } else {
                throw new Exception('Fornecedor não encontrado');
            }
            break;

        case 'get_item':
            if ($method !== 'GET') {
                throw new Exception('Método não permitido');
            }
            
            $type = $_GET['type'] ?? '';
            $id = intval($_GET['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('ID inválido');
            }
            
            $table = '';
            switch ($type) {
                case 'filial':
                    $table = 'filiais';
                    break;
                case 'departamento':
                    $table = 'departamentos';
                    break;
                case 'fornecedor':
                    $table = 'fornecedores';
                    break;
                default:
                    throw new Exception('Tipo inválido');
            }
            
            $stmt = $db->query("SELECT * FROM {$table} WHERE id = :id", ['id' => $id]);
            $item = $stmt->fetch();
            
            if ($item) {
                echo json_encode(['success' => true, 'data' => $item]);
            } else {
                throw new Exception('Item não encontrado');
            }
            break;

        case 'get_filiais':
            $stmt = $db->query('SELECT * FROM filiais WHERE ativo = 1 ORDER BY nome');
            $filiais = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $filiais]);
            break;

        case 'get_departamentos':
            $stmt = $db->query('SELECT * FROM departamentos WHERE ativo = 1 ORDER BY nome');
            $departamentos = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $departamentos]);
            break;

        case 'get_fornecedores':
            $stmt = $db->query('SELECT * FROM fornecedores WHERE ativo = 1 ORDER BY nome');
            $fornecedores = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $fornecedores]);
            break;

        case 'get_parametros':
            $stmt = $db->query('SELECT * FROM parametros_retornados WHERE ativo = 1 ORDER BY ordem_exibicao');
            $parametros = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $parametros]);
            break;

        default:
            throw new Exception('Ação não encontrada');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
