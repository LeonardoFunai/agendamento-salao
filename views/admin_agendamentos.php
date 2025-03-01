<?php
session_start();
include '../database/conexao.php';

// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../views/login.php");
    exit();
}

$user_email = $_SESSION['email'];
$data_hoje = date('Y-m-d');

// Buscar agendamentos do dia atual
$sql_agendamentos_hoje = "SELECT a.id, a.data, a.hora, s.nome AS servico, u.nome AS cliente, u.telefone
                           FROM agendamentos a
                           JOIN servicos s ON a.servico_id = s.id
                           JOIN users u ON a.user_id = u.id
                           WHERE a.data = ?
                           ORDER BY a.hora";
$stmt_hoje = $conn->prepare($sql_agendamentos_hoje);
$stmt_hoje->bind_param("s", $data_hoje);
$stmt_hoje->execute();
$result_agendamentos_hoje = $stmt_hoje->get_result();

// Buscar todos os agendamentos
$sql_agendamentos = "SELECT a.id, a.data, a.hora, s.nome AS servico, u.nome AS cliente, u.telefone
                     FROM agendamentos a
                     JOIN servicos s ON a.servico_id = s.id
                     JOIN users u ON a.user_id = u.id
                     ORDER BY a.data, a.hora";
$stmt_agendamentos = $conn->prepare($sql_agendamentos);
$stmt_agendamentos->execute();
$result_agendamentos = $stmt_agendamentos->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="../css/style_agendamento_admin.css">
</head>
<body>
<nav>
    <span class="admin-email">Admin: <?php echo htmlspecialchars($user_email); ?></span>
    <a href="../controller/sair.php" class="btn-sair">Sair</a>
</nav>


    <h2>Agendamentos de Hoje (<?php echo date("d/m/Y"); ?>)</h2>

    <?php if ($result_agendamentos_hoje->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Horário</th>
                <th>Serviço</th>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
            <?php while ($agendamento = $result_agendamentos_hoje->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $agendamento['hora']; ?></td>
                    <td><?php echo $agendamento['servico']; ?></td>
                    <td><?php echo $agendamento['cliente']; ?></td>
                    <td><?php echo $agendamento['telefone']; ?></td>
                    <td>
                        <a href="editar_agendamento_admin.php?id=<?php echo $agendamento['id']; ?>">Editar</a> |
                        <a href="../controller/excluir_agendamento_admin.php?id=<?php echo $agendamento['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="color: red;">Nenhum agendamento para hoje.</p>
    <?php endif; ?>

    <h2>Todos os Agendamentos</h2>

    <?php if ($result_agendamentos->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>Data</th>
                <th>Horário</th>
                <th>Serviço</th>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
            <?php while ($agendamento = $result_agendamentos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date("d/m/Y", strtotime($agendamento['data'])); ?></td>
                    <td><?php echo $agendamento['hora']; ?></td>
                    <td><?php echo $agendamento['servico']; ?></td>
                    <td><?php echo $agendamento['cliente']; ?></td>
                    <td><?php echo $agendamento['telefone']; ?></td>
                    <td>
                        <a href="editar_agendamento_admin.php?id=<?php echo $agendamento['id']; ?>">Editar</a> |
                        <a href="../controller/excluir_agendamento_admin.php?id=<?php echo $agendamento['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="color: red;">Nenhum agendamento encontrado.</p>
    <?php endif; ?>
</body>
</html>
