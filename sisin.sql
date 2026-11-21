-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2026 a las 02:24:35
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_bien` (IN `p_id` BIGINT, IN `p_sala_id` BIGINT, IN `p_grupo_id` BIGINT, IN `p_cbi` VARCHAR(10), IN `p_tipo_id` BIGINT, IN `p_descripcion` VARCHAR(100), IN `p_marca_id` BIGINT, IN `p_modelo_id` BIGINT, IN `p_serie_medida` VARCHAR(100), IN `p_estado_id` BIGINT, IN `p_otras_especificaciones` VARCHAR(100), IN `p_user_id` BIGINT)   BEGIN
    UPDATE bienes
    SET sala_id = p_sala_id,
        grupo_id = p_grupo_id,
        cbi = UPPER(TRIM(p_cbi)),
        tipo_id = p_tipo_id,
        descripcion = UPPER(TRIM(p_descripcion)),
        marca_id = p_marca_id,
        modelo_id = p_modelo_id,
        serie_medida = UPPER(TRIM(p_serie_medida)),
        estado_id = p_estado_id,
        otras_especificaciones = UPPER(TRIM(p_otras_especificaciones)),
        updated_by = p_user_id,
        updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_estado` (IN `p_id` BIGINT, IN `p_nombre` VARCHAR(60), IN `p_user_id` BIGINT)   BEGIN
    UPDATE estados 
    SET nombre = UPPER(TRIM(p_nombre)), updated_by = p_user_id, updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_grupo` (IN `p_id` BIGINT, IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE grupos 
    SET nombre = UPPER(TRIM(p_nombre)), programa_estudio_id = p_programa_id, updated_by = p_user_id, updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_marca` (IN `p_id` BIGINT, IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE marcas 
    SET nombre = UPPER(TRIM(p_nombre)), programa_estudio_id = p_programa_id, updated_by = p_user_id, updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_modelo` (IN `p_id` BIGINT, IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE modelos 
    SET nombre = UPPER(TRIM(p_nombre)), programa_estudio_id = p_programa_id, updated_by = p_user_id, updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_sala` (IN `p_id` BIGINT, IN `p_nombre` VARCHAR(60), IN `p_observacion` VARCHAR(200), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE salas 
    SET nombre = UPPER(TRIM(p_nombre)),
        observacion = p_observacion,
        programa_estudio_id = p_programa_id,
        updated_by = p_user_id,
        updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_tipo` (IN `p_id` BIGINT, IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE tipos 
    SET nombre = UPPER(TRIM(p_nombre)), programa_estudio_id = p_programa_id, updated_by = p_user_id, updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_desactivar_bien` (IN `p_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE bienes 
    SET estado_id = 6, -- ID 6 = INACTIVO
        updated_by = p_user_id,
        updated_at = NOW()
    WHERE id = p_id AND deleted_at IS NULL;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_estado` (IN `p_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE estados SET deleted_at = NOW(), updated_by = p_user_id WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_grupo` (IN `p_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE grupos SET deleted_at = NOW(), updated_by = p_user_id WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_marca` (IN `p_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE marcas SET deleted_at = NOW(), updated_by = p_user_id WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_modelo` (IN `p_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE modelos SET deleted_at = NOW(), updated_by = p_user_id WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_sala` (IN `p_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE salas SET deleted_at = NOW(), updated_by = p_user_id WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_eliminar_tipo` (IN `p_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    UPDATE tipos SET deleted_at = NOW(), updated_by = p_user_id WHERE id = p_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_bien` (IN `p_sala_id` BIGINT, IN `p_grupo_id` BIGINT, IN `p_cbi` VARCHAR(10), IN `p_tipo_id` BIGINT, IN `p_descripcion` VARCHAR(100), IN `p_marca_id` BIGINT, IN `p_modelo_id` BIGINT, IN `p_serie_medida` VARCHAR(100), IN `p_estado_id` BIGINT, IN `p_otras_especificaciones` VARCHAR(100), IN `p_user_id` BIGINT)   BEGIN
    INSERT INTO bienes (
        sala_id, grupo_id, cbi, tipo_id, descripcion, 
        marca_id, modelo_id, serie_medida, estado_id, 
        otras_especificaciones, created_by, created_at, updated_at
    ) VALUES (
        p_sala_id, p_grupo_id, UPPER(TRIM(p_cbi)), p_tipo_id, UPPER(TRIM(p_descripcion)), 
        p_marca_id, p_modelo_id, UPPER(TRIM(p_serie_medida)), p_estado_id, 
        UPPER(TRIM(p_otras_especificaciones)), p_user_id, NOW(), NOW()
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_estado` (IN `p_nombre` VARCHAR(60), IN `p_user_id` BIGINT)   BEGIN
    INSERT INTO estados (nombre, created_by, created_at, updated_at)
    VALUES (UPPER(TRIM(p_nombre)), p_user_id, NOW(), NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_grupo` (IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    INSERT INTO grupos (nombre, programa_estudio_id, created_by, created_at, updated_at)
    VALUES (UPPER(TRIM(p_nombre)), p_programa_id, p_user_id, NOW(), NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_marca` (IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    INSERT INTO marcas (nombre, programa_estudio_id, created_by, created_at, updated_at)
    VALUES (UPPER(TRIM(p_nombre)), p_programa_id, p_user_id, NOW(), NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_modelo` (IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    INSERT INTO modelos (nombre, programa_estudio_id, created_by, created_at, updated_at)
    VALUES (UPPER(TRIM(p_nombre)), p_programa_id, p_user_id, NOW(), NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_sala` (IN `p_nombre` VARCHAR(60), IN `p_observacion` VARCHAR(200), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    INSERT INTO salas (nombre, observacion, programa_estudio_id, created_by, created_at, updated_at)
    VALUES (UPPER(TRIM(p_nombre)), p_observacion, p_programa_id, p_user_id, NOW(), NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_tipo` (IN `p_nombre` VARCHAR(60), IN `p_programa_id` BIGINT, IN `p_user_id` BIGINT)   BEGIN
    INSERT INTO tipos (nombre, programa_estudio_id, created_by, created_at, updated_at)
    VALUES (UPPER(TRIM(p_nombre)), p_programa_id, p_user_id, NOW(), NOW());
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_usuario` (IN `p_username` VARCHAR(100), IN `p_password` VARCHAR(255), IN `p_tipo_usuario` VARCHAR(20), IN `p_institucion_id` BIGINT, IN `p_programa_estudio_id` BIGINT, IN `p_created_by` BIGINT)   BEGIN
    DECLARE v_usuario_id BIGINT;

    -- Registrar usuario con su institucion asignada
    INSERT INTO usuarios (username, password, tipo_usuario, institucion_id, activo, created_by, created_at, updated_at)
    VALUES (p_username, p_password, p_tipo_usuario, p_institucion_id, 1, p_created_by, NOW(), NOW());

    SET v_usuario_id = LAST_INSERT_ID();

    -- Registrar el programa de estudio de esa institucion
    IF p_programa_estudio_id IS NOT NULL THEN
        INSERT INTO usuario_programa_estudio (usuario_id, programa_estudio_id, acceso, created_by, created_at, updated_at)
        VALUES (v_usuario_id, p_programa_estudio_id, 1, p_created_by, NOW(), NOW());
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_bienes` (IN `p_programa_estudio_id` BIGINT, IN `p_institucion_id` BIGINT, IN `p_es_superadmin` TINYINT)   BEGIN
    SELECT 
        b.id,
        b.cbi,
        b.descripcion,
        b.serie_medida,
        b.otras_especificaciones,
        b.sala_id,
        s.nombre AS sala_nombre,
        s.programa_estudio_id,
        pe.nombre AS programa_estudio_nombre,
        i.nombre AS institucion_nombre,
        b.grupo_id,
        g.nombre AS grupo_nombre,
        b.tipo_id,
        t.nombre AS tipo_nombre,
        b.marca_id,
        m.nombre AS marca_nombre,
        b.modelo_id,
        mo.nombre AS modelo_nombre,
        b.estado_id,
        e.nombre AS estado_nombre,
        b.created_at,
        b.updated_at
    FROM bienes b
    INNER JOIN salas s ON b.sala_id = s.id
    INNER JOIN programas_estudio pe ON s.programa_estudio_id = pe.id
    INNER JOIN instituciones i ON pe.institucion_id = i.id
    INNER JOIN grupos g ON b.grupo_id = g.id
    INNER JOIN tipos t ON b.tipo_id = t.id
    INNER JOIN estados e ON b.estado_id = e.id
    LEFT JOIN marcas m ON b.marca_id = m.id
    LEFT JOIN modelos mo ON b.modelo_id = mo.id
    WHERE b.deleted_at IS NULL
      AND (
          -- Si es superadmin global ve todo; si filtró institución ve esa institución; si es usuario normal ve solo su programa
          (p_es_superadmin = 1 AND (p_institucion_id IS NULL OR pe.institucion_id = p_institucion_id))
          OR (p_es_superadmin = 0 AND s.programa_estudio_id = p_programa_estudio_id)
      )
    ORDER BY b.id DESC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_estados` ()   BEGIN
    SELECT id, nombre FROM estados WHERE deleted_at IS NULL ORDER BY nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_grupos` (IN `p_programa_estudio_id` BIGINT, IN `p_es_superadmin` TINYINT)   BEGIN
    IF p_es_superadmin = 1 THEN
        SELECT 
            g.id,
            g.nombre,
            g.programa_estudio_id,
            pe.nombre AS programa_estudio,
            pe.nombre AS programa_estudio_nombre,
            g.created_at,
            g.updated_at,
            DATE_FORMAT(g.created_at, '%d/%m/%Y %H:%i') AS fecha_creacion,
            DATE_FORMAT(g.updated_at, '%d/%m/%Y %H:%i') AS fecha_modificacion
        FROM grupos g
        INNER JOIN programas_estudio pe ON g.programa_estudio_id = pe.id
        WHERE g.deleted_at IS NULL
        ORDER BY g.nombre ASC;
    ELSE
        SELECT 
            g.id,
            g.nombre,
            g.programa_estudio_id,
            pe.nombre AS programa_estudio,
            pe.nombre AS programa_estudio_nombre,
            g.created_at,
            g.updated_at,
            DATE_FORMAT(g.created_at, '%d/%m/%Y %H:%i') AS fecha_creacion,
            DATE_FORMAT(g.updated_at, '%d/%m/%Y %H:%i') AS fecha_modificacion
        FROM grupos g
        INNER JOIN programas_estudio pe ON g.programa_estudio_id = pe.id
        WHERE g.programa_estudio_id = p_programa_estudio_id
          AND g.deleted_at IS NULL
        ORDER BY g.nombre ASC;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_marcas` (IN `p_programa_id` BIGINT)   BEGIN
    SELECT m.*, pe.nombre AS programa_estudio_nombre
    FROM marcas m
    LEFT JOIN programas_estudio pe ON pe.id = m.programa_estudio_id
    WHERE m.deleted_at IS NULL
      AND (p_programa_id IS NULL OR m.programa_estudio_id = p_programa_id)
    ORDER BY m.nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_modelos` (IN `p_programa_id` BIGINT)   BEGIN
    SELECT mo.*, pe.nombre AS programa_estudio_nombre
    FROM modelos mo
    LEFT JOIN programas_estudio pe ON pe.id = mo.programa_estudio_id
    WHERE mo.deleted_at IS NULL
      AND (p_programa_id IS NULL OR mo.programa_estudio_id = p_programa_id)
    ORDER BY mo.nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_salas` (IN `p_programa_id` BIGINT)   BEGIN
    SELECT s.*, pe.nombre AS programa_estudio_nombre
    FROM salas s
    LEFT JOIN programas_estudio pe ON pe.id = s.programa_estudio_id
    WHERE s.deleted_at IS NULL
      AND (p_programa_id IS NULL OR s.programa_estudio_id = p_programa_id)
    ORDER BY s.nombre ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_tipos` (IN `p_programa_id` BIGINT)   BEGIN
    SELECT t.*, pe.nombre AS programa_estudio_nombre
    FROM tipos t
    LEFT JOIN programas_estudio pe ON pe.id = t.programa_estudio_id
    WHERE t.deleted_at IS NULL
      AND (p_programa_id IS NULL OR t.programa_estudio_id = p_programa_id)
    ORDER BY t.nombre ASC;
END$$

DELIMITER ;

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

--
-- Volcado de datos para la tabla `bienes`
--

INSERT INTO `bienes` (`id`, `sala_id`, `grupo_id`, `cbi`, `tipo_id`, `descripcion`, `marca_id`, `modelo_id`, `serie_medida`, `estado_id`, `otras_especificaciones`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 31, 17, '0111', 10, 'LABORATORIO', NULL, NULL, '78451', 5, 'LLEGÓ DESACTUALIZADO', 1, NULL, '2026-11-19 06:37:20', '2026-11-19 06:37:20', NULL),
(5, 32, 18, '1111111111', 12, 'GABINETE', 9, 7, 'S/N', 5, NULL, 11, NULL, '2026-11-19 07:09:23', '2026-11-19 07:09:23', NULL);

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
(4, 'MALO', NULL, NULL, '2026-10-01 04:53:41', '2026-10-01 04:53:41', NULL),
(5, 'ACTIVO', 1, 1, '2026-11-19 02:38:34', '2026-11-19 06:02:50', NULL),
(6, 'INACTIVO', 1, NULL, '2026-11-19 02:38:45', '2026-11-19 02:38:45', NULL);

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

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id`, `programa_estudio_id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(13, 14, 'PC-1', 10, NULL, '2026-11-19 06:18:52', '2026-11-19 06:18:52', NULL),
(14, 14, 'PC-2', 10, NULL, '2026-11-19 06:19:04', '2026-11-19 06:19:04', NULL),
(15, 13, 'ECO', 9, 9, '2026-11-19 06:19:33', '2026-11-19 06:19:33', '2026-11-19 06:19:38'),
(17, 14, 'COMPUTADORAS', 1, NULL, '2026-11-19 06:21:59', '2026-11-19 06:21:59', NULL),
(18, 17, 'PC-01', 11, NULL, '2026-11-19 07:08:21', '2026-11-19 07:08:21', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE `instituciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ruc` char(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `tipo` varchar(20) NOT NULL DEFAULT 'INSTITUTO',
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

INSERT INTO `instituciones` (`id`, `ruc`, `nombre`, `tipo`, `direccion`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '11111111111', 'IESTP JULIO C. TELLO', 'INSTITUTO', 'Villa El Salvador', NULL, 1, '2026-10-01 04:53:41', '2026-11-21 04:33:49', '2026-11-21 04:33:49'),
(2, '20279889208', 'PRISMA', 'COLEGIO', 'AV. VILAUTA NRO. SN SANTA RITA (FRENTE AL CEMENTERIO PARQUE DEL RECUERDO) TACNA - TACNA - CALANA', 1, 1, '2026-11-12 19:34:43', '2026-11-12 20:09:14', NULL),
(3, '19815689445', 'UNTELS', 'UNIVERSIDAD', 'al frente de las cachangas', 1, NULL, '2026-11-12 20:09:03', '2026-11-12 20:09:03', NULL),
(5, '33333333333', 'JUAN PABLO', 'INSTITUTO', 'por alla', 1, NULL, '2026-11-18 09:33:13', '2026-11-18 09:33:13', NULL),
(6, '44444444444', 'MARIA AUXILIADORA', 'COLEGIO', 'AV LAS CASUARINAS', 1, NULL, '2026-11-19 08:47:12', '2026-11-19 08:47:12', NULL),
(7, '99999999999', 'JORGE BASADRE', 'COLEGIO', 'AV. EL SOL', 1, NULL, '2026-11-19 11:09:58', '2026-11-19 11:09:58', NULL),
(8, '20517637859', 'IESTP \"LURÍN\"', 'INSTITUTO', 'AA.HH. Villa Alejandro Mz. K Lote 15-16 1era Etapa, Lurín, Perú', 1, 1, '2026-11-19 11:14:17', '2026-11-19 11:59:42', NULL),
(9, '20330611023', 'INSTITUTO DE EDUCACIÓN SUPERIOR TECNOLÓGICO PÚBLICO JULIO CÉSAR TELLO', 'INSTITUTO', 'Bolivar n° 100, Villa EL Salvador 15834', 1, NULL, '2026-11-21 04:29:26', '2026-11-21 04:29:26', NULL);

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

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `programa_estudio_id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 17, 'ACER', 1, NULL, '2026-11-19 06:48:33', '2026-11-19 06:48:33', NULL);

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

--
-- Volcado de datos para la tabla `modelos`
--

INSERT INTO `modelos` (`id`, `programa_estudio_id`, `nombre`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, 17, 'PRODESK', 11, NULL, '2026-11-19 07:06:05', '2026-11-19 07:06:05', NULL);

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

--
-- Volcado de datos para la tabla `personal`
--

INSERT INTO `personal` (`id`, `apellidos`, `nombres`, `dni`, `celular`, `email`, `direccion`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 'VEGA', 'JOSE', '12345456', '954982930', 'script_t1@dsitello.ed.pe', 'AV. VILAUTA NRO. SN SANTA RITA (FRENTE AL CEMENTERIO PARQUE DEL RECUERDO) TACNA - TACNA - CALANA', 1, NULL, '2026-11-12 18:58:53', '2026-11-12 18:58:53', NULL),
(3, 'QUISPE', 'TEO', '54195159', '948994325', 'teo@gmail.com', 'su casa', 1, NULL, '2026-11-12 19:18:47', '2026-11-12 19:18:47', NULL),
(4, 'VASQUEZ', 'SERGIO', '12345678', '987654321', 'sergio123@gmail.com', 'por alla', 6, NULL, '2026-11-18 09:35:23', '2026-11-18 09:35:23', NULL),
(5, 'CHAVEZ CONTRERAS', 'ROMULO', '77777777', '902607118', 'romulito@gmail.com', 'jr las fresias 271', 8, NULL, '2026-11-19 08:49:16', '2026-11-19 08:49:16', NULL),
(6, 'ZAPATA', 'PAOLO', '45986535', '978645358', 'paolo@gmail.com', 'Av. Micaela Bastida (Sector 2, Grupo 22), Villa El Salvador', 9, NULL, '2026-11-19 11:18:53', '2026-11-19 11:18:53', NULL),
(7, 'CHAVEZ', 'MATIAS', '61139756', '902607118', 'matias@gmail.com', 'mz h lt', 6, NULL, '2026-11-19 11:31:04', '2026-11-19 11:31:04', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_programa_estudio`
--

CREATE TABLE `personal_programa_estudio` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `personal_id` bigint(20) UNSIGNED NOT NULL,
  `programa_estudio_id` bigint(20) UNSIGNED NOT NULL,
  `cargo` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personal_programa_estudio`
--

INSERT INTO `personal_programa_estudio` (`id`, `personal_id`, `programa_estudio_id`, `cargo`, `created_at`, `updated_at`) VALUES
(4, 5, 12, 'COORDINADOR', '2026-11-19 08:49:16', '2026-11-19 08:49:16'),
(5, 4, 14, 'ASISTENTE', '2026-11-19 11:17:36', '2026-11-19 11:17:36'),
(6, 6, 13, 'DOCENTE', '2026-11-19 11:18:53', '2026-11-19 11:19:09'),
(7, 7, 17, 'ASISTENTE', '2026-11-19 11:31:04', '2026-11-19 11:31:04'),
(8, 5, 14, 'COORDINADOR', '2026-11-19 11:32:49', '2026-11-19 11:32:49');

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
(9, 3, 'INGENIERÍA AMBIENTAL', NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, '2026-11-12 20:10:47', '2026-11-12 20:10:47', NULL),
(12, 6, 'MATEMATICA', 'MAT', 5, NULL, NULL, NULL, 1, 8, NULL, '2026-11-19 08:48:37', '2026-11-19 08:49:16', NULL),
(13, 7, 'LITERATURA', 'LIT', NULL, NULL, NULL, NULL, 1, 9, NULL, '2026-11-19 11:15:17', '2026-11-19 11:19:09', NULL),
(14, 8, 'ADMINISTRACION', 'ADMIN', 5, NULL, NULL, NULL, 1, 10, NULL, '2026-11-19 11:16:26', '2026-11-19 11:32:49', NULL),
(15, 7, 'MATEMATICA', 'MAT', NULL, NULL, NULL, NULL, 1, 9, NULL, '2026-11-19 11:20:45', '2026-11-19 11:20:45', NULL),
(16, 7, 'COMPUTACION', 'COM', NULL, NULL, NULL, NULL, 1, 9, NULL, '2026-11-19 11:22:50', '2026-11-19 11:22:50', NULL),
(17, 5, 'COMUNICACION', 'COM', NULL, NULL, NULL, NULL, 1, 6, NULL, '2026-11-19 11:30:34', '2026-11-19 11:30:34', NULL),
(18, 9, 'DESARROLLO DE SISTEMAS DE INFORMACION', 'DSI', NULL, NULL, NULL, NULL, 1, 14, NULL, '2026-11-21 04:30:09', '2026-11-21 04:30:09', NULL),
(19, 9, 'ASISTENTE ADMINISTRATIVO', 'ADMIN', NULL, NULL, NULL, NULL, 1, 14, NULL, '2026-11-21 04:30:28', '2026-11-21 04:30:28', NULL),
(20, 9, 'MECATRONICA AUTOMOTRIZ', 'MECATRONICA', NULL, NULL, NULL, NULL, 1, 14, NULL, '2026-11-21 04:30:52', '2026-11-21 04:30:52', NULL),
(21, 9, 'CONTABILIDAD', 'CONTA', NULL, NULL, NULL, NULL, 1, 14, NULL, '2026-11-21 04:31:13', '2026-11-21 04:31:13', NULL),
(22, 9, 'ELECTRICIDAD INDUSTRIAL', NULL, NULL, NULL, NULL, NULL, 1, 14, NULL, '2026-11-21 04:31:39', '2026-11-21 04:31:39', NULL),
(23, 9, 'MECANICA DE PRODUCCION', NULL, NULL, NULL, NULL, NULL, 1, 14, NULL, '2026-11-21 04:32:07', '2026-11-21 04:32:07', NULL);

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

--
-- Volcado de datos para la tabla `salas`
--

INSERT INTO `salas` (`id`, `programa_estudio_id`, `nombre`, `observacion`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(31, 14, 'AULA 204', NULL, 1, NULL, '2026-11-19 06:36:43', '2026-11-19 06:36:43', NULL),
(32, 17, 'AULA DE COMPUTACION N° 1', NULL, 11, NULL, '2026-11-19 07:06:29', '2026-11-19 07:06:29', NULL);

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
('QI4NVPjPccoOzH7D0CXDtlNNhdcOkSidobsTYBGF', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiMzNiWWwxWHhteVFsY1o0bnFMMnRZUUxnWmVnM09aWVhvdUg3RUozVyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91c3VhcmlvcyI7czo1OiJyb3V0ZSI7czoxNDoidXN1YXJpb3MuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiIyNWIwNWJjZTZjODg1ODQ3MDVhY2EyYWE2MjJhZTMyZTIxOWQ3ZWM0MzEzMzlkMWZjMDQ2M2MzZWE3ZWZmOTk0IjtzOjE5OiJwcm9ncmFtYV9lc3R1ZGlvX2lkIjtpOjE0O30=', 1795224081);

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
(9, 14, 'TECLADO', 10, NULL, '2026-11-19 06:21:02', '2026-11-19 06:21:02', NULL),
(10, 14, 'MONITOR', 10, NULL, '2026-11-19 06:21:10', '2026-11-19 06:21:10', NULL),
(12, 17, 'GABINETE', 11, NULL, '2026-11-19 07:06:45', '2026-11-19 07:06:45', NULL);

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
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `personal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `institucion_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_usuario` enum('ADMINISTRADOR','ADMIN_INSTITUCION','COORDINADOR','ASISTENTE','DOCENTE') NOT NULL DEFAULT 'DOCENTE',
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

INSERT INTO `usuarios` (`id`, `username`, `password`, `personal_id`, `institucion_id`, `tipo_usuario`, `activo`, `remember_token`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ADMIN', '$2y$12$oc4wT.3PH/lemn7potgj0ewIEspD/f0jHGsI8sIBULRhivghpgZoC', NULL, NULL, 'ADMINISTRADOR', 1, NULL, NULL, NULL, '2026-10-01 04:53:41', '2026-10-14 09:26:20', NULL),
(4, 'Prisma@edu.pe', '$2y$12$/VYYV/vZEw6Os1oPeqtE/.AterbxbZb8DW3kbWbjEun5ynjNZ8m.6', NULL, 2, 'ADMIN_INSTITUCION', 1, NULL, 1, NULL, '2026-11-18 20:04:11', '2026-11-18 20:04:11', NULL),
(5, 'untels@gmail.com', '$2y$12$W0DyJCC8769PHPSRANKfP.ez.A72Sw2jxswT/8f9arQktNSWFyMsC', NULL, 3, 'ADMIN_INSTITUCION', 1, NULL, 1, 1, '2026-11-18 20:19:34', '2026-11-19 21:51:57', NULL),
(6, 'juanp@gmail.com', '$2y$12$bAUnJ4AkjvSFzkwvi9A5euw7OZVNEk00UGPAJrqvtbLbryv2YWxH2', NULL, 5, 'ADMIN_INSTITUCION', 1, NULL, 1, 1, '2026-11-18 09:33:14', '2026-11-19 11:29:53', NULL),
(7, 'sergio123@gmail.com', '$2y$12$iu7cVPgrmKyKLiXVBXYrBu.XUwnrm0gpUviPfpXDmw2TCOi27JYNG', 4, 5, 'ASISTENTE', 1, NULL, 6, NULL, '2026-11-18 09:39:53', '2026-11-18 09:39:53', NULL),
(8, 'maria@gmail.com', '$2y$12$bpGUrylUmBztYA2DEBBYwOMtcc0Whkm/yX3AouMlD2XI1G5J4vp16', NULL, 6, 'ADMIN_INSTITUCION', 1, NULL, 1, 1, '2026-11-19 08:47:12', '2026-11-21 05:22:57', NULL),
(9, 'basadre@gmail.com', '$2y$12$deF6dDLLUwBNwfJ..2iXrOqj6V07/lvgNyJlA1tezHj2wJ8iBAuAW', NULL, 7, 'ADMIN_INSTITUCION', 1, NULL, 1, NULL, '2026-11-19 11:09:58', '2026-11-19 11:09:58', NULL),
(10, 'lurin123@gmail.com', '$2y$12$UMYQR1etBl0u/F5tmfjPIeTzQRqyXkYWKh4G58keA4tuQVL16sBkW', NULL, 8, 'ADMIN_INSTITUCION', 1, NULL, 1, 1, '2026-11-19 11:14:17', '2026-11-19 12:08:10', NULL),
(11, 'matias', '$2y$12$RMccRg7cDIm9mSK.mOYv2ObX5dPXXd4Fgkqn2zW5dTeQlyPl2yNAW', 7, 5, 'ADMIN_INSTITUCION', 1, NULL, 6, 1, '2026-11-19 11:31:38', '2026-11-19 12:05:26', NULL),
(12, 'romulo', '$2y$12$XHGi0ra3cvQtO84wCcsF6OfSI4WPh.PoBI1g4lzttEehlyMu807GK', 5, 8, 'COORDINADOR', 1, NULL, 10, NULL, '2026-11-19 11:33:17', '2026-11-19 11:33:17', NULL),
(14, 'Tello@gmail.com', '$2y$12$90TQWfGxNkKhzaAHUkaugOebIjXn7ekYVk2c3lvLjZtutCxyXCG2W', NULL, 9, 'ADMIN_INSTITUCION', 1, NULL, 1, NULL, '2026-11-21 04:29:26', '2026-11-21 04:29:26', NULL);

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
(18, 11, 17, 1, NULL, NULL, '2026-11-19 11:31:38', '2026-11-19 12:05:26'),
(19, 12, 14, 1, NULL, NULL, '2026-11-19 11:33:17', '2026-11-19 11:33:17');

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
-- Indices de la tabla `personal_programa_estudio`
--
ALTER TABLE `personal_programa_estudio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_programa_cargo_unique` (`personal_id`,`programa_estudio_id`,`cargo`),
  ADD KEY `ppe_programa_fk` (`programa_estudio_id`);

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
  ADD KEY `usuarios_personal_id_foreign` (`personal_id`),
  ADD KEY `usuarios_institucion_id_foreign` (`institucion_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `modelos`
--
ALTER TABLE `modelos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `personal_programa_estudio`
--
ALTER TABLE `personal_programa_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `programas_estudio`
--
ALTER TABLE `programas_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `salas`
--
ALTER TABLE `salas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuario_programa_estudio`
--
ALTER TABLE `usuario_programa_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- Filtros para la tabla `personal_programa_estudio`
--
ALTER TABLE `personal_programa_estudio`
  ADD CONSTRAINT `ppe_personal_fk` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ppe_programa_fk` FOREIGN KEY (`programa_estudio_id`) REFERENCES `programas_estudio` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `usuarios_institucion_id_foreign` FOREIGN KEY (`institucion_id`) REFERENCES `instituciones` (`id`) ON DELETE SET NULL,
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
