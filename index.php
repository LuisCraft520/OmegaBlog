<?php
//:)
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

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
<div class="StartBlog">
    <br>
    <h1>Bem vindo ao AlfaOn</h1>
    <h3>O forum oficial da turma Alfa (feito por louis_louis)</h3>
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
                $texto_horario = "Há {$diferenca->days} dias.";
            } else {
                $horas = ($diferenca->h) + ($diferenca->i / 60);
                if(round($horas) == 0){
                    $texto_horario = "Há pouco tempo";
                } else {
                $texto_horario = "Há " . round($horas) . " h.";
                }
            }
            echo '<a href="postview.php?id=' . $post['id'] . '" class="post_link">';
            echo '<div class="posts">';
            echo '<h4 class="usuario_post"><b>' . htmlspecialchars($post['usuario']) . ' •  </b>' . $texto_horario . '</h4>';
            /*<div class="user-info">
            <img src="<?php echo htmlspecialchars($post_user['imagem'] ?? 'img/default-profile.png'); ?>" alt="Imagem de perfil" class="post-user-img">
            <h4 class="usuario_post"><b>' . htmlspecialchars($post['usuario']) . ' •  </b>' . $texto_horario . '</h4>
            </div>*/

            echo '<h3 class="titulo_post">' . htmlspecialchars($post['titulo']) . '</h3>';
            //fazer com que cada conteudo tenha no maximo 6 linhas
            $texto = $post['conteudo'];

            $linhas = explode("\n", $texto);

            if (count($linhas) > 6) {
                $linhas = array_slice($linhas, 0, 12);
                $texto_reduzido = implode("\n", $linhas) . "\n...";
            } else {
                $texto_reduzido = implode("\n", $linhas);
            }

            echo '<h5 class="conteudo_post">' . htmlspecialchars($texto_reduzido) . '</h5>'; 

            echo '</div>';
            echo '</a>';
            echo '<hr>';
        }
    ?>
</div>
</body>
</html>