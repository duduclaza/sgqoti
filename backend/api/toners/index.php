<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../models/Toner.php';

$database = new Database();
$db = $database->getConnection();

$toner = new Toner($db);

// Criar tabela se não existir
$toner->createTable();

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Listar todos os toners
        $stmt = $toner->read();
        $num = $stmt->rowCount();

        if($num > 0) {
            $toners_arr = array();
            $toners_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $toner_item = array(
                    "id" => $id,
                    "modelo" => $modelo,
                    "pesocheio" => floatval($peso_cheio),
                    "pesovazio" => floatval($peso_vazio),
                    "gramatura" => floatval($gramatura),
                    "capacidade" => intval($capacidade),
                    "gramaturafolha" => floatval($gramatura_folha),
                    "preco" => floatval($preco),
                    "precofolha" => floatval($preco_folha),
                    "cor" => $cor,
                    "tipo" => $tipo,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                );

                array_push($toners_arr["records"], $toner_item);
            }

            http_response_code(200);
            echo json_encode($toners_arr);
        } else {
            http_response_code(200);
            echo json_encode(array("records" => array()));
        }
        break;

    case 'POST':
        // Criar novo toner
        $data = json_decode(file_get_contents("php://input"));

        if(!empty($data->modelo) && !empty($data->pesocheio) && !empty($data->pesovazio) && 
           !empty($data->capacidade) && !empty($data->preco) && !empty($data->cor) && !empty($data->tipo)) {

            $toner->modelo = $data->modelo;
            $toner->peso_cheio = $data->pesocheio;
            $toner->peso_vazio = $data->pesovazio;
            $toner->capacidade = $data->capacidade;
            $toner->preco = $data->preco;
            $toner->cor = $data->cor;
            $toner->tipo = $data->tipo;

            // Validar dados
            $errors = $toner->validate();
            if(!empty($errors)) {
                http_response_code(400);
                echo json_encode(array("message" => "Dados inválidos", "errors" => $errors));
                break;
            }

            if($toner->create()) {
                // Buscar o toner criado com os campos calculados
                $toner->readOne();
                
                $response = array(
                    "message" => "Toner criado com sucesso",
                    "toner" => array(
                        "id" => $toner->id,
                        "modelo" => $toner->modelo,
                        "pesocheio" => floatval($toner->peso_cheio),
                        "pesovazio" => floatval($toner->peso_vazio),
                        "gramatura" => floatval($toner->gramatura),
                        "capacidade" => intval($toner->capacidade),
                        "gramaturafolha" => floatval($toner->gramatura_folha),
                        "preco" => floatval($toner->preco),
                        "precofolha" => floatval($toner->preco_folha),
                        "cor" => $toner->cor,
                        "tipo" => $toner->tipo
                    )
                );

                http_response_code(201);
                echo json_encode($response);
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar o toner"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido"));
        break;
}
?>
