<?php
require_once __DIR__ . '/../../config/constantes.php';
require_once __DIR__ . '/../../config/bd.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/toast.php';

requer_admin();

$usuarioAdmin = usuario_atual();
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $admin_title ?? ('Painel Admin | ' . NOME_LOJA) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        beauty: {
                            50:  '#fdf2f6',
                            100: '#fce7ef',
                            200: '#fbcfe0',
                            300: '#f9a8c9',
                            400: '#f472a7',
                            500: '#ec4889',
                            600: '#db2767',
                            700: '#be1852',
                            800: '#9d1745',
                            900: '#83183d',
                        }
                    }
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css">
</head>
<body class="min-h-screen flex flex-col bg-[#fcfaf8] text-gray-800 font-sans antialiased">

<?php require_once __DIR__ . '/../../includes/toast.php'; ?>
<?php require_once __DIR__ . '/../../includes/modal.php'; ?>

<!-- Topbar Admin -->
<header class="sticky top-0 z-40 bg-white border-b border-rose-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            <div class="flex items-center space-x-3">
                <a href="<?= BASE_URL ?>/admin/index.php" class="flex items-center space-x-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-beauty-600 to-rose-500 flex items-center justify-center text-white font-serif font-bold shadow">
                        B
                    </div>
                    <div>
                        <span class="font-serif text-base sm:text-lg font-bold text-gray-900 block leading-none">Beauty Girls</span>
                        <span class="text-[10px] font-bold text-beauty-600 uppercase tracking-wider">Painel Administrativo</span>
                    </div>
                </a>
            </div>

            <!-- Links Desktop -->
            <nav class="hidden md:flex items-center space-x-1">
                <a href="<?= BASE_URL ?>/admin/index.php" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors <?= $paginaAtual === 'index.php' ? 'bg-rose-50 text-beauty-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                    Dashboard
                </a>
                <a href="<?= BASE_URL ?>/admin/produtos.php" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors <?= in_array($paginaAtual, ['produtos.php', 'produto_form.php'], true) ? 'bg-rose-50 text-beauty-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                    Produtos & Estoque
                </a>
                <a href="<?= BASE_URL ?>/admin/historico.php" class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors <?= $paginaAtual === 'historico.php' ? 'bg-rose-50 text-beauty-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                    Histórico & Auditoria
                </a>
            </nav>

            <div class="flex items-center space-x-2">
                <a href="<?= BASE_URL ?>/index.php" class="px-3 py-2 rounded-xl border border-rose-100 text-xs font-semibold text-gray-600 hover:text-beauty-600 hover:bg-rose-50 transition-colors">
                    Ver Loja
                </a>
                <a href="<?= BASE_URL ?>/logout.php" class="px-3 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-xs font-semibold transition-colors">
                    Sair
                </a>
            </div>
        </div>

        <!-- Navegação Mobile Admin -->
        <nav class="md:hidden flex items-center space-x-2 overflow-x-auto pb-2.5 no-scrollbar">
            <a href="<?= BASE_URL ?>/admin/index.php" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap <?= $paginaAtual === 'index.php' ? 'bg-beauty-600 text-white' : 'bg-gray-100 text-gray-600' ?>">
                Dashboard
            </a>
            <a href="<?= BASE_URL ?>/admin/produtos.php" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap <?= in_array($paginaAtual, ['produtos.php', 'produto_form.php'], true) ? 'bg-beauty-600 text-white' : 'bg-gray-100 text-gray-600' ?>">
                Produtos & Estoque
            </a>
            <a href="<?= BASE_URL ?>/admin/historico.php" class="px-3 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap <?= $paginaAtual === 'historico.php' ? 'bg-beauty-600 text-white' : 'bg-gray-100 text-gray-600' ?>">
                Auditoria
            </a>
        </nav>
    </div>
</header>
