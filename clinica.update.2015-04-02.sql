-- MySQL Workbench Synchronization
-- Generated: 2015-04-02 18:58
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Alfredo

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `clinica`.`usuario` 
DROP COLUMN `nombre`,
ADD COLUMN `nombre` VARCHAR(255) NOT NULL AFTER `id`;

ALTER TABLE `clinica`.`servicio` 
ADD COLUMN `categoria_servicio_id` INT(11) NULL DEFAULT NULL AFTER `duracion`,
ADD INDEX `fk_servicio_categoria_servicio1_idx` (`categoria_servicio_id` ASC);

CREATE TABLE IF NOT EXISTS `clinica`.`categoria_servicio` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

ALTER TABLE `clinica`.`servicio` 
ADD CONSTRAINT `fk_servicio_categoria_servicio1`
  FOREIGN KEY (`categoria_servicio_id`)
  REFERENCES `clinica`.`categoria_servicio` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
