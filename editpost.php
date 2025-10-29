<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

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
if (!$post_encontrado or $post_encontrado['usuario'] != $USUARIO) {
    header('Location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"] ?? "";
    $conteudo = $_POST["content"] ?? "";
    $conteudo_formatado = wordwrap($conteudo, 64, "\n", true);

    if (strlen($titulo) > 100) {
        //erro
    } elseif ($USUARIO === null) {
        //erro sem user
    } else {
        $new_array = [];
        foreach ($posts as $post) {
            if ($post['id'] === $id) {
                $post['titulo'] = $titulo;
                $post['conteudo'] = $conteudo_formatado;
            }
            array_push($new_array, $post);

        }
        $json_string = json_encode($new_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($ARQUIVO_JSON, $json_string);
        header('Location: postview.php?id=' . $id);
    }

}
 ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OmegaOn</title>
    <link rel="stylesheet" href="Style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header class="Top">
        <div class="Container">
            <a class="title" href="index.php"><b>OmegaOn</b></a>
            <div class="Perfil">
                <a class="Login-Button" href="login.php" <?php echo $invisivel_prelogin; ?>>Login</a>
                <h2 class="Nome" <?php echo $invisivel_poslogin; ?>>
                    <?php echo htmlspecialchars($USUARIO); ?>
                </h2>
            </div>
        </div>
    </header>
    <div class="post_creator">
        <form method="post">
            <h2>Editar post</h2>
            <br>
            <input type="text" name="titulo" autocomplete="off" placeholder="Titulo" <?php echo 'value="' . htmlspecialchars($post_encontrado['titulo']) . '"' ?> maxlength="64" required>
            <br><br><br>
            <textarea name="content" rows="6" placeholder="Texto do post (opcional)"><?php echo htmlspecialchars($post_encontrado['conteudo']); ?></textarea>
            <br><br><br>
            <button type="submit">Salvar alteraçoes</button>
        </form>
    </div>
</body>