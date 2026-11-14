<?php
$admin_title = 'Produtos & Estoque | Painel Admin Beauty Girls';
require_once __DIR__ . '/includes/admin_header.php';

$busca = trim($_GET['busca'] ?? '');
$filtroEstoque = $_GET['estoque'] ?? '';

$sql = "
    SELECT p.*, c.nome AS categoria_nome
    FROM `produtos` p
    LEFT JOIN `categorias` c ON p.categoria_id = c.id
    WHERE 1=1
";
$params = [];

if ($busca !== '') {
    $sql .= " AND (p.nome LIKE :busca OR c.nome LIKE :buscaCat)";
    $params[':busca'] = "%{$busca}%";
    $params[':buscaCat'] = "%{$busca}%";
}

if ($filtroEstoque === 'baixo') {
    $sql .= " AND p.estoque > 0 AND p.estoque <= p.estoque_minimo";
} elseif ($filtroEstoque === 'esgotado') {
    $sql .= " AND p.estoque <= 0";
}

$sql .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();
?>

<main class="flex-1 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Gestão de Produtos & Estoque</h1>
                <p class="text-xs text-gray-500 mt-1">Cadastre novos cosméticos, atualize preços e controle quantidades.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/produto_form.php" class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-200 hover:shadow-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Cadastrar Cosmético</span>
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" class="bg-white p-4 rounded-3xl border border-rose-100 shadow-sm flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="busca"
                value="<?= htmlspecialchars($busca) ?>"
                placeholder="Buscar por nome ou categoria..."
                class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none"
            >
            <select name="estoque" class="px-4 py-2 rounded-xl border border-gray-200 text-xs bg-white focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                <option value="">Todos os estoques</option>
                <option value="baixo" <?= $filtroEstoque === 'baixo' ? 'selected' : '' ?>>Estoque Baixo</option>
                <option value="esgotado" <?= $filtroEstoque === 'esgotado' ? 'selected' : '' ?>>Esgotados</option>
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-colors">
                Filtrar
            </button>
            <?php if ($busca !== '' || $filtroEstoque !== ''): ?>
                <a href="<?= BASE_URL ?>/admin/produtos.php" class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 text-center">
                    Limpar
                </a>
            <?php endif; ?>
        </form>

        <!-- Lista de Produtos -->
        <?php if (empty($produtos)): ?>
            <div class="bg-white rounded-3xl border border-rose-100 p-12 text-center">
                <p class="text-sm font-semibold text-gray-700">Nenhum produto encontrado.</p>
                <p class="text-xs text-gray-400 mt-1">Tente ajustar os filtros ou cadastre um novo cosmético.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-rose-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-rose-100 text-left">
                        <thead class="bg-rose-50/60">
                            <tr>
                                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-600 uppercase tracking-wider">Produto</th>
                                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-600 uppercase tracking-wider">Categoria</th>
                                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-600 uppercase tracking-wider">Preço</th>
                                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-600 uppercase tracking-wider">Estoque</th>
                                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-[11px] font-bold text-gray-600 uppercase tracking-wider text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50 text-xs">
                            <?php foreach ($produtos as $p):
                                $esgotado = ((int) $p['estoque'] <= 0);
                                $baixo = (!$esgotado && (int) $p['estoque'] <= (int) $p['estoque_minimo']);
                                $imgUrl = !empty($p['imagem']) ? BASE_URL . '/' . $p['imagem'] : 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=200&q=80';
                            ?>
                                <tr class="hover:bg-rose-50/30 transition-colors">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center space-x-3">
                                            <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($p['nome']) ?>" class="w-11 h-11 rounded-xl object-cover bg-rose-50 flex-shrink-0">
                                            <div>
                                                <p class="font-bold text-gray-900"><?= htmlspecialchars($p['nome']) ?></p>
                                                <?php if ($p['destaque']): ?>
                                                    <span class="text-[10px] font-bold text-amber-600">★ Destaque</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600"><?= htmlspecialchars($p['categoria_nome'] ?? 'Sem categoria') ?></td>
                                    <td class="px-5 py-3.5">
                                        <?php if (!empty($p['preco_promocional'])): ?>
                                            <span class="block text-[10px] text-gray-400 line-through">R$ <?= number_format((float) $p['preco'], 2, ',', '.') ?></span>
                                            <span class="font-bold text-beauty-700">R$ <?= number_format((float) $p['preco_promocional'], 2, ',', '.') ?></span>
                                        <?php else: ?>
                                            <span class="font-bold text-gray-900">R$ <?= number_format((float) $p['preco'], 2, ',', '.') ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <?php if ($esgotado): ?>
                                            <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold">Esgotado (0)</span>
                                        <?php elseif ($baixo): ?>
                                            <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Alerta: <?= (int) $p['estoque'] ?> un.</span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold"><?= (int) $p['estoque'] ?> un.</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <?= $p['ativo'] ? '<span class="text-emerald-600 font-semibold">Ativo</span>' : '<span class="text-gray-400 font-semibold">Oculto</span>' ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-2">
                                        <a href="<?= BASE_URL ?>/admin/produto_form.php?id=<?= (int) $p['id'] ?>" class="inline-flex px-3 py-1.5 rounded-xl bg-rose-50 text-beauty-700 hover:bg-rose-100 font-semibold transition-colors">
                                            Editar
                                        </a>
                                        <button
                                            type="button"
                                            onclick="confirmarExclusaoProduto(<?= (int) $p['id'] ?>, <?= htmlspecialchars(json_encode($p['nome']), ENT_QUOTES, 'UTF-8') ?>)"
                                            class="inline-flex px-3 py-1.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-semibold transition-colors"
                                        >
                                            Excluir
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</main>

<form id="formDeleteProduto" action="<?= BASE_URL ?>/actions/produto_delete.php" method="POST" class="hidden">
    <input type="hidden" name="id" id="deleteProdutoId" value="">
</form>

<script>
    function confirmarExclusaoProduto(id, nome) {
        window.customConfirm(
            'Excluir Cosmético',
            `Tem certeza que deseja excluir permanentemente o produto <strong>"${nome}"</strong>? Esta ação será registrada no histórico de auditoria.`,
            () => {
                document.getElementById('deleteProdutoId').value = id;
                document.getElementById('formDeleteProduto').submit();
            },
            true
        );
    }
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
