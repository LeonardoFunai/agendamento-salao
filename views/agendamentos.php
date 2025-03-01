<?php
//inicia uma sessão
session_start();
include '../database/conexao.php';
//verifica se o usuário está autenticado, se existe um email, caso não tenha retorna para o login
if (!isset($_SESSION['email']) || empty(trim($_SESSION['email']))) {
    header("Location: login.php");
    exit();
}
//-------------------------------------------------------------------------------------------------------------------------
//armazena o login nesta variável
$user_email = trim($_SESSION['email']);

//  slq para pegar o id do email
$sql_user = "SELECT id FROM users WHERE email = ?";
//cria um estado de declaracao preparada
$stmt = $conn->prepare($sql_user);
//preenche o placehoder ? cm string email para evitar sql Injection
$stmt->bind_param("s", $user_email);
//executa a sql
$stmt->execute();
//pega o resultado da consulta
$result_user = $stmt->get_result();
//armazena numa variavel o resultado numa array
$user = $result_user->fetch_assoc();

//verificar se o user foi encontrado , se não retorna para tela de login
if (!$user) {
    header("Location: login.php");
    exit();
}
//pega o valor do id do usuário e aloca em uma variável
$user_id = $user['id'];
//-------------------------------------------------------------------------------------------------------------------------
// Obter serviços disponíveis e suas durações
$sql_servicos = "SELECT id, nome, duracao FROM servicos";
$result_servicos = $conn->query($sql_servicos);

//cria um array
$servicos = [];
//aloca os arrays dentro de outro array já formatando os horários
while ($servico = $result_servicos->fetch_assoc()) {
    $horas = floor($servico['duracao'] / 60);
    $minutos = $servico['duracao'] % 60;
    $servicos[$servico['id']] = [
        'nome' => $servico['nome'],
        'duracao_min' => $servico['duracao'],
        'duracao_formatada' => ($horas > 0 ? $horas . "h" : "") . ($minutos > 0 ? $minutos . "min" : "")
    ];
}
//-------------------------------------------------------------------------------------------------------------------------
//pega a data selecionada e armazena nesta variável
$data_selecionada = $_POST['data'] ?? date('Y-m-d');

//cria dois arrays
$horarios_ocupados = [];
$horarios_bloqueados = [];

