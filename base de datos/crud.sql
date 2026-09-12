-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-03-2026 a las 20:03:54
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
-- Base de datos: `crud`
--
CREATE DATABASE IF NOT EXISTS `crud` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `crud`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `idventa` int(11) NOT NULL,
  `idproducto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `idventa`, `idproducto`, `cantidad`, `precio`) VALUES
(1, 2, 1, 1, 250000.00),
(2, 3, 1, 1, 250000.00),
(3, 3, 2, 1, 453.00),
(4, 4, 3, 1, 99999999.99),
(5, 5, 4, 1, 200.00),
(6, 6, 1, 1, 250000.00),
(7, 7, 2, 1, 50.00),
(8, 8, 2, 1, 50.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envios`
--

CREATE TABLE `envios` (
  `id` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `estado` enum('pendiente','preparando','en_camino','entregado') DEFAULT 'pendiente',
  `fecha_actualizacion` datetime DEFAULT current_timestamp(),
  `id_repartidor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `envios`
--

INSERT INTO `envios` (`id`, `id_venta`, `estado`, `fecha_actualizacion`, `id_repartidor`) VALUES
(1, 2, 'pendiente', '2026-03-18 14:09:05', NULL),
(2, 3, 'pendiente', '2026-03-18 14:09:05', NULL),
(3, 4, 'pendiente', '2026-03-18 14:09:05', NULL),
(4, 6, 'pendiente', '2026-03-18 14:09:05', NULL),
(5, 7, 'pendiente', '2026-03-18 14:09:05', NULL),
(6, 5, 'pendiente', '2026-03-18 14:09:05', NULL),
(8, 4, 'pendiente', '2026-03-18 14:38:07', 115),
(9, 4, 'preparando', '2026-03-18 14:38:08', 115),
(10, 4, 'en_camino', '2026-03-18 14:38:09', 115),
(11, 4, 'entregado', '2026-03-18 14:38:10', 115),
(12, 2, 'pendiente', '2026-03-18 14:46:59', 115),
(13, 2, 'preparando', '2026-03-18 14:47:05', 115),
(14, 2, 'en_camino', '2026-03-18 14:47:09', 115),
(15, 2, 'entregado', '2026-03-18 14:47:13', 115),
(16, 3, 'pendiente', '2026-03-18 14:47:51', 115),
(17, 6, 'pendiente', '2026-03-18 16:13:32', 115),
(18, 6, 'preparando', '2026-03-18 16:13:34', 115),
(19, 6, 'en_camino', '2026-03-18 16:14:06', 115),
(20, 6, 'entregado', '2026-03-18 16:14:08', 115),
(21, 2, 'pendiente', '2026-03-18 16:29:03', NULL),
(22, 3, 'en_camino', '2026-03-18 16:29:14', 115),
(23, 6, 'pendiente', '2026-03-18 16:29:23', NULL),
(24, 4, 'pendiente', '2026-03-18 16:29:27', NULL),
(25, 3, 'pendiente', '2026-03-18 16:29:29', NULL),
(26, 2, 'pendiente', '2026-03-18 16:29:36', 115),
(27, 3, 'pendiente', '2026-03-20 15:50:08', 115),
(28, 3, 'pendiente', '2026-03-20 15:50:11', NULL),
(29, 3, 'pendiente', '2026-03-20 16:32:15', 115),
(30, 4, 'pendiente', '2026-03-20 16:32:16', 115),
(31, 4, 'preparando', '2026-03-20 16:32:19', 115),
(32, 6, 'pendiente', '2026-03-20 16:32:21', 115),
(33, 6, 'en_camino', '2026-03-20 16:32:24', 115),
(34, 7, 'pendiente', '2026-03-20 16:32:24', 115),
(35, 7, 'entregado', '2026-03-20 16:32:26', 115);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `codigo` int(5) DEFAULT NULL,
  `nombres` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`codigo`, `nombres`) VALUES
(2, 'juan carlos'),
(3, 'maria jose');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `stock`, `foto`) VALUES
(1, 'Conjunto supreme', 'colaboración con nike', 250000.00, 7, 'producto_1_24-03-2026_19-31-54.jpg'),
(2, 'audifonos', 'audifonos de cable normal', 50.00, 1, 'producto_2_24-03-2026_19-30-52.jpg'),
(3, 'Conjunto supreme', 'colaboración con travis scott', 99999999.99, 2, 'producto_3_24-03-2026_19-33-20.jpg'),
(4, 'Iphon', '(Color blue)', 6000000.00, 2, 'producto_4_24-03-2026_19-34-00.jpg'),
(8, 'Chukey Supreme', 'Conjuntos supreme', 99999999.99, 5, 'producto_8_24-03-2026_19-35-07.jpg'),
(9, 'Zapatos nike', 'Estilo urbano', 234.00, 10, 'producto_9_24-03-2026_19-35-51.jpg'),
(10, 'Camoseta deportiva', 'Unico', 2000000.00, 1, 'producto_10_24-03-2026_19-36-35.jpg'),
(12, 'Groot supreme', 'Personaje marvel', 8000000.00, 2, 'producto_12_24-03-2026_19-37-19.jpg'),
(13, 'Conjuntos', 'Black', 10000.00, 3, 'producto_13_24-03-2026_19-38-02.jpg'),
(15, 'Cunjunto supreme', '(Femenino)', 1.00, 25, 'producto_15_24-03-2026_19-38-47.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `rol`) VALUES
(1, 'administrador'),
(2, 'cliente'),
(3, 'solicitudes'),
(4, 'envios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  `asunto` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `respuesta` text DEFAULT NULL,
  `estado` varchar(50) DEFAULT 'Pendiente',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id`, `idusuario`, `asunto`, `mensaje`, `respuesta`, `estado`, `fecha`, `fecha_respuesta`) VALUES
