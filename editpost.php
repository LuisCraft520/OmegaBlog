<?php
session_start();

$USUARIO = $_SESSION['usuario'] ?? null;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

$ARQUIVO_JSON = 'json/posts.json';
$json_data = file_get_contents($ARQUIVO_JSON);
$posts = json_decode($json_data, true);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$post_encontrado = null;

// Localiza o post
foreach ($posts as $p) {
    if ($p['id'] === $id) {
        $post_encontrado = $p;
        break;
    }
}

// Proteção de acesso
if ($USUARIO != "ADMIN_LOUIS") {
    if (!$post_encontrado || $post_encontrado['usuario'] != $USUARIO) {
        header('Location: index.php');
        exit;
    }
}

$pastaIMG = 'uploadsIMG/';
$nomeFinalIMG = '';

/**
 * Função para salvar imagem
 */
function SalvarImagem()
{
    global $pastaIMG, $USUARIO, $nomeFinalIMG;

    if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== 0) {
        return false;
    }

    $arquivoIMG = $_FILES['imagem'];
    $nomeTempIMG = $arquivoIMG['tmp_name'];
    $extensao = strtolower(pathinfo($arquivoIMG['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png'];

    // Extensão segura
    if (!in_array($extensao, $permitidos)) {
        die("Arquivo inválido. Somente imagens JPEG ou PNG são permitidas.");
    }

    // Cria pasta do usuário, se não existir
    $pastaUser = $pastaIMG . $USUARIO . "/";
    if (!is_dir($pastaUser)) {
        mkdir($pastaUser, 0777, true);
    }

    // Nome final único
    $nomeFinalIMG = $pastaUser . uniqid('IMG_', true) . '.' . $extensao;

    // Verifica tipo MIME real
    $tipoIMG = mime_content_type($nomeTempIMG);
    $permitidosMIME = ['image/jpeg', 'image/png'];
    if (!in_array($tipoIMG, $permitidosMIME)) {
        die("Arquivo inválido. Somente imagens JPEG ou PNG são permitidas.");
    }

    // Limita tamanho (5MB)
    if ($arquivoIMG['size'] > 5 * 1024 * 1024) {
        die("O arquivo é muito grande. O tamanho máximo permitido é 5MB.");
    }

    // Move o arquivo
    return move_uploaded_file($nomeTempIMG, $nomeFinalIMG);
}

/**
 * Processamento do formulário
 */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titulo = $_POST["titulo"] ?? "";
    $conteudo = $_POST["content"] ?? "";
    $conteudo_formatado = wordwrap($conteudo, 64, "\n", true);
    $link = $_POST["link"] ?? "";
    $deletar_image = isset($_POST["remove_image"]);

    if (strlen($titulo) > 100) {
        die("Erro: título muito longo.");
    } elseif ($USUARIO === null) {
        die("Erro: você precisa estar logado para editar.");
    } else {
        $new_array = [];

        foreach ($posts as $post) {
            if ($post['id'] === $id) {
                // Atualiza campos
                $post['titulo'] = $titulo;
                $post['conteudo'] = $conteudo_formatado;
                $post['link'] = $link;

                // Upload de nova imagem
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
                    if (SalvarImagem()) {
                        if (!empty($post['imagem']) && file_exists($post['imagem'])) {
                            unlink($post['imagem']);
                        }
                        $post['imagem'] = $nomeFinalIMG;
                    }
                }

                // Remover imagem
                if ($deletar_image) {
                    if (!empty($post['imagem']) && file_exists($post['imagem'])) {
                        unlink($post['imagem']);
                    }
                    $post['imagem'] = null;
                }
            }

            $new_array[] = $post;
        }

        // Salva alterações
        $json_string = json_encode($new_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($ARQUIVO_JSON, $json_string);

        // Redireciona
        header('Location: postview.php?id=' . $id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OmegaOn - Editar Post</title>
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

        <input type="text" name="titulo" autocomplete="off"
               placeholder="Título"
               value="<?php echo htmlspecialchars($post_encontrado['titulo']); ?>"
               maxlength="100" required>
        <br><br>

        <h4>Imagem do post</h4>

        <?php if (!empty($post_encontrado['imagem'])): ?>
            <img src="<?php echo htmlspecialchars($post_encontrado['imagem']); ?>" alt="Imagem atual" width="150"><br><br>
        <?php endif; ?>

        <input type="file" name="imagem" accept="image/*">
        <button type="submit" name="remove_image" value="1">Remover imagem</button>
        <br><br>

        <textarea name="content" rows="6" placeholder="Texto do post (opcional)"><?php echo htmlspecialchars($post_encontrado['conteudo']); ?></textarea>
        <br><br><br>

        <input type="text" name="link" autocomplete="off"
               placeholder="Link (opcional)"
               value="<?php echo htmlspecialchars($post_encontrado['link']); ?>"
               maxlength="64">
        <br><br>

        <button type="submit" name="salvar">Salvar alterações</button>
    </form>
</div>
</body>
</html>
