-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2015 at 04:43 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `clinica`
--

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE IF NOT EXISTS `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Área Estética', ''),
(2, 'Área Wellness', 'Área de la piscina');

-- --------------------------------------------------------

--
-- Table structure for table `cita`
--

CREATE TABLE IF NOT EXISTS `cita` (
  `id` bigint(19) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `hora_inicio` timestamp NULL DEFAULT NULL,
  `hora_fin` timestamp NULL DEFAULT NULL,
  `estado` tinyint(4) DEFAULT '0',
  `doctor_id` int(10) unsigned NOT NULL,
  `paciente_id` int(10) unsigned NOT NULL,
  `servicio_id` int(11) NOT NULL,
  `consultorio_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`paciente_id`,`servicio_id`,`consultorio_id`),
  KEY `fk_cita_usuario1_idx` (`doctor_id`),
  KEY `fk_cita_paciente1_idx` (`paciente_id`),
  KEY `fk_cita_servicio1_idx` (`servicio_id`),
  KEY `fk_cita_consultorio1_idx` (`consultorio_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

--
-- Dumping data for table `cita`
--

INSERT INTO `cita` (`id`, `fecha`, `hora_inicio`, `hora_fin`, `estado`, `doctor_id`, `paciente_id`, `servicio_id`, `consultorio_id`, `created_at`, `updated_at`) VALUES
(7, '2015-01-26', '2015-01-26 12:30:00', NULL, 1, 1, 9, 2, 1, NULL, NULL),
(8, '2015-02-27', '2015-02-27 13:00:00', '2015-02-27 13:30:00', 1, 1, 10, 2, 1, '2015-02-26 23:12:52', '2015-02-26 23:12:52'),
(9, '2015-03-03', '2015-03-03 12:30:00', '2015-03-03 13:30:00', 1, 1, 9, 2, 1, '2015-03-01 01:01:57', '2015-03-01 01:01:57'),
(10, '2015-03-03', '2015-03-03 13:00:00', '2015-03-03 13:30:00', 1, 3, 8, 2, 1, '2015-03-01 04:43:57', '2015-03-01 14:34:11'),
(11, '2015-03-05', '2015-03-05 13:30:00', '2015-03-05 14:30:00', 1, 1, 10, 2, 1, '2015-03-01 19:54:24', '2015-03-01 19:54:24'),
(12, '2015-03-06', '2015-03-06 12:30:00', '2015-03-06 13:30:00', 1, 3, 9, 2, 1, '2015-03-01 20:35:19', '2015-03-01 20:35:19'),
(13, '2015-03-06', '2015-03-06 13:30:00', '2015-03-06 14:00:00', 1, 1, 9, 2, 1, '2015-03-01 20:35:53', '2015-03-01 20:35:53'),
(14, '2015-03-04', '2015-03-04 13:00:00', '2015-03-04 13:40:00', 3, 4, 8, 2, 1, '2015-03-02 03:51:22', '2015-03-07 19:59:06'),
(15, '2015-03-03', '2015-03-03 11:50:00', '2015-03-03 12:50:00', 2, 1, 10, 2, 1, '2015-03-03 04:47:35', '2015-03-06 23:56:27'),
(16, '2015-03-07', '2015-03-07 11:30:00', '2015-03-07 13:30:00', 2, 1, 13, 4, 2, '2015-03-06 13:39:17', '2015-03-07 21:08:34'),
(20, '2015-03-07', '2015-03-07 09:50:00', '2015-03-07 10:30:00', 1, 4, 13, 2, 1, '2015-03-06 16:28:55', '2015-03-06 16:28:55'),
(21, '2015-03-10', '2015-03-10 13:00:00', '2015-03-10 15:00:00', 2, 4, 8, 4, 2, '2015-03-07 22:16:20', '2015-03-08 00:55:06'),
(22, '2015-03-12', '2015-03-12 12:50:00', '2015-03-12 13:30:00', 0, 1, 10, 2, 1, '2015-03-08 11:34:43', '2015-03-08 12:13:17');

-- --------------------------------------------------------

--
-- Stand-in structure for view `citas`
--
CREATE TABLE IF NOT EXISTS `citas` (
`id` bigint(19) unsigned
,`fecha` date
,`hora_inicio` timestamp
,`hora_fin` timestamp
,`estado` tinyint(4)
,`nombre_paciente` varchar(45)
,`apellido_paciente` varchar(45)
,`cedula_paciente` varchar(10)
,`nombre_doctor` varchar(45)
,`apellido_doctor` varchar(45)
,`cedula_doctor` varchar(10)
);
-- --------------------------------------------------------

--
-- Table structure for table `consultorio`
--

CREATE TABLE IF NOT EXISTS `consultorio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `capacidad` int(11) NOT NULL DEFAULT '1',
  `area_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`area_id`),
  KEY `fk_consultorio_area1_idx` (`area_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `consultorio`
--

INSERT INTO `consultorio` (`id`, `nombre`, `descripcion`, `capacidad`, `area_id`) VALUES
(1, 'Cabina 1', '', 1, 1),
(2, 'Cabina 2', '', 1, 1),
(3, 'Cabina 3', '', 1, 1),
(4, 'Cabina 4', '', 1, 1),
(5, 'Cabina 5', '', 2, 1),
(6, 'Cabina 6', '', 1, 1),
(7, 'Cabina 7', '', 1, 1),
(8, 'Cabina 8', '', 1, 1),
(9, 'Cabina 9', '', 1, 1),
(10, 'Cabina Individual', '', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `consultorio_servicio`
--

CREATE TABLE IF NOT EXISTS `consultorio_servicio` (
  `consultorio_id` int(11) NOT NULL,
  `servicio_id` int(11) NOT NULL,
  PRIMARY KEY (`consultorio_id`,`servicio_id`),
  KEY `fk_consultorio_servicio_servicio1_idx` (`servicio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `consultorio_servicio`
--

INSERT INTO `consultorio_servicio` (`consultorio_id`, `servicio_id`) VALUES
(1, 2),
(1, 3),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `disponibilidad`
--

CREATE TABLE IF NOT EXISTS `disponibilidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inicio` datetime NOT NULL,
  `fin` datetime NOT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `usuario_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disponibilidad_usuario1_idx` (`usuario_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `disponibilidad`
--

INSERT INTO `disponibilidad` (`id`, `inicio`, `fin`, `disponible`, `usuario_id`) VALUES
(1, '2015-03-13 08:30:00', '2015-03-13 15:00:00', 1, 1),
(3, '2015-03-10 08:00:00', '2015-03-10 18:00:00', 1, 4),
(5, '2015-03-11 08:00:00', '2015-03-11 12:30:00', 0, 1),
(6, '2015-03-10 08:00:00', '2015-03-10 14:00:00', 1, 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `doctor`
--
CREATE TABLE IF NOT EXISTS `doctor` (
`nombre` varchar(45)
,`apellido` varchar(45)
,`dni` varchar(10)
,`atendidos` decimal(23,0)
,`pendientes` decimal(23,0)
,`usuario_id` int(10) unsigned
,`avatar` varchar(63)
);
-- --------------------------------------------------------

--
-- Table structure for table `equipo`
--

CREATE TABLE IF NOT EXISTS `equipo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `cantidad` int(11) NOT NULL DEFAULT '1',
  `inamovible` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `equipo`
--

INSERT INTO `equipo` (`id`, `nombre`, `descripcion`, `cantidad`, `inamovible`) VALUES
(1, 'Laser', '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `equipo_servicio`
--

CREATE TABLE IF NOT EXISTS `equipo_servicio` (
  `servicio_id` int(11) NOT NULL,
  `equipo_id` int(11) NOT NULL,
  PRIMARY KEY (`servicio_id`,`equipo_id`),
  KEY `fk_equipo_servicio_equipo1_idx` (`equipo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nota`
--

CREATE TABLE IF NOT EXISTS `nota` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contenido` varchar(512) DEFAULT NULL,
  `cita_id` bigint(19) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_nota_cita1_idx` (`cita_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `paciente`
--

CREATE TABLE IF NOT EXISTS `paciente` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `sexo` tinyint(1) DEFAULT NULL,
  `estado_civil` tinyint(1) unsigned DEFAULT '0' COMMENT '0: Soltero, 1: Casado, 2:Divorciado, 3:Viudo',
  `direccion` varchar(255) DEFAULT NULL,
  `avatar` varchar(63) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `usuario_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni_UNIQUE` (`dni`),
  KEY `fk_paciente_usuario1_idx` (`usuario_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `paciente`
--

INSERT INTO `paciente` (`id`, `nombre`, `apellido`, `dni`, `fecha_nacimiento`, `sexo`, `estado_civil`, `direccion`, `avatar`, `created_at`, `updated_at`, `usuario_id`) VALUES
(8, 'Alfredo Ramón', 'Fleming Cabello', 'V-17339453', '1985-09-15', 1, 0, 'Unare III, Urbanización Yuruani, Calle Botamo, Manzana 4, Casa #1', '54e73175a3859.jpg', '2014-09-07 15:41:41', '2014-09-07 15:41:41', 1),
(9, 'Alexander José', 'Fleming Cabello', 'V-16164136', '1982-03-02', 1, 0, 'Unare III, Urbanización Yuruani, Calle Botamo, Manzana 4, Casa #1', NULL, NULL, NULL, NULL),
(10, 'Ramón Antonio', 'Fleming Aleman', 'V-8351547', '1959-02-16', 1, 1, 'Unare III, Urb. Yuruani, Calle Botamo, Mza. 4, #1, Edo. Bolívar, Venezuela', NULL, NULL, NULL, 3),
(12, 'Omar', 'Acevedo Bernal', 'V-8339453', '1980-01-03', 1, 1, 'Altavista', '54f3c4149e299.jpg', NULL, NULL, 4),
(13, 'Yanellys Josefína', 'Fleming Cabello', 'V-17339452', '1987-03-01', 0, 0, 'unare iii, calle botamo, manzana 4, #1', NULL, '2015-03-06 11:33:52', '2015-03-06 11:33:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `paciente_tipo_contacto`
--

CREATE TABLE IF NOT EXISTS `paciente_tipo_contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paciente_id` int(10) unsigned NOT NULL,
  `tipo_contacto_id` int(10) unsigned NOT NULL,
  `contacto` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_paciente_tipo_contacto_tipo_contacto1_idx` (`tipo_contacto_id`),
  KEY `fk_paciente_tipo_contacto_paciente1` (`paciente_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `paciente_tipo_contacto`
--

INSERT INTO `paciente_tipo_contacto` (`id`, `paciente_id`, `tipo_contacto_id`, `contacto`) VALUES
(1, 9, 1, '04148927539'),
(2, 9, 2, 'alexanderfleming19@hotmail.com'),
(3, 8, 1, '04249440972'),
(4, 8, 2, 'alfredofleming@msn.com'),
(5, 10, 1, '04249311408'),
(6, 10, 1, '04166864167'),
(7, 10, 2, 'ramonfleming@hotmail.com'),
(8, 13, 1, '02869530804'),
(9, 13, 2, 'yanellys_fleming@hotmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `paciente_tipo_pariente`
--

CREATE TABLE IF NOT EXISTS `paciente_tipo_pariente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo_pariente_id` int(11) NOT NULL,
  `paciente_id` int(10) unsigned NOT NULL,
  `pariente_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_paciente_has_tipo_pariente_tipo_pariente1_idx` (`tipo_pariente_id`),
  KEY `fk_paciente_has_tipo_pariente_paciente2_idx` (`paciente_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `paciente_tipo_pariente`
--

INSERT INTO `paciente_tipo_pariente` (`id`, `tipo_pariente_id`, `paciente_id`, `pariente_id`) VALUES
(1, 5, 8, 10),
(2, 7, 10, 8),
(3, 7, 10, 9),
(4, 5, 9, 10),
(5, 8, 9, 8),
(6, 8, 8, 9);

-- --------------------------------------------------------

--
-- Table structure for table `rol`
--

CREATE TABLE IF NOT EXISTS `rol` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`id`, `nombre`, `descripcion`) VALUES
(1, 'doctor', NULL),
(2, 'recepcionista', NULL),
(3, 'paciente', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `servicio`
--

CREATE TABLE IF NOT EXISTS `servicio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `duracion` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `servicio`
--

INSERT INTO `servicio` (`id`, `nombre`, `descripcion`, `duracion`) VALUES
(2, 'Maximuss Corporal', '', 40),
(3, 'Maximuss Facial', '', 40),
(4, 'Facial', '', 120);

-- --------------------------------------------------------

--
-- Table structure for table `tipo_contacto`
--

CREATE TABLE IF NOT EXISTS `tipo_contacto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `tipo_contacto`
--

INSERT INTO `tipo_contacto` (`id`, `tipo`) VALUES
(1, 'phone'),
(2, 'email');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_pariente`
--

CREATE TABLE IF NOT EXISTS `tipo_pariente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentesco_m` varchar(45) NOT NULL,
  `parentesco_f` varchar(45) NOT NULL,
  `reciproco` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parentesco_UNIQUE` (`parentesco_m`),
  UNIQUE KEY `parentesco_f_UNIQUE` (`parentesco_f`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `tipo_pariente`
--

INSERT INTO `tipo_pariente` (`id`, `parentesco_m`, `parentesco_f`, `reciproco`) VALUES
(5, 'Padre', 'Madre', 7),
(7, 'Hijo', 'Hija', 5),
(8, 'Hermano', 'Hermana', 8);

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `correo` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `contrasena_tmp` varchar(60) DEFAULT NULL COMMENT 'Contraseña temporal que se usará en caso de olvido de la contraseña.',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(60) DEFAULT NULL COMMENT 'Código usado para asegurar el ingreso al sistema automaticamente cuando el usuario elige la opción de Recordar. (auto)',
  `created_at` datetime DEFAULT NULL COMMENT 'Fecha de registro (auto)',
  `updated_at` datetime DEFAULT NULL COMMENT 'Fecha de actualización de datos (auto)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id`, `correo`, `password`, `contrasena_tmp`, `activo`, `admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin@defecto', '$2y$10$SPk9qASck2p7d87YGfLY1eNcJNUUzASa/gvjiIz/wdPqbg7zNgVsi', NULL, 1, 1, 'QZUgaAF2PySFz1PpKE4b3SSWSWGm7cYM9KLr7ldxY56q42bjDCOcnUue8ksH', '2014-09-07 15:41:41', '2015-03-02 04:01:05'),
(2, 'foo@bar.com', '', NULL, 1, 1, NULL, '2014-10-13 12:41:30', '2014-12-26 15:03:39'),
(3, 'test@testing.com', '', NULL, 1, 0, NULL, '2014-12-27 15:07:42', '2014-12-27 15:07:42'),
(4, 'omar@localhost.com', '$2y$10$OYLsVf3acSrqVgc2.Sdm1.zeBpgredHqD97yOl48gzUXM8Prk4lcy', NULL, 1, 0, 'mJdd9kaBjdnlKHIdNzgTfAYVd7W0ZX3htUJN7G0bYAR2iTgXg8LgSYpIk9ES', '2015-03-01 23:06:07', '2015-03-02 03:50:46');

-- --------------------------------------------------------

--
-- Table structure for table `usuario_rol`
--

CREATE TABLE IF NOT EXISTS `usuario_rol` (
  `usuario_id` int(10) unsigned NOT NULL,
  `rol_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`usuario_id`,`rol_id`),
  KEY `fk_usuario_rol_rol1_idx` (`rol_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usuario_rol`
--

INSERT INTO `usuario_rol` (`usuario_id`, `rol_id`) VALUES
(1, 1),
(3, 1),
(4, 1),
(1, 2),
(3, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Structure for view `citas`
--
DROP TABLE IF EXISTS `citas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `citas` AS select `c`.`id` AS `id`,`c`.`fecha` AS `fecha`,`c`.`hora_inicio` AS `hora_inicio`,`c`.`hora_fin` AS `hora_fin`,`c`.`estado` AS `estado`,`p`.`nombre` AS `nombre_paciente`,`p`.`apellido` AS `apellido_paciente`,`p`.`dni` AS `cedula_paciente`,`d`.`nombre` AS `nombre_doctor`,`d`.`apellido` AS `apellido_doctor`,`d`.`dni` AS `cedula_doctor` from (((`cita` `c` join `paciente` `p` on((`c`.`paciente_id` = `p`.`id`))) join `usuario` `u` on((`c`.`doctor_id` = `u`.`id`))) join `paciente` `d` on((`d`.`usuario_id` = `u`.`id`)));

-- --------------------------------------------------------

--
-- Structure for view `doctor`
--
DROP TABLE IF EXISTS `doctor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `doctor` AS select `p`.`nombre` AS `nombre`,`p`.`apellido` AS `apellido`,`p`.`dni` AS `dni`,sum((case when ((`c`.`estado` = 1) and (`c`.`fecha` = now())) then 1 else 0 end)) AS `atendidos`,sum((case when (((`c`.`estado` = 2) or (`c`.`estado` = 0)) and (`c`.`fecha` = now())) then 1 else 0 end)) AS `pendientes`,`u`.`id` AS `usuario_id`,`p`.`avatar` AS `avatar` from (((`usuario` `u` join `usuario_rol` `r` on((`r`.`usuario_id` = `u`.`id`))) join `paciente` `p` on((`p`.`usuario_id` = `u`.`id`))) left join `cita` `c` on((`c`.`doctor_id` = `u`.`id`))) where (`r`.`rol_id` = 1) group by `p`.`id`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cita`
--
ALTER TABLE `cita`
  ADD CONSTRAINT `fk_cita_consultorio1` FOREIGN KEY (`consultorio_id`) REFERENCES `consultorio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cita_paciente1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cita_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_cita_usuario1` FOREIGN KEY (`doctor_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `consultorio`
--
ALTER TABLE `consultorio`
  ADD CONSTRAINT `fk_consultorio_area1` FOREIGN KEY (`area_id`) REFERENCES `area` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `consultorio_servicio`
--
ALTER TABLE `consultorio_servicio`
  ADD CONSTRAINT `fk_consultorio_servicio_consultorio1` FOREIGN KEY (`consultorio_id`) REFERENCES `consultorio` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_consultorio_servicio_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `disponibilidad`
--
ALTER TABLE `disponibilidad`
  ADD CONSTRAINT `fk_disponibilidad_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `equipo_servicio`
--
ALTER TABLE `equipo_servicio`
  ADD CONSTRAINT `fk_equipo_servicio_equipo1` FOREIGN KEY (`equipo_id`) REFERENCES `equipo` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_equipo_servicio_servicio1` FOREIGN KEY (`servicio_id`) REFERENCES `servicio` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `nota`
--
ALTER TABLE `nota`
  ADD CONSTRAINT `fk_nota_cita1` FOREIGN KEY (`cita_id`) REFERENCES `cita` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `paciente`
--
ALTER TABLE `paciente`
  ADD CONSTRAINT `fk_paciente_usuario1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `paciente_tipo_contacto`
--
ALTER TABLE `paciente_tipo_contacto`
  ADD CONSTRAINT `fk_paciente_tipo_contacto_paciente1` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_paciente_tipo_contacto_tipo_contacto1` FOREIGN KEY (`tipo_contacto_id`) REFERENCES `tipo_contacto` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `paciente_tipo_pariente`
--
ALTER TABLE `paciente_tipo_pariente`
  ADD CONSTRAINT `fk_paciente_has_tipo_pariente_paciente2` FOREIGN KEY (`paciente_id`) REFERENCES `paciente` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_paciente_has_tipo_pariente_tipo_pariente1` FOREIGN KEY (`tipo_pariente_id`) REFERENCES `tipo_pariente` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `usuario_rol`
--
ALTER TABLE `usuario_rol`
  ADD CONSTRAINT `fk_usuario_rol_rol1` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_usuario_rol_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
