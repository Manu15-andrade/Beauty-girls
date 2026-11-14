<?php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$admin_title = ($id ? 'Editar Cosmético' : 'Novo Cosmético') . ' | Painel Admin Beauty Girls';
require_once __DIR__ . '/includes/admin_header.php';

$produto = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM `produtos` WHERE `id` = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $produto = $stmt->fetch();
    if (!$produto) {
        set_toast('error', 'Não Encontrado', 'Produto não encontrado.');
        header('Location: ' . BASE_URL . '/admin/produtos.php');
        exit;
    }
}

$categorias = $pdo->query("SELECT * FROM `categorias` ORDER BY `nome` ASC")->fetchAll();
$imagensDisponiveis = glob(__DIR__ . '/../assets/images/produtos/*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
$actionUrl = BASE_URL . '/actions/' . ($produto ? 'produto_update.php' : 'produto_create.php');
?>

<main class="flex-1 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="font-serif text-2xl font-bold text-gray-900">
                    <?= $produto ? 'Editar Cosmético' : 'Cadastrar Novo Cosmético' ?>
                </h1>
                <p class="text-xs text-gray-500 mt-1">Todas as alterações são auditadas automaticamente.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/produtos.php" class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                ← Voltar
            </a>
        </div>

        <form action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-rose-100 shadow-sm p-6 sm:p-8 space-y-5">
            <?php if ($produto): ?>
                <input type="hidden" name="id" value="<?= (int) $produto['id'] ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="nome" class="block text-xs font-bold text-gray-700 mb-1">Nome do Cosmético *</label>
                    <input type="text" name="nome" id="nome" required value="<?= htmlspecialchars($produto['nome'] ?? '') ?>" placeholder="Ex: Sérum Facial Vitamina C" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                </div>

                <div>
                    <label for="categoria_id" class="block text-xs font-bold text-gray-700 mb-1">Categoria</label>
                    <select name="categoria_id" id="categoria_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs bg-white focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                        <option value="">Sem categoria</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= ((int) ($produto['categoria_id'] ?? 0) === (int) $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="preco" class="block text-xs font-bold text-gray-700 mb-1">Preço Normal (R$) *</label>
                    <input type="number" step="0.01" min="0.01" name="preco" id="preco" required value="<?= htmlspecialchars($produto['preco'] ?? '') ?>" placeholder="89.90" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                </div>

                <div>
                    <label for="preco_promocional" class="block text-xs font-bold text-gray-700 mb-1">Preço Promocional (R$)</label>
                    <input type="number" step="0.01" min="0" name="preco_promocional" id="preco_promocional" value="<?= htmlspecialchars($produto['preco_promocional'] ?? '') ?>" placeholder="Opcional" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="estoque" class="block text-xs font-bold text-gray-700 mb-1">Estoque Atual *</label>
                        <input type="number" min="0" name="estoque" id="estoque" required value="<?= htmlspecialchars($produto['estoque'] ?? '10') ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                    </div>
                    <div>
                        <label for="estoque_minimo" class="block text-xs font-bold text-gray-700 mb-1">Estoque Mínimo</label>
                        <input type="number" min="0" name="estoque_minimo" id="estoque_minimo" value="<?= htmlspecialchars($produto['estoque_minimo'] ?? '5') ?>" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                    </div>
                </div>
            </div>

            <div>
                <label for="descricao" class="block text-xs font-bold text-gray-700 mb-1">Descrição</label>
                <textarea name="descricao" id="descricao" rows="3" placeholder="Descreva os benefícios e ingredientes..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none"><?= htmlspecialchars($produto['descricao'] ?? '') ?></textarea>
            </div>

            <div>
                <label for="modo_uso" class="block text-xs font-bold text-gray-700 mb-1">Modo de Uso</label>
                <textarea name="modo_uso" id="modo_uso" rows="2" placeholder="Como aplicar ou utilizar o produto..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none"><?= htmlspecialchars($produto['modo_uso'] ?? '') ?></textarea>
            </div>

            <!-- Imagem -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-rose-50">
                <div>
                    <label for="imagem" class="block text-xs font-bold text-gray-700 mb-1">Enviar Nova Foto (JPG/PNG/WEBP)</label>
                    <input type="file" name="imagem" id="imagem" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-beauty-700 hover:file:bg-rose-100">
                </div>
                <div>
                    <label for="imagem_galeria" class="block text-xs font-bold text-gray-700 mb-1">Ou escolher da Galeria</label>
                    <select name="imagem_galeria" id="imagem_galeria" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-xs bg-white focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                        <option value="">Manter atual / padrão</option>
                        <?php foreach ($imagensDisponiveis as $arq):
                            $relPath = 'assets/images/produtos/' . basename($arq);
                        ?>
                            <option value="<?= htmlspecialchars($relPath) ?>" <?= (($produto['imagem'] ?? '') === $relPath) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(basename($arq)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex items-center space-x-6 pt-2">
                <label class="inline-flex items-center space-x-2 text-xs font-semibold text-gray-700 cursor-pointer">
                    <input type="checkbox" name="destaque" value="1" <?= !empty($produto['destaque']) ? 'checked' : '' ?> class="rounded text-beauty-600 focus:ring-beauty-400">
                    <span>Produto em Destaque</span>
                </label>
                <label class="inline-flex items-center space-x-2 text-xs font-semibold text-gray-700 cursor-pointer">
                    <input type="checkbox" name="ativo" value="1" <?= (!isset($produto) || !empty($produto['ativo'])) ? 'checked' : '' ?> class="rounded text-beauty-600 focus:ring-beauty-400">
                    <span>Visível na Loja</span>
                </label>
            </div>

            <div class="pt-4 border-t border-rose-50 flex justify-end space-x-3">
                <a href="<?= BASE_URL ?>/admin/produtos.php" class="px-5 py-2.5 rounded-2xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-200 hover:shadow-xl transition-all">
                    <?= $produto ? 'Salvar Alterações' : 'Cadastrar Produto' ?>
                </button>
            </div>
        </form>

    </div>
</main>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
