<?php
session_start();
include '../database/conexao.php';

if (!isset($_SESSION['email']) || empty(trim($_SESSION['email']))) {
    header("Location: login.php");
    exit();
}

$user_email = trim($_SESSION['email']);

// Obter ID do usuário
$sql_user = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();

if (!$user) {
    header("Location: login.php");
    exit();
}

$user_id = $user['id'];

// Verifica se o ID do agendamento foi passado na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID de agendamento inválido!'); window.location.href='meus_agendamentos.php';</script>";
    exit();
}

$agendamento_id = $_GET['id'];

// Obter os dados do agendamento
$sql_agendamento = "SELECT data, hora, servico_id FROM agendamentos WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql_agendamento);
$stmt->bind_param("ii", $agendamento_id, $user_id);
$stmt->execute();
$result_agendamento = $stmt->get_result();
$agendamento = $result_agendamento->fetch_assoc();

if (!$agendamento) {
    echo "<script>alert('Agendamento não encontrado!'); window.location.href='meus_agendamentos.php';</script>";
    exit();
}

$data_atual = $agendamento['data'];
$hora_atual = $agendamento['hora'];
$servico_atual_id = $agendamento['servico_id'];

$hoje = date('Y-m-d');
$data_limite = date('Y-m-d', strtotime($data_atual . ' -2 days'));

if ($hoje > $data_limite) {
    echo "<script>alert('Você só pode editar o agendamento até 2 dias antes da data marcada!'); window.location.href='meus_agendamentos.php';</script>";
    exit();
}

// Obter os serviços disponíveis
$sql_servicos = "SELECT id, nome FROM servicos";
$result_servicos = $conn->query($sql_servicos);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Agendamento</title>
    <link rel="stylesheet" href="../css/tela_edit.css">
</head>
<body>
    <nav>
        <a href="home.php">Início</a> 
        <a href="meus_agendamentos.php">Meus Agendamentos</a> 
        <a href="../controller/sair.php">Sair</a>
    </nav>

    <h2>Editar Agendamento</h2>
    <form method="POST" action="../controller/editar_agendamento_script.php">
        <input type="hidden" name="id" value="<?php echo $agendamento_id; ?>">

        <label>Serviço:</label>
        <select name="novo_servico" required>
            <?php while ($servico = $result_servicos->fetch_assoc()): ?>
                <option value="<?php echo $servico['id']; ?>" <?php echo ($servico_atual_id == $servico['id']) ? 'selected' : ''; ?>>
                    <?php echo $servico['nome']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Nova Data:</label>
        <input type="date" name="nova_data" value="<?php echo $data_atual; ?>" required>

        <label>Novo Horário:</label>
        <select name="nova_hora" required>
            <?php 
            for ($h = 7; $h <= 18; $h++) {
                if ($h != 12) {
                    $hora_formatada = sprintf('%02d:00', $h);
                    echo "<option value='$hora_formatada' " . ($hora_atual == $hora_formatada ? "selected" : "") . ">$hora_formatada</option>";
                }
            }
            ?>
        </select>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>
