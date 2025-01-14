<?php
// Habilitar exibição de erros (para desenvolvimento)
// Remova ou comente estas linhas em produção
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Definir cabeçalhos para permitir requisições CORS (se necessário)
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Allow-Headers: Content-Type");

// Configurações do banco de dados
$host = "localhost";
$dbname = "ca";
$user = "root";
$password = "";

header('Content-Type: application/json');

try {
    // Conexão com o banco de dados usando PDO
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter os dados da requisição POST (JSON)
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar se os dados foram recebidos corretamente
    if (!empty($data)) {
        // Capturar os dados do evento
        $titulo = $data['titulo'];
        $horario = $data['horario']; // Horário inicial (formato HH:MM)
        $cor = isset($data['cor']) ? $data['cor'] : '#007bff';
        $data = $data['data']; // Data de início do evento (YYYY-MM-DD)
        $fim_horario = $data['fim_horario']; // Horário final (formato HH:MM)

        // Validar formatos (opcional, mas recomendado)
        // Por exemplo, verificar se os horários estão no formato correto

        // Inserir os dados no banco de dados
        $sql = "INSERT INTO calendario (titulo, horario, cor, data, fim_horario) 
                VALUES (:titulo, :horario, :cor, :data, :fim_horario)";
        $stmt = $conn->prepare($sql);

        // Vincular os parâmetros aos valores
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':horario', $horario);
        $stmt->bindParam(':cor', $cor);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':fim_horario', $fim_horario);

        // Executar a consulta e verificar o resultado
        if ($stmt->execute()) {
            // Retornar resposta de sucesso
            echo json_encode(["status" => "success", "message" => "Evento salvo com sucesso!"]);
        } else {
            // Retornar mensagem de erro
            echo json_encode(["status" => "error", "message" => "Erro ao salvar o evento."]);
        }
    } else {
        // Retornar mensagem de erro por dados incompletos
        echo json_encode(["status" => "error", "message" => "Dados incompletos."]);
    }
} catch (PDOException $e) {
    // Retornar mensagem de erro de conexão ou consulta
    echo json_encode(["status" => "error", "message" => "Erro no servidor: " . $e->getMessage()]);
}
?>
