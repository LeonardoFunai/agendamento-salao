<?php
include __DIR__ . "/../database/conexao.php";

// Verifica se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $sexo = trim($_POST['genero']);
    $senha = trim($_POST['senha']);

    // Verificação do campo "Nome"
    if (empty($nome) || strlen($nome) > 45) {
        header("Location: ../views/formulario.php?erro=nome_invalido");
        exit();
    }

    // Verificação do campo "Email"
    if (empty($email) || strlen($email) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../views/formulario.php?erro=email_invalido");
        exit();
    }

    // Verificação do campo "Telefone"
    if (empty($telefone) || strlen($telefone) > 15) {
        header("Location: ../views/formulario.php?erro=telefone_invalido");
        exit();
    }

    // Verificação do campo "Gênero"
    $opcoes_sexo = ['Masculino', 'Feminino', 'Outro'];
    if (!in_array($sexo, $opcoes_sexo)) {
        header("Location: ../views/formulario.php?erro=sexo_invalido");
        exit();
    }

    // Verificação do campo "Senha"
    if (empty($senha) || strlen($senha) < 6) {
        header("Location: ../views/formulario.php?erro=senha_curta");
        exit();
    }


    $nome = mysqli_real_escape_string($conn, $nome);
    $email = mysqli_real_escape_string($conn, $email);
    $telefone = mysqli_real_escape_string($conn, $telefone);
    $sexo = mysqli_real_escape_string($conn, $sexo);
    $senha = mysqli_real_escape_string($conn, $senha);

    
    $sql = "INSERT INTO users (nome, email, telefone, sexo, senha) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param("sssss", $nome, $email, $telefone, $sexo, $senha);

    if ($stmt->execute()) {
        header("Location: ../views/login.php?sucesso=1");
        exit();
    } else {
        header("Location: ../views/formulario.php?erro=erro_banco");
        exit();
    }
}
?>
