-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/11/2025 às 21:54
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `retrogames_store`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `descricao` text DEFAULT NULL,
  `icone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categories`
--

INSERT INTO `categories` (`id`, `nome`, `descricao`, `icone`) VALUES
(1, 'Consoles Retro', 'PS2, Xbox, GameCube, Dreamcast e clássicos', '🎮'),
(2, 'Consoles Atuais', 'PlayStation 5, Xbox Series, Nintendo Switch', '🎯'),
(3, 'Computadores', 'PCs Gamers completos e notebooks', '💻'),
(4, 'Peças', 'Placas de vídeo, processadores, memórias', '🔧'),
(5, 'Jogos', 'Jogos físicos e digitais para todas plataformas', '🕹️'),
(6, 'Acessórios', 'Controles, headsets, teclados e mouses', '🎧');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cupons`
--

CREATE TABLE `cupons` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `desconto` decimal(5,2) NOT NULL,
  `tipo` enum('percentual','fixo') DEFAULT 'percentual',
  `ativo` tinyint(1) DEFAULT 1,
  `validade` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `frete` decimal(10,2) DEFAULT 0.00,
  `status` enum('Pendente','Pago','Enviado','Entregue','Cancelado') DEFAULT 'Pendente',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_pagamento` timestamp NULL DEFAULT NULL,
  `endereco_entrega` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `cliente_id`, `total`, `frete`, `status`, `metodo_pagamento`, `data_criacao`, `data_pagamento`, `endereco_entrega`) VALUES
