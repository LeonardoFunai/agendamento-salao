<?php
session_start();
include '../database/conexao.php';

// Verifica se o usuário é admin
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'adminleila@salon.com') {
    header("Location: ../views/login.php");
    exit();
}

// obter o ID do agendamento
$agendamento_id = $_GET['id'] ?? null;
if (!$agendamento_id) {
    header("Location: admin_agendamentos.php");
    exit();
}

// obter os detalhes do agendamento
$sql = "SELECT data, hora, servico_id FROM agendamentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $agendamento_id);
$stmt->execute();
$result = $stmt->get_result();
$agendamento = $result->fetch_assoc();

if (!$agendamento) {
    header("Location: admin_agendamentos.php");
    exit();
}

// obter os serviços disponíveis
$sql_servicos = "SELECT id, nome FROM servicos";
$result_servicos = $conn->query($sql_servicos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Agendamento (Admin)</title>
    <link rel="stylesheet" href="../css/style_edit_admin.css">
</head>
<body>
    <div class="form-box">
        <h2>Editar Agendamento (Admin)</h2>
        <form method="POST" action="../controller/editar_agendamento_script.php">
            <input type="hidden" name="id" value="<?php echo $agendamento_id; ?>">

            <label>Novo Serviço:</label>
            <select name="novo_servico" required>
                <?php while ($servico = $result_servicos->fetch_assoc()): ?>
                    <option value="<?php echo $servico['id']; ?>" <?php echo ($agendamento['servico_id'] == $servico['id']) ? 'selected' : ''; ?>>
                        <?php echo $servico['nome']; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Nova Data:</label>
            <input type="date" name="nova_data" value="<?php echo $agendamento['data']; ?>" required>

            <label>Novo Horário:</label>
            <input type="time" name="nova_hora" value="<?php echo $agendamento['hora']; ?>" required>

            <button type="submit">Salvar Alterações</button>
        </form>
    </div>
</body>

</html>
