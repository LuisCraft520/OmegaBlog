<?php
session_start();

$USUARIO = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null ;

$invisivel_prelogin = $USUARIO ? 'style="display: none;"' : '';
$invisivel_poslogin = $USUARIO ? '' : 'style="display: none;"';

$ARQUIVO_JSON = 'json/usuarios.json';
$json_data = file_get_contents($ARQUIVO_JSON);
$usuarios = json_decode($json_data, true);

$seu_user = null;

// Localiza o usuario logado
foreach ($usuarios as $u) {
    if ($u['nome'] === $USUARIO) {
        $seu_user = $u;
        break;
    }
}

// Proteção de acesso
if ($USUARIO != "ADMIN_LOUIS") {
    if (!$seu_user || $seu_user['nome'] != $USUARIO) {
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
    $nomeFinalIMG = $pastaUser . uniqid('Perfil_', true) . '.' . $extensao;

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
    $nome = $_POST["nome"] ?? "NONAME";
    $bio = $_POST["bio"] ?? "";
    $bio_formatada = wordwrap($bio, 64, "\n", true);
    $deletar_image = isset($_POST["remove_image"]);

    if (strlen($titulo) > 20) {
        die("Erro: nome muito longo.");
    } elseif ($USUARIO === null) {
        die("Erro: você precisa estar logado para editar.");
    } else {
        $new_array = [];

        foreach ($usuarios as $usuario) {
            if ($usuario['nome'] === $USUARIO) {
                // Atualiza campos
                $usuario['nome'] = $nome;
                $usuario['bio'] = $bio_formatada;

                // Upload de nova imagem
                if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
                    if (SalvarImagem()) {
                        if (!empty($usuario['imagem']) && file_exists($usuario['imagem'])) {
                            unlink($usuario['imagem']);
                        }
                        $usuario['imagem'] = $nomeFinalIMG;
                    }
                }

                // Remover imagem
                if ($deletar_image) {
                    if (!empty($usuario['imagem']) && file_exists($usuario['imagem'])) {
                        unlink($usuario['imagem']);
                    }
                    $usuario['imagem'] = "img\/Perfil.jpeg";
                }
            }

            $new_array[] = $usuario;
        }

        // Salva alterações
        $json_string = json_encode($new_array, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($ARQUIVO_JSON, $json_string);

        // Redireciona
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>OmegaOn</title>
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
        <h2>Editar perfil</h2>
        <br>

        <input type="text" name="nome" autocomplete="off"
               placeholder="Nome da conta"
               value="<?php echo htmlspecialchars($seu_user['nome']); ?>"
               maxlength="20" required>
        <br><br>

        <h4>Foto do perfil</h4>

        <?php if (!empty($seu_user['imagem'])): ?>
            <img src="<?php echo htmlspecialchars($seu_user['imagem']); ?>" alt="Imagem atual" width="150"><br><br>
        <?php endif; ?>

        <input type="file" name="imagem" accept="image/*">
        <button type="submit" name="remove_image" value="1">Remover imagem</button>
        <br><br>

        <textarea name="bio" rows="6" placeholder="Bio"><?php echo htmlspecialchars($seu_user['bio']); ?></textarea>
        <br><br><br>

        <button type="submit" name="salvar">Salvar alterações</button>
    </form>
</div>
</body>
</html>  