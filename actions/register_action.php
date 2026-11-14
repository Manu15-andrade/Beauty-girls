<?php
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/../config/bd.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/logger.php';
require_once __DIR__ . '/../includes/toast.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . '/cadastro.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefone = trim($_POST['telefone'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($nome) || !$email || strlen($senha) < 6) {
    set_toast('error', 'Dados Inválidos', 'Preencha nome, e-mail válido e senha com no mínimo 6 caracteres.');
    header('Location: ' . BASE_URL . '/cadastro.php');
    exit;
}

if (!$pdo) {
    set_toast('error', 'Erro de Conexão', 'Banco de dados indisponível no momento.');
    header('Location: ' . BASE_URL . '/cadastro.php');
    exit;
}

try {
    $stmtCheck = $pdo->prepare("SELECT `id` FROM `usuarios` WHERE `email` = :email LIMIT 1");
    $stmtCheck->execute([':email' => $email]);
    if ($stmtCheck->fetch()) {
        set_toast('warning', 'E-mail Já Cadastrado', 'Já existe uma conta utilizando este endereço de e-mail.');
        header('Location: ' . BASE_URL . '/cadastro.php');
        exit;
    }

    // Requisito CORE: Hash BCRYPT
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("
        INSERT INTO `usuarios` (`nome`, `email`, `telefone`, `senha`, `tipo`)
        VALUES (:nome, :email, :telefone, :senha, 'cliente')
    ");
    $stmt->execute([
        ':nome'     => $nome,
        ':email'    => $email,
        ':telefone' => $telefone !== '' ? $telefone : null,
        ':senha'    => $senhaHash,
    ]);

    $novoId = (int) $pdo->lastInsertId();

    registrar_log(
        $pdo,
        'CADASTRO_USUARIO',
        'usuarios',
        $novoId,
        "Novo cliente cadastrado: {$nome} ({$email})",
        null,
        ['nome' => $nome, 'email' => $email, 'tipo' => 'cliente'],
        $novoId,
        $nome
    );

    session_regenerate_id(true);
    $_SESSION['usuario'] = [
        'id'    => $novoId,
        'nome'  => $nome,
        'email' => $email,
        'tipo'  => 'cliente',
    ];

    set_toast('success', 'Conta Criada!', 'Seu cadastro foi concluído com sucesso.');
    header('Location: ' . BASE_URL . '/index.php');
    exit;
} catch (PDOException $e) {
    error_log('Erro no cadastro: ' . $e->getMessage());
    set_toast('error', 'Erro ao Cadastrar', 'Não foi possível concluir seu cadastro.');
    header('Location: ' . BASE_URL . '/cadastro.php');
    exit;
}
