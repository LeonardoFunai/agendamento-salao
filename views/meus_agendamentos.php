<?php
session_start();
include '../database/conexao.php';

if (!isset($_SESSION['email']) || empty(trim($_SESSION['email']))) {
    header("Location: login.php");
    exit();
}

$user_email = trim($_SESSION['email']);

// Obter ID do usuário pelo email
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

// Obter agendamentos do usuário incluindo o serviço
$sql_agendamentos = "SELECT a.id, a.data, a.hora, s.nome AS servico
                     FROM agendamentos a
                     JOIN servicos s ON a.servico_id = s.id
                     WHERE a.user_id = ?
                     ORDER BY a.data, a.hora";
$stmt_agendamentos = $conn->prepare($sql_agendamentos);
$stmt_agendamentos->bind_param("i", $user_id);
$stmt_agendamentos->execute();
$result_agendamentos = $stmt_agendamentos->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Agendamentos</title>
    <link rel="stylesheet" href="../css/style_meus_agendamentos.css">
</head>
<body>
    <nav>
        <a href="home.php">Início</a> 
        <a href="agendamentos.php">Agendar</a> 
        <a href="meus_agendamentos.php">Meus Agendamentos</a> 
        <a href="../controller/sair.php">Sair</a>   
        <span>Conta: <?php echo htmlspecialchars($user_email); ?></span>
    </nav>

    <h2>Meus Agendamentos</h2>

    <?php if ($result_agendamentos->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Data</th>
                <th>Horário</th>
                <th>Serviço</th>
                <th>Ações</th>
            </tr>
            <?php while ($agendamento = $result_agendamentos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date("d/m/Y", strtotime($agendamento['data'])); ?></td>
                    <td><?php echo $agendamento['hora']; ?></td>
                    <td><?php echo $agendamento['servico']; ?></td>
                    <td>
                        <?php
                        $hoje = date('Y-m-d');
                        $data_limite = date('Y-m-d', strtotime($agendamento['data'] . ' -2 days'));

                        if ($hoje <= $data_limite) {
                            echo "<a href='editar_agendamento.php?id=" . $agendamento['id'] . "'>Editar</a> | ";
                        } else {
                            echo "<span style='color: gray;'>Editar (bloqueado)</span> | ";
                        }
                        ?>
                        <a href="../controller/cancelar_script.php?id=<?php echo $agendamento['id']; ?>">Cancelar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="color: red;">Nenhum agendamento encontrado.</p>
    <?php endif; ?>
</body>
</html>
