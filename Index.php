<?php
$invisivel_prelogin = "";
$invisivel_poslogin = "style='display = none'";

$USUARIO = null;

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>OmegaBlog</title>
        <link rel="stylesheet" href="Style.css">
    </head>
    <body>
        <header class="Top">
            <h2>OmegaBlog</h2>
            <div class="Perfil" $invisivel_prelogin>
                <a rpl class="Login-Button" href="login.php">Login</a>
                <h2 class="Nome" $invisivel_poslogin><?php $USUARIO ?></h2>
            </div>
        </div>
    </body>
</html>