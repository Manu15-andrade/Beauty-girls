<?php
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/logger.php';

requer_admin();

$acao = trim($_GET['acao'] ?? '');
$busca = trim($_GET['busca'] ?? '');

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

$sql .= " ORDER BY `created_at` DESC, `id` DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

registrar_log($pdo, 'EXPORTAR_LOGS', 'logs_auditoria', null, 'Exportação de histórico de auditoria em CSV (' . count($logs) . ' registros)');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="beauty_girls_auditoria_' . date('Y-m-d_His') . '.csv"');

$output = fopen('php://output', 'w');
// BOM UTF-8 para abrir corretamente no Excel
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, ['ID', 'Data/Hora', 'Usuário ID', 'Usuário Nome', 'Ação', 'Entidade', 'Entidade ID', 'Detalhes', 'Dados Anteriores', 'Dados Novos', 'IP'], ';');

foreach ($logs as $log) {
    fputcsv($output, [
        $log['id'],
        $log['created_at'],
        $log['usuario_id'],
        $log['usuario_nome'],
        $log['acao'],
        $log['entidade'],
        $log['entidade_id'],
        $log['detalhes'],
        $log['dados_anteriores'],
        $log['dados_novos'],
        $log['ip_address'],
    ], ';');
}

fclose($output);
exit;
