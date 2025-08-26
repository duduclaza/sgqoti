<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../models/Toner.php';

$database = new Database();
$db = $database->getConnection();

$toner = new Toner($db);

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$id) {
    http_response_code(400);
    echo json_encode(array("message" => "ID é obrigatório"));
    exit;
}

$toner->id = $id;

switch($method) {
    case 'GET':
        // Buscar toner por ID
        if($toner->readOne()) {
            $toner_arr = array(
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
                "tipo" => $toner->tipo,
                "created_at" => $toner->created_at,
                "updated_at" => $toner->updated_at
            );

            http_response_code(200);
            echo json_encode($toner_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Toner não encontrado"));
        }
        break;

    case 'PUT':
        // Atualizar toner
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

            if($toner->update()) {
                // Buscar o toner atualizado com os campos calculados
                $toner->readOne();
                
                $response = array(
                    "message" => "Toner atualizado com sucesso",
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

                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível atualizar o toner"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos"));
        }
        break;

    case 'DELETE':
        // Deletar toner
        if($toner->delete()) {
            http_response_code(200);
            echo json_encode(array("message" => "Toner deletado com sucesso"));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Não foi possível deletar o toner"));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido"));
        break;
}
?>
