-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-06-2026 a las 04:25:20
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
-- Base de datos: `db_planta_asfalto_agy`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_actividades`
--

CREATE TABLE `bitacora_actividades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED DEFAULT NULL,
  `accion` varchar(255) NOT NULL,
  `detalle` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `bitacora_actividades`
--

INSERT INTO `bitacora_actividades` (`id`, `id_usuario`, `accion`, `detalle`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'Creación de Proyecto', 'Se creó el proyecto \'MANTENIMIENTO VIAL \"RENUEVAVIA - EL ALTO\" (ADQ. DE ARIDOS PARA MANTENIMIENTO DE VIAS EN LA CIUDAD DE EL ALTO)\' (ID: 6).', '127.0.0.1', '2026-05-23 01:27:12', '2026-05-23 01:27:12'),
(2, 1, 'Modificación de Material', 'Se modificó el material \'Grava 3/4\' (ID: 5).', '127.0.0.1', '2026-05-23 01:28:51', '2026-05-23 01:28:51'),
(3, 1, 'Creación de Material', 'Se creó el material \'Gravilla 3/8\' con código \'GR-38\'.', '127.0.0.1', '2026-05-23 01:30:02', '2026-05-23 01:30:02'),
(4, 1, 'Creación de Material', 'Se creó el material \'Arena Natural\' con código \'AR-NAT\'.', '127.0.0.1', '2026-05-23 01:32:00', '2026-05-23 01:32:00'),
(5, 1, 'Registro de Ingreso (Lote)', 'Se registró ingreso con 1 lote(s). ODC: CM/UACM/odc/495/23. RGTR-2026-0001: 110 Grava Triturada 1/2\" para MANTENIMIENTO VIAL \"RENUEVAVIA - EL ALTO\" (ADQ. DE ARIDOS PARA MANTENIMIENTO DE VIAS EN LA CIUDAD DE EL ALTO)', '127.0.0.1', '2026-05-23 02:46:35', '2026-05-23 02:46:35'),
(6, 1, 'Registro de Ingreso (Lote)', 'Se registró ingreso con 4 lote(s). ODC: CM/UACM/ODC/495/23. RGTR-2026-0002: 29 Grava 3/4 para MANTENIMIENTO VIAL \"RENUEVAVIA - EL ALTO\" (ADQ. DE ARIDOS PARA MANTENIMIENTO DE VIAS EN LA CIUDAD DE EL ALTO); RGTR-2026-0003: 51 Gravilla 3/8 para MANTENIMIENTO VIAL \"RENUEVAVIA - EL ALTO\" (ADQ. DE ARIDOS PARA MANTENIMIENTO DE VIAS EN LA CIUDAD DE EL ALTO); RGTR-2026-0004: 166 Filler Calcáreo para MANTENIMIENTO VIAL \"RENUEVAVIA - EL ALTO\" (ADQ. DE ARIDOS PARA MANTENIMIENTO DE VIAS EN LA CIUDAD DE EL ALTO); RGTR-2026-0005: 40 Arena Natural para MANTENIMIENTO VIAL \"RENUEVAVIA - EL ALTO\" (ADQ. DE ARIDOS PARA MANTENIMIENTO DE VIAS EN LA CIUDAD DE EL ALTO)', '127.0.0.1', '2026-05-23 02:49:23', '2026-05-23 02:49:23'),
(7, 1, 'Creación de Proyecto', 'Se creó el proyecto \'Prueba de proyectos\' (ID: 7).', '127.0.0.1', '2026-05-29 07:28:30', '2026-05-29 07:28:30'),
(8, 1, 'Registro de Ingreso (Lote)', 'Se registró ingreso con 3 lote(s). ODC: PP/UACM/ODC/495/26. RGTR-2026-0006: 150 Grava 3/4 para Prueba de proyectos; RGTR-2026-0007: 300 Grava Triturada 1/2\" para Prueba de proyectos; RGTR-2026-0008: 600 Gravilla 3/8 para Prueba de proyectos', '127.0.0.1', '2026-05-29 07:29:46', '2026-05-29 07:29:46'),
(9, 1, 'Registro de Despacho (Conciliación)', 'Conciliación ID 2, ODC: CM/UACM/ODC/495/23. RGTR-2026-0001: 100 Grava Triturada 1/2\"; RGTR-2026-0002: 20 Grava 3/4; RGTR-2026-0003: 50 Gravilla 3/8; RGTR-2026-0004: 160 Filler Calcáreo; RGTR-2026-0005: 40 Arena Natural', '127.0.0.1', '2026-05-31 22:05:37', '2026-05-31 22:05:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_ingresos`
--

CREATE TABLE `detalle_ingresos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nro_registro` varchar(20) DEFAULT NULL,
  `fecha_lote` date DEFAULT NULL,
  `id_ingreso` bigint(20) UNSIGNED NOT NULL,
  `id_material` bigint(20) UNSIGNED NOT NULL,
  `id_proyecto` bigint(20) UNSIGNED NOT NULL,
  `cantidad_adquirida` decimal(12,2) NOT NULL,
  `cantidad_actual_lote` decimal(12,2) NOT NULL,
  `acciones_planificadas` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `id_proveedor` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_ingresos`
--

INSERT INTO `detalle_ingresos` (`id`, `nro_registro`, `fecha_lote`, `id_ingreso`, `id_material`, `id_proyecto`, `cantidad_adquirida`, `cantidad_actual_lote`, `acciones_planificadas`, `created_at`, `updated_at`, `id_proveedor`) VALUES
(1, NULL, NULL, 1, 1, 1, 25000.00, 25000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(2, NULL, NULL, 2, 5, 1, 45000.00, 45000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(3, NULL, NULL, 3, 7, 1, 30000.00, 30000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(4, NULL, NULL, 4, 3, 1, 8000.00, 8000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(5, NULL, NULL, 5, 2, 2, 18000.00, 18000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(6, NULL, NULL, 6, 6, 2, 22000.00, 22000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(7, NULL, NULL, 7, 7, 2, 15000.00, 15000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(8, NULL, NULL, 8, 1, 3, 20000.00, 20000.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(9, NULL, NULL, 9, 1, 1, 20000.00, 12500.00, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57', NULL),
(10, 'RGTR-2026-0001', '2026-05-22', 10, 6, 6, 110.00, 10.00, 'para la construccion del camino de arreglar', '2026-05-23 02:46:35', '2026-05-31 22:05:37', 3),
(11, 'RGTR-2026-0002', '2026-05-22', 11, 5, 6, 29.00, 9.00, 'para la construccion del camino de arreglar', '2026-05-23 02:49:23', '2026-05-31 22:05:37', 4),
(12, 'RGTR-2026-0003', '2026-05-22', 11, 10, 6, 51.00, 1.00, 'para la construccion del camino de arreglar', '2026-05-23 02:49:23', '2026-05-31 22:05:37', 4),
(13, 'RGTR-2026-0004', '2026-05-22', 11, 8, 6, 166.00, 6.00, 'para la construccion del camino de arreglar', '2026-05-23 02:49:23', '2026-05-31 22:05:37', 3),
(14, 'RGTR-2026-0005', '2026-05-22', 11, 11, 6, 40.00, 0.00, 'para la construccion del camino de arreglar', '2026-05-23 02:49:23', '2026-05-31 22:05:37', 2),
(15, 'RGTR-2026-0006', '2026-05-28', 12, 5, 7, 150.00, 150.00, NULL, '2026-05-29 07:29:46', '2026-05-29 07:29:46', 2),
(16, 'RGTR-2026-0007', '2026-05-28', 12, 6, 7, 300.00, 300.00, NULL, '2026-05-29 07:29:46', '2026-05-29 07:29:46', 4),
(17, 'RGTR-2026-0008', '2026-05-28', 12, 10, 7, 600.00, 600.00, NULL, '2026-05-29 07:29:46', '2026-05-29 07:29:46', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_salidas`
--

CREATE TABLE `detalle_salidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_salida` bigint(20) UNSIGNED NOT NULL,
  `id_detalle_ingreso` bigint(20) UNSIGNED NOT NULL,
  `cantidad_salida` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_salidas`
--

INSERT INTO `detalle_salidas` (`id`, `id_salida`, `id_detalle_ingreso`, `cantidad_salida`, `created_at`, `updated_at`) VALUES
(1, 1, 9, 7500.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 2, 10, 100.00, '2026-05-31 22:05:37', '2026-05-31 22:05:37'),
(3, 2, 11, 20.00, '2026-05-31 22:05:37', '2026-05-31 22:05:37'),
(4, 2, 12, 50.00, '2026-05-31 22:05:37', '2026-05-31 22:05:37'),
(5, 2, 13, 160.00, '2026-05-31 22:05:37', '2026-05-31 22:05:37'),
(6, 2, 14, 40.00, '2026-05-31 22:05:37', '2026-05-31 22:05:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `nombre`, `cargo`, `area`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'Ing. Milton Quispe Choque', 'Supervisor de Obra Municipal', 'Dirección de Infraestructura Pública', 1, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 'Ing. Nancy Alanoca Vargas', 'Residente de Fiscalización Vial', 'Unidad de Mantenimiento de Vías', 1, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'Tec. Roberto Mamani Choque', 'Encargado de Laboratorio de Suelos y Asfaltos', 'Dirección de Infraestructura Pública', 1, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'Ing. Grover Nina Huanca', 'Residente de Obra - Doble Vía Viacha', 'Proyecto Doble Vía Viacha', 1, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(5, 'Ing. Elena Choquehua', 'Asistente de Fiscalización', 'Unidad de Mantenimiento de Vías', 0, '2026-05-23 01:20:57', '2026-05-23 01:20:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos`
--

CREATE TABLE `ingresos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nro_ticket` varchar(50) DEFAULT NULL,
  `odc` varchar(50) DEFAULT NULL,
  `id_proveedor` bigint(20) UNSIGNED DEFAULT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `id_funcionario` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha_adquirida` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ingresos`
--

INSERT INTO `ingresos` (`id`, `nro_ticket`, `odc`, `id_proveedor`, `id_usuario`, `id_funcionario`, `fecha_adquirida`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, 'TKT-2026-0001', 'OC-AL-2026-0045', 1, 1, NULL, '2026-04-22', 'Lote conforme a especificaciones técnicas.', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 'TKT-2026-0002', 'OC-AL-2026-0045', 2, 1, NULL, '2026-04-24', 'Material de cantera Achocalla, ensayos OK.', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'TKT-2026-0003', 'OC-AL-2026-0045', 2, 1, NULL, '2026-04-27', 'Arena lavada, libre de materia orgánica.', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'TKT-2026-0004', 'OC-AL-2026-0051', 1, 1, NULL, '2026-05-02', NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(5, 'TKT-2026-0005', 'OC-AL-2026-0058', 3, 1, NULL, '2026-05-07', NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(6, 'TKT-2026-0006', 'OC-AL-2026-0058', 4, 1, NULL, '2026-05-10', NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(7, 'TKT-2026-0007', 'OC-AL-2026-0058', 2, 1, NULL, '2026-05-12', NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(8, 'TKT-2026-0008', 'OC-AL-2025-0099', 1, 1, NULL, '2026-02-21', 'Lote final para cierre de proyecto.', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(9, 'TKT-2026-0009', 'OC-AL-2026-0062', 1, 1, NULL, '2026-05-17', NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(10, NULL, 'CM/UACM/odc/495/23', NULL, 1, 4, NULL, 'ninguna', '2026-05-23 02:46:35', '2026-05-23 02:46:35'),
(11, NULL, 'CM/UACM/ODC/495/23', NULL, 1, 2, NULL, 'ninguna', '2026-05-23 02:49:23', '2026-05-23 02:49:23'),
(12, NULL, 'PP/UACM/ODC/495/26', NULL, 1, 3, NULL, 'casi nada', '2026-05-29 07:29:46', '2026-05-29 07:29:46');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materials`
--

CREATE TABLE `materials` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo_interno` varchar(255) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_medida` bigint(20) UNSIGNED NOT NULL,
  `stock_minimo` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `materials`
--

INSERT INTO `materials` (`id`, `codigo_interno`, `nombre`, `descripcion`, `id_medida`, `stock_minimo`, `created_at`, `updated_at`) VALUES
(1, 'CA-6070', 'Cemento Asfáltico PEN 60/70', 'Cemento asfáltico convencional de penetración para mezclas asfálticas en caliente.', 1, 15000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 'CA-5080', 'Cemento Asfáltico PEN 50/70', 'Cemento asfáltico de penetración media para temperaturas ambientes altas.', 1, 10000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'EM-CSS1H', 'Emulsión Asfáltica CSS-1h', 'Emulsión asfáltica catiónica de rotura lenta para riegos de liga y imprimación.', 2, 10000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'EM-CRS1H', 'Emulsión Asfáltica CRS-1h', 'Emulsión asfáltica catiónica de rotura rápida para tratamientos superficiales.', 2, 8000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(5, 'GR-34', 'Grava 3/4', 'Agregado grueso triturado de 19 mm para dosificación de mezcla asfáltica en caliente.', 3, 25000.00, '2026-05-23 01:20:57', '2026-05-23 01:28:51'),
(6, 'GR-12', 'Grava Triturada 1/2\"', 'Agregado grueso triturado de 12.5 mm para capas de rodadura.', 1, 20000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(7, 'AR-RIO', 'Arena Limpia de Río', 'Agregado fino tamizado libre de limos y arcillas, origen aluvial.', 1, 20000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(8, 'FILLER-C', 'Filler Calcáreo', 'Polvo mineral calcáreo para mejora de graduación en mezclas asfálticas.', 1, 5000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(9, 'ADH-ES', 'AdhesivoBituminoso AntiSegrego', 'Aditivo mejorar adherencia entre agregado y asfalto, anti-segregación.', 2, 2000.00, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(10, 'GR-38', 'Gravilla 3/8', 'Agregado grueso triturado de 19 mm para dosificación de mezcla asfáltica en caliente.', 3, 25000.00, '2026-05-23 01:30:02', '2026-05-23 01:30:02'),
(11, 'AR-NAT', 'Arena Natural', 'arena que fina que se consigue facilmente', 3, 0.00, '2026-05-23 01:32:00', '2026-05-23 01:32:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_20_042127_create_unidad_medidas_table', 1),
(5, '2026_05_20_042128_create_materials_table', 1),
(6, '2026_05_20_042128_create_proveedors_table', 1),
(7, '2026_05_20_042129_create_proyectos_table', 1),
(8, '2026_05_20_042130_create_funcionarios_table', 1),
(9, '2026_05_20_042131_create_ingresos_table', 1),
(10, '2026_05_20_042132_create_detalle_ingresos_table', 1),
(11, '2026_05_20_042132_create_salidas_table', 1),
(12, '2026_05_20_042133_create_detalle_salidas_table', 1),
(13, '2026_05_22_000001_create_role_permissions_table', 1),
(14, '2026_05_22_000002_create_bitacora_actividades_table', 1),
(15, '2026_05_22_000003_add_codigo_interno_to_materials_table', 1),
(16, '2026_05_22_000004_add_rgtr_fields_to_detalle_ingresos_table', 1),
(17, '2026_05_22_000005_add_funcionario_to_ingresos_table', 1),
(18, '2026_05_22_000006_make_fecha_adquirida_nullable_in_ingresos', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `username` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedors`
--

CREATE TABLE `proveedors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `nit` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proveedors`
--

INSERT INTO `proveedors` (`id`, `razon_social`, `nit`, `telefono`, `direccion`, `created_at`, `updated_at`) VALUES
(1, 'YPFB Refinación S.A.', '1020304021', '22812400', 'Zona Senkata, Carretera a Oruro, El Alto', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 'Agregados del Altiplano S.R.L.', '4050607024', '71520304', 'Canteras de Achocalla, La Paz', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'Importadora de Asfaltos Bolívar', '1021765438', '24567890', 'Zona Mercado, Av. 6 de Marzo, El Alto', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'Hormigones y Pavimentos S.A.', '3021890231', '22118900', 'Zona Kilómetro 7, Carretera a Viacha', '2026-05-23 01:20:57', '2026-05-23 01:20:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `encargado` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` enum('activo','finalizado','pausado') NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`id`, `nombre`, `ubicacion`, `encargado`, `fecha`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'Doble Vía Viacha - Pavimento Flexible Lote A', 'Distrito 8, Carretera a Viacha Km 12, El Alto', 'Ing. Carlos Flores Mamani', '2026-02-01', 'activo', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 'Avenida Juan Pablo II - Mantenimiento Vial', 'Distrito 6, Av. Juan Pablo II entre Av. 6 de Marzo y C. 1, El Alto', 'Ing. Sonia Quispe Quisbert', '2026-05-10', 'activo', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'Viaducto San José - Distribuidor Vial', 'Distrito 3, Plaza San José, El Alto', 'Ing. Wilfredo Choque Layme', '2025-09-01', 'finalizado', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'Calle 25 de Julio - Recapado Asfáltico', 'Distrito 12, Zona 25 de Julio, El Alto', 'Ing. René Copa Mamani', '2026-04-01', 'activo', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(5, 'Acceso Aeropuerto Internacional - Mezcla SMA', 'Distrito 2, Acceso El Alto - La Paz, El Alto', 'Ing. Patricia Condori de Tapia', '2026-03-15', 'pausado', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(6, 'MANTENIMIENTO VIAL \"RENUEVAVIA - EL ALTO\" (ADQ. DE ARIDOS PARA MANTENIMIENTO DE VIAS EN LA CIUDAD DE EL ALTO)', 'Distrito 8, El Alto', 'Ing. Carlos Flores', '2026-05-22', 'activo', '2026-05-23 01:27:12', '2026-05-23 01:27:12'),
(7, 'Prueba de proyectos', 'El ALto', 'Ing. Diego V', '2026-05-28', 'activo', '2026-05-29 07:28:30', '2026-05-29 07:28:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL,
  `permission` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role`, `permission`, `created_at`, `updated_at`) VALUES
(1, 'administrador', 'ver_dashboard', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 'administrador', 'gestionar_usuarios', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'administrador', 'gestionar_materiales', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'administrador', 'gestionar_proveedores', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(5, 'administrador', 'gestionar_proyectos', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(6, 'administrador', 'gestionar_funcionarios', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(7, 'administrador', 'gestionar_ingresos', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(8, 'administrador', 'gestionar_salidas', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(9, 'administrador', 'ver_reportes', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(10, 'administrador', 'gestionar_permisos', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(11, 'administrador', 'ver_bitacora', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(12, 'operador', 'ver_dashboard', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(13, 'operador', 'gestionar_materiales', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(14, 'operador', 'gestionar_proveedores', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(15, 'operador', 'gestionar_proyectos', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(16, 'operador', 'gestionar_funcionarios', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(17, 'operador', 'gestionar_ingresos', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(18, 'operador', 'gestionar_salidas', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(19, 'operador', 'ver_reportes', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(20, 'visor', 'ver_dashboard', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(21, 'visor', 'ver_reportes', '2026-05-23 01:20:57', '2026-05-23 01:20:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salidas`
--

CREATE TABLE `salidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_funcionario` bigint(20) UNSIGNED NOT NULL,
  `id_proyecto` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
  `odc` varchar(50) DEFAULT NULL,
  `uso` varchar(255) DEFAULT NULL,
  `fecha_salida` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `salidas`
--

INSERT INTO `salidas` (`id`, `id_funcionario`, `id_proyecto`, `id_usuario`, `odc`, `uso`, `fecha_salida`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, 'Capa de rodadura - Tramo 0+000 a 0+500', '2026-05-19', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 3, 6, 1, 'CM/UACM/ODC/495/23', NULL, '2026-05-22', '2026-05-31 22:05:37', '2026-05-31 22:05:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('GdYcx0J6My4pAkl3p3L0msUl6R0uaNeZjp8Swj0G', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYzl2bzE1STQxaVlSNTRpdXBWUTJTVXI5eTZPdHhYYVNUZXBZYkFLdCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2JpdGFjb3JhIjtzOjU6InJvdXRlIjtzOjE0OiJiaXRhY29yYS5pbmRleCI7fX0=', 1780886803);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medidas`
--

CREATE TABLE `unidad_medidas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `abreviacion` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `unidad_medidas`
--

INSERT INTO `unidad_medidas` (`id`, `nombre`, `abreviacion`, `created_at`, `updated_at`) VALUES
(1, 'Kilogramo', 'kg', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(2, 'Litro', 'L', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'Metro cúbico', 'm3', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'Tonelada', 't', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(5, 'Galón', 'gal', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(6, 'Saco', 'saco', '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(7, 'Milímetro', 'mm', '2026-05-23 01:20:57', '2026-05-23 01:20:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('administrador','operador','visor') NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `role`, `active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrador de Planta', 'admin', '$2y$12$q2IorIzSO8oiRvr5IgBire5CJisoCbqk1HK3JcAnGDh7Q1rbA6qc2', 'administrador', 1, NULL, '2026-05-23 01:20:56', '2026-05-23 01:20:56'),
(2, 'Operador de Báscula', 'operador', '$2y$12$/JKo7wDnPh8EJ4aLLZRVB.R/xWautMr0Ei3hTNpAJ.dHAlQGTHu0e', 'operador', 1, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(3, 'Auditor Externo', 'visor', '$2y$12$yZknsJBwDfHQPOvPxXtG/eZeVvo.OFHRrnW3VxEXh7uHG88n5yTfO', 'visor', 1, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57'),
(4, 'Jefe de Almacén', 'jefe', '$2y$12$wFxz6k5efXcusQoyUU/9w.VBmbCvM1nenTDkeYx.jTNIf9.Ko4mGu', 'operador', 1, NULL, '2026-05-23 01:20:57', '2026-05-23 01:20:57');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora_actividades`
--
ALTER TABLE `bitacora_actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bitacora_actividades_id_usuario_foreign` (`id_usuario`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_ingresos_id_ingreso_foreign` (`id_ingreso`),
  ADD KEY `detalle_ingresos_id_material_foreign` (`id_material`),
  ADD KEY `detalle_ingresos_id_proyecto_foreign` (`id_proyecto`),
  ADD KEY `detalle_ingresos_id_proveedor_foreign` (`id_proveedor`);

--
-- Indices de la tabla `detalle_salidas`
--
ALTER TABLE `detalle_salidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `detalle_salidas_id_salida_foreign` (`id_salida`),
  ADD KEY `detalle_salidas_id_detalle_ingreso_foreign` (`id_detalle_ingreso`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ingresos_id_proveedor_foreign` (`id_proveedor`),
  ADD KEY `ingresos_id_usuario_foreign` (`id_usuario`),
  ADD KEY `ingresos_id_funcionario_foreign` (`id_funcionario`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `materials_codigo_interno_unique` (`codigo_interno`),
  ADD KEY `materials_id_medida_foreign` (`id_medida`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `proveedors_nit_unique` (`nit`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permissions_role_permission_unique` (`role`,`permission`);

--
-- Indices de la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salidas_id_funcionario_foreign` (`id_funcionario`),
  ADD KEY `salidas_id_proyecto_foreign` (`id_proyecto`),
  ADD KEY `salidas_id_usuario_foreign` (`id_usuario`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `unidad_medidas`
--
ALTER TABLE `unidad_medidas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora_actividades`
--
ALTER TABLE `bitacora_actividades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `detalle_salidas`
--
ALTER TABLE `detalle_salidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `ingresos`
--
ALTER TABLE `ingresos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `materials`
--
ALTER TABLE `materials`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `proveedors`
--
ALTER TABLE `proveedors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `salidas`
--
ALTER TABLE `salidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `unidad_medidas`
--
ALTER TABLE `unidad_medidas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora_actividades`
--
ALTER TABLE `bitacora_actividades`
  ADD CONSTRAINT `bitacora_actividades_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `detalle_ingresos`
--
ALTER TABLE `detalle_ingresos`
  ADD CONSTRAINT `detalle_ingresos_id_ingreso_foreign` FOREIGN KEY (`id_ingreso`) REFERENCES `ingresos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_ingresos_id_material_foreign` FOREIGN KEY (`id_material`) REFERENCES `materials` (`id`),
  ADD CONSTRAINT `detalle_ingresos_id_proveedor_foreign` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedors` (`id`),
  ADD CONSTRAINT `detalle_ingresos_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`);

--
-- Filtros para la tabla `detalle_salidas`
--
ALTER TABLE `detalle_salidas`
  ADD CONSTRAINT `detalle_salidas_id_detalle_ingreso_foreign` FOREIGN KEY (`id_detalle_ingreso`) REFERENCES `detalle_ingresos` (`id`),
  ADD CONSTRAINT `detalle_salidas_id_salida_foreign` FOREIGN KEY (`id_salida`) REFERENCES `salidas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ingresos`
--
ALTER TABLE `ingresos`
  ADD CONSTRAINT `ingresos_id_funcionario_foreign` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id`),
  ADD CONSTRAINT `ingresos_id_proveedor_foreign` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedors` (`id`),
  ADD CONSTRAINT `ingresos_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_id_medida_foreign` FOREIGN KEY (`id_medida`) REFERENCES `unidad_medidas` (`id`);

--
-- Filtros para la tabla `salidas`
--
ALTER TABLE `salidas`
  ADD CONSTRAINT `salidas_id_funcionario_foreign` FOREIGN KEY (`id_funcionario`) REFERENCES `funcionarios` (`id`),
  ADD CONSTRAINT `salidas_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`),
  ADD CONSTRAINT `salidas_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
