-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-03-2026 a las 20:04:20
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `recuperarclave`
--
CREATE DATABASE IF NOT EXISTS `recuperarclave` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `recuperarclave`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro`
--

CREATE TABLE `registro` (
  `Id` int(11) NOT NULL,
  `Usuario` varchar(15) NOT NULL,
  `Correo` varchar(50) NOT NULL,
  `Contrasena` varchar(255) NOT NULL,
  `token_password` varchar(255) NOT NULL,
  `token_request` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro`
--

INSERT INTO `registro` (`Id`, `Usuario`, `Correo`, `Contrasena`, `token_password`, `token_request`) VALUES
(1, 'victor', 'rinconvictor@yahoo.com', '1', '', 1),
(2, 'julio', 'rinconvictor19800@gmail.com', '1', '', 1),
(3, 'daniel', 'danielll2004556@gmail.com', '1', '', 1),
(4, 'Luis', 'luiscarlosprietobracho@gmail.com', '3', '', 1),
(5, 'instructor', 'atengaseatenido@gmail.com', '1', '', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `registro`
--
ALTER TABLE `registro`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `registro`
--
ALTER TABLE `registro`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
