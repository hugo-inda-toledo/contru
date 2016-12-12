-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-09-2016 a las 16:21:56
-- Versión del servidor: 5.6.17
-- Versión de PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `ldz`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `group_keyword` varchar(75) NOT NULL,
  `level` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1',
  `redirect_controller` varchar(255) DEFAULT NULL,
  `redirect_action` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- Volcado de datos para la tabla `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `group_keyword`, `level`, `status`, `redirect_controller`, `redirect_action`, `created`, `modified`) VALUES
(1, 'Administrador', 'Administrador Sistema', 'administrador', 10, 1, NULL, NULL, '2015-07-29 12:45:11', '2016-09-14 11:04:02'),
(2, 'Coordinador Proyectos', 'Coordinador de Proyectos', 'coordinador_proyectos', 9, 1, NULL, NULL, '2015-09-21 14:33:29', '2015-09-21 14:33:29'),
(3, 'Gerente General', 'Gerente General Empresa', 'gerente_general', 10, 1, NULL, NULL, '2015-09-21 14:33:51', '2015-09-21 14:33:51'),
(4, 'Gerente Finanzas', 'Gerente Finanzas Empresa', 'gerente_finanzas', 8, 1, NULL, NULL, '2015-09-21 14:34:15', '2015-09-21 14:34:15'),
(5, 'Jefe RRHH', 'Jefe Recursos Humanos', 'jefe_rrhh', 8, 1, NULL, NULL, '2015-09-21 14:43:21', '2015-09-21 14:43:21'),
(6, 'Visitador', 'Visitador de Obra', 'visitador', 7, 1, NULL, NULL, '2015-09-21 14:43:35', '2015-09-21 14:43:35'),
(7, 'Admin Obra', 'Administrador de Obra', 'admin_obra', 5, 1, NULL, NULL, '2015-09-21 14:43:49', '2015-09-21 14:43:49'),
(8, 'Asistente RRHH', 'Asistente Recursos Humanos', 'asistente_rrhh', 6, 1, NULL, NULL, '2015-09-21 14:44:08', '2015-09-21 14:44:08'),
(9, 'Jefe Adquisiciones', 'Jefe Adquisiciones', 'jefe_adquisiciones', 8, 1, NULL, NULL, '2015-10-06 12:44:41', '2015-10-06 12:44:41'),
(10, 'Jefe Inventario', 'Jefe Inventario', 'jefe_inventario', 8, 1, NULL, NULL, '2015-10-06 12:44:59', '2015-10-06 12:44:59'),
(11, 'Finanzas', 'Finanzas', 'finanzas', 6, 1, NULL, NULL, '2015-10-06 12:45:16', '2015-10-06 12:46:55'),
(12, 'Oficina Tecnica', 'Oficina Tecnica', 'oficina_tecnica', 7, 1, NULL, NULL, '2015-10-06 12:45:36', '2015-10-06 12:45:36'),
(13, 'Bodega', 'Bodega', 'bodega', 6, 1, NULL, NULL, '2015-10-06 12:46:15', '2016-05-30 18:24:26'),
(14, 'Contabilidad', 'Contabilidad', 'contabilidad', 6, 1, NULL, NULL, '2015-10-06 12:47:20', '2015-10-06 12:47:20'),
(33, 'test2015', 'dsfds', 'test_2015', 7, 1, NULL, NULL, '2016-09-14 15:55:31', '2016-09-14 15:55:31'),
(34, 'prueba de keyword', 'defsf', 'prueba_de_keyword', 3, 1, NULL, NULL, '2016-09-20 10:53:43', '2016-09-20 10:53:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `groups_permissions`
--

CREATE TABLE IF NOT EXISTS `groups_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=138 ;

--
-- Volcado de datos para la tabla `groups_permissions`
--