(1, 2, 4999.90, 0.00, 'Pago', 'PIX', '2025-11-17 20:38:07', '2025-11-17 20:38:10', 'Rua Zike Tuma 142, São Paulo - SP, CEP: 04458-000'),
(2, 2, 599.90, 0.00, 'Pago', 'PIX', '2025-11-17 20:45:24', '2025-11-17 20:45:32', 'Rua Zike Tuma 142, São Paulo - SP, CEP: 04458-000');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `valor_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `pedido_id`, `produto_id`, `quantidade`, `valor_unitario`) VALUES
(1, 1, 15, 1, 4999.90),
(2, 2, 1, 1, 599.90);

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `nome` varchar(200) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `estoque` int(11) DEFAULT 0,
  `imagem` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `categoria_id`, `nome`, `preco`, `estoque`, `imagem`, `descricao`, `destaque`, `created_at`) VALUES
(1, 1, 'PlayStation 2 Slim', 599.90, 14, 'ps2-slim.jpg', 'Console PS2 Slim em excelente estado, com controle e memory card. Testado e funcionando perfeitamente.', 1, '2025-11-17 18:44:58'),
(2, 1, 'Xbox Original Crystal', 899.90, 8, 'xbox-crystal.jpg', 'Xbox Original edição Crystal transparente, raridade para colecionadores.', 1, '2025-11-17 18:44:58'),
(3, 1, 'Nintendo GameCube Purple', 749.90, 12, 'gamecube-purple.jpg', 'GameCube roxo completo com todos os cabos e controle oficial.', 0, '2025-11-17 18:44:58'),
(4, 1, 'Sega Dreamcast', 1199.90, 5, 'dreamcast.jpg', 'Dreamcast completo, console que marcou época com jogos incríveis.', 1, '2025-11-17 18:44:58'),
(5, 1, 'Xbox 360 Elite', 799.90, 10, 'xbox360-elite.jpg', 'Xbox 360 Elite 120GB, desbloqueado com HD cheio de jogos.', 0, '2025-11-17 18:44:58'),
(6, 2, 'PlayStation 5 Digital', 3499.90, 20, 'ps5-digital.jpg', 'PS5 Edição Digital, nova geração de jogos em 4K.', 1, '2025-11-17 18:44:58'),
(7, 2, 'Xbox Series X', 4199.90, 15, 'series-x.jpg', 'Xbox Series X 1TB, o console mais poderoso do mercado.', 1, '2025-11-17 18:44:58'),
(8, 2, 'Nintendo Switch OLED', 2799.90, 25, 'switch-oled.jpg', 'Switch OLED com tela vibrante de 7 polegadas.', 1, '2025-11-17 18:44:58'),
(9, 2, 'PlayStation 5 Standard', 4299.90, 18, 'ps5-standard.jpg', 'PS5 com leitor de disco, compatível com jogos físicos.', 0, '2025-11-17 18:44:58'),
(10, 2, 'Xbox Series S', 2499.90, 30, 'series-s.jpg', 'Xbox Series S compacto, perfeito para 1080p e 1440p.', 0, '2025-11-17 18:44:58'),
(11, 3, 'PC Gamer RTX 4060', 5999.90, 10, 'pc-rtx4060.jpg', 'PC Gamer completo: i5-13400F, RTX 4060, 16GB RAM, SSD 500GB.', 1, '2025-11-17 18:44:58'),
(12, 3, 'PC Gamer RTX 4070', 7999.90, 8, 'pc-rtx4070.jpg', 'Setup high-end: Ryzen 7 5800X, RTX 4070, 32GB RAM, SSD 1TB.', 1, '2025-11-17 18:44:58'),
(13, 3, 'Notebook Gamer Nitro 5', 4999.90, 12, 'nitro5.jpg', 'Acer Nitro 5: i7-12700H, RTX 3060, 16GB, SSD 512GB, tela 144Hz.', 0, '2025-11-17 18:44:58'),
(14, 3, 'PC Gamer Entry Level', 3499.90, 15, 'pc-entry.jpg', 'PC para jogos leves: i3-12100F, GTX 1650, 8GB RAM, SSD 256GB.', 0, '2025-11-17 18:44:58'),
(15, 4, 'RTX 4070 Ti SUPER', 4999.90, 5, 'rtx4070ti.jpg', 'Placa de vídeo NVIDIA RTX 4070 Ti SUPER 16GB GDDR6X.', 1, '2025-11-17 18:44:58'),
(16, 4, 'Ryzen 9 7950X', 3199.90, 10, 'ryzen9-7950x.jpg', 'Processador AMD Ryzen 9 7950X 16-core 5.7GHz.', 0, '2025-11-17 18:44:58'),
(17, 4, 'Memória 32GB DDR5', 899.90, 20, 'ram-ddr5.jpg', 'Kit 2x16GB DDR5 6000MHz RGB Corsair Vengeance.', 0, '2025-11-17 18:44:58'),
(18, 4, 'SSD 2TB NVMe', 799.90, 25, 'ssd-2tb.jpg', 'SSD Kingston NVMe 2TB PCIe 4.0 7000MB/s.', 0, '2025-11-17 18:44:58'),
(19, 4, 'Fonte 850W Gold', 649.90, 15, 'fonte-850w.jpg', 'Fonte modular 850W 80 Plus Gold Corsair RM850x.', 0, '2025-11-17 18:44:58'),
(20, 5, 'The Last of Us Part II', 149.90, 50, 'tlou2.jpg', 'Jogo físico para PlayStation 4/5, obra-prima da Naughty Dog.', 0, '2025-11-17 18:44:58'),
(21, 5, 'Elden Ring', 199.90, 40, 'eldenring.jpg', 'RPG de mundo aberto para PS5/Xbox/PC, GOTY 2022.', 1, '2025-11-17 18:44:58'),
(22, 5, 'Super Mario Wonder', 299.90, 35, 'mario-wonder.jpg', 'Aventura 2D de Mario para Nintendo Switch.', 0, '2025-11-17 18:44:58'),
(23, 5, 'GTA V Premium', 99.90, 60, 'gtav.jpg', 'Grand Theft Auto V edição premium para todas plataformas.', 0, '2025-11-17 18:44:58'),
(24, 5, 'God of War Ragnarök', 249.90, 30, 'gow-ragnarok.jpg', 'Continuação épica da saga de Kratos no PS5.', 1, '2025-11-17 18:44:58'),
(25, 6, 'Controle DualSense', 449.90, 40, 'dualsense.jpg', 'Controle sem fio DualSense para PlayStation 5.', 0, '2025-11-17 18:44:58'),
(26, 6, 'Headset HyperX Cloud II', 599.90, 25, 'hyperx-cloud2.jpg', 'Headset gamer 7.1 surround HyperX Cloud II.', 1, '2025-11-17 18:44:58'),
(27, 6, 'Teclado Mecânico Redragon', 349.90, 30, 'teclado-redragon.jpg', 'Teclado mecânico RGB switch blue ABNT2.', 0, '2025-11-17 18:44:58'),
(28, 6, 'Mouse Logitech G502', 299.90, 35, 'g502.jpg', 'Mouse gamer Logitech G502 HERO 25K DPI.', 0, '2025-11-17 18:44:58'),
(29, 6, 'Cadeira Gamer DT3', 1299.90, 12, 'cadeira-dt3.jpg', 'Cadeira gamer DT3 Sports reclinável com apoio lombar.', 0, '2025-11-17 18:44:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('cliente','admin') DEFAULT 'cliente',
  `endereco` text DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `tipo`, `endereco`, `cidade`, `estado`, `cep`, `created_at`) VALUES
(1, 'Admin', 'admin@retrogames.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, NULL, '2025-11-17 18:44:58'),
(2, 'Miguel Erick Assunção Kuipers', 'miguelerick.a.k@gmail.com', '$2y$10$UZQz/Zxmjt4bpXwKzjY8aeY9NWQcR4N61K0dZo46GkeHBC6hEju/.', 'cliente', 'Rua Zike Tuma 142', 'São Paulo', 'SP', '04458-000', '2025-11-17 20:35:27');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cupons`
--
ALTER TABLE `cupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `cupons`
--
ALTER TABLE `cupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `products` (`id`);

--
-- Restrições para tabelas `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
