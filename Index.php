<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? null : $_SESSION['usuario'];

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OmegaBlog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="Top">
        <div class="Container">
            <h2 class="title">OmegaBlog</h2>
            <div class="Perfil">
                <a class="Login-Button" href="login.php" <?php echo $invisivel_prelogin; ?>>Login</a>
                <h2 class="Nome" <?php echo $invisivel_poslogin; ?>>
                    <?php echo htmlspecialchars($USUARIO); ?>
                </h2>
            </div>
        </div>
    </header>
</body>
</html>