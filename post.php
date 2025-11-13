<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
if ($USUARIO === null) {
    header('Location: login.php');
    exit(); // Adicionado para evitar que o código continue após o redirecionamento
}

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

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST["titulo"] ?? "";
    $conteudo = $_POST["content"] ?? "";
    $conteudo_formatado = wordwrap($conteudo, 64, "\n", true);
    $link = $_POST["link"] ?? "";

    if (strlen($titulo) > 100) {
        // erro - título muito longo
        echo "O título não pode ter mais de 100 caracteres.";
    } elseif ($USUARIO === null) {
        // erro sem usuário
        echo "Você precisa estar logado para postar.";
    } else {
        //SISTEMA DE UPLOAD DE IMAGENS
        SalvarImagem();

        // Adicionando o post ao array
        if (!empty($posts)) {
            $ids = array_column($posts, 'id');
            $last_id = max($ids);
        }
        $new_id = ($last_id ?? 0) + 1;
        $new_post = [
            "id" => $new_id,
            "usuario" => $USUARIO,
            "data" => (new DateTime("now", null))->format(DateTime::ATOM),
            "titulo" => $titulo,
            "imagem" => $nomeFinalIMG,
            "conteudo" => $conteudo_formatado,
            "link" => $link,
            "comentarios" => [],
        ];
        array_push($posts, $new_post);
        $json_string = json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        file_put_contents($ARQUIVO_JSON_POST, $json_string);
        // Redirecionando para o post após salvar
        header('Location: postview.php?id=' . $new_id);
        exit(); // Finaliza o script após o redirecionamento
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OmegaOn-Post</title>
    <link rel="stylesheet" href="Style.css?v=3.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header class="Top">
    <div class="Container">
        <a class="title" href="index.php"><b>OmegaOn</b></a>
        <div class="Perfil">
            <a class="Login-Button" href="login.php" <?php echo $invisivel_prelogin; ?>>Login</a>
            <a class="Nome" href="perfview.php?id=<?php echo $seu_user['id']; ?>" <?php echo $invisivel_poslogin; ?>>
                <b><?php echo htmlspecialchars($USUARIO); ?></b>
            </a>
        </div>
    </div>
</header>
    <div class="post_creator">
        <form method="post" enctype="multipart/form-data">
            <h2>Postar</h2>
            <br>

            <input type="text" name="titulo" autocomplete="off" placeholder="Título" maxlength="64" required>
            <br><br>

            <h4>Insira aqui sua foto (opcional)</h4>
            <input type="file" name="imagem" accept="image/*">

            <textarea name="content" rows="6" placeholder="Texto do post (opcional)"></textarea>
            <br><br><br>

            <input type="text" name="link" autocomplete="off" placeholder="Insira aqui seu link(opcional)" maxlength="64">
            <br><br>

            <button type="submit">Postar</button>
        </form>
    </div>
</body>
</html>
