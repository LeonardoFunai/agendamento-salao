<?php
session_start();
include '../database/conexao.php';

// Verifica se o usuário está logado e se é administrador
if (!isset($_SESSION['email']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: ../views/login.php");
    exit();
}

// Verifica se o ID do agendamento foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID inválido!'); window.history.back();</script>";
    exit();
}

$agendamento_id = $_GET['id'];

// Excluir agendamento sem restrições para admin
$sql_delete = "DELETE FROM agendamentos WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $agendamento_id);

if ($stmt_delete->execute()) {
    echo "<script>alert('Agendamento excluído com sucesso!'); window.location.href='../views/admin_agendamentos.php';</script>";
} else {
    echo "<script>alert('Erro ao excluir o agendamento. Tente novamente.'); window.history.back();</script>";
}
?>
