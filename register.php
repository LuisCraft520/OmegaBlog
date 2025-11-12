<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

//metodo de abrir json
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
function validarSenha($senha) {
    if (strlen($senha) < 8) {
        return "A senha deve ter pelo menos 8 caracteres.";
    }
    if (!preg_match("/[a-z]/", $senha)) {
        return "A senha deve conter pelo menos uma letra minúscula.";
    }
    if (!preg_match("/[A-Z]/", $senha)) {
        return "A senha deve conter pelo menos uma letra maiúscula.";
    }
    if (!preg_match("/[0-9]/", $senha)) {
        return "A senha deve conter pelo menos um número.";
    }
    return true;
}

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
    if (strlen($nome) < 4) {
        $invisivel_erro = '';
        $mensagem = "o nome presisa ter mais de 4 caracteres";
    } elseif ($senha !== $senha2) {
        $invisivel_erro = '';
        $mensagem = "Confirme sua senha novamente";
    } elseif (validarSenha($senha) !== true) {
        $invisivel_erro = '';
        $mensagem = validarSenha($senha);
    } else {
    if (!empty($usuarios)) {
        $ids = array_column($usuarios, 'id');
        $last_id = max($ids);
        }
    $new_id = ($last_id ?? 0) + 1;
    $usuario = [        
        "id" => $new_id,
        "nome" => $nome,
        "senha" => $senha,
        "imagem" => "img\/Perfil.jpeg",
        "bio" => ""
    ];
    array_push($usuarios, $usuario);

    $json_string = json_encode($usuarios, JSON_PRETTY_PRINT);

    file_put_contents($ARQUIVO_JSON, $json_string);

    $_SESSION['usuario'] = $nome;
    header('Location: editperf.php');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>OmegaOn-Registrar</title>
        <link rel="stylesheet" href="Style.css?v=3.0">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
<header class="Top">
    <div class="Container">
        <a class="title" href="index.php"><b>OmegaOn</b></a>
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
            <h2>Registrar</h2>
            <h3>Digite seu nome</h3>
            <input type="text" name="nome" autocomplete="off" required>
            <h3>Digite sua senha</h3>
            <input type="password" name="senha" autocomplete="off" required>
            <h3>Confirme sua senha</h3>
            <input type="password" name="senha2" autocomplete="off" required>
            <br>
            <h4 class="erro" $invisivel_erro><?= $mensagem?></h4>
            <button type="submit">Registrar</button>
        </form>
        <br>
        <h4>Ja possui conta?<a href="login.php">Logar</a></h4>

    </div>
    </body>
</html>