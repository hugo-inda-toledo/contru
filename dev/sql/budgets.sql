/*
Navicat MySQL Data Transfer

Source Server         : Local Database :: MySQl
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : ldz

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2016-07-19 17:31:26
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for budgets
-- ----------------------------
DROP TABLE IF EXISTS `budgets`;
CREATE TABLE `budgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `building_id` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `total_cost` float(23,4) NOT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `advances` float(23,4) NOT NULL,
  `retentions` float(23,4) NOT NULL,
  `utilities` float(23,4) NOT NULL,
  `general_costs` float(23,4) NOT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `start_value` float(23,2) DEFAULT NULL,
  `user_created_id` int(11) DEFAULT NULL,
  `user_modified_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `building_id` (`building_id`),
  KEY `user_created_id` (`user_created_id`),
  CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`),
  CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`user_created_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of budgets
-- ----------------------------
INSERT INTO `budgets` VALUES ('10', '17', '12', '5655000.0000', 'A', 'C:\\wamp\\www\\cl_ldz_cpo.git\\dev\\src\\upload_excel\\temporal-2016-02-22_233819.xlsx', '10.0000', '5.0000', '10.0000', '900000.0000', '3', '25000.00', '3', null, '2016-02-22 23:38:19', '2016-02-22 23:38:28');
INSERT INTO `budgets` VALUES ('51', '6', '3', '159839.5156', '12', 'C:\\wamp\\www\\cl_ldz_cpo.git\\dev\\src\\upload_excel\\temporal-2016-06-15_221927.xlsx', '3.0000', '3.0000', '3.0000', '3845.8271', '3', '24500.00', '3', null, '2016-06-15 22:19:26', '2016-06-15 22:21:32');
INSERT INTO `budgets` VALUES ('53', '40', '8', '159839.5156', '', 'C:\\wamp\\www\\cl_ldz_cpo.git\\dev\\src\\upload_excel\\temporal-2016-06-16_091512.xlsx', '10.0000', '5.0000', '10.0000', '3845.8271', '3', '26000.00', '3', null, '2016-03-16 09:15:11', '2016-03-16 09:21:38');
INSERT INTO `budgets` VALUES ('54', '37', '3', '1260000.0000', 'bla bla bla', 'C:\\wamp\\www\\cl_ldz_cpo\\dev\\src\\upload_excel\\temporal-2016-06-29_163956.xlsx', '2.0000', '5.0000', '10.0000', '2500.0000', '3', '24500.00', '3', null, '2016-06-29 00:00:00', '2016-06-29 16:40:05');
INSERT INTO `budgets` VALUES ('55', '41', '9', '1260000.0000', '', 'C:\\wamp\\www\\cl_ldz_cpo\\dev\\src\\upload_excel\\temporal-2016-07-11_144151.xlsx', '10.0000', '10.0000', '10.0000', '2500.0000', '1', '1.00', '3', null, '2016-07-11 00:00:00', '2016-07-11 14:42:01');