INSERT INTO `groups_permissions` (`id`, `group_id`, `permission_id`, `created`, `modified`) VALUES
(1, 1, 1, '2016-09-14 12:43:33', '2016-09-14 12:43:33'),
(20, 1, 2, '2016-09-14 17:21:50', '2016-09-14 17:21:50'),
(22, 33, 1, '2016-09-14 17:24:13', '2016-09-14 17:24:13'),
(23, 33, 9, '2016-09-14 17:24:13', '2016-09-14 17:24:13'),
(24, 1, 3, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(25, 1, 4, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(26, 1, 5, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(27, 1, 6, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(28, 1, 7, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(29, 1, 8, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(30, 1, 9, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(31, 1, 12, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(32, 1, 13, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(33, 1, 14, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(34, 1, 15, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(35, 1, 16, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(36, 1, 17, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(37, 1, 18, '2016-09-15 14:27:01', '2016-09-15 14:27:01'),
(38, 1, 22, '2016-09-16 11:04:33', '2016-09-16 11:04:33'),
(39, 1, 19, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(40, 1, 20, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(41, 1, 21, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(42, 1, 23, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(43, 1, 24, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(44, 1, 25, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(45, 1, 26, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(46, 1, 27, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(47, 1, 28, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(48, 1, 29, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(49, 1, 30, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(50, 1, 31, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(51, 1, 32, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(52, 1, 33, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(53, 1, 34, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(54, 1, 35, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(55, 1, 36, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(56, 1, 37, '2016-09-16 11:10:23', '2016-09-16 11:10:23'),
(57, 1, 38, '2016-09-16 11:20:25', '2016-09-16 11:20:25'),
(58, 1, 39, '2016-09-16 11:21:20', '2016-09-16 11:21:20'),
(59, 1, 40, '2016-09-16 11:54:38', '2016-09-16 11:54:38'),
(60, 1, 41, '2016-09-16 12:20:01', '2016-09-16 12:20:01'),
(61, 1, 42, '2016-09-16 12:22:05', '2016-09-16 12:22:05'),
(62, 3, 1, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(63, 3, 2, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(64, 3, 3, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(65, 3, 4, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(66, 3, 5, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(67, 3, 6, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(68, 3, 7, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(69, 3, 8, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(70, 3, 9, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(71, 3, 12, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(72, 3, 13, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(73, 3, 14, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(74, 3, 15, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(75, 3, 16, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(76, 3, 17, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(77, 3, 18, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(78, 3, 19, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(79, 3, 20, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(80, 3, 21, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(81, 3, 22, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(82, 3, 23, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(83, 3, 24, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(84, 3, 25, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(85, 3, 26, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(86, 3, 27, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(87, 3, 28, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(88, 3, 29, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(89, 3, 30, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(90, 3, 31, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(91, 3, 40, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(92, 3, 41, '2016-09-16 12:38:42', '2016-09-16 12:38:42'),
(93, 1, 43, '2016-09-16 12:41:28', '2016-09-16 12:41:28'),
(94, 1, 44, '2016-09-16 12:57:33', '2016-09-16 12:57:33'),
(95, 3, 44, '2016-09-16 12:57:42', '2016-09-16 12:57:42'),
(96, 34, 1, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(97, 34, 2, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(98, 34, 3, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(99, 34, 4, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(100, 34, 5, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(101, 34, 6, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(102, 34, 7, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(103, 34, 8, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(104, 34, 9, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(105, 34, 12, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(106, 34, 13, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(107, 34, 14, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(108, 34, 15, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(109, 34, 16, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(110, 34, 17, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(111, 34, 18, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(112, 34, 19, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(113, 34, 20, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(114, 34, 21, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(115, 34, 22, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(116, 34, 23, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(117, 34, 24, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(118, 34, 25, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(119, 34, 26, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(120, 34, 27, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(121, 34, 28, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(122, 34, 29, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(123, 34, 30, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(124, 34, 31, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(125, 34, 32, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(126, 34, 33, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(127, 34, 34, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(128, 34, 35, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(129, 34, 36, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(130, 34, 37, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(131, 34, 38, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(132, 34, 39, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(133, 34, 40, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(134, 34, 41, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(135, 34, 42, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(136, 34, 43, '2016-09-20 10:53:43', '2016-09-20 10:53:43'),
(137, 34, 44, '2016-09-20 10:53:43', '2016-09-20 10:53:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(255) NOT NULL,
  `permission_description` text,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=47 ;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `permission_name`, `permission_description`, `controller`, `action`, `created`, `modified`) VALUES
(1, 'Ver Obras', 'Listado de obras activas', 'Buildings', 'index', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(2, 'Panel de Control de Obra', 'Despliega  la información global y muestra opciones de navegación de la obra', 'Buildings', 'dashboard', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(3, 'Máscara de Gastos', 'Grilla con detalle completo de gastos en obra', 'Spends', 'overview', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(4, 'Materiales Comprometidos', 'Muestra todos los items de las ordenes de compra que esten asociados a una partida determinada', 'Spends', 'purchasedMaterialsDetails', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(5, 'Materiales Gastados', 'Muestra todos los items que han sido consumidos para una partida determinada', 'Spends', 'usedMaterialsDetails', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(6, 'Materiales Facturados', 'Muestra items de las ordenes de compra asociados una partida determinada y que además, tengan asociada una factura', 'Spends', 'factMaterialsDetails', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(7, 'Subcontratos Comprometidos', 'Muestra todos los items de los subcontratos que esten asociados a una partida determinada', 'Spends', 'subcontractsDetails', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(8, 'Subcontratos Gastados', 'Muestra todos los estados de pago aprobados para cada subcontrato', 'Spends', 'usedSubcontractsDetails', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(9, 'Subcontratos Facturados', 'Muestralos items de los subcontratos que tenga asociado estados de pago y estos mismos además, tengan una factura asociada', 'Spends', 'factSubcontractsDetails', '2016-09-14 12:42:51', '2016-09-14 12:42:51'),
(12, 'Listar Trabajadores', 'Lista los trabajadores asignados a un presupuesto de obra determinado, información obtenida a través de Softland', 'Workers', 'index', '2016-09-15 12:48:06', '2016-09-15 12:48:06'),
(13, 'Editar Información Obra', 'Edita la información básica de la obra', 'Buildings', 'edit', '2016-09-15 12:49:19', '2016-09-15 12:49:19'),
(14, 'Cambio de estado de Obra', 'Cambia el estado de activo a inactivo a una obra determinada', 'Buildings', 'change_active', '2016-09-15 12:56:51', '2016-09-15 12:56:51'),
(15, 'Reset Obra', 'Elimina el presupuesto completo de la obra', 'BudgetItems', 'reset_obra', '2016-09-15 14:17:25', '2016-09-15 14:17:25'),
(16, 'Ver Presupuestos (Generales, Gast. Adic. y Gast. No Contemplados)', 'Lista todo el presupuesto especifico de una obra determinada', 'Budgets', 'review', '2016-09-15 14:21:46', '2016-09-15 14:21:46'),
(17, 'Editar Presupuesto', 'Edita la información básica, plazos y moneda del prepuesto.', 'Budgets', 'edit', '2016-09-15 14:23:00', '2016-09-15 14:23:00'),
(18, 'Comentar Presupuesto', 'Envía un comentario al presupuesto', 'Budgets', 'comment', '2016-09-15 14:26:42', '2016-09-15 14:26:42'),
(19, 'Agregar Adicionales', 'Agrega adicionales al presupuesto de la obra', 'Budgets', 'add_extra', '2016-09-15 15:11:45', '2016-09-15 15:11:45'),
(20, 'Agregar Gastos No Contemplados', 'Agrega gastos no contemplados al presupuesto de una obra', 'Budgets', 'add_expense', '2016-09-15 15:12:38', '2016-09-15 15:12:38'),
(21, 'Listar Planificaciones', 'Lista las planificaciones de un presupuesto de obra', 'Schedules', 'index', '2016-09-15 15:13:47', '2016-09-15 15:13:47'),
(22, 'Listar Estados de Pago', 'Lista los estados de pago de un presupuesto de obra', 'PaymentStatements', 'index', '2016-09-15 15:15:23', '2016-09-15 15:15:23'),
(23, 'Agregar Planificación', 'Agrega una planificación a un presupuesto de obra', 'Schedules', 'add', '2016-09-15 15:16:37', '2016-09-15 15:16:37'),
(24, 'Listar Asistencias', 'Lista la asistencia de los trabajadores asociados al presupuesto de una obra', 'Assists', 'index', '2016-09-15 15:18:16', '2016-09-15 15:18:16'),
(25, 'Listar Trabajo Realizado', 'Lista el trabajo que se ha realizado en el presupuesto de una obra', 'CompletedTasks', 'index', '2016-09-15 15:21:23', '2016-09-15 15:21:23'),
(26, 'Listar Tratos', 'Lista los tratos que contempla un presupuesto de obra.', 'Deals', 'index', '2016-09-15 15:23:05', '2016-09-15 15:23:05'),
(27, 'Listar Bonos', 'Lista los bonos asociados a un presupuesto de obra', 'Bonuses', 'index', '2016-09-15 15:25:22', '2016-09-15 15:25:22'),
(28, 'Reportes de Remuneraciones', 'Lista el reporte de remuneraciones de los trabajadores asociados a un presupuesto de obra', 'SalaryReports', 'index', '2016-09-15 15:27:56', '2016-09-15 15:27:56'),
(29, 'Detalle de asistencia y extras mensual ', 'Detalle de asistencia, horas extras, atrasos, tratos y bonos mensuales de los trabajadores asociados a un presupuesto de obra', 'Assists', 'assist_month_detail', '2016-09-15 15:43:32', '2016-09-15 15:43:32'),
(30, 'Cambiar estado del presupuesto', 'Cambia el estado del presupuesto de obra', 'BudgetApprovals', 'change', '2016-09-15 15:59:32', '2016-09-15 15:59:32'),
(31, 'Listar registro de actividad (Log)', 'Lista el registro de todas las actividades que la app genera en base a todos los usuarios de la plataforma', 'Histories', 'index', '2016-09-15 16:04:10', '2016-09-15 16:04:10'),
(32, 'Crear Permiso', 'Crear permiso para ser asignado a un perfil', 'Permissions', 'add', '2016-09-16 11:07:23', '2016-09-16 11:07:23'),
(33, 'Listar Permisos', 'Lista de Permisos para perfiles', 'Permissions', 'index', '2016-09-16 11:07:52', '2016-09-16 11:07:52'),
(34, 'Crear Perfil', 'Crea perfil con permisos personalizados', 'Groups', 'add', '2016-09-16 11:08:59', '2016-09-16 11:08:59'),
(35, 'Listar Perfiles', 'Lista los perfiles existentes', 'Groups', 'index', '2016-09-16 11:09:23', '2016-09-16 11:09:23'),
(36, 'Crear Usuario', 'Crea un nuevo usuario.', 'Users', 'add', '2016-09-16 11:09:49', '2016-09-16 11:09:49'),
(37, 'Listar Usuarios', 'Lista los usuarios existentes', 'Users', 'index', '2016-09-16 11:10:06', '2016-09-16 11:10:06'),
(38, 'Editar Perfil', 'Edita un perfil (Además de agregar y/o quitar permisos)', 'Groups', 'edit', '2016-09-16 11:20:05', '2016-09-16 11:20:05'),
(39, 'Ver Perfil', 'Muestra la información del perfil y sus permisos asociados', 'Groups', 'view', '2016-09-16 11:21:11', '2016-09-16 11:21:11'),
(40, 'Obra Actual', 'Muestra el panel de control de la obra abierta en sesión', 'Buildings', 'current', '2016-09-16 11:54:30', '2016-09-16 11:54:30'),
(41, 'Obras Ignoradas', 'Listado de obra ignoradas', 'Buildings', 'omit_buildings', '2016-09-16 12:19:34', '2016-09-16 12:19:34'),
(42, 'Ver Usuario', 'Muestra la información de un usuario', 'Users', 'view', '2016-09-16 12:21:56', '2016-09-16 12:21:56'),
(43, 'Editar Usuario', 'Edita un usuario', 'Users', 'edit', '2016-09-16 12:41:15', '2016-09-16 12:41:15'),
(44, 'Cambiar Contraseña', 'Cambia la contraseña del usuario', 'Users', 'updatePassword', '2016-09-16 12:57:24', '2016-09-16 12:57:24'),
(45, 'Ignorar Obra', 'Ignora una obra cambiando el estado de esta.', 'Buildings', 'ignore_building', '2016-09-20 11:11:07', '2016-09-20 11:11:07'),
(46, 'Habilitar Obra', 'Habilita una obra para comenzar con el proceso del control de presupuesto', 'Buildings', 'enable_building', '2016-09-20 11:12:54', '2016-09-20 11:12:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Volcado de datos para la tabla `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`, `created`, `modified`) VALUES
(2, 4, 3, '2017-01-12 00:00:00', '2017-01-12 00:00:00'),
(3, 16, 3, '2016-09-16 13:07:00', '2016-09-16 13:07:00'),
(4, 1, 2, '2016-09-20 10:23:23', '2016-09-20 10:23:23'),
(6, 1, 1, '2016-09-20 10:23:56', '2016-09-20 10:23:56'),
(7, 2, 2, '2016-09-20 10:26:38', '2016-09-20 10:26:38'),
(8, 3, 4, '2016-09-20 10:26:48', '2016-09-20 10:26:48'),
(9, 5, 5, '2016-09-20 10:27:02', '2016-09-20 10:27:02'),
(10, 6, 6, '2016-09-20 10:49:11', '2016-09-20 10:49:11'),
(11, 7, 7, '2016-09-20 10:50:21', '2016-09-20 10:50:21'),
(12, 8, 8, '2016-09-20 10:50:34', '2016-09-20 10:50:34'),
(13, 1, 11, '2016-09-20 10:51:12', '2016-09-20 10:51:12'),
(14, 17, 13, '2016-09-20 10:52:08', '2016-09-20 10:52:08'),
(15, 17, 1, '2016-09-20 10:52:39', '2016-09-20 10:52:39');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
