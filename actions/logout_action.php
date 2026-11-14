<?php
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../includes/toast.php';

if (esta_logado()) {
    $u = usuario_atual();
    registrar_log(
        $pdo,
        'LOGOUT',
        'usuarios',
        (int) $u['id'],
        "Usuário {$u['email']} encerrou a sessão",
        null,
        null,
        (int) $u['id'],
        $u['nome']
    );
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
session_destroy();

session_start();
set_toast('info', 'Sessão Encerrada', 'Você saiu da sua conta com segurança.');
header('Location: ' . BASE_URL . '/index.php');
exit;