(1, 95, '1', '1', 'dfg', 'Respondido', '2026-03-19 22:58:53', '2026-03-20 18:24:34'),
(2, 95, '2', '2', NULL, 'Pendiente', '2026-03-20 18:34:40', NULL),
(3, 95, '3', '3', NULL, 'Pendiente', '2026-03-20 18:34:45', NULL),
(4, 95, '4', '4', NULL, 'Pendiente', '2026-03-20 18:34:50', NULL),
(5, 95, '5', '5', NULL, 'Pendiente', '2026-03-20 18:34:55', NULL),
(6, 95, '7', '7', NULL, 'Pendiente', '2026-03-20 18:35:01', NULL),
(7, 95, '6', '6', NULL, 'Pendiente', '2026-03-20 18:35:05', NULL),
(8, 95, '1', '1', NULL, 'Pendiente', '2026-03-20 18:35:10', NULL),
(9, 95, '2', '2', NULL, 'Pendiente', '2026-03-20 18:35:14', NULL),
(10, 95, 'dgf', 'et', NULL, 'Pendiente', '2026-03-20 18:35:17', NULL),
(11, 95, 'ert', 'ert', NULL, 'Pendiente', '2026-03-20 18:35:22', NULL),
(12, 95, 'brr', 'prueba 1', NULL, 'Pendiente', '2026-03-24 18:27:40', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nomusuario` varchar(100) CHARACTER SET latin1 COLLATE latin1_spanish_ci NOT NULL,
  `clave` varchar(255) NOT NULL,
  `idrol` int(11) NOT NULL,
  `email` varchar(120) NOT NULL,
  `Foto` varchar(100) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `token_password` varchar(255) NOT NULL,
  `token_request` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nomusuario`, `clave`, `idrol`, `email`, `Foto`, `direccion`, `token_password`, `token_request`) VALUES
