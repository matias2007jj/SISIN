-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-10-2026 a las 08:54:52
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
-- Base de datos: `sisin`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bienes`
--

CREATE TABLE `bienes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sala_id` bigint(20) UNSIGNED NOT NULL,
  `grupo_id` bigint(20) UNSIGNED NOT NULL,
  `cbi` varchar(10) NOT NULL,
  `tipo_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `marca_id` bigint(20) UNSIGNED DEFAULT NULL,
  `modelo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `serie_medida` varchar(100) DEFAULT NULL,
  `estado_id` bigint(20) UNSIGNED NOT NULL,
  `otras_especificaciones` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'NUEVO', NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(2, 'BUENO', NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(3, 'REGULAR', NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(4, 'MALO', NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL);

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
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE `instituciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ruc` char(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `instituciones`
--

INSERT INTO `instituciones` (`id`, `ruc`, `nombre`, `direccion`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '11111111111', 'Instituto de Educación Superior Tecnológico Público Julio César Tello', 'Villa El Salvador', NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventarios`
--

CREATE TABLE `inventarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `serie` char(4) NOT NULL,
  `numero` varchar(8) NOT NULL,
  `sala_id` bigint(20) UNSIGNED NOT NULL,
  `nombre_sala` varchar(60) NOT NULL,
  `coordinador_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_coordinador` varchar(100) DEFAULT NULL,
  `asistente_t1_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_asistente_t1` varchar(100) DEFAULT NULL,
  `asistente_t2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_asistente_t2` varchar(100) DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `observacion` varchar(500) DEFAULT NULL,
  `estado_inventario` enum('ABIERTO','CERRADO','ANULADO') NOT NULL DEFAULT 'ABIERTO',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_detalles`
--

CREATE TABLE `inventario_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventario_id` bigint(20) UNSIGNED NOT NULL,
  `bien_id` bigint(20) UNSIGNED NOT NULL,
  `grupo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_grupo` varchar(60) DEFAULT NULL,
  `nro_drelm` varchar(10) DEFAULT NULL,
  `cbi` varchar(10) NOT NULL,
  `tipo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_tipo` varchar(60) DEFAULT NULL,
  `descripcion` varchar(100) NOT NULL,
  `marca_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_marca` varchar(60) DEFAULT NULL,
  `modelo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_modelo` varchar(60) DEFAULT NULL,
  `serie_medida` varchar(100) DEFAULT NULL,
  `estado_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre_estado` varchar(60) DEFAULT NULL,
  `otras_especificaciones` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `marcas`
--

CREATE TABLE `marcas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(4, '2026_01_01_000001_create_usuarios_table', 1),
(5, '2026_01_01_000002_create_personal_table', 1),
(6, '2026_01_01_000003_create_instituciones_table', 1),
(7, '2026_01_01_000004_create_programas_estudio_table', 1),
(8, '2026_01_01_000005_create_usuario_programa_estudio_table', 1),
(9, '2026_01_01_000006_create_catalogos_tables', 1),
(10, '2026_01_01_000007_create_salas_table', 1),
(11, '2026_01_01_000008_create_bienes_table', 1),
(12, '2026_01_01_000009_create_inventarios_table', 1),
(13, '2026_01_01_000010_create_inventario_detalles_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelos`
--

CREATE TABLE `modelos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal`
--

CREATE TABLE `personal` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `apellidos` varchar(60) NOT NULL,
  `nombres` varchar(60) NOT NULL,
  `dni` char(8) NOT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programas_estudio`
--

CREATE TABLE `programas_estudio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `institucion_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `abreviatura` varchar(30) DEFAULT NULL,
  `coordinador_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asistente_t1_id` bigint(20) UNSIGNED DEFAULT NULL,
  `asistente_t2_id` bigint(20) UNSIGNED DEFAULT NULL,
  `observacion` varchar(150) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `programas_estudio`
--

INSERT INTO `programas_estudio` (`id`, `institucion_id`, `nombre`, `abreviatura`, `coordinador_id`, `asistente_t1_id`, `asistente_t2_id`, `observacion`, `activo`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'ADMINISTRACIÓN DE EMPRESAS', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(2, 1, 'DESARROLLO DE SISTEMAS DE INFORMACIÓN', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(3, 1, 'CONTABILIDAD', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(4, 1, 'ELECTRICIDAD INDUSTRIAL', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(5, 1, 'MECANICA AUTOMOTRIZ', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(6, 1, 'MECANICA DE PRODUCCIÓN', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(7, 1, 'SECRETARIADO EJECUTIVO', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salas`
--

