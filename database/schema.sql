-- Beauty Girls - Schema MySQL
CREATE DATABASE IF NOT EXISTS `beauty_girls`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `beauty_girls`;

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

CREATE TABLE IF NOT EXISTS `categorias` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(80) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- Administrador padrão: admin@beautygirls.com / admin123 (BCRYPT)
INSERT IGNORE INTO `usuarios` (`nome`, `email`, `telefone`, `senha`, `tipo`) VALUES
('Administradora Beauty Girls', 'admin@beautygirls.com', '(11) 99999-9999', '$2y$10$g8odiz029DwG2MRQS30R.O1Lz1.eQm8pLwqMmiYzVClahCZLm7qYC', 'admin');

-- Categorias iniciais
INSERT IGNORE INTO `categorias` (`nome`, `slug`) VALUES
('Maquiagem', 'maquiagem'),
('Skincare', 'skincare'),
('Cabelos', 'cabelos'),
('Perfumaria', 'perfumaria'),
('Corpo & Banho', 'corpo-banho'),
('Acessórios', 'acessorios');
