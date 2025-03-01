<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salão Cabeleleila Leila</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/home.css?v=<?php echo time(); ?>">
</head>
<body>

    
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <span >
                Conta: <strong><?php session_start(); echo htmlspecialchars($_SESSION['email']); ?></strong>
            </span>
            <a href="../controller/sair.php" class="btn btn-danger me-3">Sair</a>
        </div>
    </nav>

    <?php
      if((!isset($_SESSION['email']) == true) and (!isset($_SESSION['senha']) == true )) {
        unset($_SESSION['email']);
        unset($_SESSION['senha']);
        header('Location: login.php');
      }
      $logado = $_SESSION['email'];
    ?>

    <div class="container">
        <div class="box">
            <h1 class="title">✨ Salão Cabeleleila Leila ✨</h1>
            <p class="frase-efeito">Deixe de ser leiga e venha para Leila!</p>
            <p class="lead">
                O salão mais renomado da cidade!  <br>
                Agende seu horário e tenha a experiência única do toque mágico da Leila.  
            </p>

          
            <p class="servicos"><strong>💇‍♀️ Serviços oferecidos:</strong></p>
            <ul class="servicos">
                <li>💇‍♀️ Corte de cabelo</li>
                <li>🎨 Coloração e Mechas</li>
                <li>💆‍♀️ Hidratação Profunda</li>
                <li>🔥 Escova & Modelagem</li>
                
                
            </ul>

            <hr class="my-4">
            <p class="fun">Gerencie seus agendamentos de forma fácil e rápida:</p>
            <div class="button-container">
                <a class="button" href="agendamentos.php">Agendar Horário</a>
                <a class="button" href="dados_user.php">Dados da Conta</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
