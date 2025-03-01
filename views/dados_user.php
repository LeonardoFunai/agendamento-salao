<?php
session_start();
include '../database/conexao.php';


if (!isset($_SESSION['email']) || empty(trim($_SESSION['email']))) {
    header("Location: login.php");
    exit();
}


$email_usuario = $_SESSION['email'];

$sql = "SELECT nome, email, telefone, sexo FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email_usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    echo "<script>alert('Usuário não encontrado!'); window.location.href='home.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consultar Meus Dados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dados_user.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span >
                Conta: <strong><?php  echo htmlspecialchars($_SESSION['email']); ?></strong>
            </span>
            <a href="../controller/sair.php" class="sair">Sair</a>
        </div>
</nav>

<div class="container">
    <h1>Meus Dados</h1>
    <p class="lead text-center">Aqui estão os seus dados cadastrados no sistema:</p>
    <hr class="my-4" style="width: 100%;">

    <div class="dados-container">
        <div class="dados-item">
            <strong>Nome:</strong> 
            <span><?php echo htmlspecialchars($usuario['nome']); ?></span>
        </div>

        <div class="dados-item">
            <strong>E-mail:</strong> 
            <span><?php echo htmlspecialchars($usuario['email']); ?></span>
        </div>

        <div class="dados-item">
            <strong>Telefone:</strong> 
            <span><?php echo htmlspecialchars($usuario['telefone']); ?></span>
        </div>

        <div class="dados-item">
            <strong>Sexo:</strong> 
            <span><?php echo htmlspecialchars($usuario['sexo']); ?></span>
        </div>
    </div>

    <a class="voltar" href="home.php">⬅ Voltar</a>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
