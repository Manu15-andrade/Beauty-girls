<?php
$admin_title = 'Histórico & Auditoria | Painel Admin Beauty Girls';
require_once __DIR__ . '/includes/admin_header.php';

$acao = trim($_GET['acao'] ?? '');
$busca = trim($_GET['busca'] ?? '');

$acoesDisponiveis = $pdo->query("SELECT DISTINCT `acao` FROM `logs_auditoria` ORDER BY `acao` ASC")->fetchAll(PDO::FETCH_COLUMN);

$sql = "SELECT * FROM `logs_auditoria` WHERE 1=1";
$params = [];

if ($acao !== '') {
    $sql .= " AND `acao` = :acao";
    $params[':acao'] = $acao;
}

if ($busca !== '') {
    $sql .= " AND (`detalhes` LIKE :busca OR `usuario_nome` LIKE :buscaUser OR `entidade` LIKE :buscaEnt)";
    $params[':busca'] = "%{$busca}%";
    $params[':buscaUser'] = "%{$busca}%";
    $params[':buscaEnt'] = "%{$busca}%";
}

$sql .= " ORDER BY `created_at` DESC, `id` DESC LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();
?>

<main class="flex-1 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="font-serif text-2xl sm:text-3xl font-bold text-gray-900">Histórico & Auditoria do Sistema</h1>
                <p class="text-xs text-gray-500 mt-1">Registro completo de logins, cadastros e alterações realizadas por usuários.</p>
            </div>
            <a href="<?= BASE_URL ?>/actions/exportar_logs.php?acao=<?= urlencode($acao) ?>&busca=<?= urlencode($busca) ?>" class="inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Exportar CSV</span>
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" class="bg-white p-4 rounded-3xl border border-rose-100 shadow-sm flex flex-col sm:flex-row gap-3">
            <input
                type="text"
                name="busca"
                value="<?= htmlspecialchars($busca) ?>"
                placeholder="Buscar por usuário, entidade ou detalhes..."
                class="flex-1 px-4 py-2 rounded-xl border border-gray-200 text-xs focus:ring-2 focus:ring-beauty-400 focus:outline-none"
            >
            <select name="acao" class="px-4 py-2 rounded-xl border border-gray-200 text-xs bg-white focus:ring-2 focus:ring-beauty-400 focus:outline-none">
                <option value="">Todas as ações</option>
                <?php foreach ($acoesDisponiveis as $ac): ?>
                    <option value="<?= htmlspecialchars($ac) ?>" <?= $acao === $ac ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ac) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="px-5 py-2 rounded-xl bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-colors">
                Filtrar
            </button>
            <?php if ($busca !== '' || $acao !== ''): ?>
                <a href="<?= BASE_URL ?>/admin/historico.php" class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 text-center">
                    Limpar
                </a>
            <?php endif; ?>
        </form>

        <!-- Tabela de Logs -->
        <?php if (empty($logs)): ?>
            <div class="bg-white rounded-3xl border border-rose-100 p-12 text-center">
                <p class="text-sm font-semibold text-gray-700">Nenhum registro de auditoria encontrado.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-3xl border border-rose-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-rose-100 text-left">
                        <thead class="bg-rose-50/60">
                            <tr>
                                <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Data/Hora</th>
                                <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Usuário</th>
                                <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Ação</th>
                                <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Entidade</th>
                                <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase">Detalhes</th>
                                <th class="px-4 py-3 text-[11px] font-bold text-gray-600 uppercase text-right">Dados</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50 text-xs">
                            <?php foreach ($logs as $log):
                                $temDiff = !empty($log['dados_anteriores']) || !empty($log['dados_novos']);
                            ?>
                                <tr class="hover:bg-rose-50/30 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                                        <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-800">
                                        <?= htmlspecialchars($log['usuario_nome'] ?? 'Sistema') ?>
                                        <span class="block text-[10px] text-gray-400 font-normal"><?= htmlspecialchars($log['ip_address'] ?? '') ?></span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-full bg-rose-100 text-beauty-800 text-[10px] font-bold">
                                            <?= htmlspecialchars($log['acao']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        <?= htmlspecialchars($log['entidade'] ?? '-') ?>
                                        <?= $log['entidade_id'] ? ' #' . (int) $log['entidade_id'] : '' ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700 max-w-md">
                                        <?= htmlspecialchars($log['detalhes'] ?? '') ?>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <?php if ($temDiff): ?>
                                            <button
                                                type="button"
                                                onclick="verDetalhesLog(<?= htmlspecialchars(json_encode([
                                                    'acao' => $log['acao'],
                                                    'antes' => $log['dados_anteriores'] ? json_decode($log['dados_anteriores'], true) : null,
                                                    'depois' => $log['dados_novos'] ? json_decode($log['dados_novos'], true) : null,
                                                ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                class="px-3 py-1 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-semibold transition-colors"
                                            >
                                                Ver Antes/Depois
                                            </button>
                                        <?php else: ?>
                                            <span class="text-gray-300 text-[11px]">—</span>
                                        <?php endif; ?>
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

<script>
    function verDetalhesLog(payload) {
        const formatar = (obj) => obj ? `<pre class="bg-gray-50 p-2.5 rounded-xl text-[10px] overflow-x-auto max-h-36 border border-gray-200 mt-1">${JSON.stringify(obj, null, 2)}</pre>` : '<p class="text-[11px] text-gray-400 italic">Nenhum</p>';
        const html = `
            <div class="space-y-3 text-left mt-2">
                <div>
                    <strong class="text-[11px] text-gray-700 uppercase">Antes:</strong>
                    ${formatar(payload.antes)}
                </div>
                <div>
                    <strong class="text-[11px] text-gray-700 uppercase">Depois:</strong>
                    ${formatar(payload.depois)}
                </div>
            </div>
        `;
        window.customAlert(`Auditoria: ${payload.acao}`, html);
    }
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