CREATE TABLE `salas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `observacion` varchar(200) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
('CvGLfh9GuFtH0yD38Jd2sXDQ5PYhJ8ORMn3W9Tqo', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoieHFRVEZaaGV5ODVhcVJYMFFSU1JVMWcyTzdLSzN1MTczamsxS2JwbSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hbG1hY2VuL3RhYmxhcy90aXBvIjtzOjU6InJvdXRlIjtzOjE0OiJjYXRhbG9nby5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImM0ZTU1NTAxNDZkMGY4ZWM0NGJiZDliYmU0OGJmMWM3MGZlNDQxZTI0ODhjNTZjNWJlMDIzNzAzOWI2OTY4MjkiO3M6MTk6InByb2dyYW1hX2VzdHVkaW9faWQiO2k6MTt9', 1792047071),
('ijkQk4r0C58YT8DkMW2LOujFAkBvDfzAAr8vOOA3', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiWmc4S3dqdXVXZWVrQjlnR0U3RTZQaXNSbVhjOGt4YlZnTzJoU0NEdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hbG1hY2VuL3RhYmxhcy9tYXJjYSI7czo1OiJyb3V0ZSI7czoxNDoiY2F0YWxvZ28uaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJjNGU1NTUwMTQ2ZDBmOGVjNDRiYmQ5YmJlNDhiZjFjNzBmZTQ0MWUyNDg4YzU2YzViZTAyMzcwMzliNjk2ODI5Ijt9', 1791953323);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipos`
--

CREATE TABLE `tipos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tipos`
--

