<?php
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../includes/toast.php';
require_once __DIR__ . '/../includes/upload_helper.php';

requer_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/admin/produtos.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$categoriaId = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT) ?: null;
$descricao = trim($_POST['descricao'] ?? '');
$modoUso = trim($_POST['modo_uso'] ?? '');
$preco = (float) str_replace(',', '.', $_POST['preco'] ?? '0');
$precoPromoRaw = trim($_POST['preco_promocional'] ?? '');
$precoPromo = $precoPromoRaw !== '' ? (float) str_replace(',', '.', $precoPromoRaw) : null;
$estoque = max(0, (int) ($_POST['estoque'] ?? 0));
$estoqueMinimo = max(0, (int) ($_POST['estoque_minimo'] ?? 5));
$destaque = isset($_POST['destaque']) ? 1 : 0;
$ativo = isset($_POST['ativo']) ? 1 : 0;
$imagemSelecionada = trim($_POST['imagem_galeria'] ?? '');

if (empty($nome) || $preco <= 0) {
    set_toast('error', 'Dados Inválidos', 'Informe o nome e um preço válido maior que zero.');
    header('Location: ' . BASE_URL . '/admin/produto_form.php');
    exit;
}

$upload = processar_upload_produto($_FILES['imagem'] ?? [], $imagemSelecionada !== '' ? $imagemSelecionada : null);
if (!$upload['ok']) {
    set_toast('error', 'Erro na Imagem', $upload['error']);
    header('Location: ' . BASE_URL . '/admin/produto_form.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO `produtos`
        (`categoria_id`, `nome`, `descricao`, `modo_uso`, `preco`, `preco_promocional`, `estoque`, `estoque_minimo`, `imagem`, `destaque`, `ativo`)
        VALUES
        (:categoria_id, :nome, :descricao, :modo_uso, :preco, :preco_promocional, :estoque, :estoque_minimo, :imagem, :destaque, :ativo)
    ");

    $dadosNovos = [
        'categoria_id'      => $categoriaId,
        'nome'              => $nome,
        'descricao'         => $descricao,
        'modo_uso'          => $modoUso,
        'preco'             => $preco,
        'preco_promocional' => $precoPromo,
        'estoque'           => $estoque,
        'estoque_minimo'    => $estoqueMinimo,
        'imagem'            => $upload['path'],
        'destaque'          => $destaque,
        'ativo'             => $ativo,
    ];

    $stmt->execute([
        ':categoria_id'      => $categoriaId,
        ':nome'              => $nome,
        ':descricao'         => $descricao,
        ':modo_uso'          => $modoUso,
        ':preco'             => $preco,
        ':preco_promocional' => $precoPromo,
        ':estoque'           => $estoque,
        ':estoque_minimo'    => $estoqueMinimo,
        ':imagem'            => $upload['path'],
        ':destaque'          => $destaque,
        ':ativo'             => $ativo,
    ]);

    $novoId = (int) $pdo->lastInsertId();

    registrar_log(
        $pdo,
        'CRIAR_PRODUTO',
        'produtos',
        $novoId,
        "Produto criado: {$nome} (Estoque: {$estoque})",
        null,
        $dadosNovos
    );

    set_toast('success', 'Cosmético Cadastrado', "O produto \"{$nome}\" foi adicionado ao catálogo.");
    header('Location: ' . BASE_URL . '/admin/produtos.php');
    exit;
} catch (PDOException $e) {
    error_log('Erro ao criar produto: ' . $e->getMessage());
    set_toast('error', 'Erro no Banco', 'Não foi possível cadastrar o produto.');
    header('Location: ' . BASE_URL . '/admin/produto_form.php');
    exit;
}
