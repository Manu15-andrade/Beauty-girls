<?php
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../includes/toast.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || empty($senha)) {
    set_toast('error', 'Dados Inválidos', 'Informe um e-mail válido e sua senha.');
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

if (!$pdo) {
    set_toast('error', 'Erro de Conexão', 'Não foi possível conectar ao banco de dados.');
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM `usuarios` WHERE `email` = :email AND `ativo` = 1 LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    if (!$usuario || !password_verify($senha, $usuario['senha'])) {
        registrar_log($pdo, 'LOGIN_FALHA', 'usuarios', $usuario['id'] ?? null, "Tentativa de login falhou para {$email}", null, null, $usuario['id'] ?? null, $email);
        set_toast('error', 'Credenciais Incorretas', 'E-mail ou senha inválidos. Tente novamente.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'    => (int) $usuario['id'],
        'nome'  => $usuario['nome'],
        'email' => $usuario['email'],
        'tipo'  => $usuario['tipo'],
    ];

    registrar_log(
        $pdo,
        'LOGIN',
        'usuarios',
        (int) $usuario['id'],
        "Login realizado com sucesso ({$usuario['tipo']})",
        null,
        ['email' => $usuario['email'], 'tipo' => $usuario['tipo']],
        (int) $usuario['id'],
        $usuario['nome']
    );

    set_toast('success', 'Bem-vinda!', 'Olá, ' . $usuario['nome'] . '! Acesso liberado.');

    if ($usuario['tipo'] === 'admin') {
        header('Location: ' . BASE_URL . '/admin/index.php');
    } else {
        header('Location: ' . BASE_URL . '/index.php');
    }
    exit;
} catch (PDOException $e) {
    error_log('Erro no login: ' . $e->getMessage());
    set_toast('error', 'Erro Interno', 'Ocorreu um erro ao processar seu login.');
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
