/*
Navicat MySQL Data Transfer

Source Server         : Local Database :: MySQl
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : ldz

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-07-15 17:11:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for currencies
-- ----------------------------
DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `long_name` varchar(255) DEFAULT NULL,
  `plural_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `sbif_api_keyword` varchar(255) DEFAULT NULL,
  `initials` varchar(4) DEFAULT NULL,
  `symbol` varchar(4) DEFAULT NULL,
  `variable_value` int(11) NOT NULL,
  `iconstruye_keyword` varchar(255) DEFAULT NULL,
  `softland_keyword` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of currencies
-- ----------------------------
INSERT INTO `currencies` VALUES ('1', 'Peso', 'Peso Chileno', 'Pesos', 'Moneda utilizada en el territorio chileno', 'peso', 'CL', '$', '0', null, null, '2015-09-24 15:19:46', '2015-09-28 13:14:04');
INSERT INTO `currencies` VALUES ('2', 'Dolar', 'Dolar americano', 'Dolares', 'Moneda utilizada en el territorio americano', 'dolar', 'USD', '$', '1', null, null, '2015-09-24 15:21:30', '2015-09-28 12:59:55');
INSERT INTO `currencies` VALUES ('3', 'UF', 'Unidad de fomento', 'UFs', 'Unidad de cuenta usada en Chile, reajustable de acuerdo con la inflación', 'uf', 'UF', 'U.F', '1', null, null, '2015-09-24 15:40:44', '2015-09-24 15:40:44');
