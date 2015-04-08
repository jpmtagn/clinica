USE `clinica`;
CREATE 
     OR REPLACE ALGORITHM = UNDEFINED 
    DEFINER = `root`@`localhost` 
    SQL SECURITY DEFINER
VIEW `servicios_equipos` AS
    SELECT 
        `s`.`id` AS `id`,
        `s`.`nombre` AS `nombre`,
        s.descripcion AS 'descripcion',
        s.duracion AS 'duracion',
        `e`.`nombre` AS `equipos`,
        `s`.`categoria_servicio_id` AS `categoria_id`
    FROM
        ((`servicio` `s`
        LEFT JOIN `equipo_servicio` `es` ON ((`s`.`id` = `es`.`servicio_id`)))
        LEFT JOIN `equipo` `e` ON ((`es`.`equipo_id` = `e`.`id`)))
    GROUP BY `s`.`id`;