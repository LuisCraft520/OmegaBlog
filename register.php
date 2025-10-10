<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

//metodo de abrir json
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
    $senha2 = $_POST["senha2"] ?? "";
    foreach ($usuarios as $usuario) {
        if ($usuario["nome"] == $nome) {
            $mensagem = "Ja existe um usuario com esse nome";
            $invisivel_erro = '';
            break;
        }
    }
    if ($senha !== $senha2) {
        $invisivel_erro = '';
        $mensagem = "Confirme sua senha novamente";
    } else {
    $last_id = max($usuarios) ?? 0;
    $new_id = $last_id["id"] + 1;
    $usuario = [        
        "id" => $new_id,
        "nome" => $nome,
        "senha" => $senha,
    ];
    array_push($usuarios, $usuario);

    $json_string = json_encode($usuarios, JSON_PRETTY_PRINT);

    file_put_contents($ARQUIVO_JSON, $json_string);
    $mensagem = "Registrado com sucesso, volte ao nosso site de login";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>OmegaOn-Registrar</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
    <header class="Top">
        <div class="Container">
            <a class="title" href="Index.php"><b>OmegaOn</b></a>
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
            <h2>Registrar</h2>
            <h3>Digite seu nome</h3>
            <input type="text" name="nome" autocomplete="off" required>
            <h3>Digite sua senha</h3>
            <input type="text" name="senha" autocomplete="off" required>
            <h3>Confirme sua senha</h3>
            <input type="text" name="senha2" autocomplete="off" required >
            <br>
            <h4 class="erro" $invisivel_erro><?= $mensagem?></h4>
            <img src="img/CAPTCHA.png" alt="Captcha" width="240" height="70">
            <button type="submit">Registrar</button>
        </form>
        <br>
        <h4>Ja possui conta?<a href="login.php">Logar</a></h4>
        
    </div>
    </body>
</html>