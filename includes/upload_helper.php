<?php
/**
 * Helper compartilhado para upload seguro de imagens de cosméticos.
 */

function processar_upload_produto(array $file, ?string $imagemFallback = null): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'path' => $imagemFallback];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Falha ao enviar a imagem do produto.'];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'A imagem deve ter no máximo 5MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $permitidos = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($permitidos[$mime])) {
        return ['ok' => false, 'error' => 'Formato inválido. Use apenas JPG, PNG ou WEBP.'];
    }

    $dirDestino = dirname(__DIR__) . '/assets/images/produtos';
    if (!is_dir($dirDestino)) {
        mkdir($dirDestino, 0755, true);
    }

    $nomeArquivo = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $permitidos[$mime];
    $caminhoCompleto = $dirDestino . '/' . $nomeArquivo;

    if (!move_uploaded_file($file['tmp_name'], $caminhoCompleto)) {
        return ['ok' => false, 'error' => 'Não foi possível salvar a imagem no servidor.'];
    }

    return ['ok' => true, 'path' => 'assets/images/produtos/' . $nomeArquivo];
}
