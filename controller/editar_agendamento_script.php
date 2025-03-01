<?php
session_start();
include '../database/conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['email']) || empty(trim($_SESSION['email']))) {
    header("Location: ../views/login.php");
    exit();
}

$user_email = $_SESSION['email']; // Captura o email do usuário logado

// Obtém os dados do formulário
$agendamento_id = $_POST['id'] ?? null;
$nova_data = $_POST['nova_data'] ?? null;
$nova_hora = $_POST['nova_hora'] ?? null;
$novo_servico = $_POST['novo_servico'] ?? null;

if (!$agendamento_id || !$nova_data || !$nova_hora || !$novo_servico) {
    echo "<script>alert('Todos os campos são obrigatórios!'); window.history.back();</script>";
    exit();
}

// Verifica se o usuário é admin
$is_admin = ($user_email === 'adminleila@salon.com');

if ($is_admin) {
    // Admin pode editar qualquer agendamento
    $sql_update = "UPDATE agendamentos SET data = ?, hora = ?, servico_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssii", $nova_data, $nova_hora, $novo_servico, $agendamento_id);

    if ($stmt->execute()) {
        echo "<script>alert('Agendamento atualizado com sucesso!'); window.location.href='../views/admin_agendamentos.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar o agendamento!'); window.history.back();</script>";
    }
} else {
    // Obtém o ID do usuário logado para verificar se ele é dono do agendamento
    $sql_user = "SELECT id FROM users WHERE email = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $user_email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    $user = $result_user->fetch_assoc();

    if (!$user) {
        echo "<script>alert('Usuário não encontrado!'); window.location.href='../views/login.php';</script>";
        exit();
    }

    $user_id = $user['id'];

    // Verifica se o agendamento pertence ao usuário logado
    $sql_check = "SELECT id FROM agendamentos WHERE id = ? AND user_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $agendamento_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows === 0) {
        echo "<script>alert('Você não tem permissão para editar este agendamento!'); window.history.back();</script>";
        exit();
    }

    // Atualiza o agendamento do usuário comum
    $sql_update = "UPDATE agendamentos SET data = ?, hora = ?, servico_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ssii", $nova_data, $nova_hora, $novo_servico, $agendamento_id);

    if ($stmt->execute()) {
        echo "<script>alert('Agendamento atualizado com sucesso!'); window.location.href='../views/meus_agendamentos.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar o agendamento!'); window.history.back();</script>";
    }
}
?>