//verifica se foi enviado via post e se o botaso foi acionado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consultar'])) {
    $_SESSION['consultado'] = true; // Marca que a consulta foi feita
    //cria sql para mostrar hora e id do serviço que está ocupado
    $sql_horarios_ocupados = "SELECT hora, servico_id FROM agendamentos WHERE data = ? ORDER BY hora";
    $stmt_horarios = $conn->prepare($sql_horarios_ocupados);
    $stmt_horarios->bind_param("s", $data_selecionada);
    $stmt_horarios->execute();
    $result_horarios = $stmt_horarios->get_result();

    //percorre os horarios ocupados e gera lista contendo horarios ocupados e horarios bloqueados   
    while ($row = $result_horarios->fetch_assoc()) {
        $hora_inicio = $row['hora'];
        $servico_id = $row['servico_id'];
        $duracao_min = $servicos[$servico_id]['duracao_min'];
    
        // Calcula a hora de término adicionando a duração ao horário de início
        $hora_fim = date("H:i", strtotime("+$duracao_min minutes", strtotime($hora_inicio)));
    
        $horarios_ocupados[] = [
            'hora' => $hora_inicio,
            'hora_fim' => $hora_fim, // Novo campo para exibir o horário de término
            'servico' => $servicos[$servico_id]['nome'],
            'duracao' => $servicos[$servico_id]['duracao_formatada']
        ];
    
        // Lógica para bloquear os horários dentro da duração do serviço
        $hora_bloqueada = strtotime($hora_inicio);
        for ($i = 0; $i < ceil($duracao_min / 60); $i++) {
            $hora_formatada = date("H:i", $hora_bloqueada);
            $horarios_bloqueados[] = $hora_formatada;
            $hora_bloqueada = strtotime("+1 hour", $hora_bloqueada);
        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamentos</title>
    <link rel="stylesheet" href="../css/agendamentos.css">
</head>
<body>

    <nav>
        <a class="link" href="home.php">Início</a> 
        <a class="link" href="agendamentos.php">Agendar</a> 
        <a class="link" href="meus_agendamentos.php">Meus Agendamentos</a> 
        <a class="link" href="../controller/sair.php">Sair</a> 
        <span>Conta: <?php echo htmlspecialchars($user_email); ?></span>
    </nav>

    <div class="agendamento-wrapper">

        <!-- Caixa "Consultar Horários" -->
        <div class="agendamento-container">
            <h2>Consultar Horários</h2>
            <form method="POST">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required value="<?= htmlspecialchars($data_selecionada) ?>">
                <button type="submit" name="consultar">Consultar</button>
            </form>

            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consultar'])): ?>
            <?php if (!empty($horarios_ocupados)): ?>
                <h2>Horários Ocupados</h2>
            <table style="margin: 0 auto; width: 90%; max-width: 400px; text-align: center; border-collapse: collapse;">
                <tr style="background-color: #fc72e5; color: black; font-weight: bold;">
                    <th style="padding: 5px; border: 1px solid #f285eb;">Início</th>
                    <th style="padding: 5px; border: 1px solid #f285eb;">Término</th> <!-- Nova coluna -->
                    <th style="padding: 5px; border: 1px solid #f285eb;">Serviço</th>
                    <th style="padding: 5px; border: 1px solid #f285eb;">Duração</th>
                </tr>
                <?php foreach ($horarios_ocupados as $horario): ?>
                    <tr>
                        <td style="padding: 5px; border: 1px solid #f285eb;"><?php echo $horario['hora']; ?></td>
                        <td style="padding: 5px; border: 1px solid #f285eb;"><?php echo $horario['hora_fim']; ?></td> <!-- Exibe o horário final -->
                        <td style="padding: 5px; border: 1px solid #f285eb;"><?php echo $horario['servico']; ?></td>
                        <td style="padding: 5px; border: 1px solid #f285eb;"><?php echo $horario['duracao']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>


    <?php else: ?>
        <!-- se não tiver nenhum horario ocupado -->
        <p class="success-message">Todos os horários estão disponíveis para <?php echo date("d/m/Y", strtotime($data_selecionada)); ?>.</p>
    <?php endif; ?>
<?php endif; ?>

    </div>

        <!-- Caixa "Agendar Novo Horário" -->
        <div class="agendamento-container">
            <h2>Agendar Novo Horário</h2>
            <form method="POST" action="../controller/agendamento_script.php">
                <!-- envia a data consultada -->
                <input type="hidden" name="data" value="<?php echo $data_selecionada; ?>">

                <label for="servico">Serviço:</label>
                <!-- seleciona o serviço / Chama JavaScript p/ atualizar os horários disponíveis assim que um serviço for selecionado.-->
                <select name="servico" id="servico" required onchange="atualizarHorarios()">
                    <option value="">Selecione um serviço</option>
                    <!-- percorre array servicos onde id é a chave $servico os detalhes -->
                    <?php foreach ($servicos as $id => $servico): ?>
                        <?php
                            // Calcula se há tempo suficiente no dia
                            $duracao_min = $servico['duracao_min'];
                            //Converte 18:00 para inteiro , Subtrai a duração do serviço, verificando se ele terminaria após 18h.
                            $horario_limite = strtotime("18:00") - ($duracao_min * 60);
                            $classe = ($horario_limite < strtotime("17:00"));
                        ?>
                        <!-- cada option tem o id , a duracao minima, e os servicos disponiveis -->
                        <option value="<?php echo $id; ?>" data-duracao="<?php echo $duracao_min; ?>" class="<?php echo $classe; ?>">
                            <?php echo $servico['nome']; ?> (<?php echo $servico['duracao_formatada']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>


                <label for="horarios_disponiveis">Horário Disponível:</label>
                <select name="hora" id="horarios_disponiveis" required>
                    <option value="">Selecione um serviço primeiro</option>
                </select>

                <button type="submit">Agendar</button>
            </form>
        </div>

    </div>
 
    <script>
        //essa funcao é chamada toda vez que o usuario clicka no select
        function atualizarHorarios() {
            let selectServico = document.getElementById("servico");
            let selectHorarios = document.getElementById("horarios_disponiveis");
            selectHorarios.innerHTML = "";
            //Converte o array PHP $horarios_bloqueados em um array JavaScript.
            let horariosOcupados = <?php echo json_encode($horarios_bloqueados); ?>;
            let horariosFuncionamento = ["07:00", "08:00", "09:00", "10:00", "11:00", "13:00", "14:00", "15:00", "16:00", "17:00"];

            // Obtém o tempo de duração do serviço selecionado
            //Converte para um número inteiro (parseInt()).
            let duracaoServico = parseInt(selectServico.options[selectServico.selectedIndex].getAttribute("data-duracao"));
            //filter percorre horarios funcionamento e mantem os validos
            let horariosDisponiveis = horariosFuncionamento.filter(hora => {
                let horaInicio = parseInt(hora.split(":")[0]); // separa  e pega somente  as hrs HH
                let horaFim = horaInicio + Math.ceil(duracaoServico / 60); // Calcula a hora de término
                //retorna os horarios que nao incluem os horarios ocupados
                return !horariosOcupados.includes(hora) && horaFim <= 18; // Bloqueia se ultrapassar 18h
            });

            if (horariosDisponiveis.length === 0) {
                alert("Não há horários disponíveis para este serviço hoje.");
                selectServico.selectedIndex = 0; // Reseta a seleção do serviço
                return;
            }

            // Adiciona opções ao <select> de horários
            horariosDisponiveis.forEach(hora => {
                let option = document.createElement("option");
                option.value = hora;
                option.text = hora;
                //adiciona cada opcao
                selectHorarios.appendChild(option);
            });
        }

    </script>

</body>
</html>
