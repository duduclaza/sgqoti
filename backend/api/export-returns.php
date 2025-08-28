<?php
require_once '../config/database.php';
require_once '../config/cors.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();

    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;

    $q = "SELECT * FROM toner_returns WHERE 1=1";
    $p = [];
    if ($start){ $q .= " AND created_at >= ?"; $p[] = $start . ' 00:00:00'; }
    if ($end){ $q .= " AND created_at <= ?"; $p[] = $end . ' 23:59:59'; }
    $q .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($q);
    $stmt->execute($p);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    $filename = 'Retornos_Toners_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    echo "\xEF\xBB\xBF"; // BOM

    $headers = [
        'ID','Data','Modelo','Cor','Tipo','Capacidade','Preço/Folha','Cliente Código','Cliente Nome','Filial','Modo','Peso Retornado','%','Orientação','Destino','Valor Recuperado','Obs'
    ];
    echo implode(';', $headers) . "\r\n";

    foreach ($rows as $r){
        $line = [
            $r['id'],
            $r['created_at'],
            $r['modelo'],
            $r['cor'],
            $r['tipo'],
            $r['capacidade'],
            str_replace('.',',',$r['preco_folha']),
            $r['cliente_codigo'],
            $r['cliente_nome'],
            $r['filial'],
            $r['modo'],
            $r['peso_retornado'],
            str_replace('.',',',$r['percentual']),
            $r['orientacao'],
            $r['destino'],
            str_replace('.',',',$r['valor_recuperado']),
            $r['observacoes']
        ];
        $escaped = array_map(function($f){
            $f = (string)$f;
            if (strpbrk($f, ";\n\"") !== false) { return '"' . str_replace('"','""',$f) . '"'; }
            return $f;
        }, $line);
        echo implode(';', $escaped) . "\r\n";
    }
} catch (Exception $e){
    http_response_code(500);
    echo 'Erro: ' . $e->getMessage();
}
