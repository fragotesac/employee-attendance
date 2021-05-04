# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.4.7-MariaDB-1:10.4.7+maria~bionic)
# Database: asistencia
# Generation Time: 2019-09-03 06:44:21 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table asistencia
# ------------------------------------------------------------

CREATE TABLE `asistencia` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` tinyint(11) DEFAULT NULL,
  `observacion` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asistencia_user_id` (`user_id`),
  CONSTRAINT `asistencia_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `asistencia` WRITE;
/*!40000 ALTER TABLE `asistencia` DISABLE KEYS */;

INSERT INTO `asistencia` (`id`, `user_id`, `status`, `observacion`, `created_at`)
VALUES
	(1,1,1,'',1567492281),
	(2,1,1,'',1567492325),
	(3,1,1,'',1567492728),
	(4,1,2,'',1567492738),
	(5,1,3,'',1567492757),
	(6,1,4,'',1567492769),
	(7,1,1,'testt',1567492779),
	(8,1,1,'testt',1567492864),
	(9,1,1,'testt',1567492885);

/*!40000 ALTER TABLE `asistencia` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `menu`;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(100) DEFAULT NULL,
  `label` varchar(150) DEFAULT NULL,
  `parent` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `mostrar` int(11) NOT NULL,
  `permiso` int(1) NOT NULL,
  `route` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idmenu_UNIQUE` (`id`),
  KEY `fk_menu_module` (`module_id`),
  CONSTRAINT `fk_menu_module` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `menu` WRITE;
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;

INSERT INTO `menu` (`id`, `url`, `label`, `parent`, `module_id`, `mostrar`, `permiso`, `route`)
VALUES
	(1,'/dashboard','<i class=\"fa fa-tachometer\"></i>  Inicio',NULL,1,1,4,'dashboard-index'),
	(2,'','<i class=\"fa fa-table\"></i> Mantenimiento',NULL,1,1,4,''),
	(3,'/dashboard/mantenimiento/rol','Roles',2,1,1,4,'dashboard-rol-listar'),
	(4,'/dashboard/mantenimiento/user','Usuarios',2,1,1,4,'dashboard-user-listar'),
	(5,'/dashboard/mantenimiento/modulo','Modulo',2,1,1,4,'dashboard-modulo-listar'),
	(6,'/dashboard/mantenimiento/menu','Menu',2,1,1,4,'dashboard-menu-listar'),
	(7,NULL,NULL,1,1,0,4,'dashboard-index-boleta-electronica'),
	(8,NULL,NULL,1,1,0,2,'dashboard-index-enviar-correo'),
	(9,NULL,NULL,3,1,0,3,'dashboard-rol-agregar'),
	(10,NULL,NULL,3,1,0,3,'dashboard-rol-editar'),
	(11,NULL,NULL,3,1,0,2,'dashboard-rol-eliminar'),
	(12,NULL,NULL,5,1,0,3,'dashboard-modulo-agregar'),
	(13,NULL,NULL,5,1,0,3,'dashboard-modulo-editar'),
	(14,NULL,NULL,5,1,0,2,'dashboard-modulo-eliminar'),
	(15,NULL,NULL,4,1,0,3,'dashboard-user-agregar'),
	(16,NULL,NULL,4,1,0,3,'dashboard-user-editar'),
	(17,NULL,NULL,4,1,0,2,'dashboard-user-eliminar'),
	(18,NULL,NULL,6,1,0,3,'dashboard-menu-agregar'),
	(19,NULL,NULL,6,1,0,3,'dashboard-menu-editar'),
	(20,NULL,NULL,6,1,0,2,'dashboard-menu-eliminar'),
	(21,'/dashboard/mantenimiento/privilege','Privilegios',2,1,1,4,'dashboard-privilege-listar'),
	(22,NULL,NULL,4,1,0,3,'dashboard-privilege-agregar'),
	(23,NULL,NULL,4,1,0,3,'dashboard-privilege-editar');

/*!40000 ALTER TABLE `menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idmodule_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;

INSERT INTO `module` (`id`, `name`)
VALUES
	(1,'Dashboard');

/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table privilege
# ------------------------------------------------------------

DROP TABLE IF EXISTS `privilege`;

CREATE TABLE `privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `permiso` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_privilege_role1_idx` (`role_id`),
  KEY `fk_privilege_menu1_idx` (`menu_id`),
  CONSTRAINT `fk_privilege_menu1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_privilege_role1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `privilege` WRITE;
/*!40000 ALTER TABLE `privilege` DISABLE KEYS */;

INSERT INTO `privilege` (`id`, `role_id`, `menu_id`, `permiso`)
VALUES
	(82,1,1,1),
	(83,1,3,1),
	(84,1,4,1),
	(85,1,5,1),
	(86,1,6,1),
	(87,1,21,1),
	(89,1,2,4);

/*!40000 ALTER TABLE `privilege` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;

INSERT INTO `role` (`id`, `name`)
VALUES
	(1,'SYSADMIN');

/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `full_name` varchar(300) DEFAULT NULL,
  `email` varchar(300) DEFAULT NULL,
  `active` int(1) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_user_role_idx` (`role_id`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `username`, `password`, `full_name`, `email`, `active`, `role_id`)
VALUES
	(1,'admin','d033e22ae348aeb5660fc2140aec35850c4da997','Francis Gonzales','francis@fragote.com',1,1);

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--menu
INSERT INTO dash.menu (id, url, label, parent, module_id, mostrar, permiso, route) VALUES (24, '/dashboard/reportes', '<i class="fa fa-calendar"></i> Reportes', null, 1, 1, 4, 'dashboard-reportes');