-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02-Jun-2022 às 01:40
-- Versão do servidor: 10.4.21-MariaDB
-- versão do PHP: 8.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `helpdesk`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uploaded_on` datetime NOT NULL,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `ticket`
--

CREATE TABLE `ticket` (
  `status` enum('open','closed','resolved') NOT NULL DEFAULT 'open',
  `ticket_codigo` int(11) NOT NULL,
  `ticket_user` varchar(100) NOT NULL,
  `ticket_local` varchar(40) NOT NULL,
  `ticket_nature` varchar(40) NOT NULL,
  `ticket_descricao` varchar(255) NOT NULL,
  `data_hora` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `ticket`
--

INSERT INTO `ticket` (`status`, `ticket_codigo`, `ticket_user`, `ticket_local`, `ticket_nature`, `ticket_descricao`, `data_hora`) VALUES
('open', 1, 'pedro', 'SALA19', 'precedente', 'teste', '0000-00-00 00:00:00'),
('open', 2, 'pedro', 'SALA19', 'precedente', 'tst2', '2022-02-23 11:20:08'),
('open', 3, 'pedrodaniel', 'pedrodaniel', 'pedrodaniel', 'pedrodaniel', '2022-06-01 21:40:46');

-- --------------------------------------------------------

--
-- Estrutura da tabela `tickets_comments`
--

CREATE TABLE `tickets_comments` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `msg` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `CODIGO` varchar(100) CHARACTER SET latin1 NOT NULL,
  `EMAIL` varchar(100) CHARACTER SET latin1 NOT NULL,
  `PASSWORD` varchar(100) CHARACTER SET latin1 NOT NULL,
  `NOME` varchar(200) CHARACTER SET latin1 NOT NULL,
  `NIVEL` int(11) NOT NULL DEFAULT 0,
  `USER_STATUS` int(11) NOT NULL DEFAULT 0,
  `TOKEN_CODE` varchar(200) CHARACTER SET latin1 DEFAULT NULL,
  `MENSAGENS_MARKETING` int(11) NOT NULL DEFAULT 0,
  `DATA_HORA` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`CODIGO`, `EMAIL`, `PASSWORD`, `NOME`, `NIVEL`, `USER_STATUS`, `TOKEN_CODE`, `MENSAGENS_MARKETING`, `DATA_HORA`) VALUES
('pedro', 'pedrobonfim918@gmail.com', '$2y$10$PqaPpeX.QndIaZSTUwDD2OsYANm3fCjN2V.cXCZZv9i1E5q4UI.Bm', 'pedro bonfim', 2, 1, NULL, 1, '2022-02-01 12:17:03'),
('pedroteste2', 'pedrobonfim918@gmail.com', 'Amenic1235', 'Pedro', 1, 2, NULL, 0, ''),
('pedrodaniel', 'pedrobonfim918@gmail.com', '$2y$10$pqa3WNuPXo.4uMvfEo2daeCay0GJhQKxIYvlOTlwuFiSfELdt.c9q', 'Pedro Bonfim', 2, 1, '', 1, '2022-05-30 22:50:27');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_codigo`);

--
-- Índices para tabela `tickets_comments`
--
ALTER TABLE `tickets_comments`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`CODIGO`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_codigo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tickets_comments`
--
ALTER TABLE `tickets_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
