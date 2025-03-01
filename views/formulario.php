<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro | Cabeleleila Leila</title>
    <link rel="stylesheet" href="../css/style_formulario.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="box">
        <form action="../controller/formulario_script.php" method="POST">
            <fieldset> 
                <legend><b>Cadastro</b></legend>  
                <br>

                <!-- Exibir mensagem de erro -->
                <?php if (isset($_GET['erro'])): ?>
                    <p class="error-message">
                        <?php
                            $erros = [
                                'nome_invalido' => 'Nome deve ter no máximo 45 caracteres.',
                                'email_invalido' => 'E-mail inválido ou acima de 50 caracteres.',
                                'telefone_invalido' => 'Telefone deve ter no máximo 15 caracteres.',
                                'sexo_invalido' => 'Sexo inválido.',
                                'senha_curta' => 'A senha deve ter no mínimo 6 caracteres.',
                                'erro_banco' => 'Erro ao salvar no banco de dados. Tente novamente.',
                            ];
                            echo $erros[$_GET['erro']] ?? 'Erro desconhecido.';
                        ?>
                    </p>
                <?php endif; ?>

                <div class="inputbox">
                    <input type="text" name="nome" id="nome" class="inputUser" value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>" required>
                    <label for="nome" class="labelinput">Nome Completo</label>
                </div>  
                <br><br>

                <div class="inputbox">
                    <input type="email" name="email" id="email" class="inputUser" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" required>
                    <label for="email" class="labelinput">Email</label>
                </div>
                <br><br>

                <div class="inputbox">
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label for="senha" class="labelinput">Senha</label>
                </div>
                <br><br>

                <div class="inputbox">
                    <input type="tel" name="telefone" id="telefone" class="inputUser" value="<?= htmlspecialchars($_GET['telefone'] ?? '') ?>" required>
                    <label for="telefone" class="labelinput">Telefone</label>
                </div> 
                <br><br>

                <p>Sexo:</p>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="genero" value="Feminino" <?= (isset($_GET['genero']) && $_GET['genero'] == 'Feminino') ? 'checked' : '' ?> required>
                        Feminino
                    </label>

                    <label>
                        <input type="radio" name="genero" value="Masculino" <?= (isset($_GET['genero']) && $_GET['genero'] == 'Masculino') ? 'checked' : '' ?> required>
                        Masculino
                    </label>

                    <label>
                        <input type="radio" name="genero" value="Outro" <?= (isset($_GET['genero']) && $_GET['genero'] == 'Outro') ? 'checked' : '' ?> required>
                        Outro
                    </label>
                </div>

                <br><br>
                <input type="submit" name="submit" id="submit" value="Cadastrar">
                <br><br>
                <a href="index.php" class="btn-voltar">⬅ Voltar ao Início</a>
            </fieldset>
            
        </form>
    </div>
</body>
</html>