INSERT INTO `tipos` (`id`, `programa_estudio_id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'AD', 1, NULL, '2026-10-15 11:51:10', '2026-10-15 11:51:10', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `personal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_usuario` enum('ADMINISTRADOR','COORDINADOR','ASISTENTE','CONSULTA') NOT NULL DEFAULT 'CONSULTA',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `personal_id`, `tipo_usuario`, `activo`, `remember_token`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ADMIN', '$2y$12$E3hvyqZZUiaPz8vemA1Qku1Fryu1m8/p46cnKRtFh69vQ/UHayljS', NULL, 'ADMINISTRADOR', 1, NULL, NULL, NULL, '2026-10-01 04:53:41', '2026-10-14 09:26:20', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_programa_estudio`
--

CREATE TABLE `usuario_programa_estudio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `acceso` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuario_programa_estudio`
--

INSERT INTO `usuario_programa_estudio` (`id`, `usuario_id`, `programa_estudio_id`, `acceso`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-15 06:39:51'),
(2, 1, 2, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-15 06:39:51'),
(3, 1, 3, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-15 06:39:51'),
(4, 1, 4, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-15 06:39:51'),
(5, 1, 5, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-15 06:39:51'),
(6, 1, 6, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-15 06:39:51'),
(7, 1, 7, 1, NULL, NULL, '2026-10-01 04:53:41', '2026-10-15 06:39:51');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bienes`
--
ALTER TABLE `bienes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bienes_cbi_unique` (`cbi`),
  ADD KEY `bienes_sala_id_foreign` (`sala_id`),
  ADD KEY `bienes_grupo_id_foreign` (`grupo_id`),
  ADD KEY `bienes_tipo_id_foreign` (`tipo_id`),
  ADD KEY `bienes_marca_id_foreign` (`marca_id`),
  ADD KEY `bienes_modelo_id_foreign` (`modelo_id`),
  ADD KEY `bienes_estado_id_foreign` (`estado_id`),
  ADD KEY `bienes_created_by_foreign` (`created_by`),
  ADD KEY `bienes_updated_by_foreign` (`updated_by`);

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
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `estados_nombre_unique` (`nombre`),
  ADD KEY `estados_created_by_foreign` (`created_by`),
  ADD KEY `estados_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grupos_programa_estudio_id_foreign` (`programa_estudio_id`),
  ADD KEY `grupos_created_by_foreign` (`created_by`),
  ADD KEY `grupos_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `instituciones_ruc_unique` (`ruc`),
  ADD KEY `instituciones_created_by_foreign` (`created_by`),
  ADD KEY `instituciones_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventarios_programa_estudio_id_serie_numero_unique` (`programa_estudio_id`,`serie`,`numero`),
  ADD KEY `inventarios_sala_id_foreign` (`sala_id`),
  ADD KEY `inventarios_coordinador_id_foreign` (`coordinador_id`),
  ADD KEY `inventarios_asistente_t1_id_foreign` (`asistente_t1_id`),
  ADD KEY `inventarios_asistente_t2_id_foreign` (`asistente_t2_id`),
  ADD KEY `inventarios_created_by_foreign` (`created_by`),
  ADD KEY `inventarios_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `inventario_detalles`
--
ALTER TABLE `inventario_detalles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inventario_detalles_inventario_id_bien_id_unique` (`inventario_id`,`bien_id`),
  ADD KEY `inventario_detalles_bien_id_foreign` (`bien_id`),
  ADD KEY `inventario_detalles_grupo_id_foreign` (`grupo_id`),
  ADD KEY `inventario_detalles_tipo_id_foreign` (`tipo_id`),
  ADD KEY `inventario_detalles_marca_id_foreign` (`marca_id`),
  ADD KEY `inventario_detalles_modelo_id_foreign` (`modelo_id`),
  ADD KEY `inventario_detalles_estado_id_foreign` (`estado_id`),
  ADD KEY `inventario_detalles_created_by_foreign` (`created_by`),
  ADD KEY `inventario_detalles_updated_by_foreign` (`updated_by`);

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
-- Indices de la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marcas_programa_estudio_id_foreign` (`programa_estudio_id`),
  ADD KEY `marcas_created_by_foreign` (`created_by`),
  ADD KEY `marcas_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `modelos`
--
ALTER TABLE `modelos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `modelos_programa_estudio_id_foreign` (`programa_estudio_id`),
  ADD KEY `modelos_created_by_foreign` (`created_by`),
  ADD KEY `modelos_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `personal`
--
ALTER TABLE `personal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_dni_unique` (`dni`),
  ADD KEY `personal_created_by_foreign` (`created_by`),
  ADD KEY `personal_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `programas_estudio`
--
ALTER TABLE `programas_estudio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `programas_estudio_institucion_id_foreign` (`institucion_id`),
  ADD KEY `programas_estudio_coordinador_id_foreign` (`coordinador_id`),
  ADD KEY `programas_estudio_asistente_t1_id_foreign` (`asistente_t1_id`),
  ADD KEY `programas_estudio_asistente_t2_id_foreign` (`asistente_t2_id`),
  ADD KEY `programas_estudio_created_by_foreign` (`created_by`),
  ADD KEY `programas_estudio_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `salas`
--
ALTER TABLE `salas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salas_programa_estudio_id_foreign` (`programa_estudio_id`),
  ADD KEY `salas_created_by_foreign` (`created_by`),
  ADD KEY `salas_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tipos_programa_estudio_id_foreign` (`programa_estudio_id`),
  ADD KEY `tipos_created_by_foreign` (`created_by`),
  ADD KEY `tipos_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuarios_username_unique` (`username`),
  ADD KEY `usuarios_created_by_foreign` (`created_by`),
  ADD KEY `usuarios_updated_by_foreign` (`updated_by`),
  ADD KEY `usuarios_personal_id_foreign` (`personal_id`);

--
-- Indices de la tabla `usuario_programa_estudio`
--
ALTER TABLE `usuario_programa_estudio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_programa_estudio_usuario_id_programa_estudio_id_unique` (`usuario_id`,`programa_estudio_id`),
  ADD KEY `usuario_programa_estudio_programa_estudio_id_foreign` (`programa_estudio_id`),
  ADD KEY `usuario_programa_estudio_created_by_foreign` (`created_by`),
  ADD KEY `usuario_programa_estudio_updated_by_foreign` (`updated_by`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bienes`
--
ALTER TABLE `bienes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `inventarios`
--
ALTER TABLE `inventarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario_detalles`
--
ALTER TABLE `inventario_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `marcas`
--
ALTER TABLE `marcas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `modelos`
--
ALTER TABLE `modelos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `programas_estudio`
--
ALTER TABLE `programas_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `salas`
--
ALTER TABLE `salas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario_programa_estudio`
--
ALTER TABLE `usuario_programa_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bienes`
--
ALTER TABLE `bienes`
  ADD CONSTRAINT `bienes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bienes_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`),
  ADD CONSTRAINT `bienes_grupo_id_foreign` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`),
  ADD CONSTRAINT `bienes_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`),
  ADD CONSTRAINT `bienes_modelo_id_foreign` FOREIGN KEY (`modelo_id`) REFERENCES `modelos` (`id`),
  ADD CONSTRAINT `bienes_sala_id_foreign` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`),
  ADD CONSTRAINT `bienes_tipo_id_foreign` FOREIGN KEY (`tipo_id`) REFERENCES `tipos` (`id`),
  ADD CONSTRAINT `bienes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `estados`
--
ALTER TABLE `estados`
  ADD CONSTRAINT `estados_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `estados_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD CONSTRAINT `grupos_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grupos_programa_estudio_id_foreign` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`),
  ADD CONSTRAINT `grupos_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD CONSTRAINT `instituciones_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `instituciones_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `inventarios`
--
ALTER TABLE `inventarios`
  ADD CONSTRAINT `inventarios_asistente_t1_id_foreign` FOREIGN KEY (`asistente_t1_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventarios_asistente_t2_id_foreign` FOREIGN KEY (`asistente_t2_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventarios_coordinador_id_foreign` FOREIGN KEY (`coordinador_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventarios_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventarios_programa_estudio_id_foreign` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`),
  ADD CONSTRAINT `inventarios_sala_id_foreign` FOREIGN KEY (`sala_id`) REFERENCES `salas` (`id`),
  ADD CONSTRAINT `inventarios_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `inventario_detalles`
--
ALTER TABLE `inventario_detalles`
  ADD CONSTRAINT `inventario_detalles_bien_id_foreign` FOREIGN KEY (`bien_id`) REFERENCES `bienes` (`id`),
  ADD CONSTRAINT `inventario_detalles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_detalles_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_detalles_grupo_id_foreign` FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_detalles_inventario_id_foreign` FOREIGN KEY (`inventario_id`) REFERENCES `inventarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inventario_detalles_marca_id_foreign` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_detalles_modelo_id_foreign` FOREIGN KEY (`modelo_id`) REFERENCES `modelos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_detalles_tipo_id_foreign` FOREIGN KEY (`tipo_id`) REFERENCES `tipos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `inventario_detalles_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `marcas`
--
ALTER TABLE `marcas`
  ADD CONSTRAINT `marcas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `marcas_programa_estudio_id_foreign` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`),
  ADD CONSTRAINT `marcas_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `modelos`
--
ALTER TABLE `modelos`
  ADD CONSTRAINT `modelos_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `modelos_programa_estudio_id_foreign` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`),
  ADD CONSTRAINT `modelos_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `personal`
--
ALTER TABLE `personal`
  ADD CONSTRAINT `personal_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `personal_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `programas_estudio`
--
ALTER TABLE `programas_estudio`
  ADD CONSTRAINT `programas_estudio_asistente_t1_id_foreign` FOREIGN KEY (`asistente_t1_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `programas_estudio_asistente_t2_id_foreign` FOREIGN KEY (`asistente_t2_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `programas_estudio_coordinador_id_foreign` FOREIGN KEY (`coordinador_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `programas_estudio_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `programas_estudio_institucion_id_foreign` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`),
  ADD CONSTRAINT `programas_estudio_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `salas`
--
ALTER TABLE `salas`
  ADD CONSTRAINT `salas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `salas_programa_estudio_id_foreign` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`),
  ADD CONSTRAINT `salas_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `tipos`
--
ALTER TABLE `tipos`
  ADD CONSTRAINT `tipos_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tipos_programa_estudio_id_foreign` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`),
  ADD CONSTRAINT `tipos_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_personal_id_foreign` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuario_programa_estudio`
--
ALTER TABLE `usuario_programa_estudio`
  ADD CONSTRAINT `usuario_programa_estudio_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuario_programa_estudio_programa_estudio_id_foreign` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuario_programa_estudio_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuario_programa_estudio_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
