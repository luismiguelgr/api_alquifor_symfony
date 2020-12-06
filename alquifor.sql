-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema alquifor
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema alquifor
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `alquifor` ;
USE `alquifor` ;

-- -----------------------------------------------------
-- Table `alquifor`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alquifor`.`usuarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(45) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `nombre` VARCHAR(45) NULL DEFAULT NULL,
  `primer_apellido` VARCHAR(45) NULL DEFAULT NULL,
  `segundo_apellido` VARCHAR(45) NULL DEFAULT NULL,
  `fecha_nacimiento` DATETIME NULL DEFAULT NULL,
  `direccion` VARCHAR(45) NULL DEFAULT NULL,
  `ciudad` VARCHAR(45) NULL DEFAULT NULL,
  `provincia` VARCHAR(45) NULL DEFAULT NULL,
  `codigo_postal` INT NULL DEFAULT NULL,
  `telefono` INT NULL DEFAULT NULL,
  `imagen` VARCHAR(45) NULL DEFAULT NULL,
  `tipo_perfil` INT NOT NULL,
  `roles` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 12
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `alquifor`.`publicaciones`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alquifor`.`publicaciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(150) NOT NULL,
  `foto` VARCHAR(255) NOT NULL,
  `descripcion` VARCHAR(255) NOT NULL,
  `pros` TEXT NOT NULL,
  `contras` TEXT NOT NULL,
  `visitas` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  `id_usuario` INT NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_publicaciones_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `alquifor`.`usuarios` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 33
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

CREATE INDEX `fk_publicaciones_usuario_idx` ON `alquifor`.`publicaciones` (`id_usuario` ASC, `id` ASC);


-- -----------------------------------------------------
-- Table `alquifor`.`comentarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alquifor`.`comentarios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `texto` VARCHAR(255) NOT NULL,
  `id_publicacion` INT NOT NULL,
  `id_usuario` INT NOT NULL,
  `fecha_creacion` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_comentarios_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `alquifor`.`publicaciones` (`id`),
  CONSTRAINT `fk_comentarios_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `alquifor`.`usuarios` (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

CREATE INDEX `fk_comentarios_publicacion_idx` ON `alquifor`.`comentarios` (`id_publicacion` ASC, `id` ASC);

CREATE INDEX `fk_comentarios_usuario_idx` ON `alquifor`.`comentarios` (`id_usuario` ASC);


-- -----------------------------------------------------
-- Table `alquifor`.`valoraciones`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `alquifor`.`valoraciones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `valoracion` INT NOT NULL,
  `id_usuario` INT NOT NULL,
  `id_publicacion` INT NOT NULL,
  `fecha_creacion` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_valoraciones_publicacion`
    FOREIGN KEY (`id_publicacion`)
    REFERENCES `alquifor`.`publicaciones` (`id`),
  CONSTRAINT `fk_valoraciones_usuario`
    FOREIGN KEY (`id_usuario`)
    REFERENCES `alquifor`.`usuarios` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;

CREATE INDEX `fk_valoraciones_usuario_idx` ON `alquifor`.`valoraciones` (`id_usuario` ASC);

CREATE INDEX `fk_valoraciones_publicacion_idx` ON `alquifor`.`valoraciones` (`id_publicacion` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

