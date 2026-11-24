-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2026 a las 09:05:31
-- Versión del servidor: 10.11.19-MariaDB
-- Versión de PHP: 8.4.28

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
(5, 32, 18, '1111111111', 12, 'GABINETE', 9, 7, 'S/N', 5, NULL, 11, NULL, '2026-11-19 07:09:23', '2026-11-19 07:09:23', NULL),
(6, 34, 20, '2026000001', 13, 'CAMARA MIRRORLESS FULL FRAME 4K PARA GRABACION', 10, 8, 'SN-SNY-7401', 5, 'INCLUYE LENTE 28-70MM Y BATERIA', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(7, 33, 19, '2026000002', 14, 'MICROFONO DINAMICO VOCAL PARA CABINA DE PODCAST', 12, 10, 'SN-SHR-8821', 5, 'CON BRAZO ARTICULADO Y FILTRO ANTIPOP', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(8, 35, 22, '2026000003', 18, 'ESTACION COMPACTA DE RENDERIZADO DE VIDEO 4K', 15, 13, 'SN-APL-3310', 5, '32GB RAM, 1TB SSD', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(9, 37, 21, '2026000004', 16, 'REFLECTOR LED CONTINUO DE ALTA POTENCIA', 19, 17, 'SN-GDX-5509', 5, 'CON SOFTBOX OCTAGONAL Y PARANTE', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(10, 45, 31, '2026000005', 23, 'PC DE ESCRITORIO CORE I7 PARA SIMULACION EMPRESARIAL', 20, 18, 'SN-HP-40019', 5, '16GB RAM, SSD 512GB, WINDOWS 11 PRO', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(11, 47, 31, '2026000006', 24, 'LAPTOP EMPRESARIAL 15.6 PULGADAS COORDINACION', 21, 19, 'SN-LNV-90124', 5, 'CORE I7, 16GB RAM, CARGADOR ORIGINAL', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(12, 51, 32, '2026000007', 25, 'IMPRESORA MULTIFUNCIONAL SISTEMA CONTINUO WIFI', 22, 20, 'SN-EPS-77412', 5, 'OPERATIVA CON TANQUES LLENOS', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(13, 54, 39, '2026000008', 33, 'SWITCH GIGABIT ADMINISTRABLE CAPA 2 DE 24 PUERTOS', 30, 28, 'SN-CSC-29601', 5, 'INSTALADO EN RACK PRINCIPAL', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(14, 57, 43, '2026000009', 37, 'ESCANER MULTIMARCA DE DIAGNOSTICO ELECTRONICO', 34, 32, 'SN-LCH-43105', 5, 'CON MALETIN DE CONECTORES OBD1/OBD2', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(15, 61, 47, '2026000010', 41, 'MULTIMETRO DIGITAL TRUE RMS DE ALTA PRECISION', 38, 36, 'SN-FLK-87009', 5, 'CON PUNTAS DE PRUEBA Y FUNDA PROTECTORA', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(16, 41, 20, '2026000011', 13, 'CAMARA FOTOGRAFICA Y VIDEO CON SENSOR AVERIADO', 11, 9, 'SN-CNN-6102', 6, 'DADO DE BAJA POR FALLA DE OBTURADOR', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(17, 38, 19, '2026000012', 15, 'MEZCLADORA ANALOGICA DE 8 CANALES CON RUIDO', 13, 11, 'SN-BHR-8029', 6, 'CANALES 3 Y 4 INOPERATIVOS', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(18, 36, 23, '2026000013', 18, 'WORKSTATION ANTIGUA SIN SOPORTE DE HARDWARE', 14, 12, 'SN-DLL-3661', 6, 'PLACA MADRE QUEMADA', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(19, 42, 27, '2026000014', 22, 'PROYECTOR XGA CON LAMPARA AGOTADA', 17, 15, 'SN-EPS-2019', 6, 'PENDIENTE DE REEMPLAZO POR OBSOLESCENCIA', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(20, 39, 19, '2026000015', 14, 'MICROFONO DE ESTUDIO CON CAPSULA DAÑADA', 18, 16, 'SN-RDE-1104', 6, 'SUFRIO CAIDA EN PRACTICAS', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(21, 40, 26, '2026000016', 20, 'KIT PERIFERICO INALAMBRICO INCOMPLETO', 16, 14, 'SN-LGT-3098', 6, 'SIN RECEPTOR USB Y BATERIA AGOTADA', 6, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(22, 43, 31, '2026000017', 23, 'MONITOR LED 24 PULGADAS CON LINEAS VERTICALES', 24, 22, 'SN-SMG-2401', 2, 'PANEL LCD DAÑADO', 10, 1, '2026-11-21 22:27:29', '2026-11-21 22:33:17', NULL),
(23, 44, 31, '2026000018', 23, 'MONITOR FULL HD SIN ENCENDIDO', 25, 23, 'SN-LGE-4309', 6, 'FUENTE INTERNA AVERIADA', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(24, 48, 32, '2026000019', 25, 'IMPRESORA DE TINTA CON CABEZAL OBSTRUIDO', 23, 21, 'SN-BRT-5208', 6, 'REPARACION NO RENTABLE', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(25, 46, 37, '2026000020', 31, 'UPS RESPALDO DE ENERGIA 1000VA CON BATERIA MUERTA', 26, 24, 'SN-APC-1004', 6, 'EMITE ALARMA CONTINUA DE FALLA', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(26, 52, 37, '2026000021', 31, 'ESTABILIZADOR SOLIDO 1200VA CON RELE QUEMADO', 27, 25, 'SN-FRZ-1209', 6, 'RETIRADO DE LABORATORIO', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(27, 50, 31, '2026000022', 24, 'NOTEBOOK CON BISAGRA ROTA Y TECLADO DAÑADO', 28, 26, 'SN-ASU-1188', 6, 'ALMACENADO PARA DESCARTE', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(28, 49, 34, '2026000023', 32, 'PROYECTOR DLP CON CHIP DMD CON PUNTOS BLANCOS', 29, 27, 'SN-VWS-5032', 6, 'IMAGEN DISTORSIONADA', 10, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(29, 53, 40, '2026000024', 34, 'CPU OPTIPLEX GENERACION ANTIGUA SIN DISCO', 31, 29, 'SN-DLL-7019', 6, 'BAJA POR OBSOLESCENCIA TECNOLOGICA', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(30, 55, 41, '2026000025', 35, 'FOTOCOPIADORA MULTIFUNCIONAL CON FUSOR DAÑADO', 32, 30, 'SN-RCH-2004', 6, 'CODIGO DE ERROR SC542', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(31, 56, 41, '2026000026', 35, 'IMPRESORA LASER MONOCROMATICA CON RODILLO GASTADO', 33, 31, 'SN-HP-40488', 6, 'ATASCO PERMANENTE DE PAPEL', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(32, 58, 44, '2026000027', 38, 'ANALIZADOR DE MOTORES CON PANTALLA TACTIL ROTA', 35, 33, 'SN-BSH-5003', 6, 'PENDIENTE DE BAJA PATRIMONIAL', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(33, 59, 45, '2026000028', 39, 'COMPUTADORA DE ESCRITORIO CON FUENTE QUEMADA', 36, 34, 'SN-LNV-7005', 6, 'RETIRADA POR CORTOCIRCUITO', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(34, 60, 45, '2026000029', 40, 'CALCULADORA SUMADORA CON RODILLO DE TINTA SECO', 37, 35, 'SN-CSO-1702', 6, 'DISPLAY CON DIGITOS INCOMPLETOS', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(35, 62, 48, '2026000030', 42, 'AMOLADORA INDUSTRIAL DE BANCO CON BOBINA QUEMADA', 39, 37, 'SN-MKT-9020', 6, 'DADA DE BAJA POR DESGASTE DE MOTOR', 14, NULL, '2026-11-21 22:27:29', '2026-11-21 22:27:29', NULL),
(36, 53, 49, '154544', 34, 'MONITOR', 31, 29, '111111111111111111111111111', 5, NULL, 1, NULL, '2026-11-22 23:55:42', '2026-11-22 23:55:42', NULL),
(37, 54, 49, '12344', 34, 'MOUSE', 31, 29, '444444444444444', 5, NULL, 1, NULL, '2026-11-22 23:56:13', '2026-11-22 23:56:13', NULL);

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
(17, 14, 'COMPUTADORAS', 1, NULL, '2026-11-19 06:21:59', '2026-11-19 06:21:59', NULL),
(18, 17, 'PC-01', 11, NULL, '2026-11-19 07:08:21', '2026-11-19 07:08:21', NULL),
(19, 17, 'EQUIPOS DE AUDIO Y SONIDO', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(20, 17, 'CAMARAS Y VIDEO', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(21, 17, 'ILUMINACION DE ESTUDIO', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(22, 17, 'ESTACIONES DE EDICION', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(23, 17, 'EQUIPOS DE COMPUTO', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(24, 17, 'MOBILIARIO DE ESTUDIO', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(25, 17, 'REDES Y TRANSMISION', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(26, 17, 'PERIFERICOS MULTIMEDIA', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(27, 17, 'PROYECCION Y PANTALLAS', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(28, 17, 'ACCESORIOS FOTOGRAFICOS', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(29, 14, 'MOBILIARIO DE AULA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(30, 14, 'MOBILIARIO DE OFICINA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(31, 14, 'EQUIPOS INFORMATICOS', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(32, 14, 'IMPRESION Y ESCANEO', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(33, 14, 'CLIMATIZACION', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(34, 14, 'AUDIOVISUALES DE AULA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(35, 14, 'ARCHIVADORES Y ESTANTES', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(36, 14, 'COMUNICACIONES', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(37, 14, 'ENERGIA Y RESPALDO', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(38, 14, 'SEGURIDAD Y CONTROL', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(39, 18, 'SERVIDORES Y NETWORKING', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(40, 18, 'ESTACIONES DE PROGRAMACION', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(41, 19, 'EQUIPAMIENTO DE OFICINA', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(42, 19, 'MOBILIARIO ADMINISTRATIVO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(43, 20, 'DIAGNOSTICO Y ESCANEO VEHICULAR', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(44, 20, 'HERRAMIENTAS MECATRONICAS', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(45, 21, 'COMPUTO CONTABLE', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(46, 21, 'ARCHIVO DOCUMENTARIO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(47, 22, 'TABLEROS Y MEDICION ELECTRICA', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(48, 23, 'MAQUINARIA METALMECANICA', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(49, 18, 'PC01', 1, NULL, '2026-11-22 23:23:50', '2026-11-22 23:23:50', NULL),
(50, 18, 'PC03', 15, NULL, '2026-11-23 01:07:17', '2026-11-23 01:07:17', NULL);

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
(2, '20279889208', 'PRISMA', 'COLEGIO', 'Villa EL Salvador 15834', 1, 1, '2026-11-12 19:34:43', '2026-11-22 03:08:16', NULL),
(5, '33333333333', 'JUAN PABLO', 'INSTITUTO', 'por alla', 1, NULL, '2026-11-18 09:33:13', '2026-11-18 09:33:13', NULL),
(8, '20517637859', 'IESTP \"LURÍN\"', 'INSTITUTO', 'AA.HH. Villa Alejandro Mz. K Lote 15-16 1era Etapa, Lurín, Perú', 1, 1, '2026-11-19 11:14:17', '2026-11-19 11:59:42', NULL),
(9, '20330611023', 'IESTP JULIO C.TELLO', 'INSTITUTO', 'Bolivar n° 100, Villa EL Salvador 15834', 1, 1, '2026-11-21 04:29:26', '2026-11-22 03:06:25', NULL);

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
(9, 17, 'ACER', 1, NULL, '2026-11-19 06:48:33', '2026-11-19 06:48:33', NULL),
(10, 17, 'SONY', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(11, 17, 'CANON', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(12, 17, 'SHURE', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(13, 17, 'BEHRINGER', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(14, 17, 'DELL', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(15, 17, 'APPLE', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(16, 17, 'LOGITECH', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(17, 17, 'EPSON', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(18, 17, 'RODE', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(19, 17, 'GODOX', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(20, 14, 'HP', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(21, 14, 'LENOVO', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(22, 14, 'EPSON', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(23, 14, 'BROTHER', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(24, 14, 'SAMSUNG', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(25, 14, 'LG', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(26, 14, 'APC', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(27, 14, 'FORZA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(28, 14, 'ASUS', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(29, 14, 'VIEWSONIC', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(30, 18, 'CISCO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(31, 18, 'DELL', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(32, 19, 'RICOH', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(33, 19, 'HP', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(34, 20, 'LAUNCH', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(35, 20, 'BOSCH', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(36, 21, 'LENOVO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(37, 21, 'CASIO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(38, 22, 'FLUKE', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(39, 23, 'MAKITA', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(40, 18, 'LOGITECH', 15, NULL, '2026-11-23 01:04:36', '2026-11-23 01:04:36', NULL);

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
(7, 17, 'PRODESK', 11, NULL, '2026-11-19 07:06:05', '2026-11-19 07:06:05', NULL),
(8, 17, 'ALPHA A7 IV', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(9, 17, 'EOS R6 MARK II', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(10, 17, 'SM7B', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(11, 17, 'XENYX Q802USB', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(12, 17, 'PRECISION 3660', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(13, 17, 'MAC STUDIO M2', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(14, 17, 'MX MASTER 3S', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(15, 17, 'POWERLITE E20', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(16, 17, 'NT1-A', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(17, 17, 'SL60W LED', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(18, 14, 'PRODESK 400 G9', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(19, 14, 'THINKPAD E15', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(20, 14, 'ECOTANK L3250', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(21, 14, 'DCP-T520W', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(22, 14, 'ODYSSEY F24T35', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(23, 14, '24MK430H-B', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(24, 14, 'BACK-UPS BV1000', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(25, 14, 'FVR-1202', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(26, 14, 'EXPERTBOOK B1', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(27, 14, 'PA503S', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(28, 18, 'CATALYST 2960-X', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(29, 18, 'OPTIPLEX 7010', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(30, 19, 'IM C2000', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(31, 19, 'LASERJET PRO M404DN', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(32, 20, 'X431 PRO5', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(33, 20, 'FSA 500', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(34, 21, 'THINKCENTRE M70S', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(35, 21, 'HR-170RC', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(36, 22, 'FLUKE 87V', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(37, 23, 'GA9020', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(38, 18, 'LOGITIK', 15, NULL, '2026-11-23 00:57:59', '2026-11-23 00:57:59', NULL),
(39, 18, 'LOGITIK', 15, NULL, '2026-11-23 00:58:03', '2026-11-23 00:58:03', NULL),
(40, 18, 'LOGITIK', 15, NULL, '2026-11-23 00:58:04', '2026-11-23 00:58:04', NULL),
(41, 18, 'LOGITIK', 15, NULL, '2026-11-23 00:58:13', '2026-11-23 00:58:13', NULL),
(42, 18, 'LOGITIK', 15, NULL, '2026-11-23 00:58:35', '2026-11-23 00:58:35', NULL);

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
(5, 'CHAVEZ CONTRERAS', 'ROMULO', '77777777', '902607118', 'romulito@gmail.com', 'jr las fresias 271', NULL, NULL, '2026-11-19 08:49:16', '2026-11-19 08:49:16', NULL),
(7, 'CHAVEZ', 'MATIAS', '61139756', '902607118', 'matias@gmail.com', 'mz h lt', 6, NULL, '2026-11-19 11:31:04', '2026-11-19 11:31:04', NULL),
(8, 'CHAVEZ CONTRERAS', 'LUIS', '88888888', '444444444', 'matias2007jj@gmail.com', 'mz h', 14, NULL, '2026-11-23 04:58:06', '2026-11-23 04:58:06', NULL),
(9, 'SANCHEZ', 'TEO', '48917284', '912347164', 'teo@gmail.com', 'su casa ps chismoso', 14, NULL, '2026-11-24 18:33:55', '2026-11-24 18:33:55', NULL),
(10, 'BRAVO', 'PEDRO', '61241241', '932452785', 'pedrobravo@gmail.com', 'ahi ps', 14, NULL, '2026-11-24 18:35:35', '2026-11-24 18:35:35', NULL),
(11, 'MENDOZA RIOS', 'CARLOS ALBERTO', '71001001', '987101001', 'coord.admin@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(12, 'PAREDES SALAZAR', 'LUCIA FERNANDA', '71001002', '987101002', 'asist1.admin@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(13, 'TORRES VILLANUEVA', 'DIEGO ARMANDO', '71001003', '987101003', 'asist2.admin@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(14, 'HUAMAN CASTILLO', 'JORGE LUIS', '71001004', '987101004', 'coord.mecatronica@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(15, 'RAMIREZ CORDOVA', 'KEVIN ALEXANDER', '71001005', '987101005', 'asist1.mecatronica@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(16, 'FLORES GUTIERREZ', 'ROSA MARIA', '71001006', '987101006', 'asist2.mecatronica@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(17, 'SOTO FERNANDEZ', 'PATRICIA ELENA', '71001007', '987101007', 'coord.contabilidad@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(18, 'ROJAS MEDINA', 'ANDREA VALERIA', '71001008', '987101008', 'asist1.contabilidad@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(19, 'DIAZ PALOMINO', 'MIGUEL ANGEL', '71001009', '987101009', 'asist2.contabilidad@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(20, 'VARGAS MORALES', 'HECTOR MANUEL', '71001010', '987101010', 'coord.electricidad@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(21, 'CASTRO NAVARRO', 'BRYAN JOSUE', '71001011', '987101011', 'asist1.electricidad@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(22, 'SILVA HERRERA', 'CARMEN JULIA', '71001012', '987101012', 'asist2.electricidad@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(23, 'CAMPOS ORTIZ', 'RICARDO ANTONIO', '71001013', '987101013', 'coord.mecanica@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(24, 'GOMEZ REYES', 'JOSE FERNANDO', '71001014', '987101014', 'asist1.mecanica@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(25, 'ALVAREZ DELGADO', 'SOFIA BEATRIZ', '71001015', '987101015', 'asist2.mecanica@tello.edu.pe', 'VILLA EL SALVADOR', 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL);

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
(5, 4, 14, 'ASISTENTE', '2026-11-19 11:17:36', '2026-11-19 11:17:36'),
(7, 7, 17, 'ASISTENTE', '2026-11-19 11:31:04', '2026-11-19 11:31:04'),
(8, 5, 14, 'COORDINADOR', '2026-11-19 11:32:49', '2026-11-19 11:32:49'),
(11, 8, 18, 'DOCENTE', '2026-11-24 18:32:33', '2026-11-24 18:32:33'),
(12, 9, 18, 'ASISTENTE', '2026-11-24 18:33:55', '2026-11-24 18:33:55'),
(13, 10, 18, 'COORDINADOR', '2026-11-24 18:35:36', '2026-11-24 18:35:36'),
(14, 11, 19, 'COORDINADOR', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(15, 12, 19, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(16, 13, 19, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(17, 14, 20, 'COORDINADOR', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(18, 15, 20, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(19, 16, 20, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(20, 17, 21, 'COORDINADOR', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(21, 18, 21, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(22, 19, 21, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(23, 20, 22, 'COORDINADOR', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(24, 21, 22, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(25, 22, 22, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(26, 23, 23, 'COORDINADOR', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(27, 24, 23, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(28, 25, 23, 'ASISTENTE', '2026-11-24 07:41:09', '2026-11-24 07:41:09');

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
(14, 8, 'ADMINISTRACION', 'ADMIN', 5, NULL, NULL, NULL, 1, 10, NULL, '2026-11-19 11:16:26', '2026-11-19 11:32:49', NULL),
(17, 5, 'COMUNICACION', 'COM', NULL, NULL, NULL, NULL, 1, 6, NULL, '2026-11-19 11:30:34', '2026-11-19 11:30:34', NULL),
(18, 9, 'DESARROLLO DE SISTEMAS DE INFORMACION', 'DSI', 10, NULL, NULL, NULL, 1, 14, NULL, '2026-11-21 04:30:09', '2026-11-24 18:35:36', NULL),
(19, 9, 'ASISTENTE ADMINISTRATIVO', 'ADMIN', 11, 12, 13, NULL, 1, 14, 14, '2026-11-21 04:30:28', '2026-11-24 07:41:09', NULL),
(20, 9, 'MECATRONICA AUTOMOTRIZ', 'MECATRONICA', 14, 15, 16, NULL, 1, 14, 14, '2026-11-21 04:30:52', '2026-11-24 07:41:09', NULL),
(21, 9, 'CONTABILIDAD', 'CONTA', 17, 18, 19, NULL, 1, 14, 14, '2026-11-21 04:31:13', '2026-11-24 07:41:09', NULL),
(22, 9, 'ELECTRICIDAD INDUSTRIAL', NULL, 20, 21, 22, NULL, 1, 14, 14, '2026-11-21 04:31:39', '2026-11-24 07:41:09', NULL),
(23, 9, 'MECANICA DE PRODUCCION', NULL, 23, 24, 25, NULL, 1, 14, 14, '2026-11-21 04:32:07', '2026-11-24 07:41:09', NULL);

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
(32, 17, 'AULA DE COMPUTACION N° 1', NULL, 11, NULL, '2026-11-19 07:06:29', '2026-11-19 07:06:29', NULL),
(33, 17, 'ESTUDIO DE RADIO Y PODCAST', 'Pabellón A - 1er Piso', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(34, 17, 'SET DE TELEVISION Y PRENSA', 'Pabellón A - 2do Piso', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(35, 17, 'LABORATORIO DE EDICION AUDIOVISUAL', 'Pabellón B - Aula 102', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(36, 17, 'SALA DE REDACCION DIGITAL', 'Pabellón B - Aula 104', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(37, 17, 'ESTUDIO DE FOTOGRAFIA E ILUMINACION', 'Pabellón C - Aula 201', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(38, 17, 'CABINA DE LOCUCION Y SONIDO', 'Pabellón A - Cabina 1', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(39, 17, 'AULA MULTIMEDIA 101', 'Pabellón C - 1er Piso', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(40, 17, 'AULA MULTIMEDIA 102', 'Pabellón C - 1er Piso', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(41, 17, 'ALMACEN DE EQUIPOS AUDIOVISUALES', 'Sótano - Módulo 2', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(42, 17, 'SALA DE PROYECCIONES Y ANALISIS', 'Pabellón B - 2do Piso', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(43, 14, 'AULA 101 - ADMINISTRACION', 'Pabellón Principal - 1er Piso', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(44, 14, 'AULA 102 - ADMINISTRACION', 'Pabellón Principal - 1er Piso', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(45, 14, 'LABORATORIO DE GESTION EMPRESARIAL', 'Pabellón B - 2do Piso', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(46, 14, 'SALA DE SIMULACION FINANCIERA', 'Pabellón B - 2do Piso', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(47, 14, 'COORDINACION ACADEMICA ADMIN', 'Pabellón Administrativo', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(48, 14, 'SALA DE DOCENTES ADMINISTRACION', 'Pabellón Administrativo', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(49, 14, 'AUDITORIO DE CONFERENCIAS', 'Bloque Central', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(50, 14, 'SALA DE REUNIONES Y PROYECTOS', 'Pabellón B - 1er Piso', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(51, 14, 'ARCHIVO Y TRAMITE DOCUMENTARIO', 'Pabellón Administrativo', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(52, 14, 'ALMACEN DE OFICINA', 'Bloque de Servicios', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(53, 18, 'LABORATORIO DE DESARROLLO 01', 'Pabellón DSI - 1er Piso', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(54, 18, 'LABORATORIO DE REDES Y SERVIDORES', 'Pabellón DSI - 2do Piso', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(55, 19, 'TALLER DE GESTION SECRETARIAL', 'Pabellón Administrativo - Aula 105', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(56, 19, 'SALA DE OFIMATICA EJECUTIVA', 'Pabellón Administrativo - Aula 106', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(57, 20, 'TALLER DE DIAGNOSTICO AUTOMOTRIZ', 'Nave Industrial 1', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(58, 20, 'LABORATORIO DE ELECTRONICA VEHICULAR', 'Nave Industrial 1 - Módulo B', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(59, 21, 'LABORATORIO CONTABLE Y TRIBUTARIO', 'Pabellón Contabilidad - Aula 201', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(60, 21, 'AULA DE AUDITORIA FINANCIERA', 'Pabellón Contabilidad - Aula 202', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(61, 22, 'TALLER DE INSTALACIONES ELECTRICAS', 'Nave Industrial 2', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(62, 23, 'TALLER DE TORNO Y FRESADORA', 'Nave Industrial 3', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(63, 18, 'LABORATORIO 3', NULL, 15, NULL, '2026-11-23 01:10:21', '2026-11-23 01:10:21', NULL);

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
('0fyBxyeOMU8DlHt7UYId5rfSXK0pqK3aH06thALK', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiTTV0cmtUcE9Ra0Y1RXFPWHVvUno3ZU5YMEc2Tkx3TzVSN1hkejBZNyI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FsbWFjZW4vYmllbmVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6IjczMzIyN2U0MDllNjEyNzcxNjUwNDk3MGRjODcyMWFjYWVjMTA3YWQwMjgwZWNlY2E0ZGU0NjYzZDllYTg3YzciO3M6MTk6InByb2dyYW1hX2VzdHVkaW9faWQiO2k6MTQ7fQ==', 1795503047),
('DszOW28c3v5znUYAHGgOffUt7P6pk3C94ljj6Ag8', 14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiTVhSbFFBV2IwNU5IaUhKb0VuVk5QdDB5WGYzWjhuT2tzOU5xUm8yZyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC91c3VhcmlvcyI7czo1OiJyb3V0ZSI7czoxNDoidXN1YXJpb3MuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxNDtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZjhhNzU3MDg4OTExMWZhNGIyYTUxZGY0ZTBhNzlhOTk3OTMyOWJmY2NhOWU2ZGI1MDE1ZmM0ZjgyOWUwNWRhYSI7czoxOToicHJvZ3JhbWFfZXN0dWRpb19pZCI7aToxOTt9', 1795506885),
('I1AdZYf7nYKdZeoi9rakj4Spkm3s0uoQmTMO8jLW', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiTnN1S2RWWUpNOEw4VDNFUEN6UlF2ZlVLaExSdGx0NU1wTkM1cWlLQyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hbG1hY2VuL2JpZW5lcyI7czo1OiJyb3V0ZSI7czoxMjoiYmllbmVzLmluZGV4Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NjtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZjk3NDA5ZTg0ZWU1ZmVhZTNiZDZlZDAzMjZjZTVmMDdkY2I5MDFlY2QzNTNkNTRjMmY4NmE4MGNmNmM0YjliMiI7czoxOToicHJvZ3JhbWFfZXN0dWRpb19pZCI7aToxNzt9', 1795397924),
('P8BDoPA9hXlEg4Vuni5Go0UtgH2QCL7Ix46gF21e', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiQmJYUDA4UXhEeGt2RWpqVXdPNFBFbHlxaXhDdHdyWlNHaVY5Q1NXNCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tZW51Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiNDg1ZTQ0ZWRjMmM0NThlZGQ4NmVjMTllNDAwMTNmMzg2ZDRmODAxY2RhZGU2M2E4NWM2NDQ2ZDYyZThkZTBjYiI7czoxOToicHJvZ3JhbWFfZXN0dWRpb19pZCI7aToxNDt9', 1795388173),
('yRNKDyXpGk66dURVKuWJk3RBAGqkoKAhAvRfnDb4', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiTXBFaWxpdWRkZkZpMWRUUHYzdk5xdmRJNDBsVWI4WUdrSVQxSkJmNyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL21lbnUiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1MToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2FsbWFjZW4vYmllbmVzL2V4cG9ydGFyLWV4Y2VsIjtzOjU6InJvdXRlIjtzOjIxOiJiaWVuZXMuZXhwb3J0YXIuZXhjZWwiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiI3MzMyMjdlNDA5ZTYxMjc3MTY1MDQ5NzBkYzg3MjFhY2FlYzEwN2FkMDI4MGVjZWNhNGRlNDY2M2Q5ZWE4N2M3IjtzOjE5OiJwcm9ncmFtYV9lc3R1ZGlvX2lkIjtpOjE0O30=', 1795504129);

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
(12, 17, 'GABINETE', 11, NULL, '2026-11-19 07:06:45', '2026-11-19 07:06:45', NULL),
(13, 17, 'CAMARA DE VIDEO 4K', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(14, 17, 'MICROFONO CONDENSADOR', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(15, 17, 'CONSOLA MEZCLADORA', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(16, 17, 'PANEL LED DE ESTUDIO', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(17, 17, 'TRIPODE PROFESIONAL', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(18, 17, 'ESTACION DE TRABAJO PC', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(19, 17, 'MONITOR DE REFERENCIA', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(20, 17, 'AUDIFONOS DE MONITOREO', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(21, 17, 'GRABADORA DIGITAL', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(22, 17, 'PROYECTOR MULTIMEDIA', 6, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(23, 14, 'COMPUTADORA DE ESCRITORIO', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(24, 14, 'LAPTOP EJECUTIVA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(25, 14, 'IMPRESORA MULTIFUNCIONAL', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(26, 14, 'ESCRITORIO DE MELAMINA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(27, 14, 'SILLA ERGONOMICA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(28, 14, 'CARPETA UNIPERSONAL', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(29, 14, 'PIZARRA ACRILICA', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(30, 14, 'ESTANTE METALICO', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(31, 14, 'ESTABILIZADOR DE VOLTAJE', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(32, 14, 'ECRAN RETRACTIL', 10, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(33, 18, 'SWITCH ADMINISTRABLE 24P', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(34, 18, 'WORKSTATION DESARROLLO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(35, 19, 'FOTOCOPIADORA DE ALTO RENDIMIENTO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(36, 19, 'MODULO DE RECEPCION', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(37, 20, 'ESCANER AUTOMOTRIZ OBD2', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(38, 20, 'OSCILOSCOPIO AUTOMOTRIZ', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(39, 21, 'TERMINAL CONTABLE PC', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(40, 21, 'CALCULADORA IMPRESORA', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(41, 22, 'MULTIMETRO DIGITAL INDUSTRIAL', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL),
(42, 23, 'TORNO PARALELO', 14, NULL, '2026-11-21 22:23:56', '2026-11-21 22:23:56', NULL);

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
(6, 'juanp@gmail.com', '$2y$12$bAUnJ4AkjvSFzkwvi9A5euw7OZVNEk00UGPAJrqvtbLbryv2YWxH2', NULL, 5, 'ADMIN_INSTITUCION', 1, NULL, 1, 1, '2026-11-18 09:33:14', '2026-11-19 11:29:53', NULL),
(7, 'sergio123@gmail.com', '$2y$12$iu7cVPgrmKyKLiXVBXYrBu.XUwnrm0gpUviPfpXDmw2TCOi27JYNG', 4, 5, 'ASISTENTE', 1, NULL, 6, NULL, '2026-11-18 09:39:53', '2026-11-18 09:39:53', NULL),
(10, 'lurin123@gmail.com', '$2y$12$UMYQR1etBl0u/F5tmfjPIeTzQRqyXkYWKh4G58keA4tuQVL16sBkW', NULL, 8, 'ADMIN_INSTITUCION', 1, NULL, 1, 1, '2026-11-19 11:14:17', '2026-11-19 12:08:10', NULL),
(11, 'matias', '$2y$12$RMccRg7cDIm9mSK.mOYv2ObX5dPXXd4Fgkqn2zW5dTeQlyPl2yNAW', 7, 5, 'ADMIN_INSTITUCION', 1, NULL, 6, 1, '2026-11-19 11:31:38', '2026-11-19 12:05:26', NULL),
(12, 'romulo', '$2y$12$XHGi0ra3cvQtO84wCcsF6OfSI4WPh.PoBI1g4lzttEehlyMu807GK', 5, 8, 'COORDINADOR', 1, NULL, 10, NULL, '2026-11-19 11:33:17', '2026-11-19 11:33:17', NULL),
(14, 'Tello@gmail.com', '$2y$12$90TQWfGxNkKhzaAHUkaugOebIjXn7ekYVk2c3lvLjZtutCxyXCG2W', NULL, 9, 'ADMIN_INSTITUCION', 1, NULL, 1, NULL, '2026-11-21 04:29:26', '2026-11-21 04:29:26', NULL),
(15, 'luis', '$2y$12$sbzaSGRn4Z0sHuIDcU79KekWx9lqqgM2/82OM8qviRoykdTTUuUKq', 8, 9, 'DOCENTE', 1, NULL, 14, 14, '2026-11-23 04:58:06', '2026-11-24 18:32:33', NULL),
(16, 'teo@gmail.com', '$2y$12$Om/pLnX1iLA1jUXDQYzDQu1A3Is.O0UN910EKsEi3vdtrec190IvS', 9, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 18:33:55', '2026-11-24 18:33:55', NULL),
(17, 'pedrobravo@gmail.com', '$2y$12$PLkUA.b51pkGOT5U5L3tX.gtbS06RZsQXSjro83noXpGVKwKeNykS', 10, 9, 'COORDINADOR', 1, NULL, 14, NULL, '2026-11-24 18:35:36', '2026-11-24 18:35:36', NULL),
(18, 'coord.admin@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 11, 9, 'COORDINADOR', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(19, 'asist1.admin@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 12, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(20, 'asist2.admin@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 13, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(21, 'coord.mecatronica@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 14, 9, 'COORDINADOR', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(22, 'asist1.mecatronica@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 15, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(23, 'asist2.mecatronica@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 16, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(24, 'coord.contabilidad@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 17, 9, 'COORDINADOR', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(25, 'asist1.contabilidad@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 18, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(26, 'asist2.contabilidad@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 19, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(27, 'coord.electricidad@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 20, 9, 'COORDINADOR', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(28, 'asist1.electricidad@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 21, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(29, 'asist2.electricidad@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 22, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(30, 'coord.mecanica@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 23, 9, 'COORDINADOR', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(31, 'asist1.mecanica@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 24, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL),
(32, 'asist2.mecanica@tello.edu.pe', '$2y$12$Iz/uPJ.NTvP264uSOaEOsOQjR/flonGMszSwc5Ck2/SyV.6F8OIe6', 25, 9, 'ASISTENTE', 1, NULL, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09', NULL);

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
(19, 12, 14, 1, NULL, NULL, '2026-11-19 11:33:17', '2026-11-19 11:33:17'),
(20, 15, 18, 1, NULL, NULL, '2026-11-23 04:58:06', '2026-11-24 18:32:33'),
(21, 16, 18, 1, NULL, NULL, '2026-11-24 18:33:55', '2026-11-24 18:33:55'),
(22, 17, 18, 1, NULL, NULL, '2026-11-24 18:35:36', '2026-11-24 18:35:36'),
(23, 18, 19, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(24, 19, 19, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(25, 20, 19, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(26, 21, 20, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(27, 22, 20, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(28, 23, 20, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(29, 24, 21, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(30, 25, 21, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(31, 26, 21, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(32, 27, 22, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(33, 28, 22, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(34, 29, 22, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(35, 30, 23, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(36, 31, 23, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09'),
(37, 32, 23, 1, 14, NULL, '2026-11-24 07:41:09', '2026-11-24 07:41:09');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `modelos`
--
ALTER TABLE `modelos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `personal`
--
ALTER TABLE `personal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `personal_programa_estudio`
--
ALTER TABLE `personal_programa_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `programas_estudio`
--
ALTER TABLE `programas_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `salas`
--
ALTER TABLE `salas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `tipos`
--
ALTER TABLE `tipos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `usuario_programa_estudio`
--
ALTER TABLE `usuario_programa_estudio`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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
