<?php
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../includes/toast.php';

requer_admin();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_toast('error', 'Produto Inválido', 'ID não informado para exclusão.');
    header('Location: ' . BASE_URL . '/admin/produtos.php');
    exit;
}

try {
    $stmtBusca = $pdo->prepare("SELECT * FROM `produtos` WHERE `id` = :id LIMIT 1");
    $stmtBusca->execute([':id' => $id]);
    $produto = $stmtBusca->fetch();

    if (!$produto) {
        set_toast('warning', 'Não Encontrado', 'Este produto já foi removido.');
        header('Location: ' . BASE_URL . '/admin/produtos.php');
        exit;
    }

    $stmtDel = $pdo->prepare("DELETE FROM `produtos` WHERE `id` = :id");
    $stmtDel->execute([':id' => $id]);

    registrar_log(
        $pdo,
        'EXCLUIR_PRODUTO',
        'produtos',
        $id,
        "Produto excluído: {$produto['nome']}",
        $produto,
        null
    );

    set_toast('success', 'Produto Excluído', "O cosmético \"{$produto['nome']}\" foi removido.");
} catch (PDOException $e) {
    error_log('Erro ao excluir produto: ' . $e->getMessage());
    set_toast('error', 'Erro ao Excluir', 'Não foi possível remover o produto.');
}

header('Location: ' . BASE_URL . '/admin/produtos.php');
exit;
