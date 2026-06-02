-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 02-06-2026 a las 20:10:03
-- Versión del servidor: 8.0.45-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `ava_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agreements`
--

CREATE TABLE `agreements` (
  `id` bigint UNSIGNED NOT NULL,
  `number` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('contract','credit') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `payment_date` datetime DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0' COMMENT '0: ongoing, 1:finished',
  `paid` tinyint(1) DEFAULT NULL COMMENT '0: no pagado, 1: pagado',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `agreement_details`
--

CREATE TABLE `agreement_details` (
  `id` bigint UNSIGNED NOT NULL,
  `agreement_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `product_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` decimal(10,3) NOT NULL DEFAULT '0.000',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Esta tabla es un registra para calculos futuros en cuanto a saldos de productos.';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cash_closes`
--

CREATE TABLE `cash_closes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `initial_cash_amount` decimal(10,2) DEFAULT NULL,
  `real_cash_amount` decimal(10,2) DEFAULT NULL,
  `final_cash_amount` decimal(10,2) DEFAULT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `isle_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cash_closes`
--

INSERT INTO `cash_closes` (`id`, `user_id`, `location_id`, `initial_cash_amount`, `real_cash_amount`, `final_cash_amount`, `date`, `created_at`, `updated_at`, `isle_id`) VALUES
(1, 5, 3, 60.00, -7927.63, 7197.60, '2026-04-01 00:00:00', '2026-04-01 18:47:45', '2026-04-02 21:03:34', 7),
(2, 5, 3, 60.00, -3309.00, 3313.00, '2026-04-01 00:00:00', '2026-04-01 18:49:15', '2026-04-02 21:03:57', 8),
(3, 5, 3, 60.00, 8731.00, 5644.50, '2026-04-02 00:00:00', '2026-04-02 21:05:06', '2026-04-03 09:00:48', 7),
(4, 5, 3, 60.00, -1372.19, 5644.50, '2026-04-02 00:00:00', '2026-04-02 21:05:18', '2026-04-03 09:01:06', 8),
(5, 5, 3, 60.00, 164.10, 2918.00, '2026-04-03 00:00:00', '2026-04-03 09:01:34', '2026-04-05 22:16:35', 7),
(6, 5, 3, 60.00, -59.63, 1155.00, '2026-04-03 00:00:00', '2026-04-03 09:01:43', '2026-04-05 22:16:56', 8),
(7, 5, 3, 60.00, NULL, NULL, '2026-04-05 00:00:00', '2026-04-05 22:25:28', '2026-04-05 22:25:28', 7),
(8, 5, 3, 60.00, NULL, NULL, '2026-04-05 00:00:00', '2026-04-05 22:25:42', '2026-04-05 22:25:42', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id` bigint UNSIGNED NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commercial_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `business_name`, `commercial_name`, `contact_name`, `document`, `phone`, `address`, `department`, `province`, `district`, `deleted`, `created_at`, `updated_at`) VALUES
(26, NULL, NULL, 'CLIENTES VARIOS', 'VARIOS', NULL, NULL, NULL, NULL, NULL, 0, '2026-01-23 15:01:07', '2026-01-23 15:01:07'),
(30, 'TRANSPORTES Y SERVICIOS LUCELINA SAC', 'TRANSPORTES Y SERVICIOS LUCELINA SAC', 'MARTA ILDA', '20600444060', '943572843', 'CALL. SAN FRANCISCO 1472', 'Lambayeque', 'Chiclayo', 'José Leonardo Ortiz', 0, '2026-03-03 16:53:17', '2026-03-03 16:53:17'),
(31, 'EMPRESA DE TRANSPORTE Y SERVICIOS GENERALES LEON SAC', 'EMPRESA DE TRANSPORTE Y SERVICIOS GENERALES LEON SAC', NULL, '20604664897', '978083152', 'OTR- ANEXO EL PROGRESO NRO 0219 C.P MENOR EL PROGRESO PERU', 'Amazonas', 'Bongará', 'Yambrasbamba', 0, '2026-03-03 16:57:38', '2026-03-03 16:57:38'),
(32, 'HECTOR EDGAR OCAÑA IZQUIERDO', NULL, NULL, '10167028140', '979998626', 'AV. VICTOR BELAUNDE 458 URB. INGENIERO', 'Lambayeque', 'Chiclayo', 'Chiclayo', 0, '2026-03-03 17:01:16', '2026-03-03 17:01:16'),
(33, 'DIMAR AGUILA BERMEO', 'DIMAR AGUILA BERMEO', NULL, '10165006807', '917209662', 'MZA J-LOTE 23 C.P.M SAN MIGUEL', 'Lambayeque', 'Chiclayo', 'José Leonardo Ortiz', 0, '2026-03-03 17:04:01', '2026-03-03 17:04:01'),
(34, 'EMPRESA DE TRANSPORTE LUZ ANGELICA EIRL', 'EMPRESA DE TRANSPORTE LUZ ANGELICA EIRL', NULL, '20480517823', '955466568', 'CAL. SAN FRANCISCO NRO 1472 CPM JORGE CHAVEZ', 'Lambayeque', 'Chiclayo', 'José Leonardo Ortiz', 0, '2026-03-03 17:07:24', '2026-03-03 17:07:24'),
(38, NULL, NULL, 'CLIENTES VARIOS', '77231754', NULL, NULL, NULL, NULL, NULL, 0, '2026-03-04 21:50:28', '2026-03-04 21:50:28'),
(39, NULL, NULL, NULL, '10772317544', NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 12:45:30', '2026-03-06 12:45:30'),
(40, 'CRUZADO SALAZAR TANIA MARLENE', NULL, NULL, '10400988345', NULL, NULL, NULL, NULL, NULL, 0, '2026-04-02 07:10:46', '2026-04-02 07:10:46'),
(41, 'VALLEJOS GALLARDO YENY MELISSA', NULL, NULL, '10402853170', NULL, NULL, NULL, NULL, NULL, 0, '2026-04-02 07:12:27', '2026-04-02 07:12:27'),
(42, 'FERRETERIA CASA FUERTE E.I.R.L.', NULL, NULL, '20611921579', NULL, NULL, NULL, NULL, NULL, 0, '2026-04-05 23:51:05', '2026-04-05 23:51:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config`
--

CREATE TABLE `config` (
  `id` bigint UNSIGNED NOT NULL,
  `number` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `config`
--

INSERT INTO `config` (`id`, `number`) VALUES
(1, 585);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discharges`
--

CREATE TABLE `discharges` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `total_quantity` decimal(10,3) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `discharge_details`
--

CREATE TABLE `discharge_details` (
  `discharge_id` bigint UNSIGNED NOT NULL,
  `tank_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `truck_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `pin` char(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `employees`
--

INSERT INTO `employees` (`id`, `name`, `last_name`, `document`, `birth_date`, `phone`, `address`, `location_id`, `pin`, `deleted`, `created_at`, `updated_at`) VALUES
(4, 'Colaborador', '1', '12345678', '2000-01-01', '987654321', 'ASD', 3, '0000', 0, NULL, '2025-11-28 18:29:31'),
(5, 'Colaborador', '2', '13245678', '2005-01-11', '965837567', 'Direccion', 3, '1111', 0, '2025-12-11 16:57:58', '2025-12-11 17:04:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `isles`
--

CREATE TABLE `isles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cash_amount` decimal(15,2) DEFAULT '0.00',
  `vault` decimal(15,2) DEFAULT '0.00',
  `location_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='islas';

--
-- Volcado de datos para la tabla `isles`
--

INSERT INTO `isles` (`id`, `name`, `cash_amount`, `vault`, `location_id`, `deleted`) VALUES
(1, 'ISLA 1', 0.00, 0.00, 5, 1),
(2, 'ISLA 2', 0.00, 0.00, 2, 1),
(3, 'ISLA RIOJA 1', 0.00, 0.00, 5, 1),
(4, 'ISLA RIOJA 2', 0.00, 0.00, 5, 0),
(5, 'ISLA RIOJA 3', 0.00, 0.00, 5, 0),
(6, 'ISLA RIOJA 4', 0.00, 0.00, 5, 0),
(7, 'ISLA 1 - BAGUA', 10935.53, 5436.60, 3, 0),
(8, 'ISLA 2 - BAGUA', 6603.50, 2346.40, 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vault` decimal(15,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `locations`
--

INSERT INTO `locations` (`id`, `name`, `deleted`, `created_at`, `updated_at`, `vault`) VALUES
(1, 'Loreto', 0, '2025-08-04 14:47:42', '2025-11-28 16:29:39', 0.00),
(2, 'Naranjos', 0, '2025-08-07 14:31:04', '2025-08-26 17:53:46', 0.00),
(3, 'Bagua', 0, '2025-08-18 20:52:44', '2026-04-03 14:51:17', 40212.00),
(4, 'San Ignacio', 0, '2025-08-26 17:53:29', '2025-08-26 17:53:29', 0.00),
(5, 'Rioja', 0, '2025-11-17 16:10:43', '2025-11-28 18:58:04', 0.00),
(6, 'sede', 1, '2025-11-27 18:25:15', '2025-11-27 18:25:27', 0.00),
(7, 'Sde', 1, '2025-11-27 18:25:44', '2025-11-27 18:25:55', 0.00),
(8, 'Jose Olaya', 0, '2025-11-28 16:31:49', '2025-11-28 16:31:49', 0.00),
(9, 'Santa Ana', 0, '2025-11-28 16:31:59', '2025-11-28 16:31:59', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `location_prices`
--

CREATE TABLE `location_prices` (
  `location_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `unit_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='precios por sede de los productos';

--
-- Volcado de datos para la tabla `location_prices`
--

INSERT INTO `location_prices` (`location_id`, `product_id`, `unit_price`, `created_at`, `updated_at`) VALUES
(1, 10, 12.50, '2025-11-04 11:22:44', '2025-11-24 18:19:00'),
(1, 11, 17.00, '2025-11-07 12:04:30', '2025-11-24 17:29:32'),
(1, 12, 14.00, '2025-11-07 12:04:54', '2025-11-17 16:14:29'),
(2, 10, 10.00, '2025-11-04 11:22:44', '2025-11-24 18:19:00'),
(2, 11, 17.00, '2025-11-07 12:04:30', '2025-11-24 17:29:32'),
(2, 12, 14.00, '2025-11-07 12:04:55', '2025-11-17 16:14:29'),
(3, 10, 15.00, '2025-11-04 11:22:44', '2025-11-24 18:19:00'),
(3, 11, 17.00, '2025-11-07 12:04:31', '2025-11-28 16:53:23'),
(3, 12, 19.50, '2025-11-07 12:04:55', '2026-04-01 18:54:22'),
(3, 14, 22.20, '2025-12-11 11:44:52', '2026-04-01 09:54:53'),
(3, 15, 19.20, '2025-12-16 17:45:06', '2026-04-01 09:55:45'),
(3, 16, 21.10, '2025-12-16 17:44:36', '2026-04-01 09:56:10'),
(4, 10, 13.50, '2025-11-04 11:22:44', '2025-11-24 18:19:00'),
(4, 11, 17.00, '2025-11-07 12:04:31', '2025-11-24 17:29:32'),
(4, 12, 14.00, '2025-11-07 12:04:55', '2025-11-17 16:14:29'),
(5, 10, 15.00, '2025-11-24 18:19:00', '2025-11-25 10:14:43'),
(5, 11, 14.60, '2025-11-17 16:14:37', '2025-11-24 17:29:32'),
(5, 12, 14.60, '2025-11-17 16:14:29', '2025-11-17 16:14:29'),
(5, 13, 14.20, '2025-11-17 16:13:56', '2025-11-17 16:13:56'),
(5, 14, 10.00, '2025-11-25 03:39:38', '2025-11-25 03:39:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maintenances`
--

CREATE TABLE `maintenances` (
  `id` bigint NOT NULL,
  `date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='registro de mantenimientos';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `measurements`
--

CREATE TABLE `measurements` (
  `id` int NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL COMMENT 'auxiliar, siempre debe ser igual al producto del tanque',
  `user_id` bigint DEFAULT NULL,
  `pump_id` bigint DEFAULT NULL,
  `amount_initial` decimal(10,3) DEFAULT NULL,
  `amount_final` decimal(10,3) DEFAULT NULL,
  `amount_theorical` decimal(10,3) DEFAULT NULL,
  `amount_difference` decimal(10,3) DEFAULT NULL,
  `date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mediciones de serafin - mediciones de contometro\r\n';

--
-- Volcado de datos para la tabla `measurements`
--

INSERT INTO `measurements` (`id`, `location_id`, `user_id`, `pump_id`, `amount_initial`, `amount_final`, `amount_theorical`, `amount_difference`, `date`, `description`, `deleted`, `created_at`, `updated_at`) VALUES
(145, 3, 5, 1, 0.000, 531226.448, 0.000, 531226.448, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(146, 3, 5, 12, 0.000, 82142.730, 0.000, 82142.730, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(147, 3, 5, 13, 0.000, 40869.630, 0.000, 40869.630, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(148, 3, 5, 14, 0.000, 8351.717, 0.000, 8351.717, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(149, 3, 5, 15, 0.000, 10372.336, 0.000, 10372.336, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(150, 3, 5, 22, 0.000, 222448.596, 0.000, 222448.596, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(151, 3, 5, 10, 0.000, 412034.893, 0.000, 412034.893, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(152, 3, 5, 17, 0.000, 61021.100, 0.000, 61021.100, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(153, 3, 5, 18, 0.000, 43008.326, 0.000, 43008.326, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(154, 3, 5, 19, 0.000, 7044.897, 0.000, 7044.897, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(155, 3, 5, 20, 0.000, 10775.962, 0.000, 10775.962, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(156, 3, 5, 24, 0.000, 233490.871, 0.000, 233490.871, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(157, 3, 5, 25, 0.000, 24359.090, 0.000, 24359.090, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(158, 3, 5, 26, 0.000, 35630.835, 0.000, 35630.835, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(159, 3, 5, 27, 0.000, 4801.135, 0.000, 4801.135, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(160, 3, 5, 28, 0.000, 55448.324, 0.000, 55448.324, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(161, 3, 5, 36, 0.000, 332065.667, 0.000, 332065.667, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(162, 3, 5, 29, 0.000, 82008.102, 0.000, 82008.102, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(163, 3, 5, 30, 0.000, 55834.715, 0.000, 55834.715, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(164, 3, 5, 32, 0.000, 10137.360, 0.000, 10137.360, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(165, 3, 5, 33, 0.000, 15984.117, 0.000, 15984.117, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(166, 3, 5, 37, 0.000, 310608.239, 0.000, 310608.239, '2026-03-03', NULL, 0, '2026-03-03 15:58:26', '2026-03-03 15:58:26'),
(167, 3, 5, 1, 531226.448, 531577.374, 0.000, 350.926, '2026-03-03', NULL, 0, '2026-03-03 15:59:49', '2026-03-03 15:59:49'),
(168, 3, 5, 12, 82142.730, 82182.437, 0.000, 39.707, '2026-03-03', NULL, 0, '2026-03-03 15:59:49', '2026-03-03 15:59:49'),
(169, 3, 5, 13, 40869.630, 40892.702, 0.000, 23.072, '2026-03-03', NULL, 0, '2026-03-03 15:59:49', '2026-03-03 15:59:49'),
(170, 3, 5, 14, 8351.717, 8351.717, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(171, 3, 5, 15, 10372.336, 10372.336, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(172, 3, 5, 22, 222448.596, 222448.596, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(173, 3, 5, 10, 412034.893, 412390.571, 0.000, 355.678, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(174, 3, 5, 17, 61021.100, 61050.065, 0.000, 28.965, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(175, 3, 5, 18, 43008.326, 43059.175, 0.000, 50.849, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(176, 3, 5, 19, 7044.897, 7044.897, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(177, 3, 5, 20, 10775.962, 10775.962, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(178, 3, 5, 24, 233490.871, 233685.772, 0.000, 194.901, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(179, 3, 5, 25, 24359.090, 24359.090, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(180, 3, 5, 26, 35630.835, 35630.835, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(181, 3, 5, 27, 4801.135, 4801.135, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(182, 3, 5, 28, 55448.324, 55448.324, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(183, 3, 5, 36, 332065.667, 332219.224, 0.000, 153.557, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(184, 3, 5, 29, 82008.102, 82049.586, 0.000, 41.484, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(185, 3, 5, 30, 55834.715, 55903.484, 0.000, 68.769, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(186, 3, 5, 32, 10137.360, 10137.360, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(187, 3, 5, 33, 15984.117, 15984.117, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(188, 3, 5, 37, 310608.239, 310608.239, 0.000, 0.000, '2026-03-03', NULL, 0, '2026-03-03 16:10:48', '2026-03-03 16:10:48'),
(189, 3, 5, 1, 531577.374, 540415.258, 338.872, 8499.012, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(190, 3, 5, 12, 82182.437, 83516.554, 13.514, 1320.603, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(191, 3, 5, 13, 40892.702, 41385.292, 0.000, 492.590, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(192, 3, 5, 14, 8351.717, 8378.122, 4.509, 21.896, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(193, 3, 5, 15, 10372.336, 10768.774, 16.687, 379.751, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(194, 3, 5, 22, 222448.596, 224388.506, 209.668, 1730.242, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(195, 3, 5, 10, 412390.571, 419090.218, 315.362, 6384.285, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(196, 3, 5, 17, 61050.065, 61964.745, 0.000, 914.680, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(197, 3, 5, 18, 43059.175, 43547.144, 0.000, 487.969, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(198, 3, 5, 19, 7044.897, 7050.374, 1.232, 4.245, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(199, 3, 5, 20, 10775.962, 11342.383, 8.336, 558.085, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(200, 3, 5, 24, 233685.772, 237490.584, 50.451, 3754.361, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(201, 3, 5, 25, 24359.090, 24668.725, 0.000, 309.635, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(202, 3, 5, 26, 35630.835, 35661.806, 0.000, 30.971, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(203, 3, 5, 27, 4801.135, 4811.017, 0.000, 9.882, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(204, 3, 5, 28, 55448.324, 56397.708, 0.000, 949.384, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(205, 3, 5, 36, 332219.224, 334957.146, 0.000, 2737.922, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(206, 3, 5, 29, 82049.586, 83652.459, 17.928, 1584.945, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(207, 3, 5, 30, 55903.484, 56552.162, 0.000, 648.678, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(208, 3, 5, 32, 10137.360, 10163.704, 0.000, 26.344, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(209, 3, 5, 33, 15984.117, 16894.924, 0.000, 910.807, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(210, 3, 5, 37, 310608.239, 312144.378, 0.000, 1536.139, '2026-04-02', NULL, 0, '2026-04-02 12:02:32', '2026-04-02 12:02:32'),
(211, 3, 5, 1, 540415.258, 540730.520, 0.000, -315.262, '2026-04-02', NULL, 0, '2026-04-02 20:30:28', '2026-04-02 20:30:28'),
(212, 3, 5, 12, 83516.554, 83516.554, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:30:28', '2026-04-02 20:30:28'),
(213, 3, 5, 13, 41385.292, 41385.292, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:30:28', '2026-04-02 20:30:28'),
(214, 3, 5, 14, 8378.122, 8382.631, 0.000, -4.509, '2026-04-02', NULL, 0, '2026-04-02 20:30:28', '2026-04-02 20:30:28'),
(215, 3, 5, 15, 10768.774, 10789.738, 0.000, -20.964, '2026-04-02', NULL, 0, '2026-04-02 20:30:28', '2026-04-02 20:30:28'),
(216, 3, 5, 22, 224388.506, 224438.959, 0.000, -50.453, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(217, 3, 5, 10, 419090.218, 419442.615, 0.000, -352.397, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(218, 3, 5, 17, 61964.745, 61964.745, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(219, 3, 5, 18, 43547.144, 43547.144, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(220, 3, 5, 19, 7050.374, 7051.607, 0.000, -1.233, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(221, 3, 5, 20, 11342.383, 11386.057, 0.000, -43.674, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(222, 3, 5, 24, 237490.584, 237699.987, 0.000, -209.403, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(223, 3, 5, 25, 24668.725, 24675.484, 0.000, -6.759, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(224, 3, 5, 26, 35661.806, 35661.806, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(225, 3, 5, 27, 4811.017, 4812.462, 0.000, -1.445, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(226, 3, 5, 28, 56397.708, 56397.708, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(227, 3, 5, 36, 334957.146, 334995.435, 0.000, -38.289, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(228, 3, 5, 29, 83652.459, 83720.793, 0.000, -68.334, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(229, 3, 5, 30, 56552.162, 56552.162, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:40:06', '2026-04-02 20:40:06'),
(230, 3, 5, 32, 10163.704, 10168.583, 0.000, -4.879, '2026-04-02', NULL, 0, '2026-04-02 20:43:55', '2026-04-02 20:43:55'),
(231, 3, 5, 33, 16894.924, 16961.302, 0.000, -66.378, '2026-04-02', NULL, 0, '2026-04-02 20:43:55', '2026-04-02 20:43:55'),
(232, 3, 5, 37, 312144.378, 312144.378, 0.000, 0.000, '2026-04-02', NULL, 0, '2026-04-02 20:43:55', '2026-04-02 20:43:55'),
(233, 3, 5, 1, 540730.520, 541135.339, 0.000, -404.819, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(234, 3, 5, 12, 83516.554, 83559.304, 0.000, -42.750, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(235, 3, 5, 13, 41385.292, 41385.292, 0.000, 0.000, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(236, 3, 5, 14, 8382.631, 8390.689, 0.000, -8.058, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(237, 3, 5, 15, 10789.738, 10831.255, 0.000, -41.517, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(238, 3, 5, 22, 224438.959, 224466.177, 0.000, -27.218, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(239, 3, 5, 10, 419442.615, 419648.611, 0.000, -205.996, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(240, 3, 5, 17, 61964.745, 61971.684, 0.000, -6.939, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(241, 3, 5, 18, 43547.144, 43547.144, 0.000, 0.000, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(242, 3, 5, 19, 7051.607, 7069.334, 0.000, -17.727, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(243, 3, 5, 20, 11386.057, 11420.072, 0.000, -34.015, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(244, 3, 5, 24, 237699.987, 237747.286, 0.000, -47.299, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(245, 3, 5, 25, 24675.484, 24680.889, 0.000, -5.405, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(246, 3, 5, 26, 35661.806, 35661.806, 0.000, 0.000, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(247, 3, 5, 27, 4812.462, 4812.462, 0.000, 0.000, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(248, 3, 5, 28, 56397.708, 56711.808, 0.000, -314.100, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(249, 3, 5, 36, 334995.435, 335073.815, 0.000, -78.380, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(250, 3, 5, 29, 83720.793, 83943.738, 0.000, -222.945, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(251, 3, 5, 30, 56552.162, 56552.162, 0.000, 0.000, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(252, 3, 5, 32, 10168.583, 10180.509, 0.000, -11.926, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(253, 3, 5, 33, 16961.302, 17000.948, 0.000, -39.646, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(254, 3, 5, 37, 312144.378, 312204.392, 0.000, -60.014, '2026-04-03', NULL, 0, '2026-04-03 07:28:41', '2026-04-03 07:28:41'),
(255, 3, 5, 1, 541135.339, 541309.359, 0.000, -174.020, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(256, 3, 5, 12, 83559.304, 83559.304, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(257, 3, 5, 13, 41385.292, 41385.292, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(258, 3, 5, 14, 8390.689, 8395.905, 0.000, -5.216, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(259, 3, 5, 15, 10831.255, 10859.417, 0.000, -28.162, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(260, 3, 5, 22, 224466.177, 224479.017, 0.000, -12.840, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(261, 3, 5, 10, 419648.611, 419657.170, 0.000, -8.559, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(262, 3, 5, 17, 61971.684, 61971.684, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(263, 3, 5, 18, 43547.144, 43547.144, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(264, 3, 5, 19, 7069.334, 7079.324, 0.000, -9.990, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(265, 3, 5, 20, 11420.072, 11450.746, 0.000, -30.674, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(266, 3, 5, 24, 237747.286, 237856.563, 0.000, -109.277, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(267, 3, 5, 25, 24680.889, 24680.889, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(268, 3, 5, 26, 35661.806, 35661.806, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(269, 3, 5, 27, 4812.462, 4812.462, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(270, 3, 5, 28, 56711.808, 56711.808, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(271, 3, 5, 36, 335073.815, 335080.572, 0.000, -6.757, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(272, 3, 5, 29, 83943.738, 83979.876, 0.000, -36.138, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(273, 3, 5, 30, 56552.162, 56553.706, 0.000, -1.544, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(274, 3, 5, 32, 10180.509, 10181.934, 0.000, -1.425, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(275, 3, 5, 33, 17000.948, 17048.204, 0.000, -47.256, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(276, 3, 5, 37, 312204.392, 312204.392, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:15:51', '2026-04-05 22:15:51'),
(277, 3, 5, 1, 541309.359, 541662.778, 0.000, -353.419, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(278, 3, 5, 12, 83559.304, 83631.393, 0.000, -72.089, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(279, 3, 5, 13, 41385.292, 41385.292, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(280, 3, 5, 14, 8395.905, 8401.090, 0.000, -5.185, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(281, 3, 5, 15, 10859.417, 10895.297, 0.000, -35.880, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(282, 3, 5, 22, 224479.017, 224479.017, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(283, 3, 5, 10, 419657.170, 419726.541, 0.000, -69.371, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(284, 3, 5, 17, 61971.684, 62010.066, 0.000, -38.382, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(285, 3, 5, 18, 43547.144, 43547.144, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(286, 3, 5, 19, 7079.324, 7082.168, 0.000, -2.844, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(287, 3, 5, 20, 11450.746, 11483.153, 0.000, -32.407, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(288, 3, 5, 24, 237856.563, 237856.563, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(289, 3, 5, 25, 24680.889, 24680.889, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(290, 3, 5, 26, 35661.806, 35661.806, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(291, 3, 5, 27, 4812.462, 4812.462, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(292, 3, 5, 28, 56711.808, 56711.808, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(293, 3, 5, 36, 335080.572, 335080.572, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(294, 3, 5, 29, 83979.876, 84021.908, 0.000, -42.032, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(295, 3, 5, 30, 56553.706, 56553.706, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(296, 3, 5, 32, 10181.934, 10185.728, 0.000, -3.794, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(297, 3, 5, 33, 17048.204, 17071.437, 0.000, -23.233, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52'),
(298, 3, 5, 37, 312204.392, 312204.392, 0.000, 0.000, '2026-04-05', NULL, 0, '2026-04-05 22:24:52', '2026-04-05 22:24:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_07_24_021310_create_clients', 1),
(2, '2025_07_24_021311_create_payment_methods', 1),
(3, '2025_07_24_021314_create_employees', 1),
(4, '2025_07_24_021316_create_products', 1),
(5, '2025_07_24_021317_create_locations', 1),
(6, '2025_07_24_021318_create_tanks', 1),
(7, '2025_07_24_021321_create_suppliers', 1),
(8, '2025_07_24_021325_create_trucks', 1),
(9, '2025_07_24_021326_create_roles', 1),
(10, '2025_07_24_021328_create_users', 1),
(11, '2025_07_25_142404_create_agreements', 1),
(12, '2025_07_25_142405_create_orders', 1),
(13, '2025_07_25_142406_create_order_details', 1),
(14, '2025_07_25_142407_create_sales', 1),
(15, '2025_07_25_192031_create_payments', 1),
(16, '2025_07_25_192038_create_sale_details', 1),
(17, '2025_07_30_221822_create_purchases', 1),
(18, '2025_07_30_234818_create_purchase_details', 1),
(19, '2025_08_01_140214_create_discharges', 1),
(20, '2025_08_01_141315_create_discharge_details', 1),
(21, '2025_08_01_142607_create_transfers', 1),
(23, '2025_11_25_160251_add_status_to_transactions_table', 2),
(24, '2025_11_25_190435_add_isle_id_to_users_table', 3),
(25, '2025_12_20_113314_add_isle_id_to_cash_closes_table', 4),
(26, '2025_12_22_000001_add_isle_id_to_transactions_table', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `agreement_id` bigint UNSIGNED NOT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(10,3) UNSIGNED NOT NULL,
  `remaining` decimal(10,3) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payments`
--

CREATE TABLE `payments` (
  `id` bigint NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `sale_id` bigint UNSIGNED DEFAULT NULL,
  `agreement_id` bigint UNSIGNED DEFAULT NULL,
  `voucher_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voucher_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voucher_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `client_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'paid',
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `observation` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `photo_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `sale_id`, `agreement_id`, `voucher_type`, `voucher_id`, `voucher_file`, `number`, `client_id`, `client_name`, `amount`, `status`, `payment_method_id`, `observation`, `deleted`, `date`, `photo_url`, `created_at`, `updated_at`) VALUES
(1, 7, 257, NULL, 'Factura', NULL, NULL, 'TICKET-00000090', NULL, 'CLIENTES VARIOS', 850.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:42:02', '2026-04-01 22:42:02'),
(2, 7, 258, NULL, 'Factura', NULL, NULL, 'TICKET-00000091', NULL, 'CLIENTES VARIOS', 150.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:43:13', '2026-04-01 22:43:13'),
(3, 7, 259, NULL, 'Factura', NULL, NULL, 'TICKET-00000092', NULL, 'CLIENTES VARIOS', 200.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:44:02', '2026-04-01 22:44:02'),
(4, 7, 260, NULL, 'Factura', NULL, NULL, 'TICKET-00000093', NULL, 'CLIENTES VARIOS', 50.00, 'paid', 2, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:47:02', '2026-04-01 22:47:02'),
(5, 7, 261, NULL, 'Factura', NULL, NULL, 'TICKET-00000094', NULL, 'CLIENTES VARIOS', 157.56, 'paid', 2, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:48:19', '2026-04-01 22:48:19'),
(6, 7, 262, NULL, 'Factura', NULL, NULL, 'TICKET-00000095', NULL, 'CLIENTES VARIOS', 120.00, 'paid', 2, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:48:45', '2026-04-01 22:48:45'),
(7, 7, 263, NULL, 'Factura', NULL, NULL, 'TICKET-00000096', NULL, 'CLIENTES VARIOS', 100.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:49:40', '2026-04-01 22:49:40'),
(8, 7, 264, NULL, 'Factura', NULL, NULL, 'TICKET-00000097', NULL, 'CLIENTES VARIOS', 27.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:50:32', '2026-04-01 22:50:32'),
(9, 7, 265, NULL, 'Factura', NULL, NULL, 'TICKET-00000098', NULL, 'CLIENTES VARIOS', 3.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:51:03', '2026-04-01 22:51:03'),
(10, 7, 266, NULL, 'Factura', NULL, NULL, 'TICKET-00000099', NULL, 'CLIENTES VARIOS', 60.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:51:32', '2026-04-01 22:51:32'),
(11, 7, 267, NULL, 'Factura', NULL, NULL, 'TICKET-00000100', NULL, 'CLIENTES VARIOS', 100.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:52:12', '2026-04-01 22:52:12'),
(12, 7, 268, NULL, 'Factura', NULL, NULL, 'TICKET-00000101', NULL, 'CLIENTES VARIOS', 22.20, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:52:55', '2026-04-01 22:52:55'),
(13, 7, 269, NULL, 'Factura', NULL, NULL, 'TICKET-00000102', NULL, 'CLIENTES VARIOS', 18.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:53:31', '2026-04-01 22:53:31'),
(14, 7, 270, NULL, 'Factura', NULL, NULL, 'TICKET-00000103', NULL, 'CLIENTES VARIOS', 110.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:53:57', '2026-04-01 22:53:57'),
(15, 7, 271, NULL, 'Factura', NULL, NULL, 'TICKET-00000104', NULL, 'CLIENTES VARIOS', 31.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:54:24', '2026-04-01 22:54:24'),
(16, 7, 272, NULL, 'Factura', NULL, NULL, 'TICKET-00000105', NULL, 'CLIENTES VARIOS', 79.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:55:10', '2026-04-01 22:55:10'),
(17, 7, 273, NULL, 'Factura', NULL, NULL, 'TICKET-00000106', NULL, 'CLIENTES VARIOS', 40.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:55:42', '2026-04-01 22:55:42'),
(18, 7, 274, NULL, 'Factura', NULL, NULL, 'TICKET-00000107', NULL, 'CLIENTES VARIOS', 30.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:56:49', '2026-04-01 22:56:49'),
(19, 7, 275, NULL, 'Factura', NULL, NULL, 'TICKET-00000108', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:57:27', '2026-04-01 22:57:27'),
(20, 7, 276, NULL, 'Factura', NULL, NULL, 'TICKET-00000109', NULL, 'CLIENTES VARIOS', 42.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:59:05', '2026-04-01 22:59:05'),
(21, 7, 277, NULL, 'Factura', NULL, NULL, 'TICKET-00000110', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:59:32', '2026-04-01 22:59:32'),
(22, 7, 278, NULL, 'Factura', NULL, NULL, 'TICKET-00000111', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 22:59:46', '2026-04-01 22:59:46'),
(23, 7, 279, NULL, 'Factura', NULL, NULL, 'TICKET-00000112', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:00:08', '2026-04-01 23:00:08'),
(24, 7, 280, NULL, 'Factura', NULL, NULL, 'TICKET-00000113', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:00:29', '2026-04-01 23:00:29'),
(25, 7, 281, NULL, 'Factura', NULL, NULL, 'TICKET-00000114', NULL, 'CLIENTES VARIOS', 26.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:01:54', '2026-04-01 23:01:54'),
(26, 7, 282, NULL, 'Factura', NULL, NULL, 'TICKET-00000115', NULL, 'CLIENTES VARIOS', 8.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:02:23', '2026-04-01 23:02:23'),
(27, 7, 283, NULL, 'Factura', NULL, NULL, 'TICKET-00000116', NULL, 'CLIENTES VARIOS', 30.80, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:04:29', '2026-04-01 23:04:29'),
(28, 7, 284, NULL, 'Factura', NULL, NULL, 'TICKET-00000117', NULL, 'CLIENTES VARIOS', 160.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:06:49', '2026-04-01 23:06:49'),
(29, 7, 285, NULL, 'Factura', NULL, NULL, 'TICKET-00000118', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:09:36', '2026-04-01 23:09:36'),
(30, 7, 286, NULL, 'Factura', NULL, NULL, 'TICKET-00000119', NULL, 'CLIENTES VARIOS', 140.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:10:22', '2026-04-01 23:10:22'),
(31, 7, 287, NULL, 'Factura', NULL, NULL, 'TICKET-00000120', NULL, 'CLIENTES VARIOS', 5.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:10:58', '2026-04-01 23:10:58'),
(32, 7, 288, NULL, 'Factura', NULL, NULL, 'TICKET-00000121', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:11:27', '2026-04-01 23:11:27'),
(33, 7, 289, NULL, 'Factura', NULL, NULL, 'TICKET-00000122', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:11:57', '2026-04-01 23:11:57'),
(34, 7, 290, NULL, 'Factura', NULL, NULL, 'TICKET-00000123', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:12:19', '2026-04-01 23:12:19'),
(35, 7, 291, NULL, 'Factura', NULL, NULL, 'TICKET-00000124', NULL, 'CLIENTES VARIOS', 12.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:14:09', '2026-04-01 23:14:09'),
(36, 7, 292, NULL, 'Factura', NULL, NULL, 'TICKET-00000125', NULL, 'CLIENTES VARIOS', 128.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:15:11', '2026-04-01 23:15:11'),
(37, 7, 293, NULL, 'Factura', NULL, NULL, 'TICKET-00000126', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:15:38', '2026-04-01 23:15:38'),
(38, 7, 294, NULL, 'Factura', NULL, NULL, 'TICKET-00000127', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:16:04', '2026-04-01 23:16:04'),
(39, 7, 295, NULL, 'Factura', NULL, NULL, 'TICKET-00000128', NULL, 'CLIENTES VARIOS', 75.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:16:41', '2026-04-01 23:16:41'),
(40, 7, 296, NULL, 'Factura', NULL, NULL, 'TICKET-00000129', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:17:05', '2026-04-01 23:17:05'),
(41, 7, 297, NULL, 'Factura', NULL, NULL, 'TICKET-00000130', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:18:04', '2026-04-01 23:18:04'),
(42, 7, 298, NULL, 'Factura', NULL, NULL, 'TICKET-00000131', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:18:19', '2026-04-01 23:18:19'),
(43, 7, 299, NULL, 'Factura', NULL, NULL, 'TICKET-00000132', NULL, 'CLIENTES VARIOS', 50.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:18:40', '2026-04-01 23:18:40'),
(44, 7, 300, NULL, 'Factura', NULL, NULL, 'TICKET-00000133', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:18:56', '2026-04-01 23:18:56'),
(45, 7, 301, NULL, 'Factura', NULL, NULL, 'TICKET-00000134', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:19:18', '2026-04-01 23:19:18'),
(46, 7, 302, NULL, 'Factura', NULL, NULL, 'TICKET-00000135', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:21:33', '2026-04-01 23:21:33'),
(47, 7, 303, NULL, 'Factura', NULL, NULL, 'TICKET-00000136', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:21:45', '2026-04-01 23:21:45'),
(48, 7, 304, NULL, 'Factura', NULL, NULL, 'TICKET-00000137', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:21:59', '2026-04-01 23:21:59'),
(49, 7, 305, NULL, 'Factura', NULL, NULL, 'TICKET-00000138', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:22:13', '2026-04-01 23:22:13'),
(50, 7, 306, NULL, 'Factura', NULL, NULL, 'TICKET-00000139', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:22:26', '2026-04-01 23:22:26'),
(51, 7, 307, NULL, 'Factura', NULL, NULL, 'TICKET-00000140', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:22:42', '2026-04-01 23:22:42'),
(52, 7, 308, NULL, 'Factura', NULL, NULL, 'TICKET-00000141', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:22:56', '2026-04-01 23:22:56'),
(53, 7, 309, NULL, 'Factura', NULL, NULL, 'TICKET-00000142', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:23:10', '2026-04-01 23:23:10'),
(54, 7, 310, NULL, 'Factura', NULL, NULL, 'TICKET-00000143', NULL, 'CLIENTES VARIOS', 52.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:23:35', '2026-04-01 23:23:35'),
(55, 7, 311, NULL, 'Factura', NULL, NULL, 'TICKET-00000144', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:23:58', '2026-04-01 23:23:58'),
(56, 7, 312, NULL, 'Factura', NULL, NULL, 'TICKET-00000145', NULL, 'CLIENTES VARIOS', 8.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:24:26', '2026-04-01 23:24:26'),
(57, 7, 313, NULL, 'Factura', NULL, NULL, 'TICKET-00000146', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:24:49', '2026-04-01 23:24:49'),
(58, 7, 314, NULL, 'Factura', NULL, NULL, 'TICKET-00000147', NULL, 'CLIENTES VARIOS', 5.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:25:10', '2026-04-01 23:25:10'),
(59, 7, 315, NULL, 'Factura', NULL, NULL, 'TICKET-00000148', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:25:23', '2026-04-01 23:25:23'),
(60, 7, 316, NULL, 'Factura', NULL, NULL, 'TICKET-00000149', NULL, 'CLIENTES VARIOS', 100.00, 'paid', 1, NULL, 0, '2026-04-01', NULL, '2026-04-01 23:25:41', '2026-04-01 23:25:41'),
(61, 6, 317, NULL, 'Ticket', NULL, NULL, 'TICKET-00000150', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:20:23', '2026-04-02 00:20:23'),
(62, 6, 318, NULL, 'Factura', NULL, NULL, 'TICKET-00000151', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:20:38', '2026-04-02 00:20:38'),
(63, 6, 319, NULL, 'Factura', NULL, NULL, 'TICKET-00000152', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:20:54', '2026-04-02 00:20:54'),
(64, 6, 320, NULL, 'Factura', NULL, NULL, 'TICKET-00000153', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:21:15', '2026-04-02 00:21:15'),
(65, 6, 321, NULL, 'Factura', NULL, NULL, 'TICKET-00000154', NULL, NULL, 15.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:21:29', '2026-04-02 00:21:29'),
(66, 6, 322, NULL, 'Factura', NULL, NULL, 'TICKET-00000155', NULL, NULL, 20.13, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:21:48', '2026-04-02 00:21:48'),
(67, 6, 323, NULL, 'Factura', NULL, NULL, 'TICKET-00000156', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:22:09', '2026-04-02 00:22:09'),
(68, 6, 324, NULL, 'Factura', NULL, NULL, 'TICKET-00000157', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:22:23', '2026-04-02 00:22:23'),
(69, 6, 325, NULL, 'Factura', NULL, NULL, 'TICKET-00000158', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:22:39', '2026-04-02 00:22:39'),
(70, 6, 326, NULL, 'Factura', NULL, NULL, 'TICKET-00000159', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:22:54', '2026-04-02 00:22:54'),
(71, 6, 327, NULL, 'Factura', NULL, NULL, 'TICKET-00000160', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:23:18', '2026-04-02 00:23:18'),
(72, 6, 328, NULL, 'Factura', NULL, NULL, 'TICKET-00000161', NULL, NULL, 15.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:23:34', '2026-04-02 00:23:34'),
(73, 6, 329, NULL, 'Factura', NULL, NULL, 'TICKET-00000162', NULL, NULL, 100.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:23:46', '2026-04-02 00:23:46'),
(74, 6, 330, NULL, 'Factura', NULL, NULL, 'TICKET-00000163', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:24:02', '2026-04-02 00:24:02'),
(75, 6, 331, NULL, 'Factura', NULL, NULL, 'TICKET-00000164', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:24:16', '2026-04-02 00:24:16'),
(76, 6, 332, NULL, 'Factura', NULL, NULL, 'TICKET-00000165', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:24:30', '2026-04-02 00:24:30'),
(77, 6, 333, NULL, 'Factura', NULL, NULL, 'TICKET-00000166', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:24:43', '2026-04-02 00:24:43'),
(78, 6, 334, NULL, 'Factura', NULL, NULL, 'TICKET-00000167', NULL, NULL, 35.37, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:24:58', '2026-04-02 00:24:58'),
(79, 6, 335, NULL, 'Factura', NULL, NULL, 'TICKET-00000168', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:26:34', '2026-04-02 00:26:34'),
(80, 6, 336, NULL, 'Factura', NULL, NULL, 'TICKET-00000169', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:26:46', '2026-04-02 00:26:46'),
(81, 6, 337, NULL, 'Factura', NULL, NULL, 'TICKET-00000170', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:27:02', '2026-04-02 00:27:02'),
(82, 6, 338, NULL, 'Factura', NULL, NULL, 'TICKET-00000171', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:27:17', '2026-04-02 00:27:17'),
(83, 6, 339, NULL, 'Factura', NULL, NULL, 'TICKET-00000172', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:27:31', '2026-04-02 00:27:31'),
(84, 6, 340, NULL, 'Factura', NULL, NULL, 'TICKET-00000173', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:27:46', '2026-04-02 00:27:46'),
(85, 6, 341, NULL, 'Factura', NULL, NULL, 'TICKET-00000174', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:27:59', '2026-04-02 00:27:59'),
(86, 6, 345, NULL, 'Factura', NULL, NULL, 'TICKET-00000175', NULL, NULL, 407.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:32:50', '2026-04-02 00:32:50'),
(87, 6, 346, NULL, 'Factura', NULL, NULL, 'TICKET-00000176', NULL, NULL, 100.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:33:08', '2026-04-02 00:33:08'),
(88, 6, 347, NULL, 'Factura', NULL, NULL, 'TICKET-00000177', NULL, NULL, 850.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:34:25', '2026-04-02 00:34:25'),
(89, 6, 348, NULL, 'Factura', NULL, NULL, 'TICKET-00000178', NULL, NULL, 370.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:35:06', '2026-04-02 00:35:06'),
(90, 6, 349, NULL, 'Factura', NULL, NULL, 'TICKET-00000179', NULL, NULL, 666.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:35:32', '2026-04-02 00:35:32'),
(91, 6, 350, NULL, 'Factura', NULL, NULL, 'TICKET-00000180', NULL, NULL, 520.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:35:48', '2026-04-02 00:35:48'),
(92, 6, 351, NULL, 'Factura', NULL, NULL, 'TICKET-00000181', NULL, NULL, 108.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:36:03', '2026-04-02 00:36:03'),
(93, 6, 352, NULL, 'Factura', NULL, NULL, 'TICKET-00000182', NULL, NULL, 121.07, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:37:55', '2026-04-02 00:37:55'),
(94, 6, 353, NULL, 'Factura', NULL, NULL, 'TICKET-00000183', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:38:16', '2026-04-02 00:38:16'),
(95, 6, 354, NULL, 'Factura', NULL, NULL, 'TICKET-00000184', NULL, NULL, 188.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:38:28', '2026-04-02 00:38:28'),
(96, 6, 355, NULL, 'Factura', NULL, NULL, 'TICKET-00000185', NULL, NULL, 245.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:38:40', '2026-04-02 00:38:40'),
(97, 6, 356, NULL, 'Factura', NULL, NULL, 'TICKET-00000186', NULL, NULL, 500.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:38:53', '2026-04-02 00:38:53'),
(98, 6, 357, NULL, 'Factura', NULL, NULL, 'TICKET-00000187', NULL, NULL, 500.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:41:03', '2026-04-02 00:41:03'),
(99, 6, 358, NULL, 'Factura', NULL, NULL, 'TICKET-00000188', NULL, NULL, 384.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:43:20', '2026-04-02 00:43:20'),
(100, 6, 359, NULL, 'Factura', NULL, NULL, 'TICKET-00000189', NULL, NULL, 1000.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:43:41', '2026-04-02 00:43:41'),
(101, 6, 360, NULL, 'Factura', NULL, NULL, 'TICKET-00000190', NULL, NULL, 100.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:47:16', '2026-04-02 00:47:16'),
(102, 6, 361, NULL, 'Factura', NULL, NULL, 'TICKET-00000191', NULL, NULL, 40.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:47:27', '2026-04-02 00:47:27'),
(103, 6, 362, NULL, 'Factura', NULL, NULL, 'TICKET-00000192', NULL, NULL, 1554.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:47:57', '2026-04-02 00:47:57'),
(104, 6, 363, NULL, 'Factura', NULL, NULL, 'TICKET-00000193', NULL, NULL, 650.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:48:22', '2026-04-02 00:48:22'),
(105, 6, 364, NULL, 'Factura', NULL, NULL, 'TICKET-00000194', NULL, NULL, 141.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:48:48', '2026-04-02 00:48:48'),
(106, 6, 365, NULL, 'Factura', NULL, NULL, 'TICKET-00000195', NULL, NULL, 770.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:49:13', '2026-04-02 00:49:13'),
(107, 6, 366, NULL, 'Factura', NULL, NULL, 'TICKET-00000196', NULL, NULL, 60.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:52:30', '2026-04-02 00:52:30'),
(108, 6, 367, NULL, 'Factura', NULL, NULL, 'TICKET-00000197', NULL, NULL, 930.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:52:57', '2026-04-02 00:52:57'),
(109, 6, 368, NULL, 'Factura', NULL, NULL, 'TICKET-00000198', NULL, NULL, 130.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:53:12', '2026-04-02 00:53:12'),
(110, 6, 369, NULL, 'Factura', NULL, NULL, 'TICKET-00000199', NULL, NULL, 23.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:55:08', '2026-04-02 00:55:08'),
(111, 6, 370, NULL, 'Factura', NULL, NULL, 'TICKET-00000200', NULL, NULL, 23.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 00:55:47', '2026-04-02 00:55:47'),
(112, 6, 372, NULL, 'Factura', NULL, NULL, 'TICKET-00000201', NULL, NULL, 26.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 01:40:49', '2026-04-02 01:40:49'),
(113, 6, 373, NULL, 'Factura', NULL, NULL, 'TICKET-00000202', NULL, NULL, 200.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 04:42:56', '2026-04-02 04:42:56'),
(114, 6, 374, NULL, 'Factura', NULL, NULL, 'TICKET-00000203', NULL, NULL, 200.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 05:12:46', '2026-04-02 05:12:46'),
(115, 6, 375, NULL, 'Factura', NULL, NULL, 'TICKET-00000204', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:00:50', '2026-04-02 06:00:50'),
(116, 6, 378, NULL, 'Factura', NULL, NULL, 'TICKET-00000205', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:06:13', '2026-04-02 06:06:13'),
(117, 6, 379, NULL, 'Factura', NULL, NULL, 'TICKET-00000206', NULL, NULL, 300.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:14:09', '2026-04-02 06:14:09'),
(118, 6, 381, NULL, 'Factura', NULL, NULL, 'TICKET-00000207', NULL, NULL, 60.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:16:26', '2026-04-02 06:16:26'),
(119, 7, 382, NULL, 'Factura', NULL, NULL, 'TICKET-00000208', NULL, 'CLIENTES VARIOS', 80.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:35:36', '2026-04-02 06:35:36'),
(120, 7, 383, NULL, 'Factura', NULL, NULL, 'TICKET-00000209', NULL, 'CLIENTES VARIOS', 70.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:35:56', '2026-04-02 06:35:56'),
(121, 7, 384, NULL, 'Factura', NULL, NULL, 'TICKET-00000210', NULL, 'CLIENTES VARIOS', 50.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:36:18', '2026-04-02 06:36:18'),
(122, 7, 385, NULL, 'Factura', NULL, NULL, 'TICKET-00000211', NULL, 'CLIENTES VARIOS', 198.00, 'paid', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:36:35', '2026-04-02 06:36:35'),
(123, 6, 388, NULL, 'Factura', NULL, NULL, 'TICKET-00000212', NULL, NULL, 254.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:56:48', '2026-04-02 06:56:48'),
(124, 6, 389, NULL, 'Factura', NULL, NULL, 'TICKET-00000213', NULL, NULL, 388.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 06:57:15', '2026-04-02 06:57:15'),
(125, 6, 390, NULL, 'Ticket', NULL, NULL, '2315', NULL, 'EMPRESA DE TRANSPORTES LUZ ANGELICA EIRL', 2331.00, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 07:06:15', '2026-04-02 07:06:15'),
(126, 6, 391, NULL, 'Ticket', NULL, NULL, '2316', NULL, 'OCAÑA IZQUIERDO HECTOR EDGARDO', 1354.20, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 07:07:59', '2026-04-02 07:07:59'),
(127, 6, 392, NULL, 'Ticket', NULL, NULL, '2317', NULL, 'EMPRESA DE TRANSPORTE Y SERVICIOS GENERALES LEON S.A.C.', 1074.70, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 07:09:39', '2026-04-02 07:09:39'),
(128, 6, 393, NULL, 'Ticket', NULL, NULL, '2318', NULL, 'CRUZADO SALAZAR TANIA MARLENE', 1110.00, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 07:10:46', '2026-04-02 07:10:46'),
(129, 6, 394, NULL, 'Ticket', NULL, NULL, '2319', NULL, 'VALLEJOS GALLARDO YENY MELISSA', 1176.62, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 07:12:27', '2026-04-02 07:12:27'),
(130, 6, 395, NULL, 'Ticket', NULL, NULL, '2320', NULL, 'EMPRESA DE TRANSPORTE Y SERVICIOS GENERALES LEON S.A.C.', 1720.03, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 07:13:40', '2026-04-02 07:13:40'),
(131, 7, 398, NULL, 'Ticket', NULL, NULL, '2325', NULL, 'RAMIRO MENDOZA', 6030.72, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 21:15:17', '2026-04-02 21:15:17'),
(132, 7, 399, NULL, 'Ticket', NULL, NULL, '2324', NULL, 'RAMIRO MENDOZA', 4059.31, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 21:18:15', '2026-04-02 21:18:15'),
(133, 7, 400, NULL, 'Factura', NULL, NULL, 'TICKET-00000222', NULL, 'CLIENTES VARIOS', 30.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 21:19:40', '2026-04-02 21:19:40'),
(134, 6, 401, NULL, 'Ticket', NULL, NULL, '2325', NULL, 'valeria', 1122.81, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 23:32:10', '2026-04-02 23:32:10'),
(135, 6, 402, NULL, 'Ticket', NULL, NULL, '2321', NULL, 'VALERIA', 3452.14, 'pending', 1, NULL, 0, '2026-04-02', NULL, '2026-04-02 23:35:32', '2026-04-02 23:35:32'),
(136, 6, 403, NULL, 'Factura', NULL, NULL, 'TICKET-00000225', NULL, NULL, 100.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 23:39:05', '2026-04-02 23:39:05'),
(137, 6, 404, NULL, 'Factura', NULL, NULL, 'TICKET-00000226', NULL, NULL, 300.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 23:41:00', '2026-04-02 23:41:00'),
(138, 6, 405, NULL, 'Factura', NULL, NULL, 'TICKET-00000227', NULL, NULL, 100.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 23:42:23', '2026-04-02 23:42:23'),
(139, 6, 406, NULL, 'Factura', NULL, NULL, 'TICKET-00000228', NULL, NULL, 200.00, 'paid', 2, NULL, 0, '2026-04-02', NULL, '2026-04-02 23:45:04', '2026-04-02 23:45:04'),
(140, 6, 407, NULL, 'Factura', NULL, NULL, 'TICKET-00000229', NULL, NULL, 184.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:13:45', '2026-04-03 00:13:45'),
(141, 6, 408, NULL, 'Factura', NULL, NULL, 'TICKET-00000230', NULL, NULL, 30.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:27:07', '2026-04-03 00:27:07'),
(142, 6, 409, NULL, 'Factura', NULL, NULL, 'TICKET-00000231', NULL, NULL, 20.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:28:44', '2026-04-03 00:28:44'),
(143, 6, 410, NULL, 'Factura', NULL, NULL, 'TICKET-00000232', NULL, NULL, 300.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:34:18', '2026-04-03 00:34:18'),
(144, 7, 411, NULL, 'Factura', NULL, NULL, 'TICKET-00000233', NULL, NULL, 1535.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:48:39', '2026-04-03 00:48:39'),
(145, 7, 412, NULL, 'Factura', NULL, NULL, 'TICKET-00000234', NULL, NULL, 666.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:50:05', '2026-04-03 00:50:05'),
(146, 6, 413, NULL, 'Factura', NULL, NULL, 'TICKET-00000235', NULL, NULL, 666.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:52:54', '2026-04-03 00:52:54'),
(147, 6, 414, NULL, 'Factura', NULL, NULL, 'TICKET-00000236', NULL, NULL, 100.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:54:05', '2026-04-03 00:54:05'),
(148, 6, 415, NULL, 'Factura', NULL, NULL, 'TICKET-00000237', NULL, NULL, 444.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:58:28', '2026-04-03 00:58:28'),
(149, 6, 416, NULL, 'Factura', NULL, NULL, 'TICKET-00000238', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:59:06', '2026-04-03 00:59:06'),
(150, 6, 417, NULL, 'Factura', NULL, NULL, 'TICKET-00000239', NULL, NULL, 200.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 00:59:32', '2026-04-03 00:59:32'),
(151, 6, 418, NULL, 'Factura', NULL, NULL, 'TICKET-00000240', NULL, NULL, 900.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:00:17', '2026-04-03 01:00:17'),
(152, 6, 419, NULL, 'Factura', NULL, NULL, 'TICKET-00000241', NULL, NULL, 350.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:00:48', '2026-04-03 01:00:48'),
(153, 6, 420, NULL, 'Factura', NULL, NULL, 'TICKET-00000242', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:02:20', '2026-04-03 01:02:20'),
(154, 6, 421, NULL, 'Factura', NULL, NULL, 'TICKET-00000243', NULL, NULL, 402.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:02:40', '2026-04-03 01:02:40'),
(155, 6, 422, NULL, 'Factura', NULL, NULL, 'TICKET-00000244', NULL, NULL, 344.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:03:02', '2026-04-03 01:03:02'),
(156, 6, 423, NULL, 'Factura', NULL, NULL, 'TICKET-00000245', NULL, NULL, 190.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:03:24', '2026-04-03 01:03:24'),
(157, 6, 424, NULL, 'Factura', NULL, NULL, 'TICKET-00000246', NULL, NULL, 1000.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:04:32', '2026-04-03 01:04:32'),
(158, 6, 425, NULL, 'Factura', NULL, NULL, 'TICKET-00000247', NULL, NULL, 650.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:04:57', '2026-04-03 01:04:57'),
(159, 6, 426, NULL, 'Factura', NULL, NULL, 'TICKET-00000248', NULL, NULL, 777.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:05:18', '2026-04-03 01:05:18'),
(160, 6, 427, NULL, 'Factura', NULL, NULL, 'TICKET-00000249', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:09:46', '2026-04-03 01:09:46'),
(161, 6, 428, NULL, 'Factura', NULL, NULL, 'TICKET-00000250', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:10:24', '2026-04-03 01:10:24'),
(162, 6, 429, NULL, 'Factura', NULL, NULL, 'TICKET-00000251', NULL, NULL, 110.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:14:35', '2026-04-03 01:14:35'),
(163, 6, 430, NULL, 'Factura', NULL, NULL, 'TICKET-00000252', NULL, NULL, 45.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:15:11', '2026-04-03 01:15:11'),
(164, 6, 431, NULL, 'Factura', NULL, NULL, 'TICKET-00000253', NULL, NULL, 100.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:15:50', '2026-04-03 01:15:50'),
(165, 6, 432, NULL, 'Factura', NULL, NULL, 'TICKET-00000254', NULL, NULL, 120.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:16:21', '2026-04-03 01:16:21'),
(166, 6, 433, NULL, 'Factura', NULL, NULL, 'TICKET-00000255', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:18:10', '2026-04-03 01:18:10'),
(167, 6, 434, NULL, 'Factura', NULL, NULL, 'TICKET-00000256', NULL, NULL, 15.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:18:40', '2026-04-03 01:18:40'),
(168, 6, 435, NULL, 'Factura', NULL, NULL, 'TICKET-00000257', NULL, NULL, 280.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:23:39', '2026-04-03 01:23:39'),
(169, 6, 436, NULL, 'Factura', NULL, NULL, 'TICKET-00000258', NULL, NULL, 140.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:24:16', '2026-04-03 01:24:16'),
(170, 6, 437, NULL, 'Factura', NULL, NULL, 'TICKET-00000259', NULL, NULL, 150.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:24:46', '2026-04-03 01:24:46'),
(171, 6, 438, NULL, 'Factura', NULL, NULL, 'TICKET-00000260', NULL, NULL, 500.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:25:14', '2026-04-03 01:25:14'),
(172, 6, 439, NULL, 'Factura', NULL, NULL, 'TICKET-00000261', NULL, NULL, 120.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:26:02', '2026-04-03 01:26:02'),
(173, 6, 440, NULL, 'Factura', NULL, NULL, 'TICKET-00000262', NULL, NULL, 36.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:28:50', '2026-04-03 01:28:50'),
(174, 6, 441, NULL, 'Factura', NULL, NULL, 'TICKET-00000263', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:29:58', '2026-04-03 01:29:58'),
(175, 6, 442, NULL, 'Factura', NULL, NULL, 'TICKET-00000264', NULL, NULL, 565.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:30:42', '2026-04-03 01:30:42'),
(176, 6, 443, NULL, 'Factura', NULL, NULL, 'TICKET-00000265', NULL, NULL, 94.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:32:09', '2026-04-03 01:32:09'),
(177, 6, 444, NULL, 'Factura', NULL, NULL, 'TICKET-00000266', NULL, NULL, 92.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:33:07', '2026-04-03 01:33:07'),
(178, 6, 445, NULL, 'Factura', NULL, NULL, 'TICKET-00000267', NULL, NULL, 190.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:34:52', '2026-04-03 01:34:52'),
(179, 6, 446, NULL, 'Factura', NULL, NULL, 'TICKET-00000268', NULL, NULL, 77.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:35:45', '2026-04-03 01:35:45'),
(180, 6, 447, NULL, 'Factura', NULL, NULL, 'TICKET-00000269', NULL, NULL, 69.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:37:06', '2026-04-03 01:37:06'),
(181, 6, 448, NULL, 'Factura', NULL, NULL, 'TICKET-00000270', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:37:30', '2026-04-03 01:37:30'),
(182, 7, 449, NULL, 'Factura', NULL, NULL, 'TICKET-00000271', NULL, NULL, 732.31, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:41:34', '2026-04-03 01:41:34'),
(183, 7, 450, NULL, 'Factura', NULL, NULL, 'TICKET-00000272', NULL, NULL, 600.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:41:58', '2026-04-03 01:41:58'),
(184, 7, 451, NULL, 'Factura', NULL, NULL, 'TICKET-00000273', NULL, NULL, 630.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:49:55', '2026-04-03 01:49:55'),
(185, 7, 452, NULL, 'Factura', NULL, NULL, 'TICKET-00000274', NULL, NULL, 444.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:50:37', '2026-04-03 01:50:37'),
(186, 7, 453, NULL, 'Factura', NULL, NULL, 'TICKET-00000275', NULL, NULL, 120.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:51:37', '2026-04-03 01:51:37'),
(187, 7, 454, NULL, 'Factura', NULL, NULL, 'TICKET-00000276', NULL, NULL, 60.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:52:51', '2026-04-03 01:52:51'),
(188, 7, 455, NULL, 'Factura', NULL, NULL, 'TICKET-00000277', NULL, NULL, 125.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:53:14', '2026-04-03 01:53:14'),
(189, 7, 456, NULL, 'Factura', NULL, NULL, 'TICKET-00000278', NULL, NULL, 86.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:54:02', '2026-04-03 01:54:02'),
(190, 7, 457, NULL, 'Factura', NULL, NULL, 'TICKET-00000279', NULL, NULL, 69.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:54:47', '2026-04-03 01:54:47'),
(191, 7, 458, NULL, 'Factura', NULL, NULL, 'TICKET-00000280', NULL, NULL, 62.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:55:19', '2026-04-03 01:55:19'),
(192, 7, 459, NULL, 'Factura', NULL, NULL, 'TICKET-00000281', NULL, NULL, 169.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:56:34', '2026-04-03 01:56:34'),
(193, 7, 460, NULL, 'Factura', NULL, NULL, 'TICKET-00000282', NULL, NULL, 98.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:57:38', '2026-04-03 01:57:38'),
(194, 7, 461, NULL, 'Factura', NULL, NULL, 'TICKET-00000283', NULL, NULL, 153.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:59:15', '2026-04-03 01:59:15'),
(195, 7, 462, NULL, 'Factura', NULL, NULL, 'TICKET-00000284', NULL, NULL, 49.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 01:59:47', '2026-04-03 01:59:47'),
(196, 7, 463, NULL, 'Factura', NULL, NULL, 'TICKET-00000285', NULL, NULL, 288.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 02:00:18', '2026-04-03 02:00:18'),
(197, 7, 464, NULL, 'Factura', NULL, NULL, 'TICKET-00000286', NULL, NULL, 132.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 02:00:57', '2026-04-03 02:00:57'),
(198, 6, 465, NULL, 'Factura', NULL, NULL, 'TICKET-00000287', NULL, NULL, 500.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 02:07:57', '2026-04-03 02:07:57'),
(199, 6, 466, NULL, 'Factura', NULL, NULL, 'TICKET-00000288', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:11:53', '2026-04-03 07:11:53'),
(200, 6, 467, NULL, 'Factura', NULL, NULL, 'TICKET-00000289', NULL, NULL, 80.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:12:14', '2026-04-03 07:12:14'),
(201, 6, 468, NULL, 'Factura', NULL, NULL, 'TICKET-00000290', NULL, NULL, 150.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:12:35', '2026-04-03 07:12:35'),
(202, 6, 469, NULL, 'Factura', NULL, NULL, 'TICKET-00000291', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:12:56', '2026-04-03 07:12:56'),
(203, 6, 470, NULL, 'Factura', NULL, NULL, 'TICKET-00000292', NULL, NULL, 111.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:13:15', '2026-04-03 07:13:15'),
(204, 6, 471, NULL, 'Factura', NULL, NULL, 'TICKET-00000293', NULL, NULL, 900.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:14:09', '2026-04-03 07:14:09'),
(205, 6, 472, NULL, 'Factura', NULL, NULL, 'TICKET-00000294', NULL, NULL, 700.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:14:38', '2026-04-03 07:14:38'),
(206, 6, 473, NULL, 'Factura', NULL, NULL, 'TICKET-00000295', NULL, NULL, 400.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:15:07', '2026-04-03 07:15:07'),
(207, 7, 474, NULL, 'Factura', NULL, NULL, 'TICKET-00000296', NULL, NULL, 80.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:17:16', '2026-04-03 07:17:16'),
(208, 7, 475, NULL, 'Factura', NULL, NULL, 'TICKET-00000297', NULL, NULL, 315.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 07:17:59', '2026-04-03 07:17:59'),
(209, 6, 476, NULL, 'Ticket', NULL, NULL, 'TICKET-00000298', NULL, 'CLIENTES VARIOS', 110.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:17:42', '2026-04-03 22:17:42'),
(210, 6, 477, NULL, 'Factura', NULL, NULL, 'TICKET-00000299', NULL, 'CLIENTES VARIOS', 150.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:18:46', '2026-04-03 22:18:46'),
(211, 6, 478, NULL, 'Factura', NULL, NULL, 'TICKET-00000300', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:19:02', '2026-04-03 22:19:02'),
(212, 6, 479, NULL, 'Factura', NULL, NULL, 'TICKET-00000301', NULL, 'CLIENTES VARIOS', 40.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:19:22', '2026-04-03 22:19:22'),
(213, 6, 480, NULL, 'Factura', NULL, NULL, 'TICKET-00000302', NULL, 'CLIENTES VARIOS', 180.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:22:25', '2026-04-03 22:22:25'),
(214, 6, 481, NULL, 'Factura', NULL, NULL, 'TICKET-00000303', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:23:04', '2026-04-03 22:23:04'),
(215, 6, 482, NULL, 'Factura', NULL, NULL, 'TICKET-00000304', NULL, 'CLIENTES VARIOS', 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:24:16', '2026-04-03 22:24:16'),
(216, 6, 483, NULL, 'Factura', NULL, NULL, 'TICKET-00000305', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:25:01', '2026-04-03 22:25:01'),
(217, 6, 484, NULL, 'Factura', NULL, NULL, 'TICKET-00000306', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:25:15', '2026-04-03 22:25:15'),
(218, 6, 485, NULL, 'Factura', NULL, NULL, 'TICKET-00000307', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:25:26', '2026-04-03 22:25:26'),
(219, 6, 486, NULL, 'Factura', NULL, NULL, 'TICKET-00000308', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:25:44', '2026-04-03 22:25:44'),
(220, 6, 487, NULL, 'Factura', NULL, NULL, 'TICKET-00000309', NULL, 'CLIENTES VARIOS', 25.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:26:00', '2026-04-03 22:26:00'),
(221, 6, 488, NULL, 'Factura', NULL, NULL, 'TICKET-00000310', NULL, 'CLIENTES VARIOS', 11.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:26:23', '2026-04-03 22:26:23'),
(222, 6, 489, NULL, 'Factura', NULL, NULL, 'TICKET-00000311', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:26:57', '2026-04-03 22:26:57'),
(223, 6, 490, NULL, 'Factura', NULL, NULL, 'TICKET-00000312', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:27:12', '2026-04-03 22:27:12'),
(224, 6, 491, NULL, 'Factura', NULL, NULL, 'TICKET-00000313', NULL, 'CLIENTES VARIOS', 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:27:30', '2026-04-03 22:27:30'),
(225, 6, 492, NULL, 'Factura', NULL, NULL, 'TICKET-00000314', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:27:47', '2026-04-03 22:27:47'),
(226, 6, 493, NULL, 'Factura', NULL, NULL, 'TICKET-00000315', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:28:04', '2026-04-03 22:28:04'),
(227, 6, 494, NULL, 'Factura', NULL, NULL, 'TICKET-00000316', NULL, 'CLIENTES VARIOS', 5.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:28:27', '2026-04-03 22:28:27'),
(228, 6, 495, NULL, 'Factura', NULL, NULL, 'TICKET-00000317', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:28:40', '2026-04-03 22:28:40'),
(229, 6, 496, NULL, 'Factura', NULL, NULL, 'TICKET-00000318', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:28:52', '2026-04-03 22:28:52'),
(230, 6, 497, NULL, 'Factura', NULL, NULL, 'TICKET-00000319', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:30:13', '2026-04-03 22:30:13'),
(231, 6, 498, NULL, 'Factura', NULL, NULL, 'TICKET-00000320', NULL, 'CLIENTES VARIOS', 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:30:33', '2026-04-03 22:30:33'),
(232, 6, 499, NULL, 'Factura', NULL, NULL, 'TICKET-00000321', NULL, 'CLIENTES VARIOS', 30.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:31:01', '2026-04-03 22:31:01'),
(233, 6, 500, NULL, 'Factura', NULL, NULL, 'TICKET-00000322', NULL, 'CLIENTES VARIOS', 5.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:32:22', '2026-04-03 22:32:22'),
(234, 6, 501, NULL, 'Factura', NULL, NULL, 'TICKET-00000323', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:32:39', '2026-04-03 22:32:39'),
(235, 6, 502, NULL, 'Factura', NULL, NULL, 'TICKET-00000324', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:32:51', '2026-04-03 22:32:51'),
(236, 6, 503, NULL, 'Factura', NULL, NULL, 'TICKET-00000325', NULL, 'CLIENTES VARIOS', 34.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:33:08', '2026-04-03 22:33:08'),
(237, 6, 504, NULL, 'Factura', NULL, NULL, 'TICKET-00000326', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:33:19', '2026-04-03 22:33:19'),
(238, 6, 505, NULL, 'Factura', NULL, NULL, 'TICKET-00000327', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:34:25', '2026-04-03 22:34:25'),
(239, 6, 506, NULL, 'Factura', NULL, NULL, 'TICKET-00000328', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:34:36', '2026-04-03 22:34:36'),
(240, 6, 507, NULL, 'Factura', NULL, NULL, 'TICKET-00000329', NULL, 'CLIENTES VARIOS', 30.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:34:48', '2026-04-03 22:34:48'),
(241, 6, 508, NULL, 'Factura', NULL, NULL, 'TICKET-00000330', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:35:21', '2026-04-03 22:35:21'),
(242, 6, 509, NULL, 'Factura', NULL, NULL, 'TICKET-00000331', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:35:32', '2026-04-03 22:35:32'),
(243, 6, 510, NULL, 'Factura', NULL, NULL, 'TICKET-00000332', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:35:47', '2026-04-03 22:35:47'),
(244, 6, 511, NULL, 'Factura', NULL, NULL, 'TICKET-00000333', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:36:33', '2026-04-03 22:36:33'),
(245, 6, 512, NULL, 'Factura', NULL, NULL, 'TICKET-00000334', NULL, 'CLIENTES VARIOS', 5.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:36:44', '2026-04-03 22:36:44'),
(246, 6, 513, NULL, 'Factura', NULL, NULL, 'TICKET-00000335', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:36:55', '2026-04-03 22:36:55'),
(247, 6, 514, NULL, 'Factura', NULL, NULL, 'TICKET-00000336', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:37:07', '2026-04-03 22:37:07'),
(248, 6, 515, NULL, 'Factura', NULL, NULL, 'TICKET-00000337', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:38:47', '2026-04-03 22:38:47'),
(249, 6, 516, NULL, 'Factura', NULL, NULL, 'TICKET-00000338', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:38:58', '2026-04-03 22:38:58'),
(250, 6, 517, NULL, 'Factura', NULL, NULL, 'TICKET-00000339', NULL, 'CLIENTES VARIOS', 14.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:39:11', '2026-04-03 22:39:11'),
(251, 6, 518, NULL, 'Factura', NULL, NULL, 'TICKET-00000340', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:39:22', '2026-04-03 22:39:22'),
(252, 6, 519, NULL, 'Factura', NULL, NULL, 'TICKET-00000341', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:39:53', '2026-04-03 22:39:53'),
(253, 6, 520, NULL, 'Factura', NULL, NULL, 'TICKET-00000342', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:40:05', '2026-04-03 22:40:05'),
(254, 6, 521, NULL, 'Factura', NULL, NULL, 'TICKET-00000343', NULL, 'CLIENTES VARIOS', 9.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:40:17', '2026-04-03 22:40:17'),
(255, 6, 522, NULL, 'Factura', NULL, NULL, 'TICKET-00000344', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:40:47', '2026-04-03 22:40:47'),
(256, 6, 523, NULL, 'Factura', NULL, NULL, 'TICKET-00000345', NULL, 'CLIENTES VARIOS', 18.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:41:00', '2026-04-03 22:41:00'),
(257, 6, 524, NULL, 'Factura', NULL, NULL, 'TICKET-00000346', NULL, 'CLIENTES VARIOS', 1550.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:43:50', '2026-04-03 22:43:50'),
(258, 6, 525, NULL, 'Ticket', NULL, NULL, '2329', NULL, 'TRANSPORTES Y SERVICIOS LUCELINA S.A.C.', 1332.00, 'pending', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:49:09', '2026-04-03 22:49:09'),
(259, 6, 526, NULL, 'Factura', NULL, NULL, 'TICKET-00000348', NULL, 'CLIENTES VARIOS', 80.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:50:35', '2026-04-03 22:50:35'),
(260, 6, 527, NULL, 'Factura', NULL, NULL, 'TICKET-00000349', NULL, 'CLIENTES VARIOS', 200.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:55:48', '2026-04-03 22:55:48'),
(261, 6, 528, NULL, 'Ticket', NULL, NULL, '2328', NULL, 'OCAÑA IZQUIERDO HECTOR EDGARDO', 1298.21, 'pending', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 22:59:50', '2026-04-03 22:59:50'),
(262, 6, 529, NULL, 'Factura', NULL, NULL, 'TICKET-00000351', NULL, 'CLIENTES VARIOS', 500.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:01:37', '2026-04-03 23:01:37'),
(263, 6, 530, NULL, 'Factura', NULL, NULL, 'TICKET-00000352', NULL, 'CLIENTES VARIOS', 137.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:20:39', '2026-04-03 23:20:39'),
(264, 7, 531, NULL, 'Ticket', NULL, NULL, 'TICKET-00000353', NULL, NULL, 150.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:29:17', '2026-04-03 23:29:17'),
(265, 7, 532, NULL, 'Factura', NULL, NULL, 'TICKET-00000354', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:29:40', '2026-04-03 23:29:40'),
(266, 7, 533, NULL, 'Factura', NULL, NULL, 'TICKET-00000355', NULL, NULL, 75.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:29:56', '2026-04-03 23:29:56'),
(267, 7, 534, NULL, 'Factura', NULL, NULL, 'TICKET-00000356', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:30:29', '2026-04-03 23:30:29'),
(268, 7, 535, NULL, 'Factura', NULL, NULL, 'TICKET-00000357', NULL, NULL, 40.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:30:49', '2026-04-03 23:30:49'),
(269, 7, 536, NULL, 'Factura', NULL, NULL, 'TICKET-00000358', NULL, NULL, 35.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:31:12', '2026-04-03 23:31:12'),
(270, 7, 537, NULL, 'Factura', NULL, NULL, 'TICKET-00000359', NULL, NULL, 217.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:31:39', '2026-04-03 23:31:39'),
(271, 7, 538, NULL, 'Factura', NULL, NULL, 'TICKET-00000360', NULL, NULL, 13.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:31:53', '2026-04-03 23:31:53'),
(272, 7, 539, NULL, 'Factura', NULL, NULL, 'TICKET-00000361', NULL, NULL, 30.10, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:32:12', '2026-04-03 23:32:12'),
(273, 7, 540, NULL, 'Factura', NULL, NULL, 'TICKET-00000362', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:32:32', '2026-04-03 23:32:32'),
(274, 7, 541, NULL, 'Factura', NULL, NULL, 'TICKET-00000363', NULL, NULL, 65.00, 'paid', 2, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:33:21', '2026-04-03 23:33:21'),
(275, 7, 542, NULL, 'Factura', NULL, NULL, 'TICKET-00000364', NULL, NULL, 75.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:34:43', '2026-04-03 23:34:43'),
(276, 7, 543, NULL, 'Factura', NULL, NULL, 'TICKET-00000365', NULL, NULL, 45.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:35:12', '2026-04-03 23:35:12'),
(277, 7, 544, NULL, 'Factura', NULL, NULL, 'TICKET-00000366', NULL, NULL, 16.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:35:37', '2026-04-03 23:35:37'),
(278, 7, 545, NULL, 'Factura', NULL, NULL, 'TICKET-00000367', NULL, NULL, 17.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:35:53', '2026-04-03 23:35:53'),
(279, 7, 546, NULL, 'Factura', NULL, NULL, 'TICKET-00000368', NULL, NULL, 80.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:36:39', '2026-04-03 23:36:39'),
(280, 7, 547, NULL, 'Factura', NULL, NULL, 'TICKET-00000369', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:37:47', '2026-04-03 23:37:47'),
(281, 7, 548, NULL, 'Factura', NULL, NULL, 'TICKET-00000370', NULL, NULL, 55.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:38:19', '2026-04-03 23:38:19'),
(282, 7, 549, NULL, 'Factura', NULL, NULL, 'TICKET-00000371', NULL, NULL, 32.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:38:57', '2026-04-03 23:38:57'),
(283, 7, 550, NULL, 'Factura', NULL, NULL, 'TICKET-00000372', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:39:15', '2026-04-03 23:39:15'),
(284, 7, 551, NULL, 'Factura', NULL, NULL, 'TICKET-00000373', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:39:29', '2026-04-03 23:39:29'),
(285, 7, 552, NULL, 'Factura', NULL, NULL, 'TICKET-00000374', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:39:48', '2026-04-03 23:39:48'),
(286, 7, 553, NULL, 'Factura', NULL, NULL, 'TICKET-00000375', NULL, NULL, 137.90, 'paid', 1, NULL, 0, '2026-04-03', NULL, '2026-04-03 23:40:11', '2026-04-03 23:40:11'),
(287, 6, 554, NULL, 'Factura', NULL, NULL, 'TICKET-00000376', NULL, 'CLIENTES VARIOS', 300.00, 'paid', 2, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:19:21', '2026-04-04 06:19:21'),
(288, 6, 555, NULL, 'Factura', NULL, NULL, 'TICKET-00000377', NULL, 'CLIENTES VARIOS', 600.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:19:34', '2026-04-04 06:19:34');
INSERT INTO `payments` (`id`, `user_id`, `sale_id`, `agreement_id`, `voucher_type`, `voucher_id`, `voucher_file`, `number`, `client_id`, `client_name`, `amount`, `status`, `payment_method_id`, `observation`, `deleted`, `date`, `photo_url`, `created_at`, `updated_at`) VALUES
(289, 6, 556, NULL, 'Factura', NULL, NULL, 'TICKET-00000378', NULL, 'CLIENTES VARIOS', 40.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:22:18', '2026-04-04 06:22:18'),
(290, 6, 557, NULL, 'Factura', NULL, NULL, 'TICKET-00000379', NULL, 'CLIENTES VARIOS', 200.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:22:48', '2026-04-04 06:22:48'),
(291, 6, 558, NULL, 'Factura', NULL, NULL, 'TICKET-00000380', NULL, 'CLIENTES VARIOS', 285.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:23:27', '2026-04-04 06:23:27'),
(292, 6, 559, NULL, 'Factura', NULL, NULL, 'TICKET-00000381', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:26:45', '2026-04-04 06:26:45'),
(293, 6, 560, NULL, 'Factura', NULL, NULL, 'TICKET-00000382', NULL, 'CLIENTES VARIOS', 14.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:26:59', '2026-04-04 06:26:59'),
(294, 6, 561, NULL, 'Factura', NULL, NULL, 'TICKET-00000383', NULL, 'CLIENTES VARIOS', 30.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:27:26', '2026-04-04 06:27:26'),
(295, 6, 562, NULL, 'Factura', NULL, NULL, 'TICKET-00000384', NULL, 'CLIENTES VARIOS', 30.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:27:45', '2026-04-04 06:27:45'),
(296, 6, 563, NULL, 'Factura', NULL, NULL, 'TICKET-00000385', NULL, 'CLIENTES VARIOS', 13.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:28:03', '2026-04-04 06:28:03'),
(297, 6, 564, NULL, 'Factura', NULL, NULL, 'TICKET-00000386', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:28:20', '2026-04-04 06:28:20'),
(298, 6, 565, NULL, 'Factura', NULL, NULL, 'TICKET-00000387', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:31:13', '2026-04-04 06:31:13'),
(299, 6, 566, NULL, 'Factura', NULL, NULL, 'TICKET-00000388', NULL, 'CLIENTES VARIOS', 35.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:31:33', '2026-04-04 06:31:33'),
(300, 6, 567, NULL, 'Factura', NULL, NULL, 'TICKET-00000389', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:41:47', '2026-04-04 06:41:47'),
(301, 7, 568, NULL, 'Factura', NULL, NULL, 'TICKET-00000390', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:57:06', '2026-04-04 06:57:06'),
(302, 7, 569, NULL, 'Factura', NULL, NULL, 'TICKET-00000391', NULL, NULL, 30.37, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:57:22', '2026-04-04 06:57:22'),
(303, 7, 570, NULL, 'Factura', NULL, NULL, 'TICKET-00000392', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:57:39', '2026-04-04 06:57:39'),
(304, 7, 571, NULL, 'Factura', NULL, NULL, 'TICKET-00000393', NULL, NULL, 20.00, 'paid', 2, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:58:19', '2026-04-04 06:58:19'),
(305, 7, 572, NULL, 'Factura', NULL, NULL, 'TICKET-00000394', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 06:58:32', '2026-04-04 06:58:32'),
(306, 6, 573, NULL, 'Factura', NULL, NULL, 'TICKET-00000395', NULL, 'CLIENTES VARIOS', 190.00, 'paid', 2, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:03:25', '2026-04-04 07:03:25'),
(307, 6, 574, NULL, 'Factura', NULL, NULL, 'TICKET-00000396', NULL, 'CLIENTES VARIOS', 40.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:03:45', '2026-04-04 07:03:45'),
(308, 7, 575, NULL, 'Factura', NULL, NULL, 'TICKET-00000397', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:04:20', '2026-04-04 07:04:20'),
(309, 7, 576, NULL, 'Factura', NULL, NULL, 'TICKET-00000398', NULL, NULL, 15.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:04:34', '2026-04-04 07:04:34'),
(310, 7, 577, NULL, 'Factura', NULL, NULL, 'TICKET-00000399', NULL, NULL, 65.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:04:52', '2026-04-04 07:04:52'),
(311, 7, 578, NULL, 'Factura', NULL, NULL, 'TICKET-00000400', NULL, NULL, 38.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:05:15', '2026-04-04 07:05:15'),
(312, 7, 579, NULL, 'Factura', NULL, NULL, 'TICKET-00000401', NULL, NULL, 252.00, 'paid', 2, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:06:53', '2026-04-04 07:06:53'),
(313, 6, 580, NULL, 'Factura', NULL, NULL, 'TICKET-00000402', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:07:02', '2026-04-04 07:07:02'),
(314, 6, 581, NULL, 'Factura', NULL, NULL, 'TICKET-00000403', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:07:17', '2026-04-04 07:07:17'),
(315, 7, 582, NULL, 'Factura', NULL, NULL, 'TICKET-00000404', NULL, NULL, 78.00, 'paid', 2, NULL, 0, '2026-04-04', NULL, '2026-04-04 07:07:20', '2026-04-04 07:07:20'),
(316, 6, 583, NULL, 'Factura', NULL, NULL, 'TICKET-00000405', NULL, NULL, 58.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:20:21', '2026-04-04 23:20:21'),
(317, 6, 584, NULL, 'Factura', NULL, NULL, 'TICKET-00000406', NULL, NULL, 100.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:21:08', '2026-04-04 23:21:08'),
(318, 6, 585, NULL, 'Factura', NULL, NULL, 'TICKET-00000407', NULL, NULL, 140.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:22:51', '2026-04-04 23:22:51'),
(319, 6, 586, NULL, 'Factura', NULL, NULL, 'TICKET-00000408', NULL, NULL, 200.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:24:10', '2026-04-04 23:24:10'),
(320, 6, 587, NULL, 'Factura', NULL, NULL, 'TICKET-00000409', NULL, NULL, 72.40, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:28:51', '2026-04-04 23:28:51'),
(321, 6, 588, NULL, 'Factura', NULL, NULL, 'TICKET-00000410', NULL, NULL, 25.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:30:14', '2026-04-04 23:30:14'),
(322, 6, 589, NULL, 'Factura', NULL, NULL, 'TICKET-00000411', NULL, NULL, 95.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:53:13', '2026-04-04 23:53:13'),
(323, 6, 590, NULL, 'Factura', NULL, NULL, 'TICKET-00000412', NULL, NULL, 288.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:54:00', '2026-04-04 23:54:00'),
(324, 6, 591, NULL, 'Factura', NULL, NULL, 'TICKET-00000413', NULL, NULL, 120.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:55:34', '2026-04-04 23:55:34'),
(325, 6, 592, NULL, 'Factura', NULL, NULL, 'TICKET-00000414', NULL, NULL, 230.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:56:00', '2026-04-04 23:56:00'),
(326, 6, 593, NULL, 'Factura', NULL, NULL, 'TICKET-00000415', NULL, NULL, 290.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:56:40', '2026-04-04 23:56:40'),
(327, 6, 594, NULL, 'Factura', NULL, NULL, 'TICKET-00000416', NULL, NULL, 45.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:57:05', '2026-04-04 23:57:05'),
(328, 6, 595, NULL, 'Factura', NULL, NULL, 'TICKET-00000417', NULL, NULL, 33.00, 'paid', 1, NULL, 0, '2026-04-04', NULL, '2026-04-04 23:59:51', '2026-04-04 23:59:51'),
(329, 6, 596, NULL, 'Factura', NULL, NULL, 'TICKET-00000418', NULL, NULL, 100.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:00:23', '2026-04-05 00:00:23'),
(330, 6, 597, NULL, 'Factura', NULL, NULL, 'TICKET-00000419', NULL, NULL, 66.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:01:33', '2026-04-05 00:01:33'),
(331, 6, 598, NULL, 'Factura', NULL, NULL, 'TICKET-00000420', NULL, NULL, 37.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:02:10', '2026-04-05 00:02:10'),
(332, 6, 599, NULL, 'Factura', NULL, NULL, 'TICKET-00000421', NULL, NULL, 70.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:03:04', '2026-04-05 00:03:04'),
(333, 6, 600, NULL, 'Factura', NULL, NULL, 'TICKET-00000422', NULL, NULL, 25.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:03:58', '2026-04-05 00:03:58'),
(334, 6, 601, NULL, 'Factura', NULL, NULL, 'TICKET-00000423', NULL, NULL, 48.50, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:04:29', '2026-04-05 00:04:29'),
(335, 6, 602, NULL, 'Factura', NULL, NULL, 'TICKET-00000424', NULL, NULL, 50.10, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:05:27', '2026-04-05 00:05:27'),
(336, 6, 603, NULL, 'Factura', NULL, NULL, 'TICKET-00000425', NULL, NULL, 35.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:06:51', '2026-04-05 00:06:51'),
(337, 6, 604, NULL, 'Factura', NULL, NULL, 'TICKET-00000426', NULL, NULL, 42.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:07:25', '2026-04-05 00:07:25'),
(338, 6, 605, NULL, 'Factura', NULL, NULL, 'TICKET-00000427', NULL, NULL, 115.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:08:19', '2026-04-05 00:08:19'),
(339, 6, 606, NULL, 'Factura', NULL, NULL, 'TICKET-00000428', NULL, NULL, 220.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:08:50', '2026-04-05 00:08:50'),
(340, 6, 607, NULL, 'Factura', NULL, NULL, 'TICKET-00000429', NULL, NULL, 22.50, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:13:55', '2026-04-05 00:13:55'),
(341, 6, 608, NULL, 'Factura', NULL, NULL, 'TICKET-00000430', NULL, NULL, 45.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:14:33', '2026-04-05 00:14:33'),
(342, 6, 609, NULL, 'Factura', NULL, NULL, 'TICKET-00000431', NULL, NULL, 55.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:17:20', '2026-04-05 00:17:20'),
(343, 6, 610, NULL, 'Factura', NULL, NULL, 'TICKET-00000432', NULL, NULL, 266.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:17:50', '2026-04-05 00:17:50'),
(344, 6, 611, NULL, 'Factura', NULL, NULL, 'TICKET-00000433', NULL, NULL, 300.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:43:21', '2026-04-05 00:43:21'),
(345, 6, 612, NULL, 'Factura', NULL, NULL, 'TICKET-00000434', NULL, NULL, 840.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:43:58', '2026-04-05 00:43:58'),
(346, 6, 613, NULL, 'Ticket', NULL, NULL, '2326', NULL, 'LUZ ANGELICA', 2184.88, 'pending', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:47:51', '2026-04-05 00:47:51'),
(347, 6, 614, NULL, 'Ticket', NULL, NULL, '2327', NULL, 'VALERIA', 2886.00, 'pending', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:49:23', '2026-04-05 00:49:23'),
(348, 7, 615, NULL, 'Ticket', NULL, NULL, 'TICKET-00000437', NULL, NULL, 88.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:58:08', '2026-04-05 00:58:08'),
(349, 7, 616, NULL, 'Factura', NULL, NULL, 'TICKET-00000438', NULL, NULL, 160.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 00:58:50', '2026-04-05 00:58:50'),
(350, 7, 617, NULL, 'Factura', NULL, NULL, 'TICKET-00000439', NULL, NULL, 80.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 01:00:12', '2026-04-05 01:00:12'),
(351, 7, 618, NULL, 'Factura', NULL, NULL, 'TICKET-00000440', NULL, NULL, 60.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 01:01:00', '2026-04-05 01:01:00'),
(352, 7, 619, NULL, 'Factura', NULL, NULL, 'TICKET-00000441', NULL, NULL, 70.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 01:01:31', '2026-04-05 01:01:31'),
(353, 7, 620, NULL, 'Factura', NULL, NULL, 'TICKET-00000442', NULL, NULL, 150.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 01:02:06', '2026-04-05 01:02:06'),
(354, 7, 621, NULL, 'Factura', NULL, NULL, 'TICKET-00000443', NULL, NULL, 250.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 01:02:54', '2026-04-05 01:02:54'),
(355, 7, 622, NULL, 'Factura', NULL, NULL, 'TICKET-00000444', NULL, NULL, 56.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 01:03:39', '2026-04-05 01:03:39'),
(356, 7, 623, NULL, 'Factura', NULL, NULL, 'TICKET-00000445', NULL, NULL, 35.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 01:04:29', '2026-04-05 01:04:29'),
(357, 7, 624, NULL, 'Factura', NULL, NULL, 'TICKET-00000446', NULL, NULL, 32.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:06:15', '2026-04-05 07:06:15'),
(358, 7, 625, NULL, 'Factura', NULL, NULL, 'TICKET-00000447', NULL, NULL, 120.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:07:10', '2026-04-05 07:07:10'),
(359, 6, 626, NULL, 'Factura', NULL, NULL, 'TICKET-00000448', NULL, NULL, 60.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:10:22', '2026-04-05 07:10:22'),
(360, 6, 627, NULL, 'Factura', NULL, NULL, 'TICKET-00000449', NULL, NULL, 130.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:10:56', '2026-04-05 07:10:56'),
(361, 6, 628, NULL, 'Factura', NULL, NULL, 'TICKET-00000450', NULL, NULL, 140.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:11:29', '2026-04-05 07:11:29'),
(362, 6, 629, NULL, 'Factura', NULL, NULL, 'TICKET-00000451', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:11:55', '2026-04-05 07:11:55'),
(363, 6, 630, NULL, 'Factura', NULL, NULL, 'TICKET-00000452', NULL, NULL, 555.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:12:44', '2026-04-05 07:12:44'),
(364, 6, 631, NULL, 'Factura', NULL, NULL, 'TICKET-00000453', NULL, NULL, 400.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:12:59', '2026-04-05 07:12:59'),
(365, 6, 632, NULL, 'Ticket', NULL, NULL, '2330', NULL, 'valeria', 2220.02, 'pending', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:14:59', '2026-04-05 07:14:59'),
(366, 7, 633, NULL, 'Factura', NULL, NULL, 'TICKET-00000455', NULL, NULL, 65.00, 'paid', 3, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:43:01', '2026-04-05 07:43:01'),
(367, 7, 634, NULL, 'Factura', NULL, NULL, 'TICKET-00000456', NULL, NULL, 183.00, 'paid', 3, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:45:29', '2026-04-05 07:45:29'),
(368, 6, 635, NULL, 'Factura', NULL, NULL, 'TICKET-00000457', NULL, NULL, 186.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:48:56', '2026-04-05 07:48:56'),
(369, 6, 636, NULL, 'Factura', NULL, NULL, 'TICKET-00000458', NULL, NULL, 65.00, 'paid', 3, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:49:45', '2026-04-05 07:49:45'),
(370, 6, 637, NULL, 'Factura', NULL, NULL, 'TICKET-00000459', NULL, NULL, 170.00, 'paid', 3, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:50:47', '2026-04-05 07:50:47'),
(371, 6, 638, NULL, 'Factura', NULL, NULL, 'TICKET-00000460', NULL, NULL, 82.00, 'paid', 3, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:51:42', '2026-04-05 07:51:42'),
(372, 6, 639, NULL, 'Factura', NULL, NULL, 'TICKET-00000461', NULL, NULL, 40.00, 'paid', 3, NULL, 0, '2026-04-05', NULL, '2026-04-05 07:52:41', '2026-04-05 07:52:41'),
(373, 7, 640, NULL, 'Ticket', NULL, NULL, 'TICKET-00000462', NULL, 'CLIENTES VARIOS', 14.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:42:20', '2026-04-05 22:42:20'),
(374, 7, 641, NULL, 'Factura', NULL, NULL, 'TICKET-00000463', NULL, 'CLIENTES VARIOS', 15.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:42:41', '2026-04-05 22:42:41'),
(375, 7, 642, NULL, 'Factura', NULL, NULL, 'TICKET-00000464', NULL, 'CLIENTES VARIOS', 680.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:44:21', '2026-04-05 22:44:21'),
(376, 7, 643, NULL, 'Factura', NULL, NULL, 'TICKET-00000465', NULL, 'CLIENTES VARIOS', 200.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:44:35', '2026-04-05 22:44:35'),
(377, 7, 644, NULL, 'Factura', NULL, NULL, 'TICKET-00000466', NULL, 'CLIENTES VARIOS', 80.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:45:47', '2026-04-05 22:45:47'),
(378, 7, 645, NULL, 'Factura', NULL, NULL, 'TICKET-00000467', NULL, 'CLIENTES VARIOS', 120.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:46:56', '2026-04-05 22:46:56'),
(379, 7, 646, NULL, 'Factura', NULL, NULL, 'TICKET-00000468', NULL, 'CLIENTES VARIOS', 100.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:47:10', '2026-04-05 22:47:10'),
(380, 7, 647, NULL, 'Factura', NULL, NULL, 'TICKET-00000469', NULL, 'CLIENTES VARIOS', 111.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:49:47', '2026-04-05 22:49:47'),
(381, 7, 648, NULL, 'Factura', NULL, NULL, 'TICKET-00000470', NULL, 'CLIENTES VARIOS', 120.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:51:09', '2026-04-05 22:51:09'),
(382, 7, 649, NULL, 'Factura', NULL, NULL, 'TICKET-00000471', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:52:34', '2026-04-05 22:52:34'),
(383, 7, 650, NULL, 'Factura', NULL, NULL, 'TICKET-00000472', NULL, NULL, 120.00, 'paid', 2, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:55:39', '2026-04-05 22:55:39'),
(384, 7, 651, NULL, 'Factura', NULL, NULL, 'TICKET-00000473', NULL, 'CLIENTES VARIOS', 100.00, 'paid', 2, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:56:13', '2026-04-05 22:56:13'),
(385, 7, 652, NULL, 'Factura', NULL, NULL, 'TICKET-00000474', NULL, 'CLIENTES VARIOS', 50.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:56:38', '2026-04-05 22:56:38'),
(386, 7, 653, NULL, 'Factura', NULL, NULL, 'TICKET-00000475', NULL, 'CLIENTES VARIOS', 27.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:57:02', '2026-04-05 22:57:02'),
(387, 7, 654, NULL, 'Factura', NULL, NULL, 'TICKET-00000476', NULL, 'CLIENTES VARIOS', 30.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:58:19', '2026-04-05 22:58:19'),
(388, 7, 655, NULL, 'Factura', NULL, NULL, 'TICKET-00000477', NULL, 'CLIENTES VARIOS', 150.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:58:40', '2026-04-05 22:58:40'),
(389, 7, 656, NULL, 'Factura', NULL, NULL, 'TICKET-00000478', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:59:13', '2026-04-05 22:59:13'),
(390, 7, 657, NULL, 'Factura', NULL, NULL, 'TICKET-00000479', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:59:26', '2026-04-05 22:59:26'),
(391, 7, 658, NULL, 'Factura', NULL, NULL, 'TICKET-00000480', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:59:39', '2026-04-05 22:59:39'),
(392, 7, 659, NULL, 'Factura', NULL, NULL, 'TICKET-00000481', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 22:59:52', '2026-04-05 22:59:52'),
(393, 7, 660, NULL, 'Factura', NULL, NULL, 'TICKET-00000482', NULL, 'CLIENTES VARIOS', 230.00, 'paid', 2, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:01:34', '2026-04-05 23:01:34'),
(394, 7, 661, NULL, 'Factura', NULL, NULL, 'TICKET-00000483', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:01:53', '2026-04-05 23:01:53'),
(395, 7, 662, NULL, 'Factura', NULL, NULL, 'TICKET-00000484', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:02:07', '2026-04-05 23:02:07'),
(396, 7, 663, NULL, 'Factura', NULL, NULL, 'TICKET-00000485', NULL, NULL, 19.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:02:31', '2026-04-05 23:02:31'),
(397, 7, 664, NULL, 'Factura', NULL, NULL, 'TICKET-00000486', NULL, NULL, 11.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:02:44', '2026-04-05 23:02:44'),
(398, 7, 665, NULL, 'Factura', NULL, NULL, 'TICKET-00000487', NULL, NULL, 19.50, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:03:23', '2026-04-05 23:03:23'),
(399, 7, 666, NULL, 'Factura', NULL, NULL, 'TICKET-00000488', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:03:48', '2026-04-05 23:03:48'),
(400, 7, 667, NULL, 'Factura', NULL, NULL, 'TICKET-00000489', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:04:03', '2026-04-05 23:04:03'),
(401, 7, 668, NULL, 'Factura', NULL, NULL, 'TICKET-00000490', NULL, NULL, 60.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:04:25', '2026-04-05 23:04:25'),
(402, 7, 669, NULL, 'Factura', NULL, NULL, 'TICKET-00000491', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:07:43', '2026-04-05 23:07:43'),
(403, 7, 670, NULL, 'Factura', NULL, NULL, 'TICKET-00000492', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:08:04', '2026-04-05 23:08:04'),
(404, 7, 671, NULL, 'Factura', NULL, NULL, 'TICKET-00000493', NULL, NULL, 35.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:08:23', '2026-04-05 23:08:23'),
(405, 7, 672, NULL, 'Factura', NULL, NULL, 'TICKET-00000494', NULL, NULL, 5.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:08:40', '2026-04-05 23:08:40'),
(406, 7, 673, NULL, 'Factura', NULL, NULL, 'TICKET-00000495', NULL, NULL, 15.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:08:55', '2026-04-05 23:08:55'),
(407, 7, 674, NULL, 'Factura', NULL, NULL, 'TICKET-00000496', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:09:09', '2026-04-05 23:09:09'),
(408, 7, 675, NULL, 'Factura', NULL, NULL, 'TICKET-00000497', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:09:38', '2026-04-05 23:09:38'),
(409, 7, 676, NULL, 'Factura', NULL, NULL, 'TICKET-00000498', NULL, NULL, 45.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:09:49', '2026-04-05 23:09:49'),
(410, 7, 677, NULL, 'Factura', NULL, NULL, 'TICKET-00000499', NULL, NULL, 300.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:10:59', '2026-04-05 23:10:59'),
(411, 7, 678, NULL, 'Factura', NULL, NULL, 'TICKET-00000500', NULL, NULL, 300.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:11:15', '2026-04-05 23:11:15'),
(412, 7, 679, NULL, 'Factura', NULL, NULL, 'TICKET-00000501', NULL, NULL, 202.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:11:38', '2026-04-05 23:11:38'),
(413, 7, 680, NULL, 'Factura', NULL, NULL, 'TICKET-00000502', NULL, NULL, 90.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:11:54', '2026-04-05 23:11:54'),
(414, 7, 681, NULL, 'Factura', NULL, NULL, 'TICKET-00000503', NULL, NULL, 110.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:12:11', '2026-04-05 23:12:11'),
(415, 7, 682, NULL, 'Factura', NULL, NULL, 'TICKET-00000504', NULL, NULL, 271.00, 'paid', 2, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:12:48', '2026-04-05 23:12:48'),
(416, 7, 683, NULL, 'Factura', NULL, NULL, 'TICKET-00000505', NULL, NULL, 29.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:13:24', '2026-04-05 23:13:24'),
(417, 6, 684, NULL, 'Ticket', NULL, NULL, 'TICKET-00000506', NULL, NULL, 800.00, 'paid', 2, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:42:34', '2026-04-05 23:42:34'),
(418, 6, 685, NULL, 'Factura', NULL, NULL, 'TICKET-00000507', NULL, NULL, 710.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:42:49', '2026-04-05 23:42:49'),
(419, 6, 686, NULL, 'Factura', NULL, NULL, 'TICKET-00000508', NULL, NULL, 770.00, 'paid', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:45:23', '2026-04-05 23:45:23'),
(420, 6, 687, NULL, 'Ticket', NULL, NULL, '2331', NULL, 'AGUILA BERMEO DIMAR', 888.00, 'pending', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:46:37', '2026-04-05 23:46:37'),
(421, 6, 688, NULL, 'Factura', NULL, NULL, 'TICKET-00000510', NULL, NULL, 700.00, 'paid', 2, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:47:56', '2026-04-05 23:47:56'),
(422, 6, 689, NULL, 'Ticket', NULL, NULL, '2332', NULL, 'FERRETERIA CASA FUERTE E.I.R.L.', 2500.00, 'pending', 1, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:51:05', '2026-04-05 23:51:05'),
(423, 6, 694, NULL, 'Factura', NULL, NULL, 'TICKET-00000512', NULL, NULL, 85.00, 'paid', 2, NULL, 0, '2026-04-05', NULL, '2026-04-05 23:53:23', '2026-04-05 23:53:23'),
(424, 6, 699, NULL, 'Ticket', NULL, NULL, 'TICKET-00000513', NULL, 'CLIENTES VARIOS', 32.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:00:38', '2026-04-06 00:00:38'),
(425, 6, 702, NULL, 'Factura', NULL, NULL, 'TICKET-00000514', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:04:23', '2026-04-06 00:04:23'),
(426, 6, 706, NULL, 'Factura', NULL, NULL, 'TICKET-00000515', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:07:10', '2026-04-06 00:07:10'),
(427, 6, 707, NULL, 'Factura', NULL, NULL, 'TICKET-00000516', NULL, NULL, 100.00, 'paid', 2, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:07:31', '2026-04-06 00:07:31'),
(428, 6, 708, NULL, 'Factura', NULL, NULL, 'TICKET-00000517', NULL, NULL, 94.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:08:08', '2026-04-06 00:08:08'),
(429, 6, 709, NULL, 'Factura', NULL, NULL, 'TICKET-00000518', NULL, NULL, 100.00, 'paid', 2, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:08:30', '2026-04-06 00:08:30'),
(430, 6, 710, NULL, 'Factura', NULL, NULL, 'TICKET-00000519', NULL, NULL, 28.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:09:32', '2026-04-06 00:09:32'),
(431, 6, 711, NULL, 'Factura', NULL, NULL, 'TICKET-00000520', NULL, NULL, 33.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:09:55', '2026-04-06 00:09:55'),
(432, 6, 712, NULL, 'Factura', NULL, NULL, 'TICKET-00000521', NULL, NULL, 50.00, 'paid', 2, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:10:18', '2026-04-06 00:10:18'),
(433, 6, 713, NULL, 'Factura', NULL, NULL, 'TICKET-00000522', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:10:50', '2026-04-06 00:10:50'),
(434, 6, 714, NULL, 'Factura', NULL, NULL, 'TICKET-00000523', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:11:06', '2026-04-06 00:11:06'),
(435, 6, 715, NULL, 'Factura', NULL, NULL, 'TICKET-00000524', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:11:24', '2026-04-06 00:11:24'),
(436, 6, 716, NULL, 'Factura', NULL, NULL, 'TICKET-00000525', NULL, NULL, 25.13, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:11:44', '2026-04-06 00:11:44'),
(437, 6, 717, NULL, 'Factura', NULL, NULL, 'TICKET-00000526', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:12:14', '2026-04-06 00:12:14'),
(438, 6, 718, NULL, 'Factura', NULL, NULL, 'TICKET-00000527', NULL, NULL, 42.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:12:30', '2026-04-06 00:12:30'),
(439, 6, 719, NULL, 'Factura', NULL, NULL, 'TICKET-00000528', NULL, NULL, 35.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:13:07', '2026-04-06 00:13:07'),
(440, 6, 720, NULL, 'Factura', NULL, NULL, 'TICKET-00000529', NULL, NULL, 44.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:13:25', '2026-04-06 00:13:25'),
(441, 6, 721, NULL, 'Factura', NULL, NULL, 'TICKET-00000530', NULL, NULL, 140.00, 'paid', 2, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:15:22', '2026-04-06 00:15:22'),
(442, 6, 722, NULL, 'Factura', NULL, NULL, 'TICKET-00000531', NULL, NULL, 80.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:15:50', '2026-04-06 00:15:50'),
(443, 6, 724, NULL, 'Factura', NULL, NULL, 'TICKET-00000532', NULL, NULL, 55.00, 'paid', 4, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:17:02', '2026-04-06 00:17:02'),
(444, 6, 725, NULL, 'Factura', NULL, NULL, 'TICKET-00000533', NULL, NULL, 35.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:17:23', '2026-04-06 00:17:23'),
(445, 6, 726, NULL, 'Factura', NULL, NULL, 'TICKET-00000534', NULL, NULL, 35.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:17:42', '2026-04-06 00:17:42'),
(446, 6, 727, NULL, 'Factura', NULL, NULL, 'TICKET-00000535', NULL, NULL, 120.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:18:07', '2026-04-06 00:18:07'),
(447, 6, 728, NULL, 'Factura', NULL, NULL, 'TICKET-00000536', NULL, NULL, 70.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:18:24', '2026-04-06 00:18:24'),
(448, 6, 729, NULL, 'Factura', NULL, NULL, 'TICKET-00000537', NULL, NULL, 30.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:18:48', '2026-04-06 00:18:48'),
(449, 6, 730, NULL, 'Factura', NULL, NULL, 'TICKET-00000538', NULL, NULL, 64.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:21:16', '2026-04-06 00:21:16'),
(450, 6, 731, NULL, 'Factura', NULL, NULL, 'TICKET-00000539', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:21:32', '2026-04-06 00:21:32'),
(451, 6, 732, NULL, 'Factura', NULL, NULL, 'TICKET-00000540', NULL, NULL, 25.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:22:01', '2026-04-06 00:22:01'),
(452, 6, 733, NULL, 'Factura', NULL, NULL, 'TICKET-00000541', NULL, NULL, 51.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:22:18', '2026-04-06 00:22:18'),
(453, 6, 734, NULL, 'Factura', NULL, NULL, 'TICKET-00000542', NULL, NULL, 56.80, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:22:41', '2026-04-06 00:22:41'),
(454, 6, 735, NULL, 'Factura', NULL, NULL, 'TICKET-00000543', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 00:23:10', '2026-04-06 00:23:10'),
(455, 7, 739, NULL, 'Factura', NULL, NULL, 'TICKET-00000544', NULL, 'CLIENTES VARIOS', 20.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 06:10:46', '2026-04-06 06:10:46'),
(456, 7, 740, NULL, 'Factura', NULL, NULL, 'TICKET-00000545', NULL, NULL, 13.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 06:11:46', '2026-04-06 06:11:46'),
(457, 7, 741, NULL, 'Factura', NULL, NULL, 'TICKET-00000546', NULL, 'CLIENTES VARIOS', 10.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 06:12:33', '2026-04-06 06:12:33'),
(458, 7, 742, NULL, 'Factura', NULL, NULL, 'TICKET-00000547', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 06:13:04', '2026-04-06 06:13:04'),
(459, 7, 743, NULL, 'Factura', NULL, NULL, 'TICKET-00000548', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 06:13:23', '2026-04-06 06:13:23'),
(460, 7, 744, NULL, 'Factura', NULL, NULL, 'TICKET-00000549', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 06:14:28', '2026-04-06 06:14:28'),
(461, 7, 745, NULL, 'Factura', NULL, NULL, 'TICKET-00000550', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 06:14:48', '2026-04-06 06:14:48'),
(462, 6, 746, NULL, 'Factura', NULL, NULL, 'TICKET-00000551', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 07:02:45', '2026-04-06 07:02:45'),
(463, 6, 747, NULL, 'Factura', NULL, NULL, 'TICKET-00000552', NULL, NULL, 26.00, 'paid', 1, NULL, 0, '2026-04-06', NULL, '2026-04-06 07:02:59', '2026-04-06 07:02:59'),
(464, 6, 753, NULL, 'Ticket', NULL, NULL, 'TICKET-00000553', NULL, NULL, 32.00, 'paid', 3, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:02:36', '2026-04-07 06:02:36'),
(465, 6, 754, NULL, 'Factura', NULL, NULL, 'TICKET-00000554', NULL, NULL, 95.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:03:30', '2026-04-07 06:03:30'),
(466, 6, 755, NULL, 'Factura', NULL, NULL, 'TICKET-00000555', NULL, NULL, 67.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:03:57', '2026-04-07 06:03:57'),
(467, 6, 756, NULL, 'Factura', NULL, NULL, 'TICKET-00000556', NULL, NULL, 25.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:04:32', '2026-04-07 06:04:32'),
(468, 6, 757, NULL, 'Factura', NULL, NULL, 'TICKET-00000557', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:05:25', '2026-04-07 06:05:25'),
(469, 6, 758, NULL, 'Factura', NULL, NULL, 'TICKET-00000558', NULL, NULL, 95.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:06:17', '2026-04-07 06:06:17'),
(470, 6, 759, NULL, 'Factura', NULL, NULL, 'TICKET-00000559', NULL, NULL, 69.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:06:50', '2026-04-07 06:06:50'),
(471, 6, 760, NULL, 'Factura', NULL, NULL, 'TICKET-00000560', NULL, NULL, 40.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:07:21', '2026-04-07 06:07:21'),
(472, 6, 761, NULL, 'Factura', NULL, NULL, 'TICKET-00000561', NULL, NULL, 40.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:07:58', '2026-04-07 06:07:58'),
(473, 6, 762, NULL, 'Factura', NULL, NULL, 'TICKET-00000562', NULL, NULL, 173.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:09:41', '2026-04-07 06:09:41'),
(474, 6, 763, NULL, 'Factura', NULL, NULL, 'TICKET-00000563', NULL, NULL, 92.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:10:15', '2026-04-07 06:10:15'),
(475, 6, 764, NULL, 'Factura', NULL, NULL, 'TICKET-00000564', NULL, NULL, 150.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:10:53', '2026-04-07 06:10:53'),
(476, 6, 765, NULL, 'Factura', NULL, NULL, 'TICKET-00000565', NULL, NULL, 155.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:11:24', '2026-04-07 06:11:24'),
(477, 6, 766, NULL, 'Factura', NULL, NULL, 'TICKET-00000566', NULL, NULL, 45.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:12:34', '2026-04-07 06:12:34'),
(478, 6, 767, NULL, 'Factura', NULL, NULL, 'TICKET-00000567', NULL, NULL, 170.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:12:56', '2026-04-07 06:12:56'),
(479, 6, 768, NULL, 'Factura', NULL, NULL, 'TICKET-00000568', NULL, NULL, 70.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:14:10', '2026-04-07 06:14:10'),
(480, 6, 769, NULL, 'Factura', NULL, NULL, 'TICKET-00000569', NULL, NULL, 92.50, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:14:51', '2026-04-07 06:14:51'),
(481, 6, 770, NULL, 'Factura', NULL, NULL, 'TICKET-00000570', NULL, NULL, 60.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:16:22', '2026-04-07 06:16:22'),
(482, 6, 771, NULL, 'Factura', NULL, NULL, 'TICKET-00000571', NULL, NULL, 238.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:16:45', '2026-04-07 06:16:45'),
(483, 6, 772, NULL, 'Factura', NULL, NULL, 'TICKET-00000572', NULL, NULL, 50.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:17:04', '2026-04-07 06:17:04'),
(484, 6, 773, NULL, 'Factura', NULL, NULL, 'TICKET-00000573', NULL, NULL, 90.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:17:24', '2026-04-07 06:17:24'),
(485, 6, 774, NULL, 'Factura', NULL, NULL, 'TICKET-00000574', NULL, NULL, 10.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:17:51', '2026-04-07 06:17:51'),
(486, 6, 775, NULL, 'Factura', NULL, NULL, 'TICKET-00000575', NULL, NULL, 230.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:25:28', '2026-04-07 06:25:28'),
(487, 6, 776, NULL, 'Factura', NULL, NULL, 'TICKET-00000576', NULL, NULL, 1304.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:25:48', '2026-04-07 06:25:48'),
(488, 6, 777, NULL, 'Factura', NULL, NULL, 'TICKET-00000577', NULL, NULL, 500.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:26:17', '2026-04-07 06:26:17'),
(489, 6, 778, NULL, 'Factura', NULL, NULL, 'TICKET-00000578', NULL, NULL, 292.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:26:40', '2026-04-07 06:26:40'),
(490, 6, 779, NULL, 'Factura', NULL, NULL, 'TICKET-00000579', NULL, NULL, 1110.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 06:32:44', '2026-04-07 06:32:44'),
(491, 6, 780, NULL, 'Factura', NULL, NULL, 'TICKET-00000580', NULL, NULL, 50.00, 'paid', 3, NULL, 0, '2026-04-07', NULL, '2026-04-07 07:09:16', '2026-04-07 07:09:16'),
(492, 6, 781, NULL, 'Factura', NULL, NULL, 'TICKET-00000581', NULL, NULL, 108.00, 'paid', 3, NULL, 0, '2026-04-07', NULL, '2026-04-07 07:10:03', '2026-04-07 07:10:03'),
(493, 6, 782, NULL, 'Factura', NULL, NULL, 'TICKET-00000582', NULL, NULL, 50.00, 'paid', 3, NULL, 0, '2026-04-07', NULL, '2026-04-07 07:12:42', '2026-04-07 07:12:42'),
(494, 6, 783, NULL, 'Factura', NULL, NULL, 'TICKET-00000583', NULL, NULL, 130.00, 'paid', 3, NULL, 0, '2026-04-07', NULL, '2026-04-07 07:13:30', '2026-04-07 07:13:30'),
(495, 6, 784, NULL, 'Factura', NULL, NULL, 'TICKET-00000584', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 07:17:05', '2026-04-07 07:17:05'),
(496, 6, 785, NULL, 'Factura', NULL, NULL, 'TICKET-00000585', NULL, NULL, 20.00, 'paid', 1, NULL, 0, '2026-04-07', NULL, '2026-04-07 07:17:22', '2026-04-07 07:17:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `name`, `deleted`) VALUES
(1, 'Efectivo', 0),
(2, 'Tarjeta', 0),
(3, 'Yape', 0),
(4, 'Transferencia', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `measurement_unit` enum('galones','litros') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'galones',
  `unit_price` decimal(8,2) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `brand`, `type`, `category`, `measurement_unit`, `unit_price`, `deleted`, `created_at`, `updated_at`) VALUES
(10, 'GLP', NULL, NULL, 'Combustible', 'galones', NULL, 1, '2025-11-04 11:22:44', '2026-02-07 17:13:57'),
(11, 'GNV', NULL, NULL, 'Combustible', 'galones', NULL, 1, '2025-11-07 12:04:30', '2026-02-07 17:14:02'),
(12, 'Gasolina Regular', NULL, NULL, 'Combustible', 'galones', NULL, 0, '2025-11-07 12:04:54', '2025-11-17 16:14:29'),
(13, 'Gasolina Premiun', NULL, NULL, 'Combustible', 'galones', NULL, 1, '2025-11-17 16:13:56', '2026-02-07 17:14:18'),
(14, 'DIESEL', NULL, NULL, 'Combustible', 'galones', NULL, 0, '2025-11-25 03:39:38', '2025-11-28 16:53:54'),
(15, 'Gasohol Regular', NULL, NULL, 'Combustible', 'galones', NULL, 0, '2025-11-28 17:16:04', '2025-11-28 17:16:04'),
(16, 'Gasohol Premium', NULL, NULL, 'Combustible', 'galones', NULL, 0, '2025-11-28 17:16:28', '2025-11-28 17:16:28'),
(17, 'Petroleo', NULL, NULL, 'Combustible', 'galones', NULL, 1, '2025-11-28 17:17:13', '2026-02-07 17:14:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pumps`
--

CREATE TABLE `pumps` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isle_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `side` int NOT NULL DEFAULT '1' COMMENT 'lado 1 o 2',
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='surtidores';

--
-- Volcado de datos para la tabla `pumps`
--

INSERT INTO `pumps` (`id`, `name`, `isle_id`, `product_id`, `side`, `deleted`) VALUES
(1, 'MAQUINA 1', 7, 14, 1, 0),
(10, 'MAQUINA 1', 7, 14, 2, 0),
(12, 'MAQUINA 2', 7, 14, 1, 0),
(13, 'MAQUINA 2', 7, 12, 1, 0),
(14, 'MAQUINA 2', 7, 16, 1, 0),
(15, 'MAQUINA 2', 7, 15, 1, 0),
(17, 'MAQUINA 2', 7, 14, 2, 0),
(18, 'MAQUINA 2', 7, 12, 2, 0),
(19, 'MAQUINA 2', 7, 16, 2, 0),
(20, 'MAQUINA 2', 7, 15, 2, 0),
(22, 'MAQUINA 3', 7, 14, 1, 0),
(24, 'MAQUINA 3', 7, 14, 2, 0),
(25, 'MAQUINA 4', 8, 14, 1, 0),
(26, 'MAQUINA 4', 8, 12, 1, 0),
(27, 'MAQUINA 4', 8, 16, 1, 0),
(28, 'MAQUINA 4', 8, 15, 1, 0),
(29, 'MAQUINA 4', 8, 14, 2, 0),
(30, 'MAQUINA 4', 8, 12, 2, 0),
(32, 'MAQUINA 4', 8, 16, 2, 0),
(33, 'MAQUINA 4', 8, 15, 2, 0),
(36, 'MAQUINA 5', 8, 14, 1, 0),
(37, 'MAQUINA 5', 8, 14, 2, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint UNSIGNED NOT NULL,
  `voucher_type` tinyint UNSIGNED DEFAULT NULL COMMENT '1: Factura , 2:Boleta , 3:Nota de Venta, 4: Otro',
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method_id` bigint UNSIGNED NOT NULL,
  `supplier_id` bigint UNSIGNED DEFAULT NULL,
  `purchase_temp` decimal(8,2) DEFAULT NULL,
  `real_temp` decimal(8,2) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `purchases`
--

INSERT INTO `purchases` (`id`, `voucher_type`, `invoice_number`, `payment_method_id`, `supplier_id`, `purchase_temp`, `real_temp`, `deleted`, `date`, `created_at`, `updated_at`) VALUES
(39, 4, 'SEDE MAYLUX', 1, NULL, NULL, NULL, 0, '2026-04-03', '2026-04-06 10:14:37', '2026-04-06 10:14:37'),
(40, 1, 'FE04-00330218', 4, NULL, NULL, NULL, 0, '2026-04-03', '2026-04-06 10:16:18', '2026-04-06 10:16:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `purchase_details`
--

CREATE TABLE `purchase_details` (
  `id` bigint NOT NULL,
  `purchase_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `tank_id` bigint UNSIGNED DEFAULT NULL,
  `quantity` decimal(12,3) NOT NULL DEFAULT '0.000',
  `measurement_unit` enum('galones','litros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `purchase_details`
--

INSERT INTO `purchase_details` (`id`, `purchase_id`, `product_id`, `tank_id`, `quantity`, `measurement_unit`, `unit_price`, `subtotal`) VALUES
(16, 39, 14, 25, 3000.000, 'galones', 20.43, 61290.00),
(17, 40, 14, 25, 3300.000, 'galones', 20.43, 67419.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `created_at`, `updated_at`) VALUES
(1, 'master', 've todo', '2025-08-01 12:51:49', '2025-08-01 12:51:50'),
(2, 'admin', 'prueba', '2025-08-04 11:48:10', '2025-08-04 11:48:10'),
(3, 'worker', 'trabajador comun', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_plate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `date` datetime NOT NULL,
  `type_sale` tinyint NOT NULL COMMENT '0: directa, 1: contratos, 2:creditos',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `adicional` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `user_id`, `location_id`, `client_id`, `client_name`, `vehicle_plate`, `phone`, `total`, `date`, `type_sale`, `deleted`, `created_at`, `updated_at`, `adicional`) VALUES
(257, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 850.00, '2026-04-01 22:42:02', 0, 0, '2026-04-01 22:42:02', '2026-04-01 22:42:02', 0.00),
(258, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 150.00, '2026-04-01 22:43:13', 0, 0, '2026-04-01 22:43:13', '2026-04-01 22:43:13', 0.00),
(259, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 200.00, '2026-04-01 22:44:02', 0, 0, '2026-04-01 22:44:02', '2026-04-01 22:44:02', 0.00),
(260, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 50.00, '2026-04-01 22:47:02', 0, 0, '2026-04-01 22:47:02', '2026-04-01 22:47:02', 0.00),
(261, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 157.56, '2026-04-01 22:48:19', 0, 0, '2026-04-01 22:48:19', '2026-04-01 22:48:19', 0.00),
(262, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 120.00, '2026-04-01 22:48:45', 0, 0, '2026-04-01 22:48:45', '2026-04-01 22:48:45', 0.00),
(263, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 100.00, '2026-04-01 22:49:40', 0, 0, '2026-04-01 22:49:40', '2026-04-01 22:49:40', 0.00),
(264, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 27.00, '2026-04-01 22:50:32', 0, 0, '2026-04-01 22:50:32', '2026-04-01 22:50:32', 0.00),
(265, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 3.00, '2026-04-01 22:51:03', 0, 0, '2026-04-01 22:51:03', '2026-04-01 22:51:03', 0.00),
(266, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 60.00, '2026-04-01 22:51:32', 0, 0, '2026-04-01 22:51:32', '2026-04-01 22:51:32', 0.00),
(267, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 100.00, '2026-04-01 22:52:12', 0, 0, '2026-04-01 22:52:12', '2026-04-01 22:52:12', 0.00),
(268, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 22.20, '2026-04-01 22:52:55', 0, 0, '2026-04-01 22:52:55', '2026-04-01 22:52:55', 0.00),
(269, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 18.00, '2026-04-01 22:53:31', 0, 0, '2026-04-01 22:53:31', '2026-04-01 22:53:31', 0.00),
(270, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 110.00, '2026-04-01 22:53:57', 0, 0, '2026-04-01 22:53:57', '2026-04-01 22:53:57', 0.00),
(271, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 31.00, '2026-04-01 22:54:24', 0, 0, '2026-04-01 22:54:24', '2026-04-01 22:54:24', 0.00),
(272, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 79.00, '2026-04-01 22:55:10', 0, 0, '2026-04-01 22:55:10', '2026-04-01 22:55:10', 0.00),
(273, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 40.00, '2026-04-01 22:55:42', 0, 0, '2026-04-01 22:55:42', '2026-04-01 22:55:42', 0.00),
(274, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.00, '2026-04-01 22:56:49', 0, 0, '2026-04-01 22:56:49', '2026-04-01 22:56:49', 0.00),
(275, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-01 22:57:27', 0, 0, '2026-04-01 22:57:27', '2026-04-01 22:57:27', 0.00),
(276, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 42.00, '2026-04-01 22:59:05', 0, 0, '2026-04-01 22:59:05', '2026-04-01 22:59:05', 0.00),
(277, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 22:59:32', 0, 0, '2026-04-01 22:59:32', '2026-04-01 22:59:32', 0.00),
(278, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 22:59:46', 0, 0, '2026-04-01 22:59:46', '2026-04-01 22:59:46', 0.00),
(279, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:00:08', 0, 0, '2026-04-01 23:00:08', '2026-04-01 23:00:08', 0.00),
(280, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:00:29', 0, 0, '2026-04-01 23:00:29', '2026-04-01 23:00:29', 0.00),
(281, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 26.00, '2026-04-01 23:01:54', 0, 0, '2026-04-01 23:01:54', '2026-04-01 23:01:54', 0.00),
(282, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 8.00, '2026-04-01 23:02:23', 0, 0, '2026-04-01 23:02:23', '2026-04-01 23:02:23', 0.00),
(283, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.80, '2026-04-01 23:04:29', 0, 0, '2026-04-01 23:04:29', '2026-04-01 23:04:29', 0.00),
(284, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 160.00, '2026-04-01 23:06:49', 0, 0, '2026-04-01 23:06:49', '2026-04-01 23:06:49', 0.00),
(285, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-01 23:09:36', 0, 0, '2026-04-01 23:09:36', '2026-04-01 23:09:36', 0.00),
(286, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 140.00, '2026-04-01 23:10:22', 0, 0, '2026-04-01 23:10:22', '2026-04-01 23:10:22', 0.00),
(287, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 5.00, '2026-04-01 23:10:58', 0, 0, '2026-04-01 23:10:58', '2026-04-01 23:10:58', 0.00),
(288, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:11:27', 0, 0, '2026-04-01 23:11:27', '2026-04-01 23:11:27', 0.00),
(289, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-01 23:11:57', 0, 0, '2026-04-01 23:11:57', '2026-04-01 23:11:57', 0.00),
(290, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:12:19', 0, 0, '2026-04-01 23:12:19', '2026-04-01 23:12:19', 0.00),
(291, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 12.00, '2026-04-01 23:14:09', 0, 0, '2026-04-01 23:14:09', '2026-04-01 23:14:09', 0.00),
(292, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 128.00, '2026-04-01 23:15:11', 0, 0, '2026-04-01 23:15:11', '2026-04-01 23:15:11', 0.00),
(293, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-01 23:15:38', 0, 0, '2026-04-01 23:15:38', '2026-04-01 23:15:38', 0.00),
(294, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:16:04', 0, 0, '2026-04-01 23:16:04', '2026-04-01 23:16:04', 0.00),
(295, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 75.00, '2026-04-01 23:16:41', 0, 0, '2026-04-01 23:16:41', '2026-04-01 23:16:41', 0.00),
(296, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-01 23:17:05', 0, 0, '2026-04-01 23:17:05', '2026-04-01 23:17:05', 0.00),
(297, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-01 23:18:04', 0, 0, '2026-04-01 23:18:04', '2026-04-01 23:18:04', 0.00),
(298, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:18:19', 0, 0, '2026-04-01 23:18:19', '2026-04-01 23:18:19', 0.00),
(299, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 50.00, '2026-04-01 23:18:40', 0, 0, '2026-04-01 23:18:40', '2026-04-01 23:18:40', 0.00),
(300, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:18:56', 0, 0, '2026-04-01 23:18:56', '2026-04-01 23:18:56', 0.00),
(301, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:19:18', 0, 0, '2026-04-01 23:19:18', '2026-04-01 23:19:18', 0.00),
(302, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:21:33', 0, 0, '2026-04-01 23:21:33', '2026-04-01 23:21:33', 0.00),
(303, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:21:45', 0, 0, '2026-04-01 23:21:45', '2026-04-01 23:21:45', 0.00),
(304, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:21:59', 0, 0, '2026-04-01 23:21:59', '2026-04-01 23:21:59', 0.00),
(305, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:22:13', 0, 0, '2026-04-01 23:22:13', '2026-04-01 23:22:13', 0.00),
(306, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:22:26', 0, 0, '2026-04-01 23:22:26', '2026-04-01 23:22:26', 0.00),
(307, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-01 23:22:42', 0, 0, '2026-04-01 23:22:42', '2026-04-01 23:22:42', 0.00),
(308, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:22:56', 0, 0, '2026-04-01 23:22:56', '2026-04-01 23:22:56', 0.00),
(309, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-01 23:23:10', 0, 0, '2026-04-01 23:23:10', '2026-04-01 23:23:10', 0.00),
(310, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 52.00, '2026-04-01 23:23:35', 0, 0, '2026-04-01 23:23:35', '2026-04-01 23:23:35', 0.00),
(311, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:23:58', 0, 0, '2026-04-01 23:23:58', '2026-04-01 23:23:58', 0.00),
(312, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 8.00, '2026-04-01 23:24:26', 0, 0, '2026-04-01 23:24:26', '2026-04-01 23:24:26', 0.00),
(313, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-01 23:24:49', 0, 0, '2026-04-01 23:24:49', '2026-04-01 23:24:49', 0.00),
(314, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 5.00, '2026-04-01 23:25:10', 0, 0, '2026-04-01 23:25:10', '2026-04-01 23:25:10', 0.00),
(315, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-01 23:25:23', 0, 0, '2026-04-01 23:25:23', '2026-04-01 23:25:23', 0.00),
(316, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 100.00, '2026-04-01 23:25:41', 0, 0, '2026-04-01 23:25:41', '2026-04-01 23:25:41', 0.00),
(317, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:20:23', 0, 0, '2026-04-02 00:20:23', '2026-04-02 00:20:23', 0.00),
(318, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 00:20:38', 0, 0, '2026-04-02 00:20:38', '2026-04-02 00:20:38', 0.00),
(319, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 00:20:54', 0, 0, '2026-04-02 00:20:54', '2026-04-02 00:20:54', 0.00),
(320, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:21:15', 0, 0, '2026-04-02 00:21:15', '2026-04-02 00:21:15', 0.00),
(321, 6, 3, NULL, NULL, NULL, NULL, 15.00, '2026-04-02 00:21:29', 0, 0, '2026-04-02 00:21:29', '2026-04-02 00:21:29', 0.00),
(322, 6, 3, NULL, NULL, NULL, NULL, 20.13, '2026-04-02 00:21:48', 0, 0, '2026-04-02 00:21:48', '2026-04-02 00:21:48', 0.00),
(323, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 00:22:09', 0, 0, '2026-04-02 00:22:09', '2026-04-02 00:22:09', 0.00),
(324, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:22:23', 0, 0, '2026-04-02 00:22:23', '2026-04-02 00:22:23', 0.00),
(325, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:22:39', 0, 0, '2026-04-02 00:22:39', '2026-04-02 00:22:39', 0.00),
(326, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 00:22:54', 0, 0, '2026-04-02 00:22:54', '2026-04-02 00:22:54', 0.00),
(327, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-02 00:23:18', 0, 0, '2026-04-02 00:23:18', '2026-04-02 00:23:18', 0.00),
(328, 6, 3, NULL, NULL, NULL, NULL, 15.00, '2026-04-02 00:23:34', 0, 0, '2026-04-02 00:23:34', '2026-04-02 00:23:34', 0.00),
(329, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-02 00:23:46', 0, 0, '2026-04-02 00:23:46', '2026-04-02 00:23:46', 0.00),
(330, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:24:02', 0, 0, '2026-04-02 00:24:02', '2026-04-02 00:24:02', 0.00),
(331, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 00:24:16', 0, 0, '2026-04-02 00:24:16', '2026-04-02 00:24:16', 0.00),
(332, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 00:24:30', 0, 0, '2026-04-02 00:24:30', '2026-04-02 00:24:30', 0.00),
(333, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:24:43', 0, 0, '2026-04-02 00:24:43', '2026-04-02 00:24:43', 0.00),
(334, 6, 3, NULL, NULL, NULL, NULL, 35.37, '2026-04-02 00:24:58', 0, 0, '2026-04-02 00:24:58', '2026-04-02 00:24:58', 0.00),
(335, 6, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-02 00:26:34', 0, 0, '2026-04-02 00:26:34', '2026-04-02 00:26:34', 0.00),
(336, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 00:26:46', 0, 0, '2026-04-02 00:26:46', '2026-04-02 00:26:46', 0.00),
(337, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:27:02', 0, 0, '2026-04-02 00:27:02', '2026-04-02 00:27:02', 0.00),
(338, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:27:17', 0, 0, '2026-04-02 00:27:17', '2026-04-02 00:27:17', 0.00),
(339, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:27:31', 0, 0, '2026-04-02 00:27:31', '2026-04-02 00:27:31', 0.00),
(340, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:27:46', 0, 0, '2026-04-02 00:27:46', '2026-04-02 00:27:46', 0.00),
(341, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-02 00:27:59', 0, 0, '2026-04-02 00:27:59', '2026-04-02 00:27:59', 0.00),
(345, 6, 3, NULL, NULL, NULL, NULL, 407.00, '2026-04-02 00:32:50', 0, 0, '2026-04-02 00:32:50', '2026-04-02 00:32:50', 0.00),
(346, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-02 00:33:08', 0, 0, '2026-04-02 00:33:08', '2026-04-02 00:33:08', 0.00),
(347, 6, 3, NULL, NULL, NULL, NULL, 850.00, '2026-04-02 00:34:25', 0, 0, '2026-04-02 00:34:25', '2026-04-02 00:34:25', 0.00),
(348, 6, 3, NULL, NULL, NULL, NULL, 370.00, '2026-04-02 00:35:06', 0, 0, '2026-04-02 00:35:06', '2026-04-02 00:35:06', 0.00),
(349, 6, 3, NULL, NULL, NULL, NULL, 666.00, '2026-04-02 00:35:32', 0, 0, '2026-04-02 00:35:32', '2026-04-02 00:35:32', 0.00),
(350, 6, 3, NULL, NULL, NULL, NULL, 520.00, '2026-04-02 00:35:48', 0, 0, '2026-04-02 00:35:48', '2026-04-02 00:35:48', 0.00),
(351, 6, 3, NULL, NULL, NULL, NULL, 108.00, '2026-04-02 00:36:03', 0, 0, '2026-04-02 00:36:03', '2026-04-02 00:36:03', 0.00),
(352, 6, 3, NULL, NULL, NULL, NULL, 121.07, '2026-04-02 00:37:55', 0, 0, '2026-04-02 00:37:55', '2026-04-02 00:37:55', 0.00),
(353, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-02 00:38:15', 0, 0, '2026-04-02 00:38:15', '2026-04-02 00:38:15', 0.00),
(354, 6, 3, NULL, NULL, NULL, NULL, 188.00, '2026-04-02 00:38:28', 0, 0, '2026-04-02 00:38:28', '2026-04-02 00:38:28', 0.00),
(355, 6, 3, NULL, NULL, NULL, NULL, 245.00, '2026-04-02 00:38:40', 0, 0, '2026-04-02 00:38:40', '2026-04-02 00:38:40', 0.00),
(356, 6, 3, NULL, NULL, NULL, NULL, 500.00, '2026-04-02 00:38:53', 0, 0, '2026-04-02 00:38:53', '2026-04-02 00:38:53', 0.00),
(357, 6, 3, NULL, NULL, NULL, NULL, 500.00, '2026-04-02 00:41:03', 0, 0, '2026-04-02 00:41:03', '2026-04-02 00:41:03', 0.00),
(358, 6, 3, NULL, NULL, NULL, NULL, 384.00, '2026-04-02 00:43:20', 0, 0, '2026-04-02 00:43:20', '2026-04-02 00:43:20', 0.00),
(359, 6, 3, NULL, NULL, NULL, NULL, 1000.00, '2026-04-02 00:43:41', 0, 0, '2026-04-02 00:43:41', '2026-04-02 00:43:41', 0.00),
(360, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-02 00:47:16', 0, 0, '2026-04-02 00:47:16', '2026-04-02 00:47:16', 0.00),
(361, 6, 3, NULL, NULL, NULL, NULL, 40.00, '2026-04-02 00:47:27', 0, 0, '2026-04-02 00:47:27', '2026-04-02 00:47:27', 0.00),
(362, 6, 3, NULL, NULL, NULL, NULL, 1554.00, '2026-04-02 00:47:57', 0, 0, '2026-04-02 00:47:57', '2026-04-02 00:47:57', 0.00),
(363, 6, 3, NULL, NULL, NULL, NULL, 650.00, '2026-04-02 00:48:22', 0, 0, '2026-04-02 00:48:22', '2026-04-02 00:48:22', 0.00),
(364, 6, 3, NULL, NULL, NULL, NULL, 141.00, '2026-04-02 00:48:48', 0, 0, '2026-04-02 00:48:48', '2026-04-02 00:48:48', 0.00),
(365, 6, 3, NULL, NULL, NULL, NULL, 770.00, '2026-04-02 00:49:13', 0, 0, '2026-04-02 00:49:13', '2026-04-02 00:49:13', 0.00),
(366, 6, 3, NULL, NULL, NULL, NULL, 60.00, '2026-04-02 00:52:30', 0, 0, '2026-04-02 00:52:30', '2026-04-02 00:52:30', 0.00),
(367, 6, 3, NULL, NULL, NULL, NULL, 930.00, '2026-04-02 00:52:57', 0, 0, '2026-04-02 00:52:57', '2026-04-02 00:52:57', 0.00),
(368, 6, 3, NULL, NULL, NULL, NULL, 130.00, '2026-04-02 00:53:12', 0, 0, '2026-04-02 00:53:12', '2026-04-02 00:53:12', 0.00),
(369, 6, 3, NULL, NULL, NULL, NULL, 23.00, '2026-04-02 00:55:08', 0, 0, '2026-04-02 00:55:08', '2026-04-02 00:55:08', 0.00),
(370, 6, 3, NULL, NULL, NULL, NULL, 23.00, '2026-04-02 00:55:47', 0, 0, '2026-04-02 00:55:47', '2026-04-02 00:55:47', 0.00),
(372, 6, 3, NULL, NULL, NULL, NULL, 26.00, '2026-04-02 01:40:48', 0, 0, '2026-04-02 01:40:48', '2026-04-02 01:40:48', 0.00),
(373, 6, 3, NULL, NULL, NULL, NULL, 200.00, '2026-04-02 04:42:56', 0, 0, '2026-04-02 04:42:56', '2026-04-02 04:42:56', 0.00),
(374, 6, 3, NULL, NULL, NULL, NULL, 200.00, '2026-04-02 05:12:46', 0, 0, '2026-04-02 05:12:46', '2026-04-02 05:12:46', 0.00),
(375, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 06:00:50', 0, 0, '2026-04-02 06:00:50', '2026-04-02 06:00:50', 0.00),
(378, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-02 06:06:13', 0, 0, '2026-04-02 06:06:13', '2026-04-02 06:06:13', 0.00),
(379, 6, 3, NULL, NULL, NULL, NULL, 300.00, '2026-04-02 06:14:09', 0, 0, '2026-04-02 06:14:09', '2026-04-02 06:14:09', 0.00),
(381, 6, 3, NULL, NULL, NULL, NULL, 60.00, '2026-04-02 06:16:26', 0, 0, '2026-04-02 06:16:26', '2026-04-02 06:16:26', 0.00),
(382, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 80.00, '2026-04-02 06:35:36', 0, 0, '2026-04-02 06:35:36', '2026-04-02 06:35:36', 0.00),
(383, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 70.00, '2026-04-02 06:35:56', 0, 0, '2026-04-02 06:35:56', '2026-04-02 06:35:56', 0.00),
(384, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 50.00, '2026-04-02 06:36:18', 0, 0, '2026-04-02 06:36:18', '2026-04-02 06:36:18', 0.00),
(385, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 198.00, '2026-04-02 06:36:35', 0, 0, '2026-04-02 06:36:35', '2026-04-02 06:36:35', 0.00),
(388, 6, 3, NULL, NULL, NULL, NULL, 254.00, '2026-04-02 06:56:48', 0, 0, '2026-04-02 06:56:48', '2026-04-02 06:56:48', 0.00),
(389, 6, 3, NULL, NULL, NULL, NULL, 388.00, '2026-04-02 06:57:15', 0, 0, '2026-04-02 06:57:15', '2026-04-02 06:57:15', 0.00),
(390, 6, 3, NULL, 'EMPRESA DE TRANSPORTES LUZ ANGELICA EIRL', 'T8M-925', NULL, 2331.00, '2026-04-02 07:06:15', 2, 0, '2026-04-02 07:06:15', '2026-04-02 07:06:15', 0.00),
(391, 6, 3, NULL, 'OCAÑA IZQUIERDO HECTOR EDGARDO', 'AUK-843', NULL, 1354.20, '2026-04-02 07:07:59', 2, 0, '2026-04-02 07:07:59', '2026-04-02 07:07:59', 0.00),
(392, 6, 3, NULL, 'EMPRESA DE TRANSPORTE Y SERVICIOS GENERALES LEON S.A.C.', 'T9T-866', NULL, 1074.70, '2026-04-02 07:09:39', 2, 0, '2026-04-02 07:09:39', '2026-04-02 07:09:39', 0.00),
(393, 6, 3, NULL, 'CRUZADO SALAZAR TANIA MARLENE', 'ACU-859', NULL, 1110.00, '2026-04-02 07:10:46', 2, 0, '2026-04-02 07:10:46', '2026-04-02 07:10:46', 0.00),
(394, 6, 3, NULL, 'VALLEJOS GALLARDO YENY MELISSA', 'M3Y-759', NULL, 1176.62, '2026-04-02 07:12:27', 2, 0, '2026-04-02 07:12:27', '2026-04-02 07:12:27', 0.00),
(395, 6, 3, NULL, 'EMPRESA DE TRANSPORTE Y SERVICIOS GENERALES LEON S.A.C.', 'M5T-827', NULL, 1720.03, '2026-04-02 07:13:40', 2, 0, '2026-04-02 07:13:40', '2026-04-02 07:13:40', 0.00),
(398, 7, 3, NULL, 'RAMIRO MENDOZA', NULL, NULL, 6030.72, '2026-04-02 21:15:17', 2, 0, '2026-04-02 21:15:17', '2026-04-02 21:15:17', 0.00),
(399, 7, 3, NULL, 'RAMIRO MENDOZA', NULL, NULL, 4059.31, '2026-04-02 21:18:15', 2, 0, '2026-04-02 21:18:15', '2026-04-02 21:18:15', 0.00),
(400, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.00, '2026-04-02 21:19:40', 0, 0, '2026-04-02 21:19:40', '2026-04-02 21:19:40', 0.00),
(401, 6, 3, NULL, 'valeria', 'BUS-720', NULL, 1122.81, '2026-04-02 23:32:10', 2, 0, '2026-04-02 23:32:10', '2026-04-02 23:32:10', 0.00),
(402, 6, 3, NULL, 'VALERIA', 'BMZ-746', NULL, 3452.14, '2026-04-02 23:35:32', 2, 0, '2026-04-02 23:35:32', '2026-04-02 23:35:32', 0.00),
(403, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-02 23:39:05', 0, 0, '2026-04-02 23:39:05', '2026-04-02 23:39:05', 0.00),
(404, 6, 3, NULL, NULL, NULL, NULL, 300.00, '2026-04-02 23:41:00', 0, 0, '2026-04-02 23:41:00', '2026-04-02 23:41:00', 0.00),
(405, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-02 23:42:23', 0, 0, '2026-04-02 23:42:23', '2026-04-02 23:42:23', 0.00),
(406, 6, 3, NULL, NULL, NULL, NULL, 200.00, '2026-04-02 23:45:04', 0, 0, '2026-04-02 23:45:04', '2026-04-02 23:45:04', 0.00),
(407, 6, 3, NULL, NULL, NULL, NULL, 184.00, '2026-04-03 00:13:45', 0, 0, '2026-04-03 00:13:45', '2026-04-03 00:13:45', 0.00),
(408, 6, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-03 00:27:07', 0, 0, '2026-04-03 00:27:07', '2026-04-03 00:27:07', 0.00),
(409, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-03 00:28:44', 0, 0, '2026-04-03 00:28:44', '2026-04-03 00:28:44', 0.00),
(410, 6, 3, NULL, NULL, NULL, NULL, 300.00, '2026-04-03 00:34:18', 0, 0, '2026-04-03 00:34:18', '2026-04-03 00:34:18', 0.00),
(411, 7, 3, NULL, NULL, NULL, NULL, 1535.00, '2026-04-03 00:48:39', 0, 0, '2026-04-03 00:48:39', '2026-04-03 00:48:39', 0.00),
(412, 7, 3, NULL, NULL, NULL, NULL, 666.00, '2026-04-03 00:50:05', 0, 0, '2026-04-03 00:50:05', '2026-04-03 00:50:05', 0.00),
(413, 6, 3, NULL, NULL, NULL, NULL, 666.00, '2026-04-03 00:52:54', 0, 0, '2026-04-03 00:52:54', '2026-04-03 00:52:54', 0.00),
(414, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-03 00:54:05', 0, 0, '2026-04-03 00:54:05', '2026-04-03 00:54:05', 0.00),
(415, 6, 3, NULL, NULL, NULL, NULL, 444.00, '2026-04-03 00:58:28', 0, 0, '2026-04-03 00:58:28', '2026-04-03 00:58:28', 0.00),
(416, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 00:59:06', 0, 0, '2026-04-03 00:59:06', '2026-04-03 00:59:06', 0.00),
(417, 6, 3, NULL, NULL, NULL, NULL, 200.00, '2026-04-03 00:59:32', 0, 0, '2026-04-03 00:59:32', '2026-04-03 00:59:32', 0.00),
(418, 6, 3, NULL, NULL, NULL, NULL, 900.00, '2026-04-03 01:00:17', 0, 0, '2026-04-03 01:00:17', '2026-04-03 01:00:17', 0.00),
(419, 6, 3, NULL, NULL, NULL, NULL, 350.00, '2026-04-03 01:00:47', 0, 0, '2026-04-03 01:00:47', '2026-04-03 01:00:47', 0.00),
(420, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 01:02:20', 0, 0, '2026-04-03 01:02:20', '2026-04-03 01:02:20', 0.00),
(421, 6, 3, NULL, NULL, NULL, NULL, 402.00, '2026-04-03 01:02:40', 0, 0, '2026-04-03 01:02:40', '2026-04-03 01:02:40', 0.00),
(422, 6, 3, NULL, NULL, NULL, NULL, 344.00, '2026-04-03 01:03:02', 0, 0, '2026-04-03 01:03:02', '2026-04-03 01:03:02', 0.00),
(423, 6, 3, NULL, NULL, NULL, NULL, 190.00, '2026-04-03 01:03:24', 0, 0, '2026-04-03 01:03:24', '2026-04-03 01:03:24', 0.00),
(424, 6, 3, NULL, NULL, NULL, NULL, 1000.00, '2026-04-03 01:04:32', 0, 0, '2026-04-03 01:04:32', '2026-04-03 01:04:32', 0.00),
(425, 6, 3, NULL, NULL, NULL, NULL, 650.00, '2026-04-03 01:04:57', 0, 0, '2026-04-03 01:04:57', '2026-04-03 01:04:57', 0.00),
(426, 6, 3, NULL, NULL, NULL, NULL, 777.00, '2026-04-03 01:05:18', 0, 0, '2026-04-03 01:05:18', '2026-04-03 01:05:18', 0.00),
(427, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 01:09:46', 0, 0, '2026-04-03 01:09:46', '2026-04-03 01:09:46', 0.00),
(428, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-03 01:10:24', 0, 0, '2026-04-03 01:10:24', '2026-04-03 01:10:24', 0.00),
(429, 6, 3, NULL, NULL, NULL, NULL, 110.00, '2026-04-03 01:14:35', 0, 0, '2026-04-03 01:14:35', '2026-04-03 01:14:35', 0.00),
(430, 6, 3, NULL, NULL, NULL, NULL, 45.00, '2026-04-03 01:15:11', 0, 0, '2026-04-03 01:15:11', '2026-04-03 01:15:11', 0.00),
(431, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-03 01:15:50', 0, 0, '2026-04-03 01:15:50', '2026-04-03 01:15:50', 0.00),
(432, 6, 3, NULL, NULL, NULL, NULL, 120.00, '2026-04-03 01:16:21', 0, 0, '2026-04-03 01:16:21', '2026-04-03 01:16:21', 0.00),
(433, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 01:18:10', 0, 0, '2026-04-03 01:18:10', '2026-04-03 01:18:10', 0.00),
(434, 6, 3, NULL, NULL, NULL, NULL, 15.00, '2026-04-03 01:18:40', 0, 0, '2026-04-03 01:18:40', '2026-04-03 01:18:40', 0.00),
(435, 6, 3, NULL, NULL, NULL, NULL, 280.00, '2026-04-03 01:23:39', 0, 0, '2026-04-03 01:23:39', '2026-04-03 01:23:39', 0.00),
(436, 6, 3, NULL, NULL, NULL, NULL, 140.00, '2026-04-03 01:24:16', 0, 0, '2026-04-03 01:24:16', '2026-04-03 01:24:16', 0.00),
(437, 6, 3, NULL, NULL, NULL, NULL, 150.00, '2026-04-03 01:24:46', 0, 0, '2026-04-03 01:24:46', '2026-04-03 01:24:46', 0.00),
(438, 6, 3, NULL, NULL, NULL, NULL, 500.00, '2026-04-03 01:25:14', 0, 0, '2026-04-03 01:25:14', '2026-04-03 01:25:14', 0.00),
(439, 6, 3, NULL, NULL, NULL, NULL, 120.00, '2026-04-03 01:26:02', 0, 0, '2026-04-03 01:26:02', '2026-04-03 01:26:02', 0.00),
(440, 6, 3, NULL, NULL, NULL, NULL, 36.00, '2026-04-03 01:28:50', 0, 0, '2026-04-03 01:28:50', '2026-04-03 01:28:50', 0.00),
(441, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 01:29:58', 0, 0, '2026-04-03 01:29:58', '2026-04-03 01:29:58', 0.00),
(442, 6, 3, NULL, NULL, NULL, NULL, 565.00, '2026-04-03 01:30:42', 0, 0, '2026-04-03 01:30:42', '2026-04-03 01:30:42', 0.00),
(443, 6, 3, NULL, NULL, NULL, NULL, 94.00, '2026-04-03 01:32:09', 0, 0, '2026-04-03 01:32:09', '2026-04-03 01:32:09', 0.00),
(444, 6, 3, NULL, NULL, NULL, NULL, 92.00, '2026-04-03 01:33:07', 0, 0, '2026-04-03 01:33:07', '2026-04-03 01:33:07', 0.00),
(445, 6, 3, NULL, NULL, NULL, NULL, 190.00, '2026-04-03 01:34:51', 0, 0, '2026-04-03 01:34:51', '2026-04-03 01:34:51', 0.00),
(446, 6, 3, NULL, NULL, NULL, NULL, 77.00, '2026-04-03 01:35:45', 0, 0, '2026-04-03 01:35:45', '2026-04-03 01:35:45', 0.00),
(447, 6, 3, NULL, NULL, NULL, NULL, 69.00, '2026-04-03 01:37:06', 0, 0, '2026-04-03 01:37:06', '2026-04-03 01:37:06', 0.00),
(448, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 01:37:30', 0, 0, '2026-04-03 01:37:30', '2026-04-03 01:37:30', 0.00),
(449, 7, 3, NULL, NULL, NULL, NULL, 732.31, '2026-04-03 01:41:34', 0, 0, '2026-04-03 01:41:34', '2026-04-03 01:41:34', 0.00),
(450, 7, 3, NULL, NULL, NULL, NULL, 600.00, '2026-04-03 01:41:58', 0, 0, '2026-04-03 01:41:58', '2026-04-03 01:41:58', 0.00),
(451, 7, 3, NULL, NULL, NULL, NULL, 630.00, '2026-04-03 01:49:55', 0, 0, '2026-04-03 01:49:55', '2026-04-03 01:49:55', 0.00),
(452, 7, 3, NULL, NULL, NULL, NULL, 444.00, '2026-04-03 01:50:37', 0, 0, '2026-04-03 01:50:37', '2026-04-03 01:50:37', 0.00),
(453, 7, 3, NULL, NULL, NULL, NULL, 120.00, '2026-04-03 01:51:37', 0, 0, '2026-04-03 01:51:37', '2026-04-03 01:51:37', 0.00),
(454, 7, 3, NULL, NULL, NULL, NULL, 60.00, '2026-04-03 01:52:51', 0, 0, '2026-04-03 01:52:51', '2026-04-03 01:52:51', 0.00),
(455, 7, 3, NULL, NULL, NULL, NULL, 125.00, '2026-04-03 01:53:14', 0, 0, '2026-04-03 01:53:14', '2026-04-03 01:53:14', 0.00),
(456, 7, 3, NULL, NULL, NULL, NULL, 86.00, '2026-04-03 01:54:02', 0, 0, '2026-04-03 01:54:02', '2026-04-03 01:54:02', 0.00),
(457, 7, 3, NULL, NULL, NULL, NULL, 69.00, '2026-04-03 01:54:47', 0, 0, '2026-04-03 01:54:47', '2026-04-03 01:54:47', 0.00),
(458, 7, 3, NULL, NULL, NULL, NULL, 62.00, '2026-04-03 01:55:19', 0, 0, '2026-04-03 01:55:19', '2026-04-03 01:55:19', 0.00),
(459, 7, 3, NULL, NULL, NULL, NULL, 169.00, '2026-04-03 01:56:34', 0, 0, '2026-04-03 01:56:34', '2026-04-03 01:56:34', 0.00),
(460, 7, 3, NULL, NULL, NULL, NULL, 98.00, '2026-04-03 01:57:38', 0, 0, '2026-04-03 01:57:38', '2026-04-03 01:57:38', 0.00),
(461, 7, 3, NULL, NULL, NULL, NULL, 153.00, '2026-04-03 01:59:15', 0, 0, '2026-04-03 01:59:15', '2026-04-03 01:59:15', 0.00),
(462, 7, 3, NULL, NULL, NULL, NULL, 49.00, '2026-04-03 01:59:47', 0, 0, '2026-04-03 01:59:47', '2026-04-03 01:59:47', 0.00),
(463, 7, 3, NULL, NULL, NULL, NULL, 288.00, '2026-04-03 02:00:18', 0, 0, '2026-04-03 02:00:18', '2026-04-03 02:00:18', 0.00),
(464, 7, 3, NULL, NULL, NULL, NULL, 132.00, '2026-04-03 02:00:57', 0, 0, '2026-04-03 02:00:57', '2026-04-03 02:00:57', 0.00),
(465, 6, 3, NULL, NULL, NULL, NULL, 500.00, '2026-04-03 02:07:57', 0, 0, '2026-04-03 02:07:57', '2026-04-03 02:07:57', 0.00),
(466, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-03 07:11:53', 0, 0, '2026-04-03 07:11:53', '2026-04-03 07:11:53', 0.00),
(467, 6, 3, NULL, NULL, NULL, NULL, 80.00, '2026-04-03 07:12:14', 0, 0, '2026-04-03 07:12:14', '2026-04-03 07:12:14', 0.00),
(468, 6, 3, NULL, NULL, NULL, NULL, 150.00, '2026-04-03 07:12:35', 0, 0, '2026-04-03 07:12:35', '2026-04-03 07:12:35', 0.00),
(469, 6, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-03 07:12:56', 0, 0, '2026-04-03 07:12:56', '2026-04-03 07:12:56', 0.00),
(470, 6, 3, NULL, NULL, NULL, NULL, 111.00, '2026-04-03 07:13:15', 0, 0, '2026-04-03 07:13:15', '2026-04-03 07:13:15', 0.00),
(471, 6, 3, NULL, NULL, NULL, NULL, 900.00, '2026-04-03 07:14:09', 0, 0, '2026-04-03 07:14:09', '2026-04-03 07:14:09', 0.00),
(472, 6, 3, NULL, NULL, NULL, NULL, 700.00, '2026-04-03 07:14:38', 0, 0, '2026-04-03 07:14:38', '2026-04-03 07:14:38', 0.00),
(473, 6, 3, NULL, NULL, NULL, NULL, 400.00, '2026-04-03 07:15:07', 0, 0, '2026-04-03 07:15:07', '2026-04-03 07:15:07', 0.00),
(474, 7, 3, NULL, NULL, NULL, NULL, 80.00, '2026-04-03 07:17:16', 0, 0, '2026-04-03 07:17:16', '2026-04-03 07:17:16', 0.00),
(475, 7, 3, NULL, NULL, NULL, NULL, 315.00, '2026-04-03 07:17:59', 0, 0, '2026-04-03 07:17:59', '2026-04-03 07:17:59', 0.00),
(476, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 110.00, '2026-04-03 22:17:42', 0, 0, '2026-04-03 22:17:42', '2026-04-03 22:17:42', 0.00),
(477, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 150.00, '2026-04-03 22:18:46', 0, 0, '2026-04-03 22:18:46', '2026-04-03 22:18:46', 0.00),
(478, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:19:02', 0, 0, '2026-04-03 22:19:02', '2026-04-03 22:19:02', 0.00),
(479, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 40.00, '2026-04-03 22:19:22', 0, 0, '2026-04-03 22:19:22', '2026-04-03 22:19:22', 0.00),
(480, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 180.00, '2026-04-03 22:22:25', 0, 0, '2026-04-03 22:22:25', '2026-04-03 22:22:25', 0.00),
(481, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:23:04', 0, 0, '2026-04-03 22:23:04', '2026-04-03 22:23:04', 0.00),
(482, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 50.00, '2026-04-03 22:24:16', 0, 0, '2026-04-03 22:24:16', '2026-04-03 22:24:16', 0.00),
(483, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:25:01', 0, 0, '2026-04-03 22:25:01', '2026-04-03 22:25:01', 0.00),
(484, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:25:15', 0, 0, '2026-04-03 22:25:15', '2026-04-03 22:25:15', 0.00),
(485, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:25:26', 0, 0, '2026-04-03 22:25:26', '2026-04-03 22:25:26', 0.00),
(486, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-03 22:25:44', 0, 0, '2026-04-03 22:25:44', '2026-04-03 22:25:44', 0.00),
(487, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 25.00, '2026-04-03 22:26:00', 0, 0, '2026-04-03 22:26:00', '2026-04-03 22:26:00', 0.00),
(488, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 11.00, '2026-04-03 22:26:23', 0, 0, '2026-04-03 22:26:23', '2026-04-03 22:26:23', 0.00),
(489, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:26:57', 0, 0, '2026-04-03 22:26:57', '2026-04-03 22:26:57', 0.00),
(490, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:27:12', 0, 0, '2026-04-03 22:27:12', '2026-04-03 22:27:12', 0.00),
(491, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 50.00, '2026-04-03 22:27:30', 0, 0, '2026-04-03 22:27:30', '2026-04-03 22:27:30', 0.00),
(492, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-03 22:27:47', 0, 0, '2026-04-03 22:27:47', '2026-04-03 22:27:47', 0.00),
(493, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:28:04', 0, 0, '2026-04-03 22:28:04', '2026-04-03 22:28:04', 0.00),
(494, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 5.00, '2026-04-03 22:28:27', 0, 0, '2026-04-03 22:28:27', '2026-04-03 22:28:27', 0.00),
(495, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:28:40', 0, 0, '2026-04-03 22:28:40', '2026-04-03 22:28:40', 0.00),
(496, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:28:52', 0, 0, '2026-04-03 22:28:52', '2026-04-03 22:28:52', 0.00),
(497, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:30:13', 0, 0, '2026-04-03 22:30:13', '2026-04-03 22:30:13', 0.00),
(498, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 50.00, '2026-04-03 22:30:33', 0, 0, '2026-04-03 22:30:33', '2026-04-03 22:30:33', 0.00),
(499, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.00, '2026-04-03 22:31:01', 0, 0, '2026-04-03 22:31:01', '2026-04-03 22:31:01', 0.00),
(500, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 5.00, '2026-04-03 22:32:22', 0, 0, '2026-04-03 22:32:22', '2026-04-03 22:32:22', 0.00),
(501, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:32:39', 0, 0, '2026-04-03 22:32:39', '2026-04-03 22:32:39', 0.00),
(502, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:32:51', 0, 0, '2026-04-03 22:32:51', '2026-04-03 22:32:51', 0.00),
(503, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 34.00, '2026-04-03 22:33:08', 0, 0, '2026-04-03 22:33:08', '2026-04-03 22:33:08', 0.00),
(504, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:33:19', 0, 0, '2026-04-03 22:33:19', '2026-04-03 22:33:19', 0.00),
(505, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:34:25', 0, 0, '2026-04-03 22:34:25', '2026-04-03 22:34:25', 0.00),
(506, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:34:36', 0, 0, '2026-04-03 22:34:36', '2026-04-03 22:34:36', 0.00),
(507, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.00, '2026-04-03 22:34:48', 0, 0, '2026-04-03 22:34:48', '2026-04-03 22:34:48', 0.00),
(508, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-03 22:35:21', 0, 0, '2026-04-03 22:35:21', '2026-04-03 22:35:21', 0.00),
(509, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:35:32', 0, 0, '2026-04-03 22:35:32', '2026-04-03 22:35:32', 0.00),
(510, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-03 22:35:47', 0, 0, '2026-04-03 22:35:47', '2026-04-03 22:35:47', 0.00),
(511, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:36:33', 0, 0, '2026-04-03 22:36:33', '2026-04-03 22:36:33', 0.00),
(512, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 5.00, '2026-04-03 22:36:44', 0, 0, '2026-04-03 22:36:44', '2026-04-03 22:36:44', 0.00),
(513, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-03 22:36:55', 0, 0, '2026-04-03 22:36:55', '2026-04-03 22:36:55', 0.00),
(514, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-03 22:37:07', 0, 0, '2026-04-03 22:37:07', '2026-04-03 22:37:07', 0.00),
(515, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:38:47', 0, 0, '2026-04-03 22:38:47', '2026-04-03 22:38:47', 0.00),
(516, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:38:58', 0, 0, '2026-04-03 22:38:58', '2026-04-03 22:38:58', 0.00),
(517, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 14.00, '2026-04-03 22:39:11', 0, 0, '2026-04-03 22:39:11', '2026-04-03 22:39:11', 0.00),
(518, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:39:22', 0, 0, '2026-04-03 22:39:22', '2026-04-03 22:39:22', 0.00),
(519, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-03 22:39:53', 0, 0, '2026-04-03 22:39:53', '2026-04-03 22:39:53', 0.00),
(520, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:40:05', 0, 0, '2026-04-03 22:40:05', '2026-04-03 22:40:05', 0.00),
(521, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 9.00, '2026-04-03 22:40:17', 0, 0, '2026-04-03 22:40:17', '2026-04-03 22:40:17', 0.00),
(522, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-03 22:40:47', 0, 0, '2026-04-03 22:40:47', '2026-04-03 22:40:47', 0.00),
(523, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 18.00, '2026-04-03 22:41:00', 0, 0, '2026-04-03 22:41:00', '2026-04-03 22:41:00', 0.00),
(524, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 1550.00, '2026-04-03 22:43:50', 0, 0, '2026-04-03 22:43:50', '2026-04-03 22:43:50', 0.00),
(525, 6, 3, NULL, 'TRANSPORTES Y SERVICIOS LUCELINA S.A.C.', 'CAM-865', NULL, 1332.00, '2026-04-03 22:49:09', 2, 0, '2026-04-03 22:49:09', '2026-04-03 22:49:09', 0.00),
(526, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 80.00, '2026-04-03 22:50:35', 0, 0, '2026-04-03 22:50:35', '2026-04-03 22:50:35', 0.00),
(527, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 200.00, '2026-04-03 22:55:48', 0, 0, '2026-04-03 22:55:48', '2026-04-03 22:55:48', 0.00),
(528, 6, 3, NULL, 'OCAÑA IZQUIERDO HECTOR EDGARDO', 'ASA-880', NULL, 1298.21, '2026-04-03 22:59:50', 2, 0, '2026-04-03 22:59:50', '2026-04-03 22:59:50', 0.00),
(529, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 500.00, '2026-04-03 23:01:37', 0, 0, '2026-04-03 23:01:37', '2026-04-03 23:01:37', 0.00),
(530, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 137.00, '2026-04-03 23:20:39', 0, 0, '2026-04-03 23:20:39', '2026-04-03 23:20:39', 0.00),
(531, 7, 3, NULL, NULL, NULL, NULL, 150.00, '2026-04-03 23:29:17', 0, 0, '2026-04-03 23:29:17', '2026-04-03 23:29:17', 0.00),
(532, 7, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 23:29:40', 0, 0, '2026-04-03 23:29:40', '2026-04-03 23:29:40', 0.00),
(533, 7, 3, NULL, NULL, NULL, NULL, 75.00, '2026-04-03 23:29:56', 0, 0, '2026-04-03 23:29:56', '2026-04-03 23:29:56', 0.00),
(534, 7, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 23:30:29', 0, 0, '2026-04-03 23:30:29', '2026-04-03 23:30:29', 0.00),
(535, 7, 3, NULL, NULL, NULL, NULL, 40.00, '2026-04-03 23:30:49', 0, 0, '2026-04-03 23:30:49', '2026-04-03 23:30:49', 0.00),
(536, 7, 3, NULL, NULL, NULL, NULL, 35.00, '2026-04-03 23:31:12', 0, 0, '2026-04-03 23:31:12', '2026-04-03 23:31:12', 0.00),
(537, 7, 3, NULL, NULL, NULL, NULL, 217.00, '2026-04-03 23:31:39', 0, 0, '2026-04-03 23:31:39', '2026-04-03 23:31:39', 0.00),
(538, 7, 3, NULL, NULL, NULL, NULL, 13.00, '2026-04-03 23:31:53', 0, 0, '2026-04-03 23:31:53', '2026-04-03 23:31:53', 0.00),
(539, 7, 3, NULL, NULL, NULL, NULL, 30.10, '2026-04-03 23:32:12', 0, 0, '2026-04-03 23:32:12', '2026-04-03 23:32:12', 0.00),
(540, 7, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-03 23:32:32', 0, 0, '2026-04-03 23:32:32', '2026-04-03 23:32:32', 0.00),
(541, 7, 3, NULL, NULL, NULL, NULL, 65.00, '2026-04-03 23:33:21', 0, 0, '2026-04-03 23:33:21', '2026-04-03 23:33:21', 0.00),
(542, 7, 3, NULL, NULL, NULL, NULL, 75.00, '2026-04-03 23:34:43', 0, 0, '2026-04-03 23:34:43', '2026-04-03 23:34:43', 0.00),
(543, 7, 3, NULL, NULL, NULL, NULL, 45.00, '2026-04-03 23:35:12', 0, 0, '2026-04-03 23:35:12', '2026-04-03 23:35:12', 0.00),
(544, 7, 3, NULL, NULL, NULL, NULL, 16.00, '2026-04-03 23:35:37', 0, 0, '2026-04-03 23:35:37', '2026-04-03 23:35:37', 0.00),
(545, 7, 3, NULL, NULL, NULL, NULL, 17.00, '2026-04-03 23:35:53', 0, 0, '2026-04-03 23:35:53', '2026-04-03 23:35:53', 0.00),
(546, 7, 3, NULL, NULL, NULL, NULL, 80.00, '2026-04-03 23:36:39', 0, 0, '2026-04-03 23:36:39', '2026-04-03 23:36:39', 0.00),
(547, 7, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-03 23:37:47', 0, 0, '2026-04-03 23:37:47', '2026-04-03 23:37:47', 0.00),
(548, 7, 3, NULL, NULL, NULL, NULL, 55.00, '2026-04-03 23:38:19', 0, 0, '2026-04-03 23:38:19', '2026-04-03 23:38:19', 0.00),
(549, 7, 3, NULL, NULL, NULL, NULL, 32.00, '2026-04-03 23:38:57', 0, 0, '2026-04-03 23:38:57', '2026-04-03 23:38:57', 0.00),
(550, 7, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-03 23:39:15', 0, 0, '2026-04-03 23:39:15', '2026-04-03 23:39:15', 0.00),
(551, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-03 23:39:29', 0, 0, '2026-04-03 23:39:29', '2026-04-03 23:39:29', 0.00),
(552, 7, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-03 23:39:48', 0, 0, '2026-04-03 23:39:48', '2026-04-03 23:39:48', 0.00),
(553, 7, 3, NULL, NULL, NULL, NULL, 137.90, '2026-04-03 23:40:11', 0, 0, '2026-04-03 23:40:11', '2026-04-03 23:40:11', 0.00),
(554, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 300.00, '2026-04-04 06:19:21', 0, 0, '2026-04-04 06:19:21', '2026-04-04 06:19:21', 0.00),
(555, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 600.00, '2026-04-04 06:19:34', 0, 0, '2026-04-04 06:19:34', '2026-04-04 06:19:34', 0.00),
(556, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 40.00, '2026-04-04 06:22:18', 0, 0, '2026-04-04 06:22:18', '2026-04-04 06:22:18', 0.00),
(557, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 200.00, '2026-04-04 06:22:48', 0, 0, '2026-04-04 06:22:48', '2026-04-04 06:22:48', 0.00),
(558, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 285.00, '2026-04-04 06:23:27', 0, 0, '2026-04-04 06:23:27', '2026-04-04 06:23:27', 0.00),
(559, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-04 06:26:45', 0, 0, '2026-04-04 06:26:45', '2026-04-04 06:26:45', 0.00),
(560, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 14.00, '2026-04-04 06:26:59', 0, 0, '2026-04-04 06:26:59', '2026-04-04 06:26:59', 0.00),
(561, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.00, '2026-04-04 06:27:26', 0, 0, '2026-04-04 06:27:26', '2026-04-04 06:27:26', 0.00),
(562, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.00, '2026-04-04 06:27:45', 0, 0, '2026-04-04 06:27:45', '2026-04-04 06:27:45', 0.00),
(563, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 13.00, '2026-04-04 06:28:03', 0, 0, '2026-04-04 06:28:03', '2026-04-04 06:28:03', 0.00),
(564, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-04 06:28:20', 0, 0, '2026-04-04 06:28:20', '2026-04-04 06:28:20', 0.00),
(565, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-04 06:31:13', 0, 0, '2026-04-04 06:31:13', '2026-04-04 06:31:13', 0.00),
(566, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 35.00, '2026-04-04 06:31:33', 0, 0, '2026-04-04 06:31:33', '2026-04-04 06:31:33', 0.00),
(567, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-04 06:41:47', 0, 0, '2026-04-04 06:41:47', '2026-04-04 06:41:47', 0.00),
(568, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-04 06:57:06', 0, 0, '2026-04-04 06:57:06', '2026-04-04 06:57:06', 0.00),
(569, 7, 3, NULL, NULL, NULL, NULL, 30.37, '2026-04-04 06:57:22', 0, 0, '2026-04-04 06:57:22', '2026-04-04 06:57:22', 0.00),
(570, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-04 06:57:39', 0, 0, '2026-04-04 06:57:39', '2026-04-04 06:57:39', 0.00),
(571, 7, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-04 06:58:19', 0, 0, '2026-04-04 06:58:19', '2026-04-04 06:58:19', 0.00),
(572, 7, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-04 06:58:32', 0, 0, '2026-04-04 06:58:32', '2026-04-04 06:58:32', 0.00),
(573, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 190.00, '2026-04-04 07:03:25', 0, 0, '2026-04-04 07:03:25', '2026-04-04 07:03:25', 0.00),
(574, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 40.00, '2026-04-04 07:03:45', 0, 0, '2026-04-04 07:03:45', '2026-04-04 07:03:45', 0.00),
(575, 7, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-04 07:04:20', 0, 0, '2026-04-04 07:04:20', '2026-04-04 07:04:20', 0.00),
(576, 7, 3, NULL, NULL, NULL, NULL, 15.00, '2026-04-04 07:04:34', 0, 0, '2026-04-04 07:04:34', '2026-04-04 07:04:34', 0.00),
(577, 7, 3, NULL, NULL, NULL, NULL, 65.00, '2026-04-04 07:04:52', 0, 0, '2026-04-04 07:04:52', '2026-04-04 07:04:52', 0.00),
(578, 7, 3, NULL, NULL, NULL, NULL, 38.00, '2026-04-04 07:05:15', 0, 0, '2026-04-04 07:05:15', '2026-04-04 07:05:15', 0.00),
(579, 7, 3, NULL, NULL, NULL, NULL, 252.00, '2026-04-04 07:06:53', 0, 0, '2026-04-04 07:06:53', '2026-04-04 07:06:53', 0.00),
(580, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-04 07:07:02', 0, 0, '2026-04-04 07:07:02', '2026-04-04 07:07:02', 0.00),
(581, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-04 07:07:17', 0, 0, '2026-04-04 07:07:17', '2026-04-04 07:07:17', 0.00),
(582, 7, 3, NULL, NULL, NULL, NULL, 78.00, '2026-04-04 07:07:20', 0, 0, '2026-04-04 07:07:20', '2026-04-04 07:07:20', 0.00),
(583, 6, 3, NULL, NULL, NULL, NULL, 58.00, '2026-04-04 23:20:21', 0, 0, '2026-04-04 23:20:21', '2026-04-04 23:20:21', 0.00),
(584, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-04 23:21:08', 0, 0, '2026-04-04 23:21:08', '2026-04-04 23:21:08', 0.00),
(585, 6, 3, NULL, NULL, NULL, NULL, 140.00, '2026-04-04 23:22:51', 0, 0, '2026-04-04 23:22:51', '2026-04-04 23:22:51', 0.00),
(586, 6, 3, NULL, NULL, NULL, NULL, 200.00, '2026-04-04 23:24:10', 0, 0, '2026-04-04 23:24:10', '2026-04-04 23:24:10', 0.00),
(587, 6, 3, NULL, NULL, NULL, NULL, 72.40, '2026-04-04 23:28:51', 0, 0, '2026-04-04 23:28:51', '2026-04-04 23:28:51', 0.00),
(588, 6, 3, NULL, NULL, NULL, NULL, 25.00, '2026-04-04 23:30:14', 0, 0, '2026-04-04 23:30:14', '2026-04-04 23:30:14', 0.00),
(589, 6, 3, NULL, NULL, NULL, NULL, 95.00, '2026-04-04 23:53:13', 0, 0, '2026-04-04 23:53:13', '2026-04-04 23:53:13', 0.00),
(590, 6, 3, NULL, NULL, NULL, NULL, 288.00, '2026-04-04 23:54:00', 0, 0, '2026-04-04 23:54:00', '2026-04-04 23:54:00', 0.00),
(591, 6, 3, NULL, NULL, NULL, NULL, 120.00, '2026-04-04 23:55:34', 0, 0, '2026-04-04 23:55:34', '2026-04-04 23:55:34', 0.00),
(592, 6, 3, NULL, NULL, NULL, NULL, 230.00, '2026-04-04 23:56:00', 0, 0, '2026-04-04 23:56:00', '2026-04-04 23:56:00', 0.00),
(593, 6, 3, NULL, NULL, NULL, NULL, 290.00, '2026-04-04 23:56:40', 0, 0, '2026-04-04 23:56:40', '2026-04-04 23:56:40', 0.00),
(594, 6, 3, NULL, NULL, NULL, NULL, 45.00, '2026-04-04 23:57:05', 0, 0, '2026-04-04 23:57:05', '2026-04-04 23:57:05', 0.00),
(595, 6, 3, NULL, NULL, NULL, NULL, 33.00, '2026-04-04 23:59:51', 0, 0, '2026-04-04 23:59:51', '2026-04-04 23:59:51', 0.00),
(596, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-05 00:00:23', 0, 0, '2026-04-05 00:00:23', '2026-04-05 00:00:23', 0.00),
(597, 6, 3, NULL, NULL, NULL, NULL, 66.00, '2026-04-05 00:01:32', 0, 0, '2026-04-05 00:01:32', '2026-04-05 00:01:32', 0.00),
(598, 6, 3, NULL, NULL, NULL, NULL, 37.00, '2026-04-05 00:02:10', 0, 0, '2026-04-05 00:02:10', '2026-04-05 00:02:10', 0.00),
(599, 6, 3, NULL, NULL, NULL, NULL, 70.00, '2026-04-05 00:03:04', 0, 0, '2026-04-05 00:03:04', '2026-04-05 00:03:04', 0.00),
(600, 6, 3, NULL, NULL, NULL, NULL, 25.00, '2026-04-05 00:03:58', 0, 0, '2026-04-05 00:03:58', '2026-04-05 00:03:58', 0.00),
(601, 6, 3, NULL, NULL, NULL, NULL, 48.50, '2026-04-05 00:04:29', 0, 0, '2026-04-05 00:04:29', '2026-04-05 00:04:29', 0.00),
(602, 6, 3, NULL, NULL, NULL, NULL, 50.10, '2026-04-05 00:05:27', 0, 0, '2026-04-05 00:05:27', '2026-04-05 00:05:27', 0.00),
(603, 6, 3, NULL, NULL, NULL, NULL, 35.00, '2026-04-05 00:06:51', 0, 0, '2026-04-05 00:06:51', '2026-04-05 00:06:51', 0.00),
(604, 6, 3, NULL, NULL, NULL, NULL, 42.00, '2026-04-05 00:07:25', 0, 0, '2026-04-05 00:07:25', '2026-04-05 00:07:25', 0.00),
(605, 6, 3, NULL, NULL, NULL, NULL, 115.00, '2026-04-05 00:08:19', 0, 0, '2026-04-05 00:08:19', '2026-04-05 00:08:19', 0.00),
(606, 6, 3, NULL, NULL, NULL, NULL, 220.00, '2026-04-05 00:08:50', 0, 0, '2026-04-05 00:08:50', '2026-04-05 00:08:50', 0.00),
(607, 6, 3, NULL, NULL, NULL, NULL, 22.50, '2026-04-05 00:13:55', 0, 0, '2026-04-05 00:13:55', '2026-04-05 00:13:55', 0.00),
(608, 6, 3, NULL, NULL, NULL, NULL, 45.00, '2026-04-05 00:14:33', 0, 0, '2026-04-05 00:14:33', '2026-04-05 00:14:33', 0.00),
(609, 6, 3, NULL, NULL, NULL, NULL, 55.00, '2026-04-05 00:17:20', 0, 0, '2026-04-05 00:17:20', '2026-04-05 00:17:20', 0.00),
(610, 6, 3, NULL, NULL, NULL, NULL, 266.00, '2026-04-05 00:17:50', 0, 0, '2026-04-05 00:17:50', '2026-04-05 00:17:50', 0.00),
(611, 6, 3, NULL, NULL, NULL, NULL, 300.00, '2026-04-05 00:43:21', 0, 0, '2026-04-05 00:43:21', '2026-04-05 00:43:21', 0.00),
(612, 6, 3, NULL, NULL, NULL, NULL, 840.00, '2026-04-05 00:43:58', 0, 0, '2026-04-05 00:43:58', '2026-04-05 00:43:58', 0.00),
(613, 6, 3, NULL, 'LUZ ANGELICA', 'T7L-813', NULL, 2184.88, '2026-04-05 00:47:51', 2, 0, '2026-04-05 00:47:51', '2026-04-05 00:47:51', 0.00),
(614, 6, 3, NULL, 'VALERIA', 'BMZ-746', NULL, 2886.00, '2026-04-05 00:49:23', 2, 0, '2026-04-05 00:49:23', '2026-04-05 00:49:23', 0.00),
(615, 7, 3, NULL, NULL, NULL, NULL, 88.00, '2026-04-05 00:58:08', 0, 0, '2026-04-05 00:58:08', '2026-04-05 00:58:08', 0.00),
(616, 7, 3, NULL, NULL, NULL, NULL, 160.00, '2026-04-05 00:58:50', 0, 0, '2026-04-05 00:58:50', '2026-04-05 00:58:50', 0.00),
(617, 7, 3, NULL, NULL, NULL, NULL, 80.00, '2026-04-05 01:00:12', 0, 0, '2026-04-05 01:00:12', '2026-04-05 01:00:12', 0.00),
(618, 7, 3, NULL, NULL, NULL, NULL, 60.00, '2026-04-05 01:01:00', 0, 0, '2026-04-05 01:01:00', '2026-04-05 01:01:00', 0.00),
(619, 7, 3, NULL, NULL, NULL, NULL, 70.00, '2026-04-05 01:01:31', 0, 0, '2026-04-05 01:01:31', '2026-04-05 01:01:31', 0.00),
(620, 7, 3, NULL, NULL, NULL, NULL, 150.00, '2026-04-05 01:02:06', 0, 0, '2026-04-05 01:02:06', '2026-04-05 01:02:06', 0.00),
(621, 7, 3, NULL, NULL, NULL, NULL, 250.00, '2026-04-05 01:02:54', 0, 0, '2026-04-05 01:02:54', '2026-04-05 01:02:54', 0.00),
(622, 7, 3, NULL, NULL, NULL, NULL, 56.00, '2026-04-05 01:03:39', 0, 0, '2026-04-05 01:03:39', '2026-04-05 01:03:39', 0.00),
(623, 7, 3, NULL, NULL, NULL, NULL, 35.00, '2026-04-05 01:04:29', 0, 0, '2026-04-05 01:04:29', '2026-04-05 01:04:29', 0.00),
(624, 7, 3, NULL, NULL, NULL, NULL, 32.00, '2026-04-05 07:06:15', 0, 0, '2026-04-05 07:06:15', '2026-04-05 07:06:15', 0.00),
(625, 7, 3, NULL, NULL, NULL, NULL, 120.00, '2026-04-05 07:07:10', 0, 0, '2026-04-05 07:07:10', '2026-04-05 07:07:10', 0.00),
(626, 6, 3, NULL, NULL, NULL, NULL, 60.00, '2026-04-05 07:10:22', 0, 0, '2026-04-05 07:10:22', '2026-04-05 07:10:22', 0.00),
(627, 6, 3, NULL, NULL, NULL, NULL, 130.00, '2026-04-05 07:10:56', 0, 0, '2026-04-05 07:10:56', '2026-04-05 07:10:56', 0.00),
(628, 6, 3, NULL, NULL, NULL, NULL, 140.00, '2026-04-05 07:11:29', 0, 0, '2026-04-05 07:11:29', '2026-04-05 07:11:29', 0.00),
(629, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-05 07:11:55', 0, 0, '2026-04-05 07:11:55', '2026-04-05 07:11:55', 0.00),
(630, 6, 3, NULL, NULL, NULL, NULL, 555.00, '2026-04-05 07:12:44', 0, 0, '2026-04-05 07:12:44', '2026-04-05 07:12:44', 0.00),
(631, 6, 3, NULL, NULL, NULL, NULL, 400.00, '2026-04-05 07:12:59', 0, 0, '2026-04-05 07:12:59', '2026-04-05 07:12:59', 0.00),
(632, 6, 3, NULL, 'valeria', 'BUS-720', NULL, 2220.02, '2026-04-05 07:14:59', 2, 0, '2026-04-05 07:14:59', '2026-04-05 07:14:59', 0.00),
(633, 7, 3, NULL, NULL, NULL, NULL, 65.00, '2026-04-05 07:43:01', 0, 0, '2026-04-05 07:43:01', '2026-04-05 07:43:01', 0.00),
(634, 7, 3, NULL, NULL, NULL, NULL, 183.00, '2026-04-05 07:45:29', 0, 0, '2026-04-05 07:45:29', '2026-04-05 07:45:29', 0.00),
(635, 6, 3, NULL, NULL, NULL, NULL, 186.00, '2026-04-05 07:48:56', 0, 0, '2026-04-05 07:48:56', '2026-04-05 07:48:56', 0.00),
(636, 6, 3, NULL, NULL, NULL, NULL, 65.00, '2026-04-05 07:49:45', 0, 0, '2026-04-05 07:49:45', '2026-04-05 07:49:45', 0.00),
(637, 6, 3, NULL, NULL, NULL, NULL, 170.00, '2026-04-05 07:50:47', 0, 0, '2026-04-05 07:50:47', '2026-04-05 07:50:47', 0.00),
(638, 6, 3, NULL, NULL, NULL, NULL, 82.00, '2026-04-05 07:51:42', 0, 0, '2026-04-05 07:51:42', '2026-04-05 07:51:42', 0.00),
(639, 6, 3, NULL, NULL, NULL, NULL, 40.00, '2026-04-05 07:52:41', 0, 0, '2026-04-05 07:52:41', '2026-04-05 07:52:41', 0.00),
(640, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 14.00, '2026-04-05 22:42:20', 0, 0, '2026-04-05 22:42:20', '2026-04-05 22:42:20', 0.00),
(641, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 15.00, '2026-04-05 22:42:41', 0, 0, '2026-04-05 22:42:41', '2026-04-05 22:42:41', 0.00),
(642, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 680.00, '2026-04-05 22:44:21', 0, 0, '2026-04-05 22:44:21', '2026-04-05 22:44:21', 0.00),
(643, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 200.00, '2026-04-05 22:44:35', 0, 0, '2026-04-05 22:44:35', '2026-04-05 22:44:35', 0.00),
(644, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 80.00, '2026-04-05 22:45:47', 0, 0, '2026-04-05 22:45:47', '2026-04-05 22:45:47', 0.00),
(645, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 120.00, '2026-04-05 22:46:56', 0, 0, '2026-04-05 22:46:56', '2026-04-05 22:46:56', 0.00),
(646, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 100.00, '2026-04-05 22:47:10', 0, 0, '2026-04-05 22:47:10', '2026-04-05 22:47:10', 0.00),
(647, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 111.00, '2026-04-05 22:49:47', 0, 0, '2026-04-05 22:49:47', '2026-04-05 22:49:47', 0.00),
(648, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 120.00, '2026-04-05 22:51:09', 0, 0, '2026-04-05 22:51:09', '2026-04-05 22:51:09', 0.00),
(649, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-05 22:52:34', 0, 0, '2026-04-05 22:52:34', '2026-04-05 22:52:34', 0.00),
(650, 7, 3, NULL, NULL, NULL, NULL, 120.00, '2026-04-05 22:55:39', 0, 0, '2026-04-05 22:55:39', '2026-04-05 22:55:39', 0.00),
(651, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 100.00, '2026-04-05 22:56:13', 0, 0, '2026-04-05 22:56:13', '2026-04-05 22:56:13', 0.00);
INSERT INTO `sales` (`id`, `user_id`, `location_id`, `client_id`, `client_name`, `vehicle_plate`, `phone`, `total`, `date`, `type_sale`, `deleted`, `created_at`, `updated_at`, `adicional`) VALUES
(652, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 50.00, '2026-04-05 22:56:38', 0, 0, '2026-04-05 22:56:38', '2026-04-05 22:56:38', 0.00),
(653, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 27.00, '2026-04-05 22:57:02', 0, 0, '2026-04-05 22:57:02', '2026-04-05 22:57:02', 0.00),
(654, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 30.00, '2026-04-05 22:58:19', 0, 0, '2026-04-05 22:58:19', '2026-04-05 22:58:19', 0.00),
(655, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 150.00, '2026-04-05 22:58:40', 0, 0, '2026-04-05 22:58:40', '2026-04-05 22:58:40', 0.00),
(656, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-05 22:59:13', 0, 0, '2026-04-05 22:59:13', '2026-04-05 22:59:13', 0.00),
(657, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-05 22:59:26', 0, 0, '2026-04-05 22:59:26', '2026-04-05 22:59:26', 0.00),
(658, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-05 22:59:39', 0, 0, '2026-04-05 22:59:39', '2026-04-05 22:59:39', 0.00),
(659, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-05 22:59:52', 0, 0, '2026-04-05 22:59:52', '2026-04-05 22:59:52', 0.00),
(660, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 230.00, '2026-04-05 23:01:34', 0, 0, '2026-04-05 23:01:34', '2026-04-05 23:01:34', 0.00),
(661, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-05 23:01:53', 0, 0, '2026-04-05 23:01:53', '2026-04-05 23:01:53', 0.00),
(662, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-05 23:02:07', 0, 0, '2026-04-05 23:02:07', '2026-04-05 23:02:07', 0.00),
(663, 7, 3, NULL, NULL, NULL, NULL, 19.00, '2026-04-05 23:02:31', 0, 0, '2026-04-05 23:02:31', '2026-04-05 23:02:31', 0.00),
(664, 7, 3, NULL, NULL, NULL, NULL, 11.00, '2026-04-05 23:02:44', 0, 0, '2026-04-05 23:02:44', '2026-04-05 23:02:44', 0.00),
(665, 7, 3, NULL, NULL, NULL, NULL, 19.50, '2026-04-05 23:03:23', 0, 0, '2026-04-05 23:03:23', '2026-04-05 23:03:23', 0.00),
(666, 7, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-05 23:03:48', 0, 0, '2026-04-05 23:03:48', '2026-04-05 23:03:48', 0.00),
(667, 7, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-05 23:04:03', 0, 0, '2026-04-05 23:04:03', '2026-04-05 23:04:03', 0.00),
(668, 7, 3, NULL, NULL, NULL, NULL, 60.00, '2026-04-05 23:04:25', 0, 0, '2026-04-05 23:04:25', '2026-04-05 23:04:25', 0.00),
(669, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-05 23:07:43', 0, 0, '2026-04-05 23:07:43', '2026-04-05 23:07:43', 0.00),
(670, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-05 23:08:04', 0, 0, '2026-04-05 23:08:04', '2026-04-05 23:08:04', 0.00),
(671, 7, 3, NULL, NULL, NULL, NULL, 35.00, '2026-04-05 23:08:23', 0, 0, '2026-04-05 23:08:23', '2026-04-05 23:08:23', 0.00),
(672, 7, 3, NULL, NULL, NULL, NULL, 5.00, '2026-04-05 23:08:40', 0, 0, '2026-04-05 23:08:40', '2026-04-05 23:08:40', 0.00),
(673, 7, 3, NULL, NULL, NULL, NULL, 15.00, '2026-04-05 23:08:55', 0, 0, '2026-04-05 23:08:55', '2026-04-05 23:08:55', 0.00),
(674, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-05 23:09:09', 0, 0, '2026-04-05 23:09:09', '2026-04-05 23:09:09', 0.00),
(675, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-05 23:09:38', 0, 0, '2026-04-05 23:09:38', '2026-04-05 23:09:38', 0.00),
(676, 7, 3, NULL, NULL, NULL, NULL, 45.00, '2026-04-05 23:09:49', 0, 0, '2026-04-05 23:09:49', '2026-04-05 23:09:49', 0.00),
(677, 7, 3, NULL, NULL, NULL, NULL, 300.00, '2026-04-05 23:10:59', 0, 0, '2026-04-05 23:10:59', '2026-04-05 23:10:59', 0.00),
(678, 7, 3, NULL, NULL, NULL, NULL, 300.00, '2026-04-05 23:11:15', 0, 0, '2026-04-05 23:11:15', '2026-04-05 23:11:15', 0.00),
(679, 7, 3, NULL, NULL, NULL, NULL, 202.00, '2026-04-05 23:11:38', 0, 0, '2026-04-05 23:11:38', '2026-04-05 23:11:38', 0.00),
(680, 7, 3, NULL, NULL, NULL, NULL, 90.00, '2026-04-05 23:11:54', 0, 0, '2026-04-05 23:11:54', '2026-04-05 23:11:54', 0.00),
(681, 7, 3, NULL, NULL, NULL, NULL, 110.00, '2026-04-05 23:12:11', 0, 0, '2026-04-05 23:12:11', '2026-04-05 23:12:11', 0.00),
(682, 7, 3, NULL, NULL, NULL, NULL, 271.00, '2026-04-05 23:12:48', 0, 0, '2026-04-05 23:12:48', '2026-04-05 23:12:48', 0.00),
(683, 7, 3, NULL, NULL, NULL, NULL, 29.00, '2026-04-05 23:13:24', 0, 0, '2026-04-05 23:13:24', '2026-04-05 23:13:24', 0.00),
(684, 6, 3, NULL, NULL, NULL, NULL, 800.00, '2026-04-05 23:42:34', 0, 0, '2026-04-05 23:42:34', '2026-04-05 23:42:34', 0.00),
(685, 6, 3, NULL, NULL, NULL, NULL, 710.00, '2026-04-05 23:42:49', 0, 0, '2026-04-05 23:42:49', '2026-04-05 23:42:49', 0.00),
(686, 6, 3, NULL, NULL, NULL, NULL, 770.00, '2026-04-05 23:45:23', 0, 0, '2026-04-05 23:45:23', '2026-04-05 23:45:23', 0.00),
(687, 6, 3, NULL, 'AGUILA BERMEO DIMAR', 'C8A-864', NULL, 888.00, '2026-04-05 23:46:37', 2, 0, '2026-04-05 23:46:37', '2026-04-05 23:46:37', 0.00),
(688, 6, 3, NULL, NULL, NULL, NULL, 700.00, '2026-04-05 23:47:56', 0, 0, '2026-04-05 23:47:56', '2026-04-05 23:47:56', 0.00),
(689, 6, 3, NULL, 'FERRETERIA CASA FUERTE E.I.R.L.', 'BKU-847', NULL, 2500.00, '2026-04-05 23:51:05', 2, 0, '2026-04-05 23:51:05', '2026-04-05 23:51:05', 0.00),
(694, 6, 3, NULL, NULL, NULL, NULL, 85.00, '2026-04-05 23:53:23', 0, 0, '2026-04-05 23:53:23', '2026-04-05 23:53:23', 0.00),
(699, 6, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 32.00, '2026-04-06 00:00:38', 0, 0, '2026-04-06 00:00:38', '2026-04-06 00:00:38', 0.00),
(702, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-06 00:04:23', 0, 0, '2026-04-06 00:04:23', '2026-04-06 00:04:23', 0.00),
(706, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-06 00:07:10', 0, 0, '2026-04-06 00:07:10', '2026-04-06 00:07:10', 0.00),
(707, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-06 00:07:31', 0, 0, '2026-04-06 00:07:31', '2026-04-06 00:07:31', 0.00),
(708, 6, 3, NULL, NULL, NULL, NULL, 94.00, '2026-04-06 00:08:08', 0, 0, '2026-04-06 00:08:08', '2026-04-06 00:08:08', 0.00),
(709, 6, 3, NULL, NULL, NULL, NULL, 100.00, '2026-04-06 00:08:30', 0, 0, '2026-04-06 00:08:30', '2026-04-06 00:08:30', 0.00),
(710, 6, 3, NULL, NULL, NULL, NULL, 28.00, '2026-04-06 00:09:32', 0, 0, '2026-04-06 00:09:32', '2026-04-06 00:09:32', 0.00),
(711, 6, 3, NULL, NULL, NULL, NULL, 33.00, '2026-04-06 00:09:55', 0, 0, '2026-04-06 00:09:55', '2026-04-06 00:09:55', 0.00),
(712, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-06 00:10:18', 0, 0, '2026-04-06 00:10:18', '2026-04-06 00:10:18', 0.00),
(713, 6, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-06 00:10:50', 0, 0, '2026-04-06 00:10:50', '2026-04-06 00:10:50', 0.00),
(714, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-06 00:11:06', 0, 0, '2026-04-06 00:11:06', '2026-04-06 00:11:06', 0.00),
(715, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-06 00:11:24', 0, 0, '2026-04-06 00:11:24', '2026-04-06 00:11:24', 0.00),
(716, 6, 3, NULL, NULL, NULL, NULL, 25.13, '2026-04-06 00:11:44', 0, 0, '2026-04-06 00:11:44', '2026-04-06 00:11:44', 0.00),
(717, 6, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-06 00:12:14', 0, 0, '2026-04-06 00:12:14', '2026-04-06 00:12:14', 0.00),
(718, 6, 3, NULL, NULL, NULL, NULL, 42.00, '2026-04-06 00:12:30', 0, 0, '2026-04-06 00:12:30', '2026-04-06 00:12:30', 0.00),
(719, 6, 3, NULL, NULL, NULL, NULL, 35.00, '2026-04-06 00:13:07', 0, 0, '2026-04-06 00:13:07', '2026-04-06 00:13:07', 0.00),
(720, 6, 3, NULL, NULL, NULL, NULL, 44.00, '2026-04-06 00:13:25', 0, 0, '2026-04-06 00:13:25', '2026-04-06 00:13:25', 0.00),
(721, 6, 3, NULL, NULL, NULL, NULL, 140.00, '2026-04-06 00:15:22', 0, 0, '2026-04-06 00:15:22', '2026-04-06 00:15:22', 0.00),
(722, 6, 3, NULL, NULL, NULL, NULL, 80.00, '2026-04-06 00:15:50', 0, 0, '2026-04-06 00:15:50', '2026-04-06 00:15:50', 0.00),
(724, 6, 3, NULL, NULL, NULL, NULL, 55.00, '2026-04-06 00:17:02', 0, 0, '2026-04-06 00:17:02', '2026-04-06 00:17:02', 0.00),
(725, 6, 3, NULL, NULL, NULL, NULL, 35.00, '2026-04-06 00:17:23', 0, 0, '2026-04-06 00:17:23', '2026-04-06 00:17:23', 0.00),
(726, 6, 3, NULL, NULL, NULL, NULL, 35.00, '2026-04-06 00:17:42', 0, 0, '2026-04-06 00:17:42', '2026-04-06 00:17:42', 0.00),
(727, 6, 3, NULL, NULL, NULL, NULL, 120.00, '2026-04-06 00:18:07', 0, 0, '2026-04-06 00:18:07', '2026-04-06 00:18:07', 0.00),
(728, 6, 3, NULL, NULL, NULL, NULL, 70.00, '2026-04-06 00:18:24', 0, 0, '2026-04-06 00:18:24', '2026-04-06 00:18:24', 0.00),
(729, 6, 3, NULL, NULL, NULL, NULL, 30.00, '2026-04-06 00:18:48', 0, 0, '2026-04-06 00:18:48', '2026-04-06 00:18:48', 0.00),
(730, 6, 3, NULL, NULL, NULL, NULL, 64.00, '2026-04-06 00:21:16', 0, 0, '2026-04-06 00:21:16', '2026-04-06 00:21:16', 0.00),
(731, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-06 00:21:32', 0, 0, '2026-04-06 00:21:32', '2026-04-06 00:21:32', 0.00),
(732, 6, 3, NULL, NULL, NULL, NULL, 25.00, '2026-04-06 00:22:01', 0, 0, '2026-04-06 00:22:01', '2026-04-06 00:22:01', 0.00),
(733, 6, 3, NULL, NULL, NULL, NULL, 51.00, '2026-04-06 00:22:18', 0, 0, '2026-04-06 00:22:18', '2026-04-06 00:22:18', 0.00),
(734, 6, 3, NULL, NULL, NULL, NULL, 56.80, '2026-04-06 00:22:41', 0, 0, '2026-04-06 00:22:41', '2026-04-06 00:22:41', 0.00),
(735, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-06 00:23:10', 0, 0, '2026-04-06 00:23:10', '2026-04-06 00:23:10', 0.00),
(739, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 20.00, '2026-04-06 06:10:46', 0, 0, '2026-04-06 06:10:46', '2026-04-06 06:10:46', 0.00),
(740, 7, 3, NULL, NULL, NULL, NULL, 13.00, '2026-04-06 06:11:46', 0, 0, '2026-04-06 06:11:46', '2026-04-06 06:11:46', 0.00),
(741, 7, 3, NULL, 'CLIENTES VARIOS', NULL, NULL, 10.00, '2026-04-06 06:12:33', 0, 0, '2026-04-06 06:12:33', '2026-04-06 06:12:33', 0.00),
(742, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-06 06:13:04', 0, 0, '2026-04-06 06:13:04', '2026-04-06 06:13:04', 0.00),
(743, 7, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-06 06:13:23', 0, 0, '2026-04-06 06:13:23', '2026-04-06 06:13:23', 0.00),
(744, 7, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-06 06:14:28', 0, 0, '2026-04-06 06:14:28', '2026-04-06 06:14:28', 0.00),
(745, 7, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-06 06:14:48', 0, 0, '2026-04-06 06:14:48', '2026-04-06 06:14:48', 0.00),
(746, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-06 07:02:45', 0, 0, '2026-04-06 07:02:45', '2026-04-06 07:02:45', 0.00),
(747, 6, 3, NULL, NULL, NULL, NULL, 26.00, '2026-04-06 07:02:59', 0, 0, '2026-04-06 07:02:59', '2026-04-06 07:02:59', 0.00),
(753, 6, 3, NULL, NULL, NULL, NULL, 32.00, '2026-04-07 06:02:36', 0, 0, '2026-04-07 06:02:36', '2026-04-07 06:02:36', 0.00),
(754, 6, 3, NULL, NULL, NULL, NULL, 95.00, '2026-04-07 06:03:30', 0, 0, '2026-04-07 06:03:30', '2026-04-07 06:03:30', 0.00),
(755, 6, 3, NULL, NULL, NULL, NULL, 67.00, '2026-04-07 06:03:57', 0, 0, '2026-04-07 06:03:57', '2026-04-07 06:03:57', 0.00),
(756, 6, 3, NULL, NULL, NULL, NULL, 25.00, '2026-04-07 06:04:32', 0, 0, '2026-04-07 06:04:32', '2026-04-07 06:04:32', 0.00),
(757, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-07 06:05:25', 0, 0, '2026-04-07 06:05:25', '2026-04-07 06:05:25', 0.00),
(758, 6, 3, NULL, NULL, NULL, NULL, 95.00, '2026-04-07 06:06:17', 0, 0, '2026-04-07 06:06:17', '2026-04-07 06:06:17', 0.00),
(759, 6, 3, NULL, NULL, NULL, NULL, 69.00, '2026-04-07 06:06:50', 0, 0, '2026-04-07 06:06:50', '2026-04-07 06:06:50', 0.00),
(760, 6, 3, NULL, NULL, NULL, NULL, 40.00, '2026-04-07 06:07:21', 0, 0, '2026-04-07 06:07:21', '2026-04-07 06:07:21', 0.00),
(761, 6, 3, NULL, NULL, NULL, NULL, 40.00, '2026-04-07 06:07:58', 0, 0, '2026-04-07 06:07:58', '2026-04-07 06:07:58', 0.00),
(762, 6, 3, NULL, NULL, NULL, NULL, 173.00, '2026-04-07 06:09:41', 0, 0, '2026-04-07 06:09:41', '2026-04-07 06:09:41', 0.00),
(763, 6, 3, NULL, NULL, NULL, NULL, 92.00, '2026-04-07 06:10:15', 0, 0, '2026-04-07 06:10:15', '2026-04-07 06:10:15', 0.00),
(764, 6, 3, NULL, NULL, NULL, NULL, 150.00, '2026-04-07 06:10:53', 0, 0, '2026-04-07 06:10:53', '2026-04-07 06:10:53', 0.00),
(765, 6, 3, NULL, NULL, NULL, NULL, 155.00, '2026-04-07 06:11:24', 0, 0, '2026-04-07 06:11:24', '2026-04-07 06:11:24', 0.00),
(766, 6, 3, NULL, NULL, NULL, NULL, 45.00, '2026-04-07 06:12:34', 0, 0, '2026-04-07 06:12:34', '2026-04-07 06:12:34', 0.00),
(767, 6, 3, NULL, NULL, NULL, NULL, 170.00, '2026-04-07 06:12:56', 0, 0, '2026-04-07 06:12:56', '2026-04-07 06:12:56', 0.00),
(768, 6, 3, NULL, NULL, NULL, NULL, 70.00, '2026-04-07 06:14:10', 0, 0, '2026-04-07 06:14:10', '2026-04-07 06:14:10', 0.00),
(769, 6, 3, NULL, NULL, NULL, NULL, 92.50, '2026-04-07 06:14:51', 0, 0, '2026-04-07 06:14:51', '2026-04-07 06:14:51', 0.00),
(770, 6, 3, NULL, NULL, NULL, NULL, 60.00, '2026-04-07 06:16:22', 0, 0, '2026-04-07 06:16:22', '2026-04-07 06:16:22', 0.00),
(771, 6, 3, NULL, NULL, NULL, NULL, 238.00, '2026-04-07 06:16:45', 0, 0, '2026-04-07 06:16:45', '2026-04-07 06:16:45', 0.00),
(772, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-07 06:17:04', 0, 0, '2026-04-07 06:17:04', '2026-04-07 06:17:04', 0.00),
(773, 6, 3, NULL, NULL, NULL, NULL, 90.00, '2026-04-07 06:17:24', 0, 0, '2026-04-07 06:17:24', '2026-04-07 06:17:24', 0.00),
(774, 6, 3, NULL, NULL, NULL, NULL, 10.00, '2026-04-07 06:17:51', 0, 0, '2026-04-07 06:17:51', '2026-04-07 06:17:51', 0.00),
(775, 6, 3, NULL, NULL, NULL, NULL, 230.00, '2026-04-07 06:25:28', 0, 0, '2026-04-07 06:25:28', '2026-04-07 06:25:28', 0.00),
(776, 6, 3, NULL, NULL, NULL, NULL, 1304.00, '2026-04-07 06:25:48', 0, 0, '2026-04-07 06:25:48', '2026-04-07 06:25:48', 0.00),
(777, 6, 3, NULL, NULL, NULL, NULL, 500.00, '2026-04-07 06:26:17', 0, 0, '2026-04-07 06:26:17', '2026-04-07 06:26:17', 0.00),
(778, 6, 3, NULL, NULL, NULL, NULL, 292.00, '2026-04-07 06:26:40', 0, 0, '2026-04-07 06:26:40', '2026-04-07 06:26:40', 0.00),
(779, 6, 3, NULL, NULL, NULL, NULL, 1110.00, '2026-04-07 06:32:44', 0, 0, '2026-04-07 06:32:44', '2026-04-07 06:32:44', 0.00),
(780, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-07 07:09:16', 0, 0, '2026-04-07 07:09:16', '2026-04-07 07:09:16', 0.00),
(781, 6, 3, NULL, NULL, NULL, NULL, 108.00, '2026-04-07 07:10:02', 0, 0, '2026-04-07 07:10:02', '2026-04-07 07:10:02', 0.00),
(782, 6, 3, NULL, NULL, NULL, NULL, 50.00, '2026-04-07 07:12:42', 0, 0, '2026-04-07 07:12:42', '2026-04-07 07:12:42', 0.00),
(783, 6, 3, NULL, NULL, NULL, NULL, 130.00, '2026-04-07 07:13:30', 0, 0, '2026-04-07 07:13:30', '2026-04-07 07:13:30', 0.00),
(784, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-07 07:17:05', 0, 0, '2026-04-07 07:17:05', '2026-04-07 07:17:05', 0.00),
(785, 6, 3, NULL, NULL, NULL, NULL, 20.00, '2026-04-07 07:17:22', 0, 0, '2026-04-07 07:17:22', '2026-04-07 07:17:22', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_details`
--

CREATE TABLE `sale_details` (
  `product_id` bigint UNSIGNED NOT NULL,
  `sale_id` bigint UNSIGNED NOT NULL,
  `order_detail_id` bigint UNSIGNED DEFAULT NULL,
  `pump_id` bigint UNSIGNED DEFAULT NULL,
  `truck_id` bigint UNSIGNED DEFAULT NULL,
  `closing_number` tinyint UNSIGNED DEFAULT NULL,
  `quantity` decimal(10,3) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sale_details`
--

INSERT INTO `sale_details` (`product_id`, `sale_id`, `order_detail_id`, `pump_id`, `truck_id`, `closing_number`, `quantity`, `unit_price`, `discounted_price`, `subtotal`, `deleted`) VALUES
(12, 539, NULL, 30, NULL, NULL, 1.544, 19.50, 19.50, 30.10, 0),
(12, 640, NULL, 30, NULL, NULL, 0.718, 19.50, 19.50, 14.00, 0),
(12, 641, NULL, 30, NULL, NULL, 0.769, 19.50, 19.50, 15.00, 0),
(12, 649, NULL, 26, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 660, NULL, 30, NULL, NULL, 11.795, 19.50, 19.50, 230.00, 0),
(12, 661, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 662, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 663, NULL, 30, NULL, NULL, 0.974, 19.50, 19.50, 19.00, 0),
(12, 664, NULL, 30, NULL, NULL, 0.564, 19.50, 19.50, 11.00, 0),
(12, 665, NULL, 30, NULL, NULL, 1.000, 19.50, 19.50, 19.50, 0),
(12, 666, NULL, 30, NULL, NULL, 1.026, 19.50, 19.50, 20.00, 0),
(12, 667, NULL, 30, NULL, NULL, 1.538, 19.50, 19.50, 30.00, 0),
(12, 668, NULL, 30, NULL, NULL, 3.077, 19.50, 19.50, 60.00, 0),
(12, 669, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 670, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 671, NULL, 30, NULL, NULL, 1.795, 19.50, 19.50, 35.00, 0),
(12, 672, NULL, 30, NULL, NULL, 0.256, 19.50, 19.50, 5.00, 0),
(12, 673, NULL, 30, NULL, NULL, 0.769, 19.50, 19.50, 15.00, 0),
(12, 674, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 675, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 676, NULL, 30, NULL, NULL, 2.308, 19.50, 19.50, 45.00, 0),
(12, 706, NULL, 13, NULL, NULL, 2.564, 19.50, 19.50, 50.00, 0),
(12, 707, NULL, 13, NULL, NULL, 5.128, 19.50, 19.50, 100.00, 0),
(12, 708, NULL, 13, NULL, NULL, 4.821, 19.50, 19.50, 94.00, 0),
(12, 709, NULL, 13, NULL, NULL, 5.128, 19.50, 19.50, 100.00, 0),
(12, 710, NULL, 13, NULL, NULL, 1.436, 19.50, 19.50, 28.00, 0),
(12, 711, NULL, 13, NULL, NULL, 1.692, 19.50, 19.50, 33.00, 0),
(12, 712, NULL, 13, NULL, NULL, 2.564, 19.50, 19.50, 50.00, 0),
(12, 713, NULL, 13, NULL, NULL, 1.538, 19.50, 19.50, 30.00, 0),
(12, 714, NULL, 13, NULL, NULL, 1.026, 19.50, 19.50, 20.00, 0),
(12, 715, NULL, 13, NULL, NULL, 2.564, 19.50, 19.50, 50.00, 0),
(12, 716, NULL, 13, NULL, NULL, 1.289, 19.50, 19.50, 25.13, 0),
(12, 724, NULL, 18, NULL, NULL, 2.821, 19.50, 19.50, 55.00, 0),
(12, 725, NULL, 18, NULL, NULL, 1.795, 19.50, 19.50, 35.00, 0),
(12, 726, NULL, 18, NULL, NULL, 1.795, 19.50, 19.50, 35.00, 0),
(12, 727, NULL, 18, NULL, NULL, 6.154, 19.50, 19.50, 120.00, 0),
(12, 728, NULL, 18, NULL, NULL, 3.590, 19.50, 19.50, 70.00, 0),
(12, 729, NULL, 18, NULL, NULL, 1.538, 19.50, 19.50, 30.00, 0),
(12, 730, NULL, 18, NULL, NULL, 3.282, 19.50, 19.50, 64.00, 0),
(12, 731, NULL, 18, NULL, NULL, 2.564, 19.50, 19.50, 50.00, 0),
(12, 732, NULL, 18, NULL, NULL, 1.282, 19.50, 19.50, 25.00, 0),
(12, 733, NULL, 18, NULL, NULL, 2.615, 19.50, 19.50, 51.00, 0),
(12, 734, NULL, 18, NULL, NULL, 2.913, 19.50, 19.50, 56.80, 0),
(12, 740, NULL, 30, NULL, NULL, 0.667, 19.50, 19.50, 13.00, 0),
(12, 741, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 742, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 743, NULL, 30, NULL, NULL, 1.026, 19.50, 19.50, 20.00, 0),
(12, 744, NULL, 30, NULL, NULL, 1.026, 19.50, 19.50, 20.00, 0),
(12, 745, NULL, 30, NULL, NULL, 0.513, 19.50, 19.50, 10.00, 0),
(12, 746, NULL, 13, NULL, NULL, 1.026, 19.50, 19.50, 20.00, 0),
(12, 747, NULL, 13, NULL, NULL, 1.333, 19.50, 19.50, 26.00, 0),
(14, 257, NULL, 36, NULL, NULL, 38.288, 22.20, 22.20, 850.00, 0),
(14, 258, NULL, 25, NULL, NULL, 6.757, 22.20, 22.20, 150.00, 0),
(14, 259, NULL, 29, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 260, NULL, 29, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 261, NULL, 29, NULL, NULL, 7.097, 22.20, 22.20, 157.56, 0),
(14, 262, NULL, 29, NULL, NULL, 5.405, 22.20, 22.20, 120.00, 0),
(14, 263, NULL, 29, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 264, NULL, 29, NULL, NULL, 1.216, 22.20, 22.20, 27.00, 0),
(14, 265, NULL, 29, NULL, NULL, 0.135, 22.20, 22.20, 3.00, 0),
(14, 266, NULL, 29, NULL, NULL, 2.703, 22.20, 22.20, 60.00, 0),
(14, 267, NULL, 29, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 268, NULL, 29, NULL, NULL, 1.000, 22.20, 22.20, 22.20, 0),
(14, 269, NULL, 29, NULL, NULL, 0.811, 22.20, 22.20, 18.00, 0),
(14, 270, NULL, 29, NULL, NULL, 4.955, 22.20, 22.20, 110.00, 0),
(14, 271, NULL, 29, NULL, NULL, 1.396, 22.20, 22.20, 31.00, 0),
(14, 272, NULL, 29, NULL, NULL, 3.559, 22.20, 22.20, 79.00, 0),
(14, 273, NULL, 29, NULL, NULL, 1.802, 22.20, 22.20, 40.00, 0),
(14, 345, NULL, 1, NULL, NULL, 18.333, 22.20, 22.20, 407.00, 0),
(14, 346, NULL, 1, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 347, NULL, 1, NULL, NULL, 38.288, 22.20, 22.20, 850.00, 0),
(14, 348, NULL, 1, NULL, NULL, 16.667, 22.20, 22.20, 370.00, 0),
(14, 349, NULL, 1, NULL, NULL, 30.000, 22.20, 22.20, 666.00, 0),
(14, 350, NULL, 1, NULL, NULL, 23.423, 22.20, 22.20, 520.00, 0),
(14, 351, NULL, 1, NULL, NULL, 4.865, 22.20, 22.20, 108.00, 0),
(14, 352, NULL, 1, NULL, NULL, 5.454, 22.20, 22.20, 121.07, 0),
(14, 353, NULL, 10, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 354, NULL, 10, NULL, NULL, 8.468, 22.20, 22.20, 188.00, 0),
(14, 355, NULL, 10, NULL, NULL, 11.036, 22.20, 22.20, 245.00, 0),
(14, 356, NULL, 10, NULL, NULL, 22.523, 22.20, 22.20, 500.00, 0),
(14, 357, NULL, 10, NULL, NULL, 22.523, 22.20, 22.20, 500.00, 0),
(14, 358, NULL, 10, NULL, NULL, 17.297, 22.20, 22.20, 384.00, 0),
(14, 359, NULL, 10, NULL, NULL, 45.045, 22.20, 22.20, 1000.00, 0),
(14, 360, NULL, 22, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 361, NULL, 22, NULL, NULL, 1.802, 22.20, 22.20, 40.00, 0),
(14, 362, NULL, 22, NULL, NULL, 70.000, 22.20, 22.20, 1554.00, 0),
(14, 363, NULL, 22, NULL, NULL, 29.279, 22.20, 22.20, 650.00, 0),
(14, 364, NULL, 22, NULL, NULL, 6.351, 22.20, 22.20, 141.00, 0),
(14, 365, NULL, 22, NULL, NULL, 34.685, 22.20, 22.20, 770.00, 0),
(14, 366, NULL, 24, NULL, NULL, 2.703, 22.20, 22.20, 60.00, 0),
(14, 367, NULL, 24, NULL, NULL, 41.892, 22.20, 22.20, 930.00, 0),
(14, 368, NULL, 24, NULL, NULL, 5.856, 22.20, 22.20, 130.00, 0),
(14, 369, NULL, 22, NULL, NULL, 1.036, 22.20, 22.20, 23.00, 0),
(14, 370, NULL, 10, NULL, NULL, 1.036, 22.20, 22.20, 23.00, 0),
(14, 373, NULL, 22, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 374, NULL, 1, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 379, NULL, 12, NULL, NULL, 13.514, 22.20, 22.20, 300.00, 0),
(14, 381, NULL, 10, NULL, NULL, 2.703, 22.20, 22.20, 60.00, 0),
(14, 382, NULL, 29, NULL, NULL, 3.604, 22.20, 22.20, 80.00, 0),
(14, 383, NULL, 29, NULL, NULL, 3.153, 22.20, 22.20, 70.00, 0),
(14, 384, NULL, 29, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 385, NULL, 29, NULL, NULL, 8.919, 22.20, 22.20, 198.00, 0),
(14, 388, NULL, 1, NULL, NULL, 11.441, 22.20, 22.20, 254.00, 0),
(14, 389, NULL, 1, NULL, NULL, 17.477, 22.20, 22.20, 388.00, 0),
(14, 390, NULL, 10, 20, NULL, 105.000, 22.20, 22.20, 2331.00, 0),
(14, 391, NULL, 1, 18, NULL, 61.000, 22.20, 22.20, 1354.20, 0),
(14, 392, NULL, 1, 17, NULL, 48.410, 22.20, 22.20, 1074.70, 0),
(14, 393, NULL, 1, 21, NULL, 50.000, 22.20, 22.20, 1110.00, 0),
(14, 394, NULL, 22, 22, NULL, 53.001, 22.20, 22.20, 1176.62, 0),
(14, 395, NULL, 10, 23, NULL, 77.479, 22.20, 22.20, 1720.03, 0),
(14, 399, NULL, 29, NULL, NULL, 182.852, 22.20, 22.20, 4059.31, 0),
(14, 400, NULL, 29, NULL, NULL, 1.351, 22.20, 22.20, 30.00, 0),
(14, 401, NULL, 10, 24, NULL, 50.577, 22.20, 22.20, 1122.81, 0),
(14, 402, NULL, 10, 25, NULL, 155.502, 22.20, 22.20, 3452.14, 0),
(14, 403, NULL, 17, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 405, NULL, 1, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 406, NULL, 1, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 411, NULL, 29, NULL, NULL, 69.144, 22.20, 22.20, 1535.00, 0),
(14, 412, NULL, 36, NULL, NULL, 30.000, 22.20, 22.20, 666.00, 0),
(14, 413, NULL, 1, NULL, NULL, 30.000, 22.20, 22.20, 666.00, 0),
(14, 414, NULL, 24, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 415, NULL, 1, NULL, NULL, 20.000, 22.20, 22.20, 444.00, 0),
(14, 416, NULL, 1, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 417, NULL, 1, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 418, NULL, 1, NULL, NULL, 40.541, 22.20, 22.20, 900.00, 0),
(14, 419, NULL, 1, NULL, NULL, 15.766, 22.20, 22.20, 350.00, 0),
(14, 420, NULL, 1, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 421, NULL, 1, NULL, NULL, 18.108, 22.20, 22.20, 402.00, 0),
(14, 422, NULL, 1, NULL, NULL, 15.495, 22.20, 22.20, 344.00, 0),
(14, 423, NULL, 1, NULL, NULL, 8.559, 22.20, 22.20, 190.00, 0),
(14, 424, NULL, 10, NULL, NULL, 45.045, 22.20, 22.20, 1000.00, 0),
(14, 425, NULL, 10, NULL, NULL, 29.279, 22.20, 22.20, 650.00, 0),
(14, 426, NULL, 10, NULL, NULL, 35.000, 22.20, 22.20, 777.00, 0),
(14, 435, NULL, 22, NULL, NULL, 12.613, 22.20, 22.20, 280.00, 0),
(14, 436, NULL, 22, NULL, NULL, 6.306, 22.20, 22.20, 140.00, 0),
(14, 437, NULL, 24, NULL, NULL, 6.757, 22.20, 22.20, 150.00, 0),
(14, 438, NULL, 24, NULL, NULL, 22.523, 22.20, 22.20, 500.00, 0),
(14, 441, NULL, 12, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 442, NULL, 17, NULL, NULL, 25.450, 22.20, 22.20, 565.00, 0),
(14, 443, NULL, 17, NULL, NULL, 4.234, 22.20, 22.20, 94.00, 0),
(14, 444, NULL, 17, NULL, NULL, 4.144, 22.20, 22.20, 92.00, 0),
(14, 448, NULL, 17, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 449, NULL, 36, NULL, NULL, 32.987, 22.20, 22.20, 732.31, 0),
(14, 450, NULL, 36, NULL, NULL, 27.027, 22.20, 22.20, 600.00, 0),
(14, 451, NULL, 36, NULL, NULL, 28.378, 22.20, 22.20, 630.00, 0),
(14, 452, NULL, 36, NULL, NULL, 20.000, 22.20, 22.20, 444.00, 0),
(14, 453, NULL, 25, NULL, NULL, 5.405, 22.20, 22.20, 120.00, 0),
(14, 455, NULL, 29, NULL, NULL, 5.631, 22.20, 22.20, 125.00, 0),
(14, 458, NULL, 29, NULL, NULL, 2.793, 22.20, 22.20, 62.00, 0),
(14, 463, NULL, 29, NULL, NULL, 12.973, 22.20, 22.20, 288.00, 0),
(14, 465, NULL, 10, NULL, NULL, 22.523, 22.20, 22.20, 500.00, 0),
(14, 467, NULL, 12, NULL, NULL, 3.604, 22.20, 22.20, 80.00, 0),
(14, 468, NULL, 17, NULL, NULL, 6.757, 22.20, 22.20, 150.00, 0),
(14, 470, NULL, 1, NULL, NULL, 5.000, 22.20, 22.20, 111.00, 0),
(14, 471, NULL, 10, NULL, NULL, 40.541, 22.20, 22.20, 900.00, 0),
(14, 472, NULL, 10, NULL, NULL, 31.532, 22.20, 22.20, 700.00, 0),
(14, 473, NULL, 17, NULL, NULL, 18.018, 22.20, 22.20, 400.00, 0),
(14, 475, NULL, 29, NULL, NULL, 14.189, 22.20, 22.20, 315.00, 0),
(14, 524, NULL, 10, NULL, NULL, 69.820, 22.20, 22.20, 1550.00, 0),
(14, 525, NULL, 10, 26, NULL, 60.000, 22.20, 22.20, 1332.00, 0),
(14, 526, NULL, 10, NULL, NULL, 3.604, 22.20, 22.20, 80.00, 0),
(14, 527, NULL, 22, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 528, NULL, 22, 27, NULL, 58.478, 22.20, 22.20, 1298.21, 0),
(14, 529, NULL, 22, NULL, NULL, 22.523, 22.20, 22.20, 500.00, 0),
(14, 530, NULL, 22, NULL, NULL, 6.171, 22.20, 22.20, 137.00, 0),
(14, 531, NULL, 36, NULL, NULL, 6.757, 22.20, 22.20, 150.00, 0),
(14, 532, NULL, 29, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 533, NULL, 29, NULL, NULL, 3.378, 22.20, 22.20, 75.00, 0),
(14, 534, NULL, 29, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 535, NULL, 29, NULL, NULL, 1.802, 22.20, 22.20, 40.00, 0),
(14, 536, NULL, 29, NULL, NULL, 1.577, 22.20, 22.20, 35.00, 0),
(14, 537, NULL, 29, NULL, NULL, 9.775, 22.20, 22.20, 217.00, 0),
(14, 538, NULL, 29, NULL, NULL, 0.586, 22.20, 22.20, 13.00, 0),
(14, 554, NULL, 10, NULL, NULL, 13.514, 22.20, 22.20, 300.00, 0),
(14, 555, NULL, 10, NULL, NULL, 27.027, 22.20, 22.20, 600.00, 0),
(14, 556, NULL, 22, NULL, NULL, 1.802, 22.20, 22.20, 40.00, 0),
(14, 557, NULL, 22, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 558, NULL, 24, NULL, NULL, 12.838, 22.20, 22.20, 285.00, 0),
(14, 573, NULL, 1, NULL, NULL, 8.559, 22.20, 22.20, 190.00, 0),
(14, 574, NULL, 22, NULL, NULL, 1.802, 22.20, 22.20, 40.00, 0),
(14, 579, NULL, 29, NULL, NULL, 11.351, 22.20, 22.20, 252.00, 0),
(14, 584, NULL, 12, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 586, NULL, 12, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 590, NULL, 12, NULL, NULL, 12.973, 22.20, 22.20, 288.00, 0),
(14, 591, NULL, 12, NULL, NULL, 5.405, 22.20, 22.20, 120.00, 0),
(14, 592, NULL, 12, NULL, NULL, 10.360, 22.20, 22.20, 230.00, 0),
(14, 593, NULL, 12, NULL, NULL, 13.063, 22.20, 22.20, 290.00, 0),
(14, 594, NULL, 12, NULL, NULL, 2.027, 22.20, 22.20, 45.00, 0),
(14, 596, NULL, 17, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 597, NULL, 17, NULL, NULL, 2.973, 22.20, 22.20, 66.00, 0),
(14, 599, NULL, 17, NULL, NULL, 3.153, 22.20, 22.20, 70.00, 0),
(14, 606, NULL, 17, NULL, NULL, 9.910, 22.20, 22.20, 220.00, 0),
(14, 610, NULL, 17, NULL, NULL, 11.982, 22.20, 22.20, 266.00, 0),
(14, 611, NULL, 1, NULL, NULL, 13.514, 22.20, 22.20, 300.00, 0),
(14, 612, NULL, 1, NULL, NULL, 37.838, 22.20, 22.20, 840.00, 0),
(14, 613, NULL, 10, 28, NULL, 98.418, 22.20, 22.20, 2184.88, 0),
(14, 614, NULL, 10, 25, NULL, 130.000, 22.20, 22.20, 2886.00, 0),
(14, 616, NULL, 29, NULL, NULL, 7.207, 22.20, 22.20, 160.00, 0),
(14, 619, NULL, 29, NULL, NULL, 3.153, 22.20, 22.20, 70.00, 0),
(14, 620, NULL, 29, NULL, NULL, 6.757, 22.20, 22.20, 150.00, 0),
(14, 621, NULL, 29, NULL, NULL, 11.261, 22.20, 22.20, 250.00, 0),
(14, 625, NULL, 29, NULL, NULL, 5.405, 22.20, 22.20, 120.00, 0),
(14, 627, NULL, 17, NULL, NULL, 5.856, 22.20, 22.20, 130.00, 0),
(14, 628, NULL, 12, NULL, NULL, 6.306, 22.20, 22.20, 140.00, 0),
(14, 630, NULL, 10, NULL, NULL, 25.000, 22.20, 22.20, 555.00, 0),
(14, 631, NULL, 1, NULL, NULL, 18.018, 22.20, 22.20, 400.00, 0),
(14, 632, NULL, 10, 24, NULL, 100.001, 22.20, 22.20, 2220.02, 0),
(14, 634, NULL, 29, NULL, NULL, 8.243, 22.20, 22.20, 183.00, 0),
(14, 637, NULL, 12, NULL, NULL, 7.658, 22.20, 22.20, 170.00, 0),
(14, 642, NULL, 36, NULL, NULL, 30.631, 22.20, 22.20, 680.00, 0),
(14, 643, NULL, 36, NULL, NULL, 9.009, 22.20, 22.20, 200.00, 0),
(14, 644, NULL, 25, NULL, NULL, 3.604, 22.20, 22.20, 80.00, 0),
(14, 645, NULL, 25, NULL, NULL, 5.405, 22.20, 22.20, 120.00, 0),
(14, 646, NULL, 25, NULL, NULL, 4.505, 22.20, 22.20, 100.00, 0),
(14, 677, NULL, 29, NULL, NULL, 13.514, 22.20, 22.20, 300.00, 0),
(14, 678, NULL, 29, NULL, NULL, 13.514, 22.20, 22.20, 300.00, 0),
(14, 679, NULL, 29, NULL, NULL, 9.099, 22.20, 22.20, 202.00, 0),
(14, 680, NULL, 29, NULL, NULL, 4.054, 22.20, 22.20, 90.00, 0),
(14, 681, NULL, 29, NULL, NULL, 4.955, 22.20, 22.20, 110.00, 0),
(14, 682, NULL, 29, NULL, NULL, 12.207, 22.20, 22.20, 271.00, 0),
(14, 683, NULL, 29, NULL, NULL, 1.306, 22.20, 22.20, 29.00, 0),
(14, 684, NULL, 1, NULL, NULL, 36.036, 22.20, 22.20, 800.00, 0),
(14, 685, NULL, 1, NULL, NULL, 31.982, 22.20, 22.20, 710.00, 0),
(14, 686, NULL, 1, NULL, NULL, 34.685, 22.20, 22.20, 770.00, 0),
(14, 687, NULL, 1, 19, NULL, 40.000, 22.20, 22.20, 888.00, 0),
(14, 688, NULL, 1, NULL, NULL, 31.532, 22.20, 22.20, 700.00, 0),
(14, 689, NULL, 1, 29, NULL, 112.613, 22.20, 22.20, 2500.00, 0),
(14, 694, NULL, 22, NULL, NULL, 3.829, 22.20, 22.20, 85.00, 0),
(14, 699, NULL, 12, NULL, NULL, 1.441, 22.20, 22.20, 32.00, 0),
(14, 702, NULL, 12, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 757, NULL, 17, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 761, NULL, 17, NULL, NULL, 1.802, 22.20, 22.20, 40.00, 0),
(14, 763, NULL, 17, NULL, NULL, 4.144, 22.20, 22.20, 92.00, 0),
(14, 764, NULL, 17, NULL, NULL, 6.757, 22.20, 22.20, 150.00, 0),
(14, 765, NULL, 12, NULL, NULL, 6.982, 22.20, 22.20, 155.00, 0),
(14, 767, NULL, 12, NULL, NULL, 7.658, 22.20, 22.20, 170.00, 0),
(14, 768, NULL, 12, NULL, NULL, 3.153, 22.20, 22.20, 70.00, 0),
(14, 771, NULL, 12, NULL, NULL, 10.721, 22.20, 22.20, 238.00, 0),
(14, 772, NULL, 12, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 773, NULL, 12, NULL, NULL, 4.054, 22.20, 22.20, 90.00, 0),
(14, 775, NULL, 10, NULL, NULL, 10.360, 22.20, 22.20, 230.00, 0),
(14, 776, NULL, 10, NULL, NULL, 58.739, 22.20, 22.20, 1304.00, 0),
(14, 777, NULL, 1, NULL, NULL, 22.523, 22.20, 22.20, 500.00, 0),
(14, 778, NULL, 1, NULL, NULL, 13.153, 22.20, 22.20, 292.00, 0),
(14, 779, NULL, 1, NULL, NULL, 50.000, 22.20, 22.20, 1110.00, 0),
(14, 780, NULL, 12, NULL, NULL, 2.252, 22.20, 22.20, 50.00, 0),
(14, 783, NULL, 17, NULL, NULL, 5.856, 22.20, 22.20, 130.00, 0),
(15, 281, NULL, 33, NULL, NULL, 1.354, 19.20, 19.20, 26.00, 0),
(15, 282, NULL, 33, NULL, NULL, 0.417, 19.20, 19.20, 8.00, 0),
(15, 283, NULL, 33, NULL, NULL, 1.604, 19.20, 19.20, 30.80, 0),
(15, 284, NULL, 33, NULL, NULL, 8.333, 19.20, 19.20, 160.00, 0),
(15, 285, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 286, NULL, 33, NULL, NULL, 7.292, 19.20, 19.20, 140.00, 0),
(15, 287, NULL, 33, NULL, NULL, 0.260, 19.20, 19.20, 5.00, 0),
(15, 288, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 289, NULL, 33, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 290, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 291, NULL, 33, NULL, NULL, 0.625, 19.20, 19.20, 12.00, 0),
(15, 292, NULL, 33, NULL, NULL, 6.667, 19.20, 19.20, 128.00, 0),
(15, 293, NULL, 33, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 294, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 295, NULL, 33, NULL, NULL, 3.906, 19.20, 19.20, 75.00, 0),
(15, 296, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 297, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 298, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 299, NULL, 33, NULL, NULL, 2.604, 19.20, 19.20, 50.00, 0),
(15, 300, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 301, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 302, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 303, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 304, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 305, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 306, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 307, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 308, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 309, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 310, NULL, 33, NULL, NULL, 2.708, 19.20, 19.20, 52.00, 0),
(15, 311, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 312, NULL, 33, NULL, NULL, 0.417, 19.20, 19.20, 8.00, 0),
(15, 313, NULL, 33, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 314, NULL, 33, NULL, NULL, 0.260, 19.20, 19.20, 5.00, 0),
(15, 315, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 316, NULL, 33, NULL, NULL, 5.208, 19.20, 19.20, 100.00, 0),
(15, 323, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 324, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 325, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 326, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 327, NULL, 15, NULL, NULL, 2.604, 19.20, 19.20, 50.00, 0),
(15, 328, NULL, 15, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 329, NULL, 15, NULL, NULL, 5.208, 19.20, 19.20, 100.00, 0),
(15, 330, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 331, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 332, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 333, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 334, NULL, 15, NULL, NULL, 1.842, 19.20, 19.20, 35.37, 0),
(15, 335, NULL, 20, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 336, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 337, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 338, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 339, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 340, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 341, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 375, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 378, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 398, NULL, 28, NULL, NULL, 314.100, 19.20, 19.20, 6030.72, 0),
(15, 407, NULL, 20, NULL, NULL, 9.583, 19.20, 19.20, 184.00, 0),
(15, 408, NULL, 15, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 409, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 410, NULL, 15, NULL, NULL, 15.625, 19.20, 19.20, 300.00, 0),
(15, 428, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 429, NULL, 15, NULL, NULL, 5.729, 19.20, 19.20, 110.00, 0),
(15, 430, NULL, 15, NULL, NULL, 2.344, 19.20, 19.20, 45.00, 0),
(15, 431, NULL, 15, NULL, NULL, 5.208, 19.20, 19.20, 100.00, 0),
(15, 433, NULL, 15, NULL, NULL, 2.604, 19.20, 19.20, 50.00, 0),
(15, 434, NULL, 15, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 440, NULL, 15, NULL, NULL, 1.875, 19.20, 19.20, 36.00, 0),
(15, 445, NULL, 20, NULL, NULL, 9.896, 19.20, 19.20, 190.00, 0),
(15, 446, NULL, 20, NULL, NULL, 4.010, 19.20, 19.20, 77.00, 0),
(15, 454, NULL, 33, NULL, NULL, 3.125, 19.20, 19.20, 60.00, 0),
(15, 456, NULL, 33, NULL, NULL, 4.479, 19.20, 19.20, 86.00, 0),
(15, 457, NULL, 33, NULL, NULL, 3.594, 19.20, 19.20, 69.00, 0),
(15, 460, NULL, 33, NULL, NULL, 5.104, 19.20, 19.20, 98.00, 0),
(15, 461, NULL, 33, NULL, NULL, 7.969, 19.20, 19.20, 153.00, 0),
(15, 462, NULL, 33, NULL, NULL, 2.552, 19.20, 19.20, 49.00, 0),
(15, 464, NULL, 33, NULL, NULL, 6.875, 19.20, 19.20, 132.00, 0),
(15, 466, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 469, NULL, 20, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 474, NULL, 33, NULL, NULL, 4.167, 19.20, 19.20, 80.00, 0),
(15, 480, NULL, 15, NULL, NULL, 9.375, 19.20, 19.20, 180.00, 0),
(15, 481, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 482, NULL, 15, NULL, NULL, 2.604, 19.20, 19.20, 50.00, 0),
(15, 483, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 484, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 485, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 486, NULL, 15, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 487, NULL, 15, NULL, NULL, 1.302, 19.20, 19.20, 25.00, 0),
(15, 488, NULL, 15, NULL, NULL, 0.573, 19.20, 19.20, 11.00, 0),
(15, 489, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 490, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 491, NULL, 15, NULL, NULL, 2.604, 19.20, 19.20, 50.00, 0),
(15, 492, NULL, 15, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 493, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 494, NULL, 15, NULL, NULL, 0.260, 19.20, 19.20, 5.00, 0),
(15, 495, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 496, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 497, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 498, NULL, 20, NULL, NULL, 2.604, 19.20, 19.20, 50.00, 0),
(15, 499, NULL, 20, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 500, NULL, 20, NULL, NULL, 0.260, 19.20, 19.20, 5.00, 0),
(15, 501, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 502, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 503, NULL, 20, NULL, NULL, 1.771, 19.20, 19.20, 34.00, 0),
(15, 504, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 505, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 506, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 507, NULL, 20, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 508, NULL, 20, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 509, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 510, NULL, 20, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 511, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 512, NULL, 20, NULL, NULL, 0.260, 19.20, 19.20, 5.00, 0),
(15, 513, NULL, 20, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 514, NULL, 20, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 515, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 516, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 517, NULL, 20, NULL, NULL, 0.729, 19.20, 19.20, 14.00, 0),
(15, 518, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 519, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 520, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 521, NULL, 20, NULL, NULL, 0.469, 19.20, 19.20, 9.00, 0),
(15, 522, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 523, NULL, 20, NULL, NULL, 0.938, 19.20, 19.20, 18.00, 0),
(15, 541, NULL, 33, NULL, NULL, 3.385, 19.20, 19.20, 65.00, 0),
(15, 542, NULL, 33, NULL, NULL, 3.906, 19.20, 19.20, 75.00, 0),
(15, 543, NULL, 33, NULL, NULL, 2.344, 19.20, 19.20, 45.00, 0),
(15, 544, NULL, 33, NULL, NULL, 0.833, 19.20, 19.20, 16.00, 0),
(15, 545, NULL, 33, NULL, NULL, 0.885, 19.20, 19.20, 17.00, 0),
(15, 546, NULL, 33, NULL, NULL, 4.167, 19.20, 19.20, 80.00, 0),
(15, 547, NULL, 33, NULL, NULL, 2.604, 19.20, 19.20, 50.00, 0),
(15, 548, NULL, 33, NULL, NULL, 2.865, 19.20, 19.20, 55.00, 0),
(15, 549, NULL, 33, NULL, NULL, 1.667, 19.20, 19.20, 32.00, 0),
(15, 550, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 551, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 552, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 553, NULL, 33, NULL, NULL, 7.182, 19.20, 19.20, 137.90, 0),
(15, 559, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 560, NULL, 15, NULL, NULL, 0.729, 19.20, 19.20, 14.00, 0),
(15, 561, NULL, 20, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 562, NULL, 20, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 563, NULL, 20, NULL, NULL, 0.677, 19.20, 19.20, 13.00, 0),
(15, 564, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 565, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 566, NULL, 15, NULL, NULL, 1.823, 19.20, 19.20, 35.00, 0),
(15, 567, NULL, 15, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 568, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 569, NULL, 33, NULL, NULL, 1.582, 19.20, 19.20, 30.37, 0),
(15, 570, NULL, 33, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 571, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 572, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 575, NULL, 33, NULL, NULL, 1.563, 19.20, 19.20, 30.00, 0),
(15, 576, NULL, 33, NULL, NULL, 0.781, 19.20, 19.20, 15.00, 0),
(15, 577, NULL, 33, NULL, NULL, 3.385, 19.20, 19.20, 65.00, 0),
(15, 578, NULL, 33, NULL, NULL, 1.979, 19.20, 19.20, 38.00, 0),
(15, 580, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 581, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 582, NULL, 33, NULL, NULL, 4.063, 19.20, 19.20, 78.00, 0),
(15, 583, NULL, 15, NULL, NULL, 3.021, 19.20, 19.20, 58.00, 0),
(15, 585, NULL, 15, NULL, NULL, 7.292, 19.20, 19.20, 140.00, 0),
(15, 587, NULL, 15, NULL, NULL, 3.771, 19.20, 19.20, 72.40, 0),
(15, 589, NULL, 15, NULL, NULL, 4.948, 19.20, 19.20, 95.00, 0),
(15, 595, NULL, 20, NULL, NULL, 1.719, 19.20, 19.20, 33.00, 0),
(15, 598, NULL, 20, NULL, NULL, 1.927, 19.20, 19.20, 37.00, 0),
(15, 600, NULL, 20, NULL, NULL, 1.302, 19.20, 19.20, 25.00, 0),
(15, 601, NULL, 20, NULL, NULL, 2.526, 19.20, 19.20, 48.50, 0),
(15, 602, NULL, 20, NULL, NULL, 2.609, 19.20, 19.20, 50.10, 0),
(15, 603, NULL, 20, NULL, NULL, 1.823, 19.20, 19.20, 35.00, 0),
(15, 604, NULL, 20, NULL, NULL, 2.188, 19.20, 19.20, 42.00, 0),
(15, 605, NULL, 20, NULL, NULL, 5.990, 19.20, 19.20, 115.00, 0),
(15, 607, NULL, 20, NULL, NULL, 1.172, 19.20, 19.20, 22.50, 0),
(15, 608, NULL, 20, NULL, NULL, 2.344, 19.20, 19.20, 45.00, 0),
(15, 609, NULL, 20, NULL, NULL, 2.865, 19.20, 19.20, 55.00, 0),
(15, 615, NULL, 33, NULL, NULL, 4.583, 19.20, 19.20, 88.00, 0),
(15, 617, NULL, 33, NULL, NULL, 4.167, 19.20, 19.20, 80.00, 0),
(15, 622, NULL, 33, NULL, NULL, 2.917, 19.20, 19.20, 56.00, 0),
(15, 623, NULL, 33, NULL, NULL, 1.823, 19.20, 19.20, 35.00, 0),
(15, 624, NULL, 33, NULL, NULL, 1.667, 19.20, 19.20, 32.00, 0),
(15, 626, NULL, 20, NULL, NULL, 3.125, 19.20, 19.20, 60.00, 0),
(15, 629, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 633, NULL, 28, NULL, NULL, 3.385, 19.20, 19.20, 65.00, 0),
(15, 635, NULL, 15, NULL, NULL, 9.688, 19.20, 19.20, 186.00, 0),
(15, 636, NULL, 20, NULL, NULL, 3.385, 19.20, 19.20, 65.00, 0),
(15, 647, NULL, 33, NULL, NULL, 5.781, 19.20, 19.20, 111.00, 0),
(15, 735, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 739, NULL, 33, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 753, NULL, 20, NULL, NULL, 1.667, 19.20, 19.20, 32.00, 0),
(15, 754, NULL, 20, NULL, NULL, 4.948, 19.20, 19.20, 95.00, 0),
(15, 755, NULL, 20, NULL, NULL, 3.490, 19.20, 19.20, 67.00, 0),
(15, 756, NULL, 20, NULL, NULL, 1.302, 19.20, 19.20, 25.00, 0),
(15, 758, NULL, 20, NULL, NULL, 4.948, 19.20, 19.20, 95.00, 0),
(15, 759, NULL, 20, NULL, NULL, 3.594, 19.20, 19.20, 69.00, 0),
(15, 760, NULL, 20, NULL, NULL, 2.083, 19.20, 19.20, 40.00, 0),
(15, 762, NULL, 20, NULL, NULL, 9.010, 19.20, 19.20, 173.00, 0),
(15, 766, NULL, 15, NULL, NULL, 2.344, 19.20, 19.20, 45.00, 0),
(15, 769, NULL, 15, NULL, NULL, 4.818, 19.20, 19.20, 92.50, 0),
(15, 770, NULL, 15, NULL, NULL, 3.125, 19.20, 19.20, 60.00, 0),
(15, 774, NULL, 20, NULL, NULL, 0.521, 19.20, 19.20, 10.00, 0),
(15, 781, NULL, 20, NULL, NULL, 5.625, 19.20, 19.20, 108.00, 0),
(15, 784, NULL, 20, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(15, 785, NULL, 15, NULL, NULL, 1.042, 19.20, 19.20, 20.00, 0),
(16, 274, NULL, 27, NULL, NULL, 1.422, 21.10, 21.10, 30.00, 0),
(16, 275, NULL, 32, NULL, NULL, 0.948, 21.10, 21.10, 20.00, 0),
(16, 276, NULL, 32, NULL, NULL, 1.991, 21.10, 21.10, 42.00, 0),
(16, 277, NULL, 32, NULL, NULL, 0.474, 21.10, 21.10, 10.00, 0),
(16, 278, NULL, 32, NULL, NULL, 0.474, 21.10, 21.10, 10.00, 0),
(16, 279, NULL, 32, NULL, NULL, 0.474, 21.10, 21.10, 10.00, 0),
(16, 280, NULL, 32, NULL, NULL, 0.474, 21.10, 21.10, 10.00, 0),
(16, 317, NULL, 14, NULL, NULL, 0.948, 21.10, 21.10, 20.00, 0),
(16, 318, NULL, 14, NULL, NULL, 0.474, 21.10, 21.10, 10.00, 0),
(16, 319, NULL, 14, NULL, NULL, 0.474, 21.10, 21.10, 10.00, 0),
(16, 320, NULL, 14, NULL, NULL, 0.948, 21.10, 21.10, 20.00, 0),
(16, 321, NULL, 14, NULL, NULL, 0.711, 21.10, 21.10, 15.00, 0),
(16, 322, NULL, 14, NULL, NULL, 0.954, 21.10, 21.10, 20.13, 0),
(16, 372, NULL, 19, NULL, NULL, 1.232, 21.10, 21.10, 26.00, 0),
(16, 404, NULL, 19, NULL, NULL, 14.218, 21.10, 21.10, 300.00, 0),
(16, 427, NULL, 14, NULL, NULL, 2.370, 21.10, 21.10, 50.00, 0),
(16, 432, NULL, 14, NULL, NULL, 5.687, 21.10, 21.10, 120.00, 0),
(16, 439, NULL, 14, NULL, NULL, 5.687, 21.10, 21.10, 120.00, 0),
(16, 447, NULL, 19, NULL, NULL, 3.270, 21.10, 21.10, 69.00, 0),
(16, 459, NULL, 32, NULL, NULL, 8.009, 21.10, 21.10, 169.00, 0),
(16, 476, NULL, 14, NULL, NULL, 5.213, 21.10, 21.10, 110.00, 0),
(16, 477, NULL, 19, NULL, NULL, 7.109, 21.10, 21.10, 150.00, 0),
(16, 478, NULL, 19, NULL, NULL, 0.948, 21.10, 21.10, 20.00, 0),
(16, 479, NULL, 19, NULL, NULL, 1.896, 21.10, 21.10, 40.00, 0),
(16, 540, NULL, 32, NULL, NULL, 1.422, 21.10, 21.10, 30.00, 0),
(16, 588, NULL, 14, NULL, NULL, 1.185, 21.10, 21.10, 25.00, 0),
(16, 618, NULL, 32, NULL, NULL, 2.844, 21.10, 21.10, 60.00, 0),
(16, 638, NULL, 14, NULL, NULL, 3.886, 21.10, 21.10, 82.00, 0),
(16, 639, NULL, 19, NULL, NULL, 1.896, 21.10, 21.10, 40.00, 0),
(16, 648, NULL, 27, NULL, NULL, 5.687, 21.10, 21.10, 120.00, 0),
(16, 650, NULL, 32, NULL, NULL, 5.687, 21.10, 21.10, 120.00, 0),
(16, 651, NULL, 32, NULL, NULL, 4.739, 21.10, 21.10, 100.00, 0),
(16, 652, NULL, 32, NULL, NULL, 2.370, 21.10, 21.10, 50.00, 0),
(16, 653, NULL, 32, NULL, NULL, 1.280, 21.10, 21.10, 27.00, 0),
(16, 654, NULL, 32, NULL, NULL, 1.422, 21.10, 21.10, 30.00, 0),
(16, 655, NULL, 32, NULL, NULL, 7.109, 21.10, 21.10, 150.00, 0),
(16, 656, NULL, 32, NULL, NULL, 0.948, 21.10, 21.10, 20.00, 0),
(16, 657, NULL, 32, NULL, NULL, 0.948, 21.10, 21.10, 20.00, 0),
(16, 658, NULL, 32, NULL, NULL, 0.948, 21.10, 21.10, 20.00, 0),
(16, 659, NULL, 32, NULL, NULL, 0.474, 21.10, 21.10, 10.00, 0),
(16, 717, NULL, 14, NULL, NULL, 1.422, 21.10, 21.10, 30.00, 0),
(16, 718, NULL, 14, NULL, NULL, 1.991, 21.10, 21.10, 42.00, 0),
(16, 719, NULL, 19, NULL, NULL, 1.659, 21.10, 21.10, 35.00, 0),
(16, 720, NULL, 19, NULL, NULL, 2.085, 21.10, 21.10, 44.00, 0),
(16, 721, NULL, 19, NULL, NULL, 6.635, 21.10, 21.10, 140.00, 0),
(16, 722, NULL, 19, NULL, NULL, 3.791, 21.10, 21.10, 80.00, 0),
(16, 782, NULL, 14, NULL, NULL, 2.370, 21.10, 21.10, 50.00, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_balances`
--

CREATE TABLE `stock_balances` (
  `id` int NOT NULL,
  `tank_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL COMMENT 'auxiliar, siempre debe ser igual al producto del tanque',
  `date` date NOT NULL,
  `initial_measurement` decimal(12,3) UNSIGNED DEFAULT NULL,
  `final_measurement` decimal(12,3) UNSIGNED DEFAULT NULL,
  `purchased_quantity` decimal(12,3) UNSIGNED DEFAULT NULL,
  `sold_quantity` decimal(12,3) DEFAULT NULL,
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commercial_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suppliers`
--

INSERT INTO `suppliers` (`id`, `company_name`, `document`, `commercial_name`, `phone`, `deleted`, `created_at`, `updated_at`) VALUES
(9, 'PETROPERU', '20100128218', NULL, NULL, 0, '2026-04-06 10:14:02', '2026-04-06 10:14:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tanks`
--

CREATE TABLE `tanks` (
  `id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` decimal(10,3) UNSIGNED NOT NULL DEFAULT '0.000',
  `stored_quantity` decimal(10,3) UNSIGNED DEFAULT '0.000',
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `is_reserve` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tanks`
--

INSERT INTO `tanks` (`id`, `location_id`, `name`, `capacity`, `stored_quantity`, `deleted`, `product_id`, `is_reserve`, `created_at`, `updated_at`) VALUES
(8, 1, 'T-GLP', 5000.000, 4844.100, 0, 10, 1, '2025-11-04 11:23:41', '2025-11-27 18:29:27'),
(9, 1, 'T-GR', 20000.000, 19997.143, 0, 12, 0, '2025-11-07 12:07:03', '2025-11-24 12:45:26'),
(10, 1, 'estJaen2', 123123.000, 666.000, 1, 12, 0, '2025-11-07 12:07:21', '2025-11-20 11:07:48'),
(11, 1, 'T-GP', 1233.000, 1223.235, 0, 11, 0, '2025-11-07 12:07:45', '2025-11-24 17:47:43'),
(12, 4, 'glpIgn1', 120000.000, 120000.000, 0, 10, 0, '2025-11-07 12:08:20', '2025-11-07 12:08:20'),
(13, 4, 'glpIgn2', 1200000.000, 0.000, 0, 10, 0, '2025-11-07 12:08:39', '2025-11-07 12:08:39'),
(14, 1, 'T-DIESEL', 10000.000, 9945.000, 0, 13, 0, '2025-11-20 11:09:11', '2025-11-24 11:13:16'),
(15, 5, 'T-GLP', 5000.000, 4955.000, 0, 10, 0, '2025-11-24 17:26:00', '2025-11-28 18:58:03'),
(16, 5, 'T-GP', 5000.000, 47.000, 0, 11, 0, '2025-11-24 17:26:35', '2025-11-27 18:07:47'),
(17, 5, 'T-GR', 5000.000, 1.000, 0, 12, 0, '2025-11-24 17:26:55', '2025-11-24 17:39:46'),
(18, 5, 'T-DIESEL', 5000.000, 3.000, 0, 13, 0, '2025-11-24 17:27:13', '2025-12-11 15:43:07'),
(19, 3, 'T-GLP (BAGUA)', 1000.000, 1000.000, 1, 10, 0, '2025-11-25 02:51:33', '2026-01-28 10:41:34'),
(20, 5, 'reseva-test', 50000.000, 0.000, 0, 10, 1, '2025-11-28 10:18:16', '2025-11-28 10:24:35'),
(21, 5, 'no-reserva', 20.000, 0.000, 0, 12, 0, '2025-11-28 10:18:46', '2025-11-28 10:18:46'),
(22, 3, 'T-GNV (BAGUA)', 1000.000, 1000.000, 1, 11, 0, '2025-11-28 16:58:42', '2026-01-28 10:41:39'),
(23, 3, 'T-GR (BAGUA)', 10000.000, 0.540, 0, 12, 0, '2025-11-28 17:20:09', '2026-04-06 07:02:59'),
(24, 3, 'T-GP (BAGUA)', 1000.000, 70.360, 1, 13, 0, '2025-11-28 17:20:35', '2026-03-03 12:52:28'),
(25, 3, 'T-DIESEL (BAGUA)', 10000.000, 6088.158, 0, 14, 0, '2025-11-28 17:20:59', '2026-04-07 07:13:30'),
(26, 3, 'T-GHR (BAGUA)', 5000.000, 222.070, 0, 15, 0, '2025-11-28 17:21:25', '2026-04-07 07:17:22'),
(27, 3, 'T-GHP (BAGUA)', 5000.000, 134.755, 0, 16, 0, '2025-11-28 17:21:50', '2026-04-07 07:12:42'),
(28, 3, 'T-PRETROLEO (BAGUA)', 1000.000, 1000.000, 1, 17, 0, '2025-11-28 17:22:58', '2026-01-28 10:42:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `location_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `isle_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('scc','sb','eb') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'scc: Salida Caja Chica, sb: Salida Boveda, eb: Entrada Boveda',
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='transacciones de bóveda y caja chica';

--
-- Volcado de datos para la tabla `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `location_id`, `isle_id`, `type`, `description`, `amount`, `date`, `created_at`, `updated_at`, `status`) VALUES
(1, 6, 3, 7, 'scc', 'T.S.P', 10.00, '2026-04-02 06:58:16', '2026-04-02 06:58:16', '2026-04-02 06:58:16', 0),
(3, 7, 3, 8, 'scc', 'comida perros', 8.00, '2026-04-02 07:25:17', '2026-04-02 07:25:17', '2026-04-02 07:25:17', 0),
(4, 7, 3, 8, 'scc', 'combustible camioneta', 100.00, '2026-04-02 07:25:41', '2026-04-02 07:25:41', '2026-04-02 07:25:41', 0),
(5, 7, 3, 8, 'scc', 'devolucion gasolina', 40.00, '2026-04-02 07:26:04', '2026-04-02 07:26:04', '2026-04-02 07:26:04', 0),
(7, 6, 3, 7, 'scc', 'GASTO OFICINA', 80.00, '2026-04-02 08:03:53', '2026-04-02 08:03:53', '2026-04-02 08:03:53', 0),
(8, 6, 3, 7, 'scc', 'VACUNA PERROS', 20.00, '2026-04-02 08:04:16', '2026-04-02 08:04:16', '2026-04-02 08:04:16', 0),
(9, 6, 3, 7, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 1 - BAGUA)', 7197.60, '2026-04-02 21:01:40', '2026-04-02 21:01:40', '2026-04-02 21:08:53', 1),
(10, 7, 3, 8, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 2 - BAGUA)', 3313.00, '2026-04-02 21:02:10', '2026-04-02 21:02:10', '2026-04-02 21:08:59', 1),
(11, 7, 3, 8, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 2 - BAGUA)', 5644.50, '2026-04-03 08:59:27', '2026-04-03 08:59:27', '2026-04-03 14:51:17', 1),
(12, 6, 3, 7, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 1 - BAGUA)', 5644.50, '2026-04-03 08:59:57', '2026-04-03 08:59:57', '2026-04-03 14:51:11', 1),
(13, 7, 3, 8, 'scc', 'COMIDA PERROS', 8.00, '2026-04-04 07:07:41', '2026-04-04 07:07:41', '2026-04-04 07:07:41', 0),
(14, 6, 3, 7, 'scc', 'PLASTICO PARA TELE', 5.00, '2026-04-04 07:07:49', '2026-04-04 07:07:49', '2026-04-04 07:07:49', 0),
(15, 7, 3, 8, 'scc', 'ABASTECIMIENTO DE DB5 CAMIONETA ROJA', 50.00, '2026-04-04 07:08:26', '2026-04-04 07:08:26', '2026-04-04 07:08:26', 0),
(16, 7, 3, 8, 'scc', 'ABASTECIMIENTO DE GASOLINA A OMAR', 10.00, '2026-04-04 07:08:59', '2026-04-04 07:08:59', '2026-04-04 07:08:59', 0),
(17, 7, 3, 8, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 2 - BAGUA)', 1155.00, '2026-04-04 07:12:52', '2026-04-04 07:12:52', '2026-04-04 07:12:52', 0),
(18, 6, 3, 7, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 1 - BAGUA)', 2932.00, '2026-04-04 07:19:19', '2026-04-04 07:19:19', '2026-04-04 07:19:19', 0),
(19, 7, 3, 8, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 2 - BAGUA)', 1199.00, '2026-04-05 07:46:08', '2026-04-05 07:46:08', '2026-04-05 07:46:08', 0),
(20, 7, 3, 8, 'scc', 'comida para perros', 8.00, '2026-04-05 07:47:07', '2026-04-05 07:47:07', '2026-04-05 07:47:07', 0),
(21, 6, 3, 7, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 1 - BAGUA)', 5483.40, '2026-04-05 07:53:14', '2026-04-05 07:53:14', '2026-04-05 07:53:14', 0),
(22, 6, 3, 7, 'eb', 'Transferencia a bóveda desde cierre de caja (Isla: ISLA 1 - BAGUA)', 4401.50, '2026-04-07 07:24:24', '2026-04-07 07:24:24', '2026-04-07 07:24:24', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transfers`
--

CREATE TABLE `transfers` (
  `id` bigint UNSIGNED NOT NULL,
  `from_tank_id` bigint UNSIGNED NOT NULL,
  `to_tank_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(10,3) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `recieved` tinyint(1) DEFAULT '0',
  `recieved_at` datetime DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trucks`
--

CREATE TABLE `trucks` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `plate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `trucks`
--

INSERT INTO `trucks` (`id`, `name`, `description`, `plate`, `deleted`, `created_at`, `updated_at`) VALUES
(16, 'BZC-753', NULL, 'BZC-753', 0, '2026-03-04 18:11:34', '2026-03-04 18:11:34'),
(17, 't9t-866', NULL, 't9t-866', 0, '2026-03-06 12:19:15', '2026-03-06 12:19:15'),
(18, 'AUK-843', NULL, 'AUK-843', 0, '2026-03-11 17:23:26', '2026-03-11 17:23:26'),
(19, 'C8A-864', NULL, 'C8A-864', 0, '2026-03-11 17:26:06', '2026-03-11 17:26:06'),
(20, 'T8M-925', NULL, 'T8M-925', 0, '2026-03-11 17:32:30', '2026-03-11 17:32:30'),
(21, 'ACU-859', NULL, 'ACU-859', 0, '2026-04-02 07:10:46', '2026-04-02 07:10:46'),
(22, 'M3Y-759', NULL, 'M3Y-759', 0, '2026-04-02 07:12:27', '2026-04-02 07:12:27'),
(23, 'M5T-827', NULL, 'M5T-827', 0, '2026-04-02 07:13:40', '2026-04-02 07:13:40'),
(24, 'BUS-720', NULL, 'BUS-720', 0, '2026-04-02 23:32:10', '2026-04-02 23:32:10'),
(25, 'BMZ-746', NULL, 'BMZ-746', 0, '2026-04-02 23:35:32', '2026-04-02 23:35:32'),
(26, 'CAM-865', NULL, 'CAM-865', 0, '2026-04-03 22:49:09', '2026-04-03 22:49:09'),
(27, 'ASA-880', NULL, 'ASA-880', 0, '2026-04-03 22:59:50', '2026-04-03 22:59:50'),
(28, 'T7L-813', NULL, 'T7L-813', 0, '2026-04-05 00:47:51', '2026-04-05 00:47:51'),
(29, 'BKU-847', NULL, 'BKU-847', 0, '2026-04-05 23:51:05', '2026-04-05 23:51:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `isle_id` bigint UNSIGNED DEFAULT NULL,
  `employee_id` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role_id`, `location_id`, `password`, `deleted`, `created_at`, `updated_at`, `isle_id`, `employee_id`) VALUES
(1, 'admin', 'admin', 2, 3, '$2y$10$HURCsAe5l8icxf7RaPSoYeiwuFG.WpHakScC4agg9EpZOZoxh8iAC', 0, '2025-08-04 12:01:17', '2025-11-28 18:24:42', NULL, NULL),
(3, 'xinergia', 'xinergia', 1, 3, '$2a$12$hPz0ns0fY1kna9nuaLniL.9c1RGWRUOjZ2P5oW8fCRzIhRvactQIq', 0, '2025-08-01 12:52:39', '2025-12-26 16:00:24', NULL, NULL),
(4, 'isla 1 - rioja', 'rioja', 3, 5, '$2y$10$cqceZNLKd2aVAaKZLQLmJu3c3xjpbscNe8x8cQWSgFnmQpOvEz.ea', 0, '2025-11-24 17:50:26', '2025-12-11 15:37:14', 3, NULL),
(5, 'admin_bagua', 'admin_bagua', 2, 3, '$2y$10$rdnvT1XSdEMDODW0yiZkGeKlUQXz2nVN5tmnG10A8xILhD6LO7txG', 0, '2025-11-28 16:50:25', '2025-12-13 07:37:48', NULL, NULL),
(6, 'isla1_bagua', 'isla1_bagua', 3, 3, '$2y$10$k4k1hllUB.rauyLB0aSTbenWbU3hKzPjRdOOucgYVQB21C6AVgD1K', 0, '2025-11-28 16:57:34', '2026-04-03 22:15:17', 7, 5),
(7, 'isla2_bagua', 'isla2_bagua', 3, 3, '$2y$10$QAcUhsb4qyZQcyncP4E5UuU8uTEBpF9IvyfR/ri0vNgOLRkJ7Q1xi', 0, '2025-11-28 16:58:04', '2026-04-05 22:40:56', 8, 5),
(9, 'ana_bagua', 'ana_bagua', 2, 3, '$2y$10$hHusqUbEA3D6hen3Tw7C4OOFtPvQ3K0QPRswr9nsB98NOBT2TTmfi', 0, '2026-01-12 20:35:19', '2026-01-12 20:35:19', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wastes`
--

CREATE TABLE `wastes` (
  `id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,3) NOT NULL DEFAULT '0.000',
  `type` enum('serafin','compras','error') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'mediciones de serafín, verificacion de compras, errores de surtidora',
  `product_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='salidas de combustible';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `agreements`
--
ALTER TABLE `agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agreement_client_id_fk` (`client_id`),
  ADD KEY `agreement_location_id_fk` (`location_id`);

--
-- Indices de la tabla `agreement_details`
--
ALTER TABLE `agreement_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agreement_details_agreement_id_fk` (`agreement_id`),
  ADD KEY `agreement_details_product_id_fk` (`product_id`);

--
-- Indices de la tabla `cash_closes`
--
ALTER TABLE `cash_closes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_cash_close_locations` (`location_id`),
  ADD KEY `FK_cash_closes_users` (`user_id`),
  ADD KEY `cash_closes_isle_id_foreign` (`isle_id`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indices de la tabla `discharges`
--
ALTER TABLE `discharges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discharges_purchase_id_foreign` (`purchase_id`),
  ADD KEY `discharges_location_id_foreign` (`location_id`);

--
-- Indices de la tabla `discharge_details`
--
ALTER TABLE `discharge_details`
  ADD PRIMARY KEY (`discharge_id`,`tank_id`),
  ADD KEY `discharge_details_product_id_foreign` (`product_id`),
  ADD KEY `discharge_details_tank_id_foreign` (`tank_id`),
  ADD KEY `discharge_details_truck_id_foreign` (`truck_id`),
  ADD KEY `discharge_id` (`discharge_id`);

--
-- Indices de la tabla `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_employees_locations` (`location_id`);

--
-- Indices de la tabla `isles`
--
ALTER TABLE `isles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indices de la tabla `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `location_prices`
--
ALTER TABLE `location_prices`
  ADD PRIMARY KEY (`location_id`,`product_id`);

--
-- Indices de la tabla `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `measurements`
--
ALTER TABLE `measurements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_agreement_id_fk` (`agreement_id`);

--
-- Indices de la tabla `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_order_id_fk` (`order_id`),
  ADD KEY `order_details_product_id_fk` (`product_id`);

--
-- Indices de la tabla `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_payment_method_payment` (`payment_method_id`),
  ADD KEY `fk_sale_payment` (`sale_id`),
  ADD KEY `fk_agreement_payment` (`agreement_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indices de la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pumps`
--
ALTER TABLE `pumps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `isle_id` (`isle_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indices de la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_payment_method_id_foreign` (`payment_method_id`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

--
-- Indices de la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_details_product_fk` (`product_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `tank_id` (`tank_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_nombre_unique` (`nombre`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_user_id_fk` (`user_id`),
  ADD KEY `sales_client_id_fk` (`client_id`),
  ADD KEY `sales_location_id_fk` (`location_id`);

--
-- Indices de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`product_id`,`sale_id`),
  ADD KEY `sale_details_sale_id_foreign` (`sale_id`),
  ADD KEY `order_detail_id` (`order_detail_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `pump_id` (`pump_id`),
  ADD KEY `truck_id` (`truck_id`);

--
-- Indices de la tabla `stock_balances`
--
ALTER TABLE `stock_balances`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `product_id` (`product_id`) USING BTREE,
  ADD KEY `tank_id` (`tank_id`) USING BTREE;

--
-- Indices de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tanks`
--
ALTER TABLE `tanks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tanks_product_id_foreign` (`product_id`),
  ADD KEY `tanks_location_id_foreign` (`location_id`);

--
-- Indices de la tabla `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_expenses_users` (`user_id`),
  ADD KEY `FK_transactions_locations` (`location_id`),
  ADD KEY `transactions_isle_id_fk` (`isle_id`);

--
-- Indices de la tabla `transfers`
--
ALTER TABLE `transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transfers_from_tank_id_fk` (`from_tank_id`),
  ADD KEY `transfers_to_tank_id_fk` (`to_tank_id`),
  ADD KEY `transfers_product_id_fk` (`product_id`);

--
-- Indices de la tabla `trucks`
--
ALTER TABLE `trucks`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `users_location_id_foreign` (`location_id`),
  ADD KEY `users_isle_id_foreign` (`isle_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indices de la tabla `wastes`
--
ALTER TABLE `wastes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `location_id` (`location_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `agreements`
--
ALTER TABLE `agreements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `agreement_details`
--
ALTER TABLE `agreement_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT de la tabla `cash_closes`
--
ALTER TABLE `cash_closes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `config`
--
ALTER TABLE `config`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `discharges`
--
ALTER TABLE `discharges`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `isles`
--
ALTER TABLE `isles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `measurements`
--
ALTER TABLE `measurements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=299;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT de la tabla `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT de la tabla `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=497;

--
-- AUTO_INCREMENT de la tabla `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `pumps`
--
ALTER TABLE `pumps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=786;

--
-- AUTO_INCREMENT de la tabla `stock_balances`
--
ALTER TABLE `stock_balances`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `tanks`
--
ALTER TABLE `tanks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `transfers`
--
ALTER TABLE `transfers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `trucks`
--
ALTER TABLE `trucks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `wastes`
--
ALTER TABLE `wastes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `agreements`
--
ALTER TABLE `agreements`
  ADD CONSTRAINT `agreement_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `agreement_location_id_fk` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Filtros para la tabla `agreement_details`
--
ALTER TABLE `agreement_details`
  ADD CONSTRAINT `agreement_details_agreement_id_fk` FOREIGN KEY (`agreement_id`) REFERENCES `agreements` (`id`),
  ADD CONSTRAINT `agreement_details_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `cash_closes`
--
ALTER TABLE `cash_closes`
  ADD CONSTRAINT `cash_closes_isle_id_foreign` FOREIGN KEY (`isle_id`) REFERENCES `isles` (`id`),
  ADD CONSTRAINT `FK_cash_close_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `FK_cash_closes_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `discharges`
--
ALTER TABLE `discharges`
  ADD CONSTRAINT `discharges_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `discharges_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`);

--
-- Filtros para la tabla `discharge_details`
--
ALTER TABLE `discharge_details`
  ADD CONSTRAINT `discharge_details_discharge_id_foreign` FOREIGN KEY (`discharge_id`) REFERENCES `discharges` (`id`),
  ADD CONSTRAINT `discharge_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `discharge_details_tank_id_foreign` FOREIGN KEY (`tank_id`) REFERENCES `tanks` (`id`),
  ADD CONSTRAINT `discharge_details_truck_id_foreign` FOREIGN KEY (`truck_id`) REFERENCES `trucks` (`id`);

--
-- Filtros para la tabla `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `FK_employees_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Filtros para la tabla `isles`
--
ALTER TABLE `isles`
  ADD CONSTRAINT `FK_isle_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Filtros para la tabla `measurements`
--
ALTER TABLE `measurements`
  ADD CONSTRAINT `FK_measurements_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_agreement_id_fk` FOREIGN KEY (`agreement_id`) REFERENCES `agreements` (`id`);

--
-- Filtros para la tabla `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_order_id_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_agreement_payment` FOREIGN KEY (`agreement_id`) REFERENCES `agreements` (`id`),
  ADD CONSTRAINT `fk_client_payment` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `fk_payment_method_payment` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  ADD CONSTRAINT `fk_sale_payment` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `fk_user_payment` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `pumps`
--
ALTER TABLE `pumps`
  ADD CONSTRAINT `FK_pump_isle` FOREIGN KEY (`isle_id`) REFERENCES `isles` (`id`),
  ADD CONSTRAINT `FK_pumps_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Filtros para la tabla `purchase_details`
--
ALTER TABLE `purchase_details`
  ADD CONSTRAINT `FK_purchase_details_tanks` FOREIGN KEY (`tank_id`) REFERENCES `tanks` (`id`),
  ADD CONSTRAINT `purchase_details_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_details_purchase_fk` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_client_id_fk` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `sales_location_id_fk` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `sales_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `FK_sale_details_order_details` FOREIGN KEY (`order_detail_id`) REFERENCES `order_details` (`id`),
  ADD CONSTRAINT `FK_sale_details_pumps` FOREIGN KEY (`pump_id`) REFERENCES `pumps` (`id`),
  ADD CONSTRAINT `FK_sale_details_trucks` FOREIGN KEY (`truck_id`) REFERENCES `trucks` (`id`),
  ADD CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`);

--
-- Filtros para la tabla `stock_balances`
--
ALTER TABLE `stock_balances`
  ADD CONSTRAINT `fk_balance_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_balance_tank_id` FOREIGN KEY (`tank_id`) REFERENCES `tanks` (`id`);

--
-- Filtros para la tabla `tanks`
--
ALTER TABLE `tanks`
  ADD CONSTRAINT `tanks_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tanks_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `FK_expenses_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_transactions_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `transactions_isle_id_fk` FOREIGN KEY (`isle_id`) REFERENCES `isles` (`id`);

--
-- Filtros para la tabla `transfers`
--
ALTER TABLE `transfers`
  ADD CONSTRAINT `transfers_from_tank_id_fk` FOREIGN KEY (`from_tank_id`) REFERENCES `tanks` (`id`),
  ADD CONSTRAINT `transfers_product_id_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `transfers_to_tank_id_fk` FOREIGN KEY (`to_tank_id`) REFERENCES `tanks` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_users_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `users_isle_id_foreign` FOREIGN KEY (`isle_id`) REFERENCES `isles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `wastes`
--
ALTER TABLE `wastes`
  ADD CONSTRAINT `FK_wastes_locations` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `FK_wastes_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
