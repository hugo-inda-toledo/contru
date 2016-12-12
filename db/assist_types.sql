/*
Navicat MySQL Data Transfer

Source Server         : LOCAL-WAMP
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : ldz

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-10-20 14:48:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `assist_types`
-- ----------------------------
DROP TABLE IF EXISTS `assist_types`;
CREATE TABLE `assist_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of assist_types
-- ----------------------------
INSERT INTO `assist_types` VALUES ('1', 'Asistencia', 'Asistencia a la Obra', '2015-10-14 15:31:41', '2015-10-14 15:31:44');
INSERT INTO `assist_types` VALUES ('2', 'Falla', 'Ausencia al trabajo de la Obra', '2015-10-14 15:32:40', '2015-10-14 15:32:43');
INSERT INTO `assist_types` VALUES ('3', 'Permiso', 'Permiso para ausencia en la Obra', '2015-10-14 15:34:45', '2015-10-14 15:34:48');
INSERT INTO `assist_types` VALUES ('4', 'Licencia Compin', 'Licencia de tipo COMPIN', '2015-10-14 15:35:38', '2015-10-14 15:35:41');
INSERT INTO `assist_types` VALUES ('5', 'Licencia Achs', 'Licencia de tipo ACHS', '2015-10-14 15:36:15', '2015-10-14 15:36:17');
INSERT INTO `assist_types` VALUES ('6', 'Cesación', 'Cesación del contrato del Trabajador', '2015-10-14 15:36:39', '2015-10-14 15:36:42');
INSERT INTO `assist_types` VALUES ('7', 'Movimiento Personal', 'Movimiento de Obra de trabajador', '2015-10-14 15:37:40', '2015-10-14 15:37:43');
INSERT INTO `assist_types` VALUES ('8', 'Incorporación Trabajador', 'Incorporación de un nuevo trabajador', '2015-10-14 15:38:17', '2015-10-14 15:38:20');
