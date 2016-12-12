/*
Navicat MySQL Data Transfer

Source Server         : ldz
Source Server Version : 50544
Source Host           : 192.168.0.211:3306
Source Database       : ldz

Target Server Type    : MYSQL
Target Server Version : 50544
File Encoding         : 65001

Date: 2015-10-08 17:29:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL COMMENT 'identificador grupo',
  `email` varchar(100) NOT NULL COMMENT 'email para inicio sesion y recuperacion password',
  `password` varchar(200) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `lastname_f` varchar(100) NOT NULL,
  `lastname_m` varchar(100) DEFAULT NULL,
  `celphone` varchar(12) DEFAULT NULL,
  `address` varchar(150) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `active` int(4) NOT NULL,
  `user_creator_id` int(11) NOT NULL,
  `user_modifier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '1', 'admin@i1.cl', '$2y$10$h52PEhcsYX1TMMzBw8ODNuIMtu0ftuI96TTihZyimiVfsYdfMaT6e', 'admin fist name', 'admin lastname_f', 'admin lastname_m', '+56 9 1111 1', 'address', '2001-01-01 00:00:00', '2015-10-08 10:25:43', '1', '0', '0');
INSERT INTO `users` VALUES ('3', '2', 'coord@test.cl', '$2y$10$JTWif0mMR3LtSgtnNJQF2uA1Zs3MTBJYPRSc0Re.P.an/JdrdNydW', 'Coordinador', 'Proyectos', '', '12345678', 'Julio Nieto 2005', '2015-09-21 18:47:16', '2015-10-08 17:00:14', '1', '0', '0');
INSERT INTO `users` VALUES ('4', '4', 'gerente.finanzas@test.cl', '$2y$10$JDfu2pxQ85oKfNOUYU.5QegCmXQ.2DUEgOBC1zL6p3HCC7lL9yV8G', 'Gerente Finanzas', 'Prueba', '', '123456789', '', '2015-09-22 13:15:45', '2015-10-08 16:57:53', '1', '0', '0');
INSERT INTO `users` VALUES ('5', '3', 'gerente.general@test.cl', '$2y$10$MX2KSZ5miTevmqxJPSnab.MTa0uNHs1Z3MthRYi2cx5QnulIBeWkS', 'gerente general', 'paterno', 'materno', '', '', '2015-10-07 15:02:02', '2015-10-08 16:48:27', '1', '0', '0');
INSERT INTO `users` VALUES ('6', '5', 'jefe.rrhh@test.cl', '$2y$10$Ur5FCB/vaBQW.PMr8Ca2sOvp1BwsMPhkf5a/VdzJlo9vLykvLEBE2', 'jefe.rrhh', 'test', 'test', '', '', '2015-10-07 15:16:01', '2015-10-07 15:16:01', '1', '0', '0');
INSERT INTO `users` VALUES ('7', '6', 'visitador@test.cl', '$2y$10$MOXbkTOpbGNC8uMhyBYoU.0yS2QPfhYT/jZ/Mqilu4ywJq7hMIPKm', 'visitador', 'test', 'test', '', '', '2015-10-07 15:17:40', '2015-10-07 15:17:40', '1', '0', '0');
INSERT INTO `users` VALUES ('8', '7', 'admin.obra@test.cl', '$2y$10$cE8TY9RPV3wT7CHAZ26d1.xKgW3i9is/bbd2KMKoPN1c/IrZH9sra', 'admin.obra', 'test', 'test', '', '', '2015-10-07 15:18:13', '2015-10-07 15:18:13', '1', '0', '0');
INSERT INTO `users` VALUES ('9', '8', 'asistente.rrhh@test.cl', '$2y$10$TZ2SBIS3hO1EregFavJ/gOTT/.La7iIi0qTPbPbLVXAHdlRk.I1v2', 'asistente.rrhh', 'test', 'test', '', '', '2015-10-07 15:18:52', '2015-10-07 15:18:52', '1', '0', '0');
INSERT INTO `users` VALUES ('10', '9', 'jefe.adquisiciones@test.cl', '$2y$10$VHnLB6Al2qIdrdVv5DHX3O646Kh6qZQNLqwI1MxdJND/Z0vfqJlSm', 'jefe.adquisiciones', 'test', 'test', '', '', '2015-10-07 15:19:25', '2015-10-07 15:19:25', '1', '0', '0');
INSERT INTO `users` VALUES ('11', '10', 'jefe.inventario@test.cl', '$2y$10$v3.h.CiLaVThkbpVa5oYCuLGgQEWjmTVq8oJ/uFkBRVzy6AgL8xyO', 'jefe.inventario', 'test', 'test', '', '', '2015-10-07 15:19:58', '2015-10-07 15:19:58', '1', '0', '0');
INSERT INTO `users` VALUES ('12', '11', 'finanzas@test.cl', '$2y$10$Kq6o4/jnlKDztl4sEtrd2uTY6.O5OeZqwhrWpRgcBdE2QLZsYAPJO', 'finanzas', 'test', 'test', '', '', '2015-10-07 15:20:57', '2015-10-07 15:20:57', '1', '0', '0');
INSERT INTO `users` VALUES ('13', '12', 'oficina.tecnica@test.cl', '$2y$10$bLV/.sumByQS2boykcm8lO4avwH4loMsc3CZ4Y9ei3D7xrtpsDD1a', 'oficina.tecnica', 'test', 'test', '', '', '2015-10-07 15:21:30', '2015-10-07 15:21:30', '1', '0', '0');
INSERT INTO `users` VALUES ('14', '13', 'bodega@test.cl', '$2y$10$sOND4F2C0vJmJU.zv90ereTCBvAX6jwc7QX2xSQyVT7MsDhhRbLR2', 'bodega', 'test', 'test', '', '', '2015-10-07 15:22:17', '2015-10-07 15:22:17', '1', '0', '0');
INSERT INTO `users` VALUES ('15', '14', 'contabilidad@test.cl', '$2y$10$VQz/gq3SH/GJFdVCw7QsnexrH4mCJP61fQo/QqaJYK.SX9OsaM33K', 'contabilidad', 'test', 'test', '', '', '2015-10-07 15:22:53', '2015-10-07 15:22:53', '1', '0', '0');
