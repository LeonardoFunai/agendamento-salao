<?php 
$server = "localhost";
$user = "root";
$pass = "";
$bd = "crud";

$conn = mysqli_connect($server, $user, $pass, $bd);

// Verifica se a conexão foi bem-sucedida
if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Função para exibir mensagens
function mensagem($texto, $tipo){
    echo "<div class='alert alert-$tipo' role='alert'>
            $texto
          </div>";
}

// Função para formatar datas
function mostra_data($data){
    $d = explode('-', $data);
    return $d[2] ."/" .$d[1] ."/" . $d[0];
}
?>
