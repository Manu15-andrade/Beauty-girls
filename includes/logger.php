<?php
/**
 * Beauty Girls - Sistema de Auditoria Contínua (CORE)
 * Registra qualquer alteração ou ação relevante na tabela logs_auditoria.
 */

function registrar_log(
    ?PDO $pdo,
    string $acao,
    ?string $entidade = null,
    ?int $entidade_id = null,
    string $detalhes = '',
    ?array $dados_anteriores = null,
    ?array $dados_novos = null,
    ?int $usuario_id = null,
    ?string $usuario_nome = null
): bool {
    if (!$pdo) {
        return false;
    }

    try {
        if ($usuario_id === null && isset($_SESSION['usuario']['id'])) {
            $usuario_id = (int) $_SESSION['usuario']['id'];
        }
        if ($usuario_nome === null && isset($_SESSION['usuario']['nome'])) {
            $usuario_nome = (string) $_SESSION['usuario']['nome'];
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : null;

        $stmt = $pdo->prepare("
            INSERT INTO `logs_auditoria`
            (`usuario_id`, `usuario_nome`, `acao`, `entidade`, `entidade_id`, `detalhes`, `dados_anteriores`, `dados_novos`, `ip_address`, `user_agent`)
            VALUES
            (:usuario_id, :usuario_nome, :acao, :entidade, :entidade_id, :detalhes, :dados_anteriores, :dados_novos, :ip_address, :user_agent)
        ");

        return $stmt->execute([
            ':usuario_id'       => $usuario_id,
            ':usuario_nome'     => $usuario_nome ?? 'Sistema / Visitante',
            ':acao'             => $acao,
            ':entidade'         => $entidade,
            ':entidade_id'      => $entidade_id,
            ':detalhes'         => $detalhes,
            ':dados_anteriores' => $dados_anteriores !== null ? json_encode($dados_anteriores, JSON_UNESCAPED_UNICODE) : null,
            ':dados_novos'      => $dados_novos !== null ? json_encode($dados_novos, JSON_UNESCAPED_UNICODE) : null,
            ':ip_address'       => $ip,
            ':user_agent'       => $userAgent,
        ]);
    } catch (PDOException $e) {
        error_log('Erro ao registrar log de auditoria: ' . $e->getMessage());
        return false;
    }
}
