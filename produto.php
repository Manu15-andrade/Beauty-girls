<?php
/**
 * Beauty Girls - Página de Detalhes do Cosmético
 */

require_once __DIR__ . '/config/constantes.php';
require_once __DIR__ . '/config/bd.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = null;
$relacionados = [];

if ($id && $pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, c.nome AS categoria_nome, c.slug AS categoria_slug 
            FROM `produtos` p 
            LEFT JOIN `categorias` c ON p.categoria_id = c.id 
            WHERE p.id = :id AND p.ativo = 1 
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $produto = $stmt->fetch();

        if ($produto && !empty($produto['categoria_id'])) {
            $stmtRel = $pdo->prepare("
                SELECT * FROM `produtos` 
                WHERE `categoria_id` = :cat_id AND `id` != :id AND `ativo` = 1 
                LIMIT 4
            ");
            $stmtRel->execute([':cat_id' => $produto['categoria_id'], ':id' => $id]);
            $relacionados = $stmtRel->fetchAll();
        }
    } catch (PDOException $e) {
        error_log("Erro ao carregar produto: " . $e->getMessage());
    }
}

if (!$produto) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$page_title = htmlspecialchars($produto['nome']) . ' | ' . NOME_LOJA;
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$preco = (float)$produto['preco'];
$precoPromo = $produto['preco_promocional'] ? (float)$produto['preco_promocional'] : null;
$temPromo = ($precoPromo !== null && $precoPromo < $preco);
$precoFinal = $temPromo ? $precoPromo : $preco;
$esgotado = ($produto['estoque'] <= 0);
$estoqueBaixo = (!$esgotado && $produto['estoque'] <= $produto['estoque_minimo']);
$imgUrl = !empty($produto['imagem']) ? BASE_URL . '/' . $produto['imagem'] : 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=800&q=80';
?>

