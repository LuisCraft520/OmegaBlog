<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

//metodo de abrir o json
$ARQUIVO_JSON_POST = 'json/posts.json';
$json_data_post = file_get_contents($ARQUIVO_JSON_POST);
$posts = json_decode($json_data_post, true);
if ($posts === null) {
    $posts  = [];
}

$ARQUIVO_JSON_USER = 'json/usuarios.json';
$json_data_user = file_get_contents($ARQUIVO_JSON_USER);
$usuarios = json_decode($json_data_user, true);
if ($usuarios === null) {
    $usuarios = [];
}

$seu_user = null;

// Localiza o usuario logado
foreach ($usuarios as $u) {
    if ($u['nome'] === $USUARIO) {
        $seu_user = $u;
        break;
    }
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
        <title>AlfaOn - Login</title>
        <link rel="stylesheet" href="Style.css?v=3.0">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
<header class="Top">
    <div class="Container">
        <a class="title" href="index.php"><b>AlfaOn</b></a>
        <div class="Perfil">
            <a class="Login-Button" href="login.php" <?php echo $invisivel_prelogin; ?>>Login</a>
            <a class="Nome" href="perfview.php?id=<?php echo $seu_user['id']; ?>" <?php echo $invisivel_poslogin; ?>>
                <b><?php echo htmlspecialchars($USUARIO); ?></b>
            </a>
        </div>
    </div>
</header>
    <div class="form">
        <form method="post">
            <h2>Login</h2>
            <h3>Digite seu nome</h3>
            <input type="text" name="nome" autocomplete="off" required>
            <h3>Digite sua senha</h3>
            <input type="password" name="senha" autocomplete="off" required>
            <br>
            
            <h4 class="erro" $invisivel_erro><?= $mensagem?></h4>

            <button type="submit">Logar</button>
        </form>
        <h4>Não possui conta?<a href="register.php">Registrar</a></h4>

    </div>


    </body>
</html>