(2, 'Argemiro', '', 2, 'caza@nena.com.co', 'usuario_2_27-02-2026_19-04-51.webp', 'SALIDA123', '', 1),
(4, 'veronica13', '$2y$10$6C1eB0PcTa16cD7jUMCZ4e6/OjHaGV7ZZWQb6KC/0.lDh4MhpRbgO', 3, 'damidami@damians.com', 'usuario_4_27-02-2026_19-19-40.jpg', 'abajo izquierda123', '', 1),
(66, 'carlino', '$2y$10$KVh71.Vdn0kzT8m2Euz7VurqZE9kpTq0Uv2JABY3SCv98yIuYHluq', 3, 'calino@gmail.com', 'usuario_66_27-02-2026_19-45-52.gif', '', '', 1),
(67, 'carlinata', '', 2, 'calinata@gmail.com', 'usuario_67_27-02-2026_19-49-17.gif', '', '', 1),
(73, 'carlitox', '', 2, 'asdas@asdad', 'usuario_73_27-02-2026_20-37-40.gif', 'adasd', '', 1),
(75, 'lucho', '123', 1, '123@123', 'giphy.gif', '123', '', 1),
(84, 'sebastian', '123', 1, 'aura@gmail.com', '', '', '', 1),
(85, 'sebastian', '123', 1, 'sebas@gmail.com', '', '', '', 1),
(86, 'sebastian', '123', 1, 'sebas@gmail.com', '', '', '', 1),
(87, 'sebastian', '123', 1, 'sebas@gmail.com', '', '', '', 1),
(89, 'sebas', '$2y$10$GHOHEeisuRm7X00pa5XSiO5bKFgRAoXdKouU4BTvliMMw8x.5ySze', 2, 'yulysbeltranmade@gmail.com', 'usuario_66_26-02-2026_23-42-30.png', 'SALIDA123', '', 1),
(90, 'yibelis medina', '1', 1, '487sdf@dsf', 'catalina.webp', 'adentro', '', 1),
(91, 'jhoanny andrade', '1', 1, '1asd@asd', 'fueguito.webp', 'afuera', '', 1),
(92, 'señorita manzanita redonda', '1', 1, '1@1', 'img2.jpg', '123', '', 1),
(93, 'eleazar gonzales', '1', 1, '987@456', 'img4.jpg', 'a un lado', '', 1),
(94, 'edickson ferrer', '1', 1, 'asd@158', 'usuario_65_1772143989.webp', 'al otro lado', '', 1),
(95, 'castillito', '$2y$10$5B9Oz218eoZ871ksUC.jMuEtRVh7aFbFtGX6P8aH375Ly.SvzwRC2', 2, 'asd@dsa', 'usuario_95_24-03-2026_19-27-01.jpg', 'sdfsd', '', 1),
(97, 'sydney sweeney', '1', 1, '', 'usuario_97_24-03-2026_19-51-46.jpg', '', '', 1),
(98, 'lucho', '$2y$10$RkQK2/4f1XfwSU8ubTEjxegnUYxkgXdFTY1ETSLzUAYU88Znx8b1G', 1, 'yulysbeltranmade@gmail.com', 'incognito.png', 'abajo izquierda123', '', 1),
(103, 'luis', '$2y$10$B.wKvg3J6fXIf4WLAqfDL.fZbQ5nAreHF.8uiUICCSJuOHFB.ylCO', 1, 'luiscarlosprietobracho@gmail.com', 'usuario_103_20-03-2026_21-55-55.webp', 'SALIDA1234', '', 0),
(104, 'victor', '1', 1, '', '', '', '', 1),
(107, 'daniel', '$2y$10$S1zBQ80/axtE3DLLebHN3.1LxJocwJbpnA0JexO6qn8kattyFMEGW', 2, 'l@123', 'incognito.png', 'ARRIBA159', '', 1),
(111, 'brandon', '$2y$10$FVljsmHlAdSJdCxCf6b6Z.Gb1mQ3pa5JV0GSeFKF94braKXRFEeau', 2, 'yulysbeltranmade@gmail.com', 'incognito.jpg', 'abajo izquierda123', '', 1),
(112, 'eugenio', '$2y$10$Zorci6gZUFGcOg0fc5b3HeM5Bt3F3w9.4WKxkjK8K5pYjmYAA9wo6', 3, 'isa@gmail.com', 'incognito.jpg', '', '', 1),
(113, 'fabian', '$2y$10$WP4MyO/aVvtdnK7aoXQBFeCpxVfR/g.DFDys0lAbw3bBZlw2qE3E6', 2, 'fabia@sfas', 'incognito.jpg', 'falsa123', '', 1),
(115, 'juancho', '1', 4, '', '', '', '', 1),
(117, 'pepe', '1', 4, '', '', '', '', 1),
(118, 'pepe', '1', 4, '1', '1', '1', '', 0),
(119, 'dani', '$2y$10$CnIiAJxzSFqAbEDTMO4AaO53/45BLFmwyxa5aKwM/CLRZa4snI3/a', 4, 'dano@falso', 'incognito.png', 'calle falsa13', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `idcliente` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `idcliente`, `fecha`, `total`, `idusuario`) VALUES
(2, 95, '2026-03-04 17:47:17', 250000.00, NULL),
(3, 95, '2026-03-05 14:53:49', 250453.00, NULL),
(4, 95, '2026-03-05 16:00:31', 99999999.99, NULL),
(5, 112, '2026-03-06 14:35:16', 200.00, NULL),
(6, 95, '2026-03-06 17:43:34', 250000.00, NULL),
(7, 95, '2026-03-06 17:46:48', 50.00, NULL),
(8, 95, '2026-03-24 13:39:48', 50.00, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idventa` (`idventa`),
  ADD KEY `idproducto` (`idproducto`);

--
-- Indices de la tabla `envios`
--
ALTER TABLE `envios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `id_repartidor` (`id_repartidor`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idrol` (`idrol`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idcliente` (`idcliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `envios`
--
ALTER TABLE `envios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`idventa`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`idproducto`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `envios`
--
ALTER TABLE `envios`
  ADD CONSTRAINT `envios_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  ADD CONSTRAINT `envios_ibfk_2` FOREIGN KEY (`id_repartidor`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`idrol`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`idcliente`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
