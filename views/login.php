<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Cabeleleila Leila</title>
    <link rel="stylesheet" href="../css/style_login.css?v=<?php echo time(); ?>">
</head>
<body>

    <div id="error-message" class="error-message">
        <button class="close-btn" onclick="fecharMensagemErro()">×</button>
        Erro: Email ou senha inválidos. Tente novamente.
    </div>

    <div class="login-box">
        <h1>🔐 Login</h1>
        <form action="../controller/testlogin.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <br><br>
            <input type="password" name="senha" placeholder="Senha" required>
            <br><br>
            <input class="inputsubmit" type="submit" name="submit" value="Entrar">
        </form>

        
        <a href="index.php" class="btn-voltar">⬅ Voltar ao Início</a>
    </div>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('erro')) {
            const errorMessage = document.getElementById("error-message");
            errorMessage.style.display = "block";

            setTimeout(() => {
                errorMessage.style.display = "none";
            }, 3000);
        }

        function fecharMensagemErro() {
            document.getElementById("error-message").style.display = "none";
        }
    </script>

</body>
</html>
