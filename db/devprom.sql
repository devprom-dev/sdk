SET character_set_server=utf8;
SET character_set_database=utf8;
SET collation_database=utf8_general_ci;
SET NAMES 'utf8' COLLATE 'utf8_general_ci';
SET CHARACTER SET utf8;

DROP TABLE IF EXISTS `AdvertiseBooks`;
CREATE TABLE `AdvertiseBooks` (
  `AdvertiseBooksId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `BookStore` mediumtext,
  `BookUIN` mediumtext,
  `Caption` mediumtext,
  `BookUrl` mediumtext,
  `ImageUrl` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`AdvertiseBooksId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `AdvertiseBooks` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `Blog`;
CREATE TABLE `Blog` (
  `BlogId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`BlogId`),
  KEY `VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `Blog` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `BlogLink`;
CREATE TABLE `BlogLink` (
  `BlogLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `BlogUrl` mediumtext,
  `Blog` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`BlogLinkId`),
  KEY `Blog` (`Blog`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `BlogLink` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `BlogPost`;
CREATE TABLE `BlogPost` (
  `BlogPostId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Content` mediumtext,
  `AuthorId` int(11) DEFAULT NULL,
  `Blog` int(11) DEFAULT NULL,
  `IsPublished` char(1) DEFAULT NULL,
  `ContentEditor` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`BlogPostId`),
  KEY `Blog` (`Blog`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `BlogPost` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `BlogPostChange`;
CREATE TABLE `BlogPostChange` (
  `BlogPostChangeId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `BlogPost` int(11) DEFAULT NULL,
  `Content` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`BlogPostChangeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `BlogPostChange` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `BlogPostTag`;
CREATE TABLE `BlogPostTag` (
  `BlogPostTagId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `BlogPost` int(11) DEFAULT NULL,
  `Tag` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`BlogPostTagId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `BlogPostTag` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `BlogSubscriber`;
CREATE TABLE `BlogSubscriber` (
  `BlogSubscriberId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Email` mediumtext,
  `Blog` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`BlogSubscriberId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `BlogSubscriber` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `Comment`;
CREATE TABLE `Comment` (
  `CommentId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `AuthorId` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `PrevComment` int(11) DEFAULT NULL,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `ExternalAuthor` mediumtext,
  `ExternalEmail` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`CommentId`),
  KEY `i$29` (`RecordModified`),
  KEY `i$30` (`VPD`),
  KEY `i$31` (`ObjectId`,`ObjectClass`),
  FULLTEXT KEY `I$search$caption` (`Caption`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `Comment` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `Donation`;
CREATE TABLE `Donation` (
  `DonationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `WMZVolume` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`DonationId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `Donation` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `Email`;
CREATE TABLE `Email` (
  `EmailId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ToAddress` mediumtext,
  `FromAddress` mediumtext,
  `Body` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`EmailId`),
  UNIQUE KEY `XPKEmail` (`EmailId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `Email` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `EmailQueue`;
CREATE TABLE `EmailQueue` (
  `EmailQueueId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `FromAddress` mediumtext,
  `MailboxClass` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`EmailQueueId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


LOCK TABLES `EmailQueue` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `EmailQueueAddress`;
CREATE TABLE `EmailQueueAddress` (
  `EmailQueueAddressId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ToAddress` mediumtext,
  `EmailQueue` int(11) DEFAULT NULL,
  `cms_UserId` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`EmailQueueAddressId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `EmailQueueAddress` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `MeetingParticipation`;
CREATE TABLE `MeetingParticipation` (
  `MeetingParticipationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Meeting` int(11) DEFAULT NULL,
  `Participant` int(11) DEFAULT NULL,
  `Comments` mediumtext,
  `VPD` varchar(32) DEFAULT NULL,
  `Accepted` char(1) DEFAULT NULL,
  `Rejected` char(1) DEFAULT NULL,
  `RejectReason` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `RememberInterval` int(11) DEFAULT NULL,
  PRIMARY KEY (`MeetingParticipationId`),
  UNIQUE KEY `XPKMeetingParticipation` (`MeetingParticipationId`),
  KEY `MeetingParticipation_vpd_idx` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `MeetingParticipation` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `News`;
CREATE TABLE `News` (
  `NewsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Content` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`NewsId`),
  UNIQUE KEY `XPKNews` (`NewsId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `News` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `ObjectChangeLog`;
CREATE TABLE `ObjectChangeLog` (
  `ObjectChangeLogId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ObjectId` int(11) DEFAULT NULL,
  `EntityRefName` varchar(128) DEFAULT NULL,
  `ChangeKind` mediumtext,
  `Author` varchar(255) DEFAULT NULL,
  `Content` mediumtext,
  `ObjectUrl` mediumtext,
  `EntityName` varchar(128) DEFAULT NULL,
  `VisibilityLevel` int(11) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `ClassName` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ObjectChangeLogId`,`RecordModified`),
  KEY `I$ObjectChangeLog$ClassName` (`ClassName`),
  KEY `I$ObjectChangeLog$RecordModified` (`RecordModified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (UNIX_TIMESTAMP(RecordModified))
(PARTITION p_201305 VALUES LESS THAN (1370030400) ENGINE = MyISAM,
 PARTITION p_201306 VALUES LESS THAN (1372622400) ENGINE = MyISAM,
 PARTITION p_201307 VALUES LESS THAN (1375300800) ENGINE = MyISAM,
 PARTITION p_201308 VALUES LESS THAN (1377979200) ENGINE = MyISAM,
 PARTITION p_201309 VALUES LESS THAN (1380571200) ENGINE = MyISAM,
 PARTITION p_max VALUES LESS THAN MAXVALUE ENGINE = MyISAM) */;


LOCK TABLES `ObjectChangeLog` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `ObjectChangeLog_Delete`;
CREATE TABLE `ObjectChangeLog_Delete` (
  `ObjectChangeLogId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ObjectId` int(11) DEFAULT NULL,
  `EntityRefName` varchar(128) DEFAULT NULL,
  `ChangeKind` mediumtext,
  `Author` int(11) DEFAULT NULL,
  `Content` mediumtext,
  `ObjectUrl` mediumtext,
  `EntityName` varchar(128) DEFAULT NULL,
  `VisibilityLevel` int(11) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `ClassName` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ObjectChangeLogId`),
  UNIQUE KEY `XPKObjectChangeLog` (`ObjectChangeLogId`),
  KEY `ObjectId` (`ObjectId`,`EntityRefName`(50),`VPD`),
  KEY `I$48` (`Author`),
  KEY `I$ObjectChangeLog$VPD` (`VPD`),
  KEY `I$ObjectChangeLog$OrderNum` (`OrderNum`),
  KEY `I$ObjectChangeLog$ObjectClass` (`ObjectId`,`ClassName`),
  KEY `I$ObjectChangeLog$ClassName` (`ClassName`),
  KEY `I$ObjectChangeLog$ObjectIdClassName` (`ObjectId`,`ClassName`),
  KEY `I$ObjectChangeLog$RecordCreated` (`RecordCreated`),
  KEY `I$ObjectChangeLog$RecordModified` (`RecordModified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `ObjectChangeLog_Delete` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `ObjectEmailNotification`;
CREATE TABLE `ObjectEmailNotification` (
  `ObjectEmailNotificationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Header` mediumtext,
  `RecordDescription` mediumtext,
  `Footer` mediumtext,
  `IsAdd` char(1) DEFAULT NULL,
  `IsModify` char(1) DEFAULT NULL,
  `IsDelete` char(1) DEFAULT NULL,
  `HeaderEn` mediumtext,
  `FooterEn` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`ObjectEmailNotificationId`),
  UNIQUE KEY `XPKObjectEmailNotification` (`ObjectEmailNotificationId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `ObjectEmailNotification` WRITE;
INSERT INTO `ObjectEmailNotification` (`ObjectEmailNotificationId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Header`, `RecordDescription`, `Footer`, `IsAdd`, `IsModify`, `IsDelete`, `HeaderEn`, `FooterEn`, `RecordVersion`) VALUES (1,'2006-01-14 15:01:36','2010-06-06 18:05:13','',10,'Общее уведомление','','','Письмо автоматически сформировано системой управления процессом разработки (%SERVER_NAME%).\nДля исключения себя из списка рассылки обратитесь к координатору Вашего проекта.','Y','Y','Y','','The e-mail have been generated automatically by Development process management system (%SERVER_NAME%).\nTo unsubscribe please ask coordinator of your project.',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `ObjectEmailNotificationLink`;
CREATE TABLE `ObjectEmailNotificationLink` (
  `ObjectEmailNotificationLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `EmailNotification` int(11) DEFAULT NULL,
  `EntityReferenceName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`ObjectEmailNotificationLinkId`),
  UNIQUE KEY `XPKObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


LOCK TABLES `ObjectEmailNotificationLink` WRITE;
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (1,'2006-01-14 15:01:51','2006-01-14 15:01:51','',10,1,'pm_Release',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (2,'2006-01-14 19:09:59','2006-01-14 19:09:59','',20,1,'pm_Participant',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (3,'2006-01-14 19:10:13','2006-01-14 19:10:13','',30,1,'pm_Project',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (4,'2006-01-14 19:10:39','2006-01-14 19:10:39','',40,1,'pm_ChangeRequest',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (5,'2006-01-14 19:14:06','2006-01-14 19:14:06','',50,1,'pm_Artefact',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (6,'2006-01-14 21:04:03','2006-01-14 21:04:03','',60,1,'pm_Enhancement',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (7,'2006-01-14 21:04:16','2006-01-14 21:04:16','',70,1,'pm_Bug',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (8,'2006-01-14 21:04:26','2006-01-14 21:04:26','',80,1,'pm_Task',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (9,'2006-03-09 22:21:06','2006-03-09 22:21:06','',90,1,'cms_Link',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (10,'2010-06-06 18:05:03','2010-06-06 18:05:03','',100,1,'Comment',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (11,'2010-06-06 18:05:09','2010-06-06 18:05:09','',110,1,'WikiPage',0);
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (12,'2010-06-06 18:05:17','2010-06-06 18:05:17','',120,1,'pm_Scrum',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `Priority`;
CREATE TABLE `Priority` (
  `PriorityId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `RelatedColor` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`PriorityId`),
  UNIQUE KEY `XPKPriority` (`PriorityId`),
  KEY `Priority_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


LOCK TABLES `Priority` WRITE;
INSERT INTO `Priority` (`PriorityId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`, `RelatedColor`) VALUES (1,'2005-12-24 11:55:57','2005-12-24 23:18:52',10,'Критично',NULL,0,'#DB7A40');
INSERT INTO `Priority` (`PriorityId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`, `RelatedColor`) VALUES (2,'2005-12-24 11:56:23','2005-12-24 23:18:57',20,'Высокий',NULL,0,'#D5BB28');
INSERT INTO `Priority` (`PriorityId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`, `RelatedColor`) VALUES (3,'2005-12-24 11:56:38','2005-12-24 23:19:02',30,'Обычный',NULL,0,'#6969A5');
INSERT INTO `Priority` (`PriorityId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`, `RelatedColor`) VALUES (4,'2005-12-24 11:56:48','2005-12-24 23:19:08',40,'Низкий',NULL,0,'#6969A5');
INSERT INTO `Priority` (`PriorityId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`, `RelatedColor`) VALUES (5,'2005-12-24 11:57:18','2005-12-24 23:19:13',50,'В свободное время',NULL,0,'#6969A5');
UNLOCK TABLES;


DROP TABLE IF EXISTS `SystemLogSQL`;
CREATE TABLE `SystemLogSQL` (
  `SQLId` int(11) NOT NULL AUTO_INCREMENT,
  `SQLContent` mediumtext,
  `RecordCreated` datetime DEFAULT NULL,
  PRIMARY KEY (`SQLId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `SystemLogSQL` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `TemplateHTML`;
CREATE TABLE `TemplateHTML` (
  `TemplateHTMLId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `CSSBlock` mediumtext,
  `Header` mediumtext,
  `Footer` mediumtext,
  `HeaderContents` char(1) DEFAULT NULL,
  `SectionNumbers` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`TemplateHTMLId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `TemplateHTML` WRITE;
INSERT INTO `TemplateHTML` (`TemplateHTMLId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `CSSBlock`, `Header`, `Footer`, `HeaderContents`, `SectionNumbers`, `RecordVersion`) VALUES (1,'2006-02-23 23:25:44','2006-02-26 12:24:10','f22f2d9a48cc34256d431998bb260517',10,'Помощь','Для генерации справки (помощи) для внутренней части системы','body {font-size:8pt;font-family:verdana;line-height:145%;\r\nmargin-left:10pt;margin-right:15pt;}\r\nh3 {font-family:arial;}\r\ndiv {padding-left:3pt;text-align:justify;padding-left:5pt;}\r\ntd {font-size:8pt;text-align:justify;line-height:145%;}','','','Y','Y',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `TemplateHTML2`;
CREATE TABLE `TemplateHTML2` (
  `TemplateHTML2Id` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `CSSBlock` mediumtext,
  `Header` mediumtext,
  `Footer` mediumtext,
  `HeaderContents` char(1) DEFAULT NULL,
  `SectionNumbers` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`TemplateHTML2Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `TemplateHTML2` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `WikiPage`;
CREATE TABLE `WikiPage` (
  `WikiPageId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` int(11) DEFAULT NULL,
  `Content` longtext,
  `ParentPage` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `Author` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `UserField1` mediumtext,
  `IsTemplate` int(11) DEFAULT NULL,
  `UserField2` mediumtext,
  `IsArchived` char(1) DEFAULT NULL,
  `UserField3` longtext,
  `IsDraft` char(1) DEFAULT NULL,
  `ContentEditor` mediumtext,
  `State` varchar(32) DEFAULT NULL,
  `PageType` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StateObject` int(11) DEFAULT NULL,
  `ParentPath` mediumtext,
  `SectionNumber` mediumtext,
  `DocumentId` int(11) DEFAULT NULL,
  `SortIndex` mediumtext,
  PRIMARY KEY (`WikiPageId`),
  UNIQUE KEY `XPKWikiPage` (`WikiPageId`),
  KEY `WikiPage_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`,`VPD`),
  KEY `ParentPage` (`ParentPage`,`VPD`),
  KEY `WikiPage$Archived` (`ParentPage`,`IsArchived`),
  KEY `I$WikiPage$State` (`State`),
  KEY `I$WikiPage$ParentPage` (`ParentPage`),
  KEY `I$WikiPage$ReferenceName` (`ReferenceName`),
  KEY `I$WikiPage$Document` (`DocumentId`),
  FULLTEXT KEY `I$43` (`Caption`,`Content`),
  FULLTEXT KEY `I$WikiPage$ParentPath` (`ParentPath`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `WikiPage` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `WikiPageChange`;
CREATE TABLE `WikiPageChange` (
  `WikiPageChangeId` int(11) NOT NULL AUTO_INCREMENT,
  `WikiPage` int(11) DEFAULT NULL,
  `Content` longtext,
  `Author` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`WikiPageChangeId`),
  UNIQUE KEY `XPKWikiPageChange` (`WikiPageChangeId`),
  KEY `WikiPageChange_vpd_idx` (`VPD`),
  KEY `WikiPage` (`WikiPage`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `WikiPageChange` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `WikiPageFile`;
CREATE TABLE `WikiPageFile` (
  `WikiPageFileId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `ContentMime` mediumtext,
  `ContentPath` mediumtext,
  `ContentExt` varchar(255) DEFAULT NULL,
  `WikiPage` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`WikiPageFileId`),
  UNIQUE KEY `XPKWikiPageFile` (`WikiPageFileId`),
  KEY `WikiPageFile_vpd_idx` (`VPD`),
  KEY `WikiPage` (`WikiPage`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `WikiPageFile` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `WikiPageTrace`;
CREATE TABLE `WikiPageTrace` (
  `WikiPageTraceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `SourcePage` int(11) DEFAULT NULL,
  `TargetPage` int(11) DEFAULT NULL,
  `IsActual` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `Baseline` int(11) DEFAULT NULL,
  `Type` varchar(128) DEFAULT NULL,
  `UnsyncReasonType` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`WikiPageTraceId`),
  KEY `I$WikiPageTrace$Source` (`SourcePage`),
  KEY `I$WikiPageTrace$Target` (`TargetPage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `WikiPageTrace` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `WikiPageType`;
CREATE TABLE `WikiPageType` (
  `WikiPageTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `ReferenceName` mediumtext,
  `DefaultPageTemplate` int(11) DEFAULT NULL,
  `ShortCaption` mediumtext,
  `WikiEditor` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `PageReferenceName` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`WikiPageTypeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `WikiPageType` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `WikiTag`;
CREATE TABLE `WikiTag` (
  `WikiTagId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Wiki` int(11) DEFAULT NULL,
  `Tag` int(11) DEFAULT NULL,
  `WikiReferenceName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`WikiTagId`),
  KEY `Wiki` (`Wiki`,`Tag`,`VPD`),
  KEY `I$WikiTag$Wiki` (`Wiki`),
  KEY `I$WikiTag$Tag` (`Tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `WikiTag` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `attribute`;
CREATE TABLE `attribute` (
  `attributeId` int(11) NOT NULL AUTO_INCREMENT,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `AttributeType` varchar(64) DEFAULT NULL,
  `DefaultValue` mediumtext,
  `IsRequired` char(1) DEFAULT NULL,
  `IsVisible` char(1) DEFAULT NULL,
  `entityId` int(11) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`attributeId`),
  UNIQUE KEY `XPKattribute` (`attributeId`),
  KEY `attribute_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30),`entityId`),
  KEY `entityId` (`entityId`),
  KEY `I$attribute$Type` (`AttributeType`)
) ENGINE=MyISAM AUTO_INCREMENT=1620 DEFAULT CHARSET=utf8;


LOCK TABLES `attribute` WRITE;
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1,'Название','Caption','VARCHAR','','Y','Y',2,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (2,'Ссылочное имя','ReferenceName','VARCHAR','','Y','Y',2,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (3,'PHP файл','PHPFile','TEXT','','Y','Y',2,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (4,'Меню','Menu','REF_cms_MainMenuId','','Y','Y',2,40,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (5,'Название','Caption','VARCHAR','','Y','Y',1,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (6,'Ссылочное имя','ReferenceName','VARCHAR','','Y','Y',1,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (7,'Имя','Caption','VARCHAR','','N','Y',3,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (8,'E-mail','Email','TEXT','','N','Y',3,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (9,'Логин','Login','TEXT','','N','Y',3,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1527,'Является уникальным','IsUnique','CHAR','N','N','Y',353,75,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (11,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',4,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (12,'Проект','Project','REF_pm_ProjectId','','Y','Y',4,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (13,'Роль в проекте','ProjectRole','REF_pm_ProjectRoleId','','Y','Y',4,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (14,'Название','Caption','VARCHAR','','Y','Y',6,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1339,'Группа','UserGroup','REF_co_UserGroupId',NULL,'Y','Y',323,10,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (16,'Название','Caption','VARCHAR','','Y','Y',5,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (17,'Описание','Description','RICHTEXT','',NULL,'Y',5,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (18,'Название','Caption','VARCHAR','','Y','Y',7,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (19,'Название','Caption','VARCHAR','','Y','Y',8,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (20,'Описание','Description','RICHTEXT','',NULL,'Y',8,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (21,'Файл','Content','FILE','','Y','Y',8,5,NULL,'2006-01-26 21:07:52',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (22,'Кодовое название','CodeName','TEXT','','Y','Y',5,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (23,'Проект','Project','REF_pm_ProjectId','','Y','N',8,50,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (24,'Каталог','Kind','REF_pm_ArtefactTypeId','','Y','Y',8,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (25,'Название','Caption','VARCHAR','','Y','Y',9,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (26,'Ссылочное имя','ReferenceName','VARCHAR','','Y','N',9,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (27,'Содержание','Content','LARGETEXT','','','N',9,30,NULL,'2006-02-16 21:25:22',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (28,'Родительская страница','ParentPage','REF_WikiPageId','',NULL,'N',9,40,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (29,'Название','Caption','VARCHAR','','N','N',10,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (30,'Описание','Description','VARCHAR','',NULL,'Y',10,35,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (31,'Файл','Content','FILE','','Y','Y',10,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (32,'Страница','WikiPage','REF_WikiPageId','','Y','',10,40,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (33,'Проект','Project','REF_pm_ProjectId','','Y','N',9,50,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (34,'Название','Caption','VARCHAR','','Y','Y',11,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (35,'Описание','Description','RICHTEXT','',NULL,'Y',11,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (361,'Окружение','Environment','REF_pm_EnvironmentId','',NULL,'Y',11,26,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (37,'Файл','Attachment','FILE','',NULL,'Y',11,40,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (38,'Обнаружил','Submitter','REF_pm_ParticipantId','','Y','Y',11,60,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (39,'Состояние','State','TEXT','',NULL,'Y',11,50,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (40,'Название','Caption','VARCHAR','','Y','Y',12,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (41,'Описание','Description','RICHTEXT','',NULL,'Y',12,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (42,'Требование','Requirement','REF_WikiPageId','',NULL,'Y',12,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (43,'Файл','Attachment','FILE','',NULL,'Y',12,50,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (44,'Составитель','Submitter','REF_pm_ParticipantId','','Y','Y',12,40,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (45,'Название','ReleaseNumber','VARCHAR','','Y','Y',14,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (46,'Описание','Description','RICHTEXT','',NULL,'Y',14,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (47,'Проект','Project','REF_pm_ProjectId','','Y','Y',14,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (48,'Дата начала','StartDate','DATE','','Y','Y',14,40,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (49,'Дата окончания','FinishDate','DATE','','Y','Y',14,50,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (50,'Итерация','Release','REF_pm_ReleaseId',NULL,'N','Y',15,15,NULL,'2010-06-06 18:05:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (53,'Комментарий','Comments','RICHTEXT','','','Y',15,100,NULL,'2006-02-01 23:02:08',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (54,'Исполнитель','Assignee','REF_cms_UserId','','Y','Y',15,10,NULL,'2005-12-27 21:58:24',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (56,'Планируемая трудоемкость, ч.','Planned','FLOAT','','Y','Y',15,7,NULL,'2005-12-23 23:04:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (58,'Страница','WikiPage','REF_WikiPageId','','Y',NULL,16,10,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (59,'Содержание','Content','LARGETEXT','','Y','Y',16,20,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (60,'Автор','Author','REF_pm_ParticipantId','','Y','Y',16,30,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (61,'Автор','Author','REF_pm_ParticipantId','','Y','Y',9,60,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (62,'Платформа','Platform','LARGETEXT','',NULL,'N',5,40,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (63,'Инструментарий','Tools','LARGETEXT','','N','N',5,160,NULL,'2010-06-06 18:05:23',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (64,'Главная страница','MainWikiPage','REF_WikiPageId','','','N',5,60,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (65,'Страница требований','RequirementsWikiPage','REF_WikiPageId','','','N',5,70,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (66,'Дата начала','StartDate','DATE','','Y','Y',5,80,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (67,'Дата окончания','FinishDate','DATE','',NULL,'Y',5,90,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1540,'UID','UID','TEXT',NULL,'N','N',363,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (70,'Проект','Project','REF_pm_ProjectId','','Y',NULL,12,60,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (71,'Выложил','Participant','REF_pm_ParticipantId','','Y',NULL,8,60,'2005-12-22 22:41:37','2005-12-22 22:41:37',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (72,'Требование','Requirement','REF_WikiPageId','','Y','Y',11,35,'2005-12-23 21:42:07','2005-12-23 21:42:07',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (73,'Проект','Project','REF_pm_ProjectId','','Y','Y',11,70,'2005-12-23 21:51:05','2005-12-23 21:51:05',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (74,'Текущая','IsCurrent','CHAR',NULL,'N','Y',14,15,'2005-12-23 22:41:09','2010-06-06 18:05:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (75,'ICQ','ICQNumber','TEXT','','N','N',3,25,'2005-12-24 11:54:06','2010-06-06 18:05:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (76,'Название','Caption','VARCHAR','','Y','Y',17,10,'2005-12-24 11:55:04','2005-12-24 11:55:04',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (77,'Тема','Subject','LARGETEXT','','Y','Y',18,10,'2005-12-24 11:58:15','2005-12-24 11:58:15',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (78,'Место','Location','TEXT','','Y','Y',18,20,'2005-12-24 11:58:51','2005-12-24 11:58:51',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (79,'Дата','MeetingDate','DATE','','Y','Y',18,30,'2005-12-24 11:59:13','2005-12-24 11:59:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (80,'Митинг','Meeting','REF_pm_MeetingId','','Y','Y',19,10,'2005-12-24 12:01:17','2005-12-24 12:01:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (81,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',19,20,'2005-12-24 12:01:45','2005-12-24 12:01:45',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (82,'Комментарий','Comments','LARGETEXT','',NULL,'Y',19,30,'2005-12-24 12:02:01','2005-12-24 12:02:01',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (83,'Средняя загрузка в день, ч.','Capacity','INTEGER','','N','Y',3,15,'2005-12-24 14:50:47','2005-12-24 14:50:47',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (84,'Проект','Project','REF_pm_ProjectId','','Y',NULL,3,50,'2005-12-24 14:51:40','2005-12-24 14:51:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (85,'Название','Caption','VARCHAR','','Y','Y',20,10,'2005-12-24 21:50:11','2005-12-24 21:50:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (86,'Тип','TaskType','REF_pm_TaskTypeId','','Y','Y',15,20,'2005-12-24 21:52:25','2005-12-24 21:52:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (87,'Приоритет','Priority','REF_PriorityId','3','Y','Y',15,30,'2005-12-24 22:59:00','2005-12-24 23:04:57',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (88,'Бизнес приоритет','Priority','REF_PriorityId','3','Y','Y',11,25,'2005-12-24 23:05:44','2006-01-11 23:51:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (89,'Бизнес приоритет','Priority','REF_PriorityId','3','Y','Y',12,25,'2005-12-24 23:13:39','2005-12-24 23:13:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1324,'Название','Caption','VARCHAR',NULL,'Y','Y',318,10,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (91,'Название','Caption','VARCHAR','','Y','Y',21,10,'2005-12-25 00:23:10','2005-12-25 00:23:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1407,'Состояние','State','TEXT',NULL,'N','N',9,150,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (93,'Результат','Result','LARGETEXT','',NULL,'N',15,90,'2005-12-25 11:49:28','2005-12-25 11:49:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (94,'Проверяющий','Controller','REF_pm_ParticipantId','','','Y',15,52,'2005-12-27 21:59:04','2005-12-27 21:59:23',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (95,'Название','Caption','VARCHAR','','Y','Y',22,10,'2005-12-28 09:03:31','2005-12-28 09:03:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (96,'Описание','Description','RICHTEXT','',NULL,'Y',22,20,'2005-12-28 09:03:57','2005-12-28 09:03:57',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (97,'Приоритет','Priority','REF_PriorityId','3','Y','Y',22,30,'2005-12-28 09:04:36','2005-12-28 09:04:36',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (98,'Автор','Author','REF_cms_UserId','','Y','Y',22,40,'2005-12-28 09:05:08','2006-02-01 01:02:26',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (99,'Проект','Project','REF_pm_ProjectId','','Y','N',22,50,'2005-12-28 09:29:32','2005-12-28 09:29:32',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (100,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId','',NULL,'Y',15,35,'2005-12-28 09:31:21','2005-12-28 09:31:21',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (101,'IP адрес','IPAddress','TEXT','','Y','Y',23,10,'2006-01-06 13:34:32','2006-01-06 13:34:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (102,'Проект','Project','REF_pm_ProjectId','','Y','Y',23,20,'2006-01-06 13:35:03','2006-01-06 13:35:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (103,'Содержание','Content','LARGETEXT','','Y','Y',24,10,'2006-01-06 18:51:53','2006-01-06 18:51:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (104,'Тема','Caption','VARCHAR','','Y','Y',25,10,'2006-01-06 21:13:47','2006-01-06 21:13:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (105,'Кому','ToAddress','TEXT','',NULL,'Y',25,20,'2006-01-06 21:14:08','2006-01-06 21:14:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (106,'От кого','FromAddress','TEXT','','Y','Y',25,30,'2006-01-06 21:14:21','2006-01-06 21:14:21','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (107,'Содержание','Body','LARGETEXT','','Y','Y',25,40,'2006-01-06 21:14:44','2006-01-06 21:14:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (108,'Проект','Project','REF_pm_ProjectId','','Y','Y',26,10,'2006-01-09 16:49:16','2006-01-09 16:49:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (109,'Участник','Participant','REF_cms_UserId','','Y','Y',26,20,'2006-01-09 16:51:46','2006-01-09 16:51:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (110,'Название','Caption','VARCHAR','','Y','Y',27,10,'2006-01-09 22:42:49','2006-01-09 22:42:49','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (111,'Описание','Description','LARGETEXT','',NULL,'Y',27,20,'2006-01-09 22:43:03','2006-01-09 22:43:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (112,'Версия','Version','VARCHAR','1','Y','Y',27,30,'2006-01-09 22:43:38','2006-02-20 22:22:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (113,'Содержание','Content','REF_WikiPageId','','','',27,40,'2006-01-09 22:44:13','2006-01-09 22:51:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (114,'Проект','Project','REF_pm_ProjectId','','Y','',27,50,'2006-01-09 22:44:26','2006-01-09 22:51:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (115,'Название','Caption','RICHTEXT',NULL,'N','Y',15,5,'2006-01-12 08:37:37','2010-06-06 18:05:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (116,'Ид справки','HelpId','INTEGER','','','Y',2,50,'2006-01-12 23:40:59','2006-01-12 23:41:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (117,'Уведомление об операции над объектом','EmailNotification','REF_ObjectEmailNotificationId','','Y','Y',29,10,'2006-01-14 14:54:24','2006-01-14 14:54:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (118,'Ссылочное имя класса','EntityReferenceName','TEXT','','Y','Y',29,20,'2006-01-14 14:54:39','2006-01-14 14:54:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (119,'Название','Caption','VARCHAR','','Y','Y',28,10,'2006-01-14 14:55:22','2006-01-14 14:55:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (120,'Заголовок','Header','LARGETEXT','',NULL,'Y',28,20,'2006-01-14 14:56:07','2006-01-14 14:56:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (121,'Описание атрибутов объекта','RecordDescription','LARGETEXT','',NULL,'Y',28,30,'2006-01-14 14:56:25','2006-01-14 14:56:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (122,'Окончание','Footer','LARGETEXT','',NULL,'Y',28,40,'2006-01-14 14:56:43','2006-01-14 14:56:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (123,'Активно при создании','IsAdd','CHAR','Y',NULL,'Y',28,50,'2006-01-14 14:57:09','2006-01-14 14:57:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (124,'Активно при модификации','IsModify','CHAR','Y',NULL,'Y',28,60,'2006-01-14 14:57:46','2006-01-14 14:57:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (125,'Активно при удалении','IsDelete','CHAR','Y',NULL,'Y',28,70,'2006-01-14 14:58:00','2006-01-14 14:58:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (127,'Название объекта','Caption','VARCHAR','',NULL,'Y',30,10,'2006-01-16 21:30:13','2006-01-16 21:30:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (128,'Ид объекта','ObjectId','INTEGER','',NULL,'N',30,20,'2006-01-16 21:30:38','2006-01-16 21:30:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (129,'Класс объекта','EntityRefName','TEXT','','Y','N',30,30,'2006-01-16 21:31:00','2006-01-16 21:31:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (130,'Тип','ChangeKind','TEXT','','Y','N',30,40,'2006-01-16 21:31:22','2006-01-16 21:31:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (131,'Автор','Author','VARCHAR','',NULL,'Y',30,60,'2006-01-16 21:31:42','2006-01-16 21:31:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (132,'Содержание','Content','LARGETEXT','',NULL,'Y',30,50,'2006-01-16 21:31:56','2006-01-16 21:31:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1430,'Категория','Category','TEXT',NULL,'Y','Y',345,20,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1429,'Название','Caption','VARCHAR',NULL,'Y','Y',345,10,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1428,'Параметры','Parameters','LARGETEXT',NULL,'N','Y',314,25,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1427,'Кодовое название','ReferenceName','VARCHAR',NULL,'Y','Y',85,20,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (137,'Url объекта','ObjectUrl','TEXT','',NULL,'N',30,35,'2006-01-18 01:15:45','2006-01-18 01:15:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (138,'Название сущности','EntityName','TEXT','',NULL,'N',30,37,'2006-01-18 01:20:33','2006-01-18 01:20:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (139,'Связь со справочной документацией','HelpLink','REF_HelpLinkId','',NULL,'Y',32,10,'2006-01-19 10:00:58','2006-01-19 10:00:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (140,'Задача','Task','REF_pm_TaskId','',NULL,'Y',32,20,'2006-01-19 10:02:21','2006-01-19 10:02:21','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (141,'Содержание','Caption','LARGETEXT','','Y','Y',35,10,'2006-01-21 14:41:10','2006-01-21 14:41:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (142,'Автор','AuthorId','REF_cms_UserId','','Y','Y',35,20,'2006-01-21 14:41:28','2006-01-21 14:41:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (143,'Объект','ObjectId','INTEGER','',NULL,'Y',35,30,'2006-01-21 14:41:46','2006-01-21 14:41:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (144,'Предыдущий комментарий','PrevComment','REF_CommentId','',NULL,'Y',35,40,'2006-01-21 14:42:36','2006-01-21 14:42:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (145,'Класс объекта','ObjectClass','TEXT','','','Y',35,35,'2006-01-21 23:50:46','2006-01-21 23:51:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (146,'Ид сессии','SessionHash','TEXT','','Y','Y',26,30,'2006-01-22 16:32:27','2006-01-22 16:32:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (147,'Дата последнего входа','PrevLoginDate','DATE','',NULL,'Y',26,40,'2006-01-22 17:27:34','2006-01-22 17:27:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (148,'Уровень видимости','VisibilityLevel','INTEGER','','','N',30,70,'2006-01-24 22:20:39','2006-01-24 22:54:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (149,'Дом. тел.','HomePhone','TEXT','',NULL,'N',3,27,'2006-01-25 19:05:09','2006-01-25 19:05:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (150,'Моб. тел.','MobilePhone','TEXT','',NULL,'N',3,28,'2006-01-25 19:05:35','2006-01-25 19:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (151,'Использовать фазу подготовки требований','IsRequirements','CHAR','Y',NULL,'N',36,90,'2006-01-25 21:02:16','2006-01-25 21:02:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (152,'Использовать фазу подготовки справочной документации','IsHelps','CHAR','Y',NULL,'N',36,110,'2006-01-25 21:02:53','2006-01-25 21:02:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (153,'Использовать фазу тестирования','IsTests','CHAR','Y',NULL,'N',36,100,'2006-01-25 21:03:41','2006-01-25 21:03:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (154,'Проект','Project','REF_pm_ProjectId','','Y','Y',36,100,'2006-01-25 21:05:31','2006-03-11 13:00:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (156,'Кодовое название','CodeName','TEXT','',NULL,'Y',23,30,'2006-01-27 21:31:50','2006-01-27 21:31:50','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (157,'Название','Caption','VARCHAR','',NULL,'Y',23,40,'2006-01-27 21:32:06','2006-01-27 21:32:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (158,'Логин','Login','TEXT','',NULL,'Y',23,50,'2006-01-27 21:32:19','2006-01-27 21:32:19','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (159,'Email','Email','TEXT','',NULL,'Y',23,60,'2006-01-27 21:32:30','2006-01-27 21:32:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (160,'Пароль','Password','TEXT','',NULL,'Y',23,70,'2006-01-27 21:32:47','2006-01-27 21:32:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (161,'Методология','Methodology','TEXT','',NULL,'Y',23,80,'2006-01-27 21:34:02','2006-01-27 21:34:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (162,'Хеш создания','CreationHash','TEXT','',NULL,'Y',23,90,'2006-01-27 21:46:12','2006-01-27 21:46:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (163,'Магазин','BookStore','TEXT','','Y','Y',37,10,'2006-01-29 21:41:58','2006-01-29 21:41:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (164,'Идентификатор','BookUIN','TEXT','','Y','Y',37,20,'2006-01-29 21:42:14','2006-01-29 21:42:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (165,'Название книги','Caption','VARCHAR','','Y','Y',37,30,'2006-01-29 21:42:44','2006-01-29 21:42:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (166,'Url','BookUrl','TEXT','','Y','Y',37,40,'2006-01-29 21:42:55','2006-01-29 21:42:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (167,'Изображение','ImageUrl','TEXT','','Y','Y',37,50,'2006-01-29 21:45:47','2006-01-29 21:45:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (179,'Имя пожертвовашего','Caption','VARCHAR','',NULL,'Y',38,10,'2006-02-02 22:00:31','2006-02-02 22:00:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (180,'Величина взноса (WMZ)','WMZVolume','TEXT','',NULL,'Y',38,20,'2006-02-02 22:00:59','2006-02-02 22:00:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (181,'Номер','Caption','VARCHAR','','Y','Y',39,10,'2006-02-09 22:22:43','2006-02-09 22:22:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (182,'Описание','Description','LARGETEXT','',NULL,'Y',39,20,'2006-02-09 22:23:58','2006-02-09 22:23:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1309,'База знаний','KnowledgeBase','INTEGER','0','Y','N',316,30,'2010-10-01 17:16:24','2010-10-01 17:16:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (184,'Проект','Project','REF_pm_ProjectId','','Y',NULL,39,40,'2006-02-09 22:26:49','2006-02-09 22:26:49','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (185,'Релиз','Version','REF_pm_VersionId',NULL,'Y','N',14,60,'2006-02-09 23:17:04','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (188,'Название','Caption','VARCHAR','','Y','Y',40,10,'2006-02-11 17:01:21','2006-02-11 17:01:21','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (189,'Заголовок','Caption','VARCHAR','','Y','Y',41,10,'2006-02-11 17:02:57','2006-02-11 17:02:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (190,'Содержание','Content','TEXT','','Y','Y',41,20,'2006-02-11 17:03:24','2006-02-11 17:03:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (191,'Автор','AuthorId','INTEGER','',NULL,NULL,41,30,'2006-02-11 17:03:42','2006-02-11 17:03:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (192,'Блог','Blog','REF_BlogId','','Y',NULL,41,40,'2006-02-11 17:03:56','2006-02-11 17:03:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (193,'Название','Caption','VARCHAR','','N','Y',42,10,'2006-02-11 17:05:56','2006-02-11 17:05:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (194,'Описание','Description','LARGETEXT','',NULL,'Y',42,20,'2006-02-11 17:06:09','2006-02-11 17:06:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (195,'Файл','Content','FILE','',NULL,'Y',42,30,'2006-02-11 17:06:38','2006-02-11 17:06:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (196,'Сообщение блога','BlogPost','REF_BlogPostId','','Y',NULL,42,40,'2006-02-11 17:07:23','2006-02-11 17:07:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (197,'Название блога','Caption','VARCHAR','','Y','Y',43,10,'2006-02-11 17:09:01','2006-02-11 17:09:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (198,'Описание блога','Description','LARGETEXT','',NULL,'Y',43,20,'2006-02-11 17:09:18','2006-02-11 17:09:18','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (199,'Ссылка на блог','BlogUrl','TEXT','','Y','Y',43,30,'2006-02-11 17:09:37','2006-02-11 17:09:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (200,'Блог','Blog','REF_BlogId','','Y',NULL,43,40,'2006-02-11 17:09:56','2006-02-11 17:09:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (201,'Электронный адрес','Email','TEXT','','Y','Y',44,10,'2006-02-11 17:11:24','2006-02-11 17:11:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (202,'Блог','Blog','REF_BlogId','','Y','Y',44,20,'2006-02-11 17:11:41','2006-02-12 22:15:48','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (203,'Блог проекта','Blog','REF_BlogId','','Y',NULL,5,110,'2006-02-11 17:21:10','2006-02-11 17:21:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (204,'Опубликовано','IsPublished','CHAR','N',NULL,NULL,41,50,'2006-02-11 18:04:37','2006-02-11 18:04:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (205,'Внешний автор','ExternalAuthor','TEXT','',NULL,NULL,35,50,'2006-02-12 12:17:23','2006-02-12 12:17:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (206,'Название','Caption','VARCHAR','','Y','Y',45,10,'2006-02-12 21:24:56','2006-02-12 21:24:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (207,'Описание','Description','LARGETEXT','','Y','Y',45,20,'2006-02-12 21:25:28','2006-02-12 21:25:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (208,'Отправитель','FromAddress','TEXT','','Y','Y',45,30,'2006-02-12 21:26:37','2006-02-12 21:26:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (209,'Адрес получателя','ToAddress','TEXT','','Y','Y',46,10,'2006-02-12 21:27:13','2006-02-12 21:27:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (210,'Очередь сообщений','EmailQueue','REF_EmailQueueId','','Y','Y',46,20,'2006-02-12 21:27:55','2006-02-12 21:27:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (211,'Проект','Project','REF_pm_ProjectId','','Y',NULL,47,10,'2006-02-13 21:25:07','2006-02-13 21:25:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (212,'Публиковать проект','IsProjectInfo','CHAR','N','N','Y',47,20,'2006-02-13 21:26:55','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (213,'Публиковать сведения об участниках проекта','IsParticipants','CHAR','N','N','Y',47,30,'2006-02-13 21:27:44','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (214,'Публиковать блог проекта','IsBlog','CHAR','N',NULL,'Y',47,40,'2006-02-13 21:28:07','2006-02-13 21:28:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1308,'Проект','Source','REF_pm_ProjectId',NULL,'Y','N',316,20,'2010-10-01 17:16:24','2010-10-01 17:16:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (535,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId',NULL,'Y','N',114,10,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (217,'Исходное пожелание','ChangeRequest','REF_pm_ChangeRequestId','',NULL,'Y',11,33,'2006-02-22 08:50:26','2006-02-22 08:50:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (218,'Исходное пожелание','ChangeRequest','REF_pm_ChangeRequestId','',NULL,'Y',12,27,'2006-02-22 08:51:35','2006-02-22 08:51:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (219,'Пользовательское поле 1','UserField1','TEXT','',NULL,'N',9,70,'2006-02-22 21:08:52','2006-02-22 21:08:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (220,'Название','Caption','VARCHAR','','Y','Y',48,10,'2006-02-23 22:32:44','2006-02-23 22:32:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (221,'Итерация','Description','RICHTEXT','',NULL,'Y',48,20,'2006-02-23 22:33:02','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (222,'Стили (CSS)','CSSBlock','LARGETEXT','',NULL,'Y',48,30,'2006-02-23 22:33:51','2006-02-23 22:33:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (223,'Верхний колонтитул','Header','LARGETEXT','',NULL,'Y',48,40,'2006-02-23 22:34:43','2006-02-23 22:34:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (224,'Нижний колонтитул','Footer','LARGETEXT','',NULL,'Y',48,50,'2006-02-23 22:35:17','2006-02-23 22:35:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (225,'С оглавлением в начале страницы','HeaderContents','CHAR','Y',NULL,'Y',48,60,'2006-02-23 22:36:50','2006-02-23 22:36:50','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1541,'Название','Caption','VARCHAR',NULL,'N','N',363,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (227,'Номер','Caption','VARCHAR','1','Y','Y',49,10,'2006-02-25 15:49:01','2006-02-25 15:49:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (228,'Комментарий','Description','RICHTEXT','',NULL,'Y',49,20,'2006-02-25 15:53:12','2006-02-25 15:53:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (229,'Результат проверки','Result','RICHTEXT','',NULL,'N',49,30,'2006-02-25 15:53:30','2006-02-25 15:53:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (230,'Релиз','Release','REF_pm_ReleaseId','','N',NULL,49,40,'2006-02-25 15:53:52','2006-02-25 15:53:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (231,'Нумеровать разделы','SectionNumbers','CHAR','Y',NULL,'Y',48,70,'2006-02-25 17:22:25','2006-02-25 17:22:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (232,'Сборка','Build','REF_pm_BuildId','','Y','Y',50,10,'2006-02-26 16:11:12','2006-02-26 16:11:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (233,'Задача','Task','REF_pm_TaskId','','Y','Y',50,20,'2006-02-26 16:11:25','2006-02-26 16:11:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (234,'Название','Caption','VARCHAR','','Y','Y',51,10,'2006-03-06 22:01:49','2006-03-06 22:01:49','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (235,'Ссылочное имя','ReferenceName','VARCHAR','','Y','Y',51,20,'2006-03-06 22:02:05','2006-03-06 22:02:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (236,'Адрес','Caption','VARCHAR','','Y','Y',52,10,'2006-03-06 22:03:00','2006-03-06 22:03:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (237,'Описание','Description','RICHTEXT','',NULL,'Y',52,20,'2006-03-06 22:03:17','2006-03-06 22:03:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (238,'Категория','Category','REF_cms_LinkCategoryId','','Y','Y',52,30,'2006-03-06 22:03:44','2006-03-06 22:03:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (239,'Заказчик принимает участие в проекте','IsUserInProject','CHAR','N',NULL,'N',36,300,'2006-03-08 09:05:26','2006-03-08 09:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (240,'Активна','IsActive','CHAR','Y',NULL,'Y',32,30,'2006-03-08 10:46:25','2006-03-08 10:46:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (241,'Опубликована','IsPublished','CHAR','N',NULL,'Y',52,40,'2006-03-09 22:34:08','2006-03-09 22:34:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (242,'Использовать итерации фиксированной длительности','IsFixedRelease','CHAR','Y','N','Y',36,18,'2006-03-11 12:59:54','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (243,'Длительность итерации в неделях','ReleaseDuration','INTEGER','1','N','Y',36,19,'2006-03-11 13:01:51','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1310,'Блог','Blog','INTEGER','0','Y','N',316,40,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (245,'Название','Caption','VARCHAR','','Y','Y',55,10,'2006-03-16 21:18:41','2006-03-16 21:18:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (246,'Wiki страница','Wiki','REF_WikiPageId','','Y','Y',56,10,'2006-03-16 21:19:55','2006-03-16 21:19:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (247,'Тэг','Tag','REF_TagId','','Y','Y',56,20,'2006-03-16 21:20:14','2006-03-16 21:20:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (248,'Тип Wiki страницы','WikiReferenceName','TEXT','',NULL,NULL,56,30,'2006-03-17 08:24:02','2006-03-17 08:24:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (249,'Название','Caption','VARCHAR','','Y','Y',57,10,'2006-03-21 23:28:06','2006-03-21 23:28:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (250,'Язык проекта','Language','REF_cms_LanguageId','1',NULL,'Y',5,38,'2006-03-21 23:29:30','2006-03-21 23:29:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (251,'Кодовое значение','CodeName','TEXT','','Y','Y',57,20,'2006-03-21 23:30:11','2006-03-21 23:30:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (252,'Язык проекта','Language','TEXT','',NULL,NULL,23,100,'2006-03-21 23:44:01','2006-03-21 23:44:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (253,'Пожелание','Request','REF_pm_ChangeRequestId','','Y',NULL,58,10,'2006-03-26 10:12:47','2006-03-26 10:12:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (254,'Тэг','Tag','REF_TagId','','Y','Y',58,20,'2006-03-26 10:13:00','2006-03-26 10:13:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (255,'Является шаблоном','IsTemplate','INTEGER','0','Y','N',9,80,'2006-03-26 17:22:13','2006-03-26 17:22:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (256,'Несколько конфигураций программного продукта','IsConfigurations','CHAR','N',NULL,'N',5,55,'2006-03-27 23:29:16','2006-03-27 23:29:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (257,'Название','Caption','VARCHAR','','Y','Y',59,10,'2006-03-27 23:31:22','2006-03-27 23:31:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (258,'Особенности конфигурации','Details','RICHTEXT','','Y','Y',59,20,'2006-03-27 23:32:01','2006-03-27 23:32:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1416,'Слабые стороны','Weaknesses','LARGETEXT',NULL,'N','Y',341,40,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (262,'Описание','Caption','VARCHAR','',NULL,'Y',60,10,'2010-06-06 18:05:01','2010-06-06 18:05:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (263,'Номер','Caption','VARCHAR','','Y','Y',61,10,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (264,'Описание','Description','LARGETEXT','',NULL,'Y',61,20,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (265,'Имя файла','BackupFileName','TEXT','','Y',NULL,60,20,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (266,'Название компании','Caption','VARCHAR','','Y','Y',62,10,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1306,'Версия','Version','VARCHAR',NULL,'N','Y',74,50,'2010-10-01 17:16:03','2010-10-01 17:16:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (268,'Язык интерфейса','Language','REF_cms_LanguageId','','Y','Y',62,30,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (269,'Публиковать домашнюю страницу проекта','IsKnowledgeBase','CHAR','N',NULL,'Y',47,50,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (270,'Публиковать информацию о релизах проекта','IsReleases','CHAR','N',NULL,'Y',47,60,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (271,'Публиковать страницу ввода пожеланий','IsChangeRequests','CHAR','Y',NULL,'Y',47,25,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (272,'E-mail внешнего автора','ExternalEmail','TEXT','',NULL,'Y',35,60,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (273,'Имя','Caption','VARCHAR','','Y','Y',63,10,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (274,'E-mail','Email','VARCHAR','','Y','Y',63,20,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (275,'Логин','Login','VARCHAR','','Y','Y',63,30,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (276,'ICQ','ICQ','TEXT','',NULL,'Y',63,40,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (277,'Телефон','Phone','TEXT','',NULL,'Y',63,50,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (278,'Пароль','Password','TEXT','','Y','N',63,60,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (279,'Пользователь','SystemUser','REF_cms_UserId','','Y',NULL,3,5,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (280,'Перекрыть атрибуты пользователя','OverrideUser','CHAR','N',NULL,'Y',3,19,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (281,'Хеш сессии','SessionHash','TEXT','',NULL,'N',63,70,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (282,'Название','Caption','VARCHAR','','Y','Y',64,10,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (283,'Описание','Description','RICHTEXT','','N','Y',64,20,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1415,'Сильные стороны','Strengths','LARGETEXT',NULL,'N','Y',341,30,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (285,'Функция','Function','REF_pm_FunctionId','',NULL,'Y',22,38,'2010-06-06 18:05:05','2010-06-06 18:05:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (286,'Определять порядок следования задач','IsTasksDepend','CHAR','Y',NULL,'Y',36,250,'2010-06-06 18:05:05','2010-06-06 18:05:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1414,'Функция','Feature','REF_pm_FunctionId',NULL,'Y','Y',341,20,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1413,'Продукт','Competitor','REF_pm_CompetitorId',NULL,'Y','Y',341,10,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1412,'Описание','Description','LARGETEXT',NULL,'N','Y',340,20,'2011-02-21 21:08:35','2011-02-21 21:08:35',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1411,'Название','Caption','VARCHAR',NULL,'Y','Y',340,10,'2011-02-21 21:08:35','2011-02-21 21:08:35',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (292,'Закреплять ответственных за высокоуровневыми функциями','IsResponsibleForFunctions','CHAR','Y',NULL,'Y',36,160,'2010-06-06 18:05:06','2010-06-06 18:05:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (293,'Может участвовать в нескольких проектах','IsShared','CHAR','Y',NULL,'N',63,65,'2010-06-06 18:05:06','2010-06-06 18:05:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (294,'Использовать перекрестную проверку задач','IsCrossChecking','CHAR','Y',NULL,'Y',36,260,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (295,'Использовать фазу проектирования','IsDesign','CHAR','N',NULL,'Y',36,80,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (296,'Разрешать отклонения от методологии','IsHighTolerance','CHAR','N',NULL,'Y',36,210,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1410,'Автор','Author','REF_cms_UserId',NULL,'Y','N',337,60,'2011-02-21 21:08:34','2011-02-21 21:08:34',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (298,'Пользователь','User','INTEGER','','Y',NULL,65,10,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (299,'Настройка','Settings','TEXT','','Y',NULL,65,20,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (300,'Значение','Value','TEXT','','Y',NULL,65,30,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (302,'Является администратором','IsAdmin','CHAR','N',NULL,'N',63,67,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (303,'Запланировано','IsPlanned','CHAR','N',NULL,NULL,11,80,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (304,'Запланировано','IsPlanned','CHAR','N',NULL,NULL,12,70,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (305,'Имя файла','FileName','TEXT','',NULL,NULL,61,30,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (306,'Имя файла протокола','LogFileName','TEXT','',NULL,NULL,61,40,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (307,'Пользователь','UserId','TEXT','','Y','Y',66,10,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (308,'URL','URL','LARGETEXT','','Y','Y',66,20,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (309,'IP адрес','Caption','VARCHAR','','Y','Y',67,10,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (310,'Страна','Country','TEXT','','Y','Y',67,20,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (311,'Город','City','TEXT','','Y','Y',67,30,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (312,'Среднее время проверки выполненной задачи, ч.','VerificationTime','INTEGER','1','Y','Y',36,270,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (313,'Принимает участие в проекте','IsActive','CHAR','Y','Y',NULL,3,70,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (314,'Название','Caption','VARCHAR','','Y','Y',68,10,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (315,'Описание','Description','TEXT','','Y','Y',68,20,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (316,'Ссылка (RSS)','RssLink','TEXT','','Y','Y',68,30,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (317,'Язык','Language','REF_cms_LanguageId','','Y','Y',68,40,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (318,'Публичный','IsPublic','CHAR','','N','Y',68,50,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (319,'Заголовок','Caption','VARCHAR','','Y','Y',69,10,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (320,'Описание','Description','TEXT','','Y','Y',69,20,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (321,'Ссылка','HtmlLink','TEXT','','Y','Y',69,30,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (322,'Новостной канал','NewsChannel','REF_pm_NewsChannelId','','Y',NULL,69,40,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (323,'Новостной канал','NewsChannel','REF_pm_NewsChannelId','','Y','Y',70,10,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (324,'Проект','Project','REF_pm_ProjectId','','Y',NULL,70,20,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1378,'Редактор содержимого','ContentEditor','TEXT','','Y','N',9,140,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (327,'Заголовок (Английский)','HeaderEn','LARGETEXT','',NULL,'Y',28,80,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (328,'Окончание (Английский)','FooterEn','LARGETEXT','',NULL,'Y',28,90,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (329,'Адресат','ToParticipant','REF_pm_ParticipantId','','Y','Y',72,10,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (330,'Тема','Subject','LARGETEXT','',NULL,'Y',72,20,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (331,'Содержание','Content','LARGETEXT','',NULL,'Y',72,30,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (332,'Отправитель','FromParticipant','REF_pm_ParticipantId','','Y','Y',72,40,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (333,'Skype','Skype','TEXT','',NULL,'N',3,26,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (334,'Skype','Skype','TEXT','',NULL,'Y',63,45,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1377,'Редактор документов','WikiEditorClass','TEXT','WikiSyntaxEditor','Y','Y',5,250,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1376,'Описание','Description','TEXT',NULL,'N','Y',75,60,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (337,'Тестовый сценарий','TestScenario','REF_TestScenarioId','','Y','Y',74,10,'2010-06-06 18:05:14','2010-06-06 18:05:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1307,'Проект','Target','REF_pm_ProjectId',NULL,'Y','Y',316,10,'2010-10-01 17:16:24','2010-10-01 17:16:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (340,'Тест','Test','REF_pm_TestId','','Y','Y',75,10,'2010-06-06 18:05:14','2010-06-06 18:05:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (341,'Тестовый случай','TestCase','REF_WikiPageId','','Y','Y',75,20,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (342,'Успешный результат','Success','CHAR','',NULL,'Y',75,30,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (343,'Тестировал','Tester','REF_pm_ParticipantId','','Y','Y',75,40,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (345,'Тест','Test','REF_pm_TestId','',NULL,NULL,11,90,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (346,'Сборка','Build','REF_pm_BuildId','N',NULL,'Y',11,28,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (347,'Релиз','Release','REF_pm_ReleaseId','','Y','Y',11,27,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (349,'Требуется утверждение пожеланий','RequestApproveRequired','CHAR','Y',NULL,'Y',36,130,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (351,'Что я сделал вчера?','WasYesterday','RICHTEXT','','Y','Y',76,10,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (352,'Что я планирую сделать сегодня?','WhatToday','RICHTEXT','','Y','Y',76,20,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (353,'Текущие проблемы','CurrentProblems','RICHTEXT','','Y','Y',76,30,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (354,'Участник','Participant','REF_pm_ParticipantId','','Y',NULL,76,40,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (355,'Использовать ежедневные митинги','UseScrums','CHAR','N','N','Y',36,70,'2010-06-06 18:05:16','2010-10-01 17:16:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (356,'Название','Caption','VARCHAR','','Y','Y',77,10,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (357,'Описание','Description','RICHTEXT','',NULL,'Y',77,20,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (358,'Окружение','Environment','REF_pm_EnvironmentId','',NULL,'Y',74,35,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (360,'Используется несколько окружений','UseEnvironments','CHAR','N',NULL,'Y',36,170,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (362,'Файл','File','FILE','','Y','Y',78,10,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (363,'Описание','Description','LARGETEXT','',NULL,'Y',78,20,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (364,'Объект','ObjectId','INTEGER','','Y',NULL,78,30,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (365,'Класс объект','ObjectClass','TEXT','','Y',NULL,78,40,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (367,'Проверка тестового случая','TestCaseExecution','REF_pm_TestCaseExecutionId','',NULL,NULL,11,90,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (368,'Проверка тестового случая','TestCaseExecution','REF_pm_TestCaseExecutionId','',NULL,NULL,12,80,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (369,'Используется управление вехами проекта','HasMilestones','CHAR','N',NULL,'N',36,20,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (370,'Дата','MilestoneDate','DATE','','Y','Y',79,10,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (371,'Название','Caption','VARCHAR','','Y','Y',79,20,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (372,'Описание','Description','RICHTEXT','',NULL,'Y',79,30,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (373,'Пройдена','Passed','CHAR','N',NULL,'Y',79,40,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (374,'Митинг','Meeting','REF_pm_MeetingId','','Y','Y',80,10,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (375,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',80,20,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1267,'Использовать планирование релизов','IsReleasesUsed','VARCHAR','Y','N','Y',36,17,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (377,'Релиз','Release','REF_pm_ReleaseId','','Y','Y',81,10,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (378,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId','','Y','Y',81,20,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (379,'Заметка','Content','LARGETEXT','',NULL,'Y',81,30,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (380,'Название','Caption','VARCHAR','',NULL,'Y',82,10,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (381,'Задача','Task','REF_pm_TaskId','','N','Y',82,20,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (382,'Участник','Participant','REF_cms_UserId','','N','Y',82,30,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (383,'Описание','Description','TEXT','','N','Y',82,30,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (384,'Завершена','Completed','CHAR','','N','Y',82,50,'2010-06-06 18:05:21','2010-06-06 18:05:21','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1375,'Описание','Description','LARGETEXT',NULL,'N','Y',63,110,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1374,'Описание','Description','LARGETEXT',NULL,'N','Y',6,15,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1426,'Значение','ResourceValue','TEXT',NULL,'Y','Y',344,20,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1425,'Ключ','ResourceKey','TEXT',NULL,'Y','Y',344,10,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (391,'Пользовательское поле 2','UserField2','TEXT','0','Y','N',9,90,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (392,'Название','Caption','VARCHAR','','Y','Y',85,10,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (393,'Исходное пожелание','SourceRequest','REF_pm_ChangeRequestId','','Y','Y',86,10,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (394,'Целевое пожелание','TargetRequest','REF_pm_ChangeRequestId','','Y','Y',86,20,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (395,'Тип связи','LinkType','REF_pm_ChangeRequestLinkTypeId','','Y','Y',86,30,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (398,'Проект закрыт','IsClosed','CHAR','N',NULL,'Y',5,105,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (399,'Трудоемкость','Estimation','FLOAT','',NULL,'Y',22,32,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (400,'Оценка трудоемкости','RequestEstimationRequired','VARCHAR','EstimationStoryPointsStrategy','Y','Y',36,218,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (401,'Исполнитель','Owner','REF_cms_UserId','','N','N',22,45,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (402,'Владелец','Owner','REF_pm_ParticipantId','',NULL,NULL,55,20,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (403,'Название','Caption','VARCHAR','','Y','Y',87,10,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (404,'Ссылочное имя','ReferenceName','VARCHAR','','Y','Y',87,20,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (405,'Тип','Type','REF_pm_IssueTypeId','',NULL,'Y',22,22,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1373,'Описание','Description','LARGETEXT',NULL,'N','Y',20,15,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (407,'В архиве','IsArchived','CHAR','N','Y',NULL,8,70,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (408,'Черновой вариант','IsDraft','CHAR','N','N','Y',14,18,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (409,'Причина изменения даты','ReasonToChangeDate','RICHTEXT','',NULL,'Y',79,50,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (410,'Результат завершения','CompleteResult','RICHTEXT','',NULL,'Y',79,60,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (411,'Название','Caption','VARCHAR','','Y','Y',88,10,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (412,'Описание','Description','LARGETEXT','',NULL,'Y',88,20,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (413,'Название','Caption','VARCHAR','','Y','Y',89,10,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (414,'Варианты ответов','Answers','LARGETEXT','',NULL,'Y',89,20,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (415,'Является разделом','IsSection','CHAR','N',NULL,NULL,89,30,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (416,'Опрос','Poll','REF_pm_PollId','','Y',NULL,89,40,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (417,'Опрос','Poll','REF_pm_PollId','','Y','Y',90,10,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (418,'Пользователь','User','REF_cms_UserId','','Y','Y',90,20,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (419,'Позиция опросника','PollItem','REF_pm_PollItemId','','Y','Y',91,10,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (420,'Результат опроса','PollResult','REF_pm_PollResultId','','Y','Y',91,20,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (421,'Ответ','Answer','LARGETEXT','','Y','Y',91,30,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (422,'Текущий','IsCurrent','CHAR','Y','Y','Y',90,30,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (423,'Дата завершения','CommitDate','DATE','',NULL,NULL,90,40,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (424,'Источник','SourceName','TEXT','','Y','Y',92,10,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (425,'Класс','ClassName','TEXT','','Y','Y',92,20,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (426,'Сущность','EntityName','TEXT','','Y','Y',92,30,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (427,'Старый идентификатор','OldObjectId','INTEGER','','Y','Y',92,40,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (428,'Новый идентификатор','NewObjectId','INTEGER','','Y','Y',92,50,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (429,'Сущность','EntityName','TEXT','','Y','Y',93,10,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (430,'Объект','ObjectId','INTEGER','','Y','Y',93,20,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (431,'Ссылка','ReferenceUrl','TEXT',NULL,'Y','Y',94,10,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (432,'Название источника','ServerName','TEXT',NULL,'Y','Y',94,20,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (433,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',94,30,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (434,'Цель','Caption','VARCHAR',NULL,'Y','Y',95,10,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (435,'Дата','Deadline','DATE',NULL,'Y','Y',95,20,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (436,'Комментарий','Comment','LARGETEXT',NULL,NULL,'Y',95,30,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (437,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId',NULL,'N',NULL,95,40,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (438,'Повестка','Agenda','RICHTEXT',NULL,NULL,'Y',18,15,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (439,'Время','MeetingTime','TEXT',NULL,NULL,'Y',18,40,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (440,'Accepted','Подтверждено','CHAR','N',NULL,'Y',19,40,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (441,'Отклонено','Rejected','CHAR','N',NULL,'Y',19,50,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (442,'Причина отклонения','RejectReason','LARGETEXT',NULL,NULL,'Y',19,60,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (443,'Подтверждено','Accepted','CHAR','N','Y','Y',80,30,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (444,'Отклонено','Rejected','FLOAT','N','Y','Y',80,40,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (445,'Причина отклонения','RejectReason','FLOAT',NULL,NULL,'Y',80,50,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (446,'Сущность','EntityName','TEXT',NULL,'Y','Y',96,10,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (447,'Идентификатор','ObjectId','FLOAT',NULL,'Y','Y',96,20,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1006,'Активирован','IsActivated','CHAR','N','Y','N',63,90,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1007,'Тема','Subject','TEXT',NULL,'Y','Y',201,10,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (451,'Релиз','Release','REF_pm_ReleaseId',NULL,'Y','Y',97,10,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (452,'Дата сбора метрик','SnapshotDate','DATE',NULL,'Y','Y',97,20,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (453,'Загрузка задачами','Workload','INTEGER',NULL,'Y','Y',97,30,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (454,'Остаточная трудоемкость','LeftWorkload','INTEGER',NULL,'Y','Y',97,40,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (455,'Прошло дней','SnapshotDays','INTEGER',NULL,'Y','Y',97,50,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (456,'Планируемая загрузка','PlannedWorkload','INTEGER',NULL,'Y','Y',97,60,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (457,'Участники выбирают себе задачи самостоятельно','IsParticipantsTakeTasks','CHAR','N','N','Y',36,230,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (458,'Публичность','Access','TEXT',NULL,'Y','Y',23,110,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (459,'Язык','Language','REF_cms_LanguageId',NULL,'Y','Y',63,35,'2010-06-06 18:05:30','2010-06-06 18:05:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (460,'Использовать декомпозицию по функциям','UseFunctionalDecomposition','CHAR','Y','N','Y',36,150,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (461,'Класс объекта','EntityName','TEXT',NULL,'Y','Y',98,10,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (462,'Идентификатор объекта','ObjectId','INTEGER',NULL,'Y','Y',98,20,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (463,'Активна','IsActive','CHAR','N','Y','Y',98,30,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (464,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',98,40,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (465,'Затрачено, ч.','Capacity','FLOAT',NULL,'Y','Y',82,20,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (466,'Оставшаяся трудоемкость, ч.','LeftWork','FLOAT',NULL,'N','Y',15,75,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (467,'В архиве','IsArchived','CHAR','N','Y','N',9,100,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (468,'Пользовательское поле 3','UserField3','TEXT',NULL,'Y','N',9,110,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (469,'Название','Caption','VARCHAR',NULL,'Y','Y',99,10,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (470,'Название','Caption','VARCHAR','','Y','Y',100,10,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (471,'Описание','Description','RICHTEXT','',NULL,'Y',100,20,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (472,'Стили (CSS)','CSSBlock','LARGETEXT','',NULL,'Y',100,30,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (473,'Верхний колонтитул','Header','LARGETEXT','',NULL,'Y',100,40,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (474,'Нижний колонтитул','Footer','LARGETEXT','',NULL,'Y',100,50,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (475,'С оглавлением в начале страницы','HeaderContents','CHAR','Y',NULL,'Y',100,60,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (476,'Нумеровать разделы','SectionNumbers','CHAR','Y',NULL,'Y',100,70,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (477,'Отслеживать сроки выполнения задач и реализации пожеланий','IsDeadlineUsed','CHAR','N','N','Y',36,240,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1266,'Использовать управление версиями','IsVersionsUsed','CHAR','Y','N','Y',36,20,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (479,'Релиз','PlannedRelease','REF_pm_VersionId',NULL,'N','N',22,150,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (480,'Назначение','Caption','VARCHAR',NULL,'Y','Y',101,10,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (481,'Код','CodeName','TEXT',NULL,'Y','Y',101,20,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (482,'Адрес подписчика','Caption','VARCHAR',NULL,'Y','Y',102,10,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (483,'Рассылка','Notification','REF_cms_EmailNotificationId',NULL,'Y','Y',102,20,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (484,'Состояние подписки','IsActive','CHAR','Y','Y','Y',102,30,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (485,'Текст рассылки (Wiki)','Content','LARGETEXT',NULL,'N','Y',101,30,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (486,'Пользователь','cms_UserId','INTEGER',NULL,'N','Y',46,30,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (487,'Ежедневная загрузка, ч.','Capacity','FLOAT','0','Y','Y',4,15,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (488,'Название','Caption','VARCHAR',NULL,'Y','Y',103,10,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (489,'Девиз','Tagline','LARGETEXT',NULL,'Y','Y',103,20,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (490,'Описание','Description','LARGETEXT',NULL,'Y','Y',103,30,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (491,'Автор','Author','REF_cms_UserId',NULL,'Y','N',103,40,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (492,'Команда','Team','REF_co_TeamId',NULL,'Y','Y',104,10,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (493,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',104,20,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (494,'Роли в команде','TeamRoles','LARGETEXT',NULL,'Y','Y',104,30,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (495,'Тип','LicenseType','TEXT',NULL,'Y','Y',105,10,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (496,'Значение','LicenseValue','TEXT',NULL,'Y','N',105,20,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (497,'Ключ','LicenseKey','TEXT',NULL,'Y','N',105,30,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (498,'Название','Caption','VARCHAR',NULL,'Y','Y',106,10,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (499,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',106,20,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (500,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',107,10,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (501,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',107,20,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (502,'Публиковать документацию','IsPublicDocumentation','CHAR','N','N','Y',47,70,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (503,'Публиковать артефакты','IsPublicArtefacts','CHAR','N','N','Y',47,80,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (504,'Роль в проекте','Caption','VARCHAR',NULL,'Y','Y',108,10,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (505,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',108,20,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (506,'Активна','IsActive','CHAR','Y','Y','Y',108,30,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (507,'Требуемая занятость, часов в день','RequiredWorkload','INTEGER','8','Y','Y',108,40,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (508,'Оплата часа работы','PriceOfHour','TEXT','0','Y','Y',108,50,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (509,'Описание','Description','RICHTEXT',NULL,'Y','Y',108,60,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (510,'Дополнительные требования','Requirements','RICHTEXT',NULL,'N','Y',108,70,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (511,'Фотография','Photo','IMAGE',NULL,'N','N',63,80,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1034,'Кодовое значение','CodeName','TEXT',NULL,'Y','Y',208,20,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1033,'Название','Caption','VARCHAR',NULL,'Y','Y',208,10,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (514,'Название','Caption','VARCHAR',NULL,'Y','Y',109,10,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (515,'Название','Caption','VARCHAR',NULL,'Y','Y',110,10,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (516,'Категория','Category','REF_co_ServiceCategoryId',NULL,'Y','Y',110,20,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (517,'Описание','Description','LARGETEXT',NULL,'Y','Y',110,30,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (518,'Стоимость','Cost','LARGETEXT',NULL,'Y','Y',110,40,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (519,'Автор','Author','REF_cms_UserId',NULL,'N','N',110,50,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (520,'Команда','Team','REF_co_TeamId',NULL,'N','N',110,60,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (521,'Услуга','Service','REF_co_ServiceId',NULL,'Y','Y',111,10,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (522,'Заказчик','Customer','REF_cms_UserId',NULL,'Y','Y',111,20,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (523,'Отзыв','Response','RICHTEXT',NULL,'N','Y',111,30,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (524,'Закрыта','IsClosed','CHAR','N','Y','Y',111,40,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (525,'Пользователь','SystemUser','REF_cms_UserId',NULL,'N','N',30,80,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (527,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',112,10,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (528,'Путь к репозиторию','SVNPath','VARCHAR',NULL,'Y','Y',112,20,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (529,'Имя пользователя','LoginName','VARCHAR',NULL,'N','Y',112,30,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (530,'Пароль','SVNPassword','PASSWORD',NULL,'N','Y',112,40,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1264,'Назначаются встречи с участниками проекта','HasMeetings','CHAR','N','N','Y',5,210,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (532,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',113,10,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (533,'Версия','Version','TEXT','0','Y','Y',113,20,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (534,'Описание','Description','LARGETEXT',NULL,'N','Y',113,30,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (536,'Проект','Project','REF_pm_ProjectId',NULL,'Y','N',114,20,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (537,'Стоимость','Cost','TEXT',NULL,'N','Y',114,30,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (538,'Максимальное время реализации, дней','Duration','INTEGER','1','Y','Y',114,40,'2010-06-06 18:05:39','2010-06-06 18:05:48','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (540,'Комментарий','Comment','RICHTEXT',NULL,'N','Y',114,50,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (541,'Пожелание','IssueOutsourcing','REF_co_IssueOutsourcingId',NULL,'Y','N',115,10,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (542,'Стоимость','Cost','LARGETEXT',NULL,'Y','Y',115,20,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (543,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','N',115,30,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (544,'Комментарий','Comment','RICHTEXT',NULL,'N','Y',115,40,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (545,'Принято','IsAccepted','CHAR','N','Y','Y',115,50,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (546,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',116,10,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (547,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',116,20,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (548,'Адресат','Addressee','TEXT',NULL,'Y','Y',116,30,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (549,'Название','Caption','VARCHAR',NULL,'Y','Y',117,10,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (550,'Идентификатор','ObjectId','INTEGER',NULL,'Y','Y',117,20,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (551,'Сущность','EntityRefName','TEXT',NULL,'Y','Y',117,30,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (552,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',118,10,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (553,'Загрузка артефакта','DownloadAction','REF_pm_DownloadActionId',NULL,'Y','Y',118,20,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (554,'Название','Caption','VARCHAR',NULL,'Y','Y',119,10,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (555,'Название','Caption','VARCHAR',NULL,'Y','Y',120,10,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (556,'Совет','Advise','RICHTEXT',NULL,'Y','Y',120,20,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (557,'Тематика','Theme','REF_co_AdviseThemeId',NULL,'Y','Y',120,30,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (558,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',120,40,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (559,'Утвержден','IsApproved','CHAR','N','Y','Y',120,50,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (560,'Участник','Participant','REF_pm_ParticipantId',NULL,'Y','Y',121,10,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (561,'Итерация','Iteration','REF_pm_ReleaseId',NULL,'Y','Y',121,20,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (562,'Метрика','Metric','TEXT',NULL,'Y','Y',121,30,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (563,'Значение метрики','MetricValue','FLOAT',NULL,'Y','Y',121,40,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (564,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',122,10,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (565,'Номер','Caption','VARCHAR',NULL,'Y','Y',122,20,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (566,'Название','Caption','VARCHAR',NULL,'Y','Y',123,10,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (567,'Сумма','Volume','FLOAT',NULL,'Y','Y',123,20,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (568,'Комментарий','Comment','LARGETEXT',NULL,'Y','Y',123,30,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (569,'Счет','Bill','REF_co_BillId',NULL,'Y','Y',123,40,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (570,'Периодичность оплаты, дн.','Period','INTEGER',NULL,'Y','Y',126,30,'2010-06-06 18:05:44','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (571,'Класс','ObjectClass','TEXT',NULL,'Y','Y',124,20,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (572,'Рейтинг','Rating','INTEGER',NULL,'Y','Y',124,30,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (573,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',125,10,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (574,'Адрес','IPAddress','TEXT',NULL,'Y','Y',125,20,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (575,'Рейтинг','Rating','REF_co_RatingId',NULL,'Y','Y',125,30,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (576,'Название','Caption','VARCHAR',NULL,'Y','Y',126,10,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (577,'Стоимость','Cost','FLOAT',NULL,'Y','Y',126,20,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (578,'Периодичность','Period','INTEGER',NULL,'Y','Y',126,30,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (579,'Условия','Conditions','RICHTEXT',NULL,'Y','Y',126,40,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (580,'Опция','Option','REF_co_OptionId',NULL,'Y','Y',127,10,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (581,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',127,20,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (582,'Активна','IsActive','CHAR','Y','Y','Y',127,30,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (583,'Кодовое название','CodeName','TEXT',NULL,'Y','Y',126,50,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (584,'Оплачена','IsPayed','CHAR','N','Y','Y',127,40,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (585,'Дата оплаты','PaymentDate','DATE',NULL,'Y','Y',127,50,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (586,'Итерация','Iteration','REF_pm_ReleaseId',NULL,'Y','Y',128,10,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (587,'Метрика','Metric','TEXT',NULL,'Y','Y',128,20,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (588,'Значение метрики','MetricValue','FLOAT',NULL,'Y','Y',128,30,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (589,'Релиз','Version','REF_pm_VersionId',NULL,'Y','Y',129,10,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (590,'Метрика','Metric','TEXT',NULL,'Y','Y',129,20,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (591,'Значение метрики','MetricValue','FLOAT',NULL,'Y','Y',129,30,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (592,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',130,10,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (593,'IP','IPAddress','TEXT',NULL,'Y','Y',130,20,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (594,'Причина блокировки','BlockReason','TEXT',NULL,'Y','Y',130,30,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (595,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',131,10,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (596,'Количество попыток','RetryAmount','INTEGER','1','Y','Y',131,20,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (597,'Вопрос на русском','QuestionRussian','TEXT',NULL,'Y','Y',132,10,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (598,'Вопрос на английском','QuestionEnglish','TEXT',NULL,'Y','Y',132,20,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (599,'Ответ на русском','Answer','TEXT',NULL,'Y','Y',132,30,'2010-06-06 18:05:47','2010-06-06 18:05:48','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (600,'Ответ на английском','AnswerEnglish','TEXT',NULL,'Y','Y',132,40,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1000,'Черновик','IsDraft','CHAR','N','Y','N',9,120,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1001,'Принимает участие','IsActive','CHAR','Y','Y','Y',104,40,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1002,'Использовать планирование задач','IsPlanningUsed','CHAR','Y','Y','N',36,10,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1003,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',23,120,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1004,'Сообщение блога','BlogPost','REF_BlogPostId',NULL,'Y','Y',200,10,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1005,'Тэг','Tag','REF_TagId',NULL,'Y','Y',200,20,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1008,'Текст сообщения','Content','LARGETEXT',NULL,'Y','Y',201,20,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1009,'Автор','Author','REF_cms_UserId',NULL,'Y','N',201,30,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1010,'Получатель пользователь','ToUser','REF_cms_UserId',NULL,'N','Y',201,40,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1011,'Получатель команда','ToTeam','REF_co_TeamId',NULL,'N','Y',201,50,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1012,'Вид поиска','SearchKind','TEXT',NULL,'Y','Y',202,10,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1013,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','Y',202,20,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1014,'Результат','Result','LARGETEXT',NULL,'Y','Y',202,30,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1263,'Проект по администрированию','AdminProject','REF_pm_ProjectId',NULL,'N','N',62,70,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1016,'Язык проекта','Language','TEXT',NULL,'Y','Y',23,37,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1017,'Условия поиска','Conditions','LARGETEXT',NULL,'Y','Y',202,40,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1018,'Рейтинг','Rating','FLOAT','0','Y','N',63,100,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1019,'Рейтинг','Rating','FLOAT','0','Y','Y',103,50,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1020,'Рейтинг','Rating','FLOAT','0','Y','N',5,170,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1021,'Содержание','Content','RICHTEXT',NULL,'Y','Y',203,10,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1022,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',203,20,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1023,'Название','Caption','VARCHAR',NULL,'Y','Y',204,10,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1024,'Статус','TeamState','REF_co_TeamStateId','1','Y','N',103,60,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1025,'Описание','Description','TEXT',NULL,'Y','Y',204,20,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1026,'Название','Caption','VARCHAR',NULL,'Y','Y',205,10,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1027,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',206,10,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1028,'Роль','CommunityRole','REF_co_CommunityRoleId',NULL,'Y','Y',206,20,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1029,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',207,10,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1030,'Стоимость часа','HourCost','TEXT','0','Y','Y',207,20,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1031,'Профессиональные навыки','Skills','LARGETEXT',NULL,'Y','Y',207,30,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1032,'Владение инструментами','Tools','LARGETEXT',NULL,'Y','Y',207,40,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1035,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',209,10,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1036,'Использовать бюджетирование в проекте','IsBugetUsed','CHAR','Y','N','Y',209,20,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1037,'Валюта проекта','Currency','REF_pm_CurrencyId','1','Y','Y',209,30,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1038,'Название','Caption','VARCHAR',NULL,'Y','Y',210,10,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1039,'Модель оплаты','PaymentModel','REF_pm_PaymentModelId','1','Y','Y',209,40,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1040,'Скрывать стоимость работ участников','HideParticipantsCost','CHAR','N','N','Y',209,50,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1041,'Оплата','Salary','FLOAT','0','Y','Y',3,80,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1042,'Название','Caption','VARCHAR',NULL,'Y','Y',211,10,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1043,'Название','Caption','VARCHAR',NULL,'Y','Y',212,10,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1044,'Название проекта','Caption','VARCHAR',NULL,'Y','Y',213,10,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1045,'Краткое описание','Description','RICHTEXT',NULL,'Y','Y',213,20,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1046,'Тип','Kind','REF_co_TenderKindId','1','Y','Y',213,30,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1047,'Состояние','State','REF_co_TenderStateId','1','Y','N',213,40,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1048,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','N',213,50,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1049,'Тендер','Tender','REF_co_TenderId',NULL,'Y','Y',214,10,'2010-06-06 18:05:56','2010-06-06 18:05:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1050,'Файл','Attachment','FILE',NULL,'Y','Y',214,20,'2010-06-06 18:05:56','2010-06-06 18:05:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1051,'Название','Caption','VARCHAR',NULL,'Y','Y',215,10,'2010-06-06 18:05:56','2010-06-06 18:05:56','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1052,'Тендер','Tender','REF_co_TenderId',NULL,'Y','Y',216,10,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1053,'Команда','Team','REF_co_TeamId',NULL,'Y','Y',216,20,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1054,'Состояние','State','REF_co_TenderParticipanceStateId','1','Y','Y',216,30,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1055,'Название задачи','Caption','VARCHAR',NULL,'Y','Y',217,10,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1056,'Параметры','Parameters','LARGETEXT',NULL,'Y','Y',217,20,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1057,'Почтовый ящик','MailboxClass','TEXT','mailbox','Y','Y',45,40,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1058,'Проект','Project','REF_pm_ProjectId',NULL,'N','Y',216,40,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1059,'Является тендером','IsTender','CHAR','N','Y','N',5,180,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1060,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',213,60,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1061,'text(1374)','AdminEmail','TEXT','','N','Y',62,40,'2010-06-06 18:05:58','2010-06-06 18:05:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1062,'Погрешность оценки, %','InitialEstimationError','INTEGER',NULL,'N','N',39,50,'2010-06-06 18:05:58','2010-06-06 18:05:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1063,'Процент ошибок, %','InitialBugsInWorkload','INTEGER',NULL,'N','N',39,60,'2010-06-06 18:05:58','2010-06-06 18:05:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1065,'Пользовательское поле 3','UserField3','TEXT',NULL,'N','N',9,130,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1200,'Окружение','Environment','REF_pm_EnvironmentId',NULL,'N','N',22,27,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1201,'Название','Caption','VARCHAR',NULL,'Y','Y',300,10,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1202,'Описание','Description','RICHTEXT',NULL,'N','Y',300,20,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1203,'Тестовый набор','TestSuite','REF_WikiPageId',NULL,'Y','Y',301,10,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1204,'Тестировщик','Assignee','REF_pm_ParticipantId',NULL,'N','Y',301,20,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1205,'Планируемая трудоемкость','Planned','FLOAT',NULL,'N','Y',301,30,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1206,'Тест план','TestPlan','REF_pm_TestPlanId',NULL,'Y','N',301,40,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1207,'Участники отчитываются о затраченном времени','IsReportsOnActivities','CHAR','N','N','Y',36,220,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1208,'Дата','ReportDate','DATE',NULL,'Y','Y',82,10,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1209,'Разрешать изменение логина и пароля пользователя','AllowToChangeLogin','CHAR','Y','N','Y',62,50,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1210,'Обнаружено в','SubmittedVersion','VARCHAR',NULL,'N','Y',22,50,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1211,'Заказчик выполняет приемку пожеланий','CustomerAcceptsIssues','CHAR','N','N','N',36,310,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1212,'Закреплять ответственного за пожеланием','IsResponsibleForIssue','CHAR','Y','N','Y',36,140,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1214,'Выполнено в','ClosedInVersion','VARCHAR',NULL,'N','N',22,115,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1215,'Название','Caption','VARCHAR',NULL,'Y','Y',302,10,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1216,'Имя файла','FileName','TEXT',NULL,'Y','Y',302,20,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1217,'MIME тип','MimeType','TEXT',NULL,'Y','Y',302,30,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1218,'Файл','File','FILE',NULL,'Y','Y',302,40,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1219,'Название','Caption','VARCHAR',NULL,'Y','Y',303,10,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1220,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','Y',303,20,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1221,'Снимок','Snapshot','REF_cms_SnapshotId',NULL,'Y','Y',304,10,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1222,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',304,20,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1223,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',304,30,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1224,'Элемент снимка','SnapshotItem','REF_cms_SnapshotItemId',NULL,'Y','Y',305,10,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1225,'Название атрибута','Caption','VARCHAR',NULL,'Y','Y',305,20,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1226,'Ссылочное имя атрибута','ReferenceName','VARCHAR',NULL,'Y','Y',305,30,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1227,'Значение атрибута','Value','TEXT',NULL,'Y','Y',305,40,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1228,'Отображается на сайте','IsDisplayedOnSite','CHAR','N','N','N',7,20,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1229,'Версия','Version','VARCHAR',NULL,'N','Y',8,80,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1230,'Релиз','Version','REF_pm_VersionId',NULL,'N','N',49,50,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1231,'Актуальна','IsActual','CHAR','Y','N','N',49,60,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1232,'Актуальна','IsActual','CHAR','Y','N','N',14,70,'2010-06-06 18:06:09','2010-06-06 18:06:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1233,'Актуален','IsActual','CHAR','Y','N','N',39,70,'2010-06-06 18:06:09','2010-06-06 18:06:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1542,'Пользователь','SystemUser','REF_cms_UserId',NULL,'N','N',363,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1235,'Требуется авторизация для скачивания','IsAuthorizedDownload','CHAR','N','N','N',8,32,'2010-06-06 18:06:09','2010-06-06 18:06:09','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1409,'Переход','Transition','REF_pm_TransitionId',NULL,'N','N',337,50,'2011-02-21 21:08:33','2011-02-21 21:08:33',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1237,'Проектная роль','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',306,10,'2010-06-06 18:06:10','2010-06-06 18:06:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1238,'Объект','ReferenceName','VARCHAR',NULL,'Y','Y',306,20,'2010-06-06 18:06:10','2010-06-06 18:06:10','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1239,'Тип объекта','ReferenceType','TEXT',NULL,'Y','Y',306,30,'2010-06-06 18:06:11','2010-06-06 18:06:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1240,'Доступ','AccessType','TEXT',NULL,'Y','Y',306,40,'2010-06-06 18:06:11','2010-06-06 18:06:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1241,'Кодовое название','ReferenceName','VARCHAR',NULL,'N','Y',6,30,'2010-06-06 18:06:11','2010-06-06 18:06:11','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1242,'Путь к файлам','RootPath','VARCHAR',NULL,'N','Y',112,25,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1243,'Автор','Author','TEXT',NULL,'Y','Y',113,40,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1244,'Дата','CommitDate','TEXT',NULL,'Y','Y',113,50,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1245,'Приложение','Application','TEXT',NULL,'N','Y',49,70,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1246,'Дата начала','StartDate','DATE',NULL,'Y','Y',39,25,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1247,'Дата окончания','FinishDate','DATE',NULL,'N','Y',39,30,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1248,'Публичный','IsPublic','CHAR','N','N','N',88,30,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1249,'Хеш пользователя','AnonymousHash','TEXT',NULL,'N','N',90,50,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1250,'Хеш значение','HashKey','TEXT',NULL,'Y','Y',307,10,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1251,'Идентификаторы','Ids','RICHTEXT',NULL,'Y','Y',307,20,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1252,'Версия','Version','REF_pm_VersionId',NULL,'Y','Y',308,10,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1253,'Дата метрики','SnapshotDate','DATE',NULL,'Y','Y',308,20,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1254,'Фактическая загрузка','Workload','FLOAT',NULL,'Y','Y',308,30,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1255,'Прошло дней','SnapshotDays','INTEGER',NULL,'Y','Y',308,40,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1256,'Плановая загрузка','PlannedWorkload','FLOAT',NULL,'Y','Y',308,50,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1257,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',309,10,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1258,'Включать номер релиза','UseRelease','CHAR','Y','Y','Y',309,20,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1259,'Включать номер итерации','UseIteration','CHAR','Y','Y','Y',309,30,'2010-06-06 18:06:15','2010-06-06 18:06:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1260,'Включать номер сборки','UseBuild','CHAR','Y','Y','Y',309,40,'2010-06-06 18:06:15','2010-06-06 18:06:15','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1261,'text(sourcecontrol8)','IsSubversionUsed','CHAR','Y','N','N',5,190,'2010-10-01 17:15:57','2010-10-01 17:15:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1262,'Выкладывать файлы проекта','IsFileServer','CHAR','Y','N','N',5,200,'2010-10-01 17:15:57','2010-10-01 17:15:57','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1265,'Проводятся опросы мнений участников','IsPollUsed','CHAR','Y','N','Y',5,220,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1268,'Сообщение блога','BlogPost','REF_BlogPostId',NULL,'Y','Y',310,10,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1269,'Содержание','Content','LARGETEXT',NULL,'Y','Y',310,20,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1270,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','Y',310,30,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1271,'Ид объекта','ObjectId','INTEGER',NULL,'Y','N',311,10,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1272,'Класс объекта','ObjectClass','TEXT',NULL,'Y','N',311,20,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1273,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',311,30,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1274,'Адрес электронной почты','Email','TEXT',NULL,'Y','Y',311,40,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1275,'Тип','Kind','TEXT',NULL,'Y','Y',312,10,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1276,'Дата начала','StartDate','DATE',NULL,'Y','Y',312,20,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1277,'Дата окончания','FinishDate','DATE',NULL,'Y','Y',312,30,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1278,'Год','IntervalYear','INTEGER',NULL,'Y','Y',312,40,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1279,'Месяц','IntervalMonth','INTEGER',NULL,'Y','Y',312,50,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1280,'День','IntervalDay','INTEGER',NULL,'Y','Y',312,60,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1281,'Название','Caption','VARCHAR',NULL,'Y','Y',312,70,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1282,'Квартал','IntervalQuarter','INTEGER',NULL,'Y','Y',312,80,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1283,'Неделя','IntervalWeek','INTEGER',NULL,'Y','Y',312,90,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1284,'Базовая роль','ProjectRoleBase','REF_pm_ProjectRoleId',NULL,'Y','N',6,40,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1285,'Почтовый сервер','HostAddress','TEXT',NULL,'Y','Y',313,10,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1286,'Порт сервера','PortServer','INTEGER','110','Y','Y',313,20,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1287,'Почтовый ящик','EmailAddress','TEXT',NULL,'Y','Y',313,30,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1288,'Пароль на почтовый ящик','EmailPassword','PASSWORD',NULL,'Y','Y',313,40,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1289,'Использовать SSL/TLS','UseSSL','CHAR','N','N','Y',313,50,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1290,'Использовать режим отладки','UseDebug','CHAR','N','N','Y',313,60,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1291,'Связанный проект','Project','REF_pm_ProjectId',NULL,'Y','Y',313,45,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1292,'Назначение','Caption','VARCHAR',NULL,'Y','Y',313,5,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1293,'Активен','IsActive','CHAR','Y','N','Y',313,70,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1294,'В проекте используется база знаний','IsKnowledgeUsed','CHAR','Y','N','Y',5,230,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1295,'Ведется блог проекта','IsBlogUsed','CHAR','Y','N','Y',5,240,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1296,'Назначение','Caption','VARCHAR',NULL,'Y','Y',314,10,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1297,'Класс','ClassName','TEXT',NULL,'Y','Y',314,20,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1298,'Минуты','Minutes','VARCHAR','*','Y','Y',314,30,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1299,'Часы','Hours','VARCHAR','*','Y','Y',314,40,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1300,'Дни месяца','Days','VARCHAR','*','Y','Y',314,50,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1301,'Дни недели','WeekDays','VARCHAR','*','Y','Y',314,60,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1302,'Задание активно','IsActive','CHAR','Y','N','Y',314,70,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1303,'Задание','ScheduledJob','REF_co_ScheduledJobId',NULL,'Y','Y',315,10,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1304,'Результат','Result','LARGETEXT',NULL,'Y','Y',315,20,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1305,'Выполнено','IsCompleted','CHAR','N','Y','Y',315,30,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1311,'Требования','Requirements','INTEGER','0','Y','N',316,50,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1312,'Тестовая документация','Testing','INTEGER','0','Y','N',316,60,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1313,'Справочная документация','HelpFiles','INTEGER','0','Y','N',316,70,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1314,'Файлы','Files','INTEGER','0','Y','N',316,80,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1315,'Категория','ReferenceName','VARCHAR',NULL,'N','Y',20,30,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1316,'Роль в проекте','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',20,40,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1317,'Базовый тип','ParentTaskType','REF_pm_TaskTypeId',NULL,'Y','Y',20,50,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1318,'Используется при планировании','UsedInPlanning','CHAR','Y','N','Y',20,60,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1319,'Класс','ObjectClass','TEXT',NULL,'Y','Y',317,10,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1320,'Объект','ObjectId','INTEGER',NULL,'Y','Y',317,20,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1321,'Роль в проекте','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',317,30,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1322,'Доступ','AccessType','TEXT',NULL,'Y','Y',317,40,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1323,'Исходный код','SourceCode','INTEGER','0','Y','N',316,90,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1325,'Описание','Description','LARGETEXT',NULL,'N','Y',318,20,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1326,'Входит в группу','ParentGroup','REF_co_ProjectGroupId',NULL,'N','Y',318,30,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1327,'Группа','ProjectGroup','REF_co_ProjectGroupId',NULL,'Y','Y',319,10,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1328,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',319,20,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1329,'Название','Caption','VARCHAR',NULL,'Y','Y',320,10,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1330,'Описание','Description','LARGETEXT',NULL,'N','Y',320,20,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1331,'Входит в группу','ParentGroup','REF_co_UserGroupId',NULL,'N','Y',320,30,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1332,'Группа','UserGroup','REF_co_UserGroupId',NULL,'Y','Y',321,10,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1333,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',321,20,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1334,'Название','Caption','VARCHAR',NULL,'Y','Y',322,10,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1335,'Название таблицы','ReferenceName','VARCHAR',NULL,'Y','Y',322,20,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1336,'Пакет','packageId','REF_packageId',NULL,'Y','Y',322,30,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1337,'Экземпляры упорядочены','IsOrdered','CHAR',NULL,'N','Y',322,40,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1338,'Является справочником','IsDictionary','CHAR',NULL,'N','Y',322,50,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1340,'Право доступа','AccessType','TEXT',NULL,'Y','Y',323,40,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1341,'Объект','ReferenceName','VARCHAR',NULL,'Y','Y',323,30,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1342,'Тип объекта','ReferenceType','TEXT',NULL,'Y','Y',323,20,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1343,'Название','Caption','VARCHAR',NULL,'Y','Y',324,10,'2010-10-01 17:16:28','2010-10-01 17:16:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1344,'Веха','Milestone','REF_pm_MilestoneId',NULL,'N','N',95,50,'2010-10-01 17:16:28','2010-10-01 17:16:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1345,'Название','Caption','VARCHAR',NULL,'Y','Y',325,10,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1346,'Описание','Description','LARGETEXT',NULL,'N','Y',325,20,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1347,'Имя файла','FileName','TEXT',NULL,'Y','Y',325,30,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1348,'Используется по умолчанию','IsDefault','CHAR','N','N','Y',325,40,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1349,'Язык шаблона','Language','REF_cms_LanguageId',NULL,'Y','Y',325,35,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1350,'Название','Caption','VARCHAR',NULL,'Y','Y',326,10,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1351,'Описание','Description','LARGETEXT',NULL,'N','Y',326,20,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1352,'Стадия процесса','ProjectStage','REF_pm_ProjectStageId',NULL,'N','Y',14,80,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1353,'Тип задачи','TaskType','REF_pm_TaskTypeId',NULL,'Y','Y',327,10,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1354,'Стадия процесса','ProjectStage','REF_pm_ProjectStageId',NULL,'Y','Y',327,20,'2010-10-01 17:16:30','2010-10-01 17:16:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1355,'Пожелания и функции','Requests','INTEGER','0','Y','Y',316,25,'2010-10-01 17:16:30','2010-10-01 17:16:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1356,'Название','Caption','VARCHAR',NULL,'Y','Y',328,10,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1357,'Ссылочное имя','ReferenceName','VARCHAR',NULL,'Y','Y',328,20,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1358,'Результат','Result','REF_pm_TestExecutionResultId',NULL,'Y','Y',75,50,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1359,'Результат','Result','REF_pm_TestExecutionResultId',NULL,'Y','Y',74,60,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1360,'Название','Caption','VARCHAR',NULL,'Y','Y',329,10,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1361,'Описание','Description','TEXT',NULL,'Y','Y',329,20,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1362,'URL','Url','TEXT',NULL,'Y','Y',329,30,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1363,'Название','Caption','VARCHAR',NULL,'Y','Y',330,10,'2010-11-01 21:19:04','2010-11-01 21:19:04',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1364,'Категория','Category','REF_cms_ReportCategoryId',NULL,'Y','Y',329,40,'2010-11-01 21:19:04','2010-11-01 21:19:04',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1365,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId',NULL,'Y','Y',331,10,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1366,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',331,20,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1367,'Класс','ObjectClass','TEXT',NULL,'Y','Y',331,30,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1368,'Актуальна','IsActual','CHAR','Y','Y','Y',331,40,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1369,'Задача','Task','REF_pm_TaskId',NULL,'Y','Y',332,10,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1370,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',332,20,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1371,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',332,30,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1372,'Актуальна','IsActual','CHAR','Y','Y','Y',332,40,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1379,'Редактор содержимого','ContentEditor','TEXT','','Y','N',41,140,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1380,'Тэг','Tag','REF_TagId',NULL,'Y','Y',333,10,'2011-02-21 21:08:26','2011-02-21 21:08:26',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1381,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',333,20,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1382,'Класс объекта','ObjectClass','VARCHAR',NULL,'Y','Y',333,30,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1383,'Название','Caption','VARCHAR',NULL,'Y','Y',334,10,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1384,'Описание','Description','LARGETEXT',NULL,'N','Y',334,20,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1385,'Важность','Importance','REF_pm_ImportanceId',NULL,'N','Y',64,25,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1386,'Название','Caption','VARCHAR',NULL,'Y','Y',335,10,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1387,'Описание','Description','LARGETEXT',NULL,'N','Y',335,20,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1388,'Сущность','ObjectClass','TEXT',NULL,'Y','N',335,30,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1389,'Является терминальным','IsTerminal','CHAR','N','N','Y',335,40,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1390,'Название','Caption','VARCHAR',NULL,'Y','Y',336,10,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1391,'Описание','Description','LARGETEXT',NULL,'N','Y',336,20,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1392,'Исходное состояние','SourceState','REF_pm_StateId',NULL,'Y','N',336,30,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1393,'Целевое состояние','TargetState','REF_pm_StateId',NULL,'Y','Y',336,40,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1394,'Необходимо указать причину перехода','IsReasonRequired','CHAR','N','N','Y',336,50,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1395,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',337,10,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1396,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',337,20,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1397,'Состояние','State','REF_pm_StateId',NULL,'Y','Y',337,30,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1398,'Кодовое имя','ReferenceName','VARCHAR',NULL,'Y','Y',335,25,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1399,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','Y',338,10,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1400,'Роль','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',338,20,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1401,'Состояние','State','TEXT',NULL,'N','N',203,30,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1402,'Состояние','State','TEXT',NULL,'N','N',15,110,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1403,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','N',339,10,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1404,'Атрибут','ReferenceName','VARCHAR',NULL,'Y','Y',339,20,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1405,'Сущность','Entity','LARGETEXT',NULL,'Y','N',339,30,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1406,'Комментарий','Comment','LARGETEXT',NULL,'N','N',337,40,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1408,'Состояние','State','TEXT',NULL,'N','N',22,35,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1417,'Название','Caption','VARCHAR',NULL,'Y','Y',342,10,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1418,'Описание','Description','LARGETEXT',NULL,'N','Y',342,20,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1419,'Ссылочное имя','ReferenceName','VARCHAR',NULL,'Y','Y',342,30,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1420,'text(937)','DefaultPageTemplate','REF_WikiPageId',NULL,'N','Y',342,40,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1421,'Тип страницы','PageType','REF_WikiPageTypeId',NULL,'N','N',9,160,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1422,'Исходная страницы','SourcePage','REF_WikiPageId',NULL,'Y','Y',343,10,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1423,'Целевая страница','TargetPage','REF_WikiPageId',NULL,'Y','Y',343,20,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1424,'Связь актуальна','IsActual','CHAR','Y','Y','Y',343,30,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1431,'Параметры','Url','TEXT',NULL,'Y','Y',345,30,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1432,'Описание','Description','LARGETEXT',NULL,'N','Y',345,40,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1433,'Добавить в избранное','IsHandAccess','CHAR','N','N','Y',345,35,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1434,'Автор','Author','REF_cms_UserId',NULL,'Y','N',345,50,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1435,'Название','Caption','VARCHAR',NULL,'Y','Y',346,10,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1436,'Параметры','Url','TEXT',NULL,'Y','Y',346,20,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1437,'Описание','Description','LARGETEXT',NULL,'N','Y',346,30,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1438,'Автор','Author','REF_cms_UserId',NULL,'Y','N',346,40,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1439,'Базовый отчет','ReportBase','TEXT',NULL,'N','Y',345,25,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1440,'Базовый отчет','ReportBase','TEXT',NULL,'N','Y',346,15,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1441,'Настройка','Setting','TEXT',NULL,'Y','Y',347,10,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1442,'Значение','Value','TEXT',NULL,'Y','Y',347,20,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1443,'Участник','Participant','REF_pm_ParticipantId',NULL,'Y','Y',347,30,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1444,'Краткое название','ShortCaption','VARCHAR',NULL,'N','Y',342,15,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1445,'Сущность','ObjectClass','TEXT',NULL,'Y','Y',348,10,'2011-06-15 08:01:38','2011-06-15 08:01:38',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1446,'Атрибут','ObjectAttribute','TEXT',NULL,'Y','Y',348,20,'2011-06-15 08:01:38','2011-06-15 08:01:38',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1447,'Значение атрибута','AttributeValue','TEXT',NULL,'Y','Y',348,30,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1448,'Идентификаторы объектов','ObjectIds','LARGETEXT',NULL,'Y','Y',348,40,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1449,'Начальная скорость','InitialVelocity','INTEGER',NULL,'Y','Y',39,80,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1450,'Начальная скорость','InitialVelocity','INTEGER',NULL,'Y','Y',14,55,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1451,'text(1024)','DaysInWeek','INTEGER','5','Y','Y',5,260,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1452,'Количество объектов','QueueLength','INTEGER',NULL,'N','N',335,50,'2011-08-13 18:29:27','2011-08-13 18:29:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1453,'text(kanban14)','IsKanbanUsed','CHAR','N','N','N',36,320,'2011-08-13 18:29:27','2011-08-13 18:29:27',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1454,'Описание','Caption','LARGETEXT',NULL,'Y','Y',349,10,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1455,'Состояние','State','TEXT',NULL,'N','N',349,20,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1456,'Описание','Caption','LARGETEXT',NULL,'Y','Y',350,10,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1457,'Цель','Aim','REF_sm_AimId',NULL,'Y','N',350,20,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1458,'Состояние','State','TEXT',NULL,'N','N',350,30,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1459,'Описание','Caption','LARGETEXT',NULL,'Y','Y',351,10,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1460,'Активность','Activity','REF_sm_ActivityId',NULL,'Y','N',351,20,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1461,'Состояние','State','TEXT',NULL,'N','N',351,30,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1462,'Имя','Caption','VARCHAR',NULL,'Y','Y',352,10,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1463,'Состояние','State','TEXT',NULL,'N','N',352,20,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1464,'Персона','Person','REF_sm_PersonId',NULL,'Y','N',349,30,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1465,'Оценка','Estimation','FLOAT','0','Y','Y',351,40,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1466,'Описание','Description','LARGETEXT',NULL,'N','Y',352,30,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1467,'Ценности','Valuable','LARGETEXT',NULL,'N','Y',352,40,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1468,'Проблемы','Problems','LARGETEXT',NULL,'N','Y',352,50,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1469,'Фотография','Photo','IMAGE',NULL,'N','Y',352,60,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1470,'Тип действия','Kind','INTEGER','0','Y','N',351,50,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1471,'text(storymapping10)','IsStoryMappingUsed','CHAR','N','N','N',36,330,'2011-08-13 18:29:31','2011-08-13 18:29:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1472,'Оценка','Estimation','FLOAT','0','Y','Y',350,40,'2011-08-13 18:29:31','2011-08-13 18:29:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1473,'text(1033)','IsRequestOrderUsed','CHAR','N','N','Y',36,125,'2011-08-13 18:29:31','2011-08-13 18:29:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1474,'Идентификатор LDAP','LDAPUID','TEXT',NULL,'N','N',63,120,'2011-08-26 11:12:00','2011-08-26 11:12:00',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1475,'Идентификатор LDAP','LDAPUID','TEXT',NULL,'N','N',318,40,'2011-08-26 11:12:16','2011-08-26 11:12:16',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1476,'Идентификатор LDAP','LDAPUID','TEXT',NULL,'N','N',320,40,'2011-08-26 11:12:23','2011-08-26 11:12:23',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1478,'Редактор страниц','WikiEditor','TEXT',NULL,'Y','Y',342,50,'2011-08-26 11:12:28','2011-08-26 11:12:28',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1479,'text(1059)','ConnectorClass','VARCHAR',NULL,'Y','Y',112,5,'2011-09-15 10:26:43','2011-09-15 10:26:43',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1480,'Продолжительность цикла, ч.','LifecycleDuration','INTEGER',NULL,'N','N',22,160,'2011-10-28 21:46:02','2011-10-28 21:46:02',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1481,'Количество объектов','TotalCount','INTEGER',NULL,'Y','Y',348,50,'2011-10-28 21:46:02','2011-10-28 21:46:02',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1482,'Дата начала','StartDate','DATE',NULL,'N','N',22,170,'2011-12-09 08:01:29','2011-12-09 08:01:29',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1483,'Дата окончания','FinishDate','DATE',NULL,'N','N',22,180,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1484,'Дата начала','StartDate','DATE',NULL,'N','N',15,120,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1485,'Дата окончания','FinishDate','DATE',NULL,'N','N',15,130,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1486,'Название','Caption','VARCHAR',NULL,'Y','Y',353,10,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1487,'Ссылочное имя','ReferenceName','VARCHAR',NULL,'Y','Y',353,20,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1488,'Сущность','EntityReferenceName','VARCHAR',NULL,'Y','Y',353,30,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1489,'Тип','AttributeType','INTEGER',NULL,'Y','Y',353,40,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1490,'Значение по умолчанию','DefaultValue','VARCHAR',NULL,'N','Y',353,50,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1491,'Видимо на форме','IsVisible','CHAR','Y','Y','Y',353,60,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1492,'Пользовательский атрибут','CustomAttribute','REF_pm_CustomAttributeId',NULL,'Y','N',354,10,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1493,'Идентификатор объекта','ObjectId','INTEGER',NULL,'Y','N',354,20,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1494,'Значение: число','IntegerValue','INTEGER',NULL,'N','N',354,30,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1495,'Значение: текст','StringValue','TEXT',NULL,'N','N',354,40,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1496,'Значение: текст','TextValue','LARGETEXT',NULL,'N','N',354,50,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1497,'Варианты значений','ValueRange','LARGETEXT',NULL,'N','N',353,45,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1498,'Обязательно для заполнения','IsRequired','CHAR','N','N','Y',353,70,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1499,'text(1365)','TaskEstimationUsed','CHAR','Y','Y','Y',36,215,'2011-12-09 08:01:55','2011-12-09 08:01:55',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1500,'Дискриминатор','ObjectKind','TEXT',NULL,'N','N',353,80,'2011-12-09 08:01:55','2011-12-09 08:01:55',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1501,'Название','Caption','VARCHAR',NULL,'Y','Y',355,10,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1502,'Описание','Description','LARGETEXT',NULL,'N','Y',355,20,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1503,'Результат проверки','CheckResult','CHAR','N','Y','Y',355,30,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1504,'Включена','IsEnabled','CHAR','Y','N','N',355,40,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1505,'Значение','Value','TEXT',NULL,'N','N',355,50,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1506,'Список','ListName','TEXT',NULL,'Y','Y',303,30,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1507,'Название','Caption','VARCHAR',NULL,'Y','Y',356,10,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1508,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','Y',357,10,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1509,'Предикат','Predicate','REF_pm_PredicateId',NULL,'Y','Y',357,20,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1510,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','Y',358,10,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1511,'Атрибут','ReferenceName','VARCHAR',NULL,'Y','Y',358,20,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1512,'Сущность','Entity','TEXT',NULL,'N','N',358,30,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1513,'Репозиторий','Repository','REF_pm_SubversionId',NULL,'Y','N',113,60,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1514,'Краткое название','Caption','VARCHAR',NULL,'N','Y',112,27,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1515,'Описание','Description','LARGETEXT',NULL,'N','Y',353,55,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1516,'Значение: пароль','PasswordValue','PASSWORD',NULL,'N','N',354,60,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1517,'Итерация','Iteration','REF_pm_ReleaseId',NULL,'N','N',82,80,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1518,'Название','Caption','VARCHAR',NULL,'Y','Y',359,10,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1519,'Протокол','ProtocolName','TEXT',NULL,'Y','N',359,20,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1520,'Способ подключения','MailboxProvider','REF_co_MailboxProviderId','1','Y','Y',313,18,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1521,'Оставшаяся трудоемкость','EstimationLeft','FLOAT',NULL,'N','N',22,135,'2012-03-20 07:59:19','2012-03-20 07:59:19',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1522,'Название','Caption','VARCHAR',NULL,'Y','Y',360,10,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1523,'Ссылочное имя','ReferenceName','VARCHAR',NULL,'Y','Y',360,20,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1524,'Название','Caption','VARCHAR',NULL,'N','Y',361,10,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1525,'Ссылочное имя','ReferenceName','VARCHAR',NULL,'Y','Y',361,20,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1526,'Состояние','State','REF_pm_StateId',NULL,'Y','Y',361,30,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1528,'Тип задачи','TaskType','REF_pm_TaskTypeId',NULL,'N','N',97,70,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1529,'text(ee207)','Releases','INTEGER','0','Y','Y',316,27,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1530,'Итерации и задачи','Tasks','INTEGER','0','Y','Y',316,28,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1531,'Продолжительность','Duration','FLOAT','0','N','N',337,70,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1532,'Напомнить','RememberInterval','INTEGER','0','N','Y',18,50,'2012-10-05 07:51:40','2012-10-05 07:51:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1533,'Напомнить','RememberInterval','INTEGER','0','N','N',19,70,'2012-10-05 07:51:40','2012-10-05 07:51:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1534,'Напомнить','RememberInterval','INTEGER','0','Y','N',80,60,'2012-10-05 07:51:40','2012-10-05 07:51:40',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1535,'Редакция','ProductEdition','TEXT',NULL,'N','N',325,50,'2015-03-03 16:38:08','2015-03-03 16:38:08',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1536,'Функция','Feature','REF_pm_FunctionId',NULL,'N','N',362,10,'2015-03-03 16:38:08','2015-03-03 16:38:08',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1537,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',362,20,'2015-03-03 16:38:08','2015-03-03 16:38:08',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1538,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',362,30,'2015-03-03 16:38:08','2015-03-03 16:38:08',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1539,'Версия','VersionNum','INTEGER','0','Y','Y',113,21,NULL,NULL,NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1543,'UID','UID','TEXT',NULL,'N','N',364,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1544,'Название','Caption','VARCHAR',NULL,'N','N',364,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1545,'Функциональная область','Workspace','REF_pm_WorkspaceId',NULL,'N','N',364,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1546,'UID','UID','TEXT',NULL,'N','N',365,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1547,'Название','Caption','VARCHAR',NULL,'N','N',365,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1548,'Отчет','ReportUID','TEXT',NULL,'N','N',365,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1549,'Модуль','ModuleUID','TEXT',NULL,'N','N',365,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1550,'Меню','WorkspaceMenu','REF_pm_WorkspaceMenuId',NULL,'N','N',365,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1551,'Имя класса','ClassName','VARCHAR',NULL,'N','N',30,10,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1552,'Ссылочное имя страницы','PageReferenceName','VARCHAR',NULL,'Y','N',342,300,'2015-03-03 16:38:09','2015-03-03 16:38:09',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1590,'Изменение','ObjectChangeLogId','REF_ObjectChangeLogId',NULL,'Y','Y',369,10,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1589,'Модуль','Module','VARCHAR',NULL,'N','N',345,20,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1553,'Адрес отправителя','SenderAddress','VARCHAR',NULL,'N','Y',313,43,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1554,'Путь к родительской странице','ParentPath','TEXT',NULL,'N','N',9,0,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1555,'Номер раздела','SectionNumber','VARCHAR',NULL,'N','N',9,0,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1556,'Цвет','RelatedColor','COLOR',NULL,'N','Y',17,20,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1557,'Цвет','RelatedColor','COLOR',NULL,'N','Y',87,30,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1558,'Цвет','RelatedColor','COLOR',NULL,'N','Y',335,35,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1559,'Ид объекта','ObjectId','TEXT',NULL,'N','N',303,5,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1560,'Класс объекта','ObjectClass','TEXT',NULL,'N','N',303,6,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1561,'Ревизия','Baseline','REF_cms_SnapshotId',NULL,'N','N',343,40,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1562,'Тип','Type','TEXT',NULL,'N','N',303,50,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1563,'Описание','Description','LARGETEXT',NULL,'N','Y',303,60,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1564,'Тип','Type','TEXT',NULL,'N','N',343,40,'2015-03-03 16:38:10','2015-03-03 16:38:10',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1565,'text(1716)','IsTasks','CHAR',NULL,'N','N',36,15,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1566,'MetricValueDate','MetricValueDate','DATETIME',NULL,'N','N',129,15,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1567,'MetricValueDate','MetricValueDate','DATETIME',NULL,'N','N',128,15,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1568,'Краткое название','ShortCaption','VARCHAR',NULL,'N','Y',20,13,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1569,'Причина рассинхронизации','UnsyncReasonType','VARCHAR',NULL,'N','N',343,100,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1570,'Временная зона','Timezone','VARCHAR',NULL,'N','N',26,100,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1571,'Состояние','State','REF_pm_StateId',NULL,'Y','N',366,10,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1572,'Атрибут','ReferenceName','VARCHAR',NULL,'Y','Y',366,20,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1573,'Сущность','Entity','VARCHAR',NULL,'Y','N',366,30,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1574,'Видимо на форме','IsVisible','CHAR','Y','N','Y',366,40,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1575,'Обязательно для заполнения','IsRequired','CHAR','N','N','Y',366,50,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1576,'Тип связи','Type','VARCHAR',NULL,'N','N',331,100,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1577,'Иконка','Icon','VARCHAR',NULL,'N','N',363,100,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1578,'Ссылочное имя','ReferenceName','VARCHAR',NULL,'Y','Y',330,100,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1579,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',367,10,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1580,'Подключение','Connector','REF_pm_SubversionId',NULL,'Y','N',367,20,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1581,'Имя пользователя','UserName','VARCHAR',NULL,'Y','Y',367,30,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1582,'Пароль','UserPassword','PASSWORD',NULL,'N','Y',367,40,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1583,'Имя класса ссылки','AttributeTypeClassName','VARCHAR',NULL,'N','N',353,130,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1584,'Мощность','Capacity','INTEGER',NULL,'N','N',353,140,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1585,'Объект','ObjectId','INTEGER',NULL,'Y','Y',368,10,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1586,'Класс','ObjectClass','VARCHAR',NULL,'Y','Y',368,20,'2015-03-03 16:38:11','2015-03-03 16:38:11',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1587,'Цвет','RelatedColor','COLOR',NULL,'N','Y',20,20,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1588,'Обратное название','BackwardCaption','VARCHAR',NULL,'Y','Y',85,15,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1591,'Атрибуты','Attributes','TEXT',NULL,'Y','Y',369,10,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1592,'Тип шаблона','Kind','VARCHAR','case','Y','N',325,100,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1593,'text(1876)','IsDefault','CHAR','N','N','Y',20,70,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1594,'Название','Caption','VARCHAR',NULL,'Y','Y',370,10,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1595,'Системное имя','ReferenceName','VARCHAR',NULL,'Y','Y',370,20,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1596,'Описание','Description','TEXT',NULL,'N','Y',370,30,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1597,'text(1916)','HasIssues','CHAR','Y','N','Y',370,25,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1598,'text(1918)','ChildrenLevels','VARCHAR',NULL,'N','Y',370,27,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1599,'Уровень','Type','REF_pm_FeatureTypeId',NULL,'N','Y',64,22,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1600,'Родительская функция','ParentFeature','REF_pm_FunctionId',NULL,'N','Y',64,30,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1601,'Трудоемкость','Estimation','INTEGER',NULL,'N','N',64,100,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1602,'Осталось','Workload','INTEGER',NULL,'N','N',64,110,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1603,'Дата начала','StartDate','DATE',NULL,'N','N',64,120,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1604,'Дата завершения','DeliveryDate','DATE',NULL,'N','N',64,130,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1605,'Оставшаяся трудоемкость','EstimationLeft','INTEGER',NULL,'N','N',64,140,'2015-03-03 16:38:12','2015-03-03 16:38:12',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1606,'Оценка окончания','DeliveryDate','DATE',NULL,'N','N',22,185,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1607,'Содержание','Content','WYSIWYG',NULL,'N','Y',75,140,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1608,'Название','Caption','VARCHAR',NULL,'Y','Y',371,10,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1609,'Домены','Domains','VARCHAR',NULL,'N','Y',371,20,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1610,'Описание','Description','TEXT',NULL,'N','Y',371,30,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1611,'text(support22)','CanSeeCompanyIssues','CHAR',NULL,'N','Y',371,25,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1612,'Название','username','VARCHAR',NULL,'Y','Y',372,10,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1613,'Email','email','VARCHAR',NULL,'Y','Y',372,20,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1614,'Компания','Company','REF_CompanyId',NULL,'Y','Y',372,30,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1615,'Название (системное)','username_canonical','VARCHAR',NULL,'N','N',372,10,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1616,'Email (системный)','email_canonical','VARCHAR',NULL,'N','N',372,20,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1617,'Компания','Company','REF_CompanyId',NULL,'N','Y',373,10,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1618,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',373,20,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1619,'Оплачено до','PayedTill','DATE',NULL,'Y','Y',110,20,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `BlogPostFile`;
CREATE TABLE `BlogPostFile` (
  `BlogPostFileId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `ContentMime` mediumtext,
  `ContentPath` mediumtext,
  `ContentExt` varchar(255) DEFAULT NULL,
  `BlogPost` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`BlogPostFileId`),
  KEY `BlogPost` (`BlogPost`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `BlogPostFile` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `businessfunction`;
CREATE TABLE `businessfunction` (
  `businessfunctionId` int(11) NOT NULL AUTO_INCREMENT,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `packageId` int(11) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Description` mediumtext,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`businessfunctionId`),
  UNIQUE KEY `XPKbusinessfunction` (`businessfunctionId`),
  KEY `businessfunction_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `businessfunction` WRITE;
INSERT INTO `businessfunction` (`businessfunctionId`, `Caption`, `ReferenceName`, `packageId`, `OrderNum`, `Description`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (1,'Статистика использования проектов','ProjectUseStat',2,10,'','2006-01-09 17:11:58','2006-01-09 17:11:58','');
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Backup`;
CREATE TABLE `cms_Backup` (
  `cms_BackupId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `BackupFileName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_BackupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Backup` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_BatchJob`;
CREATE TABLE `cms_BatchJob` (
  `cms_BatchJobId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Parameters` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_BatchJobId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_BatchJob` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_BlackList`;
CREATE TABLE `cms_BlackList` (
  `cms_BlackListId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `BlockReason` mediumtext,
  `IPAddress` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_BlackListId`),
  KEY `i$7` (`SystemUser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_BlackList` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_BrowserTransitionLog`;
CREATE TABLE `cms_BrowserTransitionLog` (
  `cms_BrowserTransitionLogId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `UserId` mediumtext,
  `URL` mediumtext,
  PRIMARY KEY (`cms_BrowserTransitionLogId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_BrowserTransitionLog` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_CheckQuestion`;
CREATE TABLE `cms_CheckQuestion` (
  `cms_CheckQuestionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `QuestionRussian` mediumtext,
  `QuestionEnglish` mediumtext,
  `Answer` mediumtext,
  `AnswerEnglish` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_CheckQuestionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_CheckQuestion` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Checkpoint`;
CREATE TABLE `cms_Checkpoint` (
  `cms_CheckpointId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `CheckResult` char(1) DEFAULT NULL,
  `IsEnabled` char(1) DEFAULT NULL,
  `Value` mediumtext,
  PRIMARY KEY (`cms_CheckpointId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Checkpoint` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_ClientInfo`;
CREATE TABLE `cms_ClientInfo` (
  `cms_ClientInfoId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `Country` mediumtext,
  `City` mediumtext,
  PRIMARY KEY (`cms_ClientInfoId`),
  KEY `Caption` (`Caption`(20))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_ClientInfo` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_DeletedObject`;
CREATE TABLE `cms_DeletedObject` (
  `cms_DeletedObjectId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `EntityName` mediumtext,
  `ObjectId` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_DeletedObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_DeletedObject` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_EmailNotification`;
CREATE TABLE `cms_EmailNotification` (
  `cms_EmailNotificationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `CodeName` mediumtext,
  `Content` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_EmailNotificationId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_EmailNotification` WRITE;
INSERT INTO `cms_EmailNotification` (`cms_EmailNotificationId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `Content`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:36','2010-06-06 18:05:36','',30,'Объвление о вакансиях','VacancyNotification',NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_EntityCluster_Delete`;
CREATE TABLE `cms_EntityCluster_Delete` (
  `cms_EntityClusterId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `ObjectAttribute` varchar(32) DEFAULT NULL,
  `AttributeValue` varchar(128) DEFAULT NULL,
  `ObjectIds` longtext,
  `TotalCount` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_EntityClusterId`),
  KEY `I$cms_EntityCluster$RecordModified` (`RecordModified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_EntityCluster_Delete` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_ExternalUser`;
CREATE TABLE `cms_ExternalUser` (
  `username` varchar(255) NOT NULL,
  `username_canonical` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_canonical` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `cms_ExternalUserId` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`cms_ExternalUserId`),
  UNIQUE KEY `UNIQ_59F2E2C792FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_59F2E2C7A0D96FBF` (`email_canonical`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_ExternalUser` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_IdsHash`;
CREATE TABLE `cms_IdsHash` (
  `cms_IdsHashId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `HashKey` varchar(32) DEFAULT NULL,
  `Ids` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_IdsHashId`),
  KEY `I$52` (`HashKey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_IdsHash` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Language`;
CREATE TABLE `cms_Language` (
  `cms_LanguageId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `CodeName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_LanguageId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Language` WRITE;
INSERT INTO `cms_Language` (`cms_LanguageId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `RecordVersion`) VALUES (1,'2006-03-21 23:30:31','2006-03-21 23:30:31','',10,'Русский','RU',0);
INSERT INTO `cms_Language` (`cms_LanguageId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `RecordVersion`) VALUES (2,'2006-03-21 23:30:44','2006-03-21 23:30:44','',20,'Английский','EN',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_License`;
CREATE TABLE `cms_License` (
  `cms_LicenseId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `LicenseType` mediumtext,
  `LicenseValue` mediumtext,
  `LicenseKey` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_LicenseId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_License` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Link`;
CREATE TABLE `cms_Link` (
  `cms_LinkId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Category` int(11) DEFAULT NULL,
  `IsPublished` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_LinkId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Link` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_LinkCategory`;
CREATE TABLE `cms_LinkCategory` (
  `cms_LinkCategoryId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_LinkCategoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_LinkCategory` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_LoginRetry`;
CREATE TABLE `cms_LoginRetry` (
  `cms_LoginRetryId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `RetryAmount` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_LoginRetryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_LoginRetry` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_MainMenu`;
CREATE TABLE `cms_MainMenu` (
  `cms_MainMenuId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_MainMenuId`),
  UNIQUE KEY `XPKcms_MainMenu` (`cms_MainMenuId`),
  KEY `cms_MainMenu_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30))
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_MainMenu` WRITE;
INSERT INTO `cms_MainMenu` (`cms_MainMenuId`, `OrderNum`, `Caption`, `ReferenceName`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1,10,'Вертикальное меню','Vertical',NULL,'2005-12-22 21:20:20',NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_NotificationSubscription`;
CREATE TABLE `cms_NotificationSubscription` (
  `cms_NotificationSubscriptionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Notification` int(11) DEFAULT NULL,
  `IsActive` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_NotificationSubscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_NotificationSubscription` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Page`;
CREATE TABLE `cms_Page` (
  `cms_PageId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `PHPFile` mediumtext,
  `Menu` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `HelpId` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_PageId`),
  UNIQUE KEY `XPKcms_Page` (`cms_PageId`),
  KEY `cms_Page_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30),`Menu`),
  KEY `Menu` (`Menu`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Page` WRITE;
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (1,10,'Релизы.','Project','project.php',1,NULL,'2010-06-06 18:05:48',NULL,42,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (2,70,'Участники.','Participants','participants.php',1,NULL,'2006-01-12 23:49:18',NULL,44,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (3,47,'Требования.','Requirements','requirements.php',1,NULL,'2006-03-20 23:02:27',NULL,52,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (4,20,'Пожелания.','Requests','requests.php',1,NULL,'2006-01-12 23:49:06',NULL,43,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (5,45,'Мои задачи.','Tasks','tasks.php',1,NULL,'2010-06-06 18:06:13',NULL,50,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (6,40,'Итерации.','Planning','planning.php',1,NULL,'2010-06-06 18:05:48',NULL,53,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (7,60,'Файлы.','Artefacts','artefacts.php',1,NULL,'2010-06-06 18:06:10',NULL,45,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (8,5,'Проект.','Main','index.php',1,NULL,'2010-06-06 18:05:48',NULL,41,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (9,55,'Документация.','Help','helpfiles.php',1,'2006-01-09 22:41:18','2006-01-13 10:02:31','',51,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (10,50,'Тестирование.','Testing','testing.php',1,'2010-06-06 18:05:14','2010-06-06 18:05:14','',NULL,0);
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (12,15,'Функции.','Feature','functions.php',1,NULL,NULL,NULL,NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_PluginModule`;
CREATE TABLE `cms_PluginModule` (
  `cms_PluginModuleId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_PluginModuleId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_PluginModule` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_RemapObject`;
CREATE TABLE `cms_RemapObject` (
  `cms_RemapObjectId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `EntityName` mediumtext,
  `ObjectId` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_RemapObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_RemapObject` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Report`;
CREATE TABLE `cms_Report` (
  `cms_ReportId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Url` mediumtext,
  `Category` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_ReportId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Report` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_ReportCategory`;
CREATE TABLE `cms_ReportCategory` (
  `cms_ReportCategoryId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `ReferenceName` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`cms_ReportCategoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_ReportCategory` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_SerializedObject`;
CREATE TABLE `cms_SerializedObject` (
  `cms_SerializedObjectId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SourceName` mediumtext,
  `ClassName` mediumtext,
  `EntityName` mediumtext,
  `OldObjectId` int(11) DEFAULT NULL,
  `NewObjectId` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_SerializedObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_SerializedObject` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_SynchronizationSource`;
CREATE TABLE `cms_SynchronizationSource` (
  `cms_SynchronizationSourceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ReferenceUrl` mediumtext,
  `ServerName` mediumtext,
  `Author` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_SynchronizationSourceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_SynchronizationSource` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_SystemSettings`;
CREATE TABLE `cms_SystemSettings` (
  `cms_SystemSettingsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Language` int(11) DEFAULT NULL,
  `AdminEmail` mediumtext,
  `AllowToChangeLogin` char(1) DEFAULT NULL,
  `DisplayFeedbackForm` char(1) DEFAULT NULL,
  `AdminProject` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_SystemSettingsId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_SystemSettings` WRITE;
INSERT INTO `cms_SystemSettings` (`cms_SystemSettingsId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Language`, `AdminEmail`, `AllowToChangeLogin`, `DisplayFeedbackForm`, `AdminProject`, `RecordVersion`) VALUES (1,NULL,NULL,NULL,NULL,'DEVPROM',1,NULL,'Y','N',NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_TempFile`;
CREATE TABLE `cms_TempFile` (
  `cms_TempFileId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `FileName` mediumtext,
  `MimeType` mediumtext,
  `FileExt` varchar(32) DEFAULT NULL,
  `FilePath` mediumtext,
  `FileMime` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_TempFileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_TempFile` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Update`;
CREATE TABLE `cms_Update` (
  `cms_UpdateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `FileName` mediumtext,
  `LogFileName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_UpdateId`),
  KEY `i$8` (`RecordCreated`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Update` WRITE;
INSERT INTO `cms_Update` (`cms_UpdateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `LogFileName`, `RecordVersion`) VALUES (15,NULL,NULL,NULL,NULL,'3.0',NULL,NULL,NULL,0);
INSERT INTO `cms_Update` (`cms_UpdateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `LogFileName`, `RecordVersion`) VALUES (16,'2015-03-03 16:38:13','2015-03-03 16:38:13',NULL,NULL,'',NULL,NULL,NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_User`;
CREATE TABLE `cms_User` (
  `cms_UserId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Email` mediumtext,
  `Login` mediumtext,
  `ICQ` mediumtext,
  `Phone` mediumtext,
  `Password` mediumtext,
  `SessionHash` mediumtext,
  `IsShared` char(1) DEFAULT NULL,
  `IsAdmin` char(1) DEFAULT NULL,
  `Skype` mediumtext,
  `Language` int(11) DEFAULT NULL,
  `PhotoMime` mediumtext,
  `PhotoPath` mediumtext,
  `PhotoExt` varchar(32) DEFAULT NULL,
  `IsActivated` char(1) DEFAULT NULL,
  `Rating` float DEFAULT NULL,
  `Description` mediumtext,
  `LDAPUID` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_UserId`),
  KEY `Login` (`Login`(20)),
  KEY `i$33` (`IsAdmin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_User` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_UserLock`;
CREATE TABLE `cms_UserLock` (
  `cms_UserLockId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `EntityName` mediumtext,
  `ObjectId` int(11) DEFAULT NULL,
  `IsActive` char(1) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_UserLockId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_UserLock` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_UserSettings`;
CREATE TABLE `cms_UserSettings` (
  `cms_UserSettingsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `User` int(11) DEFAULT NULL,
  `Settings` varchar(32) DEFAULT NULL,
  `Value` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_UserSettingsId`),
  KEY `i$24` (`User`,`Settings`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_UserSettings` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_EntityCluster`;
CREATE TABLE `cms_EntityCluster` (
  `cms_EntityClusterId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `VPD` varchar(32) DEFAULT NULL,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `ObjectAttribute` varchar(32) DEFAULT NULL,
  `AttributeValue` varchar(128) DEFAULT NULL,
  `ObjectIds` longtext,
  `TotalCount` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_EntityClusterId`,`RecordModified`),
  KEY `I$cms_EntityCluster$RecordModified` (`RecordModified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
/*!50100 PARTITION BY RANGE (UNIX_TIMESTAMP(RecordModified))
(PARTITION p_201305 VALUES LESS THAN (1370030400) ENGINE = MyISAM,
 PARTITION p_201306 VALUES LESS THAN (1372622400) ENGINE = MyISAM,
 PARTITION p_201307 VALUES LESS THAN (1375300800) ENGINE = MyISAM,
 PARTITION p_201308 VALUES LESS THAN (1377979200) ENGINE = MyISAM,
 PARTITION p_201309 VALUES LESS THAN (1380571200) ENGINE = MyISAM,
 PARTITION p_max VALUES LESS THAN MAXVALUE ENGINE = MyISAM) */;


LOCK TABLES `cms_EntityCluster` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Resource`;
CREATE TABLE `cms_Resource` (
  `cms_ResourceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ResourceKey` mediumtext,
  `ResourceValue` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_ResourceId`),
  KEY `I$cms_Resource$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Resource` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_Snapshot`;
CREATE TABLE `cms_Snapshot` (
  `cms_SnapshotId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `ListName` mediumtext,
  `ObjectId` varchar(64) DEFAULT NULL,
  `ObjectClass` varchar(64) DEFAULT NULL,
  `Type` varchar(64) DEFAULT NULL,
  `Description` mediumtext,
  PRIMARY KEY (`cms_SnapshotId`),
  KEY `I$cms_Snapshot$Object` (`ObjectId`,`ObjectClass`),
  KEY `I$cms_Snapshot$Branch` (`ObjectId`,`ObjectClass`,`Type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_Snapshot` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_SnapshotItem`;
CREATE TABLE `cms_SnapshotItem` (
  `cms_SnapshotItemId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Snapshot` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(128) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_SnapshotItemId`),
  KEY `I$cms_SnapshotItem$Snapshot` (`Snapshot`),
  KEY `I$cms_SnapshotItem$Object` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_SnapshotItem` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `cms_SnapshotItemValue`;
CREATE TABLE `cms_SnapshotItemValue` (
  `cms_SnapshotItemValueId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `SnapshotItem` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` varchar(128) DEFAULT NULL,
  `Value` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`cms_SnapshotItemValueId`),
  KEY `I$cms_SnapshotItemValue$SnapshotItem` (`SnapshotItem`),
  KEY `I$cms_SnapshotItemValue$SnapshotItemReference` (`SnapshotItem`,`ReferenceName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `cms_SnapshotItemValue` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_AccessRight`;
CREATE TABLE `co_AccessRight` (
  `co_AccessRightId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `UserGroup` int(11) DEFAULT NULL,
  `AccessType` varchar(32) DEFAULT NULL,
  `ReferenceName` varchar(255) DEFAULT NULL,
  `ReferenceType` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_AccessRightId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_AccessRight` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Advise`;
CREATE TABLE `co_Advise` (
  `co_AdviseId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `Advise` mediumtext,
  `Theme` int(11) DEFAULT NULL,
  `Author` int(11) DEFAULT NULL,
  `IsApproved` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_AdviseId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Advise` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_AdviseTheme`;
CREATE TABLE `co_AdviseTheme` (
  `co_AdviseThemeId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_AdviseThemeId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


LOCK TABLES `co_AdviseTheme` WRITE;
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Ведение проекта',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с пожеланиями',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (3,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Управление проектом',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (4,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Планирование',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (5,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Выполнение задач',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (6,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с требованиями',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (7,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Тестирование',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (8,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с документацией',0);
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (9,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с артефактами',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Bill`;
CREATE TABLE `co_Bill` (
  `co_BillId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_BillId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Bill` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_BillOperation`;
CREATE TABLE `co_BillOperation` (
  `co_BillOperationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Volume` float DEFAULT NULL,
  `Comment` mediumtext,
  `Bill` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_BillOperationId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_BillOperation` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_CommunityRole`;
CREATE TABLE `co_CommunityRole` (
  `co_CommunityRoleId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_CommunityRoleId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `co_CommunityRole` WRITE;
INSERT INTO `co_CommunityRole` (`co_CommunityRoleId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:53','2010-06-06 18:05:53','',10,'Пользователь',0);
INSERT INTO `co_CommunityRole` (`co_CommunityRoleId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:53','2010-06-06 18:05:53','',20,'Участник проектов',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_CustomReport`;
CREATE TABLE `co_CustomReport` (
  `co_CustomReportId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Url` mediumtext,
  `Description` mediumtext,
  `Author` int(11) DEFAULT NULL,
  `ReportBase` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_CustomReportId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_CustomReport` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_IssueOutsourcing`;
CREATE TABLE `co_IssueOutsourcing` (
  `co_IssueOutsourcingId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ChangeRequest` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `Cost` mediumtext,
  `Duration` int(11) DEFAULT NULL,
  `Comment` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_IssueOutsourcingId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_IssueOutsourcing` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_JobRun`;
CREATE TABLE `co_JobRun` (
  `co_JobRunId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ScheduledJob` int(11) DEFAULT NULL,
  `Result` mediumtext,
  `IsCompleted` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_JobRunId`),
  KEY `I$co_JobRun$Job` (`ScheduledJob`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_JobRun` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_MailTransport`;
CREATE TABLE `co_MailTransport` (
  `co_MailTransportId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  PRIMARY KEY (`co_MailTransportId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `co_MailTransport` WRITE;
INSERT INTO `co_MailTransport` (`co_MailTransportId`, `VPD`, `RecordVersion`, `OrderNum`, `RecordCreated`, `RecordModified`, `Caption`, `ReferenceName`) VALUES (1,NULL,0,10,NULL,NULL,'SMTP','SMTP');
INSERT INTO `co_MailTransport` (`co_MailTransportId`, `VPD`, `RecordVersion`, `OrderNum`, `RecordCreated`, `RecordModified`, `Caption`, `ReferenceName`) VALUES (2,NULL,0,20,NULL,NULL,'IMAP','IMAP');
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_MailboxProvider`;
CREATE TABLE `co_MailboxProvider` (
  `co_MailboxProviderId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `ProtocolName` mediumtext,
  PRIMARY KEY (`co_MailboxProviderId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `co_MailboxProvider` WRITE;
INSERT INTO `co_MailboxProvider` (`co_MailboxProviderId`, `VPD`, `RecordVersion`, `OrderNum`, `RecordCreated`, `RecordModified`, `Caption`, `ProtocolName`) VALUES (1,NULL,0,NULL,NULL,NULL,'POP3','POP3');
INSERT INTO `co_MailboxProvider` (`co_MailboxProviderId`, `VPD`, `RecordVersion`, `OrderNum`, `RecordCreated`, `RecordModified`, `Caption`, `ProtocolName`) VALUES (2,NULL,0,NULL,NULL,NULL,'IMAP','IMAP');
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Message`;
CREATE TABLE `co_Message` (
  `co_MessageId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Subject` mediumtext,
  `Content` mediumtext,
  `Author` int(11) DEFAULT NULL,
  `ToUser` int(11) DEFAULT NULL,
  `ToTeam` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_MessageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Message` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Option`;
CREATE TABLE `co_Option` (
  `co_OptionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `Cost` float DEFAULT NULL,
  `Period` int(11) DEFAULT NULL,
  `Conditions` mediumtext,
  `CodeName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_OptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Option` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_OptionUser`;
CREATE TABLE `co_OptionUser` (
  `co_OptionUserId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Option` int(11) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `IsActive` char(1) DEFAULT NULL,
  `IsPayed` char(1) DEFAULT NULL,
  `PaymentDate` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_OptionUserId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_OptionUser` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_OutsourcingSuggestion`;
CREATE TABLE `co_OutsourcingSuggestion` (
  `co_OutsourcingSuggestionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `IssueOutsourcing` int(11) DEFAULT NULL,
  `Cost` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `Comment` mediumtext,
  `IsAccepted` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_OutsourcingSuggestionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_OutsourcingSuggestion` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_ProjectGroup`;
CREATE TABLE `co_ProjectGroup` (
  `co_ProjectGroupId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `ParentGroup` int(11) DEFAULT NULL,
  `LDAPUID` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_ProjectGroupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_ProjectGroup` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_ProjectGroupLink`;
CREATE TABLE `co_ProjectGroupLink` (
  `co_ProjectGroupLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ProjectGroup` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_ProjectGroupLinkId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_ProjectGroupLink` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_ProjectParticipant`;
CREATE TABLE `co_ProjectParticipant` (
  `co_ProjectParticipantId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `Price` int(11) DEFAULT NULL,
  `PriceCode` varchar(32) DEFAULT NULL,
  `Skills` mediumtext,
  `Tools` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_ProjectParticipantId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_ProjectParticipant` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_ProjectSubscription`;
CREATE TABLE `co_ProjectSubscription` (
  `co_ProjectSubscriptionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_ProjectSubscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_ProjectSubscription` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Rating`;
CREATE TABLE `co_Rating` (
  `co_RatingId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_RatingId`),
  KEY `i$28` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Rating` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_RatingVoice`;
CREATE TABLE `co_RatingVoice` (
  `co_RatingVoiceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `IPAddress` mediumtext,
  `Rating` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_RatingVoiceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_RatingVoice` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_RemoteMailbox`;
CREATE TABLE `co_RemoteMailbox` (
  `co_RemoteMailboxId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `HostAddress` mediumtext,
  `PortServer` int(11) DEFAULT NULL,
  `EmailAddress` mediumtext,
  `EmailPassword` mediumtext,
  `UseSSL` char(1) DEFAULT NULL,
  `UseDebug` char(1) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `IsActive` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `MailboxProvider` int(11) DEFAULT NULL,
  `SenderAddress` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`co_RemoteMailboxId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_RemoteMailbox` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_ScheduledJob`;
CREATE TABLE `co_ScheduledJob` (
  `co_ScheduledJobId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ClassName` mediumtext,
  `Minutes` varchar(32) DEFAULT NULL,
  `Hours` varchar(32) DEFAULT NULL,
  `Days` varchar(32) DEFAULT NULL,
  `WeekDays` varchar(32) DEFAULT NULL,
  `IsActive` char(1) DEFAULT NULL,
  `Parameters` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_ScheduledJobId`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;


LOCK TABLES `co_ScheduledJob` WRITE;
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (1,NULL,NULL,NULL,10,'text(955)','processrevisionlog','*/10','*','*','*','Y',NULL,0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (2,NULL,NULL,NULL,20,'text(956)','processstatistics','*','*','*','*','Y',NULL,0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (3,NULL,NULL,NULL,300,'text(957)','processbackup','0','23','*','*','Y','{\"limit\":\"20\"}',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (4,NULL,NULL,NULL,200,'text(958)','processemailqueue','*','*','*','*','Y','{\"limit\":\"20\"}',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (5,NULL,NULL,NULL,40,'text(959)','support/scanmailboxes','*','*','*','*','Y',NULL,0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (6,NULL,NULL,NULL,50,'text(967)','processdigest','*/10','*','*','*','Y','{\"limit\":\"10\",\"type\":\"every10minutes\"}',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (7,NULL,NULL,NULL,60,'text(968)','processdigest','0','*','*','*','Y','{\"limit\":\"10\",\"type\":\"every1hour\"}',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (8,NULL,NULL,NULL,70,'text(960)','processdigest','0','23','*','*','Y','{\"limit\":\"10\",\"type\":\"daily\"}',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (9,NULL,NULL,NULL,80,'text(962)','processdigest','0','23','*/2','*','Y','{\"limit\":\"10\",\"type\":\"every2days\"}',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (10,NULL,NULL,NULL,90,'text(963)','processdigest','0','8','*','1','Y','{\"limit\":\"10\",\"type\":\"weekly\"}',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (11,NULL,NULL,NULL,100,'text(993)','trackhistory','*/5','*','*','*','Y','',0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (12,NULL,NULL,NULL,NULL,'text(1130)','processcheckpoints','*/10','*','*','*','Y',NULL,0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (13,NULL,NULL,NULL,NULL,'text(1194)','meetingremember','0','7','*','*','Y',NULL,0);
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (14,NULL,NULL,NULL,NULL,'text(1227)','processdigest','*','*','*','*','Y',NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_SearchResult`;
CREATE TABLE `co_SearchResult` (
  `co_SearchResultId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SearchKind` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `Result` mediumtext,
  `Conditions` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_SearchResultId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_SearchResult` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_ServiceCategory`;
CREATE TABLE `co_ServiceCategory` (
  `co_ServiceCategoryId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_ServiceCategoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


LOCK TABLES `co_ServiceCategory` WRITE;
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:37','2010-06-06 18:05:37','',10,'Разработка ПО',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:37','2010-06-06 18:05:37','',20,'Администрирование',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (3,'2010-06-06 18:05:37','2010-06-06 18:05:37','',30,'Тестирование',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (4,'2010-06-06 18:05:37','2010-06-06 18:05:37','',40,'Маркетинг',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (5,'2010-06-06 18:05:37','2010-06-06 18:05:37','',50,'Продажи',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (6,'2010-06-06 18:05:37','2010-06-06 18:05:37','',60,'Консультирование',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (7,'2010-06-06 18:05:37','2010-06-06 18:05:37','',70,'Дизайн',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (8,'2010-06-06 18:05:37','2010-06-06 18:05:37','',80,'Обучение',0);
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (9,'2010-06-06 18:05:37','2010-06-06 18:05:37','',90,'Управление проектами',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_ServiceRequest`;
CREATE TABLE `co_ServiceRequest` (
  `co_ServiceRequestId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Service` int(11) DEFAULT NULL,
  `Customer` int(11) DEFAULT NULL,
  `Response` mediumtext,
  `IsClosed` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_ServiceRequestId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_ServiceRequest` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Team`;
CREATE TABLE `co_Team` (
  `co_TeamId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `Tagline` mediumtext,
  `Description` mediumtext,
  `Author` int(11) DEFAULT NULL,
  `Rating` float DEFAULT NULL,
  `TeamState` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TeamId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Team` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_TeamState`;
CREATE TABLE `co_TeamState` (
  `co_TeamStateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TeamStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `co_TeamState` WRITE;
INSERT INTO `co_TeamState` (`co_TeamStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:52','2010-06-06 18:05:52','',10,'Свободна','Команда готова выполнять проекты',0);
INSERT INTO `co_TeamState` (`co_TeamStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:52','2010-06-06 18:05:52','',20,'Занята','Команда занята выполнением своих проектов',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_TeamUser`;
CREATE TABLE `co_TeamUser` (
  `co_TeamUserId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Team` int(11) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `TeamRoles` mediumtext,
  `IsActive` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TeamUserId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_TeamUser` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Tender`;
CREATE TABLE `co_Tender` (
  `co_TenderId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Kind` int(11) DEFAULT NULL,
  `State` int(11) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TenderId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Tender` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_TenderAttachment`;
CREATE TABLE `co_TenderAttachment` (
  `co_TenderAttachmentId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Tender` int(11) DEFAULT NULL,
  `AttachmentMime` mediumtext,
  `AttachmentPath` mediumtext,
  `AttachmentExt` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TenderAttachmentId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_TenderAttachment` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_TenderKind`;
CREATE TABLE `co_TenderKind` (
  `co_TenderKindId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TenderKindId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `co_TenderKind` WRITE;
INSERT INTO `co_TenderKind` (`co_TenderKindId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:55','2010-06-06 18:05:55','',10,'Открытый',0);
INSERT INTO `co_TenderKind` (`co_TenderKindId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:55','2010-06-06 18:05:55','',20,'Закрытый',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_TenderParticipanceState`;
CREATE TABLE `co_TenderParticipanceState` (
  `co_TenderParticipanceStateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TenderParticipanceStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


LOCK TABLES `co_TenderParticipanceState` WRITE;
INSERT INTO `co_TenderParticipanceState` (`co_TenderParticipanceStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:56','2010-06-06 18:05:56','',10,'Рассматривается',0);
INSERT INTO `co_TenderParticipanceState` (`co_TenderParticipanceStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:56','2010-06-06 18:05:56','',20,'Подтверждено',0);
INSERT INTO `co_TenderParticipanceState` (`co_TenderParticipanceStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (3,'2010-06-06 18:05:56','2010-06-06 18:05:56','',30,'Отклонено',0);
INSERT INTO `co_TenderParticipanceState` (`co_TenderParticipanceStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (4,'2010-06-06 18:05:57','2010-06-06 18:05:57','',40,'Готовит предложение',0);
INSERT INTO `co_TenderParticipanceState` (`co_TenderParticipanceStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (5,'2010-06-06 18:05:57','2010-06-06 18:05:57','',50,'Предложение готово',0);
INSERT INTO `co_TenderParticipanceState` (`co_TenderParticipanceStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (6,'2010-06-06 18:05:58','2010-06-06 18:05:58','',60,'Тендер выигран',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_TenderParticipant`;
CREATE TABLE `co_TenderParticipant` (
  `co_TenderParticipantId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Tender` int(11) DEFAULT NULL,
  `Team` int(11) DEFAULT NULL,
  `State` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TenderParticipantId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_TenderParticipant` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_TenderState`;
CREATE TABLE `co_TenderState` (
  `co_TenderStateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_TenderStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


LOCK TABLES `co_TenderState` WRITE;
INSERT INTO `co_TenderState` (`co_TenderStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:55','2010-06-06 18:05:55','',10,'Открыт',0);
INSERT INTO `co_TenderState` (`co_TenderStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:55','2010-06-06 18:05:55','',20,'Завершен',0);
INSERT INTO `co_TenderState` (`co_TenderStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (3,'2010-06-06 18:05:55','2010-06-06 18:05:55','',30,'Отменен',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_UserGroup`;
CREATE TABLE `co_UserGroup` (
  `co_UserGroupId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `ParentGroup` int(11) DEFAULT NULL,
  `LDAPUID` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_UserGroupId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_UserGroup` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_UserGroupLink`;
CREATE TABLE `co_UserGroupLink` (
  `co_UserGroupLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `UserGroup` int(11) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_UserGroupLinkId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_UserGroupLink` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_UserRole`;
CREATE TABLE `co_UserRole` (
  `co_UserRoleId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `CommunityRole` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`co_UserRoleId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_UserRole` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_AffectedObjects`;
CREATE TABLE `co_AffectedObjects` (
  `co_AffectedObjectsId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`co_AffectedObjectsId`),
  KEY `I$co_AffectedObjects$Object` (`ObjectId`,`ObjectClass`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;


LOCK TABLES `co_AffectedObjects` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Company`;
CREATE TABLE `co_Company` (
  `co_CompanyId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` varchar(255) DEFAULT NULL,
  `Domains` varchar(255) DEFAULT NULL,
  `Description` mediumtext,
  `CanSeeCompanyIssues` char(1) DEFAULT 'N',
  PRIMARY KEY (`co_CompanyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Company` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_CompanyProject`;
CREATE TABLE `co_CompanyProject` (
  `co_CompanyProjectId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `OrderNum` int(11) DEFAULT NULL,
  `Company` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  PRIMARY KEY (`co_CompanyProjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_CompanyProject` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `co_Service`;
CREATE TABLE `co_Service` (
  `co_ServiceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(256) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Category` int(11) DEFAULT NULL,
  `Description` mediumtext,
  `Cost` mediumtext,
  `Author` int(11) DEFAULT NULL,
  `Team` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `PayedTill` date DEFAULT NULL,
  PRIMARY KEY (`co_ServiceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `co_Service` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `entity`;
CREATE TABLE `entity` (
  `entityId` int(11) NOT NULL AUTO_INCREMENT,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `packageId` int(11) DEFAULT NULL,
  `IsOrdered` char(1) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `IsDictionary` char(1) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`entityId`),
  UNIQUE KEY `XPKentity` (`entityId`),
  KEY `entity_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30))
) ENGINE=MyISAM AUTO_INCREMENT=374 DEFAULT CHARSET=utf8;


LOCK TABLES `entity` WRITE;
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (1,'Меню','cms_MainMenu',1,'Y',10,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (2,'Страница','cms_Page',1,'Y',20,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (3,'Участник','pm_Participant',4,'Y',30,NULL,'2006-01-28 10:34:07','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (4,'Участие в проекте','pm_ParticipantRole',4,'Y',40,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (5,'Проект','pm_Project',4,'Y',50,NULL,NULL,'N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (6,'Роль в проекте','pm_ProjectRole',4,'Y',60,NULL,'2006-01-28 10:34:37','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (7,'Каталог','pm_ArtefactType',10,'Y',70,NULL,'2006-01-26 21:21:25','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (8,'Файл','pm_Artefact',10,'Y',80,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (9,'Wiki страница','WikiPage',9,'Y',90,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (10,'Файл страницы','WikiPageFile',9,'Y',100,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (11,'Ошибка','pm_Bug',2,'Y',110,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (12,'Доработка','pm_Enhancement',2,'Y',120,NULL,'2006-01-11 23:54:24','',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (13,'pm_Task','Задача',2,'Y',130,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (14,'Итерация','pm_Release',5,'Y',140,NULL,'2006-01-28 10:56:05','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (15,'Задача','pm_Task',5,'Y',150,NULL,NULL,NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (16,'Изменение страницы Wiki','WikiPageChange',9,'',160,NULL,'2005-12-22 21:42:44',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (17,'Приоритет','Priority',11,'Y',170,'2005-12-24 11:54:48','2005-12-24 22:59:48','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (18,'Митинг','pm_Meeting',2,'Y',180,'2005-12-24 11:57:42','2005-12-24 11:57:42',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (19,'Участие в митинге','MeetingParticipation',2,'Y',190,'2005-12-24 12:00:55','2005-12-24 12:00:55',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (20,'Тип задачи','pm_TaskType',5,'Y',200,'2005-12-24 21:49:45','2005-12-24 22:26:51','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (21,'Состояние работы','pm_TaskState',2,'Y',210,'2005-12-25 00:20:57','2005-12-25 00:20:57','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (22,'Пожелание','pm_ChangeRequest',8,'Y',220,'2005-12-28 08:53:48','2005-12-28 08:53:48',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (23,'Создание проекта','pm_ProjectCreation',2,'Y',230,'2006-01-06 13:33:44','2006-01-06 13:33:44',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (24,'Новость','News',1,'Y',240,'2006-01-06 18:51:23','2006-01-06 18:51:23',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (25,'Электронное письмо','Email',1,'Y',250,'2006-01-06 21:12:53','2006-01-06 21:12:53',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (26,'Использование проекта','pm_ProjectUse',2,'Y',260,'2006-01-09 16:47:02','2006-01-09 16:47:02',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (27,'Справка','pm_Help',2,'Y',270,'2006-01-09 22:42:25','2006-01-09 22:42:25',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (28,'Уведомление об операции над объектом','ObjectEmailNotification',1,'Y',280,'2006-01-14 14:53:02','2006-01-14 14:53:02',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (29,'Связь уведомления с классом','ObjectEmailNotificationLink',1,'Y',290,'2006-01-14 14:53:33','2006-01-14 14:53:33',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (30,'Изменение объекта','ObjectChangeLog',11,'Y',300,'2006-01-16 21:29:23','2006-01-16 21:29:23',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (31,'Связь со справочной документацией','HelpLink',2,'Y',310,'2006-01-17 08:47:27','2006-01-17 08:47:27',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (32,'Причина деактуализации справочной документации ','pm_HelpDeactReason',2,NULL,330,'2006-01-19 09:59:15','2006-01-19 09:59:15',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (36,'Методология','pm_Methodology',6,NULL,350,'2006-01-25 20:54:22','2006-01-25 20:54:22',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (35,'Комментарий','Comment',14,'Y',340,'2006-01-21 14:40:03','2006-01-21 14:40:03',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (37,'Реклама книг','AdvertiseBooks',1,'Y',360,'2006-01-29 21:41:13','2006-01-29 21:41:13',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (38,'Взнос','Donation',1,NULL,370,'2006-02-02 21:59:50','2006-02-02 21:59:50',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (39,'Релиз','pm_Version',5,'Y',380,'2006-02-09 21:56:11','2006-02-09 21:56:11','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (40,'Блог','Blog',9,'Y',390,'2006-02-11 17:00:52','2006-02-11 17:00:52',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (41,'Сообщение блога','BlogPost',9,'Y',400,'2006-02-11 17:02:16','2006-02-11 17:02:16',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (42,'Файл сообщения блога','BlogPostFile',9,'Y',410,'2006-02-11 17:04:56','2006-02-11 17:04:56',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (43,'Ссылка на блог','BlogLink',1,'Y',420,'2006-02-11 17:08:12','2006-02-11 17:08:12',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (44,'Подписчик блога','BlogSubscriber',1,'Y',430,'2006-02-11 17:10:33','2006-02-11 17:10:33',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (45,'Очередь сообщений','EmailQueue',15,'Y',440,'2006-02-12 21:22:50','2006-02-12 21:22:50',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (46,'Адресат очереди сообщений','EmailQueueAddress',15,'Y',450,'2006-02-12 21:23:47','2006-02-12 21:23:47',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (47,'Публикация проекта','pm_PublicInfo',2,'Y',460,'2006-02-13 21:24:35','2006-02-13 21:24:35',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (48,'Шаблон - HTML','TemplateHTML',2,'Y',470,'2006-02-23 22:30:12','2006-02-23 22:30:12',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (49,'Сборка','pm_Build',10,'Y',480,'2006-02-25 15:45:51','2006-02-25 15:45:51','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (50,'Связь задачи и сборки','pm_BuildTask',2,'Y',490,'2006-02-26 16:10:36','2006-02-26 16:10:36',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (51,'Категория ссылок','cms_LinkCategory',1,'Y',500,'2006-03-06 22:00:32','2006-03-06 22:00:32','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (52,'Ссылка','cms_Link',1,'Y',510,'2006-03-06 22:01:05','2006-03-06 22:01:05',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (56,'Тэг Wiki страницы','WikiTag',9,'Y',530,'2006-03-16 21:17:31','2006-03-16 21:17:31',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (55,'Тэг','Tag',14,'Y',520,'2006-03-16 21:17:04','2006-03-16 21:17:04','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (57,'Язык','cms_Language',15,'Y',540,'2006-03-21 23:27:44','2006-03-21 23:27:44','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (58,'Тэг пожелания','pm_RequestTag',2,'Y',550,'2006-03-26 10:12:09','2006-03-26 10:12:09',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (59,'Конфигурация программного продукта','pm_Configuration',2,'Y',560,'2006-03-27 23:30:38','2006-03-27 23:30:38','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (60,'Резервная копия','cms_Backup',15,'Y',570,'2010-06-06 18:05:01','2010-06-06 18:05:01','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (61,'Обновление','cms_Update',15,'Y',580,'2010-06-06 18:05:01','2010-06-06 18:05:01','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (62,'Настройки системы','cms_SystemSettings',15,'Y',590,'2010-06-06 18:05:02','2010-06-06 18:05:02',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (63,'Пользователь','cms_User',15,'Y',600,'2010-06-06 18:05:03','2010-06-06 18:05:03',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (64,'Функция','pm_Function',8,'Y',610,'2010-06-06 18:05:04','2010-06-06 18:05:04','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (65,'Пользовательская настройка','cms_UserSettings',1,NULL,620,'2010-06-06 18:05:07','2010-06-06 18:05:07',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (66,'Протокол переходов','cms_BrowserTransitionLog',1,NULL,630,'2010-06-06 18:05:09','2010-06-06 18:05:09',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (67,'Информация о клиенте','cms_ClientInfo',1,NULL,640,'2010-06-06 18:05:09','2010-06-06 18:05:09',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (68,'Новостной канал','pm_NewsChannel',2,'Y',640,'2010-06-06 18:05:10','2010-06-06 18:05:10','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (69,'Позиция в новостном канале','pm_NewsChannelItem',2,'Y',650,'2010-06-06 18:05:11','2010-06-06 18:05:11',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (70,'Подписка проекта на новости','pm_NewsChannelSubscription',2,'Y',670,'2010-06-06 18:05:11','2010-06-06 18:05:11',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (72,'Почтовое сообщение','pm_UserMail',4,'Y',690,'2010-06-06 18:05:13','2010-06-06 18:05:13',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (74,'Тест','pm_Test',12,'Y',710,'2010-06-06 18:05:14','2010-06-06 18:05:14',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (75,'Проверка тестового случая','pm_TestCaseExecution',12,'Y',720,'2010-06-06 18:05:14','2010-06-06 18:05:14',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (76,'Ежедневный митинг','pm_Scrum',2,'Y',730,'2010-06-06 18:05:16','2010-10-01 17:16:29','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (77,'Окружение','pm_Environment',12,'Y',740,'2010-06-06 18:05:17','2010-06-06 18:05:17','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (78,'Приложение','pm_Attachment',14,'N',750,'2010-06-06 18:05:17','2010-06-06 18:05:17','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (79,'Веха','pm_Milestone',5,'Y',760,'2010-06-06 18:05:18','2010-06-06 18:05:18','N','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (80,'Участие в митинге','pm_MeetingParticipant',2,'Y',770,'2010-06-06 18:05:19','2010-06-06 18:05:19','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (81,'Заметка к релизу','pm_ReleaseNote',2,'Y',780,'2010-06-06 18:05:20','2010-06-06 18:05:20',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (82,'Активность','pm_Activity',5,'Y',790,'2010-06-06 18:05:20','2010-06-06 18:05:20',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (84,'Связь теста с требованием','pm_TestScenarioReqLink',2,'Y',810,'2010-06-06 18:05:21','2010-06-06 18:05:21',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (85,'Тип связи пожеланий','pm_ChangeRequestLinkType',8,'Y',820,'2010-06-06 18:05:22','2010-06-06 18:05:22','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (86,'Связь с пожеланиями','pm_ChangeRequestLink',8,'Y',830,'2010-06-06 18:05:22','2010-06-06 18:05:22',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (87,'Тип пожелания','pm_IssueType',8,'Y',840,'2010-06-06 18:05:23','2010-06-06 18:05:23','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (88,'Опросник','pm_Poll',2,NULL,850,'2010-06-06 18:05:24','2010-06-06 18:05:24',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (89,'Позиция опросника','pm_PollItem',2,'Y',860,'2010-06-06 18:05:24','2010-06-06 18:05:24',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (90,'Результат опроса','pm_PollResult',2,NULL,870,'2010-06-06 18:05:25','2010-06-06 18:05:25',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (91,'Позиция результата опроса','pm_PollItemResult',2,'Y',880,'2010-06-06 18:05:25','2010-06-06 18:05:25',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (92,'Сериализированный объект','cms_SerializedObject',1,NULL,890,'2010-06-06 18:05:26','2010-06-06 18:05:26',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (93,'Ремап объекта','cms_RemapObject',1,NULL,900,'2010-06-06 18:05:26','2010-06-06 18:05:26',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (94,'Источник синхронизации','cms_SynchronizationSource',1,'Y',910,'2010-06-06 18:05:27','2010-06-06 18:05:27',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (95,'Срок','pm_Deadline',8,'Y',920,'2010-06-06 18:05:27','2010-06-06 18:05:27',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (96,'Удаленный объект','cms_DeletedObject',1,'Y',930,'2010-06-06 18:05:28','2010-06-06 18:05:28',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (97,'Метрики итерации','pm_ReleaseMetrics',16,NULL,940,'2010-06-06 18:05:29','2010-06-06 18:05:29',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (98,'Пользовательская блокировка','cms_UserLock',1,NULL,950,'2010-06-06 18:05:30','2010-06-06 18:05:30',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (99,'Состояние требования','pm_RequirementState',2,'Y',960,'2010-06-06 18:05:31','2010-06-06 18:05:31','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (100,'Шаблон - HTML','TemplateHTML2',6,'Y',470,'2010-06-06 18:05:31','2010-06-06 18:05:31','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (101,'Почтовая рассылка','cms_EmailNotification',1,'Y',970,'2010-06-06 18:05:32','2010-06-06 18:05:32','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (102,'Подписка на рассылку','cms_NotificationSubscription',1,'Y',980,'2010-06-06 18:05:33','2010-06-06 18:05:33',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (103,'Команда','co_Team',3,NULL,990,'2010-06-06 18:05:33','2010-06-06 18:05:33',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (104,'Участие в команде','co_TeamUser',3,'Y',1000,'2010-06-06 18:05:34','2010-06-06 18:05:34',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (105,'Лицензия','cms_License',15,'Y',1010,'2010-06-06 18:05:34','2010-06-06 18:05:34',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (106,'Тэг проекта','pm_ProjectTag',2,'Y',1020,'2010-06-06 18:05:34','2010-06-06 18:05:34',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (107,'Подписка на проект','co_ProjectSubscription',3,NULL,1030,'2010-06-06 18:05:35','2010-06-06 18:05:35',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (108,'Вакансия в проекте','pm_Vacancy',2,'Y',1040,'2010-06-06 18:05:35','2010-06-06 18:05:35',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (109,'Категория услуги','co_ServiceCategory',3,'Y',1050,'2010-06-06 18:05:37','2010-06-06 18:05:37','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (110,'Услуга','co_Service',3,'Y',1060,'2010-06-06 18:05:37','2010-06-06 18:05:37',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (111,'Заявка на услугу','co_ServiceRequest',3,'Y',1070,'2010-06-06 18:05:37','2010-06-06 18:05:37',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (112,'text(1058)','pm_Subversion',10,NULL,1080,'2010-06-06 18:05:38','2010-06-06 18:05:38',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (113,'text(1060)','pm_SubversionRevision',10,NULL,1090,'2010-06-06 18:05:39','2010-06-06 18:05:39',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (114,'Аутсорсинг пожелания','co_IssueOutsourcing',3,'Y',1100,'2010-06-06 18:05:39','2010-06-06 18:05:39',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (115,'Предложение по реализации','co_OutsourcingSuggestion',3,NULL,1110,'2010-06-06 18:05:40','2010-06-06 18:05:40',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (116,'Приглашение в проект','pm_Invitation',2,NULL,1120,'2010-06-06 18:05:40','2010-06-06 18:05:40',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (117,'Загрузка артефакта','pm_DownloadAction',2,NULL,1130,'2010-06-06 18:05:41','2010-06-06 18:05:41',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (118,'Загрузивший пользователь','pm_DownloadActor',2,NULL,1140,'2010-06-06 18:05:41','2010-06-06 18:05:41',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (119,'Тематика совета','co_AdviseTheme',3,NULL,1150,'2010-06-06 18:05:42','2010-06-06 18:05:42','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (120,'Совет пользователям','co_Advise',3,NULL,1160,'2010-06-06 18:05:42','2010-06-06 18:05:42',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (121,'Метрика участника','pm_ParticipantMetrics',16,NULL,1170,'2010-06-06 18:05:43','2010-06-06 18:05:43',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (122,'Счет','co_Bill',3,NULL,1180,'2010-06-06 18:05:43','2010-06-06 18:05:43',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (123,'Операция по счету','co_BillOperation',3,'Y',1190,'2010-06-06 18:05:43','2010-06-06 18:05:43',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (124,'Рейтинг','co_Rating',3,NULL,1200,'2010-06-06 18:05:44','2010-06-06 18:05:44',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (125,'Голос рейтинга','co_RatingVoice',3,NULL,1210,'2010-06-06 18:05:44','2010-06-06 18:05:44',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (126,'Опция','co_Option',3,NULL,1220,'2010-06-06 18:05:45','2010-06-06 18:05:45',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (127,'Подключение опции','co_OptionUser',3,NULL,1230,'2010-06-06 18:05:45','2010-06-06 18:05:45',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (128,'Метрика итерации','pm_IterationMetric',16,NULL,1240,'2010-06-06 18:05:46','2010-06-06 18:05:46',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (129,'Метрика релиза','pm_VersionMetric',16,NULL,1250,'2010-06-06 18:05:46','2010-06-06 18:05:46',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (130,'Черный список','cms_BlackList',15,NULL,1260,'2010-06-06 18:05:47','2010-06-06 18:05:47',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (131,'Попытка логина','cms_LoginRetry',1,NULL,1270,'2010-06-06 18:05:47','2010-06-06 18:05:47',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (132,'Контрольный вопрос','cms_CheckQuestion',1,NULL,1280,'2010-06-06 18:05:47','2010-06-06 18:05:47',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (200,'Тэг сообщения блога','BlogPostTag',9,'Y',1290,'2010-06-06 18:05:49','2010-06-06 18:05:49',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (201,'Сообщение','co_Message',3,'Y',1300,'2010-06-06 18:05:50','2010-06-06 18:05:50',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (202,'Результат поиска','co_SearchResult',3,NULL,1310,'2010-06-06 18:05:51','2010-06-06 18:05:51',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (203,'Вопрос','pm_Question',4,'Y',1320,'2010-06-06 18:05:52','2010-06-06 18:05:52',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (204,'Статус команды','co_TeamState',3,'Y',1330,'2010-06-06 18:05:52','2010-06-06 18:05:52','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (205,'Роль в сообществе','co_CommunityRole',3,'Y',1340,'2010-06-06 18:05:52','2010-06-06 18:05:52','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (206,'Роль пользователя','co_UserRole',3,'Y',1350,'2010-06-06 18:05:53','2010-06-06 18:05:53',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (207,'Участник проектов','co_ProjectParticipant',3,NULL,1360,'2010-06-06 18:05:53','2010-06-06 18:05:53',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (208,'Валюта','pm_Currency',2,'Y',1370,'2010-06-06 18:05:54','2010-06-06 18:05:54','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (209,'Настройки бюджетирования','pm_BugetSettings',2,'Y',1380,'2010-06-06 18:05:54','2010-06-06 18:05:54',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (210,'Модель оплаты','pm_PaymentModel',2,'Y',1390,'2010-06-06 18:05:54','2010-06-06 18:05:54','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (211,'Состояние тендера','co_TenderState',3,'Y',1400,'2010-06-06 18:05:55','2010-06-06 18:05:55','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (212,'Тип тендера','co_TenderKind',3,'Y',1410,'2010-06-06 18:05:55','2010-06-06 18:05:55','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (213,'Тендер','co_Tender',3,NULL,1420,'2010-06-06 18:05:55','2010-06-06 18:05:55',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (214,'Приложение к тендеру','co_TenderAttachment',3,'Y',1430,'2010-06-06 18:05:56','2010-06-06 18:05:56',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (215,'Состояние участия в тендере','co_TenderParticipanceState',3,'Y',1440,'2010-06-06 18:05:56','2010-06-06 18:05:56','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (216,'Участник тендера','co_TenderParticipant',3,'Y',1450,'2010-06-06 18:05:56','2010-06-06 18:05:56',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (217,'Параметры задачи','cms_BatchJob',15,'Y',1460,'2010-06-06 18:05:57','2010-06-06 18:05:57',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (300,'Тест план','pm_TestPlan',12,'Y',1470,'2010-06-06 18:06:04','2010-06-06 18:06:04',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (301,'Позиция тест плана','pm_TestPlanItem',12,'Y',1480,'2010-06-06 18:06:04','2010-06-06 18:06:04',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (302,'Временный файл','cms_TempFile',15,'Y',1490,'2010-06-06 18:06:06','2010-06-06 18:06:06',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (303,'Снимок','cms_Snapshot',14,'Y',1500,'2010-06-06 18:06:07','2010-06-06 18:06:07',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (304,'Элемент снимка','cms_SnapshotItem',14,'Y',1510,'2010-06-06 18:06:07','2010-06-06 18:06:07',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (305,'Значение элемента снимка','cms_SnapshotItemValue',14,'Y',1520,'2010-06-06 18:06:07','2010-06-06 18:06:07',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (306,'Право доступа','pm_AccessRight',6,NULL,1530,'2010-06-06 18:06:10','2010-06-06 18:06:10',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (307,'Хеш идентификаторов','cms_IdsHash',14,NULL,1540,'2010-06-06 18:06:13','2010-06-06 18:06:13',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (308,'Берндаун релиза','pm_VersionBurndown',16,NULL,1550,'2010-06-06 18:06:14','2010-06-06 18:06:14',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (309,'Настройки версии','pm_VersionSettings',6,NULL,1560,'2010-06-06 18:06:14','2010-06-06 18:06:14',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (310,'Изменение сообщения блога','BlogPostChange',2,'Y',1570,'2010-10-01 17:15:58','2010-10-01 17:15:58',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (311,'Наблюдатель','pm_Watcher',9,'Y',1580,'2010-10-01 17:15:59','2010-10-01 17:15:59',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (312,'Интервал календаря','pm_CalendarInterval',2,NULL,1590,'2010-10-01 17:15:59','2010-10-01 17:15:59',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (313,'Почтовый ящик','co_RemoteMailbox',3,'Y',1600,'2010-10-01 17:16:00','2010-10-01 17:16:00','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (314,'Задание по расписанию','co_ScheduledJob',15,'Y',1610,'2010-10-01 17:16:01','2010-10-01 17:16:01','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (315,'Выполнение задания','co_JobRun',15,'Y',1620,'2010-10-01 17:16:02','2010-10-01 17:16:02',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (316,'Связанный проект','pm_ProjectLink',6,'Y',1630,'2010-10-01 17:16:24','2010-10-01 17:16:24',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (317,'Доступ к объекту','pm_ObjectAccess',6,NULL,1640,'2010-10-01 17:16:25','2010-10-01 17:16:25',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (318,'Группа проектов','co_ProjectGroup',15,'Y',1650,'2010-10-01 17:16:26','2010-10-01 17:16:26','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (319,'Проект в группе','co_ProjectGroupLink',15,NULL,1660,'2010-10-01 17:16:26','2010-10-01 17:16:26',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (320,'Группа пользователей','co_UserGroup',15,'Y',1670,'2010-10-01 17:16:26','2010-10-01 17:16:26','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (321,'Пользователь в группе','co_UserGroupLink',15,NULL,1680,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (322,'Сущность','entity',1,'Y',1690,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,'',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (323,'Право доступа','co_AccessRight',15,NULL,1700,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (324,'Модуль','cms_PluginModule',1,NULL,1710,'2010-10-01 17:16:28','2010-10-01 17:16:28',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (325,'Шаблон проекта','pm_ProjectTemplate',11,'Y',1720,'2010-10-01 17:16:29','2010-10-01 17:16:29','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (326,'Стадия процесса','pm_ProjectStage',6,'Y',1730,'2010-10-01 17:16:29','2010-10-01 17:16:29','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (327,'Тип задачи в стадии процесса','pm_TaskTypeStage',6,'Y',1740,'2010-10-01 17:16:29','2010-10-01 17:16:29','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (328,'Результат тестирования','pm_TestExecutionResult',12,'Y',1750,'2010-11-01 21:19:03','2010-11-01 21:19:03','Y','',0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (329,'Отчет','cms_Report',4,'Y',1760,'2010-11-01 21:19:03','2010-11-01 21:19:03','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (330,'Категория отчета','cms_ReportCategory',4,'Y',1770,'2010-11-01 21:19:04','2010-11-01 21:19:04','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (331,'Трассировка пожелания','pm_ChangeRequestTrace',8,'Y',1780,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (332,'Трассировка задачи','pm_TaskTrace',5,'Y',1790,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (333,'Пользовательский тэг','pm_CustomTag',14,'Y',1800,'2011-02-21 21:08:26','2011-02-21 21:08:26','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (334,'Важность','pm_Importance',8,'Y',1810,'2011-02-21 21:08:27','2011-02-21 21:08:27','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (335,'Состояние','pm_State',7,'Y',1820,'2011-02-21 21:08:27','2011-02-21 21:08:27','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (336,'Переход в состояние','pm_Transition',7,'Y',1830,'2011-02-21 21:08:28','2011-02-21 21:08:28','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (337,'Состояние объекта','pm_StateObject',7,'N',1840,'2011-02-21 21:08:28','2011-02-21 21:08:28','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (338,'Право доступа на переход','pm_TransitionRole',7,'Y',1850,'2011-02-21 21:08:29','2011-02-21 21:08:29','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (339,'Атрибуты перехода','pm_TransitionAttribute',7,'N',1860,'2011-02-21 21:08:30','2011-02-21 21:08:30','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (340,'Конкурирующий продукт','pm_Competitor',8,'Y',1870,'2011-02-21 21:08:35','2011-02-21 21:08:35','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (341,'Анализ функции продукта','pm_FeatureAnalysis',8,'Y',1880,'2011-02-21 21:08:35','2011-02-21 21:08:35','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (342,'Тип страницы','WikiPageType',9,'Y',1890,'2011-04-14 07:59:48','2011-04-14 07:59:48','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (343,'Трассировка страницы','WikiPageTrace',9,'Y',1900,'2011-04-14 07:59:48','2011-04-14 07:59:48','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (344,'Ресурс','cms_Resource',15,'Y',1910,'2011-04-14 07:59:49','2011-04-14 07:59:49','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (345,'Пользовательский отчет','pm_CustomReport',6,'Y',1920,'2011-04-14 07:59:49','2011-04-14 07:59:49','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (346,'Пользовательский отчет','co_CustomReport',15,'Y',1930,'2011-04-14 07:59:50','2011-04-14 07:59:50','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (347,'Настройка пользователя','pm_UserSetting',6,'N',1940,'2011-04-14 07:59:51','2011-04-14 07:59:51','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (348,'Кластер сущности','cms_EntityCluster',1,'N',1950,'2011-06-15 08:01:38','2011-06-15 08:01:38','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (349,'Цель','sm_Aim',13,'Y',1960,'2011-08-13 18:29:28','2011-08-13 18:29:28','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (350,'Активность','sm_Activity',13,'Y',1970,'2011-08-13 18:29:28','2011-08-13 18:29:28','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (351,'Действие','sm_Action',13,'Y',1980,'2011-08-13 18:29:28','2011-08-13 18:29:28','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (352,'Персона','sm_Person',13,'Y',1990,'2011-08-13 18:29:29','2011-08-13 18:29:29','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (353,'Пользовательский атрибут','pm_CustomAttribute',6,'Y',2000,'2011-12-09 08:01:30','2011-12-09 08:01:30','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (354,'Значение атрибута','pm_AttributeValue',6,'N',2010,'2011-12-09 08:01:31','2011-12-09 08:01:31','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (355,'Проверка','cms_Checkpoint',15,'N',2020,'2012-03-20 07:59:16','2012-03-20 07:59:16','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (356,'Предикат','pm_Predicate',7,'N',2030,'2012-03-20 07:59:17','2012-03-20 07:59:17','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (357,'Предусловие на переход','pm_TransitionPredicate',7,'N',2040,'2012-03-20 07:59:17','2012-03-20 07:59:17','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (358,'Очищаемое поле','pm_TransitionResetField',7,'Y',2050,'2012-03-20 07:59:17','2012-03-20 07:59:17','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (359,'Тип почтового ящика','co_MailboxProvider',15,'Y',2060,'2012-03-20 07:59:18','2012-03-20 07:59:18','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (360,'Транспорт почты','co_MailTransport',15,'Y',2070,'2012-10-05 07:51:38','2012-10-05 07:51:38','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (361,'Дополнительные действия','pm_StateAction',7,'Y',2080,'2012-10-05 07:51:38','2012-10-05 07:51:38','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (362,'Трассировка функции','pm_FunctionTrace',8,'N',2090,'2015-03-03 16:38:08','2015-03-03 16:38:08','N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (363,'Функциональная область','pm_Workspace',19,'Y',2090,'2015-03-03 16:38:09','2015-03-03 16:38:09','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (364,'Меню','pm_WorkspaceMenu',19,'Y',2090,'2015-03-03 16:38:09','2015-03-03 16:38:09','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (365,'Пункт меню','pm_WorkspaceMenuItem',19,'Y',2090,'2015-03-03 16:38:09','2015-03-03 16:38:09','Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (366,'Атрибут состояния','pm_StateAttribute',7,'Y',10,NULL,NULL,'Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (367,'Аккаунт в СКВ','pm_SubversionUser',7,'Y',10,NULL,NULL,'Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (368,'Изменненные объекты','co_AffectedObjects',7,'N',10,NULL,NULL,'N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (369,'Изменившиеся атрибуты','ObjectChangeLogAttribute',7,'N',10,NULL,NULL,'N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (370,'Уровень функции','pm_FeatureType',7,'Y',10,NULL,NULL,'Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (371,'Компания','co_Company',7,'Y',10,NULL,NULL,'Y',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (372,'Клиент','cms_ExternalUser',7,'N',10,NULL,NULL,'N',NULL,0);
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (373,'Привязка проекта к компании','co_CompanyProject',7,'N',10,NULL,NULL,'N',NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `ObjectChangeLogAttribute`;
CREATE TABLE `ObjectChangeLogAttribute` (
  `ObjectChangeLogAttributeId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `ObjectChangeLogId` int(11) DEFAULT NULL,
  `Attributes` varchar(2048) DEFAULT NULL,
  PRIMARY KEY (`ObjectChangeLogAttributeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `ObjectChangeLogAttribute` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `package`;
CREATE TABLE `package` (
  `packageId` int(11) NOT NULL AUTO_INCREMENT,
  `Caption` mediumtext,
  `Description` mediumtext,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`packageId`),
  UNIQUE KEY `XPKpackage` (`packageId`),
  KEY `package_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;


LOCK TABLES `package` WRITE;
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (1,'Структура сайта','',10,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (2,'Управление проектами','',20,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (3,'Сообщество',NULL,NULL,'2010-06-06 18:05:33','2010-06-06 18:05:33','');
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (4,'Проект',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (5,'План работ',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (6,'Настройки проекта',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (7,'Настройки Workflow',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (8,'Продукт',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (9,'Документация',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (10,'Интеграция и развертывание',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (11,'Справочники',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (12,'Тестирование',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (13,'StoryMapping',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (14,'Расширения',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (15,'Система',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (16,'Метрики',NULL,NULL,NULL,NULL,NULL);
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (17,'Интерфейс пользователя',NULL,NULL,NULL,NULL,NULL);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_AccessRight`;
CREATE TABLE `pm_AccessRight` (
  `pm_AccessRightId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ProjectRole` int(11) DEFAULT NULL,
  `ReferenceName` varchar(32) DEFAULT NULL,
  `ReferenceType` varchar(32) DEFAULT NULL,
  `AccessType` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_AccessRightId`),
  UNIQUE KEY `UK_pm_AccessRight` (`ReferenceName`,`ReferenceType`,`ProjectRole`,`VPD`),
  KEY `I$46` (`VPD`),
  KEY `I$47` (`ReferenceName`,`ReferenceType`,`ProjectRole`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_AccessRight` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Activity`;
CREATE TABLE `pm_Activity` (
  `pm_ActivityId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Task` int(11) DEFAULT NULL,
  `Participant` int(11) DEFAULT NULL,
  `Description` mediumtext,
  `Completed` char(1) DEFAULT NULL,
  `Capacity` float DEFAULT NULL,
  `ReportDate` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `Iteration` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_ActivityId`),
  KEY `I$pm_Activity$Participant` (`Participant`),
  KEY `I$pm_Activity$ReportDate` (`ReportDate`),
  KEY `I$pm_Activity$Task` (`Task`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Activity` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Artefact`;
CREATE TABLE `pm_Artefact` (
  `pm_ArtefactId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Project` int(11) DEFAULT NULL,
  `ContentMime` mediumtext,
  `ContentPath` mediumtext,
  `ContentExt` mediumtext,
  `Description` mediumtext,
  `Kind` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Participant` int(11) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `IsArchived` char(1) DEFAULT NULL,
  `Version` varchar(32) DEFAULT NULL,
  `IsAuthorizedDownload` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ArtefactId`),
  UNIQUE KEY `XPKpm_Artefact` (`pm_ArtefactId`),
  KEY `pm_Artefact_vpd_idx` (`VPD`),
  KEY `Kind` (`Kind`,`VPD`,`Project`),
  FULLTEXT KEY `I$44` (`Caption`,`Description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Artefact` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ArtefactType`;
CREATE TABLE `pm_ArtefactType` (
  `pm_ArtefactTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `IsDisplayedOnSite` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ArtefactTypeId`),
  UNIQUE KEY `XPKpm_ArtefactType` (`pm_ArtefactTypeId`),
  KEY `pm_ArtefactType_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ArtefactType` WRITE;
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (1,30,'Документы разработки',NULL,NULL,NULL,'Y',0);
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (2,60,'Документы развертывания',NULL,NULL,NULL,'Y',0);
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (3,40,'Исходный код',NULL,NULL,NULL,'Y',0);
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (4,50,'Исполняемые файлы',NULL,'2010-06-06 18:05:51',NULL,'Y',0);
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (5,20,'Документы проектирования',NULL,NULL,NULL,'Y',0);
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (6,10,'Документы анализа',NULL,NULL,NULL,'Y',0);
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (7,15,'Документы планирования','2010-06-06 18:05:16','2010-06-06 18:05:16','','Y',0);
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (8,55,'Документы тестирования','2010-06-06 18:05:22','2010-06-06 18:05:22','','Y',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Attachment`;
CREATE TABLE `pm_Attachment` (
  `pm_AttachmentId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `FileMime` mediumtext,
  `FilePath` mediumtext,
  `FileExt` mediumtext,
  `Description` mediumtext,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_AttachmentId`),
  KEY `i$25` (`ObjectId`,`ObjectClass`,`VPD`),
  KEY `I$pm_Attachment$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Attachment` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_AttributeValue`;
CREATE TABLE `pm_AttributeValue` (
  `pm_AttributeValueId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `CustomAttribute` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `IntegerValue` int(11) DEFAULT NULL,
  `StringValue` mediumtext,
  `TextValue` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `PasswordValue` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`pm_AttributeValueId`),
  UNIQUE KEY `UK_pm_AttributeValue` (`CustomAttribute`,`ObjectId`),
  KEY `I$pm_AttributeValue$ObjectIdAttribute` (`ObjectId`,`CustomAttribute`),
  KEY `I$pm_AttributeValue$ObjectId` (`ObjectId`),
  KEY `I$pm_AttributeValue$Attribute` (`CustomAttribute`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_AttributeValue` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Bug`;
CREATE TABLE `pm_Bug` (
  `pm_BugId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `AttachmentMime` mediumtext,
  `AttachmentPath` mediumtext,
  `AttachmentExt` varchar(32) DEFAULT NULL,
  `Submitter` int(11) DEFAULT NULL,
  `State` mediumtext,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Requirement` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `Priority` int(11) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ChangeRequest` int(11) DEFAULT NULL,
  `IsPlanned` char(1) DEFAULT NULL,
  `Test` int(11) DEFAULT NULL,
  `Build` int(11) DEFAULT NULL,
  `Release` int(11) DEFAULT NULL,
  `Environment` int(11) DEFAULT NULL,
  `TestCaseExecution` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_BugId`),
  UNIQUE KEY `XPKpm_Bug` (`pm_BugId`),
  KEY `pm_Bug_vpd_idx` (`VPD`),
  KEY `ChangeRequest` (`ChangeRequest`,`VPD`),
  KEY `Requirement` (`Requirement`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Bug` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_BugSettings`;
CREATE TABLE `pm_BugSettings` (
  `pm_BugetSettingsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `IsBugetUsed` char(1) DEFAULT NULL,
  `Currency` int(11) DEFAULT NULL,
  `PaymentModel` int(11) DEFAULT NULL,
  `HideParticipantsCost` char(1) DEFAULT NULL,
  PRIMARY KEY (`pm_BugetSettingsId`),
  KEY `i$4` (`Project`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_BugSettings` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_BugetSettings`;
CREATE TABLE `pm_BugetSettings` (
  `pm_BugetSettingsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `IsBugetUsed` char(1) DEFAULT NULL,
  `Currency` int(11) DEFAULT NULL,
  `PaymentModel` int(11) DEFAULT NULL,
  `HideParticipantsCost` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_BugetSettingsId`),
  KEY `i$4` (`Project`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_BugetSettings` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Build`;
CREATE TABLE `pm_Build` (
  `pm_BuildId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` varchar(32) DEFAULT NULL,
  `Description` mediumtext,
  `Result` mediumtext,
  `Release` int(11) DEFAULT NULL,
  `Version` int(11) DEFAULT NULL,
  `IsActual` char(1) DEFAULT NULL,
  `Application` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_BuildId`),
  KEY `Release` (`Release`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Build` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_BuildTask`;
CREATE TABLE `pm_BuildTask` (
  `pm_BuildTaskId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Build` int(11) DEFAULT NULL,
  `Task` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_BuildTaskId`),
  KEY `Task` (`Task`,`Build`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_BuildTask` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_CalendarInterval`;
CREATE TABLE `pm_CalendarInterval` (
  `pm_CalendarIntervalId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Kind` varchar(16) DEFAULT NULL,
  `StartDate` datetime DEFAULT NULL,
  `FinishDate` datetime DEFAULT NULL,
  `IntervalYear` int(11) DEFAULT NULL,
  `IntervalMonth` int(11) DEFAULT NULL,
  `IntervalDay` int(11) DEFAULT NULL,
  `Caption` int(11) DEFAULT NULL,
  `IntervalQuarter` int(11) DEFAULT NULL,
  `IntervalWeek` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StartDateOnly` date DEFAULT NULL,
  `StartDateWeekday` int(11) DEFAULT NULL,
  `MinDaysInWeek` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_CalendarIntervalId`),
  KEY `I$55` (`Kind`),
  KEY `I$56` (`IntervalYear`),
  KEY `I$57` (`Caption`),
  KEY `I$pm_CalendarInterval$StartDateMul` (`Kind`,`StartDateWeekday`,`StartDateOnly`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_CalendarInterval` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ChangeRequest`;
CREATE TABLE `pm_ChangeRequest` (
  `pm_ChangeRequestId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Priority` int(11) DEFAULT NULL,
  `Author` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Function` int(11) DEFAULT NULL,
  `Estimation` float DEFAULT NULL,
  `Owner` int(11) DEFAULT NULL,
  `Type` int(11) DEFAULT NULL,
  `PlannedRelease` int(11) DEFAULT NULL,
  `Environment` int(11) DEFAULT NULL,
  `SubmittedVersion` varchar(255) DEFAULT NULL,
  `ClosedInVersion` varchar(255) DEFAULT NULL,
  `State` varchar(32) DEFAULT NULL,
  `LifecycleDuration` int(11) DEFAULT NULL,
  `StartDate` timestamp NULL DEFAULT NULL,
  `FinishDate` timestamp NULL DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `EstimationLeft` float DEFAULT NULL,
  `StateObject` int(11) DEFAULT NULL,
  `DeliveryDate` datetime DEFAULT NULL,
  `Severity` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_ChangeRequestId`),
  UNIQUE KEY `XPKpm_ChangeRequest` (`pm_ChangeRequestId`),
  KEY `pm_ChangeRequest_vpd_idx` (`VPD`),
  KEY `VPD` (`VPD`),
  KEY `Release` (`VPD`),
  KEY `Function` (`Function`,`VPD`),
  KEY `i$13` (`PlannedRelease`),
  KEY `i$16` (`Project`),
  KEY `i$38` (`SubmittedVersion`),
  KEY `I$AUTHOR` (`Author`),
  KEY `I$pm_ChangeRequest$State` (`State`),
  FULLTEXT KEY `I$42` (`Caption`,`Description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ChangeRequest` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ChangeRequestLink`;
CREATE TABLE `pm_ChangeRequestLink` (
  `pm_ChangeRequestLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `SourceRequest` int(11) DEFAULT NULL,
  `TargetRequest` int(11) DEFAULT NULL,
  `LinkType` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ChangeRequestLinkId`),
  KEY `I$53` (`SourceRequest`),
  KEY `I$54` (`TargetRequest`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ChangeRequestLink` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ChangeRequestLinkType`;
CREATE TABLE `pm_ChangeRequestLinkType` (
  `pm_ChangeRequestLinkTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `BackwardCaption` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pm_ChangeRequestLinkTypeId`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ChangeRequestLinkType` WRITE;
INSERT INTO `pm_ChangeRequestLinkType` (`pm_ChangeRequestLinkTypeId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`, `BackwardCaption`) VALUES (1,'2010-06-06 18:05:22','2010-06-06 18:05:22','',10,'Дубликат','duplicates',0,'Дубликат');
INSERT INTO `pm_ChangeRequestLinkType` (`pm_ChangeRequestLinkTypeId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`, `BackwardCaption`) VALUES (2,'2010-06-06 18:05:26','2010-06-06 18:05:26','',20,'Зависимость','dependency',0,'Зависимость');
INSERT INTO `pm_ChangeRequestLinkType` (`pm_ChangeRequestLinkTypeId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`, `BackwardCaption`) VALUES (3,'2010-06-06 18:05:26','2010-06-06 18:05:26','',30,'Блокируется','blocked',0,'Блокирует');
INSERT INTO `pm_ChangeRequestLinkType` (`pm_ChangeRequestLinkTypeId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`, `BackwardCaption`) VALUES (4,'2010-06-06 18:05:26','2010-06-06 18:05:26','',40,'Блокирует','blocks',0,'Блокируется');
INSERT INTO `pm_ChangeRequestLinkType` (`pm_ChangeRequestLinkTypeId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`, `BackwardCaption`) VALUES (5,NULL,NULL,NULL,50,'Реализация','implemented',0,'Реализует');
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ChangeRequestTrace`;
CREATE TABLE `pm_ChangeRequestTrace` (
  `pm_ChangeRequestTraceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ChangeRequest` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(255) DEFAULT NULL,
  `IsActual` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `Type` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`pm_ChangeRequestTraceId`),
  KEY `I$ChangeRequestTrace$Request` (`ChangeRequest`),
  KEY `I$ChangeRequestTrace$Object` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ChangeRequestTrace` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Competitor`;
CREATE TABLE `pm_Competitor` (
  `pm_CompetitorId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_CompetitorId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Competitor` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Configuration`;
CREATE TABLE `pm_Configuration` (
  `pm_ConfigurationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Details` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ConfigurationId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Configuration` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Currency`;
CREATE TABLE `pm_Currency` (
  `pm_CurrencyId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `CodeName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_CurrencyId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Currency` WRITE;
INSERT INTO `pm_Currency` (`pm_CurrencyId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:54','2010-06-06 18:05:54','',10,'Рубль','RUB',0);
INSERT INTO `pm_Currency` (`pm_CurrencyId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:54','2010-06-06 18:05:54','',20,'Доллар США','USD',0);
INSERT INTO `pm_Currency` (`pm_CurrencyId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `RecordVersion`) VALUES (3,'2010-06-06 18:05:54','2010-06-06 18:05:54','',30,'Евро','EUR',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_CustomAttribute`;
CREATE TABLE `pm_CustomAttribute` (
  `pm_CustomAttributeId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `EntityReferenceName` mediumtext,
  `AttributeType` int(11) DEFAULT NULL,
  `DefaultValue` mediumtext,
  `IsVisible` char(1) DEFAULT NULL,
  `ValueRange` mediumtext,
  `IsRequired` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `ObjectKind` varchar(128) DEFAULT NULL,
  `Description` mediumtext,
  `IsUnique` char(1) DEFAULT NULL,
  `AttributeTypeClassName` varchar(255) DEFAULT NULL,
  `Capacity` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_CustomAttributeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_CustomAttribute` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_CustomReport`;
CREATE TABLE `pm_CustomReport` (
  `pm_CustomReportId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Category` mediumtext,
  `Url` mediumtext,
  `Description` mediumtext,
  `IsHandAccess` char(1) DEFAULT NULL,
  `Author` int(11) DEFAULT NULL,
  `ReportBase` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `Module` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`pm_CustomReportId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_CustomReport` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_CustomTag`;
CREATE TABLE `pm_CustomTag` (
  `pm_CustomTagId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Tag` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_CustomTagId`),
  KEY `I$pm_CustomTag$Tag` (`Tag`),
  KEY `I$pm_CustomTag$Object` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_CustomTag` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Deadline`;
CREATE TABLE `pm_Deadline` (
  `pm_DeadlineId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Deadline` datetime DEFAULT NULL,
  `Comment` mediumtext,
  `ChangeRequest` int(11) DEFAULT NULL,
  `Milestone` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_DeadlineId`),
  KEY `i$35` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Deadline` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_DownloadAction`;
CREATE TABLE `pm_DownloadAction` (
  `pm_DownloadActionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `ObjectId` int(11) DEFAULT NULL,
  `EntityRefName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_DownloadActionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_DownloadAction` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_DownloadActor`;
CREATE TABLE `pm_DownloadActor` (
  `pm_DownloadActorId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `SystemUser` int(11) DEFAULT NULL,
  `DownloadAction` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_DownloadActorId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_DownloadActor` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Enhancement`;
CREATE TABLE `pm_Enhancement` (
  `pm_EnhancementId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Requirement` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `AttachmentMime` mediumtext,
  `AttachmentPath` mediumtext,
  `AttachmentExt` varchar(32) DEFAULT NULL,
  `Submitter` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Priority` int(11) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ChangeRequest` int(11) DEFAULT NULL,
  `IsPlanned` char(1) DEFAULT NULL,
  `TestCaseExecution` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_EnhancementId`),
  UNIQUE KEY `XPKpm_Enhancement` (`pm_EnhancementId`),
  KEY `pm_Enhancement_vpd_idx` (`VPD`),
  KEY `ChangeRequest` (`ChangeRequest`,`VPD`),
  KEY `Requirement` (`Requirement`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Enhancement` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Environment`;
CREATE TABLE `pm_Environment` (
  `pm_EnvironmentId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_EnvironmentId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Environment` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_FeatureAnalysis`;
CREATE TABLE `pm_FeatureAnalysis` (
  `pm_FeatureAnalysisId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Competitor` int(11) DEFAULT NULL,
  `Feature` int(11) DEFAULT NULL,
  `Strengths` mediumtext,
  `Weaknesses` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_FeatureAnalysisId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_FeatureAnalysis` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_FeatureType`;
CREATE TABLE `pm_FeatureType` (
  `pm_FeatureTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` varchar(255) DEFAULT NULL,
  `ReferenceName` varchar(255) DEFAULT NULL,
  `Description` mediumtext,
  `HasIssues` char(1) DEFAULT 'Y',
  `ChildrenLevels` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pm_FeatureTypeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_FeatureType` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Function`;
CREATE TABLE `pm_Function` (
  `pm_FunctionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Importance` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `Type` int(11) DEFAULT NULL,
  `ParentFeature` int(11) DEFAULT NULL,
  `ParentPath` mediumtext,
  `SortIndex` mediumtext,
  `Estimation` int(11) DEFAULT NULL,
  `Workload` int(11) DEFAULT NULL,
  `StartDate` datetime DEFAULT NULL,
  `DeliveryDate` datetime DEFAULT NULL,
  `EstimationLeft` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_FunctionId`),
  KEY `VPD` (`VPD`),
  FULLTEXT KEY `I$pm_Function$ParentPath` (`ParentPath`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Function` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_FunctionTrace`;
CREATE TABLE `pm_FunctionTrace` (
  `pm_FunctionTraceId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Feature` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`pm_FunctionTraceId`),
  KEY `I$pm_FunctionTrace$Object` (`ObjectId`,`ObjectClass`),
  KEY `I$pm_FunctionTrace$Feature` (`Feature`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_FunctionTrace` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Help`;
CREATE TABLE `pm_Help` (
  `pm_HelpId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Version` varchar(32) DEFAULT NULL,
  `Content` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_HelpId`),
  UNIQUE KEY `XPKpm_Help` (`pm_HelpId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Help` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_HelpDeactReason`;
CREATE TABLE `pm_HelpDeactReason` (
  `pm_HelpDeactReasonId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `HelpLink` int(11) DEFAULT NULL,
  `Task` int(11) DEFAULT NULL,
  `IsActive` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_HelpDeactReasonId`),
  UNIQUE KEY `XPKpm_HelpDeactReason` (`pm_HelpDeactReasonId`),
  KEY `HelpLink` (`HelpLink`,`Task`,`IsActive`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_HelpDeactReason` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Importance`;
CREATE TABLE `pm_Importance` (
  `pm_ImportanceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ImportanceId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Importance` WRITE;
INSERT INTO `pm_Importance` (`pm_ImportanceId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `RecordVersion`) VALUES (1,NULL,NULL,NULL,10,'Обязательно','text(885)',0);
INSERT INTO `pm_Importance` (`pm_ImportanceId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `RecordVersion`) VALUES (2,NULL,NULL,NULL,20,'Важно','text(886)',0);
INSERT INTO `pm_Importance` (`pm_ImportanceId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `RecordVersion`) VALUES (3,NULL,NULL,NULL,30,'Желательно','text(887)',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Invitation`;
CREATE TABLE `pm_Invitation` (
  `pm_InvitationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `Author` int(11) DEFAULT NULL,
  `Addressee` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_InvitationId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Invitation` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_IssueType`;
CREATE TABLE `pm_IssueType` (
  `pm_IssueTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `RelatedColor` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`pm_IssueTypeId`),
  KEY `I$pm_IssueType$VPD` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_IssueType` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_IterationMetric`;
CREATE TABLE `pm_IterationMetric` (
  `pm_IterationMetricId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Iteration` int(11) DEFAULT NULL,
  `Metric` varchar(32) DEFAULT NULL,
  `MetricValue` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `MetricValueDate` datetime DEFAULT NULL,
  PRIMARY KEY (`pm_IterationMetricId`),
  KEY `i$18` (`Iteration`,`Metric`),
  KEY `I$pm_IterationMetric$Date` (`MetricValueDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_IterationMetric` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Meeting`;
CREATE TABLE `pm_Meeting` (
  `pm_MeetingId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Subject` mediumtext,
  `Location` mediumtext,
  `VPD` varchar(32) DEFAULT NULL,
  `MeetingDate` datetime DEFAULT NULL,
  `Agenda` mediumtext,
  `MeetingTime` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `RememberInterval` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_MeetingId`),
  UNIQUE KEY `XPKpm_Meeting` (`pm_MeetingId`),
  KEY `pm_Meeting_vpd_idx` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Meeting` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_MeetingParticipant`;
CREATE TABLE `pm_MeetingParticipant` (
  `pm_MeetingParticipantId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Meeting` int(11) DEFAULT NULL,
  `Participant` int(11) DEFAULT NULL,
  `Accepted` char(1) DEFAULT NULL,
  `Rejected` char(1) DEFAULT NULL,
  `RejectReason` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `RememberInterval` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_MeetingParticipantId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_MeetingParticipant` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Methodology`;
CREATE TABLE `pm_Methodology` (
  `pm_MethodologyId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `IsRequirements` char(1) DEFAULT NULL,
  `IsHelps` char(1) DEFAULT NULL,
  `IsTests` char(1) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `IsBuilds` char(1) DEFAULT NULL,
  `IsUserInProject` char(1) DEFAULT NULL,
  `IsFixedRelease` char(1) DEFAULT NULL,
  `ReleaseDuration` int(11) DEFAULT NULL,
  `IsTasksDepend` char(1) DEFAULT NULL,
  `IsResponsibleForFunctions` char(1) DEFAULT NULL,
  `IsCrossChecking` char(1) DEFAULT NULL,
  `IsDesign` char(1) DEFAULT NULL,
  `IsHighTolerance` char(1) DEFAULT NULL,
  `VerificationTime` int(11) DEFAULT NULL,
  `RequestApproveRequired` char(1) DEFAULT NULL,
  `UseScrums` char(1) DEFAULT NULL,
  `UseEnvironments` char(1) DEFAULT NULL,
  `HasMilestones` char(1) DEFAULT NULL,
  `RequestEstimationRequired` varchar(255) DEFAULT NULL,
  `IsParticipantsTakeTasks` char(1) DEFAULT NULL,
  `UseFunctionalDecomposition` char(1) DEFAULT NULL,
  `IsDeadlineUsed` char(1) DEFAULT NULL,
  `IsPlanningUsed` char(1) DEFAULT NULL,
  `IsReportsOnActivities` char(1) DEFAULT NULL,
  `CustomerAcceptsIssues` char(1) DEFAULT NULL,
  `IsResponsibleForIssue` char(1) DEFAULT NULL,
  `IsVersionsUsed` char(1) DEFAULT NULL,
  `IsReleasesUsed` char(1) DEFAULT NULL,
  `IsKanbanUsed` char(1) DEFAULT NULL,
  `IsStoryMappingUsed` char(1) DEFAULT NULL,
  `IsRequestOrderUsed` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `TaskEstimationUsed` char(1) DEFAULT NULL,
  `IsTasks` char(1) DEFAULT NULL,
  PRIMARY KEY (`pm_MethodologyId`),
  KEY `i$3` (`Project`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Methodology` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Milestone`;
CREATE TABLE `pm_Milestone` (
  `pm_MilestoneId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `MilestoneDate` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `Passed` char(1) DEFAULT NULL,
  `ReasonToChangeDate` mediumtext,
  `CompleteResult` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_MilestoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Milestone` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_NewsChannel`;
CREATE TABLE `pm_NewsChannel` (
  `pm_NewsChannelId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `RssLink` mediumtext,
  `Language` mediumtext,
  `IsPublic` mediumtext,
  PRIMARY KEY (`pm_NewsChannelId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_NewsChannel` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_NewsChannelItem`;
CREATE TABLE `pm_NewsChannelItem` (
  `pm_NewsChannelItemId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `HtmlLink` mediumtext,
  `NewsChannel` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_NewsChannelItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_NewsChannelItem` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_NewsChannelSubscription`;
CREATE TABLE `pm_NewsChannelSubscription` (
  `pm_NewsChannelSubscriptionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `NewsChannel` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_NewsChannelSubscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_NewsChannelSubscription` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ObjectAccess`;
CREATE TABLE `pm_ObjectAccess` (
  `pm_ObjectAccessId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ObjectClass` varchar(128) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ProjectRole` int(11) DEFAULT NULL,
  `AccessType` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ObjectAccessId`),
  KEY `pm_ObjectAccess$Role` (`ProjectRole`),
  KEY `pm_ObjectAccess$Object` (`ObjectId`),
  KEY `pm_ObjectAccess$Class` (`ObjectClass`),
  KEY `pm_ObjectAccess$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ObjectAccess` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Participant`;
CREATE TABLE `pm_Participant` (
  `pm_ParticipantId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Email` mediumtext,
  `Login` mediumtext,
  `Password` mediumtext,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `ICQNumber` mediumtext,
  `Capacity` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `HomePhone` mediumtext,
  `MobilePhone` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `OverrideUser` char(1) DEFAULT NULL,
  `IsActive` char(1) DEFAULT NULL,
  `Skype` mediumtext,
  `Salary` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ParticipantId`),
  UNIQUE KEY `XPKpm_Participant` (`pm_ParticipantId`),
  KEY `pm_Participant_vpd_idx` (`VPD`),
  KEY `Project` (`Project`,`Login`(20)),
  KEY `SystemUser` (`SystemUser`),
  KEY `i$20` (`IsActive`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Participant` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ParticipantMetrics`;
CREATE TABLE `pm_ParticipantMetrics` (
  `pm_ParticipantMetricsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Participant` int(11) DEFAULT NULL,
  `Iteration` int(11) DEFAULT NULL,
  `Metric` varchar(32) DEFAULT NULL,
  `MetricValue` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ParticipantMetricsId`),
  KEY `I$pm_ParticipantMetrics$IP` (`Iteration`,`Participant`),
  KEY `I$pm_ParticipantMetrics$IPM` (`Iteration`,`Participant`,`Metric`),
  KEY `i$6` (`Participant`,`Iteration`,`Metric`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ParticipantMetrics` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ParticipantRole`;
CREATE TABLE `pm_ParticipantRole` (
  `pm_ParticipantRoleId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Participant` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `ProjectRole` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Capacity` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ParticipantRoleId`),
  UNIQUE KEY `XPKpm_ParticipantRole` (`pm_ParticipantRoleId`),
  KEY `pm_ParticipantRole_vpd_idx` (`VPD`),
  KEY `Participant` (`Participant`,`ProjectRole`,`VPD`),
  KEY `i$19` (`Project`,`Participant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ParticipantRole` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_PaymentModel`;
CREATE TABLE `pm_PaymentModel` (
  `pm_PaymentModelId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_PaymentModelId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_PaymentModel` WRITE;
INSERT INTO `pm_PaymentModel` (`pm_PaymentModelId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:54','2010-06-06 18:05:54','',10,'Почасовая оплата',0);
INSERT INTO `pm_PaymentModel` (`pm_PaymentModelId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:54','2010-06-06 18:05:54','',20,'Ежемесячная оплата',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Poll`;
CREATE TABLE `pm_Poll` (
  `pm_PollId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `IsPublic` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_PollId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Poll` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_PollItem`;
CREATE TABLE `pm_PollItem` (
  `pm_PollItemId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Answers` mediumtext,
  `IsSection` char(1) DEFAULT NULL,
  `Poll` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_PollItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_PollItem` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_PollItemResult`;
CREATE TABLE `pm_PollItemResult` (
  `pm_PollItemResultId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `PollItem` int(11) DEFAULT NULL,
  `PollResult` int(11) DEFAULT NULL,
  `Answer` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_PollItemResultId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_PollItemResult` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_PollResult`;
CREATE TABLE `pm_PollResult` (
  `pm_PollResultId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Poll` int(11) DEFAULT NULL,
  `User` int(11) DEFAULT NULL,
  `IsCurrent` char(1) DEFAULT NULL,
  `CommitDate` datetime DEFAULT NULL,
  `AnonymousHash` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_PollResultId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_PollResult` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Predicate`;
CREATE TABLE `pm_Predicate` (
  `pm_PredicateId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  PRIMARY KEY (`pm_PredicateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Predicate` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Project`;
CREATE TABLE `pm_Project` (
  `pm_ProjectId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `CodeName` varchar(32) DEFAULT NULL,
  `Platform` mediumtext,
  `Tools` mediumtext,
  `MainWikiPage` int(11) DEFAULT NULL,
  `RequirementsWikiPage` int(11) DEFAULT NULL,
  `StartDate` date DEFAULT NULL,
  `FinishDate` date DEFAULT NULL,
  `BudgetCode` varchar(32) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Version` int(11) DEFAULT NULL,
  `Blog` int(11) DEFAULT NULL,
  `Language` int(11) DEFAULT NULL,
  `IsConfigurations` char(1) DEFAULT NULL,
  `IsClosed` char(1) DEFAULT NULL,
  `Rating` float DEFAULT NULL,
  `IsTender` char(1) DEFAULT NULL,
  `IsSubversionUsed` char(1) DEFAULT NULL,
  `IsFileServer` char(1) DEFAULT NULL,
  `HasMeetings` char(1) DEFAULT NULL,
  `IsPollUsed` char(1) DEFAULT NULL,
  `IsKnowledgeUsed` char(1) DEFAULT NULL,
  `IsBlogUsed` char(1) DEFAULT NULL,
  `WikiEditorClass` mediumtext,
  `DaysInWeek` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `IsSupportUsed` char(1) DEFAULT 'N',
  PRIMARY KEY (`pm_ProjectId`),
  UNIQUE KEY `XPKpm_Project` (`pm_ProjectId`),
  KEY `pm_Project_vpd_idx` (`VPD`),
  KEY `i$1` (`CodeName`),
  KEY `I$pm_Project$VPD` (`VPD`),
  FULLTEXT KEY `Caption` (`Caption`,`Description`),
  FULLTEXT KEY `I$40` (`Caption`,`Description`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Project` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ProjectCreation`;
CREATE TABLE `pm_ProjectCreation` (
  `pm_ProjectCreationId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `IPAddress` mediumtext,
  `Project` int(11) DEFAULT NULL,
  `CodeName` mediumtext,
  `Caption` mediumtext,
  `Login` mediumtext,
  `Email` mediumtext,
  `Password` mediumtext,
  `Methodology` mediumtext,
  `CreationHash` mediumtext,
  `Language` mediumtext,
  `Access` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ProjectCreationId`),
  UNIQUE KEY `XPKpm_ProjectCreation` (`pm_ProjectCreationId`),
  KEY `i$34` (`SystemUser`,`Project`),
  KEY `I$50` (`Project`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ProjectCreation` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ProjectLink`;
CREATE TABLE `pm_ProjectLink` (
  `pm_ProjectLinkId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Target` int(11) DEFAULT NULL,
  `Source` int(11) DEFAULT NULL,
  `KnowledgeBase` int(11) DEFAULT NULL,
  `Blog` int(11) DEFAULT NULL,
  `Requirements` int(11) DEFAULT NULL,
  `Testing` int(11) DEFAULT NULL,
  `HelpFiles` int(11) DEFAULT NULL,
  `Files` int(11) DEFAULT NULL,
  `SourceCode` int(11) DEFAULT NULL,
  `Requests` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `Releases` int(11) DEFAULT NULL,
  `Tasks` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_ProjectLinkId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ProjectLink` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ProjectRole`;
CREATE TABLE `pm_ProjectRole` (
  `pm_ProjectRoleId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ReferenceName` varchar(32) DEFAULT NULL,
  `ProjectRoleBase` int(11) DEFAULT NULL,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ProjectRoleId`),
  UNIQUE KEY `XPKpm_ProjectRole` (`pm_ProjectRoleId`),
  KEY `pm_ProjectRole_vpd_idx` (`VPD`),
  KEY `pm_ProjectRole$RefVPD` (`VPD`,`ReferenceName`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ProjectRole` WRITE;
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (1,10,'Аналитик',NULL,'2010-06-06 18:06:11',NULL,'analyst',NULL,NULL,0);
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (2,20,'Разработчик',NULL,'2010-06-06 18:06:11',NULL,'developer',NULL,NULL,0);
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (3,30,'Тестировщик',NULL,'2010-06-06 18:06:11',NULL,'tester',NULL,NULL,0);
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (4,40,'Координатор',NULL,'2010-06-06 18:06:11',NULL,'lead',NULL,NULL,0);
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (5,50,'Заказчик',NULL,'2010-06-06 18:06:11',NULL,'client',NULL,NULL,0);
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (6,15,'Проектировщик','2010-06-06 18:05:07','2010-06-06 18:05:07',NULL,NULL,NULL,NULL,0);
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (7,35,'Технический писатель','2010-06-06 18:05:07','2010-06-06 18:06:11',NULL,'writer',NULL,NULL,0);
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (8,15,'Архитектор',NULL,NULL,NULL,'architect',NULL,NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ProjectStage`;
CREATE TABLE `pm_ProjectStage` (
  `pm_ProjectStageId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ProjectStageId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ProjectStage` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ProjectTag`;
CREATE TABLE `pm_ProjectTag` (
  `pm_ProjectTagId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Project` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ProjectTagId`),
  FULLTEXT KEY `I$41` (`Caption`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ProjectTag` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ProjectTemplate`;
CREATE TABLE `pm_ProjectTemplate` (
  `pm_ProjectTemplateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `FileName` mediumtext,
  `IsDefault` char(1) DEFAULT NULL,
  `Language` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `ProductEdition` mediumtext,
  `Kind` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`pm_ProjectTemplateId`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ProjectTemplate` WRITE;
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (31,'2010-08-17 08:36:48','2010-08-17 08:36:48',NULL,130,'text(co5)','text(co6)','sdlc_ru.xml','N',1,0,'ee','methodology');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (30,'2010-08-17 08:29:39','2010-08-17 08:29:39',NULL,120,'text(co5)','text(co6)','sdlc_en.xml','N',2,0,'ee','methodology');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (37,'2010-08-17 09:02:17','2010-08-17 09:02:17',NULL,60,'text(co11)','text(co12)','ticket_en.xml','N',2,0,'ee','case');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (38,'2010-08-17 09:03:09','2010-08-17 09:03:09',NULL,60,'text(co11)','text(co12)','ticket_ru.xml','N',1,0,'ee','case');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (39,'2010-08-17 09:03:09','2010-08-17 09:03:09',NULL,190,'text(co3)','text(co4)','openup_en.xml','N',2,0,'ee','methodology');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (50,NULL,NULL,NULL,40,'text(co17)','text(co18)','testing_ru.xml',NULL,1,0,'ee','case');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (51,NULL,NULL,NULL,50,'text(co19)','text(co20)','docs_ru.xml',NULL,1,0,'ee','case');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (52,NULL,NULL,NULL,45,'text(co21)','text(co22)','tracker_ru.xml',NULL,1,0,'team','process');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (41,NULL,NULL,NULL,20,'text(co7)','text(co8)','scrum_ru.xml','N',1,0,'team','methodology');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (48,NULL,NULL,NULL,20,'text(co13)','text(co14)','ba_ru.xml',NULL,1,0,'ee','case');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (42,NULL,NULL,NULL,20,'text(co7)','text(co8)','scrum_en.xml',NULL,2,0,'team','methodology');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (44,NULL,NULL,NULL,10,'text(co9)','text(co10)','kanban_ru.xml',NULL,1,0,'team','methodology');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (45,NULL,NULL,NULL,5,'text(co1)','text(co2)','tasks_ru.xml',NULL,1,0,'team','process');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (46,NULL,NULL,NULL,200,'text(co3)','text(co4)','openup_ru.xml',NULL,1,0,'ee','methodology');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (49,NULL,NULL,NULL,30,'text(co15)','text(co16)','reqs_ru.xml',NULL,1,0,'ee','case');
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`, `ProductEdition`, `Kind`) VALUES (47,NULL,NULL,NULL,10,'text(co9)','text(co10)','kanban_en.xml',NULL,2,0,'team','methodology');
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ProjectUse`;
CREATE TABLE `pm_ProjectUse` (
  `pm_ProjectUseId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `Participant` int(11) DEFAULT NULL,
  `SessionHash` varchar(36) DEFAULT NULL,
  `PrevLoginDate` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `Timezone` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`pm_ProjectUseId`),
  UNIQUE KEY `XPKpm_ProjectUse` (`pm_ProjectUseId`),
  KEY `Participant` (`Participant`,`SessionHash`,`VPD`),
  KEY `SessionHash` (`SessionHash`,`VPD`),
  KEY `i$37` (`SessionHash`,`RecordModified`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ProjectUse` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_PublicInfo`;
CREATE TABLE `pm_PublicInfo` (
  `pm_PublicInfoId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `IsProjectInfo` char(1) DEFAULT NULL,
  `IsParticipants` char(1) DEFAULT NULL,
  `IsBlog` char(1) DEFAULT NULL,
  `IsKnowledgeBase` char(1) DEFAULT NULL,
  `IsReleases` char(1) DEFAULT NULL,
  `IsChangeRequests` char(1) DEFAULT NULL,
  `IsPublicDocumentation` char(1) DEFAULT NULL,
  `IsPublicArtefacts` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_PublicInfoId`),
  KEY `i$2` (`Project`),
  KEY `I$49` (`Project`),
  KEY `pm_PublicInfo$VPD` (`VPD`),
  KEY `pm_PublicInfo$ProjectVPD` (`Project`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_PublicInfo` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Question`;
CREATE TABLE `pm_Question` (
  `pm_QuestionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Content` mediumtext,
  `Author` int(11) DEFAULT NULL,
  `State` varchar(32) DEFAULT NULL,
  `Owner` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StateObject` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_QuestionId`),
  KEY `I$pm_Question$State` (`State`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Question` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Release`;
CREATE TABLE `pm_Release` (
  `pm_ReleaseId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `ReleaseNumber` varchar(32) DEFAULT NULL,
  `Description` mediumtext,
  `Project` int(11) DEFAULT NULL,
  `StartDate` datetime DEFAULT NULL,
  `FinishDate` datetime DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `IsCurrent` char(1) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Version` int(11) DEFAULT NULL,
  `IsDraft` char(1) DEFAULT NULL,
  `IsActual` char(1) DEFAULT NULL,
  `ProjectStage` int(11) DEFAULT NULL,
  `InitialVelocity` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ReleaseId`),
  UNIQUE KEY `XPKpm_Release` (`pm_ReleaseId`),
  KEY `pm_Release_vpd_idx` (`VPD`),
  KEY `Project` (`Project`,`Version`,`IsCurrent`,`VPD`),
  KEY `Project_2` (`Project`,`VPD`),
  KEY `i$5` (`Version`),
  KEY `i$26` (`Project`),
  KEY `I$pm_Release$StartDate` (`StartDate`),
  KEY `I$pm_Release$FinishDate` (`FinishDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Release` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ReleaseMetrics`;
CREATE TABLE `pm_ReleaseMetrics` (
  `pm_ReleaseMetricsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Release` int(11) DEFAULT NULL,
  `SnapshotDate` datetime DEFAULT NULL,
  `Workload` float DEFAULT NULL,
  `LeftWorkload` float DEFAULT NULL,
  `SnapshotDays` int(11) DEFAULT NULL,
  `PlannedWorkload` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `TaskType` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_ReleaseMetricsId`),
  KEY `i$21` (`Release`,`SnapshotDays`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ReleaseMetrics` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_ReleaseNote`;
CREATE TABLE `pm_ReleaseNote` (
  `pm_ReleaseNoteId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Release` int(11) DEFAULT NULL,
  `ChangeRequest` int(11) DEFAULT NULL,
  `Content` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ReleaseNoteId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_ReleaseNote` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_RequestTag`;
CREATE TABLE `pm_RequestTag` (
  `pm_RequestTagId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Request` int(11) DEFAULT NULL,
  `Tag` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_RequestTagId`),
  KEY `Request` (`Request`,`Tag`,`VPD`),
  KEY `i$15` (`VPD`),
  KEY `i$17` (`VPD`,`Tag`),
  KEY `I$pm_RequestTag$Tag` (`Tag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_RequestTag` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_RequirementState`;
CREATE TABLE `pm_RequirementState` (
  `pm_RequirementStateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_RequirementStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_RequirementState` WRITE;
INSERT INTO `pm_RequirementState` (`pm_RequirementStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:31','2010-06-06 18:05:31','',10,'В работе',0);
INSERT INTO `pm_RequirementState` (`pm_RequirementStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (2,'2010-06-06 18:05:31','2010-06-06 18:05:31','',20,'Готово',0);
INSERT INTO `pm_RequirementState` (`pm_RequirementStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (3,'2010-06-06 18:05:31','2010-06-06 18:05:31','',30,'Подписано',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Scrum`;
CREATE TABLE `pm_Scrum` (
  `pm_ScrumId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `WasYesterday` mediumtext,
  `WhatToday` mediumtext,
  `CurrentProblems` mediumtext,
  `Participant` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_ScrumId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Scrum` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_State`;
CREATE TABLE `pm_State` (
  `pm_StateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `IsTerminal` char(1) DEFAULT NULL,
  `ReferenceName` varchar(32) DEFAULT NULL,
  `QueueLength` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `RelatedColor` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`pm_StateId`),
  KEY `I$pm_State$VPD` (`VPD`),
  KEY `I$pm_State$Class` (`ObjectClass`),
  KEY `I$pm_State$Reference` (`ReferenceName`),
  KEY `I$pm_State$ObjectClass` (`ObjectClass`),
  KEY `I$pm_State$ClassVpd` (`ObjectClass`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_State` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_StateAction`;
CREATE TABLE `pm_StateAction` (
  `pm_StateActionId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `State` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_StateActionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_StateAction` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_StateAttribute`;
CREATE TABLE `pm_StateAttribute` (
  `pm_StateAttributeId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `State` int(11) DEFAULT NULL,
  `ReferenceName` varchar(128) DEFAULT NULL,
  `Entity` varchar(128) DEFAULT NULL,
  `IsVisible` char(1) DEFAULT NULL,
  `IsRequired` char(1) DEFAULT NULL,
  PRIMARY KEY (`pm_StateAttributeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_StateAttribute` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_StateObject`;
CREATE TABLE `pm_StateObject` (
  `pm_StateObjectId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(32) DEFAULT NULL,
  `State` int(11) DEFAULT NULL,
  `Comment` mediumtext,
  `Transition` int(11) DEFAULT NULL,
  `Author` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `Duration` float DEFAULT NULL,
  PRIMARY KEY (`pm_StateObjectId`),
  KEY `I$pm_StateObject$VPD` (`VPD`),
  KEY `I$pm_StateObject$Object` (`ObjectId`),
  KEY `I$pm_StateObject$Class` (`ObjectClass`),
  KEY `I$pm_StateObject$State` (`State`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_StateObject` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Subversion`;
CREATE TABLE `pm_Subversion` (
  `pm_SubversionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `SVNPath` mediumtext,
  `LoginName` mediumtext,
  `SVNPassword` mediumtext,
  `RootPath` mediumtext,
  `ConnectorClass` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `Caption` mediumtext,
  PRIMARY KEY (`pm_SubversionId`),
  KEY `I$pm_Subversion$Project` (`Project`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Subversion` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_SubversionRevision`;
CREATE TABLE `pm_SubversionRevision` (
  `pm_SubversionRevisionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `Version` varchar(255) DEFAULT NULL,
  `VersionNum` int(11) unsigned DEFAULT NULL,
  `Description` mediumtext,
  `Author` mediumtext,
  `CommitDate` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `Repository` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_SubversionRevisionId`),
  KEY `i$36` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_SubversionRevision` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_SubversionUser`;
CREATE TABLE `pm_SubversionUser` (
  `pm_SubversionUserId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `SystemUser` int(11) DEFAULT NULL,
  `Connector` int(11) DEFAULT NULL,
  `UserName` varchar(255) DEFAULT NULL,
  `UserPassword` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`pm_SubversionUserId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_SubversionUser` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Task`;
CREATE TABLE `pm_Task` (
  `pm_TaskId` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNum` int(11) DEFAULT NULL,
  `Release` int(11) DEFAULT NULL,
  `Comments` mediumtext,
  `Assignee` int(11) DEFAULT NULL,
  `Planned` float DEFAULT NULL,
  `Fact` float DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `TaskType` int(11) DEFAULT NULL,
  `Priority` int(11) DEFAULT NULL,
  `Result` mediumtext,
  `Controller` int(11) DEFAULT NULL,
  `ChangeRequest` int(11) DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Caption` mediumtext,
  `PrecedingTask` int(11) DEFAULT NULL,
  `LeftWork` float DEFAULT NULL,
  `State` varchar(32) DEFAULT NULL,
  `StartDate` timestamp NULL DEFAULT NULL,
  `FinishDate` timestamp NULL DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StateObject` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_TaskId`),
  UNIQUE KEY `XPKpm_Task` (`pm_TaskId`),
  KEY `pm_Task_vpd_idx` (`VPD`),
  KEY `Release` (`Release`,`TaskType`,`VPD`),
  KEY `Bug` (`TaskType`,`VPD`),
  KEY `Enhancement` (`TaskType`,`VPD`),
  KEY `ChangeRequest` (`ChangeRequest`,`TaskType`,`VPD`),
  KEY `Assignee` (`Assignee`,`TaskType`,`VPD`),
  KEY `Controller` (`Controller`,`TaskType`,`VPD`),
  KEY `TaskType` (`TaskType`,`VPD`),
  KEY `ResultRequirement1` (`VPD`),
  KEY `RecordModified` (`RecordModified`,`VPD`),
  KEY `RecordCreated` (`RecordCreated`,`VPD`),
  KEY `PrecedingTask` (`PrecedingTask`,`VPD`),
  KEY `i$23` (`Release`),
  KEY `I$pm_Task$State` (`State`),
  FULLTEXT KEY `I$45` (`Caption`,`Comments`,`Result`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Task` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TaskState`;
CREATE TABLE `pm_TaskState` (
  `pm_TaskStateId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TaskStateId`),
  UNIQUE KEY `XPKpm_TaskState` (`pm_TaskStateId`),
  KEY `pm_TaskState_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TaskState` WRITE;
INSERT INTO `pm_TaskState` (`pm_TaskStateId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`) VALUES (1,'2005-12-25 00:23:59','2005-12-25 00:23:59',10,'Назначена',NULL,0);
INSERT INTO `pm_TaskState` (`pm_TaskStateId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`) VALUES (2,'2005-12-25 00:24:13','2005-12-25 00:24:13',20,'Открыта',NULL,0);
INSERT INTO `pm_TaskState` (`pm_TaskStateId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`) VALUES (3,'2005-12-25 00:24:28','2005-12-25 00:24:28',30,'Выполнена',NULL,0);
INSERT INTO `pm_TaskState` (`pm_TaskStateId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`) VALUES (4,'2005-12-27 22:24:12','2005-12-27 22:24:12',40,'На проверке',NULL,0);
INSERT INTO `pm_TaskState` (`pm_TaskStateId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`) VALUES (5,'2005-12-27 22:24:21','2005-12-27 22:24:21',50,'Проверено',NULL,0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TaskTrace`;
CREATE TABLE `pm_TaskTrace` (
  `pm_TaskTraceId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Task` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` varchar(255) DEFAULT NULL,
  `IsActual` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TaskTraceId`),
  KEY `I$TaskTrace$Task` (`Task`),
  KEY `I$TaskTrace$Object` (`ObjectId`,`ObjectClass`),
  KEY `I$pm_TaskTrace$Task` (`Task`),
  KEY `I$pm_TaskTrace$Object` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TaskTrace` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TaskType`;
CREATE TABLE `pm_TaskType` (
  `pm_TaskTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `VPD` varchar(32) DEFAULT NULL,
  `ReferenceName` varchar(32) DEFAULT NULL,
  `ProjectRole` int(11) DEFAULT NULL,
  `ParentTaskType` int(11) DEFAULT NULL,
  `UsedInPlanning` char(1) DEFAULT NULL,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `ShortCaption` varchar(128) DEFAULT NULL,
  `RelatedColor` varchar(16) DEFAULT NULL,
  `IsDefault` char(1) DEFAULT NULL,
  PRIMARY KEY (`pm_TaskTypeId`),
  UNIQUE KEY `XPKpm_TaskType` (`pm_TaskTypeId`),
  KEY `pm_TaskType_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TaskType` WRITE;
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (10,'2010-10-01 17:16:30','2010-10-01 17:16:30',55,'Управление проектом',NULL,'management',4,NULL,'N',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (2,'2005-12-24 21:50:33','2005-12-24 21:50:33',20,'Разработка',NULL,'development',2,NULL,'Y',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (3,'2005-12-27 23:09:09','2005-12-27 23:09:09',30,'Тестирование',NULL,'testing',3,NULL,'Y',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (4,'2005-12-28 09:41:39','2010-06-06 18:05:07',5,'Анализ',NULL,'analysis',1,NULL,'Y',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (5,'2006-01-18 15:34:31','2006-01-18 15:34:31',50,'Документирование',NULL,'documenting',7,NULL,'Y',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (6,'2006-02-19 15:53:21','2006-02-19 15:53:21',60,'Развертывание',NULL,'deployment',2,NULL,'N',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (7,'2006-03-08 09:22:57','2006-03-08 09:22:57',70,'Приемка',NULL,'accepting',5,NULL,'N',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (8,'2010-06-06 18:05:07','2010-06-06 18:05:07',7,'Проектирование',NULL,'design',8,NULL,'Y',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (9,'2010-06-06 18:05:07','2010-06-06 18:05:07',80,'Другое',NULL,'other',2,NULL,'N',NULL,0,NULL,NULL,NULL);
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`, `ShortCaption`, `RelatedColor`, `IsDefault`) VALUES (11,NULL,NULL,25,'Дизайн тестов',NULL,'testdesign',3,NULL,'Y',NULL,0,NULL,NULL,NULL);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TaskTypeStage`;
CREATE TABLE `pm_TaskTypeStage` (
  `pm_TaskTypeStageId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `TaskType` int(11) DEFAULT NULL,
  `ProjectStage` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TaskTypeStageId`),
  KEY `I$pm_TaskTypeStage$TaskType` (`TaskType`),
  KEY `I$pm_TaskTypeStage$ProjectStage` (`ProjectStage`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TaskTypeStage` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Test`;
CREATE TABLE `pm_Test` (
  `pm_TestId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `TestScenario` int(11) DEFAULT NULL,
  `Environment` int(11) DEFAULT NULL,
  `Version` varchar(255) DEFAULT NULL,
  `Result` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TestId`),
  KEY `I$pm_Test$Environment` (`Environment`),
  KEY `I$pm_Test$Version` (`Version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Test` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TestCaseExecution`;
CREATE TABLE `pm_TestCaseExecution` (
  `pm_TestCaseExecutionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Test` int(11) DEFAULT NULL,
  `TestCase` int(11) DEFAULT NULL,
  `Success` char(1) DEFAULT NULL,
  `Tester` int(11) DEFAULT NULL,
  `Result` int(11) DEFAULT NULL,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  `Content` longtext,
  PRIMARY KEY (`pm_TestCaseExecutionId`),
  KEY `I$pm_TestCaseExecution$Test` (`Test`),
  KEY `I$pm_TestCaseExecution$RecordModified` (`RecordModified`),
  KEY `I$pm_TestCaseExecution$TestCase` (`TestCase`),
  KEY `I$pm_TestCaseExecution$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TestCaseExecution` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TestExecutionResult`;
CREATE TABLE `pm_TestExecutionResult` (
  `pm_TestExecutionResultId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `ReferenceName` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TestExecutionResultId`),
  KEY `I$pm_TestExecutionResult$VPD` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TestExecutionResult` WRITE;
INSERT INTO `pm_TestExecutionResult` (`pm_TestExecutionResultId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`) VALUES (1,'2010-10-03 19:15:16','2010-10-03 19:15:16',NULL,10,'Пройден','succeeded',0);
INSERT INTO `pm_TestExecutionResult` (`pm_TestExecutionResultId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`) VALUES (2,'2010-10-03 19:15:24','2010-10-03 19:15:24',NULL,20,'Провален','failed',0);
INSERT INTO `pm_TestExecutionResult` (`pm_TestExecutionResultId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`) VALUES (3,'2010-10-03 19:15:32','2010-10-03 19:15:32',NULL,30,'Заблокирован','blocked',0);
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TestPlan`;
CREATE TABLE `pm_TestPlan` (
  `pm_TestPlanId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TestPlanId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TestPlan` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TestPlanItem`;
CREATE TABLE `pm_TestPlanItem` (
  `pm_TestPlanItemId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `TestSuite` int(11) DEFAULT NULL,
  `Assignee` int(11) DEFAULT NULL,
  `Planned` float DEFAULT NULL,
  `TestPlan` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TestPlanItemId`),
  KEY `I$pm_TestPlanItem$TestPlan` (`TestPlan`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TestPlanItem` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Transition`;
CREATE TABLE `pm_Transition` (
  `pm_TransitionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Description` mediumtext,
  `SourceState` int(11) DEFAULT NULL,
  `TargetState` int(11) DEFAULT NULL,
  `IsReasonRequired` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TransitionId`),
  KEY `I$pm_Transition$VPD` (`VPD`),
  KEY `I$pm_Transition$Source` (`SourceState`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Transition` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TransitionAttribute`;
CREATE TABLE `pm_TransitionAttribute` (
  `pm_TransitionAttributeId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Transition` int(11) DEFAULT NULL,
  `ReferenceName` mediumtext,
  `Entity` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TransitionAttributeId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TransitionAttribute` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TransitionPredicate`;
CREATE TABLE `pm_TransitionPredicate` (
  `pm_TransitionPredicateId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Transition` int(11) DEFAULT NULL,
  `Predicate` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`pm_TransitionPredicateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TransitionPredicate` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TransitionResetField`;
CREATE TABLE `pm_TransitionResetField` (
  `pm_TransitionResetFieldId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Transition` int(11) DEFAULT NULL,
  `ReferenceName` mediumtext,
  `Entity` mediumtext,
  PRIMARY KEY (`pm_TransitionResetFieldId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TransitionResetField` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_TransitionRole`;
CREATE TABLE `pm_TransitionRole` (
  `pm_TransitionRoleId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Transition` int(11) DEFAULT NULL,
  `ProjectRole` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_TransitionRoleId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_TransitionRole` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_UserMail`;
CREATE TABLE `pm_UserMail` (
  `pm_UserMailId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ToParticipant` int(11) DEFAULT NULL,
  `Subject` mediumtext,
  `Content` mediumtext,
  `FromParticipant` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_UserMailId`),
  KEY `ToParticipant` (`ToParticipant`),
  KEY `FromParticipant` (`FromParticipant`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_UserMail` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_UserSetting`;
CREATE TABLE `pm_UserSetting` (
  `pm_UserSettingId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Setting` varchar(32) DEFAULT NULL,
  `Value` mediumtext,
  `Participant` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_UserSettingId`),
  UNIQUE KEY `UK_pm_UserSetting` (`VPD`,`Setting`,`Participant`),
  KEY `I$pm_UserSetting$Participant` (`Participant`),
  KEY `I$pm_UserSetting$Setting` (`Setting`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_UserSetting` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Vacancy`;
CREATE TABLE `pm_Vacancy` (
  `pm_VacancyId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Project` int(11) DEFAULT NULL,
  `IsActive` char(1) DEFAULT NULL,
  `RequiredWorkload` int(11) DEFAULT NULL,
  `PriceOfHour` mediumtext,
  `Description` mediumtext,
  `Requirements` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_VacancyId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Vacancy` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Version`;
CREATE TABLE `pm_Version` (
  `pm_VersionId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` varchar(32) DEFAULT NULL,
  `Description` mediumtext,
  `Project` int(11) DEFAULT NULL,
  `InitialEstimationError` int(11) DEFAULT NULL,
  `InitialBugsInWorkload` int(11) DEFAULT NULL,
  `IsActual` char(1) DEFAULT NULL,
  `StartDate` datetime DEFAULT NULL,
  `FinishDate` datetime DEFAULT NULL,
  `InitialVelocity` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_VersionId`),
  KEY `i$9` (`Project`,`VPD`),
  KEY `i$12` (`Project`,`Caption`),
  KEY `i$22` (`VPD`,`Caption`),
  KEY `I$pm_Version$StartDate` (`StartDate`),
  KEY `I$pm_Version$FinishDate` (`FinishDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Version` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_VersionBurndown`;
CREATE TABLE `pm_VersionBurndown` (
  `pm_VersionBurndownId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Version` int(11) DEFAULT NULL,
  `SnapshotDate` datetime DEFAULT NULL,
  `Workload` float DEFAULT NULL,
  `SnapshotDays` int(11) DEFAULT NULL,
  `PlannedWorkload` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_VersionBurndownId`),
  KEY `I$pm_VersionBurndown$Version` (`Version`),
  KEY `I$pm_VersionBurndown$SnapshotDays` (`SnapshotDays`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_VersionBurndown` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_VersionMetric`;
CREATE TABLE `pm_VersionMetric` (
  `pm_VersionMetricId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Version` int(11) DEFAULT NULL,
  `Metric` varchar(32) DEFAULT NULL,
  `MetricValue` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `MetricValueDate` datetime DEFAULT NULL,
  PRIMARY KEY (`pm_VersionMetricId`),
  KEY `i$10` (`Version`),
  KEY `i$11` (`Version`,`Metric`),
  KEY `I$pm_VersionMetric$Date` (`MetricValueDate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_VersionMetric` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_VersionSettings`;
CREATE TABLE `pm_VersionSettings` (
  `pm_VersionSettingsId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `Project` int(11) DEFAULT NULL,
  `UseRelease` char(1) DEFAULT NULL,
  `UseIteration` char(1) DEFAULT NULL,
  `UseBuild` char(1) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_VersionSettingsId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_VersionSettings` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Watcher`;
CREATE TABLE `pm_Watcher` (
  `pm_WatcherId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `ObjectId` int(11) DEFAULT NULL,
  `ObjectClass` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `Email` mediumtext,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`pm_WatcherId`),
  KEY `I$pm_Watcher$ObjectId` (`ObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Watcher` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_Workspace`;
CREATE TABLE `pm_Workspace` (
  `pm_WorkspaceId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT '0',
  `RecordVersion` int(11) DEFAULT '0',
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `UID` varchar(128) DEFAULT NULL,
  `Caption` mediumtext,
  `SystemUser` int(11) DEFAULT NULL,
  `Icon` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`pm_WorkspaceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_Workspace` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_WorkspaceMenu`;
CREATE TABLE `pm_WorkspaceMenu` (
  `pm_WorkspaceMenuId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT '0',
  `RecordVersion` int(11) DEFAULT '0',
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `UID` varchar(128) DEFAULT NULL,
  `Caption` mediumtext,
  `Workspace` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_WorkspaceMenuId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_WorkspaceMenu` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `pm_WorkspaceMenuItem`;
CREATE TABLE `pm_WorkspaceMenuItem` (
  `pm_WorkspaceMenuItemId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT '0',
  `RecordVersion` int(11) DEFAULT '0',
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `UID` varchar(128) DEFAULT NULL,
  `Caption` mediumtext,
  `ReportUID` varchar(128) DEFAULT NULL,
  `ModuleUID` varchar(128) DEFAULT NULL,
  `WorkspaceMenu` int(11) DEFAULT NULL,
  PRIMARY KEY (`pm_WorkspaceMenuItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `pm_WorkspaceMenuItem` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `settingsId` int(11) NOT NULL AUTO_INCREMENT,
  `FontSize` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`settingsId`),
  UNIQUE KEY `XPKsettings` (`settingsId`),
  KEY `settings_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


LOCK TABLES `settings` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `sm_Action`;
CREATE TABLE `sm_Action` (
  `sm_ActionId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `Activity` int(11) DEFAULT NULL,
  `State` mediumtext,
  `Estimation` float DEFAULT NULL,
  `Kind` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StateObject` int(11) DEFAULT NULL,
  PRIMARY KEY (`sm_ActionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `sm_Action` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `sm_Activity`;
CREATE TABLE `sm_Activity` (
  `sm_ActivityId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `Aim` int(11) DEFAULT NULL,
  `State` mediumtext,
  `Estimation` float DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StateObject` int(11) DEFAULT NULL,
  PRIMARY KEY (`sm_ActivityId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `sm_Activity` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `sm_Aim`;
CREATE TABLE `sm_Aim` (
  `sm_AimId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `State` mediumtext,
  `Person` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StateObject` int(11) DEFAULT NULL,
  PRIMARY KEY (`sm_AimId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `sm_Aim` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `sm_Person`;
CREATE TABLE `sm_Person` (
  `sm_PersonId` int(11) NOT NULL AUTO_INCREMENT,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `Caption` mediumtext,
  `State` mediumtext,
  `Description` mediumtext,
  `Valuable` mediumtext,
  `Problems` mediumtext,
  `PhotoMime` mediumtext,
  `PhotoPath` mediumtext,
  `PhotoExt` varchar(32) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  `StateObject` int(11) DEFAULT NULL,
  PRIMARY KEY (`sm_PersonId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `sm_Person` WRITE;
UNLOCK TABLES;


DROP TABLE IF EXISTS `Tag`;
CREATE TABLE `Tag` (
  `TagId` int(11) NOT NULL AUTO_INCREMENT,
  `RecordCreated` datetime DEFAULT NULL,
  `RecordModified` datetime DEFAULT NULL,
  `VPD` varchar(32) DEFAULT NULL,
  `OrderNum` int(11) DEFAULT NULL,
  `Caption` mediumtext,
  `Owner` int(11) DEFAULT NULL,
  `RecordVersion` int(11) DEFAULT '0',
  PRIMARY KEY (`TagId`),
  KEY `I$Tag$Vpd` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


LOCK TABLES `Tag` WRITE;
UNLOCK TABLES;


