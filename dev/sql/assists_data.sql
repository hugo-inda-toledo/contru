/*
Navicat MySQL Data Transfer

Source Server         : Local
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : ldz2

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-08-12 13:21:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `assists_data`
-- ----------------------------
DROP TABLE IF EXISTS `assists_data`;
CREATE TABLE `assists_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `building_id` int(11) NOT NULL,
  `softland_id` int(11) NOT NULL,
  `nombres` varchar(255) DEFAULT NULL,
  `appaterno` varchar(255) DEFAULT NULL,
  `apmaterno` varchar(255) DEFAULT NULL,
  `rut` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono1` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `cargo_codigo` varchar(255) DEFAULT NULL,
  `cargo_nombre` varchar(255) DEFAULT NULL,
  `vig_desde` datetime DEFAULT NULL,
  `vig_hasta` datetime DEFAULT '0000-00-00 00:00:00',
  `mark` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
