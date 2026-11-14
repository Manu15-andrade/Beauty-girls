<?php
require_once __DIR__ . '/../config/constantes.php';
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? (NOME_LOJA . ' | ' . SLOGAN_LOJA) ?></title>

    <!-- Google Fonts: Playfair Display & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/custom.css">

    <script>
        window.APP_CONFIG = {
            baseUrl: <?= json_encode(BASE_URL) ?>,
            whatsapp: <?= json_encode(WHATSAPP_LOJA) ?>,
            freteGratis: <?= json_encode((float) FRETE_GRATIS_VALOR) ?>,
            nomeLoja: <?= json_encode(NOME_LOJA) ?>
        };
    </script>
</head>
<body class="min-h-screen flex flex-col bg-[#fcfaf8] text-gray-800 font-sans antialiased pb-16 md:pb-0">

<?php require_once __DIR__ . '/toast.php'; ?>
<?php require_once __DIR__ . '/modal.php'; ?>
