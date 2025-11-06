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

$deletar_image = false;

foreach ($posts as $p) {
    if ($p['id'] === $id) {
        $post_encontrado = $p;
        break;
    }
}
if ($USUARIO != "ADMIN_LOUIS") {
if (!$post_encontrado or $post_encontrado['usuario'] != $USUARIO) {
    header('Location: index.php');
    exit;
}
}

$pastaIMG = 'uploadsIMG/';
$pastaVID = 'uploadsVID/';
$nomeFinalIMG = '';
function SalvarImagem() {
    global $pastaIMG, $USUARIO, $nomeFinalIMG;
    
    if (!is_dir($pastaIMG . $USUARIO . "/")) {
        mkdir($pastaIMG . $USUARIO . "/", 0777, true);
    }

    // Verificando se o arquivo de imagem foi enviado
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $arquivoIMG = $_FILES['imagem'];
        $nomeTempIMG = $arquivoIMG['tmp_name'];
        $nomeFinalIMG = $pastaIMG . $USUARIO . "/" . basename($arquivoIMG['name']);

        // Verificando o tipo de imagem
        $tipoIMG = mime_content_type($nomeTempIMG);
        $permitidosIMG = ['image/jpeg', 'image/png'];

        if (!in_array($tipoIMG, $permitidosIMG)) {
            die("Arquivo inválido. Somente imagens JPEG ou PNG são permitidas.");
        }

        // Limitar o tamanho do arquivo (ex: 5MB)
        if ($arquivoIMG['size'] > 5 * 1024 * 1024) {
            die("O arquivo é muito grande. O tamanho máximo permitido é 5MB.");
        }

        // Movendo o arquivo para o diretório de upload
        if (move_uploaded_file($nomeTempIMG, $nomeFinalIMG)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["salvar"])) {
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
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
                    if (SalvarImagem()) {
                    unlink($post['imagem']);
                    $post['imagem'] = $nomeFinalIMG;
                    }
                }
                if ($deletar_image) {
                    $post['imagem'] = null;
                    if(file_exists($post['imagem'])) {
                        unlink($post['imagem']);
                    }
                }
            }
            array_push($new_array, $post);
          
        } 
        $json_string = json_encode($new_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($ARQUIVO_JSON, $json_string);
        header('Location: postview.php?id=' . $id);
    }

}
//deleta imagem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["remove_image"])) {
    $deletar_image = true;
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
        <form method="post" enctype="multipart/form-data">
            <h2>Editar post</h2>
            <br>

            <input type="text" name="titulo" autocomplete="off" placeholder="Titulo" <?php echo 'value="' . htmlspecialchars($post_encontrado['titulo']) . '"' ?> maxlength="64" required>
            <br><br><br>

            <h4>Troque ou remova sua foto</h4>
            <input type="file" name="imagem" accept="image/*">
            <button type="submit" name="remove_image" value="Remover imagem">Remover imagem</button>
            <br><br>

            <textarea name="content" rows="6" placeholder="Texto do post (opcional)"><?php echo htmlspecialchars($post_encontrado['conteudo']); ?></textarea>
            <br><br><br>

            <button type="submit" name="salvar">Salvar alteraçoes</button>
        </form>
    </div>
</body>
</html>