<main class="flex-1 py-8 sm:py-12 bg-[#fcfaf8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-xs text-gray-400">
            <a href="<?= BASE_URL ?>/index.php" class="hover:text-beauty-600 transition-colors">Início</a>
            <span>/</span>
            <a href="<?= BASE_URL ?>/index.php?categoria=<?= urlencode($produto['categoria_slug'] ?? '') ?>" class="hover:text-beauty-600 transition-colors">
                <?= htmlspecialchars($produto['categoria_nome'] ?? 'Cosméticos') ?>
            </a>
            <span>/</span>
            <span class="text-gray-700 font-semibold truncate max-w-xs"><?= htmlspecialchars($produto['nome']) ?></span>
        </nav>

        <!-- Detalhes do Produto -->
        <div class="bg-white rounded-3xl border border-rose-100 shadow-sm p-6 sm:p-10 grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 items-start">
            
            <!-- Imagem Principal -->
            <div class="aspect-square rounded-3xl overflow-hidden bg-rose-50 border border-rose-100 shadow-inner relative">
                <img 
                    src="<?= $imgUrl ?>" 
                    alt="<?= htmlspecialchars($produto['nome']) ?>" 
                    class="w-full h-full object-cover object-center"
                >
                <?php if ($esgotado): ?>
                    <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px] flex items-center justify-center">
                        <span class="px-4 py-2 rounded-2xl bg-black/80 text-white font-bold text-xs uppercase tracking-wider">
                            Produto Esgotado
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dados do Cosmético -->
            <div class="space-y-6">
                <div>
                    <span class="text-xs font-bold text-beauty-600 tracking-wider uppercase">
                        <?= htmlspecialchars($produto['categoria_nome'] ?? 'Cosmético') ?>
                    </span>
                    <h1 class="font-serif text-2xl sm:text-4xl font-bold text-gray-900 mt-1 leading-snug">
                        <?= htmlspecialchars($produto['nome']) ?>
                    </h1>
                </div>

                <!-- Preço e Badges -->
                <div class="border-y border-rose-50 py-4 flex items-baseline space-x-3">
                    <?php if ($temPromo): ?>
                        <span class="text-sm text-gray-400 line-through">
                            R$ <?= number_format($preco, 2, ',', '.') ?>
                        </span>
                    <?php endif; ?>
                    <span class="text-3xl font-extrabold text-gray-900">
                        R$ <?= number_format($precoFinal, 2, ',', '.') ?>
                    </span>
                    <?php if ($temPromo): ?>
                        <span class="px-2.5 py-1 rounded-full bg-beauty-100 text-beauty-700 font-bold text-xs">
                            Oferta Especial
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Status de Estoque -->
                <div>
                    <?php if ($esgotado): ?>
                        <p class="text-xs font-bold text-red-500">Temporariamente sem estoque para entrega imediata.</p>
                    <?php elseif ($estoqueBaixo): ?>
                        <p class="text-xs font-bold text-amber-600 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-amber-500 mr-2 animate-ping"></span>
                            Atenção: Apenas <?= $produto['estoque'] ?> unidades restantes no estoque!
                        </p>
                    <?php else: ?>
                        <p class="text-xs font-semibold text-emerald-600 flex items-center">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                            Em estoque (<?= $produto['estoque'] ?> unidades disponíveis)
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Descrição -->
                <div class="text-xs sm:text-sm text-gray-600 leading-relaxed space-y-2">
                    <p><?= nl2br(htmlspecialchars($produto['descricao'] ?? '')) ?></p>
                </div>

                <?php if (!empty($produto['modo_uso'])): ?>
                    <div class="p-4 bg-rose-50/50 rounded-2xl border border-rose-100 space-y-1">
                        <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wide">Modo de Uso:</h4>
                        <p class="text-xs text-gray-600 leading-relaxed"><?= nl2br(htmlspecialchars($produto['modo_uso'])) ?></p>
                    </div>
                <?php endif; ?>

                <!-- Seletor de Quantidade e Ação -->
                <?php if (!$esgotado): ?>
                    <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                        <div class="flex items-center border border-gray-200 rounded-2xl overflow-hidden bg-gray-50 h-12">
                            <button type="button" onclick="alterarQtdDetalhe(-1)" class="px-4 text-gray-600 hover:bg-gray-200 font-bold text-sm h-full">-</button>
                            <input type="number" id="qtdDetalheInput" value="1" min="1" max="<?= $produto['estoque'] ?>" class="w-14 text-center text-xs font-bold bg-transparent focus:outline-none" readonly>
                            <button type="button" onclick="alterarQtdDetalhe(1)" class="px-4 text-gray-600 hover:bg-gray-200 font-bold text-sm h-full">+</button>
                        </div>

                        <button 
                            type="button" 
                            onclick="adicionarCarrinhoDetalhe()"
                            class="flex-1 w-full sm:w-auto h-12 px-6 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 hover:from-beauty-700 hover:to-rose-700 text-white font-bold text-xs shadow-lg shadow-rose-200 transition-all flex items-center justify-center space-x-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            <span>Adicionar à Sacola</span>
                        </button>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>
</main>

<script>
    const produtoAtual = <?= json_encode($produto) ?>;

    function alterarQtdDetalhe(delta) {
        const input = document.getElementById('qtdDetalheInput');
        if (!input) return;
        let v = parseInt(input.value) || 1;
        v += delta;
        if (v < 1) v = 1;
        if (v > produtoAtual.estoque) {
            v = produtoAtual.estoque;
            if (window.showToast) window.showToast('warning', 'Estoque Limite', `Estoque máximo: ${produtoAtual.estoque}`);
        }
        input.value = v;
    }

    function adicionarCarrinhoDetalhe() {
        const input = document.getElementById('qtdDetalheInput');
        const qtd = parseInt(input.value) || 1;
        Cart.addItem(produtoAtual, qtd);
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
