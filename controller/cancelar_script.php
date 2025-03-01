<?php
session_start();
include '../database/conexao.php';

if (!isset($_SESSION['email'])) {
    header("Location: ../views/login.php");
    exit();
}

$user_email = $_SESSION['email'];
$is_admin = ($user_email == 'leila@salon.com');

if (!isset($_GET['id'])) {
    echo "<script>alert('ID inválido!'); window.location.href='../views/agendamentos.php';</script>";
    exit();
}

$id = $_GET['id'];

// Verificar se o usuário tem permissão para excluir o agendamento
$sql = $is_admin ? "DELETE FROM agendamentos WHERE id = ?" : "DELETE FROM agendamentos WHERE id = ? AND user_id = (SELECT id FROM users WHERE email = ?)";
$stmt = $conn->prepare($sql);

if ($is_admin) {
    $stmt->bind_param("i", $id);
} else {
    $stmt->bind_param("is", $id, $user_email);
}

if ($stmt->execute()) {
    echo "<script>alert('Agendamento cancelado com sucesso!'); window.location.href='../views/meus_agendamentos.php';</script>";
} else {
    echo "<script>alert('Erro ao cancelar!'); window.location.href='../views/agendamentos.php';</script>";
}
?>
