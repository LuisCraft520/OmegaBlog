<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OmegaOn</title>
    <link rel="stylesheet" href="style.css">
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
<div class="StartBlog">
    <br>
    <h1>Bem vindo ao OmegaOn</h1>
    <h3>O forum oficial da turma omega</h3>
    <br>
    <h4 <?php echo $invisivel_prelogin; ?>> Voce nao esta logado, logue em nosso site para Postar no forun </h4>
    <a class="Post-Button" href="post.php" <?php echo $invisivel_poslogin; ?>>Postar</a>
</div>
<div class="content">
    <!--futuro local dos posts-->
</div>
</body>
</html>