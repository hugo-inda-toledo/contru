/*
Navicat MySQL Data Transfer

Source Server         : ldz
Source Server Version : 50544
Source Host           : 192.168.0.211:3306
Source Database       : ldz

Target Server Type    : MYSQL
Target Server Version : 50544
File Encoding         : 65001

Date: 2015-10-08 17:30:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `budget_states`
-- ----------------------------
DROP TABLE IF EXISTS `budget_states`;
CREATE TABLE `budget_states` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of budget_states
-- ----------------------------
INSERT INTO `budget_states` VALUES ('1', 'No Tiene', 'No Tiene', '2015-10-02 16:45:07', '2015-10-02 16:45:07');
INSERT INTO `budget_states` VALUES ('2', 'Pendiente Aprobación Gerente Finanzas', 'Pendiente Aprobación Gerente Finanzas', '2015-10-02 16:45:18', '2015-10-02 16:45:18');
INSERT INTO `budget_states` VALUES ('3', 'Pendiente Aprobación Gerente General', 'Pendiente Aprobación Gerente General', '2015-10-02 16:45:18', '2015-10-02 16:45:18');
INSERT INTO `budget_states` VALUES ('4', 'En Curso', 'En Curso', '2015-10-02 16:45:18', '2015-10-02 16:45:18');
INSERT INTO `budget_states` VALUES ('5', 'En Curso Atrasado', 'En Curso Atrasado', '2015-10-02 16:45:35', '2015-10-02 16:45:35');
INSERT INTO `budget_states` VALUES ('6', 'Finalizado', 'Finalizado', '2015-10-02 16:45:43', '2015-10-02 16:45:43');
