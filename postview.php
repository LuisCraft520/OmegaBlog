<?php 
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

$invisivel_user ='style="display: none;"';

$invisivel_popup = 'style="display: none;"';

$ARQUIVO_JSON = 'json/posts.json';
$json_data = file_get_contents($ARQUIVO_JSON);
$posts = json_decode($json_data, true);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$post_encontrado = null;

foreach ($posts as $p) {
    if ($p['id'] === $id) {
        $post_encontrado = $p;
        break;
    }
}
if (!$post_encontrado) {
    header('Location: index.php');
    exit;
}
//comentarios 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["comentar"])) {
    $comentario = $_POST["comentario"] ?? "";
    $comentario_formatado = wordwrap($comentario, 64, "\n", true);
    if ($USUARIO === null) {
         /*erro*/
    } elseif ($comentario !== "") {
        $new_array = [];
        foreach ($posts as $post) {
            if ($post['id'] === $id) {
                if (!empty($post['comentarios'])) {
                    $ids = array_column($post['comentarios'], 'id');
                    $last_id = max($ids);
                }
                $new_id = ($last_id ?? 0) + 1;
                $new_comment = [
                    "id" => $new_id, 
                    "usuario" => $USUARIO, 
                    "data" => (new DateTime("now",null))->format('Y-m-d H:i:s'), 
                    "comentario" => $comentario_formatado
                ];
                array_push($post['comentarios'], $new_comment);
            }
            array_push($new_array, $post);



        }
        $json_string = json_encode($new_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($ARQUIVO_JSON, $json_string);
        header('Location: postview.php?id=' . $id);
    }
}
//edit post
if($post_encontrado['usuario'] == $USUARIO or $USUARIO == "ADMIN_LOUIS"){
    $invisivel_user ='';
}
//delete post
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])){
    if ($USUARIO === null) {
        //erro
    } else {
        $invisivel_popup = 'style="display: flex;"';
    }
}
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['confirm-popup'])){
    $new_array = [];
    foreach ($posts as $post) {
        if ($post['id'] === $id) {
            if(file_exists($post['imagem'])) {
                unlink($post['imagem']);
            }
        } else {
            array_push($new_array, $post);
        }
    }
        $json_string = json_encode($new_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($ARQUIVO_JSON, $json_string);
        header('Location: postview.php?id=' . $id);
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>OmegaOn</title>
    <link rel="stylesheet" href="Style.css?v=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <header class="Top">
        <div class="Container"> <a class="title" href="index.php"><b>OmegaOn</b></a>
            <div class="Perfil"> <a class="Login-Button" href="login.php" <?php echo $invisivel_prelogin; ?>>Login</a>
                <h2 class="Nome" <?php echo $invisivel_poslogin; ?>> <?php echo htmlspecialchars($USUARIO); ?> </h2>
            </div>
        </div>
    </header>

    <div class="post-completo">
        <div class="icos">
            <b><?php echo htmlspecialchars($post_encontrado['usuario']); ?></b>
            <div class="ico-actions" <?php echo $invisivel_user; ?>>
                <a class="edit-ico" href="editpost.php?id=<?php echo $id; ?>">
                    <img src="img/editar.png" alt="editar" width="20" height="20">
                </a>
                <form method="post">
                    <button type="submit" name="delete" class="del-ico">
                        <img src="img/delete.png" alt="excluir" width="20" height="20">
                    </button>
                </form>
            </div>
        </div>

        <h2><?php echo htmlspecialchars($post_encontrado['titulo']); ?></h2>
        <?php
        if ($post_encontrado['imagem']) {
            echo "<img class='post-image' src=" . htmlspecialchars($post_encontrado['imagem']) . " alt='Imagem do post'>";  
        } ?>

        <p><?php echo nl2br(htmlspecialchars($post_encontrado['conteudo'])); ?></p>
        
        <?php
        if ($post_encontrado['link']) {
            echo '<a href=' . htmlspecialchars($post_encontrado['link']) . ' >' . htmlspecialchars($post_encontrado['link']) . '</a>';
        } ?>

        <h4 class="ERRO" <?php echo $invisivel_prelogin; ?>> Voce nao esta logado, logue em nosso site para Comentar nos posts </h4>
        <br>
        <div class="comentarios" <?php echo $invisivel_poslogin; ?>><!--invisivel poslogin-->
            <br>
            <h2>Comentários</h2>
            <div class="comentar">
                <form method="post"> 
                    <textarea type="text" name="comentario" rows="2" placeholder="Adicione sua resposta."></textarea> 
                    <button type="submit" name="comentar">Comentar</button> 
                </form>
            </div> 
        </div>
        <br> 
        <?php
                usort($post_encontrado['comentarios'], function($a, $b) {
                    return strtotime($b['data']) - strtotime($a['data']);
                });
                foreach($post_encontrado['comentarios'] as $comentario){
                    //verificador de data e hora
                    $texto_horario = '';

                    $data_comment = new DateTime($comentario['data']);
                    $agora = new DateTime("now",null); // horário atual

                    $diferenca = $agora->diff($data_comment);

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

                    echo '<div class="coment">';
                    echo '<h4 class="usuario_coment"><b>' . htmlspecialchars($comentario['usuario']) . ' •  </b>' . $texto_horario . '</h4>';
                    //fazer com que cada conteudo tenha no maximo 6 linhas
                    $texto = $comentario['comentario'];

                    $linhas = explode("\n", $texto);

                    if (count($linhas) > 6) {
                        $linhas = array_slice($linhas, 0, 12);
                        $texto_reduzido = implode("\n", $linhas) . "\n...";
                    } else {
                        $texto_reduzido = implode("\n", $linhas);
                    }

                    echo '<h5 class="conteudo_coment">' . htmlspecialchars($texto_reduzido) . '</h5>'; 

                    echo '</div>';
                    echo '<hr>';
                }
        
        ?>
    </div>
<div class="popup" <?php echo $invisivel_popup; ?>>
  <div class="popup-backdrop"></div>
  <div class="popup-content">
    <h3>Confirmar exclusão</h3>
    <p>Tem certeza que deseja deletar este post?</p>
    <form class="botoes" method="post">
      <button class="btn-confirm" name="confirm-popup">Sim, deletar</button>
      <button class="btn-cancel">Cancelar</button>
    </form>
  </div>
</div>
</body>
</html>