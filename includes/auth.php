<?php
/**
 * Beauty Girls - Controle de Sessão e Autenticação
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/constantes.php';

function esta_logado(): bool
{
    return !empty($_SESSION['usuario']['id']);
}

function eh_admin(): bool
{
    return esta_logado() && (($_SESSION['usuario']['tipo'] ?? '') === 'admin');
}

function usuario_atual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function requer_login(): void
{
    if (!esta_logado()) {
        $_SESSION['toast'] = [
            'type'    => 'warning',
            'title'   => 'Acesso Restrito',
            'message' => 'Faça login para acessar esta área.',
        ];
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requer_admin(): void
{
    if (!esta_logado()) {
        $_SESSION['toast'] = [
            'type'    => 'warning',
            'title'   => 'Autenticação Necessária',
            'message' => 'Entre com uma conta administrativa para continuar.',
        ];
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    if (!eh_admin()) {
        $_SESSION['toast'] = [
            'type'    => 'error',
            'title'   => 'Permissão Negada',
            'message' => 'Você não possui permissão para acessar o painel administrativo.',
        ];
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
