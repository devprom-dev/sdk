SET NAMES 'cp1251';
SET character_set_server=cp1251;
SET character_set_database=cp1251;
SET collation_database=cp1251_general_ci;
SET NAMES 'cp1251' COLLATE 'cp1251_general_ci';
SET CHARACTER SET cp1251;

DROP TABLE IF EXISTS `AdvertiseBooks`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `AdvertiseBooks` (
  `AdvertiseBooksId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `BookStore` text,
  `BookUIN` text,
  `Caption` text,
  `BookUrl` text,
  `ImageUrl` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`AdvertiseBooksId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `AdvertiseBooks`
--

LOCK TABLES `AdvertiseBooks` WRITE;
/*!40000 ALTER TABLE `AdvertiseBooks` DISABLE KEYS */;
/*!40000 ALTER TABLE `AdvertiseBooks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Blog`
--

DROP TABLE IF EXISTS `Blog`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Blog` (
  `BlogId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`BlogId`),
  KEY `VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Blog`
--

LOCK TABLES `Blog` WRITE;
/*!40000 ALTER TABLE `Blog` DISABLE KEYS */;
/*!40000 ALTER TABLE `Blog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BlogLink`
--

DROP TABLE IF EXISTS `BlogLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `BlogLink` (
  `BlogLinkId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `BlogUrl` text,
  `Blog` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`BlogLinkId`),
  KEY `Blog` (`Blog`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `BlogLink`
--

LOCK TABLES `BlogLink` WRITE;
/*!40000 ALTER TABLE `BlogLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `BlogLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BlogPost`
--

DROP TABLE IF EXISTS `BlogPost`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `BlogPost` (
  `BlogPostId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Content` text,
  `AuthorId` int(11) default NULL,
  `Blog` int(11) default NULL,
  `IsPublished` char(1) default NULL,
  `ContentEditor` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`BlogPostId`),
  KEY `Blog` (`Blog`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `BlogPost`
--

LOCK TABLES `BlogPost` WRITE;
/*!40000 ALTER TABLE `BlogPost` DISABLE KEYS */;
/*!40000 ALTER TABLE `BlogPost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BlogPostChange`
--

DROP TABLE IF EXISTS `BlogPostChange`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `BlogPostChange` (
  `BlogPostChangeId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `BlogPost` int(11) default NULL,
  `Content` text,
  `SystemUser` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`BlogPostChangeId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `BlogPostChange`
--

LOCK TABLES `BlogPostChange` WRITE;
/*!40000 ALTER TABLE `BlogPostChange` DISABLE KEYS */;
/*!40000 ALTER TABLE `BlogPostChange` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BlogPostFile`
--

DROP TABLE IF EXISTS `BlogPostFile`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `BlogPostFile` (
  `BlogPostFileId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `ContentMime` text,
  `ContentPath` text,
  `ContentExt` varchar(32) default NULL,
  `BlogPost` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`BlogPostFileId`),
  KEY `BlogPost` (`BlogPost`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `BlogPostFile`
--

LOCK TABLES `BlogPostFile` WRITE;
/*!40000 ALTER TABLE `BlogPostFile` DISABLE KEYS */;
/*!40000 ALTER TABLE `BlogPostFile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BlogPostTag`
--

DROP TABLE IF EXISTS `BlogPostTag`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `BlogPostTag` (
  `BlogPostTagId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `BlogPost` int(11) default NULL,
  `Tag` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`BlogPostTagId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `BlogPostTag`
--

LOCK TABLES `BlogPostTag` WRITE;
/*!40000 ALTER TABLE `BlogPostTag` DISABLE KEYS */;
/*!40000 ALTER TABLE `BlogPostTag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `BlogSubscriber`
--

DROP TABLE IF EXISTS `BlogSubscriber`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `BlogSubscriber` (
  `BlogSubscriberId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Email` text,
  `Blog` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`BlogSubscriberId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `BlogSubscriber`
--

LOCK TABLES `BlogSubscriber` WRITE;
/*!40000 ALTER TABLE `BlogSubscriber` DISABLE KEYS */;
/*!40000 ALTER TABLE `BlogSubscriber` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Comment`
--

DROP TABLE IF EXISTS `Comment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Comment` (
  `CommentId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `AuthorId` int(11) default NULL,
  `ObjectId` int(11) default NULL,
  `PrevComment` int(11) default NULL,
  `ObjectClass` varchar(32) default NULL,
  `ExternalAuthor` text,
  `ExternalEmail` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`CommentId`),
  KEY `i$29` (`RecordModified`),
  KEY `i$30` (`VPD`),
  KEY `i$31` (`ObjectId`,`ObjectClass`),
  FULLTEXT KEY `I$search$caption` (`Caption`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Comment`
--

LOCK TABLES `Comment` WRITE;
/*!40000 ALTER TABLE `Comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `Comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Donation`
--

DROP TABLE IF EXISTS `Donation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Donation` (
  `DonationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `WMZVolume` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`DonationId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Donation`
--

LOCK TABLES `Donation` WRITE;
/*!40000 ALTER TABLE `Donation` DISABLE KEYS */;
/*!40000 ALTER TABLE `Donation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Email`
--

DROP TABLE IF EXISTS `Email`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Email` (
  `EmailId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ToAddress` text,
  `FromAddress` text,
  `Body` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`EmailId`),
  UNIQUE KEY `XPKEmail` (`EmailId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Email`
--

LOCK TABLES `Email` WRITE;
/*!40000 ALTER TABLE `Email` DISABLE KEYS */;
/*!40000 ALTER TABLE `Email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EmailQueue`
--

DROP TABLE IF EXISTS `EmailQueue`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `EmailQueue` (
  `EmailQueueId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `FromAddress` text,
  `MailboxClass` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`EmailQueueId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `EmailQueue`
--

LOCK TABLES `EmailQueue` WRITE;
/*!40000 ALTER TABLE `EmailQueue` DISABLE KEYS */;
/*!40000 ALTER TABLE `EmailQueue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `EmailQueueAddress`
--

DROP TABLE IF EXISTS `EmailQueueAddress`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `EmailQueueAddress` (
  `EmailQueueAddressId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ToAddress` text,
  `EmailQueue` int(11) default NULL,
  `cms_UserId` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`EmailQueueAddressId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `EmailQueueAddress`
--

LOCK TABLES `EmailQueueAddress` WRITE;
/*!40000 ALTER TABLE `EmailQueueAddress` DISABLE KEYS */;
/*!40000 ALTER TABLE `EmailQueueAddress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MeetingParticipation`
--

DROP TABLE IF EXISTS `MeetingParticipation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `MeetingParticipation` (
  `MeetingParticipationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `OrderNum` int(11) default NULL,
  `Meeting` int(11) default NULL,
  `Participant` int(11) default NULL,
  `Comments` text,
  `VPD` varchar(32) default NULL,
  `Accepted` char(1) default NULL,
  `Rejected` char(1) default NULL,
  `RejectReason` text,
  `RecordVersion` int(11) default '0',
  `RememberInterval` int(11) default NULL,
  PRIMARY KEY  (`MeetingParticipationId`),
  UNIQUE KEY `XPKMeetingParticipation` (`MeetingParticipationId`),
  KEY `MeetingParticipation_vpd_idx` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `MeetingParticipation`
--

LOCK TABLES `MeetingParticipation` WRITE;
/*!40000 ALTER TABLE `MeetingParticipation` DISABLE KEYS */;
/*!40000 ALTER TABLE `MeetingParticipation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `News`
--

DROP TABLE IF EXISTS `News`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `News` (
  `NewsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Content` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`NewsId`),
  UNIQUE KEY `XPKNews` (`NewsId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `News`
--

LOCK TABLES `News` WRITE;
/*!40000 ALTER TABLE `News` DISABLE KEYS */;
/*!40000 ALTER TABLE `News` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ObjectChangeLog`
--

DROP TABLE IF EXISTS `ObjectChangeLog`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ObjectChangeLog` (
  `ObjectChangeLogId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ObjectId` int(11) default NULL,
  `EntityRefName` text,
  `ChangeKind` text,
  `Author` int(11) default NULL,
  `Content` text,
  `ObjectUrl` text,
  `EntityName` text,
  `VisibilityLevel` int(11) default NULL,
  `SystemUser` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`ObjectChangeLogId`),
  UNIQUE KEY `XPKObjectChangeLog` (`ObjectChangeLogId`),
  KEY `ObjectId` (`ObjectId`,`EntityRefName`(50),`VPD`),
  KEY `i$32` (`VPD`,`VisibilityLevel`),
  KEY `I$48` (`Author`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ObjectChangeLog`
--

LOCK TABLES `ObjectChangeLog` WRITE;
/*!40000 ALTER TABLE `ObjectChangeLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `ObjectChangeLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ObjectEmailNotification`
--

DROP TABLE IF EXISTS `ObjectEmailNotification`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ObjectEmailNotification` (
  `ObjectEmailNotificationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Header` text,
  `RecordDescription` text,
  `Footer` text,
  `IsAdd` char(1) default NULL,
  `IsModify` char(1) default NULL,
  `IsDelete` char(1) default NULL,
  `HeaderEn` text,
  `FooterEn` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`ObjectEmailNotificationId`),
  UNIQUE KEY `XPKObjectEmailNotification` (`ObjectEmailNotificationId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ObjectEmailNotification`
--

LOCK TABLES `ObjectEmailNotification` WRITE;
/*!40000 ALTER TABLE `ObjectEmailNotification` DISABLE KEYS */;
INSERT INTO `ObjectEmailNotification` (`ObjectEmailNotificationId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Header`, `RecordDescription`, `Footer`, `IsAdd`, `IsModify`, `IsDelete`, `HeaderEn`, `FooterEn`, `RecordVersion`) VALUES (1,'2006-01-14 15:01:36','2010-06-06 18:05:13','',10,'Общее уведомление','','','Письмо автоматически сформировано системой управления процессом разработки (%SERVER_NAME%).\nДля исключения себя из списка рассылки обратитесь к координатору Вашего проекта.','Y','Y','Y','','The e-mail have been generated automatically by Development process management system (%SERVER_NAME%).\nTo unsubscribe please ask coordinator of your project.',0);
/*!40000 ALTER TABLE `ObjectEmailNotification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ObjectEmailNotificationLink`
--

DROP TABLE IF EXISTS `ObjectEmailNotificationLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ObjectEmailNotificationLink` (
  `ObjectEmailNotificationLinkId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `EmailNotification` int(11) default NULL,
  `EntityReferenceName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`ObjectEmailNotificationLinkId`),
  UNIQUE KEY `XPKObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ObjectEmailNotificationLink`
--

LOCK TABLES `ObjectEmailNotificationLink` WRITE;
/*!40000 ALTER TABLE `ObjectEmailNotificationLink` DISABLE KEYS */;
INSERT INTO `ObjectEmailNotificationLink` (`ObjectEmailNotificationLinkId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `EmailNotification`, `EntityReferenceName`, `RecordVersion`) VALUES (1,'2006-01-14 15:01:51','2006-01-14 15:01:51','',10,1,'pm_Release',0),(2,'2006-01-14 19:09:59','2006-01-14 19:09:59','',20,1,'pm_Participant',0),(3,'2006-01-14 19:10:13','2006-01-14 19:10:13','',30,1,'pm_Project',0),(4,'2006-01-14 19:10:39','2006-01-14 19:10:39','',40,1,'pm_ChangeRequest',0),(5,'2006-01-14 19:14:06','2006-01-14 19:14:06','',50,1,'pm_Artefact',0),(6,'2006-01-14 21:04:03','2006-01-14 21:04:03','',60,1,'pm_Enhancement',0),(7,'2006-01-14 21:04:16','2006-01-14 21:04:16','',70,1,'pm_Bug',0),(8,'2006-01-14 21:04:26','2006-01-14 21:04:26','',80,1,'pm_Task',0),(9,'2006-03-09 22:21:06','2006-03-09 22:21:06','',90,1,'cms_Link',0),(10,'2010-06-06 18:05:03','2010-06-06 18:05:03','',100,1,'Comment',0),(11,'2010-06-06 18:05:09','2010-06-06 18:05:09','',110,1,'WikiPage',0),(12,'2010-06-06 18:05:17','2010-06-06 18:05:17','',120,1,'pm_Scrum',0);
/*!40000 ALTER TABLE `ObjectEmailNotificationLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Priority`
--

DROP TABLE IF EXISTS `Priority`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Priority` (
  `PriorityId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`PriorityId`),
  UNIQUE KEY `XPKPriority` (`PriorityId`),
  KEY `Priority_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Priority`
--

LOCK TABLES `Priority` WRITE;
/*!40000 ALTER TABLE `Priority` DISABLE KEYS */;
INSERT INTO `Priority` (`PriorityId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`) VALUES (1,'2005-12-24 11:55:57','2005-12-24 23:18:52',10,'Критично',NULL,0),(2,'2005-12-24 11:56:23','2005-12-24 23:18:57',20,'Высокий',NULL,0),(3,'2005-12-24 11:56:38','2005-12-24 23:19:02',30,'Обычный',NULL,0),(4,'2005-12-24 11:56:48','2005-12-24 23:19:08',40,'Низкий',NULL,0),(5,'2005-12-24 11:57:18','2005-12-24 23:19:13',50,'В свободное время',NULL,0);
/*!40000 ALTER TABLE `Priority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SystemLogSQL`
--

DROP TABLE IF EXISTS `SystemLogSQL`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `SystemLogSQL` (
  `SQLId` int(11) NOT NULL auto_increment,
  `SQLContent` text,
  `RecordCreated` datetime default NULL,
  PRIMARY KEY  (`SQLId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `SystemLogSQL`
--

LOCK TABLES `SystemLogSQL` WRITE;
/*!40000 ALTER TABLE `SystemLogSQL` DISABLE KEYS */;
/*!40000 ALTER TABLE `SystemLogSQL` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Tag`
--

DROP TABLE IF EXISTS `Tag`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `Tag` (
  `TagId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Owner` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`TagId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `Tag`
--

LOCK TABLES `Tag` WRITE;
/*!40000 ALTER TABLE `Tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `Tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TemplateHTML`
--

DROP TABLE IF EXISTS `TemplateHTML`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TemplateHTML` (
  `TemplateHTMLId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `CSSBlock` text,
  `Header` text,
  `Footer` text,
  `HeaderContents` char(1) default NULL,
  `SectionNumbers` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`TemplateHTMLId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `TemplateHTML`
--

LOCK TABLES `TemplateHTML` WRITE;
/*!40000 ALTER TABLE `TemplateHTML` DISABLE KEYS */;
INSERT INTO `TemplateHTML` (`TemplateHTMLId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `CSSBlock`, `Header`, `Footer`, `HeaderContents`, `SectionNumbers`, `RecordVersion`) VALUES (1,'2006-02-23 23:25:44','2006-02-26 12:24:10','f22f2d9a48cc34256d431998bb260517',10,'Помощь','Для генерации справки (помощи) для внутренней части системы','body {font-size:8pt;font-family:verdana;line-height:145%;\r\nmargin-left:10pt;margin-right:15pt;}\r\nh3 {font-family:arial;}\r\ndiv {padding-left:3pt;text-align:justify;padding-left:5pt;}\r\ntd {font-size:8pt;text-align:justify;line-height:145%;}','','','Y','Y',0);
/*!40000 ALTER TABLE `TemplateHTML` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TemplateHTML2`
--

DROP TABLE IF EXISTS `TemplateHTML2`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `TemplateHTML2` (
  `TemplateHTML2Id` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `CSSBlock` text,
  `Header` text,
  `Footer` text,
  `HeaderContents` char(1) default NULL,
  `SectionNumbers` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`TemplateHTML2Id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `TemplateHTML2`
--

LOCK TABLES `TemplateHTML2` WRITE;
/*!40000 ALTER TABLE `TemplateHTML2` DISABLE KEYS */;
/*!40000 ALTER TABLE `TemplateHTML2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WikiPage`
--

DROP TABLE IF EXISTS `WikiPage`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WikiPage` (
  `WikiPageId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `Content` mediumtext,
  `ParentPage` int(11) default NULL,
  `Project` int(11) default NULL,
  `Author` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `UserField1` text,
  `IsTemplate` char(1) default NULL,
  `UserField2` text,
  `IsArchived` char(1) default NULL,
  `UserField3` int(11) default NULL,
  `IsDraft` char(1) default NULL,
  `ContentEditor` text,
  `State` varchar(32) default NULL,
  `PageType` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`WikiPageId`),
  UNIQUE KEY `XPKWikiPage` (`WikiPageId`),
  KEY `WikiPage_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(50),`VPD`),
  KEY `ParentPage` (`ParentPage`,`VPD`),
  KEY `WikiPage$Archived` (`ParentPage`,`IsArchived`),
  KEY `I$WikiPage$State` (`State`),
  FULLTEXT KEY `I$43` (`Caption`,`Content`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `WikiPage`
--

LOCK TABLES `WikiPage` WRITE;
/*!40000 ALTER TABLE `WikiPage` DISABLE KEYS */;
/*!40000 ALTER TABLE `WikiPage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WikiPageChange`
--

DROP TABLE IF EXISTS `WikiPageChange`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WikiPageChange` (
  `WikiPageChangeId` int(11) NOT NULL auto_increment,
  `WikiPage` int(11) default NULL,
  `Content` mediumtext,
  `Author` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`WikiPageChangeId`),
  UNIQUE KEY `XPKWikiPageChange` (`WikiPageChangeId`),
  KEY `WikiPageChange_vpd_idx` (`VPD`),
  KEY `WikiPage` (`WikiPage`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `WikiPageChange`
--

LOCK TABLES `WikiPageChange` WRITE;
/*!40000 ALTER TABLE `WikiPageChange` DISABLE KEYS */;
/*!40000 ALTER TABLE `WikiPageChange` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WikiPageFile`
--

DROP TABLE IF EXISTS `WikiPageFile`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WikiPageFile` (
  `WikiPageFileId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `ContentMime` text,
  `ContentPath` text,
  `ContentExt` varchar(32) default NULL,
  `WikiPage` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`WikiPageFileId`),
  UNIQUE KEY `XPKWikiPageFile` (`WikiPageFileId`),
  KEY `WikiPageFile_vpd_idx` (`VPD`),
  KEY `WikiPage` (`WikiPage`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `WikiPageFile`
--

LOCK TABLES `WikiPageFile` WRITE;
/*!40000 ALTER TABLE `WikiPageFile` DISABLE KEYS */;
/*!40000 ALTER TABLE `WikiPageFile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WikiPageTrace`
--

DROP TABLE IF EXISTS `WikiPageTrace`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WikiPageTrace` (
  `WikiPageTraceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `SourcePage` int(11) default NULL,
  `TargetPage` int(11) default NULL,
  `IsActual` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`WikiPageTraceId`),
  KEY `I$WikiPageTrace$Source` (`SourcePage`),
  KEY `I$WikiPageTrace$Target` (`TargetPage`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `WikiPageTrace`
--

LOCK TABLES `WikiPageTrace` WRITE;
/*!40000 ALTER TABLE `WikiPageTrace` DISABLE KEYS */;
/*!40000 ALTER TABLE `WikiPageTrace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WikiPageType`
--

DROP TABLE IF EXISTS `WikiPageType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WikiPageType` (
  `WikiPageTypeId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `ReferenceName` text,
  `DefaultPageTemplate` int(11) default NULL,
  `ShortCaption` text,
  `WikiEditor` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`WikiPageTypeId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `WikiPageType`
--

LOCK TABLES `WikiPageType` WRITE;
/*!40000 ALTER TABLE `WikiPageType` DISABLE KEYS */;
/*!40000 ALTER TABLE `WikiPageType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `WikiTag`
--

DROP TABLE IF EXISTS `WikiTag`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `WikiTag` (
  `WikiTagId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Wiki` int(11) default NULL,
  `Tag` int(11) default NULL,
  `WikiReferenceName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`WikiTagId`),
  KEY `Wiki` (`Wiki`,`Tag`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `WikiTag`
--

LOCK TABLES `WikiTag` WRITE;
/*!40000 ALTER TABLE `WikiTag` DISABLE KEYS */;
/*!40000 ALTER TABLE `WikiTag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attribute`
--

DROP TABLE IF EXISTS `attribute`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `attribute` (
  `attributeId` int(11) NOT NULL auto_increment,
  `Caption` text,
  `ReferenceName` text,
  `AttributeType` varchar(64) default NULL,
  `DefaultValue` text,
  `IsRequired` char(1) default NULL,
  `IsVisible` char(1) default NULL,
  `entityId` int(11) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`attributeId`),
  UNIQUE KEY `XPKattribute` (`attributeId`),
  KEY `attribute_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30),`entityId`),
  KEY `entityId` (`entityId`),
  KEY `I$attribute$Type` (`AttributeType`)
) ENGINE=MyISAM AUTO_INCREMENT=1535 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `attribute`
--

LOCK TABLES `attribute` WRITE;
/*!40000 ALTER TABLE `attribute` DISABLE KEYS */;
INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1,'Название','Caption','TEXT','','Y','Y',2,10,NULL,NULL,NULL,0),(2,'Ссылочное имя','ReferenceName','TEXT','','Y','Y',2,20,NULL,NULL,NULL,0),(3,'PHP файл','PHPFile','TEXT','','Y','Y',2,30,NULL,NULL,NULL,0),(4,'Меню','Menu','REF_cms_MainMenuId','','Y','Y',2,40,NULL,NULL,NULL,0),(5,'Название','Caption','TEXT','','Y','Y',1,10,NULL,NULL,NULL,0),(6,'Ссылочное имя','ReferenceName','TEXT','','Y','Y',1,20,NULL,NULL,NULL,0),(7,'Имя','Caption','TEXT','','Y','Y',3,10,NULL,NULL,NULL,0),(8,'E-mail','Email','TEXT','','Y','Y',3,20,NULL,NULL,NULL,0),(9,'Логин','Login','TEXT','','Y','Y',3,30,NULL,NULL,NULL,0),(1527,'Является уникальным','IsUnique','CHAR','N','N','Y',353,75,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0),(11,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',4,10,NULL,NULL,NULL,0),(12,'Проект','Project','REF_pm_ProjectId','','Y','Y',4,20,NULL,NULL,NULL,0),(13,'Роль в проекте','ProjectRole','REF_pm_ProjectRoleId','','Y','Y',4,30,NULL,NULL,NULL,0),(14,'Название','Caption','TEXT','','Y','Y',6,10,NULL,NULL,NULL,0),(1339,'Группа','UserGroup','REF_co_UserGroupId',NULL,'Y','Y',323,10,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0),(16,'Название','Caption','TEXT','','Y','Y',5,10,NULL,NULL,NULL,0),(17,'Описание','Description','RICHTEXT','',NULL,'Y',5,30,NULL,NULL,NULL,0),(18,'Название','Caption','TEXT','','Y','Y',7,10,NULL,NULL,NULL,0),(19,'Название','Caption','TEXT','','Y','Y',8,10,NULL,NULL,NULL,0),(20,'Описание','Description','RICHTEXT','',NULL,'Y',8,30,NULL,NULL,NULL,0),(21,'Файл','Content','FILE','','Y','Y',8,5,NULL,'2006-01-26 21:07:52',NULL,0),(22,'Кодовое название','CodeName','TEXT','','Y','Y',5,20,NULL,NULL,NULL,0),(23,'Проект','Project','REF_pm_ProjectId','','Y','N',8,50,NULL,NULL,NULL,0),(24,'Каталог','Kind','REF_pm_ArtefactTypeId','','Y','Y',8,20,NULL,NULL,NULL,0),(25,'Название','Caption','TEXT','','Y','Y',9,10,NULL,NULL,NULL,0),(26,'Ссылочное имя','ReferenceName','TEXT','',NULL,'N',9,20,NULL,NULL,NULL,0),(27,'Содержание','Content','LARGETEXT','','','N',9,30,NULL,'2006-02-16 21:25:22',NULL,0),(28,'Родительская страница','ParentPage','REF_WikiPageId','',NULL,'N',9,40,NULL,NULL,NULL,0),(29,'Название','Caption','VARCHAR','','N','N',10,10,NULL,NULL,NULL,0),(30,'Описание','Description','VARCHAR','',NULL,'Y',10,35,NULL,NULL,NULL,0),(31,'Файл','Content','FILE','','Y','Y',10,30,NULL,NULL,NULL,0),(32,'Страница','WikiPage','REF_WikiPageId','','Y','',10,40,NULL,NULL,NULL,0),(33,'Проект','Project','REF_pm_ProjectId','','Y','N',9,50,NULL,NULL,NULL,0),(34,'Название','Caption','TEXT','','Y','Y',11,10,NULL,NULL,NULL,0),(35,'Описание','Description','RICHTEXT','',NULL,'Y',11,20,NULL,NULL,NULL,0),(361,'Окружение','Environment','REF_pm_EnvironmentId','',NULL,'Y',11,26,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0),(37,'Файл','Attachment','FILE','',NULL,'Y',11,40,NULL,NULL,NULL,0),(38,'Обнаружил','Submitter','REF_pm_ParticipantId','','Y','Y',11,60,NULL,NULL,NULL,0),(39,'Состояние','State','TEXT','',NULL,'Y',11,50,NULL,NULL,NULL,0),(40,'Название','Caption','TEXT','','Y','Y',12,10,NULL,NULL,NULL,0),(41,'Описание','Description','RICHTEXT','',NULL,'Y',12,20,NULL,NULL,NULL,0),(42,'Требование','Requirement','REF_WikiPageId','',NULL,'Y',12,30,NULL,NULL,NULL,0),(43,'Файл','Attachment','FILE','',NULL,'Y',12,50,NULL,NULL,NULL,0),(44,'Составитель','Submitter','REF_pm_ParticipantId','','Y','Y',12,40,NULL,NULL,NULL,0),(45,'Название','ReleaseNumber','VARCHAR','','Y','Y',14,10,NULL,NULL,NULL,0),(46,'Описание','Description','RICHTEXT','',NULL,'Y',14,20,NULL,NULL,NULL,0),(47,'Проект','Project','REF_pm_ProjectId','','Y','Y',14,30,NULL,NULL,NULL,0),(48,'Дата начала','StartDate','DATE','','Y','Y',14,40,NULL,NULL,NULL,0),(49,'Дата окончания','FinishDate','DATE','','Y','Y',14,50,NULL,NULL,NULL,0),(50,'Итерация','Release','REF_pm_ReleaseId',NULL,'Y','Y',15,10,NULL,'2010-06-06 18:05:31',NULL,0),(53,'Комментарий','Comments','RICHTEXT','','','Y',15,100,NULL,'2006-02-01 23:02:08',NULL,0),(54,'Исполнитель','Assignee','REF_pm_ParticipantId','','Y','Y',15,50,NULL,'2005-12-27 21:58:24',NULL,0),(56,'Планируемая трудоемкость, ч.','Planned','FLOAT','','Y','Y',15,70,NULL,'2005-12-23 23:04:28',NULL,0),(58,'Страница','WikiPage','REF_WikiPageId','','Y',NULL,16,10,NULL,NULL,NULL,0),(59,'Содержание','Content','LARGETEXT','','Y','Y',16,20,NULL,NULL,NULL,0),(60,'Автор','Author','REF_pm_ParticipantId','','Y','Y',16,30,NULL,NULL,NULL,0),(61,'Автор','Author','REF_pm_ParticipantId','','Y','N',9,60,NULL,NULL,NULL,0),(62,'Платформа','Platform','LARGETEXT','',NULL,'N',5,40,NULL,NULL,NULL,0),(63,'Инструментарий','Tools','LARGETEXT','','N','N',5,160,NULL,'2010-06-06 18:05:23',NULL,0),(64,'Главная страница','MainWikiPage','REF_WikiPageId','','','N',5,60,NULL,NULL,NULL,0),(65,'Страница требований','RequirementsWikiPage','REF_WikiPageId','','','N',5,70,NULL,NULL,NULL,0),(66,'Дата начала','StartDate','DATE','','Y','Y',5,80,NULL,NULL,NULL,0),(67,'Дата окончания','FinishDate','DATE','',NULL,'Y',5,90,NULL,NULL,NULL,0),(69,'Текущий релиз','Version','REF_pm_VersionId',NULL,'Y','N',5,25,NULL,'2010-06-06 18:05:29',NULL,0),(70,'Проект','Project','REF_pm_ProjectId','','Y',NULL,12,60,NULL,NULL,NULL,0),(71,'Выложил','Participant','REF_pm_ParticipantId','','Y',NULL,8,60,'2005-12-22 22:41:37','2005-12-22 22:41:37',NULL,0),(72,'Требование','Requirement','REF_WikiPageId','','Y','Y',11,35,'2005-12-23 21:42:07','2005-12-23 21:42:07',NULL,0),(73,'Проект','Project','REF_pm_ProjectId','','Y','Y',11,70,'2005-12-23 21:51:05','2005-12-23 21:51:05',NULL,0),(74,'Текущая','IsCurrent','CHAR',NULL,'N','Y',14,15,'2005-12-23 22:41:09','2010-06-06 18:05:40',NULL,0),(75,'ICQ','ICQNumber','TEXT','','N','N',3,25,'2005-12-24 11:54:06','2010-06-06 18:05:13',NULL,0),(76,'Название','Caption','TEXT','','Y','Y',17,10,'2005-12-24 11:55:04','2005-12-24 11:55:04',NULL,0),(77,'Тема','Subject','LARGETEXT','','Y','Y',18,10,'2005-12-24 11:58:15','2005-12-24 11:58:15',NULL,0),(78,'Место','Location','TEXT','','Y','Y',18,20,'2005-12-24 11:58:51','2005-12-24 11:58:51',NULL,0),(79,'Дата','MeetingDate','DATE','','Y','Y',18,30,'2005-12-24 11:59:13','2005-12-24 11:59:13',NULL,0),(80,'Митинг','Meeting','REF_pm_MeetingId','','Y','Y',19,10,'2005-12-24 12:01:17','2005-12-24 12:01:17',NULL,0),(81,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',19,20,'2005-12-24 12:01:45','2005-12-24 12:01:45',NULL,0),(82,'Комментарий','Comments','LARGETEXT','',NULL,'Y',19,30,'2005-12-24 12:02:01','2005-12-24 12:02:01',NULL,0),(83,'Средняя загрузка в день, ч.','Capacity','INTEGER','','Y','Y',3,15,'2005-12-24 14:50:47','2005-12-24 14:50:47',NULL,0),(84,'Проект','Project','REF_pm_ProjectId','','Y',NULL,3,50,'2005-12-24 14:51:40','2005-12-24 14:51:40',NULL,0),(85,'Название','Caption','TEXT','','Y','Y',20,10,'2005-12-24 21:50:11','2005-12-24 21:50:11',NULL,0),(86,'Тип','TaskType','REF_pm_TaskTypeId','','Y','Y',15,15,'2005-12-24 21:52:25','2005-12-24 21:52:31',NULL,0),(87,'Приоритет','Priority','REF_PriorityId','3','Y','Y',15,55,'2005-12-24 22:59:00','2005-12-24 23:04:57',NULL,0),(88,'Бизнес приоритет','Priority','REF_PriorityId','3','Y','Y',11,25,'2005-12-24 23:05:44','2006-01-11 23:51:50',NULL,0),(89,'Бизнес приоритет','Priority','REF_PriorityId','3','Y','Y',12,25,'2005-12-24 23:13:39','2005-12-24 23:13:39',NULL,0),(1324,'Название','Caption','TEXT',NULL,'Y','Y',318,10,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(91,'Название','Caption','TEXT','','Y','Y',21,10,'2005-12-25 00:23:10','2005-12-25 00:23:10',NULL,0),(1407,'Состояние','State','TEXT',NULL,'N','N',9,150,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0),(93,'Результат','Result','LARGETEXT','',NULL,'N',15,90,'2005-12-25 11:49:28','2005-12-25 11:49:28',NULL,0),(94,'Проверяющий','Controller','REF_pm_ParticipantId','','','Y',15,52,'2005-12-27 21:59:04','2005-12-27 21:59:23',NULL,0),(95,'Название','Caption','TEXT','','Y','Y',22,10,'2005-12-28 09:03:31','2005-12-28 09:03:31',NULL,0),(96,'Описание','Description','RICHTEXT','',NULL,'Y',22,20,'2005-12-28 09:03:57','2005-12-28 09:03:57',NULL,0),(97,'Приоритет','Priority','REF_PriorityId','3','Y','Y',22,30,'2005-12-28 09:04:36','2005-12-28 09:04:36',NULL,0),(98,'Автор','Author','REF_cms_UserId','','Y','Y',22,40,'2005-12-28 09:05:08','2006-02-01 01:02:26',NULL,0),(99,'Проект','Project','REF_pm_ProjectId','','Y','N',22,50,'2005-12-28 09:29:32','2005-12-28 09:29:32',NULL,0),(100,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId','',NULL,'Y',15,35,'2005-12-28 09:31:21','2005-12-28 09:31:21',NULL,0),(101,'IP адрес','IPAddress','TEXT','','Y','Y',23,10,'2006-01-06 13:34:32','2006-01-06 13:34:32','',0),(102,'Проект','Project','REF_pm_ProjectId','','Y','Y',23,20,'2006-01-06 13:35:03','2006-01-06 13:35:03','',0),(103,'Содержание','Content','LARGETEXT','','Y','Y',24,10,'2006-01-06 18:51:53','2006-01-06 18:51:53','',0),(104,'Тема','Caption','TEXT','','Y','Y',25,10,'2006-01-06 21:13:47','2006-01-06 21:13:47','',0),(105,'Кому','ToAddress','TEXT','',NULL,'Y',25,20,'2006-01-06 21:14:08','2006-01-06 21:14:08','',0),(106,'От кого','FromAddress','TEXT','','Y','Y',25,30,'2006-01-06 21:14:21','2006-01-06 21:14:21','',0),(107,'Содержание','Body','LARGETEXT','','Y','Y',25,40,'2006-01-06 21:14:44','2006-01-06 21:14:44','',0),(108,'Проект','Project','REF_pm_ProjectId','','Y','Y',26,10,'2006-01-09 16:49:16','2006-01-09 16:49:16','',0),(109,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',26,20,'2006-01-09 16:51:46','2006-01-09 16:51:46','',0),(110,'Название','Caption','TEXT','','Y','Y',27,10,'2006-01-09 22:42:49','2006-01-09 22:42:49','',0),(111,'Описание','Description','LARGETEXT','',NULL,'Y',27,20,'2006-01-09 22:43:03','2006-01-09 22:43:03','',0),(112,'Версия','Version','VARCHAR','1','Y','Y',27,30,'2006-01-09 22:43:38','2006-02-20 22:22:25','',0),(113,'Содержание','Content','REF_WikiPageId','','','',27,40,'2006-01-09 22:44:13','2006-01-09 22:51:24','',0),(114,'Проект','Project','REF_pm_ProjectId','','Y','',27,50,'2006-01-09 22:44:26','2006-01-09 22:51:28','',0),(115,'Название','Caption','RICHTEXT',NULL,'N','Y',15,5,'2006-01-12 08:37:37','2010-06-06 18:05:58','',0),(116,'Ид справки','HelpId','INTEGER','','','Y',2,50,'2006-01-12 23:40:59','2006-01-12 23:41:51','',0),(117,'Уведомление об операции над объектом','EmailNotification','REF_ObjectEmailNotificationId','','Y','Y',29,10,'2006-01-14 14:54:24','2006-01-14 14:54:24','',0),(118,'Ссылочное имя класса','EntityReferenceName','TEXT','','Y','Y',29,20,'2006-01-14 14:54:39','2006-01-14 14:54:39','',0),(119,'Название','Caption','TEXT','','Y','Y',28,10,'2006-01-14 14:55:22','2006-01-14 14:55:22','',0),(120,'Заголовок','Header','LARGETEXT','',NULL,'Y',28,20,'2006-01-14 14:56:07','2006-01-14 14:56:07','',0),(121,'Описание атрибутов объекта','RecordDescription','LARGETEXT','',NULL,'Y',28,30,'2006-01-14 14:56:25','2006-01-14 14:56:25','',0),(122,'Окончание','Footer','LARGETEXT','',NULL,'Y',28,40,'2006-01-14 14:56:43','2006-01-14 14:56:43','',0),(123,'Активно при создании','IsAdd','CHAR','Y',NULL,'Y',28,50,'2006-01-14 14:57:09','2006-01-14 14:57:09','',0),(124,'Активно при модификации','IsModify','CHAR','Y',NULL,'Y',28,60,'2006-01-14 14:57:46','2006-01-14 14:57:46','',0),(125,'Активно при удалении','IsDelete','CHAR','Y',NULL,'Y',28,70,'2006-01-14 14:58:00','2006-01-14 14:58:00','',0),(127,'Название объекта','Caption','TEXT','',NULL,'Y',30,10,'2006-01-16 21:30:13','2006-01-16 21:30:13','',0),(128,'Ид объекта','ObjectId','INTEGER','',NULL,'N',30,20,'2006-01-16 21:30:38','2006-01-16 21:30:38','',0),(129,'Класс объекта','EntityRefName','TEXT','','Y','N',30,30,'2006-01-16 21:31:00','2006-01-16 21:31:00','',0),(130,'Тип','ChangeKind','TEXT','','Y','N',30,40,'2006-01-16 21:31:22','2006-01-16 21:31:22','',0),(131,'Автор','Author','TEXT','',NULL,'Y',30,60,'2006-01-16 21:31:42','2006-01-16 21:31:42','',0),(132,'Содержание','Content','LARGETEXT','',NULL,'Y',30,50,'2006-01-16 21:31:56','2006-01-16 21:31:56','',0),(1430,'Категория','Category','TEXT',NULL,'Y','Y',345,20,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1429,'Название','Caption','TEXT',NULL,'Y','Y',345,10,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0),(1428,'Параметры','Parameters','LARGETEXT',NULL,'N','Y',314,25,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0),(1427,'Кодовое название','ReferenceName','TEXT',NULL,'Y','Y',85,20,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0),(137,'Url объекта','ObjectUrl','TEXT','',NULL,'N',30,35,'2006-01-18 01:15:45','2006-01-18 01:15:45','',0),(138,'Название сущности','EntityName','TEXT','',NULL,'N',30,37,'2006-01-18 01:20:33','2006-01-18 01:20:33','',0),(139,'Связь со справочной документацией','HelpLink','REF_HelpLinkId','',NULL,'Y',32,10,'2006-01-19 10:00:58','2006-01-19 10:00:58','',0),(140,'Задача','Task','REF_pm_TaskId','',NULL,'Y',32,20,'2006-01-19 10:02:21','2006-01-19 10:02:21','',0),(141,'Содержание','Caption','LARGETEXT','','Y','Y',35,10,'2006-01-21 14:41:10','2006-01-21 14:41:10','',0),(142,'Автор','AuthorId','REF_cms_UserId','','Y','Y',35,20,'2006-01-21 14:41:28','2006-01-21 14:41:28','',0),(143,'Объект','ObjectId','INTEGER','',NULL,'Y',35,30,'2006-01-21 14:41:46','2006-01-21 14:41:46','',0),(144,'Предыдущий комментарий','PrevComment','REF_CommentId','',NULL,'Y',35,40,'2006-01-21 14:42:36','2006-01-21 14:42:36','',0),(145,'Класс объекта','ObjectClass','TEXT','','','Y',35,35,'2006-01-21 23:50:46','2006-01-21 23:51:14','',0),(146,'Ид сессии','SessionHash','TEXT','','Y','Y',26,30,'2006-01-22 16:32:27','2006-01-22 16:32:27','',0),(147,'Дата последнего входа','PrevLoginDate','DATE','',NULL,'Y',26,40,'2006-01-22 17:27:34','2006-01-22 17:27:34','',0),(148,'Уровень видимости','VisibilityLevel','INTEGER','','','N',30,70,'2006-01-24 22:20:39','2006-01-24 22:54:28','',0),(149,'Дом. тел.','HomePhone','TEXT','',NULL,'N',3,27,'2006-01-25 19:05:09','2006-01-25 19:05:09','',0),(150,'Моб. тел.','MobilePhone','TEXT','',NULL,'N',3,28,'2006-01-25 19:05:35','2006-01-25 19:05:35','',0),(151,'Использовать фазу подготовки требований','IsRequirements','CHAR','Y',NULL,'Y',36,90,'2006-01-25 21:02:16','2006-01-25 21:02:16','',0),(152,'Использовать фазу подготовки справочной документации','IsHelps','CHAR','Y',NULL,'Y',36,110,'2006-01-25 21:02:53','2006-01-25 21:02:53','',0),(153,'Использовать фазу тестирования','IsTests','CHAR','Y',NULL,'Y',36,100,'2006-01-25 21:03:41','2006-01-25 21:03:41','',0),(154,'Проект','Project','REF_pm_ProjectId','','Y','Y',36,100,'2006-01-25 21:05:31','2006-03-11 13:00:05','',0),(156,'Кодовое название','CodeName','TEXT','',NULL,'Y',23,30,'2006-01-27 21:31:50','2006-01-27 21:31:50','',0),(157,'Название','Caption','TEXT','',NULL,'Y',23,40,'2006-01-27 21:32:06','2006-01-27 21:32:06','',0),(158,'Логин','Login','TEXT','',NULL,'Y',23,50,'2006-01-27 21:32:19','2006-01-27 21:32:19','',0),(159,'Email','Email','TEXT','',NULL,'Y',23,60,'2006-01-27 21:32:30','2006-01-27 21:32:30','',0),(160,'Пароль','Password','TEXT','',NULL,'Y',23,70,'2006-01-27 21:32:47','2006-01-27 21:32:47','',0),(161,'Методология','Methodology','TEXT','',NULL,'Y',23,80,'2006-01-27 21:34:02','2006-01-27 21:34:02','',0),(162,'Хеш создания','CreationHash','TEXT','',NULL,'Y',23,90,'2006-01-27 21:46:12','2006-01-27 21:46:12','',0),(163,'Магазин','BookStore','TEXT','','Y','Y',37,10,'2006-01-29 21:41:58','2006-01-29 21:41:58','',0),(164,'Идентификатор','BookUIN','TEXT','','Y','Y',37,20,'2006-01-29 21:42:14','2006-01-29 21:42:14','',0),(165,'Название книги','Caption','TEXT','','Y','Y',37,30,'2006-01-29 21:42:44','2006-01-29 21:42:44','',0),(166,'Url','BookUrl','TEXT','','Y','Y',37,40,'2006-01-29 21:42:55','2006-01-29 21:42:55','',0),(167,'Изображение','ImageUrl','TEXT','','Y','Y',37,50,'2006-01-29 21:45:47','2006-01-29 21:45:47','',0),(179,'Имя пожертвовашего','Caption','TEXT','',NULL,'Y',38,10,'2006-02-02 22:00:31','2006-02-02 22:00:31','',0),(180,'Величина взноса (WMZ)','WMZVolume','TEXT','',NULL,'Y',38,20,'2006-02-02 22:00:59','2006-02-02 22:00:59','',0),(181,'Номер','Caption','TEXT','','Y','Y',39,10,'2006-02-09 22:22:43','2006-02-09 22:22:43','',0),(182,'Описание','Description','LARGETEXT','',NULL,'Y',39,20,'2006-02-09 22:23:58','2006-02-09 22:23:58','',0),(1309,'База знаний','KnowledgeBase','INTEGER','0','Y','N',316,30,'2010-10-01 17:16:24','2010-10-01 17:16:24','',0),(184,'Проект','Project','REF_pm_ProjectId','','Y',NULL,39,40,'2006-02-09 22:26:49','2006-02-09 22:26:49','',0),(185,'Релиз','Version','REF_pm_VersionId',NULL,'Y','N',14,60,'2006-02-09 23:17:04','2010-06-06 18:05:32','',0),(188,'Название','Caption','TEXT','','Y','Y',40,10,'2006-02-11 17:01:21','2006-02-11 17:01:21','',0),(189,'Заголовок','Caption','TEXT','','Y','Y',41,10,'2006-02-11 17:02:57','2006-02-11 17:02:57','',0),(190,'Содержание','Content','TEXT','','Y','Y',41,20,'2006-02-11 17:03:24','2006-02-11 17:03:24','',0),(191,'Автор','AuthorId','INTEGER','',NULL,NULL,41,30,'2006-02-11 17:03:42','2006-02-11 17:03:42','',0),(192,'Блог','Blog','REF_BlogId','','Y',NULL,41,40,'2006-02-11 17:03:56','2006-02-11 17:03:56','',0),(193,'Название','Caption','TEXT','','Y','Y',42,10,'2006-02-11 17:05:56','2006-02-11 17:05:56','',0),(194,'Описание','Description','LARGETEXT','',NULL,'Y',42,20,'2006-02-11 17:06:09','2006-02-11 17:06:09','',0),(195,'Файл','Content','FILE','',NULL,'Y',42,30,'2006-02-11 17:06:38','2006-02-11 17:06:38','',0),(196,'Сообщение блога','BlogPost','REF_BlogPostId','','Y',NULL,42,40,'2006-02-11 17:07:23','2006-02-11 17:07:23','',0),(197,'Название блога','Caption','TEXT','','Y','Y',43,10,'2006-02-11 17:09:01','2006-02-11 17:09:01','',0),(198,'Описание блога','Description','LARGETEXT','',NULL,'Y',43,20,'2006-02-11 17:09:18','2006-02-11 17:09:18','',0),(199,'Ссылка на блог','BlogUrl','TEXT','','Y','Y',43,30,'2006-02-11 17:09:37','2006-02-11 17:09:37','',0),(200,'Блог','Blog','REF_BlogId','','Y',NULL,43,40,'2006-02-11 17:09:56','2006-02-11 17:09:56','',0),(201,'Электронный адрес','Email','TEXT','','Y','Y',44,10,'2006-02-11 17:11:24','2006-02-11 17:11:24','',0),(202,'Блог','Blog','REF_BlogId','','Y','Y',44,20,'2006-02-11 17:11:41','2006-02-12 22:15:48','',0),(203,'Блог проекта','Blog','REF_BlogId','','Y',NULL,5,110,'2006-02-11 17:21:10','2006-02-11 17:21:10','',0),(204,'Опубликовано','IsPublished','CHAR','N',NULL,NULL,41,50,'2006-02-11 18:04:37','2006-02-11 18:04:37','',0),(205,'Внешний автор','ExternalAuthor','TEXT','',NULL,NULL,35,50,'2006-02-12 12:17:23','2006-02-12 12:17:23','',0),(206,'Название','Caption','TEXT','','Y','Y',45,10,'2006-02-12 21:24:56','2006-02-12 21:24:56','',0),(207,'Описание','Description','LARGETEXT','','Y','Y',45,20,'2006-02-12 21:25:28','2006-02-12 21:25:28','',0),(208,'Отправитель','FromAddress','TEXT','','Y','Y',45,30,'2006-02-12 21:26:37','2006-02-12 21:26:37','',0),(209,'Адрес получателя','ToAddress','TEXT','','Y','Y',46,10,'2006-02-12 21:27:13','2006-02-12 21:27:13','',0),(210,'Очередь сообщений','EmailQueue','REF_EmailQueueId','','Y','Y',46,20,'2006-02-12 21:27:55','2006-02-12 21:27:55','',0),(211,'Проект','Project','REF_pm_ProjectId','','Y',NULL,47,10,'2006-02-13 21:25:07','2006-02-13 21:25:07','',0),(212,'Публиковать проект','IsProjectInfo','CHAR','N','N','Y',47,20,'2006-02-13 21:26:55','2010-06-06 18:05:03','',0),(213,'Публиковать сведения об участниках проекта','IsParticipants','CHAR','N','N','Y',47,30,'2006-02-13 21:27:44','2010-06-06 18:05:03','',0),(214,'Публиковать блог проекта','IsBlog','CHAR','N',NULL,'Y',47,40,'2006-02-13 21:28:07','2006-02-13 21:28:07','',0),(1308,'Проект','Source','REF_pm_ProjectId',NULL,'Y','N',316,20,'2010-10-01 17:16:24','2010-10-01 17:16:24','',0),(535,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId',NULL,'Y','N',114,10,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0),(217,'Исходное пожелание','ChangeRequest','REF_pm_ChangeRequestId','',NULL,'Y',11,33,'2006-02-22 08:50:26','2006-02-22 08:50:26','',0),(218,'Исходное пожелание','ChangeRequest','REF_pm_ChangeRequestId','',NULL,'Y',12,27,'2006-02-22 08:51:35','2006-02-22 08:51:35','',0),(219,'Пользовательское поле 1','UserField1','TEXT','',NULL,'N',9,70,'2006-02-22 21:08:52','2006-02-22 21:08:52','',0),(220,'Название','Caption','TEXT','','Y','Y',48,10,'2006-02-23 22:32:44','2006-02-23 22:32:44','',0),(221,'Итерация','Description','RICHTEXT','',NULL,'Y',48,20,'2006-02-23 22:33:02','2010-06-06 18:06:08','',0),(222,'Стили (CSS)','CSSBlock','LARGETEXT','',NULL,'Y',48,30,'2006-02-23 22:33:51','2006-02-23 22:33:51','',0),(223,'Верхний колонтитул','Header','LARGETEXT','',NULL,'Y',48,40,'2006-02-23 22:34:43','2006-02-23 22:34:43','',0),(224,'Нижний колонтитул','Footer','LARGETEXT','',NULL,'Y',48,50,'2006-02-23 22:35:17','2006-02-23 22:35:17','',0),(225,'С оглавлением в начале страницы','HeaderContents','CHAR','Y',NULL,'Y',48,60,'2006-02-23 22:36:50','2006-02-23 22:36:50','',0),(226,'Использовать сборки','IsBuilds','CHAR','N','N','Y',36,180,'2006-02-25 15:28:06','2010-06-06 18:05:29','',0),(227,'Номер','Caption','INTEGER','1','Y','Y',49,10,'2006-02-25 15:49:01','2006-02-25 15:49:01','',0),(228,'Комментарий','Description','RICHTEXT','',NULL,'Y',49,20,'2006-02-25 15:53:12','2006-02-25 15:53:12','',0),(229,'Результат проверки','Result','RICHTEXT','',NULL,'N',49,30,'2006-02-25 15:53:30','2006-02-25 15:53:30','',0),(230,'Релиз','Release','REF_pm_ReleaseId','','N',NULL,49,40,'2006-02-25 15:53:52','2006-02-25 15:53:52','',0),(231,'Нумеровать разделы','SectionNumbers','CHAR','Y',NULL,'Y',48,70,'2006-02-25 17:22:25','2006-02-25 17:22:25','',0),(232,'Сборка','Build','REF_pm_BuildId','','Y','Y',50,10,'2006-02-26 16:11:12','2006-02-26 16:11:12','',0),(233,'Задача','Task','REF_pm_TaskId','','Y','Y',50,20,'2006-02-26 16:11:25','2006-02-26 16:11:25','',0),(234,'Название','Caption','TEXT','','Y','Y',51,10,'2006-03-06 22:01:49','2006-03-06 22:01:49','',0),(235,'Ссылочное имя','ReferenceName','TEXT','','Y','Y',51,20,'2006-03-06 22:02:05','2006-03-06 22:02:05','',0),(236,'Адрес','Caption','TEXT','','Y','Y',52,10,'2006-03-06 22:03:00','2006-03-06 22:03:00','',0),(237,'Описание','Description','RICHTEXT','',NULL,'Y',52,20,'2006-03-06 22:03:17','2006-03-06 22:03:17','',0),(238,'Категория','Category','REF_cms_LinkCategoryId','','Y','Y',52,30,'2006-03-06 22:03:44','2006-03-06 22:03:44','',0),(239,'Заказчик принимает участие в проекте','IsUserInProject','CHAR','N',NULL,'N',36,300,'2006-03-08 09:05:26','2006-03-08 09:05:26','',0),(240,'Активна','IsActive','CHAR','Y',NULL,'Y',32,30,'2006-03-08 10:46:25','2006-03-08 10:46:25','',0),(241,'Опубликована','IsPublished','CHAR','N',NULL,'Y',52,40,'2006-03-09 22:34:08','2006-03-09 22:34:08','',0),(242,'Использовать итерации фиксированной длительности','IsFixedRelease','CHAR','Y','N','Y',36,190,'2006-03-11 12:59:54','2010-06-06 18:05:29','',0),(243,'Длительность итерации в неделях','ReleaseDuration','INTEGER','1','N','Y',36,200,'2006-03-11 13:01:51','2010-06-06 18:05:29','',0),(1310,'Блог','Blog','INTEGER','0','Y','N',316,40,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(245,'Название','Caption','TEXT','','Y','Y',55,10,'2006-03-16 21:18:41','2006-03-16 21:18:41','',0),(246,'Wiki страница','Wiki','REF_WikiPageId','','Y','Y',56,10,'2006-03-16 21:19:55','2006-03-16 21:19:55','',0),(247,'Тэг','Tag','REF_TagId','','Y','Y',56,20,'2006-03-16 21:20:14','2006-03-16 21:20:14','',0),(248,'Тип Wiki страницы','WikiReferenceName','TEXT','',NULL,NULL,56,30,'2006-03-17 08:24:02','2006-03-17 08:24:02','',0),(249,'Название','Caption','TEXT','','Y','Y',57,10,'2006-03-21 23:28:06','2006-03-21 23:28:06','',0),(250,'Язык проекта','Language','REF_cms_LanguageId','1',NULL,'Y',5,38,'2006-03-21 23:29:30','2006-03-21 23:29:30','',0),(251,'Кодовое значение','CodeName','TEXT','','Y','Y',57,20,'2006-03-21 23:30:11','2006-03-21 23:30:11','',0),(252,'Язык проекта','Language','TEXT','',NULL,NULL,23,100,'2006-03-21 23:44:01','2006-03-21 23:44:01','',0),(253,'Пожелание','Request','REF_pm_ChangeRequestId','','Y',NULL,58,10,'2006-03-26 10:12:47','2006-03-26 10:12:47','',0),(254,'Тэг','Tag','REF_TagId','','Y','Y',58,20,'2006-03-26 10:13:00','2006-03-26 10:13:00','',0),(255,'Является шаблоном','IsTemplate','CHAR','N','Y','N',9,80,'2006-03-26 17:22:13','2006-03-26 17:22:13','',0),(256,'Несколько конфигураций программного продукта','IsConfigurations','CHAR','N',NULL,'N',5,55,'2006-03-27 23:29:16','2006-03-27 23:29:16','',0),(257,'Название','Caption','TEXT','','Y','Y',59,10,'2006-03-27 23:31:22','2006-03-27 23:31:22','',0),(258,'Особенности конфигурации','Details','RICHTEXT','','Y','Y',59,20,'2006-03-27 23:32:01','2006-03-27 23:32:01','',0),(1416,'Слабые стороны','Weaknesses','LARGETEXT',NULL,'N','Y',341,40,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0),(262,'Описание','Caption','TEXT','',NULL,'Y',60,10,'2010-06-06 18:05:01','2010-06-06 18:05:01','',0),(263,'Номер','Caption','TEXT','','Y','Y',61,10,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0),(264,'Описание','Description','LARGETEXT','',NULL,'Y',61,20,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0),(265,'Имя файла','BackupFileName','TEXT','','Y',NULL,60,20,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0),(266,'Название компании','Caption','TEXT','','Y','Y',62,10,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0),(1306,'Версия','Version','VARCHAR',NULL,'N','Y',74,50,'2010-10-01 17:16:03','2010-10-01 17:16:03','',0),(268,'Язык интерфейса','Language','REF_cms_LanguageId','','Y','Y',62,30,'2010-06-06 18:05:02','2010-06-06 18:05:02','',0),(269,'Публиковать домашнюю страницу проекта','IsKnowledgeBase','CHAR','N',NULL,'Y',47,50,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(270,'Публиковать информацию о релизах проекта','IsReleases','CHAR','N',NULL,'Y',47,60,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(271,'Публиковать страницу ввода пожеланий','IsChangeRequests','CHAR','Y',NULL,'Y',47,25,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(272,'E-mail внешнего автора','ExternalEmail','TEXT','',NULL,'Y',35,60,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(273,'Имя','Caption','TEXT','','Y','Y',63,10,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(274,'E-mail','Email','TEXT','','Y','Y',63,20,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(275,'Логин','Login','TEXT','','Y','Y',63,30,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(276,'ICQ','ICQ','TEXT','',NULL,'Y',63,40,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(277,'Телефон','Phone','TEXT','',NULL,'Y',63,50,'2010-06-06 18:05:03','2010-06-06 18:05:03','',0),(278,'Пароль','Password','TEXT','','Y','N',63,60,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0),(279,'Пользователь','SystemUser','REF_cms_UserId','','Y',NULL,3,5,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0),(280,'Перекрыть атрибуты пользователя','OverrideUser','CHAR','N',NULL,'Y',3,19,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0),(281,'Хеш сессии','SessionHash','TEXT','',NULL,'N',63,70,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0),(282,'Название','Caption','TEXT','','Y','Y',64,10,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0),(283,'Описание','Description','RICHTEXT','','N','Y',64,20,'2010-06-06 18:05:04','2010-06-06 18:05:04','',0),(1415,'Сильные стороны','Strengths','LARGETEXT',NULL,'N','Y',341,30,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0),(285,'Функция','Function','REF_pm_FunctionId','',NULL,'Y',22,38,'2010-06-06 18:05:05','2010-06-06 18:05:05','',0),(286,'Определять порядок следования задач','IsTasksDepend','CHAR','Y',NULL,'Y',36,250,'2010-06-06 18:05:05','2010-06-06 18:05:05','',0),(1414,'Функция','Feature','REF_pm_FunctionId',NULL,'Y','Y',341,20,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0),(1413,'Продукт','Competitor','REF_pm_CompetitorId',NULL,'Y','Y',341,10,'2011-02-21 21:08:36','2011-02-21 21:08:36',NULL,0),(1412,'Описание','Description','LARGETEXT',NULL,'N','Y',340,20,'2011-02-21 21:08:35','2011-02-21 21:08:35',NULL,0),(1411,'Название','Caption','TEXT',NULL,'Y','Y',340,10,'2011-02-21 21:08:35','2011-02-21 21:08:35',NULL,0),(292,'Закреплять ответственных за высокоуровневыми функциями','IsResponsibleForFunctions','CHAR','Y',NULL,'Y',36,160,'2010-06-06 18:05:06','2010-06-06 18:05:06','',0),(293,'Может участвовать в нескольких проектах','IsShared','CHAR','Y',NULL,'N',63,65,'2010-06-06 18:05:06','2010-06-06 18:05:06','',0),(294,'Использовать перекрестную проверку задач','IsCrossChecking','CHAR','Y',NULL,'Y',36,260,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0),(295,'Использовать фазу проектирования','IsDesign','CHAR','N',NULL,'Y',36,80,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0),(296,'Разрешать отклонения от методологии','IsHighTolerance','CHAR','N',NULL,'Y',36,210,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0),(1410,'Автор','Author','REF_pm_ParticipantId',NULL,'Y','N',337,60,'2011-02-21 21:08:34','2011-02-21 21:08:34',NULL,0),(298,'Пользователь','User','INTEGER','','Y',NULL,65,10,'2010-06-06 18:05:07','2010-06-06 18:05:07','',0),(299,'Настройка','Settings','TEXT','','Y',NULL,65,20,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0),(300,'Значение','Value','TEXT','','Y',NULL,65,30,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0),(302,'Является администратором','IsAdmin','CHAR','N',NULL,'N',63,67,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0),(303,'Запланировано','IsPlanned','CHAR','N',NULL,NULL,11,80,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0),(304,'Запланировано','IsPlanned','CHAR','N',NULL,NULL,12,70,'2010-06-06 18:05:08','2010-06-06 18:05:08','',0),(305,'Имя файла','FileName','TEXT','',NULL,NULL,61,30,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0),(306,'Имя файла протокола','LogFileName','TEXT','',NULL,NULL,61,40,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0),(307,'Пользователь','UserId','TEXT','','Y','Y',66,10,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0),(308,'URL','URL','LARGETEXT','','Y','Y',66,20,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0),(309,'IP адрес','Caption','TEXT','','Y','Y',67,10,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0),(310,'Страна','Country','TEXT','','Y','Y',67,20,'2010-06-06 18:05:09','2010-06-06 18:05:09','',0),(311,'Город','City','TEXT','','Y','Y',67,30,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0),(312,'Среднее время проверки выполненной задачи, ч.','VerificationTime','INTEGER','1','Y','Y',36,270,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0),(313,'Принимает участие в проекте','IsActive','CHAR','Y','Y',NULL,3,70,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0),(314,'Название','Caption','TEXT','','Y','Y',68,10,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0),(315,'Описание','Description','TEXT','','Y','Y',68,20,'2010-06-06 18:05:10','2010-06-06 18:05:10','',0),(316,'Ссылка (RSS)','RssLink','TEXT','','Y','Y',68,30,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0),(317,'Язык','Language','REF_cms_LanguageId','','Y','Y',68,40,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0),(318,'Публичный','IsPublic','CHAR','','N','Y',68,50,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0),(319,'Заголовок','Caption','TEXT','','Y','Y',69,10,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0),(320,'Описание','Description','TEXT','','Y','Y',69,20,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0),(321,'Ссылка','HtmlLink','TEXT','','Y','Y',69,30,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0),(322,'Новостной канал','NewsChannel','REF_pm_NewsChannelId','','Y',NULL,69,40,'2010-06-06 18:05:11','2010-06-06 18:05:11','',0),(323,'Новостной канал','NewsChannel','REF_pm_NewsChannelId','','Y','Y',70,10,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0),(324,'Проект','Project','REF_pm_ProjectId','','Y',NULL,70,20,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0),(1378,'Редактор содержимого','ContentEditor','TEXT','','Y','N',9,140,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0),(327,'Заголовок (Английский)','HeaderEn','LARGETEXT','',NULL,'Y',28,80,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0),(328,'Окончание (Английский)','FooterEn','LARGETEXT','',NULL,'Y',28,90,'2010-06-06 18:05:12','2010-06-06 18:05:12','',0),(329,'Адресат','ToParticipant','REF_pm_ParticipantId','','Y','Y',72,10,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0),(330,'Тема','Subject','LARGETEXT','',NULL,'Y',72,20,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0),(331,'Содержание','Content','LARGETEXT','',NULL,'Y',72,30,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0),(332,'Отправитель','FromParticipant','REF_pm_ParticipantId','','Y','Y',72,40,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0),(333,'Skype','Skype','TEXT','',NULL,'N',3,26,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0),(334,'Skype','Skype','TEXT','',NULL,'Y',63,45,'2010-06-06 18:05:13','2010-06-06 18:05:13','',0),(1377,'Редактор документов','WikiEditorClass','TEXT','WikiSyntaxEditor','Y','Y',5,250,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0),(1376,'Описание','Description','TEXT',NULL,'N','Y',75,60,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0),(337,'Тестовый сценарий','TestScenario','REF_WikiPageId','','Y','Y',74,10,'2010-06-06 18:05:14','2010-06-06 18:05:14','',0),(1307,'Проект','Target','REF_pm_ProjectId',NULL,'Y','Y',316,10,'2010-10-01 17:16:24','2010-10-01 17:16:24','',0),(340,'Тест','Test','REF_pm_TestId','','Y','Y',75,10,'2010-06-06 18:05:14','2010-06-06 18:05:14','',0),(341,'Тестовый случай','TestCase','REF_WikiPageId','','Y','Y',75,20,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0),(342,'Успешный результат','Success','CHAR','',NULL,'Y',75,30,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0),(343,'Тестировал','Tester','REF_pm_ParticipantId','','Y','Y',75,40,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0),(345,'Тест','Test','REF_pm_TestId','',NULL,NULL,11,90,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0),(346,'Сборка','Build','REF_pm_BuildId','N',NULL,'Y',11,28,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0),(347,'Релиз','Release','REF_pm_ReleaseId','','Y','Y',11,27,'2010-06-06 18:05:15','2010-06-06 18:05:15','',0),(349,'Требуется утверждение пожеланий','RequestApproveRequired','CHAR','Y',NULL,'Y',36,130,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0),(351,'Что я сделал вчера?','WasYesterday','RICHTEXT','','Y','Y',76,10,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0),(352,'Что я планирую сделать сегодня?','WhatToday','RICHTEXT','','Y','Y',76,20,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0),(353,'Текущие проблемы','CurrentProblems','RICHTEXT','','Y','Y',76,30,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0),(354,'Участник','Participant','REF_pm_ParticipantId','','Y',NULL,76,40,'2010-06-06 18:05:16','2010-06-06 18:05:16','',0),(355,'Использовать ежедневные митинги','UseScrums','CHAR','N','N','Y',36,70,'2010-06-06 18:05:16','2010-10-01 17:16:29','',0),(356,'Название','Caption','TEXT','','Y','Y',77,10,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0),(357,'Описание','Description','RICHTEXT','',NULL,'Y',77,20,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0),(358,'Окружение','Environment','REF_pm_EnvironmentId','',NULL,'Y',74,35,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0),(360,'Используется несколько окружений','UseEnvironments','CHAR','N',NULL,'Y',36,170,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0),(362,'Файл','File','FILE','','Y','Y',78,10,'2010-06-06 18:05:17','2010-06-06 18:05:17','',0),(363,'Описание','Description','LARGETEXT','',NULL,'Y',78,20,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0),(364,'Объект','ObjectId','INTEGER','','Y',NULL,78,30,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0),(365,'Класс объект','ObjectClass','TEXT','','Y',NULL,78,40,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0),(366,'Проверка тестового случая','TestCaseExecution','REF_pm_TestCaseExecutionId','',NULL,'N',22,37,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0),(367,'Проверка тестового случая','TestCaseExecution','REF_pm_TestCaseExecutionId','',NULL,NULL,11,90,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0),(368,'Проверка тестового случая','TestCaseExecution','REF_pm_TestCaseExecutionId','',NULL,NULL,12,80,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0),(369,'Используется управление вехами проекта','HasMilestones','CHAR','N',NULL,'Y',36,20,'2010-06-06 18:05:18','2010-06-06 18:05:18','',0),(370,'Дата','MilestoneDate','DATE','','Y','Y',79,10,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0),(371,'Название','Caption','TEXT','','Y','Y',79,20,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0),(372,'Описание','Description','RICHTEXT','',NULL,'Y',79,30,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0),(373,'Пройдена','Passed','CHAR','N',NULL,'Y',79,40,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0),(374,'Митинг','Meeting','REF_pm_MeetingId','','Y','Y',80,10,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0),(375,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',80,20,'2010-06-06 18:05:19','2010-06-06 18:05:19','',0),(1267,'Использовать планирование релизов','IsReleasesUsed','CHAR','Y','N','Y',36,5,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(377,'Релиз','Release','REF_pm_ReleaseId','','Y','Y',81,10,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0),(378,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId','','Y','Y',81,20,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0),(379,'Заметка','Content','LARGETEXT','',NULL,'Y',81,30,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0),(380,'Название','Caption','TEXT','',NULL,'Y',82,10,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0),(381,'Задача','Task','REF_pm_TaskId','','N','Y',82,20,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0),(382,'Участник','Participant','REF_pm_ParticipantId','','Y','Y',82,30,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0),(383,'Описание','Description','TEXT','','N','Y',82,30,'2010-06-06 18:05:20','2010-06-06 18:05:20','',0),(384,'Завершена','Completed','CHAR','','N','Y',82,50,'2010-06-06 18:05:21','2010-06-06 18:05:21','',0),(1375,'Описание','Description','LARGETEXT',NULL,'N','Y',63,110,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0),(1374,'Описание','Description','LARGETEXT',NULL,'N','Y',6,15,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0),(1426,'Значение','ResourceValue','TEXT',NULL,'Y','Y',344,20,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0),(1425,'Ключ','ResourceKey','TEXT',NULL,'Y','Y',344,10,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0),(391,'Пользовательское поле 2','UserField2','TEXT','0','Y','N',9,90,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0),(392,'Название','Caption','TEXT','','Y','Y',85,10,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0),(393,'Исходное пожелание','SourceRequest','REF_pm_ChangeRequestId','','Y','Y',86,10,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0),(394,'Целевое пожелание','TargetRequest','REF_pm_ChangeRequestId','','Y','Y',86,20,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0),(395,'Тип связи','LinkType','REF_pm_ChangeRequestLinkTypeId','','Y','Y',86,30,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0),(398,'Проект закрыт','IsClosed','CHAR','N',NULL,'Y',5,105,'2010-06-06 18:05:22','2010-06-06 18:05:22','',0),(399,'Трудоемкость','Estimation','FLOAT','',NULL,'Y',22,32,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0),(400,'Оценка трудоемкости','RequestEstimationRequired','VARCHAR','EstimationStoryPointsStrategy','Y','Y',36,120,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0),(401,'Ответственный','Owner','REF_pm_ParticipantId','','N','N',22,45,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0),(402,'Владелец','Owner','REF_pm_ParticipantId','',NULL,NULL,55,20,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0),(403,'Название','Caption','TEXT','','Y','Y',87,10,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0),(404,'Ссылочное имя','ReferenceName','TEXT','','Y','Y',87,20,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0),(405,'Тип','Type','REF_pm_IssueTypeId','',NULL,'Y',22,22,'2010-06-06 18:05:23','2010-06-06 18:05:23','',0),(1373,'Описание','Description','LARGETEXT',NULL,'N','Y',20,15,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0),(407,'В архиве','IsArchived','CHAR','N','Y',NULL,8,70,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0),(408,'Черновой вариант','IsDraft','CHAR','N','N','Y',14,18,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0),(409,'Причина изменения даты','ReasonToChangeDate','RICHTEXT','',NULL,'Y',79,50,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0),(410,'Результат завершения','CompleteResult','RICHTEXT','',NULL,'Y',79,60,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0),(411,'Название','Caption','TEXT','','Y','Y',88,10,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0),(412,'Описание','Description','LARGETEXT','',NULL,'Y',88,20,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0),(413,'Название','Caption','TEXT','','Y','Y',89,10,'2010-06-06 18:05:24','2010-06-06 18:05:24','',0),(414,'Варианты ответов','Answers','LARGETEXT','',NULL,'Y',89,20,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(415,'Является разделом','IsSection','CHAR','N',NULL,NULL,89,30,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(416,'Опрос','Poll','REF_pm_PollId','','Y',NULL,89,40,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(417,'Опрос','Poll','REF_pm_PollId','','Y','Y',90,10,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(418,'Пользователь','User','REF_cms_UserId','','Y','Y',90,20,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(419,'Позиция опросника','PollItem','REF_pm_PollItemId','','Y','Y',91,10,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(420,'Результат опроса','PollResult','REF_pm_PollResultId','','Y','Y',91,20,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(421,'Ответ','Answer','LARGETEXT','','Y','Y',91,30,'2010-06-06 18:05:25','2010-06-06 18:05:25','',0),(422,'Текущий','IsCurrent','CHAR','Y','Y','Y',90,30,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0),(423,'Дата завершения','CommitDate','DATE','',NULL,NULL,90,40,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0),(424,'Источник','SourceName','TEXT','','Y','Y',92,10,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0),(425,'Класс','ClassName','TEXT','','Y','Y',92,20,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0),(426,'Сущность','EntityName','TEXT','','Y','Y',92,30,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0),(427,'Старый идентификатор','OldObjectId','INTEGER','','Y','Y',92,40,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0),(428,'Новый идентификатор','NewObjectId','INTEGER','','Y','Y',92,50,'2010-06-06 18:05:26','2010-06-06 18:05:26','',0),(429,'Сущность','EntityName','TEXT','','Y','Y',93,10,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(430,'Объект','ObjectId','INTEGER','','Y','Y',93,20,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(431,'Ссылка','ReferenceUrl','TEXT',NULL,'Y','Y',94,10,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(432,'Название источника','ServerName','TEXT',NULL,'Y','Y',94,20,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(433,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',94,30,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(434,'Цель','Caption','TEXT',NULL,'Y','Y',95,10,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(435,'Дата','Deadline','DATE',NULL,'Y','Y',95,20,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(436,'Комментарий','Comment','LARGETEXT',NULL,NULL,'Y',95,30,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(437,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId',NULL,'N',NULL,95,40,'2010-06-06 18:05:27','2010-06-06 18:05:27','',0),(438,'Повестка','Agenda','RICHTEXT',NULL,NULL,'Y',18,15,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(439,'Время','MeetingTime','TEXT',NULL,NULL,'Y',18,40,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(440,'Accepted','Подтверждено','CHAR','N',NULL,'Y',19,40,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(441,'Отклонено','Rejected','CHAR','N',NULL,'Y',19,50,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(442,'Причина отклонения','RejectReason','LARGETEXT',NULL,NULL,'Y',19,60,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(443,'Подтверждено','Accepted','CHAR','N','Y','Y',80,30,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(444,'Отклонено','Rejected','FLOAT','N','Y','Y',80,40,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(445,'Причина отклонения','RejectReason','FLOAT',NULL,NULL,'Y',80,50,'2010-06-06 18:05:28','2010-06-06 18:05:28','',0),(446,'Сущность','EntityName','TEXT',NULL,'Y','Y',96,10,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0),(447,'Идентификатор','ObjectId','FLOAT',NULL,'Y','Y',96,20,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0),(1006,'Активирован','IsActivated','CHAR','N','Y','N',63,90,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0),(1007,'Тема','Subject','TEXT',NULL,'Y','Y',201,10,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0),(451,'Релиз','Release','REF_pm_ReleaseId',NULL,'Y','Y',97,10,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0),(452,'Дата сбора метрик','SnapshotDate','DATE',NULL,'Y','Y',97,20,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0),(453,'Загрузка задачами','Workload','INTEGER',NULL,'Y','Y',97,30,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0),(454,'Остаточная трудоемкость','LeftWorkload','INTEGER',NULL,'Y','Y',97,40,'2010-06-06 18:05:29','2010-06-06 18:05:29','',0),(455,'Прошло дней','SnapshotDays','INTEGER',NULL,'Y','Y',97,50,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(456,'Планируемая загрузка','PlannedWorkload','INTEGER',NULL,'Y','Y',97,60,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(457,'Участники выбирают себе задачи самостоятельно','IsParticipantsTakeTasks','CHAR','N','N','Y',36,230,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(458,'Публичность','Access','TEXT',NULL,'Y','Y',23,110,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(459,'Язык','Language','REF_cms_LanguageId',NULL,'Y','Y',63,35,'2010-06-06 18:05:30','2010-06-06 18:05:36','',0),(460,'Использовать декомпозицию по функциям','UseFunctionalDecomposition','CHAR','Y','N','Y',36,150,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(461,'Класс объекта','EntityName','TEXT',NULL,'Y','Y',98,10,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(462,'Идентификатор объекта','ObjectId','INTEGER',NULL,'Y','Y',98,20,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(463,'Активна','IsActive','CHAR','N','Y','Y',98,30,'2010-06-06 18:05:30','2010-06-06 18:05:30','',0),(464,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',98,40,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(465,'Затрачено, ч.','Capacity','FLOAT',NULL,'Y','Y',82,20,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(466,'Оставшаяся трудоемкость, ч.','LeftWork','FLOAT',NULL,'N','Y',15,75,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(467,'В архиве','IsArchived','CHAR','N','Y','N',9,100,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(468,'Пользовательское поле 3','UserField3','INTEGER',NULL,'Y','N',9,110,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(469,'Название','Caption','TEXT',NULL,'Y','Y',99,10,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(470,'Название','Caption','TEXT','','Y','Y',100,10,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(471,'Описание','Description','RICHTEXT','',NULL,'Y',100,20,'2010-06-06 18:05:31','2010-06-06 18:05:31','',0),(472,'Стили (CSS)','CSSBlock','LARGETEXT','',NULL,'Y',100,30,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(473,'Верхний колонтитул','Header','LARGETEXT','',NULL,'Y',100,40,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(474,'Нижний колонтитул','Footer','LARGETEXT','',NULL,'Y',100,50,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(475,'С оглавлением в начале страницы','HeaderContents','CHAR','Y',NULL,'Y',100,60,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(476,'Нумеровать разделы','SectionNumbers','CHAR','Y',NULL,'Y',100,70,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(477,'Отслеживать сроки выполнения задач и реализации пожеланий','IsDeadlineUsed','CHAR','N','N','Y',36,240,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(1266,'Использовать управление версиями','IsVersionsUsed','CHAR','Y','N','Y',36,20,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(479,'Плановый релиз','PlannedRelease','REF_pm_VersionId',NULL,'N','N',22,150,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(480,'Назначение','Caption','TEXT',NULL,'Y','Y',101,10,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(481,'Код','CodeName','TEXT',NULL,'Y','Y',101,20,'2010-06-06 18:05:32','2010-06-06 18:05:32','',0),(482,'Адрес подписчика','Caption','TEXT',NULL,'Y','Y',102,10,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(483,'Рассылка','Notification','REF_cms_EmailNotificationId',NULL,'Y','Y',102,20,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(484,'Состояние подписки','IsActive','CHAR','Y','Y','Y',102,30,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(485,'Текст рассылки (Wiki)','Content','LARGETEXT',NULL,'N','Y',101,30,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(486,'Пользователь','cms_UserId','INTEGER',NULL,'N','Y',46,30,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(487,'Ежедневная загрузка, ч.','Capacity','FLOAT','0','Y','Y',4,15,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(488,'Название','Caption','TEXT',NULL,'Y','Y',103,10,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(489,'Девиз','Tagline','LARGETEXT',NULL,'Y','Y',103,20,'2010-06-06 18:05:33','2010-06-06 18:05:33','',0),(490,'Описание','Description','LARGETEXT',NULL,'Y','Y',103,30,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(491,'Автор','Author','REF_cms_UserId',NULL,'Y','N',103,40,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(492,'Команда','Team','REF_co_TeamId',NULL,'Y','Y',104,10,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(493,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',104,20,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(494,'Роли в команде','TeamRoles','LARGETEXT',NULL,'Y','Y',104,30,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(495,'Тип','LicenseType','TEXT',NULL,'Y','Y',105,10,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(496,'Значение','LicenseValue','TEXT',NULL,'Y','N',105,20,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(497,'Ключ','LicenseKey','TEXT',NULL,'Y','N',105,30,'2010-06-06 18:05:34','2010-06-06 18:05:34','',0),(498,'Название','Caption','TEXT',NULL,'Y','Y',106,10,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(499,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',106,20,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(500,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',107,10,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(501,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',107,20,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(502,'Публиковать документацию','IsPublicDocumentation','CHAR','N','N','Y',47,70,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(503,'Публиковать артефакты','IsPublicArtefacts','CHAR','N','N','Y',47,80,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(504,'Роль в проекте','Caption','TEXT',NULL,'Y','Y',108,10,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(505,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',108,20,'2010-06-06 18:05:35','2010-06-06 18:05:35','',0),(506,'Активна','IsActive','CHAR','Y','Y','Y',108,30,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0),(507,'Требуемая занятость, часов в день','RequiredWorkload','INTEGER','8','Y','Y',108,40,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0),(508,'Оплата часа работы','PriceOfHour','TEXT','0','Y','Y',108,50,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0),(509,'Описание','Description','RICHTEXT',NULL,'Y','Y',108,60,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0),(510,'Дополнительные требования','Requirements','RICHTEXT',NULL,'N','Y',108,70,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0),(511,'Фотография','Photo','IMAGE',NULL,'N','N',63,80,'2010-06-06 18:05:36','2010-06-06 18:05:36','',0),(1034,'Кодовое значение','CodeName','TEXT',NULL,'Y','Y',208,20,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(1033,'Название','Caption','TEXT',NULL,'Y','Y',208,10,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(514,'Название','Caption','TEXT',NULL,'Y','Y',109,10,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0),(515,'Название','Caption','TEXT',NULL,'Y','Y',110,10,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0),(516,'Категория','Category','REF_co_ServiceCategoryId',NULL,'Y','Y',110,20,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0),(517,'Описание','Description','LARGETEXT',NULL,'Y','Y',110,30,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0),(518,'Стоимость','Cost','LARGETEXT',NULL,'Y','Y',110,40,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0),(519,'Автор','Author','REF_cms_UserId',NULL,'N','N',110,50,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0),(520,'Команда','Team','REF_co_TeamId',NULL,'N','N',110,60,'2010-06-06 18:05:37','2010-06-06 18:05:37','',0),(521,'Услуга','Service','REF_co_ServiceId',NULL,'Y','Y',111,10,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(522,'Заказчик','Customer','REF_cms_UserId',NULL,'Y','Y',111,20,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(523,'Отзыв','Response','RICHTEXT',NULL,'N','Y',111,30,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(524,'Закрыта','IsClosed','CHAR','N','Y','Y',111,40,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(525,'Пользователь','SystemUser','REF_cms_UserId',NULL,'N','N',30,80,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(527,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',112,10,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(528,'Путь к репозиторию','SVNPath','TEXT',NULL,'Y','Y',112,20,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(529,'Имя пользователя','LoginName','TEXT',NULL,'N','Y',112,30,'2010-06-06 18:05:38','2010-06-06 18:05:38','',0),(530,'Пароль','SVNPassword','PASSWORD',NULL,'N','Y',112,40,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0),(1264,'Назначаются встречи с участниками проекта','HasMeetings','CHAR','N','N','Y',5,210,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(532,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',113,10,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0),(533,'Версия','Version','TEXT','0','Y','Y',113,20,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0),(534,'Описание','Description','LARGETEXT',NULL,'N','Y',113,30,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0),(536,'Проект','Project','REF_pm_ProjectId',NULL,'Y','N',114,20,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0),(537,'Стоимость','Cost','TEXT',NULL,'N','Y',114,30,'2010-06-06 18:05:39','2010-06-06 18:05:39','',0),(538,'Максимальное время реализации, дней','Duration','INTEGER','1','Y','Y',114,40,'2010-06-06 18:05:39','2010-06-06 18:05:48','',0),(540,'Комментарий','Comment','RICHTEXT',NULL,'N','Y',114,50,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0),(541,'Пожелание','IssueOutsourcing','REF_co_IssueOutsourcingId',NULL,'Y','N',115,10,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0),(542,'Стоимость','Cost','LARGETEXT',NULL,'Y','Y',115,20,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0),(543,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','N',115,30,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0),(544,'Комментарий','Comment','RICHTEXT',NULL,'N','Y',115,40,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0),(545,'Принято','IsAccepted','CHAR','N','Y','Y',115,50,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0),(546,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',116,10,'2010-06-06 18:05:40','2010-06-06 18:05:40','',0),(547,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',116,20,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0),(548,'Адресат','Addressee','TEXT',NULL,'Y','Y',116,30,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0),(549,'Название','Caption','TEXT',NULL,'Y','Y',117,10,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0),(550,'Идентификатор','ObjectId','INTEGER',NULL,'Y','Y',117,20,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0),(551,'Сущность','EntityRefName','TEXT',NULL,'Y','Y',117,30,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0),(552,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',118,10,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0),(553,'Загрузка артефакта','DownloadAction','REF_pm_DownloadActionId',NULL,'Y','Y',118,20,'2010-06-06 18:05:41','2010-06-06 18:05:41','',0),(554,'Название','Caption','TEXT',NULL,'Y','Y',119,10,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0),(555,'Название','Caption','TEXT',NULL,'Y','Y',120,10,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0),(556,'Совет','Advise','RICHTEXT',NULL,'Y','Y',120,20,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0),(557,'Тематика','Theme','REF_co_AdviseThemeId',NULL,'Y','Y',120,30,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0),(558,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',120,40,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0),(559,'Утвержден','IsApproved','CHAR','N','Y','Y',120,50,'2010-06-06 18:05:42','2010-06-06 18:05:42','',0),(560,'Участник','Participant','REF_pm_ParticipantId',NULL,'Y','Y',121,10,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0),(561,'Итерация','Iteration','REF_pm_ReleaseId',NULL,'Y','Y',121,20,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0),(562,'Метрика','Metric','TEXT',NULL,'Y','Y',121,30,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0),(563,'Значение метрики','MetricValue','FLOAT',NULL,'Y','Y',121,40,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0),(564,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',122,10,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0),(565,'Номер','Caption','TEXT',NULL,'Y','Y',122,20,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0),(566,'Название','Caption','TEXT',NULL,'Y','Y',123,10,'2010-06-06 18:05:43','2010-06-06 18:05:43','',0),(567,'Сумма','Volume','FLOAT',NULL,'Y','Y',123,20,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(568,'Комментарий','Comment','LARGETEXT',NULL,'Y','Y',123,30,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(569,'Счет','Bill','REF_co_BillId',NULL,'Y','Y',123,40,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(570,'Периодичность оплаты, дн.','Period','INTEGER',NULL,'Y','Y',126,30,'2010-06-06 18:05:44','2010-06-06 18:05:46','',0),(571,'Класс','ObjectClass','TEXT',NULL,'Y','Y',124,20,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(572,'Рейтинг','Rating','INTEGER',NULL,'Y','Y',124,30,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(573,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',125,10,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(574,'Адрес','IPAddress','TEXT',NULL,'Y','Y',125,20,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(575,'Рейтинг','Rating','REF_co_RatingId',NULL,'Y','Y',125,30,'2010-06-06 18:05:44','2010-06-06 18:05:44','',0),(576,'Название','Caption','TEXT',NULL,'Y','Y',126,10,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(577,'Стоимость','Cost','FLOAT',NULL,'Y','Y',126,20,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(578,'Периодичность','Period','INTEGER',NULL,'Y','Y',126,30,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(579,'Условия','Conditions','RICHTEXT',NULL,'Y','Y',126,40,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(580,'Опция','Option','REF_co_OptionId',NULL,'Y','Y',127,10,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(581,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',127,20,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(582,'Активна','IsActive','CHAR','Y','Y','Y',127,30,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(583,'Кодовое название','CodeName','TEXT',NULL,'Y','Y',126,50,'2010-06-06 18:05:45','2010-06-06 18:05:45','',0),(584,'Оплачена','IsPayed','CHAR','N','Y','Y',127,40,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(585,'Дата оплаты','PaymentDate','DATE',NULL,'Y','Y',127,50,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(586,'Итерация','Iteration','REF_pm_ReleaseId',NULL,'Y','Y',128,10,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(587,'Метрика','Metric','TEXT',NULL,'Y','Y',128,20,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(588,'Значение метрики','MetricValue','FLOAT',NULL,'Y','Y',128,30,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(589,'Релиз','Version','REF_pm_VersionId',NULL,'Y','Y',129,10,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(590,'Метрика','Metric','TEXT',NULL,'Y','Y',129,20,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(591,'Значение метрики','MetricValue','FLOAT',NULL,'Y','Y',129,30,'2010-06-06 18:05:46','2010-06-06 18:05:46','',0),(592,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',130,10,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0),(593,'IP','IPAddress','TEXT',NULL,'Y','Y',130,20,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0),(594,'Причина блокировки','BlockReason','TEXT',NULL,'Y','Y',130,30,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0),(595,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',131,10,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0),(596,'Количество попыток','RetryAmount','INTEGER','1','Y','Y',131,20,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0),(597,'Вопрос на русском','QuestionRussian','TEXT',NULL,'Y','Y',132,10,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0),(598,'Вопрос на английском','QuestionEnglish','TEXT',NULL,'Y','Y',132,20,'2010-06-06 18:05:47','2010-06-06 18:05:47','',0),(599,'Ответ на русском','Answer','TEXT',NULL,'Y','Y',132,30,'2010-06-06 18:05:47','2010-06-06 18:05:48','',0),(600,'Ответ на английском','AnswerEnglish','TEXT',NULL,'Y','Y',132,40,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0),(1000,'Черновик','IsDraft','CHAR','N','Y','N',9,120,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0),(1001,'Принимает участие','IsActive','CHAR','Y','Y','Y',104,40,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0),(1002,'Использовать планирование задач','IsPlanningUsed','CHAR','Y','Y','Y',36,10,'2010-06-06 18:05:48','2010-06-06 18:05:48','',0),(1003,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',23,120,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0),(1004,'Сообщение блога','BlogPost','REF_BlogPostId',NULL,'Y','Y',200,10,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0),(1005,'Тэг','Tag','REF_TagId',NULL,'Y','Y',200,20,'2010-06-06 18:05:49','2010-06-06 18:05:49','',0),(1008,'Текст сообщения','Content','LARGETEXT',NULL,'Y','Y',201,20,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0),(1009,'Автор','Author','REF_cms_UserId',NULL,'Y','N',201,30,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0),(1010,'Получатель пользователь','ToUser','REF_cms_UserId',NULL,'N','Y',201,40,'2010-06-06 18:05:50','2010-06-06 18:05:50','',0),(1011,'Получатель команда','ToTeam','REF_co_TeamId',NULL,'N','Y',201,50,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1012,'Вид поиска','SearchKind','TEXT',NULL,'Y','Y',202,10,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1013,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','Y',202,20,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1014,'Результат','Result','LARGETEXT',NULL,'Y','Y',202,30,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1263,'Проект по администрированию','AdminProject','REF_pm_ProjectId',NULL,'N','N',62,70,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(1016,'Язык проекта','Language','TEXT',NULL,'Y','Y',23,37,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1017,'Условия поиска','Conditions','LARGETEXT',NULL,'Y','Y',202,40,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1018,'Рейтинг','Rating','FLOAT','0','Y','N',63,100,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1019,'Рейтинг','Rating','FLOAT','0','Y','Y',103,50,'2010-06-06 18:05:51','2010-06-06 18:05:51','',0),(1020,'Рейтинг','Rating','FLOAT','0','Y','N',5,170,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0),(1021,'Содержание','Content','RICHTEXT',NULL,'Y','Y',203,10,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0),(1022,'Автор','Author','REF_cms_UserId',NULL,'Y','Y',203,20,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0),(1023,'Название','Caption','TEXT',NULL,'Y','Y',204,10,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0),(1024,'Статус','TeamState','REF_co_TeamStateId','1','Y','N',103,60,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0),(1025,'Описание','Description','TEXT',NULL,'Y','Y',204,20,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0),(1026,'Название','Caption','TEXT',NULL,'Y','Y',205,10,'2010-06-06 18:05:52','2010-06-06 18:05:52','',0),(1027,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',206,10,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0),(1028,'Роль','CommunityRole','REF_co_CommunityRoleId',NULL,'Y','Y',206,20,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0),(1029,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',207,10,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0),(1030,'Стоимость часа','HourCost','TEXT','0','Y','Y',207,20,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0),(1031,'Профессиональные навыки','Skills','LARGETEXT',NULL,'Y','Y',207,30,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0),(1032,'Владение инструментами','Tools','LARGETEXT',NULL,'Y','Y',207,40,'2010-06-06 18:05:53','2010-06-06 18:05:53','',0),(1035,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',209,10,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(1036,'Использовать бюджетирование в проекте','IsBugetUsed','CHAR','Y','N','Y',209,20,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(1037,'Валюта проекта','Currency','REF_pm_CurrencyId','1','Y','Y',209,30,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(1038,'Название','Caption','TEXT',NULL,'Y','Y',210,10,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(1039,'Модель оплаты','PaymentModel','REF_pm_PaymentModelId','1','Y','Y',209,40,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(1040,'Скрывать стоимость работ участников','HideParticipantsCost','CHAR','N','N','Y',209,50,'2010-06-06 18:05:54','2010-06-06 18:05:54','',0),(1041,'Оплата','Salary','FLOAT','0','Y','Y',3,80,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1042,'Название','Caption','TEXT',NULL,'Y','Y',211,10,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1043,'Название','Caption','TEXT',NULL,'Y','Y',212,10,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1044,'Название проекта','Caption','TEXT',NULL,'Y','Y',213,10,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1045,'Краткое описание','Description','RICHTEXT',NULL,'Y','Y',213,20,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1046,'Тип','Kind','REF_co_TenderKindId','1','Y','Y',213,30,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1047,'Состояние','State','REF_co_TenderStateId','1','Y','N',213,40,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1048,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','N',213,50,'2010-06-06 18:05:55','2010-06-06 18:05:55','',0),(1049,'Тендер','Tender','REF_co_TenderId',NULL,'Y','Y',214,10,'2010-06-06 18:05:56','2010-06-06 18:05:56','',0),(1050,'Файл','Attachment','FILE',NULL,'Y','Y',214,20,'2010-06-06 18:05:56','2010-06-06 18:05:56','',0),(1051,'Название','Caption','TEXT',NULL,'Y','Y',215,10,'2010-06-06 18:05:56','2010-06-06 18:05:56','',0),(1052,'Тендер','Tender','REF_co_TenderId',NULL,'Y','Y',216,10,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1053,'Команда','Team','REF_co_TeamId',NULL,'Y','Y',216,20,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1054,'Состояние','State','REF_co_TenderParticipanceStateId','1','Y','Y',216,30,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1055,'Название задачи','Caption','TEXT',NULL,'Y','Y',217,10,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1056,'Параметры','Parameters','LARGETEXT',NULL,'Y','Y',217,20,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1057,'Почтовый ящик','MailboxClass','TEXT','mailbox','Y','Y',45,40,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1058,'Проект','Project','REF_pm_ProjectId',NULL,'N','Y',216,40,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1059,'Является тендером','IsTender','CHAR','N','Y','N',5,180,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1060,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',213,60,'2010-06-06 18:05:57','2010-06-06 18:05:57','',0),(1061,'Почтовый адрес администратора','AdminEmail','TEXT','','N','Y',62,40,'2010-06-06 18:05:58','2010-06-06 18:05:58','',0),(1062,'Погрешность оценки, %','InitialEstimationError','INTEGER',NULL,'N','N',39,50,'2010-06-06 18:05:58','2010-06-06 18:05:58','',0),(1063,'Процент ошибок, %','InitialBugsInWorkload','INTEGER',NULL,'N','N',39,60,'2010-06-06 18:05:58','2010-06-06 18:05:58','',0),(1065,'Пользовательское поле 3','UserField3','TEXT',NULL,'N','N',9,130,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0),(1200,'Окружение','Environment','REF_pm_EnvironmentId',NULL,'N','N',22,27,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0),(1201,'Название','Caption','TEXT',NULL,'Y','Y',300,10,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0),(1202,'Описание','Description','RICHTEXT',NULL,'N','Y',300,20,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0),(1203,'Тестовый набор','TestSuite','REF_WikiPageId',NULL,'Y','Y',301,10,'2010-06-06 18:06:04','2010-06-06 18:06:04','',0),(1204,'Тестировщик','Assignee','REF_pm_ParticipantId',NULL,'N','Y',301,20,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1205,'Планируемая трудоемкость','Planned','FLOAT',NULL,'N','Y',301,30,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1206,'Тест план','TestPlan','REF_pm_TestPlanId',NULL,'Y','N',301,40,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1207,'Участники отчитываются о затраченном времени','IsReportsOnActivities','CHAR','N','N','Y',36,220,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1208,'Дата','ReportDate','DATE',NULL,'Y','Y',82,10,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1209,'Разрешать изменение логина и пароля пользователя','AllowToChangeLogin','CHAR','Y','N','Y',62,50,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1210,'Обнаружено в','SubmittedVersion','TEXT',NULL,'N','Y',22,50,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1211,'Заказчик выполняет приемку пожеланий','CustomerAcceptsIssues','CHAR','N','N','N',36,310,'2010-06-06 18:06:05','2010-06-06 18:06:05','',0),(1212,'Закреплять ответственного за пожеланием','IsResponsibleForIssue','CHAR','Y','N','Y',36,140,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0),(1214,'Выполнено в','ClosedInVersion','TEXT',NULL,'N','N',22,115,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0),(1215,'Название','Caption','TEXT',NULL,'Y','Y',302,10,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0),(1216,'Имя файла','FileName','TEXT',NULL,'Y','Y',302,20,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0),(1217,'MIME тип','MimeType','TEXT',NULL,'Y','Y',302,30,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0),(1218,'Файл','File','FILE',NULL,'Y','Y',302,40,'2010-06-06 18:06:06','2010-06-06 18:06:06','',0),(1219,'Название','Caption','TEXT',NULL,'Y','Y',303,10,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0),(1220,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','Y',303,20,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0),(1221,'Снимок','Snapshot','REF_cms_SnapshotId',NULL,'Y','Y',304,10,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0),(1222,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',304,20,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0),(1223,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',304,30,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0),(1224,'Элемент снимка','SnapshotItem','REF_cms_SnapshotItemId',NULL,'Y','Y',305,10,'2010-06-06 18:06:07','2010-06-06 18:06:07','',0),(1225,'Название атрибута','Caption','TEXT',NULL,'Y','Y',305,20,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0),(1226,'Ссылочное имя атрибута','ReferenceName','TEXT',NULL,'Y','Y',305,30,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0),(1227,'Значение атрибута','Value','TEXT',NULL,'Y','Y',305,40,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0),(1228,'Отображается на сайте','IsDisplayedOnSite','CHAR','N','N','N',7,20,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0),(1229,'Версия','Version','VARCHAR',NULL,'N','Y',8,80,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0),(1230,'Релиз','Version','REF_pm_VersionId',NULL,'N','N',49,50,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0),(1231,'Актуальна','IsActual','CHAR','Y','N','N',49,60,'2010-06-06 18:06:08','2010-06-06 18:06:08','',0),(1232,'Актуальна','IsActual','CHAR','Y','N','N',14,70,'2010-06-06 18:06:09','2010-06-06 18:06:09','',0),(1233,'Актуален','IsActual','CHAR','Y','N','N',39,70,'2010-06-06 18:06:09','2010-06-06 18:06:09','',0),(1234,'Отображать форму обратной связи DEVPROM','DisplayFeedbackForm','CHAR','Y','N','Y',62,60,'2010-06-06 18:06:09','2010-06-06 18:06:09','',0),(1235,'Требуется авторизация для скачивания','IsAuthorizedDownload','CHAR','N','N','N',8,32,'2010-06-06 18:06:09','2010-06-06 18:06:09','',0),(1409,'Переход','Transition','REF_pm_TransitionId',NULL,'N','N',337,50,'2011-02-21 21:08:33','2011-02-21 21:08:33',NULL,0),(1237,'Проектная роль','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',306,10,'2010-06-06 18:06:10','2010-06-06 18:06:10','',0),(1238,'Объект','ReferenceName','TEXT',NULL,'Y','Y',306,20,'2010-06-06 18:06:10','2010-06-06 18:06:10','',0),(1239,'Тип объекта','ReferenceType','TEXT',NULL,'Y','Y',306,30,'2010-06-06 18:06:11','2010-06-06 18:06:11','',0),(1240,'Доступ','AccessType','TEXT',NULL,'Y','Y',306,40,'2010-06-06 18:06:11','2010-06-06 18:06:11','',0),(1241,'Кодовое название','ReferenceName','TEXT',NULL,'N','Y',6,30,'2010-06-06 18:06:11','2010-06-06 18:06:11','',0),(1242,'Путь к файлам','RootPath','TEXT',NULL,'N','Y',112,25,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0),(1243,'Автор','Author','TEXT',NULL,'Y','Y',113,40,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0),(1244,'Дата','CommitDate','TEXT',NULL,'Y','Y',113,50,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0),(1245,'Приложение','Application','TEXT',NULL,'N','Y',49,70,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0),(1246,'Дата начала','StartDate','DATE',NULL,'N','Y',39,25,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0),(1247,'Дата окончания','FinishDate','DATE',NULL,'N','Y',39,30,'2010-06-06 18:06:12','2010-06-06 18:06:12','',0),(1248,'Публичный','IsPublic','CHAR','N','N','N',88,30,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0),(1249,'Хеш пользователя','AnonymousHash','TEXT',NULL,'N','N',90,50,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0),(1250,'Хеш значение','HashKey','TEXT',NULL,'Y','Y',307,10,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0),(1251,'Идентификаторы','Ids','RICHTEXT',NULL,'Y','Y',307,20,'2010-06-06 18:06:13','2010-06-06 18:06:13','',0),(1252,'Версия','Version','REF_pm_VersionId',NULL,'Y','Y',308,10,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0),(1253,'Дата метрики','SnapshotDate','DATE',NULL,'Y','Y',308,20,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0),(1254,'Фактическая загрузка','Workload','FLOAT',NULL,'Y','Y',308,30,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0),(1255,'Прошло дней','SnapshotDays','INTEGER',NULL,'Y','Y',308,40,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0),(1256,'Плановая загрузка','PlannedWorkload','FLOAT',NULL,'Y','Y',308,50,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0),(1257,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',309,10,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0),(1258,'Включать номер релиза','UseRelease','CHAR','Y','Y','Y',309,20,'2010-06-06 18:06:14','2010-06-06 18:06:14','',0),(1259,'Включать номер итерации','UseIteration','CHAR','Y','Y','Y',309,30,'2010-06-06 18:06:15','2010-06-06 18:06:15','',0),(1260,'Включать номер сборки','UseBuild','CHAR','Y','Y','Y',309,40,'2010-06-06 18:06:15','2010-06-06 18:06:15','',0),(1261,'text(sourcecontrol8)','IsSubversionUsed','CHAR','Y','N','N',5,190,'2010-10-01 17:15:57','2010-10-01 17:15:57','',0),(1262,'Выкладывать файлы проекта','IsArtefactsUsed','CHAR','Y','N','Y',5,200,'2010-10-01 17:15:57','2010-10-01 17:15:57','',0),(1265,'Проводятся опросы мнений участников','IsPollUsed','CHAR','Y','N','Y',5,220,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(1268,'Сообщение блога','BlogPost','REF_BlogPostId',NULL,'Y','Y',310,10,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(1269,'Содержание','Content','LARGETEXT',NULL,'Y','Y',310,20,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(1270,'Автор','SystemUser','REF_cms_UserId',NULL,'Y','Y',310,30,'2010-10-01 17:15:58','2010-10-01 17:15:58','',0),(1271,'Ид объекта','ObjectId','INTEGER',NULL,'Y','N',311,10,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1272,'Класс объекта','ObjectClass','TEXT',NULL,'Y','N',311,20,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1273,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',311,30,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1274,'Адрес электронной почты','Email','TEXT',NULL,'Y','Y',311,40,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1275,'Тип','Kind','TEXT',NULL,'Y','Y',312,10,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1276,'Дата начала','StartDate','DATE',NULL,'Y','Y',312,20,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1277,'Дата окончания','FinishDate','DATE',NULL,'Y','Y',312,30,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1278,'Год','IntervalYear','INTEGER',NULL,'Y','Y',312,40,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1279,'Месяц','IntervalMonth','INTEGER',NULL,'Y','Y',312,50,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1280,'День','IntervalDay','INTEGER',NULL,'Y','Y',312,60,'2010-10-01 17:15:59','2010-10-01 17:15:59','',0),(1281,'Название','Caption','TEXT',NULL,'Y','Y',312,70,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0),(1282,'Квартал','IntervalQuarter','INTEGER',NULL,'Y','Y',312,80,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0),(1283,'Неделя','IntervalWeek','INTEGER',NULL,'Y','Y',312,90,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0),(1284,'Базовая роль','ProjectRoleBase','REF_pm_ProjectRoleId',NULL,'Y','N',6,40,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0),(1285,'Почтовый сервер','HostAddress','TEXT',NULL,'Y','Y',313,10,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0),(1286,'Порт сервера','PortServer','INTEGER','110','Y','Y',313,20,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0),(1287,'Почтовый ящик','EmailAddress','TEXT',NULL,'Y','Y',313,30,'2010-10-01 17:16:00','2010-10-01 17:16:00','',0),(1288,'Пароль на почтовый ящик','EmailPassword','PASSWORD',NULL,'Y','Y',313,40,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1289,'Использовать SSL/TLS','UseSSL','CHAR','N','N','Y',313,50,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1290,'Использовать режим отладки','UseDebug','CHAR','N','N','Y',313,60,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1291,'Связанный проект','Project','REF_pm_ProjectId',NULL,'Y','Y',313,45,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1292,'Назначение','Caption','TEXT',NULL,'Y','Y',313,5,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1293,'Активен','IsActive','CHAR','Y','N','Y',313,70,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1294,'В проекте используется база знаний','IsKnowledgeUsed','CHAR','Y','N','Y',5,230,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1295,'Ведется блог проекта','IsBlogUsed','CHAR','Y','N','Y',5,240,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1296,'Назначение','Caption','TEXT',NULL,'Y','Y',314,10,'2010-10-01 17:16:01','2010-10-01 17:16:01','',0),(1297,'Класс','ClassName','TEXT',NULL,'Y','Y',314,20,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1298,'Минуты','Minutes','VARCHAR','*','Y','Y',314,30,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1299,'Часы','Hours','VARCHAR','*','Y','Y',314,40,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1300,'Дни месяца','Days','VARCHAR','*','Y','Y',314,50,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1301,'Дни недели','WeekDays','VARCHAR','*','Y','Y',314,60,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1302,'Задание активно','IsActive','CHAR','Y','N','Y',314,70,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1303,'Задание','ScheduledJob','REF_co_ScheduledJobId',NULL,'Y','Y',315,10,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1304,'Результат','Result','LARGETEXT',NULL,'Y','Y',315,20,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1305,'Выполнено','IsCompleted','CHAR','N','Y','Y',315,30,'2010-10-01 17:16:02','2010-10-01 17:16:02','',0),(1311,'Требования','Requirements','INTEGER','0','Y','N',316,50,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1312,'Тестовая документация','Testing','INTEGER','0','Y','N',316,60,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1313,'Справочная документация','HelpFiles','INTEGER','0','Y','N',316,70,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1314,'Файлы','Files','INTEGER','0','Y','N',316,80,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1315,'Категория','ReferenceName','TEXT',NULL,'N','Y',20,30,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1316,'Роль в проекте','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',20,40,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1317,'Базовый тип','ParentTaskType','REF_pm_TaskTypeId',NULL,'Y','Y',20,50,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1318,'Используется при планировании','UsedInPlanning','CHAR','Y','N','Y',20,60,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1319,'Класс','ObjectClass','TEXT',NULL,'Y','Y',317,10,'2010-10-01 17:16:25','2010-10-01 17:16:25','',0),(1320,'Объект','ObjectId','INTEGER',NULL,'Y','Y',317,20,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1321,'Роль в проекте','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',317,30,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1322,'Доступ','AccessType','TEXT',NULL,'Y','Y',317,40,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1323,'Исходный код','SourceCode','INTEGER','0','Y','N',316,90,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1325,'Описание','Description','LARGETEXT',NULL,'N','Y',318,20,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1326,'Входит в группу','ParentGroup','REF_co_ProjectGroupId',NULL,'N','Y',318,30,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1327,'Группа','ProjectGroup','REF_co_ProjectGroupId',NULL,'Y','Y',319,10,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1328,'Проект','Project','REF_pm_ProjectId',NULL,'Y','Y',319,20,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1329,'Название','Caption','TEXT',NULL,'Y','Y',320,10,'2010-10-01 17:16:26','2010-10-01 17:16:26','',0),(1330,'Описание','Description','LARGETEXT',NULL,'N','Y',320,20,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1331,'Входит в группу','ParentGroup','REF_co_UserGroupId',NULL,'N','Y',320,30,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1332,'Группа','UserGroup','REF_co_UserGroupId',NULL,'Y','Y',321,10,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1333,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',321,20,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1334,'Название','Caption','TEXT',NULL,'Y','Y',322,10,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1335,'Название таблицы','ReferenceName','TEXT',NULL,'Y','Y',322,20,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1336,'Пакет','packageId','REF_packageId',NULL,'Y','Y',322,30,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1337,'Экземпляры упорядочены','IsOrdered','CHAR',NULL,'N','Y',322,40,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1338,'Является справочником','IsDictionary','CHAR',NULL,'N','Y',322,50,'2010-10-01 17:16:27','2010-10-01 17:16:27','',0),(1340,'Право доступа','AccessType','TEXT',NULL,'Y','Y',323,40,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0),(1341,'Объект','ReferenceName','TEXT',NULL,'Y','Y',323,30,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0),(1342,'Тип объекта','ReferenceType','TEXT',NULL,'Y','Y',323,20,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,0),(1343,'Название','Caption','TEXT',NULL,'Y','Y',324,10,'2010-10-01 17:16:28','2010-10-01 17:16:28',NULL,0),(1344,'Веха','Milestone','REF_pm_MilestoneId',NULL,'N','N',95,50,'2010-10-01 17:16:28','2010-10-01 17:16:28',NULL,0),(1345,'Название','Caption','TEXT',NULL,'Y','Y',325,10,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1346,'Описание','Description','LARGETEXT',NULL,'N','Y',325,20,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1347,'Имя файла','FileName','TEXT',NULL,'Y','Y',325,30,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1348,'Используется по умолчанию','IsDefault','CHAR','N','N','Y',325,40,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1349,'Язык шаблона','Language','REF_cms_LanguageId',NULL,'Y','Y',325,35,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1350,'Название','Caption','TEXT',NULL,'Y','Y',326,10,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1351,'Описание','Description','LARGETEXT',NULL,'N','Y',326,20,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1352,'Стадия процесса','ProjectStage','REF_pm_ProjectStageId',NULL,'N','Y',14,80,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1353,'Тип задачи','TaskType','REF_pm_TaskTypeId',NULL,'Y','Y',327,10,'2010-10-01 17:16:29','2010-10-01 17:16:29',NULL,0),(1354,'Стадия процесса','ProjectStage','REF_pm_ProjectStageId',NULL,'Y','Y',327,20,'2010-10-01 17:16:30','2010-10-01 17:16:30',NULL,0),(1355,'Пожелания и функции','Requests','INTEGER','0','Y','Y',316,25,'2010-10-01 17:16:30','2010-10-01 17:16:30',NULL,0),(1356,'Название','Caption','TEXT',NULL,'Y','Y',328,10,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0),(1357,'Ссылочное имя','ReferenceName','TEXT',NULL,'Y','Y',328,20,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0),(1358,'Результат','Result','REF_pm_TestExecutionResultId',NULL,'Y','Y',75,50,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0),(1359,'Результат','Result','REF_pm_TestExecutionResultId',NULL,'Y','Y',74,60,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0),(1360,'Название','Caption','TEXT',NULL,'Y','Y',329,10,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0),(1361,'Описание','Description','TEXT',NULL,'Y','Y',329,20,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0),(1362,'URL','Url','TEXT',NULL,'Y','Y',329,30,'2010-11-01 21:19:03','2010-11-01 21:19:03',NULL,0),(1363,'Название','Caption','TEXT',NULL,'Y','Y',330,10,'2010-11-01 21:19:04','2010-11-01 21:19:04',NULL,0),(1364,'Категория','Category','REF_cms_ReportCategoryId',NULL,'Y','Y',329,40,'2010-11-01 21:19:04','2010-11-01 21:19:04',NULL,0),(1365,'Пожелание','ChangeRequest','REF_pm_ChangeRequestId',NULL,'Y','Y',331,10,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0),(1366,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',331,20,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0),(1367,'Класс','ObjectClass','TEXT',NULL,'Y','Y',331,30,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0),(1368,'Актуальна','IsActual','CHAR','Y','Y','Y',331,40,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,0),(1369,'Задача','Task','REF_pm_TaskId',NULL,'Y','Y',332,10,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0),(1370,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',332,20,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0),(1371,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',332,30,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0),(1372,'Актуальна','IsActual','CHAR','Y','Y','Y',332,40,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,0),(1379,'Редактор содержимого','ContentEditor','TEXT','','Y','N',41,140,'2011-01-04 07:52:42','2011-01-04 07:52:42',NULL,0),(1380,'Тэг','Tag','REF_TagId',NULL,'Y','Y',333,10,'2011-02-21 21:08:26','2011-02-21 21:08:26',NULL,0),(1381,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',333,20,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1382,'Класс объекта','ObjectClass','VARCHAR',NULL,'Y','Y',333,30,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1383,'Название','Caption','TEXT',NULL,'Y','Y',334,10,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1384,'Описание','Description','LARGETEXT',NULL,'N','Y',334,20,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1385,'Важность','Importance','REF_pm_ImportanceId',NULL,'Y','Y',64,25,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1386,'Название','Caption','TEXT',NULL,'Y','Y',335,10,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1387,'Описание','Description','LARGETEXT',NULL,'N','Y',335,20,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1388,'Сущность','ObjectClass','TEXT',NULL,'Y','N',335,30,'2011-02-21 21:08:27','2011-02-21 21:08:27',NULL,0),(1389,'Является терминальным','IsTerminal','CHAR','N','N','Y',335,40,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0),(1390,'Название','Caption','TEXT',NULL,'Y','Y',336,10,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0),(1391,'Описание','Description','LARGETEXT',NULL,'N','Y',336,20,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0),(1392,'Исходное состояние','SourceState','REF_pm_StateId',NULL,'Y','N',336,30,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0),(1393,'Целевое состояние','TargetState','REF_pm_StateId',NULL,'Y','Y',336,40,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0),(1394,'Необходимо указать причину перехода','IsReasonRequired','CHAR','N','N','Y',336,50,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0),(1395,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',337,10,'2011-02-21 21:08:28','2011-02-21 21:08:28',NULL,0),(1396,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',337,20,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0),(1397,'Состояние','State','REF_pm_StateId',NULL,'Y','Y',337,30,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0),(1398,'Кодовое имя','ReferenceName','TEXT',NULL,'Y','Y',335,25,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0),(1399,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','Y',338,10,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0),(1400,'Роль','ProjectRole','REF_pm_ProjectRoleId',NULL,'Y','Y',338,20,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0),(1401,'Состояние','State','TEXT',NULL,'N','N',203,30,'2011-02-21 21:08:29','2011-02-21 21:08:29',NULL,0),(1402,'Состояние','State','TEXT',NULL,'N','N',15,110,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0),(1403,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','N',339,10,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0),(1404,'Атрибут','ReferenceName','TEXT',NULL,'Y','Y',339,20,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0),(1405,'Сущность','Entity','LARGETEXT',NULL,'Y','N',339,30,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0),(1406,'Комментарий','Comment','LARGETEXT',NULL,'N','N',337,40,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0),(1408,'Состояние','State','TEXT',NULL,'N','N',22,35,'2011-02-21 21:08:30','2011-02-21 21:08:30',NULL,0),(1417,'Название','Caption','TEXT',NULL,'Y','Y',342,10,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0),(1418,'Описание','Description','LARGETEXT',NULL,'N','Y',342,20,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0),(1419,'Ссылочное имя','ReferenceName','TEXT',NULL,'Y','N',342,30,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0),(1420,'text(937)','DefaultPageTemplate','REF_WikiPageId',NULL,'N','Y',342,40,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0),(1421,'Тип страницы','PageType','REF_WikiPageTypeId',NULL,'N','N',9,160,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0),(1422,'Исходная страницы','SourcePage','REF_WikiPageId',NULL,'Y','Y',343,10,'2011-04-14 07:59:48','2011-04-14 07:59:48',NULL,0),(1423,'Целевая страница','TargetPage','REF_WikiPageId',NULL,'Y','Y',343,20,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0),(1424,'Связь актуальна','IsActual','CHAR','Y','Y','Y',343,30,'2011-04-14 07:59:49','2011-04-14 07:59:49',NULL,0),(1431,'Параметры','Url','TEXT',NULL,'Y','Y',345,30,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1432,'Описание','Description','LARGETEXT',NULL,'N','Y',345,40,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1433,'Часто используемый отчет','IsHandAccess','CHAR','N','N','Y',345,35,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1434,'Автор','Author','REF_pm_ParticipantId',NULL,'Y','N',345,50,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1435,'Название','Caption','TEXT',NULL,'Y','Y',346,10,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1436,'Параметры','Url','TEXT',NULL,'Y','Y',346,20,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1437,'Описание','Description','LARGETEXT',NULL,'N','Y',346,30,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1438,'Автор','Author','REF_cms_UserId',NULL,'Y','N',346,40,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1439,'Базовый отчет','ReportBase','TEXT',NULL,'N','Y',345,25,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1440,'Базовый отчет','ReportBase','TEXT',NULL,'N','Y',346,15,'2011-04-14 07:59:50','2011-04-14 07:59:50',NULL,0),(1441,'Настройка','Setting','TEXT',NULL,'Y','Y',347,10,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0),(1442,'Значение','Value','TEXT',NULL,'Y','Y',347,20,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0),(1443,'Участник','Participant','REF_pm_ParticipantId',NULL,'Y','Y',347,30,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0),(1444,'Краткое название','ShortCaption','TEXT',NULL,'N','Y',342,15,'2011-04-14 07:59:51','2011-04-14 07:59:51',NULL,0),(1445,'Сущность','ObjectClass','TEXT',NULL,'Y','Y',348,10,'2011-06-15 08:01:38','2011-06-15 08:01:38',NULL,0),(1446,'Атрибут','ObjectAttribute','TEXT',NULL,'Y','Y',348,20,'2011-06-15 08:01:38','2011-06-15 08:01:38',NULL,0),(1447,'Значение атрибута','AttributeValue','TEXT',NULL,'Y','Y',348,30,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0),(1448,'Идентификаторы объектов','ObjectIds','LARGETEXT',NULL,'Y','Y',348,40,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0),(1449,'Начальная скорость','InitialVelocity','INTEGER',NULL,'Y','Y',39,80,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0),(1450,'Начальная скорость','InitialVelocity','INTEGER',NULL,'Y','Y',14,55,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0),(1451,'text(1024)','DaysInWeek','INTEGER','5','Y','Y',5,260,'2011-06-15 08:01:39','2011-06-15 08:01:39',NULL,0),(1452,'Количество объектов','QueueLength','INTEGER',NULL,'N','N',335,50,'2011-08-13 18:29:27','2011-08-13 18:29:27',NULL,0),(1453,'text(kanban14)','IsKanbanUsed','CHAR','N','N','N',36,320,'2011-08-13 18:29:27','2011-08-13 18:29:27',NULL,0),(1454,'Описание','Caption','LARGETEXT',NULL,'Y','Y',349,10,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0),(1455,'Состояние','State','TEXT',NULL,'N','N',349,20,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0),(1456,'Описание','Caption','LARGETEXT',NULL,'Y','Y',350,10,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0),(1457,'Цель','Aim','REF_sm_AimId',NULL,'Y','N',350,20,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0),(1458,'Состояние','State','TEXT',NULL,'N','N',350,30,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0),(1459,'Описание','Caption','LARGETEXT',NULL,'Y','Y',351,10,'2011-08-13 18:29:28','2011-08-13 18:29:28',NULL,0),(1460,'Активность','Activity','REF_sm_ActivityId',NULL,'Y','N',351,20,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0),(1461,'Состояние','State','TEXT',NULL,'N','N',351,30,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0),(1462,'Имя','Caption','TEXT',NULL,'Y','Y',352,10,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0),(1463,'Состояние','State','TEXT',NULL,'N','N',352,20,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0),(1464,'Персона','Person','REF_sm_PersonId',NULL,'Y','N',349,30,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0),(1465,'Оценка','Estimation','FLOAT','0','Y','Y',351,40,'2011-08-13 18:29:29','2011-08-13 18:29:29',NULL,0),(1466,'Описание','Description','LARGETEXT',NULL,'N','Y',352,30,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0),(1467,'Ценности','Valuable','LARGETEXT',NULL,'N','Y',352,40,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0),(1468,'Проблемы','Problems','LARGETEXT',NULL,'N','Y',352,50,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0),(1469,'Фотография','Photo','IMAGE',NULL,'N','Y',352,60,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0),(1470,'Тип действия','Kind','INTEGER','0','Y','N',351,50,'2011-08-13 18:29:30','2011-08-13 18:29:30',NULL,0),(1471,'text(storymapping10)','IsStoryMappingUsed','CHAR','N','N','N',36,330,'2011-08-13 18:29:31','2011-08-13 18:29:31',NULL,0),(1472,'Оценка','Estimation','FLOAT','0','Y','Y',350,40,'2011-08-13 18:29:31','2011-08-13 18:29:31',NULL,0),(1473,'text(1033)','IsRequestOrderUsed','CHAR','N','N','Y',36,125,'2011-08-13 18:29:31','2011-08-13 18:29:31',NULL,0),(1474,'Идентификатор LDAP','LDAPUID','TEXT',NULL,'N','N',63,120,'2011-08-26 11:12:00','2011-08-26 11:12:00',NULL,0),(1475,'Идентификатор LDAP','LDAPUID','TEXT',NULL,'N','N',318,40,'2011-08-26 11:12:16','2011-08-26 11:12:16',NULL,0),(1476,'Идентификатор LDAP','LDAPUID','TEXT',NULL,'N','N',320,40,'2011-08-26 11:12:23','2011-08-26 11:12:23',NULL,0),(1478,'Редактор страниц','WikiEditor','TEXT',NULL,'N','Y',342,50,'2011-08-26 11:12:28','2011-08-26 11:12:28',NULL,0),(1479,'text(1059)','ConnectorClass','TEXT',NULL,'Y','Y',112,5,'2011-09-15 10:26:43','2011-09-15 10:26:43',NULL,0),(1480,'Продолжительность цикла, ч.','LifecycleDuration','INTEGER',NULL,'N','N',22,160,'2011-10-28 21:46:02','2011-10-28 21:46:02',NULL,0),(1481,'Количество объектов','TotalCount','INTEGER',NULL,'Y','Y',348,50,'2011-10-28 21:46:02','2011-10-28 21:46:02',NULL,0),(1482,'Дата начала','StartDate','DATE',NULL,'N','N',22,170,'2011-12-09 08:01:29','2011-12-09 08:01:29',NULL,0),(1483,'Дата окончания','FinishDate','DATE',NULL,'N','N',22,180,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1484,'Дата начала','StartDate','DATE',NULL,'N','N',15,120,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1485,'Дата окончания','FinishDate','DATE',NULL,'N','N',15,130,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1486,'Название','Caption','TEXT',NULL,'Y','Y',353,10,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1487,'Ссылочное имя','ReferenceName','TEXT',NULL,'Y','Y',353,20,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1488,'Сущность','EntityReferenceName','TEXT',NULL,'Y','Y',353,30,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1489,'Тип','AttributeType','TEXT',NULL,'Y','Y',353,40,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1490,'Значение по умолчанию','DefaultValue','TEXT',NULL,'N','Y',353,50,'2011-12-09 08:01:30','2011-12-09 08:01:30',NULL,0),(1491,'Видимо на форме','IsVisible','CHAR','Y','Y','Y',353,60,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1492,'Пользовательский атрибут','CustomAttribute','REF_pm_CustomAttributeId',NULL,'Y','N',354,10,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1493,'Идентификатор объекта','ObjectId','INTEGER',NULL,'Y','N',354,20,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1494,'Значение: число','IntegerValue','INTEGER',NULL,'N','N',354,30,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1495,'Значение: текст','StringValue','TEXT',NULL,'N','N',354,40,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1496,'Значение: текст','TextValue','LARGETEXT',NULL,'N','N',354,50,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1497,'Варианты значений','ValueRange','LARGETEXT',NULL,'N','N',353,45,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1498,'Обязательно для заполнения','IsRequired','CHAR','N','N','Y',353,70,'2011-12-09 08:01:31','2011-12-09 08:01:31',NULL,0),(1499,'text(1111)','TaskEstimationUsed','CHAR','Y','Y','Y',36,215,'2011-12-09 08:01:55','2011-12-09 08:01:55',NULL,0),(1500,'Дискриминатор','ObjectKind','TEXT',NULL,'N','N',353,80,'2011-12-09 08:01:55','2011-12-09 08:01:55',NULL,0),(1501,'Название','Caption','TEXT',NULL,'Y','Y',355,10,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0),(1502,'Описание','Description','LARGETEXT',NULL,'N','Y',355,20,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0),(1503,'Результат проверки','CheckResult','CHAR','N','Y','Y',355,30,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0),(1504,'Включена','IsEnabled','CHAR','Y','N','N',355,40,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0),(1505,'Значение','Value','TEXT',NULL,'N','N',355,50,'2012-03-20 07:59:16','2012-03-20 07:59:16',NULL,0),(1506,'Список','ListName','TEXT',NULL,'Y','Y',303,30,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1507,'Название','Caption','TEXT',NULL,'Y','Y',356,10,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1508,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','Y',357,10,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1509,'Предикат','Predicate','REF_pm_PredicateId',NULL,'Y','Y',357,20,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1510,'Переход','Transition','REF_pm_TransitionId',NULL,'Y','Y',358,10,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1511,'Атрибут','ReferenceName','TEXT',NULL,'Y','Y',358,20,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1512,'Сущность','Entity','TEXT',NULL,'N','N',358,30,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1513,'Репозиторий','Repository','REF_pm_SubversionId',NULL,'Y','N',113,60,'2012-03-20 07:59:17','2012-03-20 07:59:17',NULL,0),(1514,'Краткое название','Caption','TEXT',NULL,'N','Y',112,27,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0),(1515,'Описание','Description','LARGETEXT',NULL,'N','Y',353,55,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0),(1516,'Значение: пароль','PasswordValue','PASSWORD',NULL,'N','N',354,60,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0),(1517,'Итерация','Iteration','REF_pm_ReleaseId',NULL,'N','N',82,80,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0),(1518,'Название','Caption','TEXT',NULL,'Y','Y',359,10,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0),(1519,'Протокол','ProtocolName','TEXT',NULL,'Y','N',359,20,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0),(1520,'Способ подключения','MailboxProvider','REF_co_MailboxProviderId','1','Y','Y',313,18,'2012-03-20 07:59:18','2012-03-20 07:59:18',NULL,0),(1521,'Оставшаяся трудоемкость','EstimationLeft','FLOAT',NULL,'N','N',22,135,'2012-03-20 07:59:19','2012-03-20 07:59:19',NULL,0),(1522,'Название','Caption','TEXT',NULL,'Y','Y',360,10,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0),(1523,'Ссылочное имя','ReferenceName','TEXT',NULL,'Y','Y',360,20,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0),(1524,'Название','Caption','TEXT',NULL,'N','Y',361,10,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0),(1525,'Ссылочное имя','ReferenceName','TEXT',NULL,'Y','Y',361,20,'2012-10-05 07:51:38','2012-10-05 07:51:38',NULL,0),(1526,'Состояние','State','REF_pm_StateId',NULL,'Y','Y',361,30,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0),(1528,'Тип задачи','TaskType','REF_pm_TaskTypeId',NULL,'N','N',97,70,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0),(1529,'Релизы','Releases','INTEGER','0','Y','Y',316,27,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0),(1530,'Итерации и задачи','Tasks','INTEGER','0','Y','Y',316,28,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0),(1531,'Продолжительность','Duration','FLOAT','0','N','N',337,70,'2012-10-05 07:51:39','2012-10-05 07:51:39',NULL,0),(1532,'Напомнить','RememberInterval','INTEGER','0','N','Y',18,50,'2012-10-05 07:51:40','2012-10-05 07:51:40',NULL,0),(1533,'Напомнить','RememberInterval','INTEGER','0','N','N',19,70,'2012-10-05 07:51:40','2012-10-05 07:51:40',NULL,0),(1534,'Напомнить','RememberInterval','INTEGER','0','Y','N',80,60,'2012-10-05 07:51:40','2012-10-05 07:51:40',NULL,0);
/*!40000 ALTER TABLE `attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `businessfunction`
--

DROP TABLE IF EXISTS `businessfunction`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `businessfunction` (
  `businessfunctionId` int(11) NOT NULL auto_increment,
  `Caption` text,
  `ReferenceName` text,
  `packageId` int(11) default NULL,
  `OrderNum` int(11) default NULL,
  `Description` text,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  PRIMARY KEY  (`businessfunctionId`),
  UNIQUE KEY `XPKbusinessfunction` (`businessfunctionId`),
  KEY `businessfunction_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `businessfunction`
--

LOCK TABLES `businessfunction` WRITE;
/*!40000 ALTER TABLE `businessfunction` DISABLE KEYS */;
INSERT INTO `businessfunction` (`businessfunctionId`, `Caption`, `ReferenceName`, `packageId`, `OrderNum`, `Description`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (1,'Статистика использования проектов','ProjectUseStat',2,10,'','2006-01-09 17:11:58','2006-01-09 17:11:58','');
/*!40000 ALTER TABLE `businessfunction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Backup`
--

DROP TABLE IF EXISTS `cms_Backup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Backup` (
  `cms_BackupId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `BackupFileName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_BackupId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Backup`
--

LOCK TABLES `cms_Backup` WRITE;
/*!40000 ALTER TABLE `cms_Backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_Backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_BatchJob`
--

DROP TABLE IF EXISTS `cms_BatchJob`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_BatchJob` (
  `cms_BatchJobId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Parameters` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_BatchJobId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_BatchJob`
--

LOCK TABLES `cms_BatchJob` WRITE;
/*!40000 ALTER TABLE `cms_BatchJob` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_BatchJob` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_BlackList`
--

DROP TABLE IF EXISTS `cms_BlackList`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_BlackList` (
  `cms_BlackListId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SystemUser` int(11) default NULL,
  `BlockReason` text,
  `IPAddress` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_BlackListId`),
  KEY `i$7` (`SystemUser`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_BlackList`
--

LOCK TABLES `cms_BlackList` WRITE;
/*!40000 ALTER TABLE `cms_BlackList` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_BlackList` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_BrowserTransitionLog`
--

DROP TABLE IF EXISTS `cms_BrowserTransitionLog`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_BrowserTransitionLog` (
  `cms_BrowserTransitionLogId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `UserId` text,
  `URL` text,
  PRIMARY KEY  (`cms_BrowserTransitionLogId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_BrowserTransitionLog`
--

LOCK TABLES `cms_BrowserTransitionLog` WRITE;
/*!40000 ALTER TABLE `cms_BrowserTransitionLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_BrowserTransitionLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_CheckQuestion`
--

DROP TABLE IF EXISTS `cms_CheckQuestion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_CheckQuestion` (
  `cms_CheckQuestionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `QuestionRussian` text,
  `QuestionEnglish` text,
  `Answer` text,
  `AnswerEnglish` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_CheckQuestionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_CheckQuestion`
--

LOCK TABLES `cms_CheckQuestion` WRITE;
/*!40000 ALTER TABLE `cms_CheckQuestion` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_CheckQuestion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Checkpoint`
--

DROP TABLE IF EXISTS `cms_Checkpoint`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Checkpoint` (
  `cms_CheckpointId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `Description` text,
  `CheckResult` char(1) default NULL,
  `IsEnabled` char(1) default NULL,
  `Value` text,
  PRIMARY KEY  (`cms_CheckpointId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Checkpoint`
--

LOCK TABLES `cms_Checkpoint` WRITE;
/*!40000 ALTER TABLE `cms_Checkpoint` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_Checkpoint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_ClientInfo`
--

DROP TABLE IF EXISTS `cms_ClientInfo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_ClientInfo` (
  `cms_ClientInfoId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `Country` text,
  `City` text,
  PRIMARY KEY  (`cms_ClientInfoId`),
  KEY `Caption` (`Caption`(20))
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_ClientInfo`
--

LOCK TABLES `cms_ClientInfo` WRITE;
/*!40000 ALTER TABLE `cms_ClientInfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_ClientInfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_DeletedObject`
--

DROP TABLE IF EXISTS `cms_DeletedObject`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_DeletedObject` (
  `cms_DeletedObjectId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `EntityName` text,
  `ObjectId` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_DeletedObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_DeletedObject`
--

LOCK TABLES `cms_DeletedObject` WRITE;
/*!40000 ALTER TABLE `cms_DeletedObject` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_DeletedObject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_EmailNotification`
--

DROP TABLE IF EXISTS `cms_EmailNotification`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_EmailNotification` (
  `cms_EmailNotificationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `CodeName` text,
  `Content` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_EmailNotificationId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_EmailNotification`
--

LOCK TABLES `cms_EmailNotification` WRITE;
/*!40000 ALTER TABLE `cms_EmailNotification` DISABLE KEYS */;
INSERT INTO `cms_EmailNotification` (`cms_EmailNotificationId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `Content`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:36','2010-06-06 18:05:36','',30,'Объвление о вакансиях','VacancyNotification',NULL,0);
/*!40000 ALTER TABLE `cms_EmailNotification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_EntityCluster`
--

DROP TABLE IF EXISTS `cms_EntityCluster`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_EntityCluster` (
  `cms_EntityClusterId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `ObjectClass` varchar(32) default NULL,
  `ObjectAttribute` varchar(32) default NULL,
  `AttributeValue` varchar(128) default NULL,
  `ObjectIds` mediumtext,
  `TotalCount` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_EntityClusterId`),
  KEY `I$cms_EntityCluster$RecordModified` (`RecordModified`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_EntityCluster`
--

LOCK TABLES `cms_EntityCluster` WRITE;
/*!40000 ALTER TABLE `cms_EntityCluster` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_EntityCluster` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_IdsHash`
--

DROP TABLE IF EXISTS `cms_IdsHash`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_IdsHash` (
  `cms_IdsHashId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `HashKey` varchar(32) default NULL,
  `Ids` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_IdsHashId`),
  KEY `I$52` (`HashKey`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_IdsHash`
--

LOCK TABLES `cms_IdsHash` WRITE;
/*!40000 ALTER TABLE `cms_IdsHash` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_IdsHash` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Language`
--

DROP TABLE IF EXISTS `cms_Language`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Language` (
  `cms_LanguageId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `CodeName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_LanguageId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Language`
--

LOCK TABLES `cms_Language` WRITE;
/*!40000 ALTER TABLE `cms_Language` DISABLE KEYS */;
INSERT INTO `cms_Language` (`cms_LanguageId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `RecordVersion`) VALUES (1,'2006-03-21 23:30:31','2006-03-21 23:30:31','',10,'Русский','RU',0),(2,'2006-03-21 23:30:44','2006-03-21 23:30:44','',20,'Английский','EN',0);
/*!40000 ALTER TABLE `cms_Language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_License`
--

DROP TABLE IF EXISTS `cms_License`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_License` (
  `cms_LicenseId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `LicenseType` text,
  `LicenseValue` text,
  `LicenseKey` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_LicenseId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_License`
--

LOCK TABLES `cms_License` WRITE;
/*!40000 ALTER TABLE `cms_License` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_License` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Link`
--

DROP TABLE IF EXISTS `cms_Link`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Link` (
  `cms_LinkId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `Category` int(11) default NULL,
  `IsPublished` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_LinkId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Link`
--

LOCK TABLES `cms_Link` WRITE;
/*!40000 ALTER TABLE `cms_Link` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_Link` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_LinkCategory`
--

DROP TABLE IF EXISTS `cms_LinkCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_LinkCategory` (
  `cms_LinkCategoryId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_LinkCategoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_LinkCategory`
--

LOCK TABLES `cms_LinkCategory` WRITE;
/*!40000 ALTER TABLE `cms_LinkCategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_LinkCategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_LoginRetry`
--

DROP TABLE IF EXISTS `cms_LoginRetry`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_LoginRetry` (
  `cms_LoginRetryId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SystemUser` int(11) default NULL,
  `RetryAmount` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_LoginRetryId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_LoginRetry`
--

LOCK TABLES `cms_LoginRetry` WRITE;
/*!40000 ALTER TABLE `cms_LoginRetry` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_LoginRetry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_MainMenu`
--

DROP TABLE IF EXISTS `cms_MainMenu`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_MainMenu` (
  `cms_MainMenuId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_MainMenuId`),
  UNIQUE KEY `XPKcms_MainMenu` (`cms_MainMenuId`),
  KEY `cms_MainMenu_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30))
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_MainMenu`
--

LOCK TABLES `cms_MainMenu` WRITE;
/*!40000 ALTER TABLE `cms_MainMenu` DISABLE KEYS */;
INSERT INTO `cms_MainMenu` (`cms_MainMenuId`, `OrderNum`, `Caption`, `ReferenceName`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1,10,'Вертикальное меню','Vertical',NULL,'2005-12-22 21:20:20',NULL,0);
/*!40000 ALTER TABLE `cms_MainMenu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_NotificationSubscription`
--

DROP TABLE IF EXISTS `cms_NotificationSubscription`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_NotificationSubscription` (
  `cms_NotificationSubscriptionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Notification` int(11) default NULL,
  `IsActive` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_NotificationSubscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_NotificationSubscription`
--

LOCK TABLES `cms_NotificationSubscription` WRITE;
/*!40000 ALTER TABLE `cms_NotificationSubscription` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_NotificationSubscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Page`
--

DROP TABLE IF EXISTS `cms_Page`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Page` (
  `cms_PageId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `PHPFile` text,
  `Menu` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `HelpId` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_PageId`),
  UNIQUE KEY `XPKcms_Page` (`cms_PageId`),
  KEY `cms_Page_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30),`Menu`),
  KEY `Menu` (`Menu`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Page`
--

LOCK TABLES `cms_Page` WRITE;
/*!40000 ALTER TABLE `cms_Page` DISABLE KEYS */;
INSERT INTO `cms_Page` (`cms_PageId`, `OrderNum`, `Caption`, `ReferenceName`, `PHPFile`, `Menu`, `RecordCreated`, `RecordModified`, `VPD`, `HelpId`, `RecordVersion`) VALUES (1,10,'Релизы.','Project','project.php',1,NULL,'2010-06-06 18:05:48',NULL,42,0),(2,70,'Участники.','Participants','participants.php',1,NULL,'2006-01-12 23:49:18',NULL,44,0),(3,47,'Требования.','Requirements','requirements.php',1,NULL,'2006-03-20 23:02:27',NULL,52,0),(4,20,'Пожелания.','Requests','requests.php',1,NULL,'2006-01-12 23:49:06',NULL,43,0),(5,45,'Мои задачи.','Tasks','tasks.php',1,NULL,'2010-06-06 18:06:13',NULL,50,0),(6,40,'Итерации.','Planning','planning.php',1,NULL,'2010-06-06 18:05:48',NULL,53,0),(7,60,'Файлы.','Artefacts','artefacts.php',1,NULL,'2010-06-06 18:06:10',NULL,45,0),(8,5,'Проект.','Main','index.php',1,NULL,'2010-06-06 18:05:48',NULL,41,0),(9,55,'Документация.','Help','helpfiles.php',1,'2006-01-09 22:41:18','2006-01-13 10:02:31','',51,0),(10,50,'Тестирование.','Testing','testing.php',1,'2010-06-06 18:05:14','2010-06-06 18:05:14','',NULL,0),(12,15,'Функции.','Feature','functions.php',1,NULL,NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `cms_Page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_PluginModule`
--

DROP TABLE IF EXISTS `cms_PluginModule`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_PluginModule` (
  `cms_PluginModuleId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_PluginModuleId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_PluginModule`
--

LOCK TABLES `cms_PluginModule` WRITE;
/*!40000 ALTER TABLE `cms_PluginModule` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_PluginModule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_RemapObject`
--

DROP TABLE IF EXISTS `cms_RemapObject`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_RemapObject` (
  `cms_RemapObjectId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `EntityName` text,
  `ObjectId` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_RemapObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_RemapObject`
--

LOCK TABLES `cms_RemapObject` WRITE;
/*!40000 ALTER TABLE `cms_RemapObject` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_RemapObject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Report`
--

DROP TABLE IF EXISTS `cms_Report`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Report` (
  `cms_ReportId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `Url` text,
  `Category` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_ReportId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Report`
--

LOCK TABLES `cms_Report` WRITE;
/*!40000 ALTER TABLE `cms_Report` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_Report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_ReportCategory`
--

DROP TABLE IF EXISTS `cms_ReportCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_ReportCategory` (
  `cms_ReportCategoryId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_ReportCategoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_ReportCategory`
--

LOCK TABLES `cms_ReportCategory` WRITE;
/*!40000 ALTER TABLE `cms_ReportCategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_ReportCategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Resource`
--

DROP TABLE IF EXISTS `cms_Resource`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Resource` (
  `cms_ResourceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ResourceKey` text,
  `ResourceValue` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_ResourceId`),
  KEY `I$cms_Resource$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Resource`
--

LOCK TABLES `cms_Resource` WRITE;
/*!40000 ALTER TABLE `cms_Resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_Resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_SerializedObject`
--

DROP TABLE IF EXISTS `cms_SerializedObject`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_SerializedObject` (
  `cms_SerializedObjectId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SourceName` text,
  `ClassName` text,
  `EntityName` text,
  `OldObjectId` int(11) default NULL,
  `NewObjectId` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_SerializedObjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_SerializedObject`
--

LOCK TABLES `cms_SerializedObject` WRITE;
/*!40000 ALTER TABLE `cms_SerializedObject` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_SerializedObject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Snapshot`
--

DROP TABLE IF EXISTS `cms_Snapshot`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Snapshot` (
  `cms_SnapshotId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `SystemUser` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  `ListName` text,
  PRIMARY KEY  (`cms_SnapshotId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Snapshot`
--

LOCK TABLES `cms_Snapshot` WRITE;
/*!40000 ALTER TABLE `cms_Snapshot` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_Snapshot` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_SnapshotItem`
--

DROP TABLE IF EXISTS `cms_SnapshotItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_SnapshotItem` (
  `cms_SnapshotItemId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Snapshot` int(11) default NULL,
  `ObjectId` int(11) default NULL,
  `ObjectClass` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_SnapshotItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_SnapshotItem`
--

LOCK TABLES `cms_SnapshotItem` WRITE;
/*!40000 ALTER TABLE `cms_SnapshotItem` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_SnapshotItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_SnapshotItemValue`
--

DROP TABLE IF EXISTS `cms_SnapshotItemValue`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_SnapshotItemValue` (
  `cms_SnapshotItemValueId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `SnapshotItem` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `Value` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_SnapshotItemValueId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_SnapshotItemValue`
--

LOCK TABLES `cms_SnapshotItemValue` WRITE;
/*!40000 ALTER TABLE `cms_SnapshotItemValue` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_SnapshotItemValue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_SynchronizationSource`
--

DROP TABLE IF EXISTS `cms_SynchronizationSource`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_SynchronizationSource` (
  `cms_SynchronizationSourceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ReferenceUrl` text,
  `ServerName` text,
  `Author` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_SynchronizationSourceId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_SynchronizationSource`
--

LOCK TABLES `cms_SynchronizationSource` WRITE;
/*!40000 ALTER TABLE `cms_SynchronizationSource` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_SynchronizationSource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_SystemSettings`
--

DROP TABLE IF EXISTS `cms_SystemSettings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_SystemSettings` (
  `cms_SystemSettingsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Language` int(11) default NULL,
  `AdminEmail` text,
  `AllowToChangeLogin` char(1) default NULL,
  `DisplayFeedbackForm` char(1) default NULL,
  `AdminProject` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_SystemSettingsId`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_SystemSettings`
--

LOCK TABLES `cms_SystemSettings` WRITE;
/*!40000 ALTER TABLE `cms_SystemSettings` DISABLE KEYS */;
INSERT INTO `cms_SystemSettings` (`cms_SystemSettingsId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Language`, `AdminEmail`, `AllowToChangeLogin`, `DisplayFeedbackForm`, `AdminProject`, `RecordVersion`) VALUES (1,NULL,NULL,NULL,NULL,'DEVPROM',1,NULL,'Y','Y',NULL,0);
/*!40000 ALTER TABLE `cms_SystemSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_TempFile`
--

DROP TABLE IF EXISTS `cms_TempFile`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_TempFile` (
  `cms_TempFileId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `FileName` text,
  `MimeType` text,
  `FileExt` varchar(32) default NULL,
  `FilePath` text,
  `FileMime` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_TempFileId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_TempFile`
--

LOCK TABLES `cms_TempFile` WRITE;
/*!40000 ALTER TABLE `cms_TempFile` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_TempFile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_Update`
--

DROP TABLE IF EXISTS `cms_Update`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_Update` (
  `cms_UpdateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `FileName` text,
  `LogFileName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_UpdateId`),
  KEY `i$8` (`RecordCreated`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_Update`
--

LOCK TABLES `cms_Update` WRITE;
/*!40000 ALTER TABLE `cms_Update` DISABLE KEYS */;
INSERT INTO `cms_Update` (`cms_UpdateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `LogFileName`, `RecordVersion`) VALUES (14,NULL,NULL,NULL,NULL,'2.9.9',NULL,NULL,NULL,0);
/*!40000 ALTER TABLE `cms_Update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_User`
--

DROP TABLE IF EXISTS `cms_User`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_User` (
  `cms_UserId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Email` text,
  `Login` text,
  `ICQ` text,
  `Phone` text,
  `Password` text,
  `SessionHash` text,
  `IsShared` char(1) default NULL,
  `IsAdmin` char(1) default NULL,
  `Skype` text,
  `Language` int(11) default NULL,
  `PhotoMime` text,
  `PhotoPath` text,
  `PhotoExt` varchar(32) default NULL,
  `IsActivated` char(1) default NULL,
  `Rating` float default NULL,
  `Description` text,
  `LDAPUID` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_UserId`),
  KEY `Login` (`Login`(20)),
  KEY `i$33` (`IsAdmin`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_User`
--

LOCK TABLES `cms_User` WRITE;
/*!40000 ALTER TABLE `cms_User` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_UserLock`
--

DROP TABLE IF EXISTS `cms_UserLock`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_UserLock` (
  `cms_UserLockId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `EntityName` text,
  `ObjectId` int(11) default NULL,
  `IsActive` char(1) default NULL,
  `SystemUser` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_UserLockId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_UserLock`
--

LOCK TABLES `cms_UserLock` WRITE;
/*!40000 ALTER TABLE `cms_UserLock` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_UserLock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cms_UserSettings`
--

DROP TABLE IF EXISTS `cms_UserSettings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `cms_UserSettings` (
  `cms_UserSettingsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `User` int(11) default NULL,
  `Settings` varchar(32) default NULL,
  `Value` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`cms_UserSettingsId`),
  KEY `i$24` (`User`,`Settings`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `cms_UserSettings`
--

LOCK TABLES `cms_UserSettings` WRITE;
/*!40000 ALTER TABLE `cms_UserSettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `cms_UserSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_AccessRight`
--

DROP TABLE IF EXISTS `co_AccessRight`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_AccessRight` (
  `co_AccessRightId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `UserGroup` int(11) default NULL,
  `AccessType` varchar(32) default NULL,
  `ReferenceName` varchar(255) default NULL,
  `ReferenceType` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_AccessRightId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_AccessRight`
--

LOCK TABLES `co_AccessRight` WRITE;
/*!40000 ALTER TABLE `co_AccessRight` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_AccessRight` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Advise`
--

DROP TABLE IF EXISTS `co_Advise`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Advise` (
  `co_AdviseId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `Advise` text,
  `Theme` int(11) default NULL,
  `Author` int(11) default NULL,
  `IsApproved` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_AdviseId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Advise`
--

LOCK TABLES `co_Advise` WRITE;
/*!40000 ALTER TABLE `co_Advise` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Advise` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_AdviseTheme`
--

DROP TABLE IF EXISTS `co_AdviseTheme`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_AdviseTheme` (
  `co_AdviseThemeId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_AdviseThemeId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_AdviseTheme`
--

LOCK TABLES `co_AdviseTheme` WRITE;
/*!40000 ALTER TABLE `co_AdviseTheme` DISABLE KEYS */;
INSERT INTO `co_AdviseTheme` (`co_AdviseThemeId`, `RecordCreated`, `RecordModified`, `VPD`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Ведение проекта',0),(2,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с пожеланиями',0),(3,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Управление проектом',0),(4,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Планирование',0),(5,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Выполнение задач',0),(6,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с требованиями',0),(7,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Тестирование',0),(8,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с документацией',0),(9,'2010-06-06 18:05:42','2010-06-06 18:05:42','','Работа с артефактами',0);
/*!40000 ALTER TABLE `co_AdviseTheme` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Bill`
--

DROP TABLE IF EXISTS `co_Bill`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Bill` (
  `co_BillId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SystemUser` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_BillId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Bill`
--

LOCK TABLES `co_Bill` WRITE;
/*!40000 ALTER TABLE `co_Bill` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Bill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_BillOperation`
--

DROP TABLE IF EXISTS `co_BillOperation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_BillOperation` (
  `co_BillOperationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Volume` float default NULL,
  `Comment` text,
  `Bill` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_BillOperationId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_BillOperation`
--

LOCK TABLES `co_BillOperation` WRITE;
/*!40000 ALTER TABLE `co_BillOperation` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_BillOperation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_CommunityRole`
--

DROP TABLE IF EXISTS `co_CommunityRole`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_CommunityRole` (
  `co_CommunityRoleId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_CommunityRoleId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_CommunityRole`
--

LOCK TABLES `co_CommunityRole` WRITE;
/*!40000 ALTER TABLE `co_CommunityRole` DISABLE KEYS */;
INSERT INTO `co_CommunityRole` (`co_CommunityRoleId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:53','2010-06-06 18:05:53','',10,'Пользователь',0),(2,'2010-06-06 18:05:53','2010-06-06 18:05:53','',20,'Участник проектов',0);
/*!40000 ALTER TABLE `co_CommunityRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_CustomReport`
--

DROP TABLE IF EXISTS `co_CustomReport`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_CustomReport` (
  `co_CustomReportId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Url` text,
  `Description` text,
  `Author` int(11) default NULL,
  `ReportBase` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_CustomReportId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_CustomReport`
--

LOCK TABLES `co_CustomReport` WRITE;
/*!40000 ALTER TABLE `co_CustomReport` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_CustomReport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_IssueOutsourcing`
--

DROP TABLE IF EXISTS `co_IssueOutsourcing`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_IssueOutsourcing` (
  `co_IssueOutsourcingId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ChangeRequest` int(11) default NULL,
  `Project` int(11) default NULL,
  `Cost` text,
  `Duration` int(11) default NULL,
  `Comment` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_IssueOutsourcingId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_IssueOutsourcing`
--

LOCK TABLES `co_IssueOutsourcing` WRITE;
/*!40000 ALTER TABLE `co_IssueOutsourcing` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_IssueOutsourcing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_JobRun`
--

DROP TABLE IF EXISTS `co_JobRun`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_JobRun` (
  `co_JobRunId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ScheduledJob` int(11) default NULL,
  `Result` text,
  `IsCompleted` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_JobRunId`),
  KEY `I$co_JobRun$Job` (`ScheduledJob`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_JobRun`
--

LOCK TABLES `co_JobRun` WRITE;
/*!40000 ALTER TABLE `co_JobRun` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_JobRun` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_MailTransport`
--

DROP TABLE IF EXISTS `co_MailTransport`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_MailTransport` (
  `co_MailTransportId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `ReferenceName` text,
  PRIMARY KEY  (`co_MailTransportId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_MailTransport`
--

LOCK TABLES `co_MailTransport` WRITE;
/*!40000 ALTER TABLE `co_MailTransport` DISABLE KEYS */;
INSERT INTO `co_MailTransport` (`co_MailTransportId`, `VPD`, `RecordVersion`, `OrderNum`, `RecordCreated`, `RecordModified`, `Caption`, `ReferenceName`) VALUES (1,NULL,0,10,NULL,NULL,'SMTP','SMTP'),(2,NULL,0,20,NULL,NULL,'IMAP','IMAP');
/*!40000 ALTER TABLE `co_MailTransport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_MailboxProvider`
--

DROP TABLE IF EXISTS `co_MailboxProvider`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_MailboxProvider` (
  `co_MailboxProviderId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `ProtocolName` text,
  PRIMARY KEY  (`co_MailboxProviderId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_MailboxProvider`
--

LOCK TABLES `co_MailboxProvider` WRITE;
/*!40000 ALTER TABLE `co_MailboxProvider` DISABLE KEYS */;
INSERT INTO `co_MailboxProvider` (`co_MailboxProviderId`, `VPD`, `RecordVersion`, `OrderNum`, `RecordCreated`, `RecordModified`, `Caption`, `ProtocolName`) VALUES (1,NULL,0,NULL,NULL,NULL,'POP3','POP3'),(2,NULL,0,NULL,NULL,NULL,'IMAP','IMAP');
/*!40000 ALTER TABLE `co_MailboxProvider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Message`
--

DROP TABLE IF EXISTS `co_Message`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Message` (
  `co_MessageId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Subject` text,
  `Content` text,
  `Author` int(11) default NULL,
  `ToUser` int(11) default NULL,
  `ToTeam` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_MessageId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Message`
--

LOCK TABLES `co_Message` WRITE;
/*!40000 ALTER TABLE `co_Message` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Option`
--

DROP TABLE IF EXISTS `co_Option`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Option` (
  `co_OptionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `Cost` float default NULL,
  `Period` int(11) default NULL,
  `Conditions` text,
  `CodeName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_OptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Option`
--

LOCK TABLES `co_Option` WRITE;
/*!40000 ALTER TABLE `co_Option` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_OptionUser`
--

DROP TABLE IF EXISTS `co_OptionUser`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_OptionUser` (
  `co_OptionUserId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Option` int(11) default NULL,
  `SystemUser` int(11) default NULL,
  `IsActive` char(1) default NULL,
  `IsPayed` char(1) default NULL,
  `PaymentDate` datetime default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_OptionUserId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_OptionUser`
--

LOCK TABLES `co_OptionUser` WRITE;
/*!40000 ALTER TABLE `co_OptionUser` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_OptionUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_OutsourcingSuggestion`
--

DROP TABLE IF EXISTS `co_OutsourcingSuggestion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_OutsourcingSuggestion` (
  `co_OutsourcingSuggestionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `IssueOutsourcing` int(11) default NULL,
  `Cost` text,
  `SystemUser` int(11) default NULL,
  `Comment` text,
  `IsAccepted` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_OutsourcingSuggestionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_OutsourcingSuggestion`
--

LOCK TABLES `co_OutsourcingSuggestion` WRITE;
/*!40000 ALTER TABLE `co_OutsourcingSuggestion` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_OutsourcingSuggestion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_ProjectGroup`
--

DROP TABLE IF EXISTS `co_ProjectGroup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_ProjectGroup` (
  `co_ProjectGroupId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `ParentGroup` int(11) default NULL,
  `LDAPUID` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ProjectGroupId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_ProjectGroup`
--

LOCK TABLES `co_ProjectGroup` WRITE;
/*!40000 ALTER TABLE `co_ProjectGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_ProjectGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_ProjectGroupLink`
--

DROP TABLE IF EXISTS `co_ProjectGroupLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_ProjectGroupLink` (
  `co_ProjectGroupLinkId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `ProjectGroup` int(11) default NULL,
  `Project` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ProjectGroupLinkId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_ProjectGroupLink`
--

LOCK TABLES `co_ProjectGroupLink` WRITE;
/*!40000 ALTER TABLE `co_ProjectGroupLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_ProjectGroupLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_ProjectParticipant`
--

DROP TABLE IF EXISTS `co_ProjectParticipant`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_ProjectParticipant` (
  `co_ProjectParticipantId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SystemUser` int(11) default NULL,
  `Price` int(11) default NULL,
  `PriceCode` varchar(32) default NULL,
  `Skills` text,
  `Tools` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ProjectParticipantId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_ProjectParticipant`
--

LOCK TABLES `co_ProjectParticipant` WRITE;
/*!40000 ALTER TABLE `co_ProjectParticipant` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_ProjectParticipant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_ProjectSubscription`
--

DROP TABLE IF EXISTS `co_ProjectSubscription`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_ProjectSubscription` (
  `co_ProjectSubscriptionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SystemUser` int(11) default NULL,
  `Project` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ProjectSubscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_ProjectSubscription`
--

LOCK TABLES `co_ProjectSubscription` WRITE;
/*!40000 ALTER TABLE `co_ProjectSubscription` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_ProjectSubscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Rating`
--

DROP TABLE IF EXISTS `co_Rating`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Rating` (
  `co_RatingId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `ObjectId` int(11) default NULL,
  `ObjectClass` varchar(32) default NULL,
  `Rating` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_RatingId`),
  KEY `i$28` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Rating`
--

LOCK TABLES `co_Rating` WRITE;
/*!40000 ALTER TABLE `co_Rating` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Rating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_RatingVoice`
--

DROP TABLE IF EXISTS `co_RatingVoice`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_RatingVoice` (
  `co_RatingVoiceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SystemUser` int(11) default NULL,
  `IPAddress` text,
  `Rating` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_RatingVoiceId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_RatingVoice`
--

LOCK TABLES `co_RatingVoice` WRITE;
/*!40000 ALTER TABLE `co_RatingVoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_RatingVoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_RemoteMailbox`
--

DROP TABLE IF EXISTS `co_RemoteMailbox`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_RemoteMailbox` (
  `co_RemoteMailboxId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `HostAddress` text,
  `PortServer` int(11) default NULL,
  `EmailAddress` text,
  `EmailPassword` text,
  `UseSSL` char(1) default NULL,
  `UseDebug` char(1) default NULL,
  `Project` int(11) default NULL,
  `Caption` text,
  `IsActive` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  `MailboxProvider` int(11) default NULL,
  PRIMARY KEY  (`co_RemoteMailboxId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_RemoteMailbox`
--

LOCK TABLES `co_RemoteMailbox` WRITE;
/*!40000 ALTER TABLE `co_RemoteMailbox` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_RemoteMailbox` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_ScheduledJob`
--

DROP TABLE IF EXISTS `co_ScheduledJob`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_ScheduledJob` (
  `co_ScheduledJobId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ClassName` text,
  `Minutes` varchar(32) default NULL,
  `Hours` varchar(32) default NULL,
  `Days` varchar(32) default NULL,
  `WeekDays` varchar(32) default NULL,
  `IsActive` char(1) default NULL,
  `Parameters` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ScheduledJobId`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_ScheduledJob`
--

LOCK TABLES `co_ScheduledJob` WRITE;
/*!40000 ALTER TABLE `co_ScheduledJob` DISABLE KEYS */;
INSERT INTO `co_ScheduledJob` (`co_ScheduledJobId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ClassName`, `Minutes`, `Hours`, `Days`, `WeekDays`, `IsActive`, `Parameters`, `RecordVersion`) VALUES (1,NULL,NULL,NULL,10,'text(955)','processrevisionlog','*/10','*','*','*','Y',NULL,0),(2,NULL,NULL,NULL,20,'text(956)','processstatistics','*','*','*','*','Y',NULL,0),(3,NULL,NULL,NULL,300,'text(957)','processbackup','0','23','*','*','Y','{\"limit\":\"20\"}',0),(4,NULL,NULL,NULL,200,'text(958)','processemailqueue','*','*','*','*','Y','{\"limit\":\"20\"}',0),(5,NULL,NULL,NULL,40,'text(959)','support/scanmailboxes','*','*','*','*','Y',NULL,0),(6,NULL,NULL,NULL,50,'text(967)','processdigest','*/10','*','*','*','Y','{\"limit\":\"10\",\"type\":\"every10minutes\"}',0),(7,NULL,NULL,NULL,60,'text(968)','processdigest','0','*','*','*','Y','{\"limit\":\"10\",\"type\":\"every1hour\"}',0),(8,NULL,NULL,NULL,70,'text(960)','processdigest','0','23','*','*','Y','{\"limit\":\"10\",\"type\":\"daily\"}',0),(9,NULL,NULL,NULL,80,'text(962)','processdigest','0','23','*/2','*','Y','{\"limit\":\"10\",\"type\":\"every2days\"}',0),(10,NULL,NULL,NULL,90,'text(963)','processdigest','0','8','*','1','Y','{\"limit\":\"10\",\"type\":\"weekly\"}',0),(11,NULL,NULL,NULL,100,'text(993)','trackhistory','*/5','*','*','*','Y','',0),(12,NULL,NULL,NULL,NULL,'text(1130)','processcheckpoints','*/10','*','*','*','Y',NULL,0),(13,NULL,NULL,NULL,NULL,'text(1194)','meetingremember','0','7','*','*','Y',NULL,0),(14,NULL,NULL,NULL,NULL,'text(1227)','processdigest','*','*','*','*','Y',NULL,0);
/*!40000 ALTER TABLE `co_ScheduledJob` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_SearchResult`
--

DROP TABLE IF EXISTS `co_SearchResult`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_SearchResult` (
  `co_SearchResultId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SearchKind` text,
  `SystemUser` int(11) default NULL,
  `Result` text,
  `Conditions` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_SearchResultId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_SearchResult`
--

LOCK TABLES `co_SearchResult` WRITE;
/*!40000 ALTER TABLE `co_SearchResult` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_SearchResult` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Service`
--

DROP TABLE IF EXISTS `co_Service`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Service` (
  `co_ServiceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Category` int(11) default NULL,
  `Description` text,
  `Cost` text,
  `Author` int(11) default NULL,
  `Team` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ServiceId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Service`
--

LOCK TABLES `co_Service` WRITE;
/*!40000 ALTER TABLE `co_Service` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_ServiceCategory`
--

DROP TABLE IF EXISTS `co_ServiceCategory`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_ServiceCategory` (
  `co_ServiceCategoryId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ServiceCategoryId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_ServiceCategory`
--

LOCK TABLES `co_ServiceCategory` WRITE;
/*!40000 ALTER TABLE `co_ServiceCategory` DISABLE KEYS */;
INSERT INTO `co_ServiceCategory` (`co_ServiceCategoryId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:37','2010-06-06 18:05:37','',10,'Разработка ПО',0),(2,'2010-06-06 18:05:37','2010-06-06 18:05:37','',20,'Администрирование',0),(3,'2010-06-06 18:05:37','2010-06-06 18:05:37','',30,'Тестирование',0),(4,'2010-06-06 18:05:37','2010-06-06 18:05:37','',40,'Маркетинг',0),(5,'2010-06-06 18:05:37','2010-06-06 18:05:37','',50,'Продажи',0),(6,'2010-06-06 18:05:37','2010-06-06 18:05:37','',60,'Консультирование',0),(7,'2010-06-06 18:05:37','2010-06-06 18:05:37','',70,'Дизайн',0),(8,'2010-06-06 18:05:37','2010-06-06 18:05:37','',80,'Обучение',0),(9,'2010-06-06 18:05:37','2010-06-06 18:05:37','',90,'Управление проектами',0);
/*!40000 ALTER TABLE `co_ServiceCategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_ServiceRequest`
--

DROP TABLE IF EXISTS `co_ServiceRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_ServiceRequest` (
  `co_ServiceRequestId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Service` int(11) default NULL,
  `Customer` int(11) default NULL,
  `Response` text,
  `IsClosed` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_ServiceRequestId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_ServiceRequest`
--

LOCK TABLES `co_ServiceRequest` WRITE;
/*!40000 ALTER TABLE `co_ServiceRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_ServiceRequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Team`
--

DROP TABLE IF EXISTS `co_Team`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Team` (
  `co_TeamId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `Tagline` text,
  `Description` text,
  `Author` int(11) default NULL,
  `Rating` float default NULL,
  `TeamState` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TeamId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Team`
--

LOCK TABLES `co_Team` WRITE;
/*!40000 ALTER TABLE `co_Team` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Team` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_TeamState`
--

DROP TABLE IF EXISTS `co_TeamState`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_TeamState` (
  `co_TeamStateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TeamStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_TeamState`
--

LOCK TABLES `co_TeamState` WRITE;
/*!40000 ALTER TABLE `co_TeamState` DISABLE KEYS */;
INSERT INTO `co_TeamState` (`co_TeamStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:52','2010-06-06 18:05:52','',10,'Свободна','Команда готова выполнять проекты',0),(2,'2010-06-06 18:05:52','2010-06-06 18:05:52','',20,'Занята','Команда занята выполнением своих проектов',0);
/*!40000 ALTER TABLE `co_TeamState` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_TeamUser`
--

DROP TABLE IF EXISTS `co_TeamUser`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_TeamUser` (
  `co_TeamUserId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Team` int(11) default NULL,
  `SystemUser` int(11) default NULL,
  `TeamRoles` text,
  `IsActive` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TeamUserId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_TeamUser`
--

LOCK TABLES `co_TeamUser` WRITE;
/*!40000 ALTER TABLE `co_TeamUser` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_TeamUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_Tender`
--

DROP TABLE IF EXISTS `co_Tender`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_Tender` (
  `co_TenderId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `Description` text,
  `Kind` int(11) default NULL,
  `State` int(11) default NULL,
  `SystemUser` int(11) default NULL,
  `Project` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TenderId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_Tender`
--

LOCK TABLES `co_Tender` WRITE;
/*!40000 ALTER TABLE `co_Tender` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_Tender` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_TenderAttachment`
--

DROP TABLE IF EXISTS `co_TenderAttachment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_TenderAttachment` (
  `co_TenderAttachmentId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Tender` int(11) default NULL,
  `AttachmentMime` text,
  `AttachmentPath` text,
  `AttachmentExt` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TenderAttachmentId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_TenderAttachment`
--

LOCK TABLES `co_TenderAttachment` WRITE;
/*!40000 ALTER TABLE `co_TenderAttachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_TenderAttachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_TenderKind`
--

DROP TABLE IF EXISTS `co_TenderKind`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_TenderKind` (
  `co_TenderKindId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TenderKindId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_TenderKind`
--

LOCK TABLES `co_TenderKind` WRITE;
/*!40000 ALTER TABLE `co_TenderKind` DISABLE KEYS */;
INSERT INTO `co_TenderKind` (`co_TenderKindId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:55','2010-06-06 18:05:55','',10,'Открытый',0),(2,'2010-06-06 18:05:55','2010-06-06 18:05:55','',20,'Закрытый',0);
/*!40000 ALTER TABLE `co_TenderKind` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_TenderParticipanceState`
--

DROP TABLE IF EXISTS `co_TenderParticipanceState`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_TenderParticipanceState` (
  `co_TenderParticipanceStateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TenderParticipanceStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_TenderParticipanceState`
--

LOCK TABLES `co_TenderParticipanceState` WRITE;
/*!40000 ALTER TABLE `co_TenderParticipanceState` DISABLE KEYS */;
INSERT INTO `co_TenderParticipanceState` (`co_TenderParticipanceStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:56','2010-06-06 18:05:56','',10,'Рассматривается',0),(2,'2010-06-06 18:05:56','2010-06-06 18:05:56','',20,'Подтверждено',0),(3,'2010-06-06 18:05:56','2010-06-06 18:05:56','',30,'Отклонено',0),(4,'2010-06-06 18:05:57','2010-06-06 18:05:57','',40,'Готовит предложение',0),(5,'2010-06-06 18:05:57','2010-06-06 18:05:57','',50,'Предложение готово',0),(6,'2010-06-06 18:05:58','2010-06-06 18:05:58','',60,'Тендер выигран',0);
/*!40000 ALTER TABLE `co_TenderParticipanceState` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_TenderParticipant`
--

DROP TABLE IF EXISTS `co_TenderParticipant`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_TenderParticipant` (
  `co_TenderParticipantId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Tender` int(11) default NULL,
  `Team` int(11) default NULL,
  `State` int(11) default NULL,
  `Project` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TenderParticipantId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_TenderParticipant`
--

LOCK TABLES `co_TenderParticipant` WRITE;
/*!40000 ALTER TABLE `co_TenderParticipant` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_TenderParticipant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_TenderState`
--

DROP TABLE IF EXISTS `co_TenderState`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_TenderState` (
  `co_TenderStateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_TenderStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_TenderState`
--

LOCK TABLES `co_TenderState` WRITE;
/*!40000 ALTER TABLE `co_TenderState` DISABLE KEYS */;
INSERT INTO `co_TenderState` (`co_TenderStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:55','2010-06-06 18:05:55','',10,'Открыт',0),(2,'2010-06-06 18:05:55','2010-06-06 18:05:55','',20,'Завершен',0),(3,'2010-06-06 18:05:55','2010-06-06 18:05:55','',30,'Отменен',0);
/*!40000 ALTER TABLE `co_TenderState` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_UserGroup`
--

DROP TABLE IF EXISTS `co_UserGroup`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_UserGroup` (
  `co_UserGroupId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `ParentGroup` int(11) default NULL,
  `LDAPUID` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_UserGroupId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_UserGroup`
--

LOCK TABLES `co_UserGroup` WRITE;
/*!40000 ALTER TABLE `co_UserGroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_UserGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_UserGroupLink`
--

DROP TABLE IF EXISTS `co_UserGroupLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_UserGroupLink` (
  `co_UserGroupLinkId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `UserGroup` int(11) default NULL,
  `SystemUser` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_UserGroupLinkId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_UserGroupLink`
--

LOCK TABLES `co_UserGroupLink` WRITE;
/*!40000 ALTER TABLE `co_UserGroupLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_UserGroupLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `co_UserRole`
--

DROP TABLE IF EXISTS `co_UserRole`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `co_UserRole` (
  `co_UserRoleId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `SystemUser` int(11) default NULL,
  `CommunityRole` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_UserRoleId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `co_UserRole`
--

LOCK TABLES `co_UserRole` WRITE;
/*!40000 ALTER TABLE `co_UserRole` DISABLE KEYS */;
/*!40000 ALTER TABLE `co_UserRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entity`
--

DROP TABLE IF EXISTS `entity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `entity` (
  `entityId` int(11) NOT NULL auto_increment,
  `Caption` text,
  `ReferenceName` text,
  `packageId` int(11) default NULL,
  `IsOrdered` char(1) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `IsDictionary` char(1) default NULL,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`entityId`),
  UNIQUE KEY `XPKentity` (`entityId`),
  KEY `entity_vpd_idx` (`VPD`),
  KEY `ReferenceName` (`ReferenceName`(30))
) ENGINE=MyISAM AUTO_INCREMENT=362 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `entity`
--

LOCK TABLES `entity` WRITE;
/*!40000 ALTER TABLE `entity` DISABLE KEYS */;
INSERT INTO `entity` (`entityId`, `Caption`, `ReferenceName`, `packageId`, `IsOrdered`, `OrderNum`, `RecordCreated`, `RecordModified`, `IsDictionary`, `VPD`, `RecordVersion`) VALUES (1,'Меню','cms_MainMenu',1,'Y',10,NULL,NULL,NULL,NULL,0),(2,'Страница','cms_Page',1,'Y',20,NULL,NULL,NULL,NULL,0),(3,'Участник','pm_Participant',2,'Y',30,NULL,'2006-01-28 10:34:07','Y',NULL,0),(4,'Участие в проекте','pm_ParticipantRole',2,'Y',40,NULL,NULL,NULL,NULL,0),(5,'Проект','pm_Project',2,'Y',50,NULL,NULL,'N',NULL,0),(6,'Роль в проекте','pm_ProjectRole',2,'Y',60,NULL,'2006-01-28 10:34:37','Y',NULL,0),(7,'Каталог','pm_ArtefactType',2,'Y',70,NULL,'2006-01-26 21:21:25','Y',NULL,0),(8,'Файл','pm_Artefact',2,'Y',80,NULL,NULL,NULL,NULL,0),(9,'Wiki страница','WikiPage',1,'Y',90,NULL,NULL,NULL,NULL,0),(10,'Файл страницы','WikiPageFile',1,'Y',100,NULL,NULL,NULL,NULL,0),(11,'Ошибка','pm_Bug',2,'Y',110,NULL,NULL,NULL,NULL,0),(12,'Доработка','pm_Enhancement',2,'Y',120,NULL,'2006-01-11 23:54:24','',NULL,0),(13,'pm_Task','Задача',2,'Y',130,NULL,NULL,NULL,NULL,0),(14,'Итерация','pm_Release',2,'Y',140,NULL,'2006-01-28 10:56:05','Y',NULL,0),(15,'Задача','pm_Task',2,'Y',150,NULL,NULL,NULL,NULL,0),(16,'Изменение страницы Wiki','WikiPageChange',1,'',160,NULL,'2005-12-22 21:42:44',NULL,NULL,0),(17,'Приоритет','Priority',2,'Y',170,'2005-12-24 11:54:48','2005-12-24 22:59:48','Y',NULL,0),(18,'Митинг','pm_Meeting',2,'Y',180,'2005-12-24 11:57:42','2005-12-24 11:57:42',NULL,NULL,0),(19,'Участие в митинге','MeetingParticipation',2,'Y',190,'2005-12-24 12:00:55','2005-12-24 12:00:55',NULL,NULL,0),(20,'Тип задачи','pm_TaskType',2,'Y',200,'2005-12-24 21:49:45','2005-12-24 22:26:51','Y',NULL,0),(21,'Состояние работы','pm_TaskState',2,'Y',210,'2005-12-25 00:20:57','2005-12-25 00:20:57','Y',NULL,0),(22,'Пожелание','pm_ChangeRequest',2,'Y',220,'2005-12-28 08:53:48','2005-12-28 08:53:48',NULL,NULL,0),(23,'Создание проекта','pm_ProjectCreation',2,'Y',230,'2006-01-06 13:33:44','2006-01-06 13:33:44',NULL,'',0),(24,'Новость','News',1,'Y',240,'2006-01-06 18:51:23','2006-01-06 18:51:23',NULL,'',0),(25,'Электронное письмо','Email',1,'Y',250,'2006-01-06 21:12:53','2006-01-06 21:12:53',NULL,'',0),(26,'Использование проекта','pm_ProjectUse',2,'Y',260,'2006-01-09 16:47:02','2006-01-09 16:47:02',NULL,'',0),(27,'Справка','pm_Help',2,'Y',270,'2006-01-09 22:42:25','2006-01-09 22:42:25',NULL,'',0),(28,'Уведомление об операции над объектом','ObjectEmailNotification',1,'Y',280,'2006-01-14 14:53:02','2006-01-14 14:53:02',NULL,'',0),(29,'Связь уведомления с классом','ObjectEmailNotificationLink',1,'Y',290,'2006-01-14 14:53:33','2006-01-14 14:53:33',NULL,'',0),(30,'Изменение объекта','ObjectChangeLog',1,'Y',300,'2006-01-16 21:29:23','2006-01-16 21:29:23',NULL,'',0),(31,'Связь со справочной документацией','HelpLink',2,'Y',310,'2006-01-17 08:47:27','2006-01-17 08:47:27',NULL,'',0),(32,'Причина деактуализации справочной документации ','pm_HelpDeactReason',2,NULL,330,'2006-01-19 09:59:15','2006-01-19 09:59:15',NULL,'',0),(36,'Методология','pm_Methodology',2,NULL,350,'2006-01-25 20:54:22','2006-01-25 20:54:22',NULL,'',0),(35,'Комментарий','Comment',1,'Y',340,'2006-01-21 14:40:03','2006-01-21 14:40:03',NULL,'',0),(37,'Реклама книг','AdvertiseBooks',1,'Y',360,'2006-01-29 21:41:13','2006-01-29 21:41:13',NULL,'',0),(38,'Взнос','Donation',1,NULL,370,'2006-02-02 21:59:50','2006-02-02 21:59:50',NULL,'',0),(39,'Релиз','pm_Version',2,'Y',380,'2006-02-09 21:56:11','2006-02-09 21:56:11','Y','',0),(40,'Блог','Blog',1,'Y',390,'2006-02-11 17:00:52','2006-02-11 17:00:52',NULL,'',0),(41,'Сообщение блога','BlogPost',1,'Y',400,'2006-02-11 17:02:16','2006-02-11 17:02:16',NULL,'',0),(42,'Файл сообщения блога','BlogPostFile',1,'Y',410,'2006-02-11 17:04:56','2006-02-11 17:04:56',NULL,'',0),(43,'Ссылка на блог','BlogLink',1,'Y',420,'2006-02-11 17:08:12','2006-02-11 17:08:12',NULL,'',0),(44,'Подписчик блога','BlogSubscriber',1,'Y',430,'2006-02-11 17:10:33','2006-02-11 17:10:33',NULL,'',0),(45,'Очередь сообщений','EmailQueue',1,'Y',440,'2006-02-12 21:22:50','2006-02-12 21:22:50',NULL,'',0),(46,'Адресат очереди сообщений','EmailQueueAddress',1,'Y',450,'2006-02-12 21:23:47','2006-02-12 21:23:47',NULL,'',0),(47,'Публикация проекта','pm_PublicInfo',2,'Y',460,'2006-02-13 21:24:35','2006-02-13 21:24:35',NULL,'',0),(48,'Шаблон - HTML','TemplateHTML',2,'Y',470,'2006-02-23 22:30:12','2006-02-23 22:30:12',NULL,'',0),(49,'Сборка','pm_Build',2,'Y',480,'2006-02-25 15:45:51','2006-02-25 15:45:51','Y','',0),(50,'Связь задачи и сборки','pm_BuildTask',2,'Y',490,'2006-02-26 16:10:36','2006-02-26 16:10:36',NULL,'',0),(51,'Категория ссылок','cms_LinkCategory',1,'Y',500,'2006-03-06 22:00:32','2006-03-06 22:00:32','Y','',0),(52,'Ссылка','cms_Link',1,'Y',510,'2006-03-06 22:01:05','2006-03-06 22:01:05',NULL,'',0),(56,'Тэг Wiki страницы','WikiTag',1,'Y',530,'2006-03-16 21:17:31','2006-03-16 21:17:31',NULL,'',0),(55,'Тэг','Tag',2,'Y',520,'2006-03-16 21:17:04','2006-03-16 21:17:04','Y','',0),(57,'Язык','cms_Language',1,'Y',540,'2006-03-21 23:27:44','2006-03-21 23:27:44','Y','',0),(58,'Тэг пожелания','pm_RequestTag',2,'Y',550,'2006-03-26 10:12:09','2006-03-26 10:12:09',NULL,'',0),(59,'Конфигурация программного продукта','pm_Configuration',2,'Y',560,'2006-03-27 23:30:38','2006-03-27 23:30:38','Y','',0),(60,'Резервная копия','cms_Backup',1,'Y',570,'2010-06-06 18:05:01','2010-06-06 18:05:01','Y','',0),(61,'Обновление','cms_Update',1,'Y',580,'2010-06-06 18:05:01','2010-06-06 18:05:01','Y','',0),(62,'Настройки системы','cms_SystemSettings',1,'Y',590,'2010-06-06 18:05:02','2010-06-06 18:05:02',NULL,'',0),(63,'Пользователь','cms_User',1,'Y',600,'2010-06-06 18:05:03','2010-06-06 18:05:03',NULL,'',0),(64,'Функция','pm_Function',2,'Y',610,'2010-06-06 18:05:04','2010-06-06 18:05:04','Y','',0),(65,'Пользовательская настройка','cms_UserSettings',1,NULL,620,'2010-06-06 18:05:07','2010-06-06 18:05:07',NULL,'',0),(66,'Протокол переходов','cms_BrowserTransitionLog',1,NULL,630,'2010-06-06 18:05:09','2010-06-06 18:05:09',NULL,'',0),(67,'Информация о клиенте','cms_ClientInfo',1,NULL,640,'2010-06-06 18:05:09','2010-06-06 18:05:09',NULL,'',0),(68,'Новостной канал','pm_NewsChannel',2,'Y',640,'2010-06-06 18:05:10','2010-06-06 18:05:10','Y','',0),(69,'Позиция в новостном канале','pm_NewsChannelItem',2,'Y',650,'2010-06-06 18:05:11','2010-06-06 18:05:11',NULL,'',0),(70,'Подписка проекта на новости','pm_NewsChannelSubscription',2,'Y',670,'2010-06-06 18:05:11','2010-06-06 18:05:11',NULL,'',0),(72,'Почтовое сообщение','pm_UserMail',2,'Y',690,'2010-06-06 18:05:13','2010-06-06 18:05:13',NULL,'',0),(74,'Тест','pm_Test',2,'Y',710,'2010-06-06 18:05:14','2010-06-06 18:05:14',NULL,'',0),(75,'Проверка тестового случая','pm_TestCaseExecution',2,'Y',720,'2010-06-06 18:05:14','2010-06-06 18:05:14',NULL,'',0),(76,'Ежедневный митинг','pm_Scrum',2,'Y',730,'2010-06-06 18:05:16','2010-10-01 17:16:29','N','',0),(77,'Окружение','pm_Environment',2,'Y',740,'2010-06-06 18:05:17','2010-06-06 18:05:17','Y','',0),(78,'Приложение','pm_Attachment',2,'N',750,'2010-06-06 18:05:17','2010-06-06 18:05:17','Y','',0),(79,'Веха','pm_Milestone',2,'Y',760,'2010-06-06 18:05:18','2010-06-06 18:05:18','Y','',0),(80,'Участие в митинге','pm_MeetingParticipant',2,'Y',770,'2010-06-06 18:05:19','2010-06-06 18:05:19','Y','',0),(81,'Заметка к релизу','pm_ReleaseNote',2,'Y',780,'2010-06-06 18:05:20','2010-06-06 18:05:20',NULL,'',0),(82,'Активность','pm_Activity',2,'Y',790,'2010-06-06 18:05:20','2010-06-06 18:05:20',NULL,'',0),(84,'Связь теста с требованием','pm_TestScenarioReqLink',2,'Y',810,'2010-06-06 18:05:21','2010-06-06 18:05:21',NULL,'',0),(85,'Тип связи пожеланий','pm_ChangeRequestLinkType',2,'Y',820,'2010-06-06 18:05:22','2010-06-06 18:05:22','Y','',0),(86,'Связь с пожеланиями','pm_ChangeRequestLink',2,'Y',830,'2010-06-06 18:05:22','2010-06-06 18:05:22',NULL,'',0),(87,'Тип пожелания','pm_IssueType',2,'Y',840,'2010-06-06 18:05:23','2010-06-06 18:05:23','Y','',0),(88,'Опросник','pm_Poll',2,NULL,850,'2010-06-06 18:05:24','2010-06-06 18:05:24',NULL,'',0),(89,'Позиция опросника','pm_PollItem',2,'Y',860,'2010-06-06 18:05:24','2010-06-06 18:05:24',NULL,'',0),(90,'Результат опроса','pm_PollResult',2,NULL,870,'2010-06-06 18:05:25','2010-06-06 18:05:25',NULL,'',0),(91,'Позиция результата опроса','pm_PollItemResult',2,'Y',880,'2010-06-06 18:05:25','2010-06-06 18:05:25',NULL,'',0),(92,'Сериализированный объект','cms_SerializedObject',1,NULL,890,'2010-06-06 18:05:26','2010-06-06 18:05:26',NULL,'',0),(93,'Ремап объекта','cms_RemapObject',1,NULL,900,'2010-06-06 18:05:26','2010-06-06 18:05:26',NULL,'',0),(94,'Источник синхронизации','cms_SynchronizationSource',1,'Y',910,'2010-06-06 18:05:27','2010-06-06 18:05:27',NULL,'',0),(95,'Срок','pm_Deadline',2,'Y',920,'2010-06-06 18:05:27','2010-06-06 18:05:27',NULL,'',0),(96,'Удаленный объект','cms_DeletedObject',1,'Y',930,'2010-06-06 18:05:28','2010-06-06 18:05:28',NULL,'',0),(97,'Метрики итерации','pm_ReleaseMetrics',2,NULL,940,'2010-06-06 18:05:29','2010-06-06 18:05:29',NULL,'',0),(98,'Пользовательская блокировка','cms_UserLock',1,NULL,950,'2010-06-06 18:05:30','2010-06-06 18:05:30',NULL,'',0),(99,'Состояние требования','pm_RequirementState',2,'Y',960,'2010-06-06 18:05:31','2010-06-06 18:05:31','Y','',0),(100,'Шаблон - HTML','TemplateHTML2',2,'Y',470,'2010-06-06 18:05:31','2010-06-06 18:05:31','Y','',0),(101,'Почтовая рассылка','cms_EmailNotification',1,'Y',970,'2010-06-06 18:05:32','2010-06-06 18:05:32','Y','',0),(102,'Подписка на рассылку','cms_NotificationSubscription',1,'Y',980,'2010-06-06 18:05:33','2010-06-06 18:05:33',NULL,'',0),(103,'Команда','co_Team',3,NULL,990,'2010-06-06 18:05:33','2010-06-06 18:05:33',NULL,'',0),(104,'Участие в команде','co_TeamUser',3,'Y',1000,'2010-06-06 18:05:34','2010-06-06 18:05:34',NULL,'',0),(105,'Лицензия','cms_License',1,'Y',1010,'2010-06-06 18:05:34','2010-06-06 18:05:34',NULL,'',0),(106,'Тэг проекта','pm_ProjectTag',2,'Y',1020,'2010-06-06 18:05:34','2010-06-06 18:05:34',NULL,'',0),(107,'Подписка на проект','co_ProjectSubscription',3,NULL,1030,'2010-06-06 18:05:35','2010-06-06 18:05:35',NULL,'',0),(108,'Вакансия в проекте','pm_Vacancy',2,'Y',1040,'2010-06-06 18:05:35','2010-06-06 18:05:35',NULL,'',0),(109,'Категория услуги','co_ServiceCategory',3,'Y',1050,'2010-06-06 18:05:37','2010-06-06 18:05:37','Y','',0),(110,'Услуга','co_Service',3,'Y',1060,'2010-06-06 18:05:37','2010-06-06 18:05:37',NULL,'',0),(111,'Заявка на услугу','co_ServiceRequest',3,'Y',1070,'2010-06-06 18:05:37','2010-06-06 18:05:37',NULL,'',0),(112,'text(1058)','pm_Subversion',2,NULL,1080,'2010-06-06 18:05:38','2010-06-06 18:05:38',NULL,'',0),(113,'text(1060)','pm_SubversionRevision',2,NULL,1090,'2010-06-06 18:05:39','2010-06-06 18:05:39',NULL,'',0),(114,'Аутсорсинг пожелания','co_IssueOutsourcing',3,'Y',1100,'2010-06-06 18:05:39','2010-06-06 18:05:39',NULL,'',0),(115,'Предложение по реализации','co_OutsourcingSuggestion',3,NULL,1110,'2010-06-06 18:05:40','2010-06-06 18:05:40',NULL,'',0),(116,'Приглашение в проект','pm_Invitation',2,NULL,1120,'2010-06-06 18:05:40','2010-06-06 18:05:40',NULL,'',0),(117,'Загрузка артефакта','pm_DownloadAction',2,NULL,1130,'2010-06-06 18:05:41','2010-06-06 18:05:41',NULL,'',0),(118,'Загрузивший пользователь','pm_DownloadActor',2,NULL,1140,'2010-06-06 18:05:41','2010-06-06 18:05:41',NULL,'',0),(119,'Тематика совета','co_AdviseTheme',3,NULL,1150,'2010-06-06 18:05:42','2010-06-06 18:05:42','Y','',0),(120,'Совет пользователям','co_Advise',3,NULL,1160,'2010-06-06 18:05:42','2010-06-06 18:05:42',NULL,'',0),(121,'Метрика участника','pm_ParticipantMetrics',2,NULL,1170,'2010-06-06 18:05:43','2010-06-06 18:05:43',NULL,'',0),(122,'Счет','co_Bill',3,NULL,1180,'2010-06-06 18:05:43','2010-06-06 18:05:43',NULL,'',0),(123,'Операция по счету','co_BillOperation',3,'Y',1190,'2010-06-06 18:05:43','2010-06-06 18:05:43',NULL,'',0),(124,'Рейтинг','co_Rating',3,NULL,1200,'2010-06-06 18:05:44','2010-06-06 18:05:44',NULL,'',0),(125,'Голос рейтинга','co_RatingVoice',3,NULL,1210,'2010-06-06 18:05:44','2010-06-06 18:05:44',NULL,'',0),(126,'Опция','co_Option',3,NULL,1220,'2010-06-06 18:05:45','2010-06-06 18:05:45',NULL,'',0),(127,'Подключение опции','co_OptionUser',3,NULL,1230,'2010-06-06 18:05:45','2010-06-06 18:05:45',NULL,'',0),(128,'Метрика итерации','pm_IterationMetric',2,NULL,1240,'2010-06-06 18:05:46','2010-06-06 18:05:46',NULL,'',0),(129,'Метрика релиза','pm_VersionMetric',2,NULL,1250,'2010-06-06 18:05:46','2010-06-06 18:05:46',NULL,'',0),(130,'Черный список','cms_BlackList',1,NULL,1260,'2010-06-06 18:05:47','2010-06-06 18:05:47',NULL,'',0),(131,'Попытка логина','cms_LoginRetry',1,NULL,1270,'2010-06-06 18:05:47','2010-06-06 18:05:47',NULL,'',0),(132,'Контрольный вопрос','cms_CheckQuestion',1,NULL,1280,'2010-06-06 18:05:47','2010-06-06 18:05:47',NULL,'',0),(200,'Тэг сообщения блога','BlogPostTag',1,'Y',1290,'2010-06-06 18:05:49','2010-06-06 18:05:49',NULL,'',0),(201,'Сообщение','co_Message',3,'Y',1300,'2010-06-06 18:05:50','2010-06-06 18:05:50',NULL,'',0),(202,'Результат поиска','co_SearchResult',3,NULL,1310,'2010-06-06 18:05:51','2010-06-06 18:05:51',NULL,'',0),(203,'Вопрос','pm_Question',2,'Y',1320,'2010-06-06 18:05:52','2010-06-06 18:05:52',NULL,'',0),(204,'Статус команды','co_TeamState',3,'Y',1330,'2010-06-06 18:05:52','2010-06-06 18:05:52','Y','',0),(205,'Роль в сообществе','co_CommunityRole',3,'Y',1340,'2010-06-06 18:05:52','2010-06-06 18:05:52','Y','',0),(206,'Роль пользователя','co_UserRole',3,'Y',1350,'2010-06-06 18:05:53','2010-06-06 18:05:53',NULL,'',0),(207,'Участник проектов','co_ProjectParticipant',3,NULL,1360,'2010-06-06 18:05:53','2010-06-06 18:05:53',NULL,'',0),(208,'Валюта','pm_Currency',2,'Y',1370,'2010-06-06 18:05:54','2010-06-06 18:05:54','Y','',0),(209,'Настройки бюджетирования','pm_BugetSettings',2,'Y',1380,'2010-06-06 18:05:54','2010-06-06 18:05:54',NULL,'',0),(210,'Модель оплаты','pm_PaymentModel',2,'Y',1390,'2010-06-06 18:05:54','2010-06-06 18:05:54','Y','',0),(211,'Состояние тендера','co_TenderState',3,'Y',1400,'2010-06-06 18:05:55','2010-06-06 18:05:55','Y','',0),(212,'Тип тендера','co_TenderKind',3,'Y',1410,'2010-06-06 18:05:55','2010-06-06 18:05:55','Y','',0),(213,'Тендер','co_Tender',3,NULL,1420,'2010-06-06 18:05:55','2010-06-06 18:05:55',NULL,'',0),(214,'Приложение к тендеру','co_TenderAttachment',3,'Y',1430,'2010-06-06 18:05:56','2010-06-06 18:05:56',NULL,'',0),(215,'Состояние участия в тендере','co_TenderParticipanceState',3,'Y',1440,'2010-06-06 18:05:56','2010-06-06 18:05:56','Y','',0),(216,'Участник тендера','co_TenderParticipant',3,'Y',1450,'2010-06-06 18:05:56','2010-06-06 18:05:56',NULL,'',0),(217,'Параметры задачи','cms_BatchJob',1,'Y',1460,'2010-06-06 18:05:57','2010-06-06 18:05:57',NULL,'',0),(300,'Тест план','pm_TestPlan',2,'Y',1470,'2010-06-06 18:06:04','2010-06-06 18:06:04',NULL,'',0),(301,'Позиция тест плана','pm_TestPlanItem',2,'Y',1480,'2010-06-06 18:06:04','2010-06-06 18:06:04',NULL,'',0),(302,'Временный файл','cms_TempFile',1,'Y',1490,'2010-06-06 18:06:06','2010-06-06 18:06:06',NULL,'',0),(303,'Снимок','cms_Snapshot',1,'Y',1500,'2010-06-06 18:06:07','2010-06-06 18:06:07',NULL,'',0),(304,'Элемент снимка','cms_SnapshotItem',1,'Y',1510,'2010-06-06 18:06:07','2010-06-06 18:06:07',NULL,'',0),(305,'Значение элемента снимка','cms_SnapshotItemValue',1,'Y',1520,'2010-06-06 18:06:07','2010-06-06 18:06:07',NULL,'',0),(306,'Право доступа','pm_AccessRight',2,NULL,1530,'2010-06-06 18:06:10','2010-06-06 18:06:10',NULL,'',0),(307,'Хеш идентификаторов','cms_IdsHash',1,NULL,1540,'2010-06-06 18:06:13','2010-06-06 18:06:13',NULL,'',0),(308,'Берндаун релиза','pm_VersionBurndown',2,NULL,1550,'2010-06-06 18:06:14','2010-06-06 18:06:14',NULL,'',0),(309,'Настройки версии','pm_VersionSettings',2,NULL,1560,'2010-06-06 18:06:14','2010-06-06 18:06:14',NULL,'',0),(310,'Изменение сообщения блога','BlogPostChange',2,'Y',1570,'2010-10-01 17:15:58','2010-10-01 17:15:58',NULL,'',0),(311,'Наблюдатель','pm_Watcher',2,'Y',1580,'2010-10-01 17:15:59','2010-10-01 17:15:59',NULL,'',0),(312,'Интервал календаря','pm_CalendarInterval',2,NULL,1590,'2010-10-01 17:15:59','2010-10-01 17:15:59',NULL,'',0),(313,'Почтовый ящик','co_RemoteMailbox',3,'Y',1600,'2010-10-01 17:16:00','2010-10-01 17:16:00','Y','',0),(314,'Задание по расписанию','co_ScheduledJob',3,'Y',1610,'2010-10-01 17:16:01','2010-10-01 17:16:01','Y','',0),(315,'Выполнение задания','co_JobRun',3,'Y',1620,'2010-10-01 17:16:02','2010-10-01 17:16:02',NULL,'',0),(316,'Связанный проект','pm_ProjectLink',2,'Y',1630,'2010-10-01 17:16:24','2010-10-01 17:16:24',NULL,'',0),(317,'Доступ к объекту','pm_ObjectAccess',2,NULL,1640,'2010-10-01 17:16:25','2010-10-01 17:16:25',NULL,'',0),(318,'Группа проектов','co_ProjectGroup',3,'Y',1650,'2010-10-01 17:16:26','2010-10-01 17:16:26','Y','',0),(319,'Проект в группе','co_ProjectGroupLink',3,NULL,1660,'2010-10-01 17:16:26','2010-10-01 17:16:26',NULL,'',0),(320,'Группа пользователей','co_UserGroup',3,'Y',1670,'2010-10-01 17:16:26','2010-10-01 17:16:26','Y','',0),(321,'Пользователь в группе','co_UserGroupLink',3,NULL,1680,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,'',0),(322,'Сущность','entity',1,'Y',1690,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,'',0),(323,'Право доступа','co_AccessRight',3,NULL,1700,'2010-10-01 17:16:27','2010-10-01 17:16:27',NULL,NULL,0),(324,'Модуль','cms_PluginModule',1,NULL,1710,'2010-10-01 17:16:28','2010-10-01 17:16:28',NULL,NULL,0),(325,'Шаблон проекта','pm_ProjectTemplate',2,'Y',1720,'2010-10-01 17:16:29','2010-10-01 17:16:29','Y',NULL,0),(326,'Стадия процесса','pm_ProjectStage',2,'Y',1730,'2010-10-01 17:16:29','2010-10-01 17:16:29','Y',NULL,0),(327,'Тип задачи в стадии процесса','pm_TaskTypeStage',2,'Y',1740,'2010-10-01 17:16:29','2010-10-01 17:16:29','Y',NULL,0),(328,'Результат тестирования','pm_TestExecutionResult',2,'Y',1750,'2010-11-01 21:19:03','2010-11-01 21:19:03','Y','',0),(329,'Отчет','cms_Report',1,'Y',1760,'2010-11-01 21:19:03','2010-11-01 21:19:03','Y',NULL,0),(330,'Категория отчета','cms_ReportCategory',1,'Y',1770,'2010-11-01 21:19:04','2010-11-01 21:19:04','Y',NULL,0),(331,'Трассировка пожелания','pm_ChangeRequestTrace',2,'Y',1780,'2011-01-04 07:52:40','2011-01-04 07:52:40',NULL,NULL,0),(332,'Трассировка задачи','pm_TaskTrace',2,'Y',1790,'2011-01-04 07:52:41','2011-01-04 07:52:41',NULL,NULL,0),(333,'Пользовательский тэг','pm_CustomTag',2,'Y',1800,'2011-02-21 21:08:26','2011-02-21 21:08:26','N',NULL,0),(334,'Важность','pm_Importance',2,'Y',1810,'2011-02-21 21:08:27','2011-02-21 21:08:27','Y',NULL,0),(335,'Состояние','pm_State',2,'Y',1820,'2011-02-21 21:08:27','2011-02-21 21:08:27','Y',NULL,0),(336,'Переход в состояние','pm_Transition',2,'Y',1830,'2011-02-21 21:08:28','2011-02-21 21:08:28','Y',NULL,0),(337,'Состояние объекта','pm_StateObject',2,'N',1840,'2011-02-21 21:08:28','2011-02-21 21:08:28','N',NULL,0),(338,'Право доступа на переход','pm_TransitionRole',2,'Y',1850,'2011-02-21 21:08:29','2011-02-21 21:08:29','N',NULL,0),(339,'Атрибуты перехода','pm_TransitionAttribute',2,'N',1860,'2011-02-21 21:08:30','2011-02-21 21:08:30','N',NULL,0),(340,'Конкурирующий продукт','pm_Competitor',2,'Y',1870,'2011-02-21 21:08:35','2011-02-21 21:08:35','Y',NULL,0),(341,'Анализ функции продукта','pm_FeatureAnalysis',2,'Y',1880,'2011-02-21 21:08:35','2011-02-21 21:08:35','N',NULL,0),(342,'Тип страницы','WikiPageType',1,'Y',1890,'2011-04-14 07:59:48','2011-04-14 07:59:48','Y',NULL,0),(343,'Трассировка страницы','WikiPageTrace',1,'Y',1900,'2011-04-14 07:59:48','2011-04-14 07:59:48','N',NULL,0),(344,'Ресурс','cms_Resource',1,'Y',1910,'2011-04-14 07:59:49','2011-04-14 07:59:49','Y',NULL,0),(345,'Пользовательский отчет','pm_CustomReport',2,'Y',1920,'2011-04-14 07:59:49','2011-04-14 07:59:49','Y',NULL,0),(346,'Пользовательский отчет','co_CustomReport',3,'Y',1930,'2011-04-14 07:59:50','2011-04-14 07:59:50','Y',NULL,0),(347,'Настройка пользователя','pm_UserSetting',2,'N',1940,'2011-04-14 07:59:51','2011-04-14 07:59:51','N',NULL,0),(348,'Кластер сущности','cms_EntityCluster',1,'N',1950,'2011-06-15 08:01:38','2011-06-15 08:01:38','N',NULL,0),(349,'Цель','sm_Aim',2,'Y',1960,'2011-08-13 18:29:28','2011-08-13 18:29:28','Y',NULL,0),(350,'Активность','sm_Activity',2,'Y',1970,'2011-08-13 18:29:28','2011-08-13 18:29:28','Y',NULL,0),(351,'Действие','sm_Action',2,'Y',1980,'2011-08-13 18:29:28','2011-08-13 18:29:28','Y',NULL,0),(352,'Персона','sm_Person',2,'Y',1990,'2011-08-13 18:29:29','2011-08-13 18:29:29','Y',NULL,0),(353,'Пользовательский атрибут','pm_CustomAttribute',2,'Y',2000,'2011-12-09 08:01:30','2011-12-09 08:01:30','Y',NULL,0),(354,'Значение атрибута','pm_AttributeValue',2,'N',2010,'2011-12-09 08:01:31','2011-12-09 08:01:31','N',NULL,0),(355,'Проверка','cms_Checkpoint',1,'N',2020,'2012-03-20 07:59:16','2012-03-20 07:59:16','N',NULL,0),(356,'Предикат','pm_Predicate',2,'N',2030,'2012-03-20 07:59:17','2012-03-20 07:59:17','N',NULL,0),(357,'Предусловие на переход','pm_TransitionPredicate',2,'N',2040,'2012-03-20 07:59:17','2012-03-20 07:59:17','N',NULL,0),(358,'Очищаемое поле','pm_TransitionResetField',2,'Y',2050,'2012-03-20 07:59:17','2012-03-20 07:59:17','Y',NULL,0),(359,'Тип почтового ящика','co_MailboxProvider',3,'Y',2060,'2012-03-20 07:59:18','2012-03-20 07:59:18','Y',NULL,0),(360,'Транспорт почты','co_MailTransport',3,'Y',2070,'2012-10-05 07:51:38','2012-10-05 07:51:38','Y',NULL,0),(361,'Дополнительные действия','pm_StateAction',2,'Y',2080,'2012-10-05 07:51:38','2012-10-05 07:51:38','Y',NULL,0);
/*!40000 ALTER TABLE `entity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `package`
--

DROP TABLE IF EXISTS `package`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `package` (
  `packageId` int(11) NOT NULL auto_increment,
  `Caption` text,
  `Description` text,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  PRIMARY KEY  (`packageId`),
  UNIQUE KEY `XPKpackage` (`packageId`),
  KEY `package_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `package`
--

LOCK TABLES `package` WRITE;
/*!40000 ALTER TABLE `package` DISABLE KEYS */;
INSERT INTO `package` (`packageId`, `Caption`, `Description`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`) VALUES (1,'Структура сайта','',10,NULL,NULL,NULL),(2,'Управление проектами','',20,NULL,NULL,NULL),(3,'Сообщество',NULL,NULL,'2010-06-06 18:05:33','2010-06-06 18:05:33','');
/*!40000 ALTER TABLE `package` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_AccessRight`
--

DROP TABLE IF EXISTS `pm_AccessRight`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_AccessRight` (
  `pm_AccessRightId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `ProjectRole` int(11) default NULL,
  `ReferenceName` varchar(32) default NULL,
  `ReferenceType` varchar(32) default NULL,
  `AccessType` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_AccessRightId`),
  KEY `I$46` (`VPD`),
  KEY `I$47` (`ReferenceName`,`ReferenceType`,`ProjectRole`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_AccessRight`
--

LOCK TABLES `pm_AccessRight` WRITE;
/*!40000 ALTER TABLE `pm_AccessRight` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_AccessRight` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Activity`
--

DROP TABLE IF EXISTS `pm_Activity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Activity` (
  `pm_ActivityId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Task` int(11) default NULL,
  `Participant` int(11) default NULL,
  `Description` text,
  `Completed` char(1) default NULL,
  `Capacity` float default NULL,
  `ReportDate` datetime default NULL,
  `RecordVersion` int(11) default '0',
  `Iteration` int(11) default NULL,
  PRIMARY KEY  (`pm_ActivityId`),
  KEY `I$pm_Activity$Participant` (`Participant`),
  KEY `I$pm_Activity$ReportDate` (`ReportDate`),
  KEY `I$pm_Activity$Task` (`Task`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Activity`
--

LOCK TABLES `pm_Activity` WRITE;
/*!40000 ALTER TABLE `pm_Activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Artefact`
--

DROP TABLE IF EXISTS `pm_Artefact`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Artefact` (
  `pm_ArtefactId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Project` int(11) default NULL,
  `ContentMime` text,
  `ContentPath` text,
  `ContentExt` text,
  `Description` text,
  `Kind` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Participant` int(11) default NULL,
  `VPD` varchar(32) default NULL,
  `IsArchived` char(1) default NULL,
  `Version` varchar(32) default NULL,
  `IsAuthorizedDownload` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ArtefactId`),
  UNIQUE KEY `XPKpm_Artefact` (`pm_ArtefactId`),
  KEY `pm_Artefact_vpd_idx` (`VPD`),
  KEY `Kind` (`Kind`,`VPD`,`Project`),
  FULLTEXT KEY `I$44` (`Caption`,`Description`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Artefact`
--

LOCK TABLES `pm_Artefact` WRITE;
/*!40000 ALTER TABLE `pm_Artefact` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Artefact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ArtefactType`
--

DROP TABLE IF EXISTS `pm_ArtefactType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ArtefactType` (
  `pm_ArtefactTypeId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `IsDisplayedOnSite` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ArtefactTypeId`),
  UNIQUE KEY `XPKpm_ArtefactType` (`pm_ArtefactTypeId`),
  KEY `pm_ArtefactType_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ArtefactType`
--

LOCK TABLES `pm_ArtefactType` WRITE;
/*!40000 ALTER TABLE `pm_ArtefactType` DISABLE KEYS */;
INSERT INTO `pm_ArtefactType` (`pm_ArtefactTypeId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `IsDisplayedOnSite`, `RecordVersion`) VALUES (1,30,'Документы разработки',NULL,NULL,NULL,'Y',0),(2,60,'Документы развертывания',NULL,NULL,NULL,'Y',0),(3,40,'Исходный код',NULL,NULL,NULL,'Y',0),(4,50,'Исполняемые файлы',NULL,'2010-06-06 18:05:51',NULL,'Y',0),(5,20,'Документы проектирования',NULL,NULL,NULL,'Y',0),(6,10,'Документы анализа',NULL,NULL,NULL,'Y',0),(7,15,'Документы планирования','2010-06-06 18:05:16','2010-06-06 18:05:16','','Y',0),(8,55,'Документы тестирования','2010-06-06 18:05:22','2010-06-06 18:05:22','','Y',0);
/*!40000 ALTER TABLE `pm_ArtefactType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Attachment`
--

DROP TABLE IF EXISTS `pm_Attachment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Attachment` (
  `pm_AttachmentId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `FileMime` text,
  `FilePath` text,
  `FileExt` text,
  `Description` text,
  `ObjectId` int(11) default NULL,
  `ObjectClass` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_AttachmentId`),
  KEY `i$25` (`ObjectId`,`ObjectClass`,`VPD`),
  KEY `I$pm_Attachment$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Attachment`
--

LOCK TABLES `pm_Attachment` WRITE;
/*!40000 ALTER TABLE `pm_Attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_AttributeValue`
--

DROP TABLE IF EXISTS `pm_AttributeValue`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_AttributeValue` (
  `pm_AttributeValueId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `CustomAttribute` int(11) default NULL,
  `ObjectId` int(11) default NULL,
  `IntegerValue` int(11) default NULL,
  `StringValue` text,
  `TextValue` text,
  `RecordVersion` int(11) default '0',
  `PasswordValue` varchar(128) default NULL,
  PRIMARY KEY  (`pm_AttributeValueId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_AttributeValue`
--

LOCK TABLES `pm_AttributeValue` WRITE;
/*!40000 ALTER TABLE `pm_AttributeValue` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_AttributeValue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Bug`
--

DROP TABLE IF EXISTS `pm_Bug`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Bug` (
  `pm_BugId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `AttachmentMime` text,
  `AttachmentPath` text,
  `AttachmentExt` varchar(32) default NULL,
  `Submitter` int(11) default NULL,
  `State` text,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Requirement` int(11) default NULL,
  `Project` int(11) default NULL,
  `Priority` int(11) default NULL,
  `VPD` varchar(32) default NULL,
  `ChangeRequest` int(11) default NULL,
  `IsPlanned` char(1) default NULL,
  `Test` int(11) default NULL,
  `Build` int(11) default NULL,
  `Release` int(11) default NULL,
  `Environment` int(11) default NULL,
  `TestCaseExecution` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_BugId`),
  UNIQUE KEY `XPKpm_Bug` (`pm_BugId`),
  KEY `pm_Bug_vpd_idx` (`VPD`),
  KEY `ChangeRequest` (`ChangeRequest`,`VPD`),
  KEY `Requirement` (`Requirement`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Bug`
--

LOCK TABLES `pm_Bug` WRITE;
/*!40000 ALTER TABLE `pm_Bug` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Bug` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_BugSettings`
--

DROP TABLE IF EXISTS `pm_BugSettings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_BugSettings` (
  `pm_BugetSettingsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Project` int(11) default NULL,
  `IsBugetUsed` char(1) default NULL,
  `Currency` int(11) default NULL,
  `PaymentModel` int(11) default NULL,
  `HideParticipantsCost` char(1) default NULL,
  PRIMARY KEY  (`pm_BugetSettingsId`),
  KEY `i$4` (`Project`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_BugSettings`
--

LOCK TABLES `pm_BugSettings` WRITE;
/*!40000 ALTER TABLE `pm_BugSettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_BugSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_BugetSettings`
--

DROP TABLE IF EXISTS `pm_BugetSettings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_BugetSettings` (
  `pm_BugetSettingsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Project` int(11) default NULL,
  `IsBugetUsed` char(1) default NULL,
  `Currency` int(11) default NULL,
  `PaymentModel` int(11) default NULL,
  `HideParticipantsCost` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_BugetSettingsId`),
  KEY `i$4` (`Project`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_BugetSettings`
--

LOCK TABLES `pm_BugetSettings` WRITE;
/*!40000 ALTER TABLE `pm_BugetSettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_BugetSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Build`
--

DROP TABLE IF EXISTS `pm_Build`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Build` (
  `pm_BuildId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` int(11) default NULL,
  `Description` text,
  `Result` text,
  `Release` int(11) default NULL,
  `Version` int(11) default NULL,
  `IsActual` char(1) default NULL,
  `Application` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_BuildId`),
  KEY `Release` (`Release`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Build`
--

LOCK TABLES `pm_Build` WRITE;
/*!40000 ALTER TABLE `pm_Build` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Build` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_BuildTask`
--

DROP TABLE IF EXISTS `pm_BuildTask`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_BuildTask` (
  `pm_BuildTaskId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Build` int(11) default NULL,
  `Task` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_BuildTaskId`),
  KEY `Task` (`Task`,`Build`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_BuildTask`
--

LOCK TABLES `pm_BuildTask` WRITE;
/*!40000 ALTER TABLE `pm_BuildTask` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_BuildTask` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_CalendarInterval`
--

DROP TABLE IF EXISTS `pm_CalendarInterval`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_CalendarInterval` (
  `pm_CalendarIntervalId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Kind` varchar(16) default NULL,
  `StartDate` datetime default NULL,
  `FinishDate` datetime default NULL,
  `IntervalYear` int(11) default NULL,
  `IntervalMonth` int(11) default NULL,
  `IntervalDay` int(11) default NULL,
  `Caption` int(11) default NULL,
  `IntervalQuarter` int(11) default NULL,
  `IntervalWeek` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_CalendarIntervalId`),
  KEY `I$55` (`Kind`),
  KEY `I$56` (`IntervalYear`),
  KEY `I$57` (`Caption`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_CalendarInterval`
--

LOCK TABLES `pm_CalendarInterval` WRITE;
/*!40000 ALTER TABLE `pm_CalendarInterval` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_CalendarInterval` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ChangeRequest`
--

DROP TABLE IF EXISTS `pm_ChangeRequest`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ChangeRequest` (
  `pm_ChangeRequestId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `Priority` int(11) default NULL,
  `Author` int(11) default NULL,
  `Project` int(11) default NULL,
  `VPD` varchar(32) default NULL,
  `Function` int(11) default NULL,
  `TestCaseExecution` int(11) default NULL,
  `Estimation` float default NULL,
  `Owner` int(11) default NULL,
  `Type` int(11) default NULL,
  `PlannedRelease` int(11) default NULL,
  `Environment` int(11) default NULL,
  `SubmittedVersion` varchar(255) default NULL,
  `ClosedInVersion` varchar(255) default NULL,
  `State` varchar(32) default NULL,
  `LifecycleDuration` int(11) default NULL,
  `StartDate` date default NULL,
  `FinishDate` date default NULL,
  `RecordVersion` int(11) default '0',
  `EstimationLeft` float default NULL,
  PRIMARY KEY  (`pm_ChangeRequestId`),
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
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ChangeRequest`
--

LOCK TABLES `pm_ChangeRequest` WRITE;
/*!40000 ALTER TABLE `pm_ChangeRequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ChangeRequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ChangeRequestLink`
--

DROP TABLE IF EXISTS `pm_ChangeRequestLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ChangeRequestLink` (
  `pm_ChangeRequestLinkId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `SourceRequest` int(11) default NULL,
  `TargetRequest` int(11) default NULL,
  `LinkType` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ChangeRequestLinkId`),
  KEY `I$53` (`SourceRequest`),
  KEY `I$54` (`TargetRequest`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ChangeRequestLink`
--

LOCK TABLES `pm_ChangeRequestLink` WRITE;
/*!40000 ALTER TABLE `pm_ChangeRequestLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ChangeRequestLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ChangeRequestLinkType`
--

DROP TABLE IF EXISTS `pm_ChangeRequestLinkType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ChangeRequestLinkType` (
  `pm_ChangeRequestLinkTypeId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ChangeRequestLinkTypeId`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ChangeRequestLinkType`
--

LOCK TABLES `pm_ChangeRequestLinkType` WRITE;
/*!40000 ALTER TABLE `pm_ChangeRequestLinkType` DISABLE KEYS */;
INSERT INTO `pm_ChangeRequestLinkType` (`pm_ChangeRequestLinkTypeId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:22','2010-06-06 18:05:22','',10,'Дубликат','duplicates',0),(2,'2010-06-06 18:05:26','2010-06-06 18:05:26','',20,'Зависимость','dependency',0),(3,'2010-06-06 18:05:26','2010-06-06 18:05:26','',30,'Блокируется','blocked',0),(4,'2010-06-06 18:05:26','2010-06-06 18:05:26','',40,'Блокирует','blocks',0);
/*!40000 ALTER TABLE `pm_ChangeRequestLinkType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ChangeRequestTrace`
--

DROP TABLE IF EXISTS `pm_ChangeRequestTrace`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ChangeRequestTrace` (
  `pm_ChangeRequestTraceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ChangeRequest` int(11) default NULL,
  `ObjectId` int(11) default NULL,
  `ObjectClass` varchar(255) default NULL,
  `IsActual` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ChangeRequestTraceId`),
  KEY `I$ChangeRequestTrace$Request` (`ChangeRequest`),
  KEY `I$ChangeRequestTrace$Object` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ChangeRequestTrace`
--

LOCK TABLES `pm_ChangeRequestTrace` WRITE;
/*!40000 ALTER TABLE `pm_ChangeRequestTrace` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ChangeRequestTrace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Competitor`
--

DROP TABLE IF EXISTS `pm_Competitor`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Competitor` (
  `pm_CompetitorId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_CompetitorId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Competitor`
--

LOCK TABLES `pm_Competitor` WRITE;
/*!40000 ALTER TABLE `pm_Competitor` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Competitor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Configuration`
--

DROP TABLE IF EXISTS `pm_Configuration`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Configuration` (
  `pm_ConfigurationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Details` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ConfigurationId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Configuration`
--

LOCK TABLES `pm_Configuration` WRITE;
/*!40000 ALTER TABLE `pm_Configuration` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Currency`
--

DROP TABLE IF EXISTS `pm_Currency`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Currency` (
  `pm_CurrencyId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `CodeName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_CurrencyId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Currency`
--

LOCK TABLES `pm_Currency` WRITE;
/*!40000 ALTER TABLE `pm_Currency` DISABLE KEYS */;
INSERT INTO `pm_Currency` (`pm_CurrencyId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `CodeName`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:54','2010-06-06 18:05:54','',10,'Рубль','RUB',0),(2,'2010-06-06 18:05:54','2010-06-06 18:05:54','',20,'Доллар США','USD',0),(3,'2010-06-06 18:05:54','2010-06-06 18:05:54','',30,'Евро','EUR',0);
/*!40000 ALTER TABLE `pm_Currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_CustomAttribute`
--

DROP TABLE IF EXISTS `pm_CustomAttribute`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_CustomAttribute` (
  `pm_CustomAttributeId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `ReferenceName` text,
  `EntityReferenceName` text,
  `AttributeType` text,
  `DefaultValue` text,
  `IsVisible` char(1) default NULL,
  `ValueRange` text,
  `IsRequired` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  `ObjectKind` varchar(128) default NULL,
  `Description` text,
  `IsUnique` char(1) default NULL,
  PRIMARY KEY  (`pm_CustomAttributeId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_CustomAttribute`
--

LOCK TABLES `pm_CustomAttribute` WRITE;
/*!40000 ALTER TABLE `pm_CustomAttribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_CustomAttribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_CustomReport`
--

DROP TABLE IF EXISTS `pm_CustomReport`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_CustomReport` (
  `pm_CustomReportId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Category` text,
  `Url` text,
  `Description` text,
  `IsHandAccess` char(1) default NULL,
  `Author` int(11) default NULL,
  `ReportBase` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_CustomReportId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_CustomReport`
--

LOCK TABLES `pm_CustomReport` WRITE;
/*!40000 ALTER TABLE `pm_CustomReport` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_CustomReport` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_CustomTag`
--

DROP TABLE IF EXISTS `pm_CustomTag`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_CustomTag` (
  `pm_CustomTagId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Tag` int(11) default NULL,
  `ObjectId` int(11) default NULL,
  `ObjectClass` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_CustomTagId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_CustomTag`
--

LOCK TABLES `pm_CustomTag` WRITE;
/*!40000 ALTER TABLE `pm_CustomTag` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_CustomTag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Deadline`
--

DROP TABLE IF EXISTS `pm_Deadline`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Deadline` (
  `pm_DeadlineId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Deadline` datetime default NULL,
  `Comment` text,
  `ChangeRequest` int(11) default NULL,
  `Milestone` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_DeadlineId`),
  KEY `i$35` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Deadline`
--

LOCK TABLES `pm_Deadline` WRITE;
/*!40000 ALTER TABLE `pm_Deadline` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Deadline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_DownloadAction`
--

DROP TABLE IF EXISTS `pm_DownloadAction`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_DownloadAction` (
  `pm_DownloadActionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `ObjectId` int(11) default NULL,
  `EntityRefName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_DownloadActionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_DownloadAction`
--

LOCK TABLES `pm_DownloadAction` WRITE;
/*!40000 ALTER TABLE `pm_DownloadAction` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_DownloadAction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_DownloadActor`
--

DROP TABLE IF EXISTS `pm_DownloadActor`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_DownloadActor` (
  `pm_DownloadActorId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `SystemUser` int(11) default NULL,
  `DownloadAction` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_DownloadActorId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_DownloadActor`
--

LOCK TABLES `pm_DownloadActor` WRITE;
/*!40000 ALTER TABLE `pm_DownloadActor` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_DownloadActor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Enhancement`
--

DROP TABLE IF EXISTS `pm_Enhancement`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Enhancement` (
  `pm_EnhancementId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `Requirement` int(11) default NULL,
  `Project` int(11) default NULL,
  `AttachmentMime` text,
  `AttachmentPath` text,
  `AttachmentExt` varchar(32) default NULL,
  `Submitter` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Priority` int(11) default NULL,
  `VPD` varchar(32) default NULL,
  `ChangeRequest` int(11) default NULL,
  `IsPlanned` char(1) default NULL,
  `TestCaseExecution` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_EnhancementId`),
  UNIQUE KEY `XPKpm_Enhancement` (`pm_EnhancementId`),
  KEY `pm_Enhancement_vpd_idx` (`VPD`),
  KEY `ChangeRequest` (`ChangeRequest`,`VPD`),
  KEY `Requirement` (`Requirement`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Enhancement`
--

LOCK TABLES `pm_Enhancement` WRITE;
/*!40000 ALTER TABLE `pm_Enhancement` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Enhancement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Environment`
--

DROP TABLE IF EXISTS `pm_Environment`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Environment` (
  `pm_EnvironmentId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_EnvironmentId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Environment`
--

LOCK TABLES `pm_Environment` WRITE;
/*!40000 ALTER TABLE `pm_Environment` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Environment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_FeatureAnalysis`
--

DROP TABLE IF EXISTS `pm_FeatureAnalysis`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_FeatureAnalysis` (
  `pm_FeatureAnalysisId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Competitor` int(11) default NULL,
  `Feature` int(11) default NULL,
  `Strengths` text,
  `Weaknesses` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_FeatureAnalysisId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_FeatureAnalysis`
--

LOCK TABLES `pm_FeatureAnalysis` WRITE;
/*!40000 ALTER TABLE `pm_FeatureAnalysis` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_FeatureAnalysis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Function`
--

DROP TABLE IF EXISTS `pm_Function`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Function` (
  `pm_FunctionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `Importance` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_FunctionId`),
  KEY `VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Function`
--

LOCK TABLES `pm_Function` WRITE;
/*!40000 ALTER TABLE `pm_Function` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Function` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Help`
--

DROP TABLE IF EXISTS `pm_Help`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Help` (
  `pm_HelpId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `Version` varchar(32) default NULL,
  `Content` int(11) default NULL,
  `Project` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_HelpId`),
  UNIQUE KEY `XPKpm_Help` (`pm_HelpId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Help`
--

LOCK TABLES `pm_Help` WRITE;
/*!40000 ALTER TABLE `pm_Help` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Help` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_HelpDeactReason`
--

DROP TABLE IF EXISTS `pm_HelpDeactReason`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_HelpDeactReason` (
  `pm_HelpDeactReasonId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `HelpLink` int(11) default NULL,
  `Task` int(11) default NULL,
  `IsActive` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_HelpDeactReasonId`),
  UNIQUE KEY `XPKpm_HelpDeactReason` (`pm_HelpDeactReasonId`),
  KEY `HelpLink` (`HelpLink`,`Task`,`IsActive`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_HelpDeactReason`
--

LOCK TABLES `pm_HelpDeactReason` WRITE;
/*!40000 ALTER TABLE `pm_HelpDeactReason` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_HelpDeactReason` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Importance`
--

DROP TABLE IF EXISTS `pm_Importance`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Importance` (
  `pm_ImportanceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ImportanceId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Importance`
--

LOCK TABLES `pm_Importance` WRITE;
/*!40000 ALTER TABLE `pm_Importance` DISABLE KEYS */;
INSERT INTO `pm_Importance` (`pm_ImportanceId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `RecordVersion`) VALUES (1,NULL,NULL,NULL,10,'Обязательно','text(885)',0),(2,NULL,NULL,NULL,20,'Важно','text(886)',0),(3,NULL,NULL,NULL,30,'Желательно','text(887)',0);
/*!40000 ALTER TABLE `pm_Importance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Invitation`
--

DROP TABLE IF EXISTS `pm_Invitation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Invitation` (
  `pm_InvitationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Project` int(11) default NULL,
  `Author` int(11) default NULL,
  `Addressee` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_InvitationId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Invitation`
--

LOCK TABLES `pm_Invitation` WRITE;
/*!40000 ALTER TABLE `pm_Invitation` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Invitation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_IssueType`
--

DROP TABLE IF EXISTS `pm_IssueType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_IssueType` (
  `pm_IssueTypeId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_IssueTypeId`),
  KEY `I$pm_IssueType$VPD` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_IssueType`
--

LOCK TABLES `pm_IssueType` WRITE;
/*!40000 ALTER TABLE `pm_IssueType` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_IssueType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_IterationMetric`
--

DROP TABLE IF EXISTS `pm_IterationMetric`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_IterationMetric` (
  `pm_IterationMetricId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Iteration` int(11) default NULL,
  `Metric` varchar(32) default NULL,
  `MetricValue` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_IterationMetricId`),
  KEY `i$18` (`Iteration`,`Metric`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_IterationMetric`
--

LOCK TABLES `pm_IterationMetric` WRITE;
/*!40000 ALTER TABLE `pm_IterationMetric` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_IterationMetric` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Meeting`
--

DROP TABLE IF EXISTS `pm_Meeting`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Meeting` (
  `pm_MeetingId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `OrderNum` int(11) default NULL,
  `Subject` text,
  `Location` text,
  `VPD` varchar(32) default NULL,
  `MeetingDate` datetime default NULL,
  `Agenda` text,
  `MeetingTime` text,
  `RecordVersion` int(11) default '0',
  `RememberInterval` int(11) default NULL,
  PRIMARY KEY  (`pm_MeetingId`),
  UNIQUE KEY `XPKpm_Meeting` (`pm_MeetingId`),
  KEY `pm_Meeting_vpd_idx` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Meeting`
--

LOCK TABLES `pm_Meeting` WRITE;
/*!40000 ALTER TABLE `pm_Meeting` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Meeting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_MeetingParticipant`
--

DROP TABLE IF EXISTS `pm_MeetingParticipant`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_MeetingParticipant` (
  `pm_MeetingParticipantId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Meeting` int(11) default NULL,
  `Participant` int(11) default NULL,
  `Accepted` char(1) default NULL,
  `Rejected` char(1) default NULL,
  `RejectReason` text,
  `RecordVersion` int(11) default '0',
  `RememberInterval` int(11) default NULL,
  PRIMARY KEY  (`pm_MeetingParticipantId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_MeetingParticipant`
--

LOCK TABLES `pm_MeetingParticipant` WRITE;
/*!40000 ALTER TABLE `pm_MeetingParticipant` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_MeetingParticipant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Methodology`
--

DROP TABLE IF EXISTS `pm_Methodology`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Methodology` (
  `pm_MethodologyId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `IsRequirements` char(1) default NULL,
  `IsHelps` char(1) default NULL,
  `IsTests` char(1) default NULL,
  `Project` int(11) default NULL,
  `IsBuilds` char(1) default NULL,
  `IsUserInProject` char(1) default NULL,
  `IsFixedRelease` char(1) default NULL,
  `ReleaseDuration` int(11) default NULL,
  `IsTasksDepend` char(1) default NULL,
  `IsResponsibleForFunctions` char(1) default NULL,
  `IsCrossChecking` char(1) default NULL,
  `IsDesign` char(1) default NULL,
  `IsHighTolerance` char(1) default NULL,
  `VerificationTime` int(11) default NULL,
  `RequestApproveRequired` char(1) default NULL,
  `UseScrums` char(1) default NULL,
  `UseEnvironments` char(1) default NULL,
  `HasMilestones` char(1) default NULL,
  `RequestEstimationRequired` varchar(255) default NULL,
  `IsParticipantsTakeTasks` char(1) default NULL,
  `UseFunctionalDecomposition` char(1) default NULL,
  `IsDeadlineUsed` char(1) default NULL,
  `IsPlanningUsed` char(1) default NULL,
  `IsReportsOnActivities` char(1) default NULL,
  `CustomerAcceptsIssues` char(1) default NULL,
  `IsResponsibleForIssue` char(1) default NULL,
  `IsVersionsUsed` char(1) default NULL,
  `IsReleasesUsed` char(1) default NULL,
  `IsKanbanUsed` char(1) default NULL,
  `IsStoryMappingUsed` char(1) default NULL,
  `IsRequestOrderUsed` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  `TaskEstimationUsed` char(1) default NULL,
  PRIMARY KEY  (`pm_MethodologyId`),
  KEY `i$3` (`Project`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Methodology`
--

LOCK TABLES `pm_Methodology` WRITE;
/*!40000 ALTER TABLE `pm_Methodology` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Methodology` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Milestone`
--

DROP TABLE IF EXISTS `pm_Milestone`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Milestone` (
  `pm_MilestoneId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `MilestoneDate` datetime default NULL,
  `Caption` text,
  `Description` text,
  `Passed` char(1) default NULL,
  `ReasonToChangeDate` text,
  `CompleteResult` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_MilestoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Milestone`
--

LOCK TABLES `pm_Milestone` WRITE;
/*!40000 ALTER TABLE `pm_Milestone` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Milestone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_NewsChannel`
--

DROP TABLE IF EXISTS `pm_NewsChannel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_NewsChannel` (
  `pm_NewsChannelId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `RssLink` text,
  `Language` text,
  `IsPublic` text,
  PRIMARY KEY  (`pm_NewsChannelId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_NewsChannel`
--

LOCK TABLES `pm_NewsChannel` WRITE;
/*!40000 ALTER TABLE `pm_NewsChannel` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_NewsChannel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_NewsChannelItem`
--

DROP TABLE IF EXISTS `pm_NewsChannelItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_NewsChannelItem` (
  `pm_NewsChannelItemId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `HtmlLink` text,
  `NewsChannel` int(11) default NULL,
  PRIMARY KEY  (`pm_NewsChannelItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_NewsChannelItem`
--

LOCK TABLES `pm_NewsChannelItem` WRITE;
/*!40000 ALTER TABLE `pm_NewsChannelItem` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_NewsChannelItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_NewsChannelSubscription`
--

DROP TABLE IF EXISTS `pm_NewsChannelSubscription`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_NewsChannelSubscription` (
  `pm_NewsChannelSubscriptionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `NewsChannel` int(11) default NULL,
  `Project` int(11) default NULL,
  PRIMARY KEY  (`pm_NewsChannelSubscriptionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_NewsChannelSubscription`
--

LOCK TABLES `pm_NewsChannelSubscription` WRITE;
/*!40000 ALTER TABLE `pm_NewsChannelSubscription` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_NewsChannelSubscription` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ObjectAccess`
--

DROP TABLE IF EXISTS `pm_ObjectAccess`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ObjectAccess` (
  `pm_ObjectAccessId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `ObjectClass` varchar(128) default NULL,
  `ObjectId` int(11) default NULL,
  `ProjectRole` int(11) default NULL,
  `AccessType` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ObjectAccessId`),
  KEY `pm_ObjectAccess$Role` (`ProjectRole`),
  KEY `pm_ObjectAccess$Object` (`ObjectId`),
  KEY `pm_ObjectAccess$Class` (`ObjectClass`),
  KEY `pm_ObjectAccess$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ObjectAccess`
--

LOCK TABLES `pm_ObjectAccess` WRITE;
/*!40000 ALTER TABLE `pm_ObjectAccess` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ObjectAccess` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Participant`
--

DROP TABLE IF EXISTS `pm_Participant`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Participant` (
  `pm_ParticipantId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Email` text,
  `Login` text,
  `Password` text,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `ICQNumber` text,
  `Capacity` int(11) default NULL,
  `Project` int(11) default NULL,
  `VPD` varchar(32) default NULL,
  `HomePhone` text,
  `MobilePhone` text,
  `SystemUser` int(11) default NULL,
  `OverrideUser` char(1) default NULL,
  `IsActive` char(1) default NULL,
  `Skype` text,
  `Salary` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ParticipantId`),
  UNIQUE KEY `XPKpm_Participant` (`pm_ParticipantId`),
  KEY `pm_Participant_vpd_idx` (`VPD`),
  KEY `Project` (`Project`,`Login`(20)),
  KEY `SystemUser` (`SystemUser`),
  KEY `i$20` (`IsActive`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Participant`
--

LOCK TABLES `pm_Participant` WRITE;
/*!40000 ALTER TABLE `pm_Participant` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Participant` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ParticipantMetrics`
--

DROP TABLE IF EXISTS `pm_ParticipantMetrics`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ParticipantMetrics` (
  `pm_ParticipantMetricsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Participant` int(11) default NULL,
  `Iteration` int(11) default NULL,
  `Metric` varchar(32) default NULL,
  `MetricValue` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ParticipantMetricsId`),
  KEY `I$pm_ParticipantMetrics$IP` (`Iteration`,`Participant`),
  KEY `I$pm_ParticipantMetrics$IPM` (`Iteration`,`Participant`,`Metric`),
  KEY `i$6` (`Participant`,`Iteration`,`Metric`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ParticipantMetrics`
--

LOCK TABLES `pm_ParticipantMetrics` WRITE;
/*!40000 ALTER TABLE `pm_ParticipantMetrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ParticipantMetrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ParticipantRole`
--

DROP TABLE IF EXISTS `pm_ParticipantRole`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ParticipantRole` (
  `pm_ParticipantRoleId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Participant` int(11) default NULL,
  `Project` int(11) default NULL,
  `ProjectRole` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Capacity` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ParticipantRoleId`),
  UNIQUE KEY `XPKpm_ParticipantRole` (`pm_ParticipantRoleId`),
  KEY `pm_ParticipantRole_vpd_idx` (`VPD`),
  KEY `Participant` (`Participant`,`ProjectRole`,`VPD`),
  KEY `i$19` (`Project`,`Participant`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ParticipantRole`
--

LOCK TABLES `pm_ParticipantRole` WRITE;
/*!40000 ALTER TABLE `pm_ParticipantRole` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ParticipantRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_PaymentModel`
--

DROP TABLE IF EXISTS `pm_PaymentModel`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_PaymentModel` (
  `pm_PaymentModelId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_PaymentModelId`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_PaymentModel`
--

LOCK TABLES `pm_PaymentModel` WRITE;
/*!40000 ALTER TABLE `pm_PaymentModel` DISABLE KEYS */;
INSERT INTO `pm_PaymentModel` (`pm_PaymentModelId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:54','2010-06-06 18:05:54','',10,'Почасовая оплата',0),(2,'2010-06-06 18:05:54','2010-06-06 18:05:54','',20,'Ежемесячная оплата',0);
/*!40000 ALTER TABLE `pm_PaymentModel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Poll`
--

DROP TABLE IF EXISTS `pm_Poll`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Poll` (
  `pm_PollId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `Description` text,
  `IsPublic` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_PollId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Poll`
--

LOCK TABLES `pm_Poll` WRITE;
/*!40000 ALTER TABLE `pm_Poll` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Poll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_PollItem`
--

DROP TABLE IF EXISTS `pm_PollItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_PollItem` (
  `pm_PollItemId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Answers` text,
  `IsSection` char(1) default NULL,
  `Poll` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_PollItemId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_PollItem`
--

LOCK TABLES `pm_PollItem` WRITE;
/*!40000 ALTER TABLE `pm_PollItem` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_PollItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_PollItemResult`
--

DROP TABLE IF EXISTS `pm_PollItemResult`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_PollItemResult` (
  `pm_PollItemResultId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `PollItem` int(11) default NULL,
  `PollResult` int(11) default NULL,
  `Answer` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_PollItemResultId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_PollItemResult`
--

LOCK TABLES `pm_PollItemResult` WRITE;
/*!40000 ALTER TABLE `pm_PollItemResult` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_PollItemResult` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_PollResult`
--

DROP TABLE IF EXISTS `pm_PollResult`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_PollResult` (
  `pm_PollResultId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Poll` int(11) default NULL,
  `User` int(11) default NULL,
  `IsCurrent` char(1) default NULL,
  `CommitDate` datetime default NULL,
  `AnonymousHash` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_PollResultId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_PollResult`
--

LOCK TABLES `pm_PollResult` WRITE;
/*!40000 ALTER TABLE `pm_PollResult` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_PollResult` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Predicate`
--

DROP TABLE IF EXISTS `pm_Predicate`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Predicate` (
  `pm_PredicateId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  PRIMARY KEY  (`pm_PredicateId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Predicate`
--

LOCK TABLES `pm_Predicate` WRITE;
/*!40000 ALTER TABLE `pm_Predicate` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Predicate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Project`
--

DROP TABLE IF EXISTS `pm_Project`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Project` (
  `pm_ProjectId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `CodeName` varchar(32) default NULL,
  `Platform` text,
  `Tools` text,
  `MainWikiPage` int(11) default NULL,
  `RequirementsWikiPage` int(11) default NULL,
  `StartDate` date default NULL,
  `FinishDate` date default NULL,
  `BudgetCode` varchar(32) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Version` int(11) default NULL,
  `Blog` int(11) default NULL,
  `Language` int(11) default NULL,
  `IsConfigurations` char(1) default NULL,
  `IsClosed` char(1) default NULL,
  `Rating` float default NULL,
  `IsTender` char(1) default NULL,
  `IsSubversionUsed` char(1) default NULL,
  `IsArtefactsUsed` char(1) default NULL,
  `HasMeetings` char(1) default NULL,
  `IsPollUsed` char(1) default NULL,
  `IsKnowledgeUsed` char(1) default NULL,
  `IsBlogUsed` char(1) default NULL,
  `WikiEditorClass` text,
  `DaysInWeek` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ProjectId`),
  UNIQUE KEY `XPKpm_Project` (`pm_ProjectId`),
  KEY `pm_Project_vpd_idx` (`VPD`),
  KEY `i$1` (`CodeName`),
  FULLTEXT KEY `Caption` (`Caption`,`Description`),
  FULLTEXT KEY `I$40` (`Caption`,`Description`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Project`
--

LOCK TABLES `pm_Project` WRITE;
/*!40000 ALTER TABLE `pm_Project` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ProjectCreation`
--

DROP TABLE IF EXISTS `pm_ProjectCreation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ProjectCreation` (
  `pm_ProjectCreationId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `IPAddress` text,
  `Project` int(11) default NULL,
  `CodeName` text,
  `Caption` text,
  `Login` text,
  `Email` text,
  `Password` text,
  `Methodology` text,
  `CreationHash` text,
  `Language` text,
  `Access` text,
  `SystemUser` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ProjectCreationId`),
  UNIQUE KEY `XPKpm_ProjectCreation` (`pm_ProjectCreationId`),
  KEY `i$34` (`SystemUser`,`Project`),
  KEY `I$50` (`Project`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ProjectCreation`
--

LOCK TABLES `pm_ProjectCreation` WRITE;
/*!40000 ALTER TABLE `pm_ProjectCreation` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ProjectCreation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ProjectLink`
--

DROP TABLE IF EXISTS `pm_ProjectLink`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ProjectLink` (
  `pm_ProjectLinkId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Target` int(11) default NULL,
  `Source` int(11) default NULL,
  `KnowledgeBase` int(11) default NULL,
  `Blog` int(11) default NULL,
  `Requirements` int(11) default NULL,
  `Testing` int(11) default NULL,
  `HelpFiles` int(11) default NULL,
  `Files` int(11) default NULL,
  `SourceCode` int(11) default NULL,
  `Requests` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  `Releases` int(11) default NULL,
  `Tasks` int(11) default NULL,
  PRIMARY KEY  (`pm_ProjectLinkId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ProjectLink`
--

LOCK TABLES `pm_ProjectLink` WRITE;
/*!40000 ALTER TABLE `pm_ProjectLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ProjectLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ProjectRole`
--

DROP TABLE IF EXISTS `pm_ProjectRole`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ProjectRole` (
  `pm_ProjectRoleId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `ReferenceName` varchar(32) default NULL,
  `ProjectRoleBase` int(11) default NULL,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ProjectRoleId`),
  UNIQUE KEY `XPKpm_ProjectRole` (`pm_ProjectRoleId`),
  KEY `pm_ProjectRole_vpd_idx` (`VPD`),
  KEY `pm_ProjectRole$RefVPD` (`VPD`,`ReferenceName`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ProjectRole`
--

LOCK TABLES `pm_ProjectRole` WRITE;
/*!40000 ALTER TABLE `pm_ProjectRole` DISABLE KEYS */;
INSERT INTO `pm_ProjectRole` (`pm_ProjectRoleId`, `OrderNum`, `Caption`, `RecordCreated`, `RecordModified`, `VPD`, `ReferenceName`, `ProjectRoleBase`, `Description`, `RecordVersion`) VALUES (1,10,'Аналитик',NULL,'2010-06-06 18:06:11',NULL,'analyst',NULL,NULL,0),(2,20,'Разработчик',NULL,'2010-06-06 18:06:11',NULL,'developer',NULL,NULL,0),(3,30,'Тестировщик',NULL,'2010-06-06 18:06:11',NULL,'tester',NULL,NULL,0),(4,40,'Координатор',NULL,'2010-06-06 18:06:11',NULL,'lead',NULL,NULL,0),(5,50,'Заказчик',NULL,'2010-06-06 18:06:11',NULL,'client',NULL,NULL,0),(6,15,'Проектировщик','2010-06-06 18:05:07','2010-06-06 18:05:07',NULL,NULL,NULL,NULL,0),(7,35,'Технический писатель','2010-06-06 18:05:07','2010-06-06 18:06:11',NULL,'writer',NULL,NULL,0),(8,15,'Архитектор',NULL,NULL,NULL,'architect',NULL,NULL,0);
/*!40000 ALTER TABLE `pm_ProjectRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ProjectStage`
--

DROP TABLE IF EXISTS `pm_ProjectStage`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ProjectStage` (
  `pm_ProjectStageId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ProjectStageId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ProjectStage`
--

LOCK TABLES `pm_ProjectStage` WRITE;
/*!40000 ALTER TABLE `pm_ProjectStage` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ProjectStage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ProjectTag`
--

DROP TABLE IF EXISTS `pm_ProjectTag`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ProjectTag` (
  `pm_ProjectTagId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Project` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ProjectTagId`),
  FULLTEXT KEY `I$41` (`Caption`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ProjectTag`
--

LOCK TABLES `pm_ProjectTag` WRITE;
/*!40000 ALTER TABLE `pm_ProjectTag` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ProjectTag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ProjectTemplate`
--

DROP TABLE IF EXISTS `pm_ProjectTemplate`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ProjectTemplate` (
  `pm_ProjectTemplateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `FileName` text,
  `IsDefault` char(1) default NULL,
  `Language` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ProjectTemplateId`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ProjectTemplate`
--

LOCK TABLES `pm_ProjectTemplate` WRITE;
/*!40000 ALTER TABLE `pm_ProjectTemplate` DISABLE KEYS */;
INSERT INTO `pm_ProjectTemplate` (`pm_ProjectTemplateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `Description`, `FileName`, `IsDefault`, `Language`, `RecordVersion`) VALUES (31,'2010-08-17 08:36:48','2010-08-17 08:36:48',NULL,130,'Проект разработки программного продукта (SDLC)','В этом шаблоне используются все возможности DEVPROM с целью автоматизации жизненного цикла разработки программного продукта (Software Development LifeCycle). \n\nУчастникам проекта по умолчанию доступны следующие возможности:\n\n- Управление фичами (функциями или свойствами) продукта\n- Управление ожиданиями к функциональности (с использованием журналов пожеланий)\n- Управление версиями и релизами продукта\n- Детальное планирование задач и контроль их выполнения\n- Управление требованиями к продукту\n- Управление тестированием продукта\n- Управление документацией к продукту\n- Интеграция с системами контроля версий','sdlc_ru.xml','N',1,0),(30,'2010-08-17 08:29:39','2010-08-17 08:29:39',NULL,120,'Software development project (SDLC)','All features of DEVPROM are used in this template to allow your team to have fully automated software development lifecycle.\n\nProject team members can use:\n- Product feature management\n- Issue and enhancement management\n- Version and release management\n- Detailed planning of tasks\n- Requirements management\n- Testing facilities\n- Documentation management\n- Tight integration with source code control systems','sdlc_en.xml','N',2,0),(35,'2010-08-17 08:57:51','2010-08-17 08:57:51',NULL,160,'Проект поддержки программного продукта','Для поддержки выпущенных продуктов или их версий используются так называемые bug trackers или issue trackers (баг-трекеры), которые позволяют регистрировать обнаруженные ошибки, вновь появляющиеся пожелания к функциональности, назначать исполнителей для выполнения этих пожеланий, а также планировать состав очередного обновления или версии.\n\nДанный шаблон позволяет использовать DEVPROM как типовой баг-трекер, без необходимости планирования итераций, декомпозиции пожеланий (или ошибок) на задачи, управления требованиями и другими артефактами проекта.','issuetr_ru.xml','N',1,0),(36,'2010-08-17 08:59:22','2010-08-17 08:59:22',NULL,170,'Software support project','To support the software product releases bug trackers or issue trackers are commonly used. It allow to raise issues or enhancements, assign them to developers, to plan the scope for futher updates or releases.\n\nThis template allows you to use DEVPROM as usual bug-tracker without to have iteration planning, issues decomposing to tasks, requirements management and so on.','issuetr_en.xml','N',2,0),(37,'2010-08-17 09:02:17','2010-08-17 09:02:17',NULL,180,'Tickets processing','The template feets for requests processing from users, customers or employees of your company. The minimum functionality of DEVPROM is accessible. On the one hand it allows to raise requests, to appoint them to executives, to control the process, and with another hand it essentially simplifies work with the tool because of absence of planning, tasks and other unused tabs.','ticket_en.xml','N',2,0),(38,'2010-08-17 09:03:09','2010-08-17 09:03:09',NULL,190,'Обработка заявок','Шаблон подойдет для обработки запросов пользователей, заказчика или сотрудников вашей компании. Доступна минимальная функциональность DEVPROM, что с одной стороны позволяет заводить заявки, назначать их на ответственных исполнителей, вести контроль исполнения, а с другой - существенно упрощает работу с инструментом из-за отсутствия планирования, задач и других лишних закладок.','ticket_ru.xml','N',1,0),(39,'2010-08-17 09:03:09','2010-08-17 09:03:09',NULL,190,'Open Unified Process','OpenUP is a lean Unified Process that applies iterative and incremental approaches within a structured lifecycle. OpenUP embraces a pragmatic, agile philosophy that focuses on the collaborative nature of software development. It is a tools-agnostic, low-ceremony process that can be extended to address a broad variety of project types. \n\nPersonal effort on an OpenUP project is organized in micro-increments. These represent short units of work that produce a steady, measurable pace of project progress (typically measured in hours or a few days). The process applies intensive collaboration as the system is incrementally developed by a committed, self-organized team. These micro-increments provide an extremely short feedback loop that drives adaptive decisions within each iteration. OpenUP divides the project into iterations: planned, time-boxed intervals typically measured in weeks. Iterations focus the team on delivering incremental value to stakeholders in a predictable manner. The iteration plan defines what should be delivered within the iteration, and the result is a demo-able or shippable build. OpenUP teams self-organize around how to accomplish iteration objectives and commit to delivering the results. They do that by defining and \"pulling\" fine-grained tasks from a work items list. OpenUP applies an iteration lifecycle that structures how micro-increments are applied to deliver stable, cohesive builds of the system that incrementally progresses towards the iteration objectives.\n\nOpenUP structures the project lifecycle into four phases: Inception, Elaboration, Construction, and Transition. The project lifecycle provides stakeholders and team members with visibility and decision points throughout the project. This enables effective oversight, and allows you to make \"go or no-go\" decisions at appropriate times. A project plan defines the lifecycle, and the end result is a released application.','openup_en.xml','N',2,0),(40,NULL,NULL,NULL,NULL,'MSF Agile v4.2','Microsoft Solutions Framework (MSF) for Agile Software Development is a scenario-driven, context-based, agile software development process for building .NET and other object-oriented applications. MSF for Agile Software Development directly incorporates practices for handling quality of service requirements such as performance and security. It is also context-based and uses a context-driven approach to determine how to operate the project.\n\nThis approach helps create an adaptive process that overcomes the boundary conditions of most agile software development processes while achieving the objectives set out in the vision of the project. Product definition, development, and testing occur in overlapping iterations resulting in incremental completion of the project. Different iterations have different focus as the project approaches release. Small iterations allow you to reduce the margin of error in your estimates and provide fast feedback about the accuracy of your project plans. Each iteration should result in a stable portion of the overall system.','msfagile_en.xml','N',2,0),(41,NULL,NULL,NULL,NULL,'Scrum','text(985)','scrum_ru.xml','N',1,0),(43,NULL,NULL,NULL,NULL,'Scrum (упрощенный)','text(985)','scrum_simple_ru.xml',NULL,1,0),(42,NULL,NULL,NULL,NULL,'Scrum','SCRUM methodology is one of the most famous project or program management methodologies. Agile methodology is a new methodology of software development and has many advanTages compared to the classical methods.<br/><br/>The Scrum methodology is iterate (the product is produced during the small cycles called iterations), incremental (the functionality of the product increase during each iteration by adding new properties) and use its own terminology for working people and some procedural steps.<br/><br/>Scrum methodology have many elements but none of them is random and unnecessary. There is a project team consisted of the owner of the product , ScrumMaster and Scrum team. Each one have different task to perform.<br/><br/>The entire Scrum process is also divided. There are following phases: pregame, game and postgame. The structure of the Scrum methodology is very complex but not complicated!<br/><br/>Scrum is a process template commonly used to oversee projects, and manage complex work. Typically it is complemented by agile software development, encouraging teamwork and self-organization. The name itself is influenced by \"scrums\" in terms of the game of rugby. The name reflects the nature of what is achieved through the method, in terms of group members all successively striving towards better work efficiency.','scrum_en.xml',NULL,2,0),(44,NULL,NULL,NULL,NULL,'text(kanban9)','text(kanban10)','kanban_ru.xml',NULL,1,0);
/*!40000 ALTER TABLE `pm_ProjectTemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ProjectUse`
--

DROP TABLE IF EXISTS `pm_ProjectUse`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ProjectUse` (
  `pm_ProjectUseId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Project` int(11) default NULL,
  `Participant` int(11) default NULL,
  `SessionHash` varchar(36) default NULL,
  `PrevLoginDate` datetime default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ProjectUseId`),
  UNIQUE KEY `XPKpm_ProjectUse` (`pm_ProjectUseId`),
  KEY `Participant` (`Participant`,`SessionHash`,`VPD`),
  KEY `SessionHash` (`SessionHash`,`VPD`),
  KEY `i$37` (`SessionHash`,`RecordModified`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ProjectUse`
--

LOCK TABLES `pm_ProjectUse` WRITE;
/*!40000 ALTER TABLE `pm_ProjectUse` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ProjectUse` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_PublicInfo`
--

DROP TABLE IF EXISTS `pm_PublicInfo`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_PublicInfo` (
  `pm_PublicInfoId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Project` int(11) default NULL,
  `IsProjectInfo` char(1) default NULL,
  `IsParticipants` char(1) default NULL,
  `IsBlog` char(1) default NULL,
  `IsKnowledgeBase` char(1) default NULL,
  `IsReleases` char(1) default NULL,
  `IsChangeRequests` char(1) default NULL,
  `IsPublicDocumentation` char(1) default NULL,
  `IsPublicArtefacts` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_PublicInfoId`),
  KEY `i$2` (`Project`),
  KEY `I$49` (`Project`),
  KEY `pm_PublicInfo$VPD` (`VPD`),
  KEY `pm_PublicInfo$ProjectVPD` (`Project`,`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_PublicInfo`
--

LOCK TABLES `pm_PublicInfo` WRITE;
/*!40000 ALTER TABLE `pm_PublicInfo` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_PublicInfo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Question`
--

DROP TABLE IF EXISTS `pm_Question`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Question` (
  `pm_QuestionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Content` text,
  `Author` int(11) default NULL,
  `State` varchar(32) default NULL,
  `Owner` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_QuestionId`),
  KEY `I$pm_Question$State` (`State`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Question`
--

LOCK TABLES `pm_Question` WRITE;
/*!40000 ALTER TABLE `pm_Question` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Release`
--

DROP TABLE IF EXISTS `pm_Release`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Release` (
  `pm_ReleaseId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `ReleaseNumber` varchar(32) default NULL,
  `Description` text,
  `Project` int(11) default NULL,
  `StartDate` datetime default NULL,
  `FinishDate` datetime default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `IsCurrent` char(1) default NULL,
  `VPD` varchar(32) default NULL,
  `Version` int(11) default NULL,
  `IsDraft` char(1) default NULL,
  `IsActual` char(1) default NULL,
  `ProjectSTage` int(11) default NULL,
  `InitialVelocity` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ReleaseId`),
  UNIQUE KEY `XPKpm_Release` (`pm_ReleaseId`),
  KEY `pm_Release_vpd_idx` (`VPD`),
  KEY `Project` (`Project`,`Version`,`IsCurrent`,`VPD`),
  KEY `Project_2` (`Project`,`VPD`),
  KEY `i$5` (`Version`),
  KEY `i$26` (`Project`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Release`
--

LOCK TABLES `pm_Release` WRITE;
/*!40000 ALTER TABLE `pm_Release` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Release` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ReleaseMetrics`
--

DROP TABLE IF EXISTS `pm_ReleaseMetrics`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ReleaseMetrics` (
  `pm_ReleaseMetricsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Release` int(11) default NULL,
  `SnapshotDate` datetime default NULL,
  `Workload` float default NULL,
  `LeftWorkload` float default NULL,
  `SnapshotDays` int(11) default NULL,
  `PlannedWorkload` float default NULL,
  `RecordVersion` int(11) default '0',
  `TaskType` int(11) default NULL,
  PRIMARY KEY  (`pm_ReleaseMetricsId`),
  KEY `i$21` (`Release`,`SnapshotDays`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ReleaseMetrics`
--

LOCK TABLES `pm_ReleaseMetrics` WRITE;
/*!40000 ALTER TABLE `pm_ReleaseMetrics` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ReleaseMetrics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_ReleaseNote`
--

DROP TABLE IF EXISTS `pm_ReleaseNote`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_ReleaseNote` (
  `pm_ReleaseNoteId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Release` int(11) default NULL,
  `ChangeRequest` int(11) default NULL,
  `Content` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ReleaseNoteId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_ReleaseNote`
--

LOCK TABLES `pm_ReleaseNote` WRITE;
/*!40000 ALTER TABLE `pm_ReleaseNote` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_ReleaseNote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_RequestTag`
--

DROP TABLE IF EXISTS `pm_RequestTag`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_RequestTag` (
  `pm_RequestTagId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Request` int(11) default NULL,
  `Tag` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_RequestTagId`),
  KEY `Request` (`Request`,`Tag`,`VPD`),
  KEY `i$15` (`VPD`),
  KEY `i$17` (`VPD`,`Tag`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_RequestTag`
--

LOCK TABLES `pm_RequestTag` WRITE;
/*!40000 ALTER TABLE `pm_RequestTag` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_RequestTag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_RequirementState`
--

DROP TABLE IF EXISTS `pm_RequirementState`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_RequirementState` (
  `pm_RequirementStateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_RequirementStateId`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_RequirementState`
--

LOCK TABLES `pm_RequirementState` WRITE;
/*!40000 ALTER TABLE `pm_RequirementState` DISABLE KEYS */;
INSERT INTO `pm_RequirementState` (`pm_RequirementStateId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `RecordVersion`) VALUES (1,'2010-06-06 18:05:31','2010-06-06 18:05:31','',10,'В работе',0),(2,'2010-06-06 18:05:31','2010-06-06 18:05:31','',20,'Готово',0),(3,'2010-06-06 18:05:31','2010-06-06 18:05:31','',30,'Подписано',0);
/*!40000 ALTER TABLE `pm_RequirementState` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Scrum`
--

DROP TABLE IF EXISTS `pm_Scrum`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Scrum` (
  `pm_ScrumId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `WasYesterday` text,
  `WhatToday` text,
  `CurrentProblems` text,
  `Participant` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_ScrumId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Scrum`
--

LOCK TABLES `pm_Scrum` WRITE;
/*!40000 ALTER TABLE `pm_Scrum` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Scrum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_State`
--

DROP TABLE IF EXISTS `pm_State`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_State` (
  `pm_StateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `ObjectClass` varchar(32) default NULL,
  `IsTerminal` char(1) default NULL,
  `ReferenceName` varchar(32) default NULL,
  `QueueLength` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_StateId`),
  KEY `I$pm_State$VPD` (`VPD`),
  KEY `I$pm_State$Class` (`ObjectClass`),
  KEY `I$pm_State$Reference` (`ReferenceName`),
  KEY `I$pm_State$ObjectClass` (`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_State`
--

LOCK TABLES `pm_State` WRITE;
/*!40000 ALTER TABLE `pm_State` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_State` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_StateAction`
--

DROP TABLE IF EXISTS `pm_StateAction`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_StateAction` (
  `pm_StateActionId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `ReferenceName` text,
  `State` int(11) default NULL,
  PRIMARY KEY  (`pm_StateActionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_StateAction`
--

LOCK TABLES `pm_StateAction` WRITE;
/*!40000 ALTER TABLE `pm_StateAction` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_StateAction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_StateObject`
--

DROP TABLE IF EXISTS `pm_StateObject`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_StateObject` (
  `pm_StateObjectId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `ObjectId` int(11) default NULL,
  `ObjectClass` varchar(32) default NULL,
  `State` int(11) default NULL,
  `Comment` text,
  `Transition` int(11) default NULL,
  `Author` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  `Duration` float default NULL,
  PRIMARY KEY  (`pm_StateObjectId`),
  KEY `I$pm_StateObject$VPD` (`VPD`),
  KEY `I$pm_StateObject$Object` (`ObjectId`),
  KEY `I$pm_StateObject$Class` (`ObjectClass`),
  KEY `I$pm_StateObject$State` (`State`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_StateObject`
--

LOCK TABLES `pm_StateObject` WRITE;
/*!40000 ALTER TABLE `pm_StateObject` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_StateObject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Subversion`
--

DROP TABLE IF EXISTS `pm_Subversion`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Subversion` (
  `pm_SubversionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Project` int(11) default NULL,
  `SVNPath` text,
  `LoginName` text,
  `SVNPassword` text,
  `RootPath` text,
  `ConnectorClass` text,
  `RecordVersion` int(11) default '0',
  `Caption` text,
  PRIMARY KEY  (`pm_SubversionId`),
  KEY `I$pm_Subversion$Project` (`Project`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Subversion`
--

LOCK TABLES `pm_Subversion` WRITE;
/*!40000 ALTER TABLE `pm_Subversion` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Subversion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_SubversionRevision`
--

DROP TABLE IF EXISTS `pm_SubversionRevision`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_SubversionRevision` (
  `pm_SubversionRevisionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Project` int(11) default NULL,
  `Version` varchar(255) default NULL,
  `Description` text,
  `Author` text,
  `CommitDate` text,
  `RecordVersion` int(11) default '0',
  `Repository` int(11) default NULL,
  PRIMARY KEY  (`pm_SubversionRevisionId`),
  KEY `i$36` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_SubversionRevision`
--

LOCK TABLES `pm_SubversionRevision` WRITE;
/*!40000 ALTER TABLE `pm_SubversionRevision` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_SubversionRevision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Task`
--

DROP TABLE IF EXISTS `pm_Task`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Task` (
  `pm_TaskId` int(11) NOT NULL auto_increment,
  `OrderNum` int(11) default NULL,
  `Release` int(11) default NULL,
  `Comments` text,
  `Assignee` int(11) default NULL,
  `Planned` float default NULL,
  `Fact` float default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `TaskType` int(11) default NULL,
  `Priority` int(11) default NULL,
  `Result` text,
  `Controller` int(11) default NULL,
  `ChangeRequest` int(11) default NULL,
  `VPD` varchar(32) default NULL,
  `Caption` text,
  `PrecedingTask` int(11) default NULL,
  `LeftWork` float default NULL,
  `State` varchar(32) default NULL,
  `StartDate` date default NULL,
  `FinishDate` date default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TaskId`),
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
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Task`
--

LOCK TABLES `pm_Task` WRITE;
/*!40000 ALTER TABLE `pm_Task` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TaskState`
--

DROP TABLE IF EXISTS `pm_TaskState`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TaskState` (
  `pm_TaskStateId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TaskStateId`),
  UNIQUE KEY `XPKpm_TaskState` (`pm_TaskStateId`),
  KEY `pm_TaskState_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TaskState`
--

LOCK TABLES `pm_TaskState` WRITE;
/*!40000 ALTER TABLE `pm_TaskState` DISABLE KEYS */;
INSERT INTO `pm_TaskState` (`pm_TaskStateId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `RecordVersion`) VALUES (1,'2005-12-25 00:23:59','2005-12-25 00:23:59',10,'Назначена',NULL,0),(2,'2005-12-25 00:24:13','2005-12-25 00:24:13',20,'Открыта',NULL,0),(3,'2005-12-25 00:24:28','2005-12-25 00:24:28',30,'Выполнена',NULL,0),(4,'2005-12-27 22:24:12','2005-12-27 22:24:12',40,'На проверке',NULL,0),(5,'2005-12-27 22:24:21','2005-12-27 22:24:21',50,'Проверено',NULL,0);
/*!40000 ALTER TABLE `pm_TaskState` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TaskTrace`
--

DROP TABLE IF EXISTS `pm_TaskTrace`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TaskTrace` (
  `pm_TaskTraceId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Task` int(11) default NULL,
  `ObjectId` int(11) default NULL,
  `ObjectClass` varchar(255) default NULL,
  `IsActual` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TaskTraceId`),
  KEY `I$TaskTrace$Task` (`Task`),
  KEY `I$TaskTrace$Object` (`ObjectId`,`ObjectClass`),
  KEY `I$pm_TaskTrace$Task` (`Task`),
  KEY `I$pm_TaskTrace$Object` (`ObjectId`,`ObjectClass`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TaskTrace`
--

LOCK TABLES `pm_TaskTrace` WRITE;
/*!40000 ALTER TABLE `pm_TaskTrace` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TaskTrace` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TaskType`
--

DROP TABLE IF EXISTS `pm_TaskType`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TaskType` (
  `pm_TaskTypeId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `VPD` varchar(32) default NULL,
  `ReferenceName` varchar(32) default NULL,
  `ProjectRole` int(11) default NULL,
  `ParentTaskType` int(11) default NULL,
  `UsedInPlanning` char(1) default NULL,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TaskTypeId`),
  UNIQUE KEY `XPKpm_TaskType` (`pm_TaskTypeId`),
  KEY `pm_TaskType_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TaskType`
--

LOCK TABLES `pm_TaskType` WRITE;
/*!40000 ALTER TABLE `pm_TaskType` DISABLE KEYS */;
INSERT INTO `pm_TaskType` (`pm_TaskTypeId`, `RecordCreated`, `RecordModified`, `OrderNum`, `Caption`, `VPD`, `ReferenceName`, `ProjectRole`, `ParentTaskType`, `UsedInPlanning`, `Description`, `RecordVersion`) VALUES (10,'2010-10-01 17:16:30','2010-10-01 17:16:30',55,'Управление проектом',NULL,'management',4,NULL,'N',NULL,0),(2,'2005-12-24 21:50:33','2005-12-24 21:50:33',20,'Разработка',NULL,'development',2,NULL,'Y',NULL,0),(3,'2005-12-27 23:09:09','2005-12-27 23:09:09',30,'Тестирование',NULL,'testing',3,NULL,'Y',NULL,0),(4,'2005-12-28 09:41:39','2010-06-06 18:05:07',5,'Анализ',NULL,'analysis',1,NULL,'Y',NULL,0),(5,'2006-01-18 15:34:31','2006-01-18 15:34:31',50,'Документирование',NULL,'documenting',7,NULL,'Y',NULL,0),(6,'2006-02-19 15:53:21','2006-02-19 15:53:21',60,'Развертывание',NULL,'deployment',2,NULL,'N',NULL,0),(7,'2006-03-08 09:22:57','2006-03-08 09:22:57',70,'Приемка',NULL,'accepting',5,NULL,'N',NULL,0),(8,'2010-06-06 18:05:07','2010-06-06 18:05:07',7,'Проектирование',NULL,'design',8,NULL,'Y',NULL,0),(9,'2010-06-06 18:05:07','2010-06-06 18:05:07',80,'Другое',NULL,'other',2,NULL,'N',NULL,0),(11,NULL,NULL,25,'Дизайн тестов',NULL,'testdesign',3,NULL,'Y',NULL,0);
/*!40000 ALTER TABLE `pm_TaskType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TaskTypeStage`
--

DROP TABLE IF EXISTS `pm_TaskTypeStage`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TaskTypeStage` (
  `pm_TaskTypeStageId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `TaskType` int(11) default NULL,
  `ProjectStage` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TaskTypeStageId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TaskTypeStage`
--

LOCK TABLES `pm_TaskTypeStage` WRITE;
/*!40000 ALTER TABLE `pm_TaskTypeStage` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TaskTypeStage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Test`
--

DROP TABLE IF EXISTS `pm_Test`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Test` (
  `pm_TestId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `TestScenario` int(11) default NULL,
  `Environment` int(11) default NULL,
  `Version` varchar(32) default NULL,
  `Result` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TestId`),
  KEY `I$pm_Test$Environment` (`Environment`),
  KEY `I$pm_Test$Version` (`Version`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Test`
--

LOCK TABLES `pm_Test` WRITE;
/*!40000 ALTER TABLE `pm_Test` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TestCaseExecution`
--

DROP TABLE IF EXISTS `pm_TestCaseExecution`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TestCaseExecution` (
  `pm_TestCaseExecutionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Test` int(11) default NULL,
  `TestCase` int(11) default NULL,
  `Success` char(1) default NULL,
  `Tester` int(11) default NULL,
  `Result` int(11) default NULL,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TestCaseExecutionId`),
  KEY `I$pm_TestCaseExecution$Test` (`Test`),
  KEY `I$pm_TestCaseExecution$RecordModified` (`RecordModified`),
  KEY `I$pm_TestCaseExecution$TestCase` (`TestCase`),
  KEY `I$pm_TestCaseExecution$VPD` (`VPD`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TestCaseExecution`
--

LOCK TABLES `pm_TestCaseExecution` WRITE;
/*!40000 ALTER TABLE `pm_TestCaseExecution` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TestCaseExecution` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TestExecutionResult`
--

DROP TABLE IF EXISTS `pm_TestExecutionResult`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TestExecutionResult` (
  `pm_TestExecutionResultId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `ReferenceName` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TestExecutionResultId`),
  KEY `I$pm_TestExecutionResult$VPD` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TestExecutionResult`
--

LOCK TABLES `pm_TestExecutionResult` WRITE;
/*!40000 ALTER TABLE `pm_TestExecutionResult` DISABLE KEYS */;
INSERT INTO `pm_TestExecutionResult` (`pm_TestExecutionResultId`, `RecordCreated`, `RecordModified`, `VPD`, `OrderNum`, `Caption`, `ReferenceName`, `RecordVersion`) VALUES (1,'2010-10-03 19:15:16','2010-10-03 19:15:16',NULL,10,'Пройден','succeeded',0),(2,'2010-10-03 19:15:24','2010-10-03 19:15:24',NULL,20,'Провален','failed',0),(3,'2010-10-03 19:15:32','2010-10-03 19:15:32',NULL,30,'Заблокирован','blocked',0);
/*!40000 ALTER TABLE `pm_TestExecutionResult` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TestPlan`
--

DROP TABLE IF EXISTS `pm_TestPlan`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TestPlan` (
  `pm_TestPlanId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TestPlanId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TestPlan`
--

LOCK TABLES `pm_TestPlan` WRITE;
/*!40000 ALTER TABLE `pm_TestPlan` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TestPlan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TestPlanItem`
--

DROP TABLE IF EXISTS `pm_TestPlanItem`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TestPlanItem` (
  `pm_TestPlanItemId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `TestSuite` int(11) default NULL,
  `Assignee` int(11) default NULL,
  `Planned` float default NULL,
  `TestPlan` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TestPlanItemId`),
  KEY `I$pm_TestPlanItem$TestPlan` (`TestPlan`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TestPlanItem`
--

LOCK TABLES `pm_TestPlanItem` WRITE;
/*!40000 ALTER TABLE `pm_TestPlanItem` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TestPlanItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Transition`
--

DROP TABLE IF EXISTS `pm_Transition`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Transition` (
  `pm_TransitionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Description` text,
  `SourceState` int(11) default NULL,
  `TargetState` int(11) default NULL,
  `IsReasonRequired` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TransitionId`),
  KEY `I$pm_Transition$VPD` (`VPD`),
  KEY `I$pm_Transition$Source` (`SourceState`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Transition`
--

LOCK TABLES `pm_Transition` WRITE;
/*!40000 ALTER TABLE `pm_Transition` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Transition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TransitionAttribute`
--

DROP TABLE IF EXISTS `pm_TransitionAttribute`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TransitionAttribute` (
  `pm_TransitionAttributeId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Transition` int(11) default NULL,
  `ReferenceName` text,
  `Entity` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TransitionAttributeId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TransitionAttribute`
--

LOCK TABLES `pm_TransitionAttribute` WRITE;
/*!40000 ALTER TABLE `pm_TransitionAttribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TransitionAttribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TransitionPredicate`
--

DROP TABLE IF EXISTS `pm_TransitionPredicate`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TransitionPredicate` (
  `pm_TransitionPredicateId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Transition` int(11) default NULL,
  `Predicate` int(11) default NULL,
  PRIMARY KEY  (`pm_TransitionPredicateId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TransitionPredicate`
--

LOCK TABLES `pm_TransitionPredicate` WRITE;
/*!40000 ALTER TABLE `pm_TransitionPredicate` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TransitionPredicate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TransitionResetField`
--

DROP TABLE IF EXISTS `pm_TransitionResetField`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TransitionResetField` (
  `pm_TransitionResetFieldId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Transition` int(11) default NULL,
  `ReferenceName` text,
  `Entity` text,
  PRIMARY KEY  (`pm_TransitionResetFieldId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TransitionResetField`
--

LOCK TABLES `pm_TransitionResetField` WRITE;
/*!40000 ALTER TABLE `pm_TransitionResetField` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TransitionResetField` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_TransitionRole`
--

DROP TABLE IF EXISTS `pm_TransitionRole`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_TransitionRole` (
  `pm_TransitionRoleId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Transition` int(11) default NULL,
  `ProjectRole` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_TransitionRoleId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_TransitionRole`
--

LOCK TABLES `pm_TransitionRole` WRITE;
/*!40000 ALTER TABLE `pm_TransitionRole` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_TransitionRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_UserMail`
--

DROP TABLE IF EXISTS `pm_UserMail`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_UserMail` (
  `pm_UserMailId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ToParticipant` int(11) default NULL,
  `Subject` text,
  `Content` text,
  `FromParticipant` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_UserMailId`),
  KEY `ToParticipant` (`ToParticipant`),
  KEY `FromParticipant` (`FromParticipant`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_UserMail`
--

LOCK TABLES `pm_UserMail` WRITE;
/*!40000 ALTER TABLE `pm_UserMail` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_UserMail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_UserSetting`
--

DROP TABLE IF EXISTS `pm_UserSetting`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_UserSetting` (
  `pm_UserSettingId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Setting` varchar(32) default NULL,
  `Value` text,
  `Participant` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_UserSettingId`),
  KEY `I$pm_UserSetting$Participant` (`Participant`),
  KEY `I$pm_UserSetting$Setting` (`Setting`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_UserSetting`
--

LOCK TABLES `pm_UserSetting` WRITE;
/*!40000 ALTER TABLE `pm_UserSetting` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_UserSetting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Vacancy`
--

DROP TABLE IF EXISTS `pm_Vacancy`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Vacancy` (
  `pm_VacancyId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` text,
  `Project` int(11) default NULL,
  `IsActive` char(1) default NULL,
  `RequiredWorkload` int(11) default NULL,
  `PriceOfHour` text,
  `Description` text,
  `Requirements` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_VacancyId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Vacancy`
--

LOCK TABLES `pm_Vacancy` WRITE;
/*!40000 ALTER TABLE `pm_Vacancy` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Vacancy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Version`
--

DROP TABLE IF EXISTS `pm_Version`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Version` (
  `pm_VersionId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `Caption` varchar(32) default NULL,
  `Description` text,
  `Project` int(11) default NULL,
  `InitialEstimationError` int(11) default NULL,
  `InitialBugsInWorkload` int(11) default NULL,
  `IsActual` char(1) default NULL,
  `StartDate` date default NULL,
  `FinishDate` date default NULL,
  `InitialVelocity` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_VersionId`),
  KEY `i$9` (`Project`,`VPD`),
  KEY `i$12` (`Project`,`Caption`),
  KEY `i$22` (`VPD`,`Caption`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Version`
--

LOCK TABLES `pm_Version` WRITE;
/*!40000 ALTER TABLE `pm_Version` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_VersionBurndown`
--

DROP TABLE IF EXISTS `pm_VersionBurndown`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_VersionBurndown` (
  `pm_VersionBurndownId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Version` int(11) default NULL,
  `SnapshotDate` datetime default NULL,
  `Workload` float default NULL,
  `SnapshotDays` int(11) default NULL,
  `PlannedWorkload` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_VersionBurndownId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_VersionBurndown`
--

LOCK TABLES `pm_VersionBurndown` WRITE;
/*!40000 ALTER TABLE `pm_VersionBurndown` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_VersionBurndown` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_VersionMetric`
--

DROP TABLE IF EXISTS `pm_VersionMetric`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_VersionMetric` (
  `pm_VersionMetricId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Version` int(11) default NULL,
  `Metric` varchar(32) default NULL,
  `MetricValue` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_VersionMetricId`),
  KEY `i$10` (`Version`),
  KEY `i$11` (`Version`,`Metric`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_VersionMetric`
--

LOCK TABLES `pm_VersionMetric` WRITE;
/*!40000 ALTER TABLE `pm_VersionMetric` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_VersionMetric` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_VersionSettings`
--

DROP TABLE IF EXISTS `pm_VersionSettings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_VersionSettings` (
  `pm_VersionSettingsId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `Project` int(11) default NULL,
  `UseRelease` char(1) default NULL,
  `UseIteration` char(1) default NULL,
  `UseBuild` char(1) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_VersionSettingsId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_VersionSettings`
--

LOCK TABLES `pm_VersionSettings` WRITE;
/*!40000 ALTER TABLE `pm_VersionSettings` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_VersionSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pm_Watcher`
--

DROP TABLE IF EXISTS `pm_Watcher`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `pm_Watcher` (
  `pm_WatcherId` int(11) NOT NULL auto_increment,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `ObjectId` int(11) default NULL,
  `ObjectClass` text,
  `SystemUser` int(11) default NULL,
  `Email` text,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_WatcherId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `pm_Watcher`
--

LOCK TABLES `pm_Watcher` WRITE;
/*!40000 ALTER TABLE `pm_Watcher` DISABLE KEYS */;
/*!40000 ALTER TABLE `pm_Watcher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `settings` (
  `settingsId` int(11) NOT NULL auto_increment,
  `FontSize` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `VPD` varchar(32) default NULL,
  PRIMARY KEY  (`settingsId`),
  UNIQUE KEY `XPKsettings` (`settingsId`),
  KEY `settings_vpd_idx` (`VPD`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sm_Action`
--

DROP TABLE IF EXISTS `sm_Action`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sm_Action` (
  `sm_ActionId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `Activity` int(11) default NULL,
  `State` text,
  `Estimation` float default NULL,
  `Kind` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`sm_ActionId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sm_Action`
--

LOCK TABLES `sm_Action` WRITE;
/*!40000 ALTER TABLE `sm_Action` DISABLE KEYS */;
/*!40000 ALTER TABLE `sm_Action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sm_Activity`
--

DROP TABLE IF EXISTS `sm_Activity`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sm_Activity` (
  `sm_ActivityId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `Aim` int(11) default NULL,
  `State` text,
  `Estimation` float default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`sm_ActivityId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sm_Activity`
--

LOCK TABLES `sm_Activity` WRITE;
/*!40000 ALTER TABLE `sm_Activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `sm_Activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sm_Aim`
--

DROP TABLE IF EXISTS `sm_Aim`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sm_Aim` (
  `sm_AimId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `State` text,
  `Person` int(11) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`sm_AimId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `sm_Aim`
--

LOCK TABLES `sm_Aim` WRITE;
/*!40000 ALTER TABLE `sm_Aim` DISABLE KEYS */;
/*!40000 ALTER TABLE `sm_Aim` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sm_Person`
--

DROP TABLE IF EXISTS `sm_Person`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `sm_Person` (
  `sm_PersonId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `Caption` text,
  `State` text,
  `Description` text,
  `Valuable` text,
  `Problems` text,
  `PhotoMime` text,
  `PhotoPath` text,
  `PhotoExt` varchar(32) default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`sm_PersonId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
SET character_set_client = @saved_cs_client;

DELETE FROM cms_Update;
INSERT INTO cms_Update (Caption) VALUES ('3.0');

UPDATE attribute SET IsRequired = 'N' WHERE ReferenceName IN ('Capacity', 'Caption', 'Email', 'Login') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Participant');

create index I$WikiPage$ParentPage on WikiPage (ParentPage);

create index I$WikiTag$Wiki on WikiTag (Wiki);

create index I$pm_Watcher$ObjectId on pm_Watcher (ObjectId);

create index I$pm_AttributeValue$ObjectIdAttribute on pm_AttributeValue (ObjectId, CustomAttribute);

create index I$pm_AttributeValue$ObjectId on pm_AttributeValue (ObjectId);

create index I$pm_AttributeValue$Attribute on pm_AttributeValue (CustomAttribute);

UPDATE attribute SET IsRequired = 'N' WHERE ReferenceName IN ('Importance') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Function');

UPDATE entity SET ReferenceName = 'pm_ProjectStage' WHERE ReferenceName = 'pm_ProjectSTage';


UPDATE attribute SET IsVisible = 'N' WHERE ReferenceName IN ('IsTests', 'IsRequirements', 'IsHelps') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Methodology');

UPDATE attribute SET IsVisible = 'N' WHERE ReferenceName IN ('Requirements', 'Testing', 'HelpFiles', 'SourceCode') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_ProjectLink');

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )  VALUES ( NOW(), NOW(), NULL,'Редакция','ProductEdition','TEXT',NULL,'N','N',325,50 ) ;

ALTER TABLE pm_ProjectTemplate ADD ProductEdition TEXT;

UPDATE pm_ProjectTemplate SET ProductEdition = 'ee' WHERE FileName IN ('sdlc_ru.xml','msfagile_en.xml','sdlc_en.xml','openup_en.xml');

UPDATE pm_ProjectTemplate SET ProductEdition = 'team' WHERE ProductEdition IS NULL;

UPDATE pm_Project SET WikiEditorClass = 'WikiRtfCKEditor' WHERE LCASE(WikiEditorClass) = 'wikirtfeditor';

UPDATE WikiPage SET ContentEditor = 'WikiRtfCKEditor' WHERE LCASE(ContentEditor) = 'wikirtfeditor';

UPDATE BlogPost SET ContentEditor = 'WikiRtfCKEditor' WHERE LCASE(ContentEditor) = 'wikirtfeditor';

INSERT INTO entity ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`packageId`,`IsOrdered`,`IsDictionary`,`OrderNum` )  VALUES ( NOW(), NOW(), NULL,'Трассировка функции','pm_FunctionTrace',2,'N','N',2090 ) ;

CREATE TABLE pm_FunctionTrace (pm_FunctionTraceId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, VPD VARCHAR(32), RecordVersion INTEGER DEFAULT 0,RecordCreated DATETIME,RecordModified DATETIME ) ENGINE=MyISAM DEFAULT CHARSET=cp1251 ;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )  VALUES ( NOW(), NOW(), NULL,'Функция','Feature','REF_pm_FunctionId',NULL,'N','N',362,10 ) ;

ALTER TABLE pm_FunctionTrace ADD Feature INTEGER;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )  VALUES ( NOW(), NOW(), NULL,'Ид объекта','ObjectId','INTEGER',NULL,'Y','Y',362,20 ) ;

ALTER TABLE pm_FunctionTrace ADD ObjectId INTEGER;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )  VALUES ( NOW(), NOW(), NULL,'Класс объекта','ObjectClass','TEXT',NULL,'Y','Y',362,30 ) ;

ALTER TABLE pm_FunctionTrace ADD ObjectClass TEXT;

UPDATE pm_CustomAttribute SET OrderNum = 15 WHERE ReferenceName = 'Description' AND EntityReferenceName = 'request';


ALTER TABLE WikiPage MODIFY UserField3 MEDIUMTEXT;

UPDATE attribute SET AttributeType = 'TEXT' WHERE ReferenceName = 'UserField3' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'WikiPage');

-- 2.9.11.7

ALTER TABLE pm_ChangeRequest ADD COLUMN StateObject INTEGER;

ALTER TABLE pm_Question ADD COLUMN StateObject INTEGER;

ALTER TABLE WikiPage ADD COLUMN StateObject INTEGER;

ALTER TABLE pm_Task ADD COLUMN StateObject INTEGER;

ALTER TABLE sm_Person ADD COLUMN StateObject INTEGER;

ALTER TABLE sm_Aim ADD COLUMN StateObject INTEGER;

ALTER TABLE sm_Activity ADD COLUMN StateObject INTEGER;

ALTER TABLE sm_Action ADD COLUMN StateObject INTEGER;

update cms_SystemSettings set DisplayFeedbackForm = 'N';

alter table ObjectChangeLog modify EntityRefName varchar(128);

create index I$ObjectChangeLog$EntityRefName on ObjectChangeLog (EntityRefName);

create index I$ObjectChangeLog$ERN_VPD on ObjectChangeLog (VPD,EntityRefName);

alter table ObjectChangeLog modify EntityName varchar(128);

create index I$ObjectChangeLog$EntityName on ObjectChangeLog (EntityName);

create index I$ObjectChangeLog$EN_VPD on ObjectChangeLog (VPD,EntityName);

create index I$ObjectChangeLog$VPD on ObjectChangeLog (VPD);

create index I$ObjectChangeLog$OrderNum on ObjectChangeLog (OrderNum);

alter table ObjectChangeLog drop index i$32;


INSERT INTO package (Caption) VALUES ('Проект');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Проект') WHERE ReferenceName IN ('pm_Participant', 'pm_ParticipantRole', 'pm_Project', 'pm_ProjectRole', 'pm_UserMail', 'pm_Question', 'cms_Report', 'cms_ReportCategory');

INSERT INTO package (Caption) VALUES ('План работ');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'План работ') WHERE ReferenceName IN ('pm_Version', 'pm_Release', 'pm_Task', 'pm_Activity', 'pm_TaskType', 'pm_Milestone', 'pm_TaskTrace');

INSERT INTO package (Caption) VALUES ('Настройки проекта');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Настройки проекта') WHERE ReferenceName IN ('pm_Methodology', 'TemplateHTML2', 'pm_VersionSettings', 'pm_ProjectLink', 'pm_ObjectAccess', 'pm_AccessRight', 'pm_ProjectStage', 'pm_TaskTypeStage', 'pm_CustomReport', 'pm_UserSetting', 'pm_CustomAttribute', 'pm_AttributeValue');

INSERT INTO package (Caption) VALUES ('Настройки Workflow');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Настройки Workflow') WHERE ReferenceName IN ('pm_State', 'pm_Transition', 'pm_StateObject', 'pm_TransitionRole', 'pm_TransitionAttribute', 'pm_Predicate', 'pm_TransitionPredicate', 'pm_TransitionResetField', 'pm_StateAction');

INSERT INTO package (Caption) VALUES ('Продукт');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Продукт') WHERE ReferenceName IN ('pm_Function', 'pm_ChangeRequest', 'pm_ChangeRequestLink', 'pm_ChangeRequestLinkType', 'pm_IssueType', 'pm_Deadline', 'pm_Watcher', 'pm_ChangeRequestTrace', 'pm_Importance', 'pm_Competitor', 'pm_FeatureAnalysis', 'pm_FunctionTrace');

INSERT INTO package (Caption) VALUES ('Документация');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Документация') WHERE ReferenceName IN ('WikiPage', 'pm_Watcher', 'WikiPageFile', 'WikiPageChange', 'WikiTag', 'WikiPageType','WikiPageTrace', 'Blog', 'BlogPost', 'BlogPostFile', 'BlogPostTag');

INSERT INTO package (Caption) VALUES ('Интеграция и развертывание');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Интеграция и развертывание') WHERE ReferenceName IN ('pm_Subversion','pm_SubversionRevision','pm_Artefact', 'pm_ArtefactType', 'pm_Build');

INSERT INTO package (Caption) VALUES ('Справочники');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Справочники') WHERE ReferenceName IN ('Priority', 'pm_ProjectTemplate', 'ObjectChangeLog');

INSERT INTO package (Caption) VALUES ('Тестирование');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Тестирование') WHERE ReferenceName IN ('pm_TestPlan', 'pm_Test', 'pm_TestCaseExecution', 'pm_Environment', 'pm_TestPlanItem', 'pm_TestExecutionResult');

INSERT INTO package (Caption) VALUES ('StoryMapping');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'StoryMapping') WHERE ReferenceName IN ('sm_Aim', 'sm_Activity', 'sm_Action', 'sm_Person');

INSERT INTO package (Caption) VALUES ('Расширения');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Расширения') WHERE ReferenceName IN ('pm_Attachment', 'Tag', 'pm_CustomTag', 'Comment', 'cms_Snapshot', 'cms_SnapshotItem', 'cms_SnapshotItemValue', 'cms_IdsHash');

INSERT INTO package (Caption) VALUES ('Система');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Система') WHERE ReferenceName IN ('co_ScheduledJob', 'co_JobRun', 'co_MailboxProvider', 'co_MailTransport', 'co_AccessRight', 'co_ProjectGroup', 'co_ProjectGroupLink', 'co_UserGroup', 'co_UserGroupLink', 'co_CustomReport', 'EmailQueue', 'EmailQueueAddress', 'cms_Language', 'cms_Backup', 'cms_Update', 'cms_SystemSettings', 'cms_User', 'cms_License', 'cms_BlackList', 'cms_BatchJob', 'cms_TempFile', 'cms_Resource', 'cms_Checkpoint');

INSERT INTO package (Caption) VALUES ('Метрики');

UPDATE entity SET packageId = (SELECT packageId FROM package WHERE Caption = 'Метрики') WHERE ReferenceName IN ('pm_ReleaseMetrics', 'pm_ParticipantMetrics','pm_IterationMetric','pm_VersionMetric', 'pm_VersionBurndown');


update attribute set OrderNum = 218 where ReferenceName = 'RequestEstimationRequired';


ALTER TABLE pm_Release CHANGE COLUMN ProjectSTage ProjectStage INTEGER;

UPDATE attribute SET IsVisible = 'N' WHERE ReferenceName = 'IsArtefactsUsed' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Project');

alter table ObjectChangeLog modify EntityRefName varchar(128);

alter table ObjectChangeLog modify EntityName varchar(128);

ALTER TABLE pm_SubversionRevision ADD COLUMN VersionNum INT(11) UNSIGNED NULL AFTER Version;

INSERT INTO `attribute` (`attributeId`, `Caption`, `ReferenceName`, `AttributeType`, `DefaultValue`, `IsRequired`, `IsVisible`, `entityId`, `OrderNum`, `RecordCreated`, `RecordModified`, `VPD`, `RecordVersion`) VALUES (1539, 'Версия', 'VersionNum', 'INTEGER', '0', 'Y', 'Y', 113, 21, NULL, NULL, NULL, 0);

DELETE FROM attribute WHERE ReferenceName = 'DisplayFeedbackForm';

DELETE FROM attribute WHERE ReferenceName = 'IsBuilds';

UPDATE entity SET IsDictionary = 'N' WHERE ReferenceName = 'pm_Release';

UPDATE pm_AccessRight SET ReferenceName = 'mytasks', ReferenceType = 'PMReport' WHERE ReferenceName = 'Tasks' AND ReferenceType = 'M';

UPDATE pm_AccessRight SET ReferenceName = 'project-settings', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Main' AND ReferenceType = 'M';

UPDATE pm_AccessRight SET ReferenceName = 'project-settings', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Participants' AND ReferenceType = 'M';

UPDATE pm_AccessRight SET ReferenceName = 'project-plan-milestone', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Project' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'project-plan-hierarchy', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'project-plan-milestone' AND ReferenceType = 'PMPluginModule';

UPDATE pm_AccessRight SET ReferenceName = 'tasks-list', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Planning' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'tasks-chart', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'tasks-list' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'tasks-board', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'tasks-list' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'tasks-trace', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'tasks-list' AND ReferenceType = 'PMPluginModule';

UPDATE pm_AccessRight SET ReferenceName = 'operations/folders', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Artefacts' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'operations/builds', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'operations/folders' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'operations/files', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'operations/folders' AND ReferenceType = 'PMPluginModule';

UPDATE pm_AccessRight SET ReferenceName = 'issues-list', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Requests' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'issues-backlog', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'issues-list' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'issues-chart', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'issues-list' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'issues-board', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'issues-list' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'issues-trace', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'issues-list' AND ReferenceType = 'PMPluginModule';

UPDATE pm_AccessRight SET ReferenceName = 'features-chart', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Feature' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'features-trace', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'features-chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'features-list', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'features-chart' AND ReferenceType = 'PMPluginModule';

UPDATE pm_AccessRight SET ReferenceName = 'requirements/chart', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Requirements' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/docs', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/import', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/history', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/matrix', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/list', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/trace', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/files', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'requirements/templates', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'requirements/chart' AND ReferenceType = 'PMPluginModule';

UPDATE pm_AccessRight SET ReferenceName = 'helpdocs/chart', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Help' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'helpdocs/docs', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'helpdocs/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'helpdocs/history', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'helpdocs/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'helpdocs/list', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'helpdocs/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'helpdocs/trace', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'helpdocs/chart' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'helpdocs/files', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'helpdocs/chart' AND ReferenceType = 'PMPluginModule';

UPDATE pm_AccessRight SET ReferenceName = 'testing/library', ReferenceType = 'PMPluginModule' WHERE ReferenceName = 'Testing' AND ReferenceType = 'M';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/chart', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/import', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/history', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/plan', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/results', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/checklist', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/list', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/trace', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/files', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';

INSERT INTO pm_AccessRight ( VPD, ProjectRole, ReferenceName, ReferenceType, AccessType ) 
SELECT VPD, ProjectRole, 'testing/templates', ReferenceType, AccessType FROM pm_AccessRight WHERE ReferenceName = 'testing/library' AND ReferenceType = 'PMPluginModule';






UPDATE attribute SET IsRequired = 'Y' WHERE ReferenceName = 'StartDate' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Version');

UPDATE pm_Project SET RequirementsWikiPage = NULL;

DELETE FROM attribute WHERE ReferenceName = 'Version' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Project');

DROP TABLE IF EXISTS `cms_ExternalUser`;

CREATE TABLE IF NOT EXISTS `cms_ExternalUser` (
  `username` varchar(255) NOT NULL,
  `username_canonical` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_canonical` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `last_login` datetime default NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime default NULL,
  `confirmation_token` varchar(255) default NULL,
  `password_requested_at` datetime default NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime default NULL,
  `cms_ExternalUserId` int(11) NOT NULL auto_increment,
  PRIMARY KEY  (`cms_ExternalUserId`),
  UNIQUE KEY `UNIQ_59F2E2C792FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_59F2E2C7A0D96FBF` (`email_canonical`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

ALTER TABLE pm_Build MODIFY Caption varchar(32);

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName = 'Caption' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Build');

UPDATE attribute SET AttributeType = 'REF_TestScenarioId' WHERE ReferenceName = 'TestScenario' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Test');

UPDATE attribute SET IsRequired = 'Y' WHERE ReferenceName = 'ReferenceName' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'WikiPage');




INSERT INTO package (Caption) SELECT 'Интерфейс пользователя' FROM package WHERE NOT EXISTS (SELECT 1 FROM package WHERE Caption = 'Интерфейс пользователя') LIMIT 1;

INSERT INTO entity ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`packageId`,`IsOrdered`,`IsDictionary`,`OrderNum` )  
VALUES ( NOW(), NOW(), NULL,'Функциональная область','pm_Workspace',19,'Y','Y',2090 ) ;

CREATE TABLE pm_Workspace (
pm_WorkspaceId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, 
VPD VARCHAR(32),
OrderNum INTEGER DEFAULT 0,
RecordVersion INTEGER DEFAULT 0,
RecordCreated DATETIME,
RecordModified DATETIME ) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'UID','UID','TEXT',NULL,'N','N',363,10 ) ;

ALTER TABLE pm_Workspace ADD UID VARCHAR(128);

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Название','Caption','TEXT',NULL,'N','N',363,10 ) ;

ALTER TABLE pm_Workspace ADD Caption TEXT;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Пользователь','SystemUser','REF_cms_UserId',NULL,'N','N',363,10 ) ;

ALTER TABLE pm_Workspace ADD SystemUser INTEGER;


INSERT INTO entity ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`packageId`,`IsOrdered`,`IsDictionary`,`OrderNum` )  
VALUES ( NOW(), NOW(), NULL,'Меню','pm_WorkspaceMenu',19,'Y','Y',2090 ) ;

CREATE TABLE pm_WorkspaceMenu (
pm_WorkspaceMenuId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, 
VPD VARCHAR(32),
OrderNum INTEGER DEFAULT 0,
RecordVersion INTEGER DEFAULT 0,
RecordCreated DATETIME,
RecordModified DATETIME ) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'UID','UID','TEXT',NULL,'N','N',364,10 ) ;

ALTER TABLE pm_WorkspaceMenu ADD UID VARCHAR(128);

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Название','Caption','TEXT',NULL,'N','N',364,10 ) ;

ALTER TABLE pm_WorkspaceMenu ADD Caption TEXT;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Функциональная область','Workspace','REF_pm_WorkspaceId',NULL,'N','N',364,10 ) ;

ALTER TABLE pm_WorkspaceMenu ADD Workspace INTEGER;



INSERT INTO entity ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`packageId`,`IsOrdered`,`IsDictionary`,`OrderNum` )  
VALUES ( NOW(), NOW(), NULL,'Пункт меню','pm_WorkspaceMenuItem',19,'Y','Y',2090 ) ;

CREATE TABLE pm_WorkspaceMenuItem (
pm_WorkspaceMenuItemId INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY, 
VPD VARCHAR(32),
OrderNum INTEGER DEFAULT 0,
RecordVersion INTEGER DEFAULT 0,
RecordCreated DATETIME,
RecordModified DATETIME ) ENGINE=MyISAM DEFAULT CHARSET=cp1251;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'UID','UID','TEXT',NULL,'N','N',365,10 ) ;

ALTER TABLE pm_WorkspaceMenuItem ADD UID VARCHAR(128);

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Название','Caption','TEXT',NULL,'N','N',365,10 ) ;

ALTER TABLE pm_WorkspaceMenuItem ADD Caption TEXT;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Отчет','ReportUID','TEXT',NULL,'N','N',365,10 ) ;

ALTER TABLE pm_WorkspaceMenuItem ADD ReportUID VARCHAR(128);

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Модуль','ModuleUID','TEXT',NULL,'N','N',365,10 ) ;

ALTER TABLE pm_WorkspaceMenuItem ADD ModuleUID VARCHAR(128);

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Меню','WorkspaceMenu','REF_pm_WorkspaceMenuId',NULL,'N','N',365,10 ) ;

ALTER TABLE pm_WorkspaceMenuItem ADD WorkspaceMenu INTEGER;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` ) 
VALUES ( NOW(), NOW(), NULL,'Имя класса','ClassName','VARCHAR',NULL,'N','N',(SELECT entityId FROM entity WHERE ReferenceName = 'ObjectChangeLog'),10 ) ;

ALTER TABLE ObjectChangeLog ADD ClassName VARCHAR(128);

CREATE INDEX I$ObjectChangeLog$ObjectClass ON ObjectChangeLog (ObjectId, ClassName);

UPDATE entity SET IsDictionary = 'N' WHERE ReferenceName IN ('pm_Milestone', 'pm_Attachment', 'pm_Function', 'cms_Update', 'cms_Backup', 'Tag', 'pm_Build', 'pm_Version', 'pm_Participant', 'pm_TransitionResetField');

UPDATE attribute SET IsRequired = 'N' WHERE ReferenceName = 'Caption' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'BlogPostFile');


UPDATE pm_ProjectTemplate SET Caption = 'text(scrum2)', OrderNum = 10 WHERE FileName = 'scrum_simple_ru.xml';

UPDATE pm_ProjectTemplate SET Caption = 'text(scrum3)', OrderNum = 20 WHERE FileName = 'scrum_ru.xml';

UPDATE pm_ProjectTemplate SET Caption = 'text(scrum4)', OrderNum = 20 WHERE FileName = 'scrum_en.xml';

UPDATE pm_ProjectTemplate SET OrderNum = 30 WHERE FileName = 'kanban_ru.xml';

UPDATE attribute SET Caption = 'text(ee207)' WHERE ReferenceName = 'Releases' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_ProjectLink');

UPDATE pm_ProjectLink SET Releases = Tasks;


UPDATE attribute SET Caption = 'text(1365)' WHERE ReferenceName = 'TaskEstimationUsed' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Methodology');

UPDATE attribute SET IsRequired = 'Y' WHERE ReferenceName = 'WikiEditor' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'WikiPageType');



UPDATE attribute SET IsVisible = 'N' WHERE ReferenceName = 'IsArtefactsUsed' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Project');

UPDATE pm_TaskTrace SET ObjectClass = 'Task' WHERE ObjectClass = 'PrecedingTask';

alter table pm_TaskTypeStage modify pm_TaskTypeStageId INTEGER auto_increment;

-- 3.0.10.x

update pm_ProjectTemplate set ProductEdition = 'ee' where FileName in ('scrum_en.xml', 'scrum_ru.xml');

UPDATE pm_ChangeRequest t SET t.Description = (SELECT MAX(av.TextValue) FROM pm_CustomAttribute a, pm_AttributeValue av WHERE BINARY a.ReferenceName = 'Description' AND BINARY a.EntityReferenceName = 'request' AND a.VPD = t.VPD AND a.pm_CustomAttributeId = av.CustomAttribute AND av.ObjectId = t.pm_ChangeRequestId AND av.VPD = t.VPD) WHERE EXISTS (SELECT COUNT(1) FROM pm_CustomAttribute a WHERE BINARY a.ReferenceName = 'Description' AND BINARY a.EntityReferenceName = 'request' AND a.VPD = t.VPD);

DELETE FROM pm_AttributeValue WHERE CustomAttribute IN (SELECT a.pm_CustomAttributeId FROM pm_CustomAttribute a WHERE BINARY a.ReferenceName = 'Description' AND BINARY a.EntityReferenceName = 'request');

DELETE FROM pm_CustomAttribute WHERE BINARY ReferenceName = 'Description' AND BINARY EntityReferenceName = 'request';

update pm_Methodology set UseScrums = 'N' where IsKanbanUsed = 'Y';

update pm_Methodology set IsKanbanUsed = 'N' where UseScrums = 'Y';

UPDATE attribute SET Caption = 'text(1374)' WHERE ReferenceName = 'AdminEmail' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'cms_SystemSettings');

UPDATE attribute SET IsRequired = 'N' WHERE ReferenceName = 'Participant' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Activity');

UPDATE attribute SET IsRequired = 'N' WHERE ReferenceName = 'Release' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

