<?php 
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';


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
    header('Location: Index.php');
    exit;
}
//comentarios 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comentario = $_POST["comentario"] ?? "";
    $comentario_formatado = wordwrap($comentario, 64, "\n", false);
    if ($USUARIO === null) { /*erro sem user*/
    } else {
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
                "data" => new DateTime(), 
                "comentario" => $comentario_formatado
                ];
                array_push($post['comentarios'], $new_comment);
                $json_string = json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                file_put_contents($ARQUIVO_JSON, $json_string);
                break;
            }
        }
    }
} ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>OmegaOn</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header class="Top">
        <div class="Container"> <a class="title" href="Index.php"><b>OmegaOn</b></a>
            <div class="Perfil"> <a class="Login-Button" href="login.php" <?php echo $invisivel_prelogin; ?>>Login</a>
                <h2 class="Nome" <?php echo $invisivel_poslogin; ?>> <?php echo htmlspecialchars($USUARIO); ?> </h2>
            </div>
        </div>
    </header>
    <div class="post-completo">
        <h2><?php echo htmlspecialchars($post_encontrado['titulo']); ?></h2>
        <p><b><?php echo htmlspecialchars($post_encontrado['usuario']); ?></b></p>
        <p><?php echo nl2br(htmlspecialchars($post_encontrado['conteudo'])); ?></p>
    </div>
    <hr>
    <div class="comentarios">
        <h2>Comentários</h2>
        <div class="comentar">
            <form method="post"> 
                <textarea type="text" name="comentario" rows="1" placeholder="Adicione sua resposta."></textarea> 
                <button type="submit">Comentar</button> 
            </form>
        </div> 
        <br> 
        <?php ?>
    </div>