-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2015 at 02:34 PM
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

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
  `fijo` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_disponibilidad_usuario1_idx` (`usuario_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

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
  `serial` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `paciente`
--

CREATE TABLE IF NOT EXISTS `paciente` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `dni` varchar(10) NOT NULL,
  `tdni` char(1) DEFAULT 'V',
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

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

-- --------------------------------------------------------

--
-- Stand-in structure for view `servicios_equipos`
--
CREATE TABLE IF NOT EXISTS `servicios_equipos` (
`id` int(11)
,`nombre` varchar(45)
,`equipos` varchar(45)
);
-- --------------------------------------------------------

--
-- Table structure for table `tipo_contacto`
--

CREATE TABLE IF NOT EXISTS `tipo_contacto` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

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

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

CREATE TABLE IF NOT EXISTS `usuario` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `contrasena_tmp` varchar(60) DEFAULT NULL COMMENT 'Contraseña temporal que se usará en caso de olvido de la contraseña.',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(60) DEFAULT NULL COMMENT 'Código usado para asegurar el ingreso al sistema automaticamente cuando el usuario elige la opción de Recordar. (auto)',
  `created_at` datetime DEFAULT NULL COMMENT 'Fecha de registro (auto)',
  `updated_at` datetime DEFAULT NULL COMMENT 'Fecha de actualización de datos (auto)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

INSERT INTO `usuario` (`id`, `correo`, `password`, `contrasena_tmp`, `activo`, `admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$SPk9qASck2p7d87YGfLY1eNcJNUUzASa/gvjiIz/wdPqbg7zNgVsi', NULL, 1, 1, 'sDbDeDrB2GHsGkbAG87WKBP4c6X5GfmrgydD6qJoBCZrcPJgqaSU5yHtDzZK', '2014-09-07 15:41:41', '2015-03-19 08:43:48');

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `doctor` AS select `p`.`nombre` AS `nombre`,`p`.`apellido` AS `apellido`,`p`.`dni` AS `dni`,sum((case when ((`c`.`estado` = 1) and (`c`.`fecha` = date_format(now(),'%Y-%m-%d'))) then 1 else 0 end)) AS `atendidos`,sum((case when (((`c`.`estado` = 2) or (`c`.`estado` = 0)) and (`c`.`fecha` = date_format(now(),'%Y-%m-%d'))) then 1 else 0 end)) AS `pendientes`,`u`.`id` AS `usuario_id`,`p`.`avatar` AS `avatar` from (((`usuario` `u` join `usuario_rol` `r` on((`r`.`usuario_id` = `u`.`id`))) join `paciente` `p` on((`p`.`usuario_id` = `u`.`id`))) left join `cita` `c` on((`c`.`doctor_id` = `u`.`id`))) where (`r`.`rol_id` = 1) group by `p`.`id`;

-- --------------------------------------------------------

--
-- Structure for view `servicios_equipos`
--
DROP TABLE IF EXISTS `servicios_equipos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `servicios_equipos` AS select `s`.`id` AS `id`,`s`.`nombre` AS `nombre`,`e`.`nombre` AS `equipos` from ((`servicio` `s` left join `equipo_servicio` `es` on((`s`.`id` = `es`.`servicio_id`))) left join `equipo` `e` on((`es`.`equipo_id` = `e`.`id`))) group by `s`.`id`;

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
  ADD CONSTRAINT `fk_nota_cita1` FOREIGN KEY (`cita_id`) REFERENCES `cita` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

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
