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
    <title>OmegaOn-Post</title>
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
    <div class="post">
        <form method="post">
            <h2>Postar</h2>
            <br>
            <h3>Titulo</h3>
            <input type="text" name="titulo" autocomplete="off" required>
            <h3>Texto do post(opicional)</h3>
            <input type="text" name="text" autocomplete="off" required>
            <button type="submit">Postar</button>
        </form>
    </div>
</body>
</html>