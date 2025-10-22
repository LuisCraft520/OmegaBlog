<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

//metodo de abrir o json
$ARQUIVO_JSON = 'json/usuarios.json';
$json_data = file_get_contents($ARQUIVO_JSON);
$usuarios = json_decode($json_data, true);
if ($usuarios === null) {
    $usuarios  = "";
}

$mensagem = "";
$invisivel_erro = 'style="display: none"';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"] ?? "";
    $senha = $_POST["senha"] ?? "";
    foreach ($usuarios as $usuario) {
        if ($usuario["nome"] == $nome) {
            if ($usuario["senha"] == $senha) {
                $_SESSION['usuario'] = $nome;
                header("Location: index.php");
                exit;
            } else {
                $invisivel_erro = '';
                $mensagem = "senha incorreta";
                break;
            }
        } else {
            $invisivel_erro = '';
            $mensagem = "nome nao encontrado";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>OmegaOn-Login</title>
        <link rel="stylesheet" href="Style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    <header class="Top">
        <div class="Container">
        <a class="title" href="index.php"><b>OmegaOn</b></a>
            <div class="Perfil">
                <a class="Login-Button" href="login.php" <?php echo $invisivel_prelogin; ?>>Login</a>
                <h2 class="Nome" <?php echo $invisivel_poslogin; ?>>
                    <?php echo htmlspecialchars($USUARIO); ?>
                </h2>
            </div>
        </div>
    </header>
    <div class="form">
        <form method="post">
            <h2>Login</h2>
            <h3>Digite seu nome</h3>
            <input type="text" name="nome" autocomplete="off" required>
            <h3>Digite sua senha</h3>
            <input type="text" name="senha" autocomplete="off" required>
            <br>
            
            <h4 class="erro" $invisivel_erro><?= $mensagem?></h4>

            <img src="img/CAPTCHA.png" alt="Captcha" width="240" height="70">
            <button type="submit">Logar</button>
        </form>
        <h4>Não possui conta?<a href="register.php">Registrar</a></h4>

    </div>


    </body>
</html>