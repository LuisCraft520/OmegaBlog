<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;
if ($USUARIO === null) {
    header('Location: login.php');
}

$ARQUIVO_JSON = 'json/posts.json';
$json_data = file_get_contents($ARQUIVO_JSON);
$posts = json_decode($json_data, true);
if ($posts === null) {
    $posts  = [];
}

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

$pastaIMG = 'uploadsIMG/';
$pastaVID = 'uploadsVID/';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"] ?? "";
    $conteudo = $_POST["content"] ?? "";
    $conteudo_formatado = wordwrap($conteudo, 64, "\n", true);

    if (strlen($titulo) > 100) {
        //erro
    } elseif ($USUARIO === null) {
        //erro sem user
    } else {

        if (!is_dir($pastaIMG)) {
            mkdir($pastaIMG, 0777, true);
        }

        $arquivoIMG = $_FILES['imagem'];
        $nomeTemp = $arquivoIMG['tmp_name'];
        $nomeFinal = $pastaIMG . "_" . $USUARIO . "_" . basename($arquivoIMG['name']);

        $tipoIMG = mime_content_type($nomeTemp);
        $permitidosIMG = ['image/jpeg', 'image/png'];

        if (!in_array($tipoIMG,$permitidosIMG)) {
            die();
        }



        if (!empty($posts)) {
            $ids = array_column($posts, 'id');
            $last_id = max($ids);
        }
        $new_id = ($last_id ?? 0) + 1;
        $new_post = [
            "id" => $new_id,
            "usuario" => $USUARIO,
            "data" => (new DateTime("now",null))->format(DateTime::ATOM),
            "titulo" => $titulo,
            "conteudo" => $conteudo_formatado,
            "comentarios" => [],
        ];
        array_push($posts, $new_post);
        $json_string = json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        file_put_contents($ARQUIVO_JSON, $json_string);
        header('Location: postview.php?id=' . $new_id);
    }

}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OmegaOn-Post</title>
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
                    <?php echo htmlspecialchars(string: $USUARIO); ?>
                </h2>
            </div>
        </div>
    </header>
    <div class="post_creator">
        <form method="post" enctype="multipart/form-data">
            <h2>Postar</h2>
            <br>

            <input type="text" name="titulo" autocomplete="off" placeholder="Título" maxlength="64" required>
            <br><br><br>

            <input type="file" name="imagem" accept="image/*">
            <br><br><br>

            <textarea name="content" rows="6" placeholder="Texto do post (opcional)"></textarea>
            <br><br><br>

            <button type="submit">Postar</button>
        </form>

</body>
</html>
