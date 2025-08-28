<?php
require_once '../config/database.php';
require_once '../config/cors.php';

header('Content-Type: application/json; charset=utf-8');

$session_started = false;
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $session_started = true;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = new Database();
    $pdo = $db->getConnection();

    // Cria tabela se não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS toner_returns (
        id INT AUTO_INCREMENT PRIMARY KEY,
        toner_id INT NOT NULL,
        modelo VARCHAR(255) NOT NULL,
        cor VARCHAR(32) NOT NULL,
        tipo VARCHAR(32) NOT NULL,
        capacidade INT NOT NULL,
        preco_folha DECIMAL(10,4) NOT NULL,
        filial VARCHAR(128) NOT NULL,
        modo VARCHAR(10) NOT NULL, -- 'peso' ou 'percent'
        peso_retornado DECIMAL(10,3) NULL,
        percentual DECIMAL(5,2) NOT NULL,
        destino VARCHAR(32) NOT NULL, -- descarte, estoque, garantia, uso_interno
        valor_recuperado DECIMAL(12,2) DEFAULT 0,
        user_name VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (toner_id) REFERENCES toners(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Ajustes de migração leve: garantir coluna user_name
    // e manter compatibilidade caso a tabela já exista com colunas antigas
    try {
        $pdo->exec("ALTER TABLE toner_returns ADD COLUMN user_name VARCHAR(255) NOT NULL DEFAULT ''");
    } catch (Exception $e) {
        // coluna já existe
    }

    switch ($method) {
        case 'GET':
            // Retornar campos essenciais para o grid e toner_id para edição
            $query = "SELECT id, created_at, modelo, filial, destino, valor_recuperado, user_name, toner_id FROM toner_returns WHERE 1=1";
            $params = [];
            if (!empty($_GET['start'])) {
                $query .= " AND created_at >= ?";
                $params[] = $_GET['start'] . ' 00:00:00';
            }
            if (!empty($_GET['end'])) {
                $query .= " AND created_at <= ?";
                $params[] = $_GET['end'] . ' 23:59:59';
            }
            if (!empty($_GET['q'])) {
                $query .= " AND (modelo LIKE ? OR filial LIKE ? OR destino LIKE ? OR user_name LIKE ?)";
                $params[] = '%' . $_GET['q'] . '%';
                $params[] = '%' . $_GET['q'] . '%';
                $params[] = '%' . $_GET['q'] . '%';
                $params[] = '%' . $_GET['q'] . '%';
            }
            $query .= " ORDER BY created_at DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { throw new Exception('Payload inválido'); }

            $tonerId = (int)($data['toner_id'] ?? 0);
            $filial = trim($data['filial'] ?? '');
            $modo = $data['modo'] ?? 'peso';
            $pesoRetornado = isset($data['peso_retornado']) ? (float)$data['peso_retornado'] : null;
            $percentualInformado = isset($data['percentual']) ? (float)$data['percentual'] : null;
            $destino = $data['destino'] ?? '';
            $userName = isset($_SESSION['user_name']) ? trim((string)$_SESSION['user_name']) : '';

            if (!$tonerId || !$filial || !$destino) {
                throw new Exception('Campos obrigatórios: toner_id, filial, destino');
            }
            if (!in_array($modo, ['peso','percent'])) { throw new Exception('Modo inválido'); }
            if (!in_array($destino, ['descarte','estoque','garantia','uso_interno'])) { throw new Exception('Destino inválido'); }

            // Carregar toner
            $stmtT = $pdo->prepare("SELECT * FROM toners WHERE id = ?");
            $stmtT->execute([$tonerId]);
            $toner = $stmtT->fetch(PDO::FETCH_ASSOC);
            if (!$toner) { throw new Exception('Toner não encontrado'); }

            $gramaturaTotal = (float)$toner['gramatura'];
            $pesoVazio = (float)$toner['peso_vazio'];
            $capacidade = (int)$toner['capacidade'];
            $precoFolha = (float)$toner['preco_folha'];

            if ($modo === 'peso') {
                if ($pesoRetornado === null) { throw new Exception('peso_retornado é obrigatório'); }
                $gramaturaPresente = max(0.0, (float)$pesoRetornado - $pesoVazio);
                $percentual = $gramaturaTotal > 0 ? round(($gramaturaPresente / $gramaturaTotal) * 100, 2) : 0;
            } else {
                if ($percentualInformado === null) { throw new Exception('percentual é obrigatório'); }
                $percentual = max(0.0, min(100.0, (float)$percentualInformado));
            }

            // Valor recuperado (apenas quando destino = estoque)
            $valorRecuperado = 0;
            if ($destino === 'estoque') {
                $folhas = round(($percentual / 100) * $capacidade);
                $valorRecuperado = round($folhas * $precoFolha, 2);
            }

            $stmt = $pdo->prepare("INSERT INTO toner_returns
                (toner_id, modelo, cor, tipo, capacidade, preco_folha, filial, modo, peso_retornado, percentual, destino, valor_recuperado, user_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $tonerId,
                $toner['modelo'],
                $toner['cor'],
                $toner['tipo'],
                $capacidade,
                $precoFolha,
                $filial,
                $modo,
                $pesoRetornado,
                $percentual,
                $destino,
                $valorRecuperado,
                $userName
            ]);

            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'percentual' => $percentual, 'valor_recuperado' => $valorRecuperado]);
            break;
        case 'PUT':
            parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
            $id = isset($qs['id']) ? (int)$qs['id'] : 0;
            if (!$id) { throw new Exception('ID obrigatório'); }
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) { throw new Exception('Payload inválido'); }

            $destino = $data['destino'] ?? null;
            $filial = $data['filial'] ?? null;

            $sets = [];$params=[];
            if ($destino){$sets[]='destino=?';$params[]=$destino;}
            if ($filial){$sets[]='filial=?';$params[]=$filial;}
            if (!$sets){ throw new Exception('Nada para atualizar'); }
            $params[]=$id;

            $stmt=$pdo->prepare('UPDATE toner_returns SET '.implode(',', $sets).' WHERE id = ?');
            $stmt->execute($params);
            echo json_encode(['success'=>true]);
            break;
        case 'DELETE':
            parse_str($_SERVER['QUERY_STRING'] ?? '', $qs);
            $id = isset($qs['id']) ? (int)$qs['id'] : 0;
            if (!$id) { throw new Exception('ID obrigatório'); }
            $stmt = $pdo->prepare('DELETE FROM toner_returns WHERE id = ?');
            $stmt->execute([$id]);
            echo json_encode(['success'=>true]);
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}

