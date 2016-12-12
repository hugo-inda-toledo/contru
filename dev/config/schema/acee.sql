/*
Navicat MySQL Data Transfer

Source Server         : LOCAL MYSQL
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : acee

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-07-17 22:53:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ajax_chat_bans
-- ----------------------------
DROP TABLE IF EXISTS `ajax_chat_bans`;
CREATE TABLE `ajax_chat_bans` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `userName` (`userName`),
  KEY `dateTime` (`dateTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of ajax_chat_bans
-- ----------------------------

-- ----------------------------
-- Table structure for ajax_chat_invitations
-- ----------------------------
DROP TABLE IF EXISTS `ajax_chat_invitations`;
CREATE TABLE `ajax_chat_invitations` (
  `userID` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  PRIMARY KEY (`userID`,`channel`),
  KEY `dateTime` (`dateTime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of ajax_chat_invitations
-- ----------------------------

-- ----------------------------
-- Table structure for ajax_chat_messages
-- ----------------------------
DROP TABLE IF EXISTS `ajax_chat_messages`;
CREATE TABLE `ajax_chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `text` text COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  KEY `message_condition` (`id`,`channel`,`dateTime`),
  KEY `dateTime` (`dateTime`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of ajax_chat_messages
-- ----------------------------
INSERT INTO `ajax_chat_messages` VALUES ('1', '2147483647', 'ChatBot', '4', '0', '2015-07-17 21:42:09', 0x7F000001, 0x2F6C6F67696E2075736572);
INSERT INTO `ajax_chat_messages` VALUES ('2', '3', 'user', '1', '0', '2015-07-17 21:42:17', 0x7F000001, 0x686F6C61);

-- ----------------------------
-- Table structure for ajax_chat_online
-- ----------------------------
DROP TABLE IF EXISTS `ajax_chat_online`;
CREATE TABLE `ajax_chat_online` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `userName` (`userName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of ajax_chat_online
-- ----------------------------
INSERT INTO `ajax_chat_online` VALUES ('3', 'user', '1', '0', '2015-07-17 22:53:16', 0x7F000001);
