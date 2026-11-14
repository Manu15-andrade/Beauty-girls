<?php
/**
 * Beauty Girls - Constantes Globais da Aplicação
 */

if (!defined('NOME_LOJA')) {
    define('NOME_LOJA', 'Beauty Girls');
}

if (!defined('SLOGAN_LOJA')) {
    define('SLOGAN_LOJA', 'Cosméticos & Autocuidado Feminino');
}

if (!defined('WHATSAPP_LOJA')) {
    define('WHATSAPP_LOJA', '5511999999999');
}

if (!defined('EMAIL_LOJA')) {
    define('EMAIL_LOJA', 'contato@beautygirls.com');
}

if (!defined('FRETE_GRATIS_VALOR')) {
    define('FRETE_GRATIS_VALOR', 150.00);
}

// Detecção dinâmica da BASE_URL (funciona em subpasta XAMPP ou raiz php -S)
if (!defined('BASE_URL')) {
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
    $appRoot = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: '');
    $baseUrl = '';
    if ($docRoot !== '' && $appRoot !== '' && stripos($appRoot, $docRoot) === 0) {
        $baseUrl = substr($appRoot, strlen($docRoot));
    }
    define('BASE_URL', rtrim($baseUrl, '/'));
}

// Configurações do Banco de Dados MySQL
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'beauty_girls');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}
