-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 23, 2015 at 01:52 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `clinica`
--
CREATE DATABASE IF NOT EXISTS `clinica` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `clinica`;

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
  `usuario_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni_UNIQUE` (`dni`),
  KEY `fk_paciente_usuario1_idx` (`usuario_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `paciente`
--

INSERT INTO `paciente` (`id`, `nombre`, `apellido`, `dni`, `fecha_nacimiento`, `sexo`, `estado_civil`, `direccion`, `usuario_id`) VALUES
(8, 'Alfredo Ramón', 'Fleming Cabello', 'V-17339453', '1985-09-15', 1, 0, 'Unare III, Urbanización Yuruani, Calle Botamo, Manzana 4, Casa #1', 1),
(9, 'Alexander José', 'Fleming Cabello', 'V-16164136', '1982-03-02', 1, 0, 'Unare III, Urbanización Yuruani, Calle Botamo, Manzana 4, Casa #1', NULL),
(10, 'Ramón Antonio', 'Fleming Aleman', 'V-8351547', '1959-02-16', 1, 1, 'Unare III, Urb. Yuruani, Calle Botamo, Mza. 4, #1, Edo. Bolívar, Venezuela', NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

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
(7, 10, 2, 'ramonfleming@hotmail.com');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `rol`
--

INSERT INTO `rol` (`id`, `nombre`, `descripcion`) VALUES
(1, 'doctor', NULL),
(2, 'enfermera', NULL),
(3, 'farmaceutico', NULL),
(4, 'paciente', NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id`, `correo`, `password`, `contrasena_tmp`, `activo`, `admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin@defecto', '$2y$10$SPk9qASck2p7d87YGfLY1eNcJNUUzASa/gvjiIz/wdPqbg7zNgVsi', NULL, 1, 1, 'BAr5pAWUuZT8whrLJuA0Gl9FpGCgCFrYfCblmQGT4N64gRyVyNqjdAeta5mL', '2014-09-07 15:41:41', '2015-01-05 14:25:10'),
(2, 'foo@bar.com', '', NULL, 1, 1, NULL, '2014-10-13 12:41:30', '2014-12-26 15:03:39'),
(3, 'test@testing.com', '', NULL, 1, 0, NULL, '2014-12-27 15:07:42', '2014-12-27 15:07:42');

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
(1, 2),
(3, 2),
(2, 3),
(3, 3);

--
-- Constraints for dumped tables
--

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
