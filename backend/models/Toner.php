<?php
class Toner {
    private $conn;
    private $table_name = "toners";

    public $id;
    public $modelo;
    public $peso_cheio;
    public $peso_vazio;
    public $gramatura;
    public $capacidade;
    public $gramatura_folha;
    public $preco;
    public $preco_folha;
    public $cor;
    public $tipo;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar tabela se não existir
    public function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            modelo VARCHAR(100) NOT NULL,
            peso_cheio DECIMAL(10,2) NOT NULL,
            peso_vazio DECIMAL(10,2) NOT NULL,
            gramatura DECIMAL(10,2) GENERATED ALWAYS AS (peso_cheio - peso_vazio) STORED,
            capacidade INT NOT NULL,
            gramatura_folha DECIMAL(10,4) GENERATED ALWAYS AS (gramatura / capacidade) STORED,
            preco DECIMAL(10,2) NOT NULL,
            preco_folha DECIMAL(10,4) GENERATED ALWAYS AS (preco / capacidade) STORED,
            cor ENUM('Black', 'Cyan', 'Magenta', 'Yellow') NOT NULL,
            tipo ENUM('Compativel', 'Original', 'Remanufaturado') NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }

    // Listar todos os toners
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Criar novo toner
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (modelo, peso_cheio, peso_vazio, capacidade, preco, cor, tipo) 
                  VALUES (:modelo, :peso_cheio, :peso_vazio, :capacidade, :preco, :cor, :tipo)";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->peso_cheio = htmlspecialchars(strip_tags($this->peso_cheio));
        $this->peso_vazio = htmlspecialchars(strip_tags($this->peso_vazio));
        $this->capacidade = htmlspecialchars(strip_tags($this->capacidade));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->cor = htmlspecialchars(strip_tags($this->cor));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));

        // Bind dos parâmetros
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":peso_cheio", $this->peso_cheio);
        $stmt->bindParam(":peso_vazio", $this->peso_vazio);
        $stmt->bindParam(":capacidade", $this->capacidade);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":cor", $this->cor);
        $stmt->bindParam(":tipo", $this->tipo);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Buscar toner por ID
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            $this->modelo = $row['modelo'];
            $this->peso_cheio = $row['peso_cheio'];
            $this->peso_vazio = $row['peso_vazio'];
            $this->gramatura = $row['gramatura'];
            $this->capacidade = $row['capacidade'];
            $this->gramatura_folha = $row['gramatura_folha'];
            $this->preco = $row['preco'];
            $this->preco_folha = $row['preco_folha'];
            $this->cor = $row['cor'];
            $this->tipo = $row['tipo'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }

        return false;
    }

    // Atualizar toner
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET modelo = :modelo, peso_cheio = :peso_cheio, peso_vazio = :peso_vazio,
                      capacidade = :capacidade, preco = :preco, cor = :cor, tipo = :tipo
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Sanitizar dados
        $this->modelo = htmlspecialchars(strip_tags($this->modelo));
        $this->peso_cheio = htmlspecialchars(strip_tags($this->peso_cheio));
        $this->peso_vazio = htmlspecialchars(strip_tags($this->peso_vazio));
        $this->capacidade = htmlspecialchars(strip_tags($this->capacidade));
        $this->preco = htmlspecialchars(strip_tags($this->preco));
        $this->cor = htmlspecialchars(strip_tags($this->cor));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind dos parâmetros
        $stmt->bindParam(":modelo", $this->modelo);
        $stmt->bindParam(":peso_cheio", $this->peso_cheio);
        $stmt->bindParam(":peso_vazio", $this->peso_vazio);
        $stmt->bindParam(":capacidade", $this->capacidade);
        $stmt->bindParam(":preco", $this->preco);
        $stmt->bindParam(":cor", $this->cor);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    // Deletar toner
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    // Validar dados
    public function validate() {
        $errors = [];

        if(empty($this->modelo)) {
            $errors[] = "Modelo é obrigatório";
        }

        if(empty($this->peso_cheio) || $this->peso_cheio <= 0) {
            $errors[] = "Peso cheio deve ser maior que zero";
        }

        if(empty($this->peso_vazio) || $this->peso_vazio <= 0) {
            $errors[] = "Peso vazio deve ser maior que zero";
        }

        if($this->peso_cheio <= $this->peso_vazio) {
            $errors[] = "Peso cheio deve ser maior que peso vazio";
        }

        if(empty($this->capacidade) || $this->capacidade <= 0) {
            $errors[] = "Capacidade deve ser maior que zero";
        }

        if(empty($this->preco) || $this->preco <= 0) {
            $errors[] = "Preço deve ser maior que zero";
        }

        if(!in_array($this->cor, ['Black', 'Cyan', 'Magenta', 'Yellow'])) {
            $errors[] = "Cor inválida";
        }

        if(!in_array($this->tipo, ['Compativel', 'Original', 'Remanufaturado'])) {
            $errors[] = "Tipo inválido";
        }

        return $errors;
    }
}
?>
