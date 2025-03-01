<?php
session_start();
include '../database/conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['email']) || empty(trim($_SESSION['email']))) {
    echo "<script>alert('Sessão expirada. Faça login novamente.'); window.location.href='../views/login.php';</script>";
    exit();
}

// Verifica se a consulta foi feita antes de permitir o agendamento
if (!isset($_SESSION['consultado']) || $_SESSION['consultado'] !== true) {
    echo "<script>alert('Consulte os horários antes de agendar!'); window.location.href='../views/agendamentos.php';</script>";
    exit();
}
if (!isset($_SESSION['consultado']) || $_SESSION['consultado'] !== true) {
    echo "<script>alert('Consulte os horários antes de agendar!'); window.location.href='../views/agendamentos.php';</script>";
    exit();
}
unset($_SESSION['consultado']); // Reseta a sessão para evitar reuso indevido

$email = trim($_SESSION['email']);

// Obtém ID do usuário
$sql_user = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $email);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();

if (!$user) {
    echo "<script>alert('Usuário não encontrado! Faça login novamente.'); window.location.href='../views/login.php';</script>";
    exit();
}

$user_id = $user['id'];

// Processa o formulário
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = $_POST['data'] ?? null;
    $hora = $_POST['hora'] ?? null;
    $servico_id = $_POST['servico'] ?? null;

    if (!$data || !$hora || !$servico_id) {
        echo "<script>alert('Preencha todos os campos!'); window.history.back();</script>";
        exit();
    }

    // Verifica se o cliente já tem um agendamento na mesma semana
    $data_semana_inicio = date('Y-m-d', strtotime('monday this week', strtotime($data)));
    $data_semana_fim = date('Y-m-d', strtotime('sunday this week', strtotime($data)));

    $sql_verificar_semana = "SELECT data FROM agendamentos WHERE user_id = ? AND data BETWEEN ? AND ?";
    $stmt_verificar_semana = $conn->prepare($sql_verificar_semana);
    $stmt_verificar_semana->bind_param("iss", $user_id, $data_semana_inicio, $data_semana_fim);
    $stmt_verificar_semana->execute();
    $result_verificar_semana = $stmt_verificar_semana->get_result();

    if ($result_verificar_semana->num_rows > 0) {
        echo "<script>alert('Você já tem um agendamento nesta semana.'); window.location.href='../views/meus_agendamentos.php';</script>";
        exit();
    }

    $sql_insert = "INSERT INTO agendamentos (user_id, servico_id, data, hora) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iiss", $user_id, $servico_id, $data, $hora);
    $stmt_insert->execute();

    unset($_SESSION['consultado']);
    echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href='../views/meus_agendamentos.php';</script>";
    exit();
}
?>
