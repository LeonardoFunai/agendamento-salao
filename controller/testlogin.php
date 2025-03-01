<?php 
session_start();

if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    include __DIR__ . "/../database/conexao.php";   

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = mysqli_real_escape_string($conn, $_POST['senha']);

    // Consulta para verificar usuário e tipo de conta (admin ou cliente)
    $sql = "SELECT id, nome, email, senha, tipo FROM users WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['tipo'] = $user['tipo']; // Define se é admin ou cliente

        // Redirecionamento de acordo com o tipo de usuário
        if ($user['tipo'] === 'admin') {
            header("Location: ../views/admin_agendamentos.php"); 
        } else {
            header("Location: ../views/home.php"); 
        }
        exit();
    } else {
        // Se o login falhar, limpar a sessão e redirecionar para o login
        unset($_SESSION['email']);
        unset($_SESSION['tipo']);
        header('Location: ../views/login.php?erro=1');
        exit();
    }
} else {
    header('Location: ../views/login.php?erro=1');
    exit();
}
?>
