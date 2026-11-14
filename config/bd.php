<?php
/**
 * Beauty Girls - Conexão Segura com MySQL via PDO
 * Inclui auto-inicialização de schema e dados padrão caso o banco ainda não exista.
 */

require_once __DIR__ . '/constantes.php';

$pdo = null;

try {
    $dsnServer = sprintf('mysql:host=%s;charset=%s', DB_HOST, DB_CHARSET);
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdoInit = new PDO($dsnServer, DB_USER, DB_PASS, $options);
    $pdoInit->exec(sprintf(
        'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
        DB_NAME
    ));

    $dsnDb = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsnDb, DB_USER, DB_PASS, $options);

    // Criação automática das tabelas caso não existam
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `usuarios` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(120) NOT NULL,
            `email` VARCHAR(150) NOT NULL UNIQUE,
            `telefone` VARCHAR(30) NULL,
            `senha` VARCHAR(255) NOT NULL,
            `tipo` ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
            `ativo` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `categorias` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `nome` VARCHAR(80) NOT NULL,
            `slug` VARCHAR(100) NOT NULL UNIQUE,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `produtos` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `categoria_id` INT UNSIGNED NULL,
            `nome` VARCHAR(150) NOT NULL,
            `descricao` TEXT NULL,
            `modo_uso` TEXT NULL,
            `preco` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
            `preco_promocional` DECIMAL(10, 2) NULL,
            `estoque` INT NOT NULL DEFAULT 0,
            `estoque_minimo` INT NOT NULL DEFAULT 5,
            `imagem` VARCHAR(255) NULL,
            `destaque` TINYINT(1) NOT NULL DEFAULT 0,
            `ativo` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT `fk_produtos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `logs_auditoria` (
            `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `usuario_id` INT UNSIGNED NULL,
            `usuario_nome` VARCHAR(120) NULL,
            `acao` VARCHAR(60) NOT NULL,
            `entidade` VARCHAR(60) NULL,
            `entidade_id` INT UNSIGNED NULL,
            `detalhes` TEXT NULL,
            `dados_anteriores` LONGTEXT NULL,
            `dados_novos` LONGTEXT NULL,
            `ip_address` VARCHAR(45) NULL,
            `user_agent` VARCHAR(255) NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_logs_acao` (`acao`),
            INDEX `idx_logs_usuario` (`usuario_id`),
            INDEX `idx_logs_created` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Seed do administrador padrão se não houver usuários
    $totalUsuarios = (int) $pdo->query("SELECT COUNT(*) FROM `usuarios`")->fetchColumn();
    if ($totalUsuarios === 0) {
        $adminHash = password_hash('admin123', PASSWORD_BCRYPT);
        $stmtAdmin = $pdo->prepare("
            INSERT INTO `usuarios` (`nome`, `email`, `telefone`, `senha`, `tipo`)
            VALUES (:nome, :email, :telefone, :senha, 'admin')
        ");
        $stmtAdmin->execute([
            ':nome'     => 'Administradora Beauty Girls',
            ':email'    => 'admin@beautygirls.com',
            ':telefone' => '(11) 99999-9999',
            ':senha'    => $adminHash,
        ]);
    }

    // Seed de categorias padrão
    $totalCategorias = (int) $pdo->query("SELECT COUNT(*) FROM `categorias`")->fetchColumn();
    if ($totalCategorias === 0) {
        $categoriasIniciais = [
            ['Maquiagem', 'maquiagem'],
            ['Skincare', 'skincare'],
            ['Cabelos', 'cabelos'],
            ['Perfumaria', 'perfumaria'],
            ['Corpo & Banho', 'corpo-banho'],
            ['Acessórios', 'acessorios'],
        ];
        $stmtCat = $pdo->prepare("INSERT INTO `categorias` (`nome`, `slug`) VALUES (:nome, :slug)");
        foreach ($categoriasIniciais as [$nome, $slug]) {
            $stmtCat->execute([':nome' => $nome, ':slug' => $slug]);
        }
    }

    // Seed de produtos iniciais usando as imagens do repositório
    $totalProdutos = (int) $pdo->query("SELECT COUNT(*) FROM `produtos`")->fetchColumn();
    if ($totalProdutos === 0) {
        $catsMap = [];
        foreach ($pdo->query("SELECT id, slug FROM `categorias`") as $row) {
            $catsMap[$row['slug']] = (int) $row['id'];
        }

        $produtosIniciais = [
            [
                'categoria_id'      => $catsMap['skincare'] ?? null,
                'nome'              => 'Sérum Facial Vitamina C Glow',
                'descricao'         => 'Sérum antioxidante concentrado com Vitamina C pura e Ácido Hialurônico para uniformizar o tom da pele e proporcionar luminosidade imediata.',
                'modo_uso'          => 'Aplique 3 a 4 gotas sobre o rosto limpo e seco pela manhã e à noite, massageando suavemente até completa absorção.',
                'preco'             => 89.90,
                'preco_promocional' => 69.90,
                'estoque'           => 25,
                'estoque_minimo'    => 5,
                'imagem'            => 'assets/images/produtos/produto-1.jpg',
                'destaque'          => 1,
            ],
            [
                'categoria_id'      => $catsMap['maquiagem'] ?? null,
                'nome'              => 'Gloss Labial Hidratante Rose Quartz',
                'descricao'         => 'Gloss labial com efeito espelhado, enriquecido com óleo de jojoba e vitamina E. Não deixa sensação pegajosa e garante lábios volumosos.',
                'modo_uso'          => 'Deslize o aplicador diretamente sobre os lábios limpos ou por cima do seu batom favorito.',
                'preco'             => 49.90,
                'preco_promocional' => null,
                'estoque'           => 18,
                'estoque_minimo'    => 5,
                'imagem'            => 'assets/images/produtos/produto-2.jpg',
                'destaque'          => 1,
            ],
            [
                'categoria_id'      => $catsMap['maquiagem'] ?? null,
                'nome'              => 'Paleta de Sombras Velvet Rose',
                'descricao'         => 'Paleta com 9 tons rosados, neutros e cintilantes de altíssima pigmentação e textura aveludada de longa duração.',
                'modo_uso'          => 'Aplique com pincel macio nas pálpebras, esfumando as transições para um acabamento profissional.',
                'preco'             => 119.90,
                'preco_promocional' => 94.90,
                'estoque'           => 4,
                'estoque_minimo'    => 5,
                'imagem'            => 'assets/images/produtos/produto-3.jpg',
                'destaque'          => 1,
            ],
            [
                'categoria_id'      => $catsMap['cabelos'] ?? null,
                'nome'              => 'Óleo Capilar Reparador Elixir Dourado',
                'descricao'         => 'Blend de óleos nobres de argan e camélia que nutre profundamente, controla o frizz e protege os fios contra danos térmicos.',
                'modo_uso'          => 'Espalhe 2 gotas nas palmas das mãos e aplique no comprimento e pontas dos cabelos úmidos ou secos.',
                'preco'             => 79.90,
                'preco_promocional' => null,
                'estoque'           => 14,
                'estoque_minimo'    => 5,
                'imagem'            => 'assets/images/produtos/produto-4.jpg',
                'destaque'          => 0,
            ],
            [
                'categoria_id'      => $catsMap['corpo-banho'] ?? null,
                'nome'              => 'Body Splash Floral Peônia & Baunilha',
                'descricao'         => 'Fragrância envolvente e delicada com notas de peônia rosa, frutas vermelhas e fundo cremoso de baunilha.',
                'modo_uso'          => 'Borrife sobre a pele após o banho ou sempre que desejar renovar a sensação de frescor.',
                'preco'             => 64.90,
                'preco_promocional' => 54.90,
                'estoque'           => 30,
                'estoque_minimo'    => 6,
                'imagem'            => 'assets/images/produtos/produto-5.jpg',
                'destaque'          => 0,
            ],
            [
                'categoria_id'      => $catsMap['skincare'] ?? null,
                'nome'              => 'Creme Hidratante Facial Niacinamida',
                'descricao'         => 'Hidratante oil-free com Niacinamida 5% e Pantenol, ideal para controlar a oleosidade e fortalecer a barreira cutânea.',
                'modo_uso'          => 'Aplique uma camada uniforme no rosto e pescoço duas vezes ao dia.',
                'preco'             => 74.90,
                'preco_promocional' => null,
                'estoque'           => 3,
                'estoque_minimo'    => 5,
                'imagem'            => 'assets/images/produtos/produto-6.jpg',
                'destaque'          => 0,
            ],
            [
                'categoria_id'      => $catsMap['acessorios'] ?? null,
                'nome'              => 'Kit Pincéis Profissionais Rose Gold',
                'descricao'         => 'Conjunto com 6 pincéis de cerdas sintéticas ultra macias para pele e olhos, acompanhado de nécessaire exclusiva.',
                'modo_uso'          => 'Utilize cada pincel conforme sua função e higienize semanalmente com sabonete neutro.',
                'preco'             => 129.90,
                'preco_promocional' => 99.90,
                'estoque'           => 12,
                'estoque_minimo'    => 4,
                'imagem'            => 'assets/images/produtos/produto-7.jpg',
                'destaque'          => 1,
            ],
        ];

        $stmtProd = $pdo->prepare("
            INSERT INTO `produtos`
            (`categoria_id`, `nome`, `descricao`, `modo_uso`, `preco`, `preco_promocional`, `estoque`, `estoque_minimo`, `imagem`, `destaque`, `ativo`)
            VALUES
            (:categoria_id, :nome, :descricao, :modo_uso, :preco, :preco_promocional, :estoque, :estoque_minimo, :imagem, :destaque, 1)
        ");

        foreach ($produtosIniciais as $prod) {
            $stmtProd->execute($prod);
        }
    }
} catch (PDOException $e) {
    error_log('Erro de conexão PDO: ' . $e->getMessage());
    $pdo = null;
}
