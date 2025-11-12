<?php
//:)
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

$invisivel_user ='style="display: none;"';

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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$usuario_encontrado = null;

foreach ($usuarios as $u) {
    if ($u['id'] === $id) {
        $usuario_encontrado = $u;
        break;
    }
}
if (!$usuario_encontrado) {
    header('Location: index.php');
    exit;
}

if($usuario_encontrado['nome'] == $USUARIO){
    $invisivel_user ='';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>AlfaOn</title>
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
<div class="perfil-view">
    <!-- na mesma linha -->
    <img class="perf-img" src="<?php echo htmlspecialchars($usuario_encontrado['imagem']) ?>" alt="Imagem de perfil">
    <br><br><br><h2><?php echo htmlspecialchars($usuario_encontrado['nome']); ?></h2><br>
    <!-- volta ao normal clear: both; -->
    <br><h3>Bio:</h3>
    <p><?php echo htmlspecialchars($usuario_encontrado['bio']); ?></p><br>
    <a class="edit-button" href="editperf.php" <?php echo $invisivel_user; ?>>Editar Perfil</a>
</div>
</body>
</html>