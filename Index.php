<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

$ARQUIVO_JSON = 'json/posts.json';
$json_data = file_get_contents($ARQUIVO_JSON);
$posts = json_decode($json_data, true);
if ($posts === null) {
    $posts  = "";
}

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
    <?php
        //separa da data mais recente pra mais a mais antiga, invertendo os posts
        usort($posts, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });
        //aparece cada um dos posts
        foreach($posts as $post) {
            //verificador de data e hora
            $texto_horario = '';

            $data_post = new DateTime($post['data']);
            $agora = new DateTime(); // horário atual

            $diferenca = $agora->diff($data_post);

            if ($diferenca->days >= 1) {
                $texto_horario = "Ha {$diferenca->days} dias.";
            } else {
                $horas = ($diferenca->h) + ($diferenca->i / 60);
                $texto_horario = "Ha " . round($horas, 1) . " horas.";
            }

            echo '<div class="posts">';
            echo '<h4 class="usuario_post"><b>' . $post['usuario'] . ' •  </b>' . $texto_horario . '</h4>';
            echo '<h3 class="titulo_post">' . $post['titulo'] . '</h3>';
            //fazer com que cada conteudo tenha no maximo 6 linhas
            $texto = $post['conteudo'];

            $linhas = explode("\n", $texto);

            if (count($linhas) > 6) {
                $linhas = array_slice($linhas, 0, 12);
                $texto_reduzido = implode("\n", $linhas) . "\n...";
            } else {
                $texto_reduzido = implode("\n", $linhas);
            }

            echo '<h5 class="conteudo_post">' . $texto_reduzido . '</h5>'; 



            echo '</div>';
            echo '<hr>';
        }
    ?>
</div>
</body>
</html>