<?php
$admin_title = 'Dashboard | Painel Admin Beauty Girls';
require_once __DIR__ . '/includes/admin_header.php';

$totalProdutos = 0;
$estoqueBaixo = [];
$esgotados = 0;
$totalLogs = 0;
$logsRecentes = [];

if ($pdo) {
    $totalProdutos = (int) $pdo->query("SELECT COUNT(*) FROM `produtos`")->fetchColumn();
    $esgotados = (int) $pdo->query("SELECT COUNT(*) FROM `produtos` WHERE `estoque` <= 0")->fetchColumn();
    $totalLogs = (int) $pdo->query("SELECT COUNT(*) FROM `logs_auditoria`")->fetchColumn();

    $stmtBaixo = $pdo->query("
        SELECT p.*, c.nome AS categoria_nome
        FROM `produtos` p
        LEFT JOIN `categorias` c ON p.categoria_id = c.id
        WHERE p.estoque <= p.estoque_minimo
        ORDER BY p.estoque ASC
        LIMIT 8
    ");
    $estoqueBaixo = $stmtBaixo->fetchAll();

    $stmtLogs = $pdo->query("SELECT * FROM `logs_auditoria` ORDER BY `created_at` DESC, `id` DESC LIMIT 6");
    $logsRecentes = $stmtLogs->fetchAll();
}
?>

<main class="flex-1 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Cabeçalho -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Visão Geral da Loja</h1>
                <p class="text-xs text-gray-500 mt-1">Monitore estoque, cosméticos em alerta e auditoria em tempo real.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/produto_form.php" class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-2xl bg-gradient-to-r from-beauty-600 to-rose-600 text-white text-xs font-bold shadow-lg shadow-rose-200 hover:shadow-xl transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Novo Cosmético</span>
            </a>
        </div>

        <!-- Métricas -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-rose-100 shadow-sm">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total de Produtos</span>
                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2"><?= $totalProdutos ?></p>
                <span class="text-[11px] text-emerald-600 font-semibold">Cadastrados no catálogo</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-amber-200 shadow-sm">
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Estoque Baixo / Alerta</span>
                <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 mt-2"><?= count($estoqueBaixo) ?></p>
                <span class="text-[11px] text-gray-500">Abaixo ou igual ao mínimo</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-red-200 shadow-sm">
                <span class="text-[11px] font-bold text-red-500 uppercase tracking-wider">Esgotados</span>
                <p class="text-2xl sm:text-3xl font-extrabold text-red-600 mt-2"><?= $esgotados ?></p>
                <span class="text-[11px] text-gray-500">Bloqueados na sacola</span>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-rose-100 shadow-sm">
                <span class="text-[11px] font-bold text-beauty-600 uppercase tracking-wider">Registros de Auditoria</span>
                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2"><?= $totalLogs ?></p>
                <a href="<?= BASE_URL ?>/admin/historico.php" class="text-[11px] text-beauty-600 font-bold hover:underline">Ver histórico completo →</a>
            </div>
        </div>

        <!-- Duas Colunas: Alertas de Estoque + Logs Recentes -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Alertas de Estoque -->
            <div class="bg-white rounded-3xl border border-rose-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-gray-900">Alertas de Estoque</h2>
                    <a href="<?= BASE_URL ?>/admin/produtos.php" class="text-xs font-bold text-beauty-600 hover:underline">Gerenciar todos</a>
                </div>

                <?php if (empty($estoqueBaixo)): ?>
                    <p class="text-xs text-gray-500 py-6 text-center">Todos os produtos estão com estoque saudável.</p>
                <?php else: ?>
                    <div class="divide-y divide-rose-50">
                        <?php foreach ($estoqueBaixo as $item): ?>
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate"><?= htmlspecialchars($item['nome']) ?></p>
                                    <p class="text-[11px] text-gray-400">Mínimo recomendado: <?= (int) $item['estoque_minimo'] ?> un.</p>
                                </div>
                                <div class="flex items-center space-x-2 flex-shrink-0">
                                    <?php if ((int) $item['estoque'] <= 0): ?>
                                        <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-bold">Esgotado (0)</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Restam <?= (int) $item['estoque'] ?></span>
                                    <?php endif; ?>
                                    <a href="<?= BASE_URL ?>/admin/produto_form.php?id=<?= (int) $item['id'] ?>" class="text-xs font-bold text-beauty-600 hover:text-beauty-800">Repor</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Logs Recentes -->
            <div class="bg-white rounded-3xl border border-rose-100 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-serif text-lg font-bold text-gray-900">Auditoria Recente</h2>
                    <a href="<?= BASE_URL ?>/admin/historico.php" class="text-xs font-bold text-beauty-600 hover:underline">Abrir Auditoria</a>
                </div>

                <?php if (empty($logsRecentes)): ?>
                    <p class="text-xs text-gray-500 py-6 text-center">Nenhuma ação registrada ainda.</p>
                <?php else: ?>
                    <div class="divide-y divide-rose-50">
                        <?php foreach ($logsRecentes as $log): ?>
                            <div class="py-3 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="px-2 py-0.5 rounded-lg bg-rose-50 text-beauty-700 text-[10px] font-bold uppercase"><?= htmlspecialchars($log['acao']) ?></span>
                                    <span class="text-[10px] text-gray-400"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></span>
                                </div>
                                <p class="text-xs text-gray-700"><?= htmlspecialchars($log['detalhes'] ?? '') ?></p>
                                <p class="text-[10px] text-gray-400">Por: <?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</main>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
