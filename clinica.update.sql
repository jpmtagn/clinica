-- MySQL Workbench Synchronization
-- Generated: 2015-04-09 08:28
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Alfredo

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `clinica`.`cita` 
ADD COLUMN `usuario_id` INT(11) NULL DEFAULT NULL AFTER `consultorio_id`;

ALTER TABLE `clinica`.`paciente_tipo_contacto` 
DROP INDEX `fk_paciente_tipo_contacto_paciente1` ;

ALTER TABLE `clinica`.`servicio` 
ADD COLUMN `anidable` TINYINT(1) NULL DEFAULT 0 AFTER `categoria_servicio_id`;

CREATE TABLE IF NOT EXISTS `clinica`.`servicio_usuario` (
  `servicio_id` INT(11) NOT NULL,
  `usuario_id` INT(10) UNSIGNED NOT NULL,
  INDEX `fk_servicio_usuario_servicio1_idx` (`servicio_id` ASC),
  PRIMARY KEY (`servicio_id`, `usuario_id`),
  INDEX `fk_servicio_usuario_usuario1_idx` (`usuario_id` ASC),
  CONSTRAINT `fk_servicio_usuario_servicio1`
    FOREIGN KEY (`servicio_id`)
    REFERENCES `clinica`.`servicio` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_servicio_usuario_usuario1`
    FOREIGN KEY (`usuario_id`)
    REFERENCES `clinica`.`usuario` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `clinica`.`paquete` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `descripcion` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `clinica`.`paquete_servicio` (
  `paquete_id` INT(11) NOT NULL,
  `servicio_id` INT(11) NOT NULL,
  PRIMARY KEY (`paquete_id`, `servicio_id`),
  INDEX `fk_paquete_servicio_servicio1_idx` (`servicio_id` ASC),
  CONSTRAINT `fk_paquete_servicio_paquete1`
    FOREIGN KEY (`paquete_id`)
    REFERENCES `clinica`.`paquete` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_paquete_servicio_servicio1`
    FOREIGN KEY (`servicio_id`)
    REFERENCES `clinica`.`servicio` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
