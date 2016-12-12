/*
Navicat MySQL Data Transfer

Source Server         : Local Database :: MySQl
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : ldz

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-07-15 17:10:30
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for valoresmonedas
-- ----------------------------
DROP TABLE IF EXISTS `valoresmonedas`;
CREATE TABLE `valoresmonedas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL,
  `currency_date` date NOT NULL,
  `currency_value` float(23,2) NOT NULL,
  `created` datetime(6) DEFAULT NULL,
  `modified` datetime(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of valoresmonedas
-- ----------------------------
INSERT INTO `valoresmonedas` VALUES ('7', '2', '2016-07-15', '650.92', '2016-07-15 16:29:18.000000', '2016-07-15 16:29:18.000000');
INSERT INTO `valoresmonedas` VALUES ('8', '3', '2016-07-15', '26087.84', '2016-07-15 16:29:18.000000', '2016-07-15 16:29:18.000000');
