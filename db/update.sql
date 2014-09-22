SET NAMES 'cp1251';
SET character_set_server=cp1251;
SET character_set_database=cp1251;
SET collation_database=cp1251_general_ci;
SET NAMES 'cp1251' COLLATE 'cp1251_general_ci';
SET CHARACTER SET cp1251;

SET global wait_timeout=600;
SET global interactive_timeout=600;
SET global connect_timeout=600;

DROP PROCEDURE IF EXISTS upgrade_db;
DROP FUNCTION IF EXISTS check_index_exists;
DROP FUNCTION IF EXISTS check_column_exists;
DROP FUNCTION IF EXISTS check_constraint_exists;
DROP FUNCTION IF EXISTS check_table_exists;
DROP PROCEDURE IF EXISTS check_table_partitioned_p;
DROP FUNCTION IF EXISTS check_mysql_version;

DELIMITER $$

CREATE DEFINER=CURRENT_USER FUNCTION check_index_exists ( a_index_name TEXT ) 
RETURNS INTEGER
DETERMINISTIC
READS SQL DATA
BEGIN
DECLARE hasIndex INTEGER;
SELECT COUNT(1) INTO hasIndex FROM information_schema.statistics WHERE LCASE(table_schema) IN (select database()) AND LCASE(index_name) = LCASE(TRIM(a_index_name));
RETURN hasIndex;
END$$

CREATE DEFINER=CURRENT_USER FUNCTION check_column_exists ( a_column_name TEXT, a_table_name TEXT ) 
RETURNS INTEGER
DETERMINISTIC
READS SQL DATA
BEGIN
DECLARE hasColumn INTEGER;
SELECT COUNT(1) INTO hasColumn FROM information_schema.columns WHERE LCASE(table_schema) IN (select database()) AND LCASE(column_name) = LCASE(TRIM(a_column_name)) AND LCASE(table_name) = LCASE(TRIM(a_table_name));
RETURN hasColumn;
END$$

CREATE DEFINER=CURRENT_USER FUNCTION check_constraint_exists ( a_constraint_name TEXT, a_table_name TEXT ) 
RETURNS INTEGER
DETERMINISTIC
READS SQL DATA
BEGIN
DECLARE hasConstraint INTEGER;
SELECT COUNT(1) INTO hasConstraint FROM information_schema.TABLE_CONSTRAINTS WHERE LCASE(table_schema) IN (select database()) AND LCASE(constraint_name) = LCASE(TRIM(a_constraint_name)) AND LCASE(table_name) = LCASE(TRIM(a_table_name));
RETURN hasConstraint;
END$$

CREATE DEFINER=CURRENT_USER FUNCTION check_table_exists ( a_table_name TEXT ) 
RETURNS INTEGER
DETERMINISTIC
READS SQL DATA
BEGIN
DECLARE hasTable INTEGER;
SELECT COUNT(1) INTO hasTable FROM information_schema.columns WHERE LCASE(table_schema) IN (select database()) AND LCASE(table_name) = LCASE(TRIM(a_table_name));
RETURN hasTable;
END$$

CREATE DEFINER=CURRENT_USER FUNCTION check_mysql_version ( a_minimum_required TEXT ) 
RETURNS INTEGER
DETERMINISTIC
READS SQL DATA
BEGIN
DECLARE version VARCHAR(128);
SELECT VERSION() INTO version;
RETURN a_minimum_required <= version;
END$$

CREATE DEFINER=CURRENT_USER PROCEDURE check_table_partitioned_p ( a_table_name TEXT, out a_result INTEGER ) 
READS SQL DATA
BEGIN
DECLARE result INTEGER;
IF NOT check_mysql_version('5.1') THEN
  SELECT 1 INTO a_result;
ELSE
  SET @s = 'SELECT COUNT(1) INTO @outvar FROM information_schema.global_variables WHERE LCASE(variable_name) LIKE \'%partition%\' AND LCASE(variable_value) = \'yes\'';
  PREPARE stmt FROM @s;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
  SELECT @outvar INTO a_result;
  IF a_result < 1 THEN
    SELECT 1 INTO a_result;
  ELSE
    SET @s = 'SELECT COUNT(1) INTO @outvar FROM information_schema.partitions WHERE LCASE(table_schema) IN (select database()) AND LCASE(table_name) = LCASE(TRIM(?)) AND partition_name IS NOT NULL';
    PREPARE stmt FROM @s;
    SET @t = a_table_name;
    EXECUTE stmt USING @t;
    DEALLOCATE PREPARE stmt;
    SELECT @outvar INTO a_result;
  END IF;
END IF;
END$$

CREATE DEFINER=CURRENT_USER PROCEDURE upgrade_db ( update_version TEXT ) 
root:BEGIN
DECLARE installedCount INTEGER;

SELECT COUNT(1) INTO installedCount FROM cms_Update WHERE Caption = TRIM(update_version);

IF installedCount > 0 THEN
	LEAVE root;
END IF;

--
--
-- upgrade statements go here
--
--

IF NOT check_index_exists('I$pm_VersionBurndown$Version') THEN
CREATE INDEX I$pm_VersionBurndown$Version ON pm_VersionBurndown (Version);
END IF;

IF NOT check_index_exists('I$pm_VersionBurndown$SnapshotDays') THEN
CREATE INDEX I$pm_VersionBurndown$SnapshotDays ON pm_VersionBurndown (SnapshotDays);
END IF;

ANALYZE TABLE pm_VersionBurndown;

OPTIMIZE TABLE pm_VersionBurndown;

ALTER TABLE WikiPageFile MODIFY ContentExt VARCHAR(255);

ALTER TABLE BlogPostFile MODIFY ContentExt VARCHAR(255);

IF NOT check_index_exists('I$pm_TaskTypeStage$TaskType') THEN
CREATE INDEX I$pm_TaskTypeStage$TaskType ON pm_TaskTypeStage (TaskType);
END IF;

IF NOT check_index_exists('I$pm_TaskTypeStage$ProjectStage') THEN
CREATE INDEX I$pm_TaskTypeStage$ProjectStage ON pm_TaskTypeStage (ProjectStage);
END IF;

ANALYZE TABLE pm_TaskTypeStage;

OPTIMIZE TABLE pm_TaskTypeStage;

UPDATE BlogPostTag t SET t.VPD = (SELECT p.VPD FROM BlogPost p WHERE p.BlogPostId = t.BlogPost);

UPDATE attribute SET IsVisible = 'Y' WHERE ReferenceName = 'ReferenceName' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'WikiPageType');

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Ссылочное имя страницы','PageReferenceName','VARCHAR',NULL,'Y','N',e.entityId,300 
  FROM entity e WHERE e.ReferenceName = 'WikiPageType' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'PageReferenceName')
 LIMIT 1;

IF NOT check_column_exists('PageReferenceName', 'WikiPageType') THEN
ALTER TABLE WikiPageType ADD PageReferenceName VARCHAR(64);
END IF;

UPDATE WikiPageType SET PageReferenceName = ReferenceName WHERE PageReferenceName IS NULL;

UPDATE WikiPageType SET ReferenceName = WikiPageTypeId WHERE ReferenceName = 'Requirements';

UPDATE pm_UserSetting SET Setting = '46415dbcc2b053ac0009d77a522add41' WHERE Setting = '252524238268b05d8a7e1d4c4c4f2251';

UPDATE pm_UserSetting SET Setting = 'abacbd7f2b743404ded13d961a65fdb9' WHERE Setting = 'fa3e4654f04d4869b4ea2c3f58d92c69';

UPDATE pm_UserSetting SET Setting = '0e2b2ff38e5d14428c2425ed167495f9' WHERE Setting = '0cb62d907762439214cd10b532d1e471';

UPDATE pm_UserSetting SET Setting = '6ed722cf5cefca2f02484c71675d5255' WHERE Setting = 'e57b3dd5223f819bbabdfdc2f8c6f973';

IF (SELECT COUNT(1) FROM attribute WHERE ReferenceName = 'Author' AND AttributeType <> 'REF_cms_UserId' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_CustomReport')) > 0 THEN
UPDATE pm_CustomReport t SET t.Author = (SELECT p.SystemUser FROM pm_Participant p WHERE p.pm_ParticipantId = t.Author) WHERE t.VPD IN (SELECT p.VPD FROM pm_PublicInfo p);
END IF;

UPDATE attribute SET AttributeType = 'REF_cms_UserId' WHERE ReferenceName = 'Author' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_CustomReport');

UPDATE pm_ChangeRequest SET State = 'submitted' WHERE IFNULL(State,'') = '';

UPDATE attribute SET AttributeType = 'DATETIME' WHERE ReferenceName IN ('StartDate', 'FinishDate') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName IN ('pm_Task', 'pm_ChangeRequest', 'pm_Release', 'pm_Version'));

ALTER TABLE pm_Task MODIFY StartDate DATETIME;

ALTER TABLE pm_Task MODIFY FinishDate DATETIME;

ALTER TABLE pm_ChangeRequest MODIFY StartDate DATETIME;

ALTER TABLE pm_ChangeRequest MODIFY FinishDate DATETIME;

ALTER TABLE pm_Version MODIFY StartDate DATETIME;

ALTER TABLE pm_Version MODIFY FinishDate DATETIME;

IF check_index_exists('I$ObjectChangeLog$EntityName') THEN
ALTER TABLE ObjectChangeLog DROP INDEX I$ObjectChangeLog$EntityName;
END IF;

IF check_index_exists('I$ObjectChangeLog$EntityRefName') THEN
ALTER TABLE ObjectChangeLog DROP INDEX I$ObjectChangeLog$EntityRefName;
END IF;

IF check_index_exists('I$ObjectChangeLog$EN_VPD') THEN
ALTER TABLE ObjectChangeLog DROP INDEX I$ObjectChangeLog$EN_VPD;
END IF;

IF check_index_exists('I$ObjectChangeLog$ERN_VPD') THEN
ALTER TABLE ObjectChangeLog DROP INDEX I$ObjectChangeLog$ERN_VPD;
END IF;

IF NOT check_index_exists('I$ObjectChangeLog$ClassName') THEN
CREATE INDEX I$ObjectChangeLog$ClassName ON ObjectChangeLog (ClassName);
END IF;

IF NOT check_index_exists('I$ObjectChangeLog$ObjectIdClassName') THEN
CREATE INDEX I$ObjectChangeLog$ObjectIdClassName ON ObjectChangeLog (ObjectId, ClassName);
END IF;

IF NOT check_index_exists('I$ObjectChangeLog$RecordCreated') THEN
CREATE INDEX I$ObjectChangeLog$RecordCreated ON ObjectChangeLog (RecordCreated);
END IF;

IF NOT check_index_exists('I$ObjectChangeLog$RecordModified') THEN
CREATE INDEX I$ObjectChangeLog$RecordModified ON ObjectChangeLog (RecordModified);
END IF;

IF NOT check_index_exists('I$ObjectChangeLog$VPD') THEN
CREATE INDEX I$ObjectChangeLog$VPD ON ObjectChangeLog (VPD);
END IF;

ANALYZE TABLE ObjectChangeLog;

UPDATE pm_Project t SET t.VPD = (SELECT p.VPD FROM pm_PublicInfo p WHERE p.Project = t.pm_ProjectId) WHERE IFNULL(t.VPD,'') = '';

IF NOT check_index_exists('I$pm_Project$VPD') THEN
CREATE INDEX I$pm_Project$VPD ON pm_Project (VPD);
END IF;

CALL check_table_partitioned_p('ObjectChangeLog', @partitions);

IF @partitions < 1 THEN

DROP TABLE IF EXISTS `ObjectChangeLog2`;

SET @s = 'CREATE TABLE `ObjectChangeLog2` (  `ObjectChangeLogId` int(11) NOT NULL auto_increment, `RecordCreated` datetime default NULL,  `RecordModified` timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP,  `VPD` varchar(32) default NULL,  `OrderNum` int(11) default NULL,  `Caption` text,  `ObjectId` int(11) default NULL,  `EntityRefName` varchar(128),  `ChangeKind` text,  `Author` int(11) default NULL,  `Content` text,  `ObjectUrl` text,  `EntityName` varchar(128),  `VisibilityLevel` int(11) default NULL,  `SystemUser` int(11) default NULL,  `RecordVersion` int(11) default 0,  `ClassName` varchar(128),  PRIMARY KEY  (`ObjectChangeLogId`, `RecordModified`)) ENGINE=MyISAM DEFAULT CHARSET=cp1251 PARTITION BY RANGE(UNIX_TIMESTAMP(RecordModified)) (PARTITION p_201305 VALUES LESS THAN (1370030400), PARTITION p_201306 VALUES LESS THAN (1372622400), PARTITION p_201307 VALUES LESS THAN (1375300800), PARTITION p_201308 VALUES LESS THAN (1377979200), PARTITION p_201309 VALUES LESS THAN (1380571200), PARTITION p_max VALUES LESS THAN (MAXVALUE))';
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO ObjectChangeLog2 SELECT * FROM ObjectChangeLog;

RENAME TABLE ObjectChangeLog TO ObjectChangeLog_Delete;

RENAME TABLE ObjectChangeLog2 TO ObjectChangeLog;

CREATE INDEX I$ObjectChangeLog$ClassName ON ObjectChangeLog (ClassName);

CREATE INDEX I$ObjectChangeLog$RecordModified ON ObjectChangeLog (RecordModified);

END IF;

CALL check_table_partitioned_p('cms_EntityCluster', @partitions);

IF @partitions < 1 THEN

DROP TABLE IF EXISTS `cms_EntityCluster2`;

SET @s = 'CREATE TABLE `cms_EntityCluster2` (  `cms_EntityClusterId` int(11) NOT NULL auto_increment, `RecordCreated` datetime DEFAULT NULL,  `RecordModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  `VPD` varchar(32) DEFAULT NULL,  `ObjectClass` varchar(32) DEFAULT NULL,  `ObjectAttribute` varchar(32) DEFAULT NULL,  `AttributeValue` varchar(128) DEFAULT NULL,  `ObjectIds` mediumtext,  `TotalCount` int(11) DEFAULT NULL,  `RecordVersion` int(11) DEFAULT 0, PRIMARY KEY  (`cms_EntityClusterId`, `RecordModified`)) ENGINE=MyISAM DEFAULT CHARSET=cp1251 PARTITION BY RANGE(UNIX_TIMESTAMP(RecordModified))(PARTITION p_201305 VALUES LESS THAN (1370030400), PARTITION p_201306 VALUES LESS THAN (1372622400), PARTITION p_201307 VALUES LESS THAN (1375300800), PARTITION p_201308 VALUES LESS THAN (1377979200), PARTITION p_201309 VALUES LESS THAN (1380571200), PARTITION p_max VALUES LESS THAN (MAXVALUE))';
PREPARE stmt FROM @s;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO cms_EntityCluster2 SELECT * FROM cms_EntityCluster;

RENAME TABLE cms_EntityCluster TO cms_EntityCluster_Delete;

RENAME TABLE cms_EntityCluster2 TO cms_EntityCluster;

CREATE INDEX I$cms_EntityCluster$RecordModified ON cms_EntityCluster (RecordModified);

END IF;

IF NOT check_index_exists('I$Tag$Vpd') THEN
CREATE INDEX I$Tag$Vpd ON Tag (VPD);
END IF;

IF NOT check_index_exists('I$pm_RequestTag$Tag') THEN
CREATE INDEX I$pm_RequestTag$Tag ON pm_RequestTag (Tag);
END IF;

IF NOT check_index_exists('I$WikiTag$Tag') THEN
CREATE INDEX I$WikiTag$Tag ON WikiTag (Tag);
END IF;

IF NOT check_index_exists('I$pm_CustomTag$Tag') THEN
CREATE INDEX I$pm_CustomTag$Tag ON pm_CustomTag (Tag);
END IF;

IF NOT check_index_exists('I$pm_CustomTag$Object') THEN
CREATE INDEX I$pm_CustomTag$Object ON pm_CustomTag (ObjectId, ObjectClass);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Адрес отправителя','SenderAddress','VARCHAR',NULL,'N','Y',e.entityId,43 
  FROM entity e WHERE e.ReferenceName = 'co_RemoteMailbox' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'SenderAddress')
 LIMIT 1;

IF NOT check_column_exists('SenderAddress', 'co_RemoteMailbox') THEN
ALTER TABLE co_RemoteMailbox ADD SenderAddress VARCHAR(128);
END IF;

ALTER TABLE pm_TransitionPredicate MODIFY Predicate BIGINT;

-- update pm_Activity t set t.Participant = (select b.pm_ParticipantId from pm_Participant a, pm_Participant b where a.SystemUser = b.SystemUser and a.pm_ParticipantId = t.Participant and b.VPD = t.VPD limit 1);

IF NOT check_column_exists('StartDateOnly', 'pm_CalendarInterval') THEN
alter table pm_CalendarInterval add StartDateOnly DATE;
END IF;

IF NOT check_column_exists('StartDateWeekday', 'pm_CalendarInterval') THEN
alter table pm_CalendarInterval add StartDateWeekday INTEGER;
END IF;

update pm_CalendarInterval set StartDateOnly = DATE(StartDate);

update pm_CalendarInterval set StartDateWeekday = DAYOFWEEK(StartDate);

IF NOT check_index_exists('I$pm_CalendarInterval$StartDateMul') THEN
create index I$pm_CalendarInterval$StartDateMul on pm_CalendarInterval (Kind,StartDateWeekday,StartDateOnly);
END IF;

UPDATE cms_User SET Password = NULL WHERE LDAPUID <> '';

-- 3.1

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Путь к родительской странице','ParentPath','TEXT',NULL,'N','N',e.entityId,0
  FROM entity e WHERE e.ReferenceName = 'WikiPage' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ParentPath')
 LIMIT 1;

IF NOT check_column_exists('ParentPath', 'WikiPage') THEN
ALTER TABLE WikiPage ADD ParentPath TEXT;
END IF;

IF NOT check_index_exists('I$WikiPage$ParentPath') THEN
CREATE FULLTEXT INDEX I$WikiPage$ParentPath ON WikiPage (ParentPath);
END IF;

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName IN ('Caption', 'Email', 'Login') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName IN ('cms_User'));

UPDATE attribute SET IsVisible = 'Y' WHERE ReferenceName = 'Author' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'WikiPage');

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Номер раздела','SectionNumber','VARCHAR',NULL,'N','N',e.entityId,0
  FROM entity e WHERE e.ReferenceName = 'WikiPage' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'SectionNumber')
 LIMIT 1;

IF NOT check_column_exists('SectionNumber', 'WikiPage') THEN
ALTER TABLE WikiPage ADD SectionNumber TEXT;
END IF;


UPDATE attribute SET OrderNum = 7 WHERE ReferenceName = 'Planned' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

UPDATE attribute SET OrderNum = 8 WHERE ReferenceName = 'Fact' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

UPDATE attribute SET OrderNum = 15 WHERE ReferenceName = 'Release' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

UPDATE attribute SET OrderNum = 10 WHERE ReferenceName = 'Assignee' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

UPDATE attribute SET OrderNum = 20 WHERE ReferenceName = 'TaskType' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

UPDATE attribute SET OrderNum = 30 WHERE ReferenceName = 'Priority' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');



INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Цвет','RelatedColor','COLOR',NULL,'N','Y',e.entityId,20
  FROM entity e WHERE e.ReferenceName = 'Priority' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'RelatedColor')
 LIMIT 1;

IF NOT check_column_exists('RelatedColor', 'Priority') THEN
ALTER TABLE Priority ADD RelatedColor VARCHAR(16);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Цвет','RelatedColor','COLOR',NULL,'N','Y',e.entityId,30
  FROM entity e WHERE e.ReferenceName = 'pm_IssueType' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'RelatedColor')
 LIMIT 1;

IF NOT check_column_exists('RelatedColor', 'pm_IssueType') THEN
ALTER TABLE pm_IssueType ADD RelatedColor VARCHAR(16);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Цвет','RelatedColor','COLOR',NULL,'N','Y',e.entityId,35
  FROM entity e WHERE e.ReferenceName = 'pm_State' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'RelatedColor')
 LIMIT 1;

IF NOT check_column_exists('RelatedColor', 'pm_State') THEN
ALTER TABLE pm_State ADD RelatedColor VARCHAR(16);
END IF;

UPDATE Priority SET RelatedColor = '#DB7A40' WHERE PriorityId = 1 AND RelatedColor IS NULL;

UPDATE Priority SET RelatedColor = '#D5BB28' WHERE PriorityId = 2 AND RelatedColor IS NULL;

UPDATE Priority SET RelatedColor = '#6969A5' WHERE PriorityId NOT IN (1, 2) AND RelatedColor IS NULL;



IF NOT check_column_exists('DocumentId', 'WikiPage') THEN
ALTER TABLE WikiPage ADD DocumentId INTEGER;

UPDATE WikiPage t SET t.DocumentId = REPLACE(SUBSTRING_INDEX(t.ParentPath, ',', 2),',','');
END IF;

IF NOT check_column_exists('SortIndex', 'WikiPage') THEN
ALTER TABLE WikiPage ADD SortIndex TEXT;

CREATE TEMPORARY TABLE tmp_WikiPageSort (WikiPageId INTEGER, SortIndex TEXT ) AS SELECT t.WikiPageId, (SELECT GROUP_CONCAT(LPAD(u.OrderNum, 10, '0') ORDER BY LENGTH(u.ParentPath)) FROM WikiPage u WHERE t.ParentPath LIKE CONCAT('%,',u.WikipageId,',%')) SortIndex FROM WikiPage t;

UPDATE WikiPage t SET t.SortIndex = (SELECT u.SortIndex FROM tmp_WikiPageSort u WHERE u.WikiPageId = t.WikiPageId);
END IF;

UPDATE attribute SET ReferenceName = 'IsFileServer' WHERE ReferenceName = 'IsArtefactsUsed';

IF check_column_exists('IsArtefactsUsed', 'pm_Project') THEN
ALTER TABLE pm_Project CHANGE IsArtefactsUsed IsFileServer CHAR(1);
END IF;

UPDATE attribute SET IsVisible = 'N' WHERE ReferenceName = 'AdminProject' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'cms_SystemSettings');

update cms_SnapshotItem set ObjectClass = 'Request' where ObjectClass = 'pm_ChangeRequest';

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Ид объекта','ObjectId','TEXT',NULL,'N','N',e.entityId,5
  FROM entity e WHERE e.ReferenceName = 'cms_Snapshot' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ObjectId')
 LIMIT 1;

IF NOT check_column_exists('ObjectId', 'cms_Snapshot') THEN
ALTER TABLE cms_Snapshot ADD ObjectId VARCHAR(128);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Класс объекта','ObjectClass','TEXT',NULL,'N','N',e.entityId,6
  FROM entity e WHERE e.ReferenceName = 'cms_Snapshot' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ObjectClass')
 LIMIT 1;

IF NOT check_column_exists('ObjectClass', 'cms_Snapshot') THEN
ALTER TABLE cms_Snapshot ADD ObjectClass VARCHAR(128);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Ревизия','Baseline','REF_cms_SnapshotId',NULL,'N','N',e.entityId,40
  FROM entity e WHERE e.ReferenceName = 'WikiPageTrace' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Baseline')
 LIMIT 1;

IF NOT check_column_exists('Baseline', 'WikiPageTrace') THEN
ALTER TABLE WikiPageTrace ADD Baseline INTEGER;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Тип','Type','TEXT',NULL,'N','N',e.entityId,50
  FROM entity e WHERE e.ReferenceName = 'cms_Snapshot' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Type')
 LIMIT 1;

IF NOT check_column_exists('Type', 'cms_Snapshot') THEN
ALTER TABLE cms_Snapshot ADD Type VARCHAR(128);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Описание','Description','TEXT',NULL,'N','Y',e.entityId,60
  FROM entity e WHERE e.ReferenceName = 'cms_Snapshot' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Description')
 LIMIT 1;

IF NOT check_column_exists('Description', 'cms_Snapshot') THEN
ALTER TABLE cms_Snapshot ADD Description TEXT;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Тип','Type','TEXT',NULL,'N','N',e.entityId,40
  FROM entity e WHERE e.ReferenceName = 'WikiPageTrace' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Type')
 LIMIT 1;

IF NOT check_column_exists('Type', 'WikiPageTrace') THEN
ALTER TABLE WikiPageTrace ADD Type VARCHAR(128);
END IF;

ALTER TABLE cms_SnapshotItem MODIFY ObjectClass VARCHAR(128);

ALTER TABLE cms_SnapshotItemValue MODIFY ReferenceName VARCHAR(128);

IF NOT check_index_exists('I$cms_SnapshotItem$Snapshot') THEN
CREATE INDEX I$cms_SnapshotItem$Snapshot ON cms_SnapshotItem (Snapshot);
END IF;

IF NOT check_index_exists('I$cms_SnapshotItem$Object') THEN
CREATE INDEX I$cms_SnapshotItem$Object ON cms_SnapshotItem (ObjectId, ObjectClass);
END IF;

IF NOT check_index_exists('I$cms_SnapshotItemValue$SnapshotItem') THEN
CREATE INDEX I$cms_SnapshotItemValue$SnapshotItem ON cms_SnapshotItemValue (SnapshotItem);
END IF;

IF NOT check_index_exists('I$cms_SnapshotItemValue$SnapshotItemReference') THEN
CREATE INDEX I$cms_SnapshotItemValue$SnapshotItemReference ON cms_SnapshotItemValue (SnapshotItem, ReferenceName);
END IF;


INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'text(1716)','IsTasks','CHAR',NULL,'N','N',e.entityId,15
  FROM entity e WHERE e.ReferenceName = 'pm_Methodology' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'IsTasks')
 LIMIT 1;

IF NOT check_column_exists('IsTasks', 'pm_Methodology') THEN
ALTER TABLE pm_Methodology ADD IsTasks CHAR(1);
UPDATE pm_Methodology SET IsTasks = IsPlanningUsed;
END IF;

UPDATE attribute SET OrderNum = 17 WHERE ReferenceName = 'IsReleasesUsed' AND entityId IN (select entityId from entity where ReferenceName = 'pm_Methodology');

UPDATE attribute SET IsVisible = 'N' WHERE ReferenceName IN ('IsPlanningUsed', 'HasMilestones') AND entityId IN (select entityId from entity where ReferenceName = 'pm_Methodology');

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'MetricValueDate','MetricValueDate','DATETIME',NULL,'N','N',e.entityId,15
  FROM entity e WHERE e.ReferenceName = 'pm_VersionMetric' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'MetricValueDate')
 LIMIT 1;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'MetricValueDate','MetricValueDate','DATETIME',NULL,'N','N',e.entityId,15
  FROM entity e WHERE e.ReferenceName = 'pm_IterationMetric' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'MetricValueDate')
 LIMIT 1;

IF NOT check_column_exists('MetricValueDate', 'pm_VersionMetric') THEN
ALTER TABLE pm_VersionMetric ADD MetricValueDate DATETIME;
UPDATE pm_VersionMetric SET MetricValueDate = FROM_DAYS(MetricValue), MetricValue = NULL WHERE Metric IN ('EstimatedStart', 'EstimatedFinish');
END IF;

IF NOT check_column_exists('MetricValueDate', 'pm_IterationMetric') THEN
ALTER TABLE pm_IterationMetric ADD MetricValueDate DATETIME;
UPDATE pm_IterationMetric SET MetricValueDate = FROM_DAYS(MetricValue), MetricValue = NULL WHERE Metric IN ('EstimatedStart', 'EstimatedFinish');
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Краткое название','ShortCaption','VARCHAR',NULL,'N','Y',e.entityId,13
  FROM entity e WHERE e.ReferenceName = 'pm_TaskType' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ShortCaption')
 LIMIT 1;

IF NOT check_column_exists('ShortCaption', 'pm_TaskType') THEN
ALTER TABLE pm_TaskType ADD ShortCaption VARCHAR(128);
END IF;

UPDATE attribute SET Caption = 'Релиз' WHERE ReferenceName = 'PlannedRelease' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_ChangeRequest');

UPDATE attribute SET Caption = 'Исполнитель' WHERE ReferenceName = 'Owner' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_ChangeRequest');

UPDATE WikiPageTrace SET Type = 'coverage' WHERE IFNULL(Type, '') = '';

IF NOT check_index_exists('I$pm_State$ClassVpd') THEN
CREATE INDEX I$pm_State$ClassVpd ON pm_State (ObjectClass, VPD);
END IF;

UPDATE pm_ChangeRequest SET Author = NULL WHERE Author = 0;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Причина рассинхронизации','UnsyncReasonType','VARCHAR',NULL,'N','N',e.entityId,100
  FROM entity e WHERE e.ReferenceName = 'WikiPageTrace' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'UnsyncReasonType')
 LIMIT 1;

IF NOT check_column_exists('UnsyncReasonType', 'WikiPageTrace') THEN
ALTER TABLE WikiPageTrace ADD UnsyncReasonType VARCHAR(32);
END IF;

UPDATE WikiPageTrace SET UnsyncReasonType = 'text-changed' WHERE IsActual = 'N' AND UnsyncReasonType IS NULL;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Временная зона','Timezone','VARCHAR',NULL,'N','N',e.entityId,100
  FROM entity e WHERE e.ReferenceName = 'pm_ProjectUse' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Timezone')
 LIMIT 1;

IF NOT check_column_exists('Timezone', 'pm_ProjectUse') THEN
ALTER TABLE pm_ProjectUse ADD Timezone VARCHAR(64);
END IF;

UPDATE attribute SET AttributeType = 'REF_cms_UserId' WHERE ReferenceName = 'Participant' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_ProjectUse');

IF NOT check_column_exists('TotalCount', 'cms_EntityCluster') THEN
ALTER TABLE cms_EntityCluster ADD TotalCount INT;
END IF;

UPDATE pm_Activity SET VPD = (SELECT VPD FROM pm_Task WHERE pm_TaskId = Task);

UPDATE attribute SET OrderNum = 18 WHERE ReferenceName = 'IsFixedRelease' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Methodology');

UPDATE attribute SET OrderNum = 19 WHERE ReferenceName = 'ReleaseDuration' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Methodology');

DELETE FROM pm_ProjectUse WHERE Timezone IS NULL;

UPDATE pm_StateObject t SET t.Author = (SELECT p.SystemUser FROM pm_Participant p WHERE p.pm_ParticipantId = t.Author) WHERE NOT EXISTS (SELECT 1 FROM attribute WHERE AttributeType = 'REF_cms_UserId' AND ReferenceName = 'Author' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_StateObject'));

UPDATE attribute SET AttributeType = 'REF_cms_UserId' WHERE ReferenceName = 'Author' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_StateObject');

-- 3.3

INSERT INTO entity (Caption, ReferenceName, packageId, IsOrdered, OrderNum, IsDictionary)
SELECT 'Атрибут состояния', 'pm_StateAttribute', 7, 'Y', 10, 'Y' 
  FROM (SELECT 1) t
  WHERE NOT EXISTS (SELECT 1 FROM entity WHERE ReferenceName = 'pm_StateAttribute');

IF NOT check_table_exists('pm_StateAttribute') THEN
CREATE TABLE `pm_StateAttribute` (
  `pm_StateAttributeId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_StateAttributeId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Состояние','State','REF_pm_StateId',NULL,'Y','N',e.entityId,10
  FROM entity e WHERE e.ReferenceName = 'pm_StateAttribute' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'State')
 LIMIT 1;

IF NOT check_column_exists('State', 'pm_StateAttribute') THEN
ALTER TABLE pm_StateAttribute ADD State INTEGER;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Атрибут','ReferenceName','VARCHAR',NULL,'Y','Y',e.entityId,20
  FROM entity e WHERE e.ReferenceName = 'pm_StateAttribute' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ReferenceName')
 LIMIT 1;

IF NOT check_column_exists('ReferenceName', 'pm_StateAttribute') THEN
ALTER TABLE pm_StateAttribute ADD ReferenceName VARCHAR(128);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Сущность','Entity','VARCHAR',NULL,'Y','N',e.entityId,30
  FROM entity e WHERE e.ReferenceName = 'pm_StateAttribute' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Entity')
 LIMIT 1;

IF NOT check_column_exists('Entity', 'pm_StateAttribute') THEN
ALTER TABLE pm_StateAttribute ADD Entity VARCHAR(128);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Видимо на форме','IsVisible','CHAR','Y','N','Y',e.entityId,40
  FROM entity e WHERE e.ReferenceName = 'pm_StateAttribute' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'IsVisible')
 LIMIT 1;

IF NOT check_column_exists('IsVisible', 'pm_StateAttribute') THEN
ALTER TABLE pm_StateAttribute ADD IsVisible CHAR(1);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Обязательно для заполнения','IsRequired','CHAR','N','N','Y',e.entityId,50
  FROM entity e WHERE e.ReferenceName = 'pm_StateAttribute' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'IsRequired')
 LIMIT 1;

IF NOT check_column_exists('IsRequired', 'pm_StateAttribute') THEN
ALTER TABLE pm_StateAttribute ADD IsRequired CHAR(1);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Тип связи','Type','VARCHAR',NULL,'N','N',e.entityId,100
  FROM entity e WHERE e.ReferenceName = 'pm_ChangeRequestTrace' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Type')
 LIMIT 1;

IF NOT check_column_exists('Type', 'pm_ChangeRequestTrace') THEN
ALTER TABLE pm_ChangeRequestTrace ADD `Type` VARCHAR(128);
END IF;

UPDATE pm_ChangeRequestTrace SET Type = 'request' WHERE Type IS NULL AND EXISTS (SELECT 1 FROM pm_ChangeRequest t WHERE t.pm_ChangeRequestId = ChangeRequest AND t.Type IS NULL);

UPDATE pm_ChangeRequestTrace SET Type = 'product' WHERE Type IS NULL AND EXISTS (SELECT 1 FROM pm_ChangeRequest t WHERE t.pm_ChangeRequestId = ChangeRequest AND t.Type IS NOT NULL);

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Иконка','Icon','VARCHAR',NULL,'N','N',e.entityId,100
  FROM entity e WHERE e.ReferenceName = 'pm_Workspace' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Icon')
 LIMIT 1;

IF NOT check_column_exists('Icon', 'pm_Workspace') THEN
ALTER TABLE pm_Workspace ADD `Icon` VARCHAR(128);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Ссылочное имя','ReferenceName','VARCHAR',NULL,'Y','Y',e.entityId,100
  FROM entity e WHERE e.ReferenceName = 'cms_ReportCategory' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ReferenceName')
 LIMIT 1;

IF NOT check_column_exists('ReferenceName', 'cms_ReportCategory') THEN
ALTER TABLE cms_ReportCategory ADD `ReferenceName` VARCHAR(128);
END IF;



INSERT INTO entity (Caption, ReferenceName, packageId, IsOrdered, OrderNum, IsDictionary)
SELECT 'Аккаунт в СКВ', 'pm_SubversionUser', 7, 'Y', 10, 'Y' 
  FROM (SELECT 1) t
  WHERE NOT EXISTS (SELECT 1 FROM entity WHERE ReferenceName = 'pm_SubversionUser');

IF NOT check_table_exists('pm_SubversionUser') THEN
CREATE TABLE `pm_SubversionUser` (
  `pm_SubversionUserId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `OrderNum` int(11) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`pm_SubversionUserId`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Пользователь','SystemUser','REF_cms_UserId',NULL,'Y','Y',e.entityId,10
  FROM entity e WHERE e.ReferenceName = 'pm_SubversionUser' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'SystemUser')
 LIMIT 1;

IF NOT check_column_exists('SystemUser', 'pm_SubversionUser') THEN
ALTER TABLE pm_SubversionUser ADD SystemUser INTEGER;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Подключение','Connector','REF_pm_SubversionId',NULL,'Y','N',e.entityId,20
  FROM entity e WHERE e.ReferenceName = 'pm_SubversionUser' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Connector')
 LIMIT 1;

IF NOT check_column_exists('Connector', 'pm_SubversionUser') THEN
ALTER TABLE pm_SubversionUser ADD Connector INTEGER;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Имя пользователя','UserName','VARCHAR',NULL,'Y','Y',e.entityId,30
  FROM entity e WHERE e.ReferenceName = 'pm_SubversionUser' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'UserName')
 LIMIT 1;

IF NOT check_column_exists('UserName', 'pm_SubversionUser') THEN
ALTER TABLE pm_SubversionUser ADD UserName VARCHAR(255);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Пароль','UserPassword','PASSWORD',NULL,'N','Y',e.entityId,40
  FROM entity e WHERE e.ReferenceName = 'pm_SubversionUser' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'UserPassword')
 LIMIT 1;

IF NOT check_column_exists('UserPassword', 'pm_SubversionUser') THEN
ALTER TABLE pm_SubversionUser ADD UserPassword VARCHAR(255);

INSERT INTO pm_SubversionUser (Connector, SystemUser, UserName, UserPassword, VPD )
SELECT s.pm_SubversionId,
	   p.SystemUser,
	   (SELECT v.StringValue FROM pm_CustomAttribute c, pm_AttributeValue v WHERE c.ReferenceName = CONCAT('RepositoryLogin', s.pm_SubversionId) AND v.CustomAttribute = c.pm_CustomAttributeId AND v.ObjectId = p.pm_ParticipantId) UserName,
	   (SELECT v.StringValue FROM pm_CustomAttribute c, pm_AttributeValue v WHERE c.ReferenceName = CONCAT('RepositoryPassword', s.pm_SubversionId) AND v.CustomAttribute = c.pm_CustomAttributeId AND v.ObjectId = p.pm_ParticipantId) UserPassword,
	   s.VPD
  FROM pm_Subversion s, pm_Participant p
 WHERE EXISTS (SELECT 1 FROM pm_CustomAttribute c, pm_AttributeValue v WHERE c.ReferenceName = CONCAT('RepositoryLogin', s.pm_SubversionId) AND v.CustomAttribute = c.pm_CustomAttributeId AND v.ObjectId = p.pm_ParticipantId AND v.StringValue <> '');

DELETE FROM pm_CustomAttribute WHERE ReferenceName LIKE 'RepositoryLogin%';

DELETE FROM pm_CustomAttribute WHERE ReferenceName LIKE 'RepositoryPassword%';
END IF;


INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Имя класса ссылки','AttributeTypeClassName','VARCHAR',NULL,'N','N',e.entityId,130
  FROM entity e WHERE e.ReferenceName = 'pm_CustomAttribute' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'AttributeTypeClassName')
 LIMIT 1;

IF NOT check_column_exists('AttributeTypeClassName', 'pm_CustomAttribute') THEN
ALTER TABLE pm_CustomAttribute ADD AttributeTypeClassName VARCHAR(255);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Мощность','Capacity','INTEGER',NULL,'N','N',e.entityId,140
  FROM entity e WHERE e.ReferenceName = 'pm_CustomAttribute' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Capacity')
 LIMIT 1;

IF NOT check_column_exists('Capacity', 'pm_CustomAttribute') THEN
ALTER TABLE pm_CustomAttribute ADD Capacity INTEGER;
END IF;


UPDATE pm_CustomAttribute SET AttributeType = '1' WHERE AttributeType = 'integer';

UPDATE pm_CustomAttribute SET AttributeType = '2' WHERE AttributeType = 'dictionary';

UPDATE pm_CustomAttribute SET AttributeType = '3' WHERE AttributeType = 'date';

UPDATE pm_CustomAttribute SET AttributeType = '4' WHERE AttributeType = 'string';

UPDATE pm_CustomAttribute SET AttributeType = '5' WHERE AttributeType = 'text';

UPDATE pm_CustomAttribute SET AttributeType = '6' WHERE AttributeType = 'wysiwyg';

UPDATE pm_CustomAttribute SET AttributeType = '7' WHERE AttributeType = 'reference';

ALTER TABLE pm_CustomAttribute MODIFY AttributeType INTEGER;

UPDATE attribute SET AttributeType = 'INTEGER' WHERE ReferenceName = 'AttributeType' AND entityId = (SELECT entityId FROM entity WHERE ReferenceName = 'pm_CustomAttribute');

UPDATE attribute set AttributeType = 'LARGETEXT' WHERE ReferenceName = 'Description' AND entityid IN (select entityId from entity where ReferenceName = 'cms_Snapshot');

UPDATE WikiPage SET ReferenceName = '1' WHERE ReferenceName = 'KnowledgeBase';

UPDATE WikiPage SET ReferenceName = '2' WHERE ReferenceName = 'Requirements';

UPDATE WikiPage SET ReferenceName = '3' WHERE ReferenceName = 'TestScenario';

UPDATE WikiPage SET ReferenceName = '4' WHERE ReferenceName = 'HelpPage';

UPDATE WikiPageType SET PageReferenceName = '2' WHERE PageReferenceName IN ('requirements','Requirements');

ALTER TABLE WikiPage MODIFY ReferenceName INTEGER;

UPDATE attribute SET AttributeType = 'INTEGER' WHERE ReferenceName IN ('IsTemplate', 'ReferenceName') AND entityId = (SELECT entityId FROM entity WHERE ReferenceName = 'WikiPage');

UPDATE attribute SET DefaultValue = '0', IsRequired = 'Y' WHERE ReferenceName IN ('IsTemplate') AND entityId = (SELECT entityId FROM entity WHERE ReferenceName = 'WikiPage');

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName = 'IsReleasesUsed' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Methodology');

INSERT INTO entity (Caption, ReferenceName, packageId, IsOrdered, OrderNum, IsDictionary)
SELECT 'Изменненные объекты', 'co_AffectedObjects', 7, 'N', 10, 'N' 
  FROM (SELECT 1) t
  WHERE NOT EXISTS (SELECT 1 FROM entity WHERE ReferenceName = 'co_AffectedObjects');

IF NOT check_table_exists('co_AffectedObjects') THEN

UPDATE WikiPage SET IsTemplate = '1' WHERE IsTemplate = 'Y';

UPDATE WikiPage SET IsTemplate = '0' WHERE IsTemplate = 'N';

ALTER TABLE WikiPage MODIFY IsTemplate INTEGER;

CREATE TABLE `co_AffectedObjects` (
  `co_AffectedObjectsId` int(11) NOT NULL auto_increment,
  `VPD` varchar(32) default NULL,
  `RecordCreated` datetime default NULL,
  `RecordModified` datetime default NULL,
  `RecordVersion` int(11) default '0',
  PRIMARY KEY  (`co_AffectedObjectsId`)
) ENGINE=MEMORY DEFAULT CHARSET=cp1251;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Объект','ObjectId','INTEGER',NULL,'Y','Y',e.entityId,10
  FROM entity e WHERE e.ReferenceName = 'co_AffectedObjects' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ObjectId')
 LIMIT 1;

IF NOT check_column_exists('ObjectId', 'co_AffectedObjects') THEN
ALTER TABLE co_AffectedObjects ADD ObjectId INTEGER;
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Класс','ObjectClass','VARCHAR',NULL,'Y','Y',e.entityId,20
  FROM entity e WHERE e.ReferenceName = 'co_AffectedObjects' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'ObjectClass')
 LIMIT 1;

IF NOT check_column_exists('ObjectClass', 'co_AffectedObjects') THEN
ALTER TABLE co_AffectedObjects ADD ObjectClass VARCHAR(64);
END IF;

IF NOT check_index_exists('I$co_AffectedObjects$Object') THEN
CREATE INDEX I$co_AffectedObjects$Object ON co_AffectedObjects (ObjectId,ObjectClass);
END IF;

UPDATE attribute SET Caption = 'Добавить в избранное' WHERE ReferenceName = 'IsHandAccess';

IF NOT check_index_exists('I$cms_Snapshot$Object') THEN
CREATE INDEX I$cms_Snapshot$Object ON cms_Snapshot (ObjectId, ObjectClass);
END IF;

IF NOT check_index_exists('I$cms_Snapshot$Branch') THEN
CREATE INDEX I$cms_Snapshot$Branch ON cms_Snapshot (ObjectId, ObjectClass, Type);
END IF;

ALTER TABLE pm_FunctionTrace MODIFY ObjectClass VARCHAR(64);

IF NOT check_index_exists('I$pm_FunctionTrace$Object') THEN
CREATE INDEX I$pm_FunctionTrace$Object ON pm_FunctionTrace (ObjectId, ObjectClass);
END IF;

IF NOT check_index_exists('I$pm_FunctionTrace$Feature') THEN
CREATE INDEX I$pm_FunctionTrace$Feature ON pm_FunctionTrace (Feature);
END IF;

IF NOT check_index_exists('I$WikiPage$ReferenceName') THEN
CREATE INDEX I$WikiPage$ReferenceName ON WikiPage (ReferenceName);
END IF;

IF NOT check_index_exists('I$pm_VersionMetric$Date') THEN
CREATE INDEX I$pm_VersionMetric$Date ON pm_VersionMetric (MetricValueDate); 
END IF;

IF NOT check_index_exists('I$pm_Version$StartDate') THEN
CREATE INDEX I$pm_Version$StartDate ON pm_Version (StartDate); 
END IF;

IF NOT check_index_exists('I$pm_Version$FinishDate') THEN
CREATE INDEX I$pm_Version$FinishDate ON pm_Version (FinishDate); 
END IF;

IF NOT check_index_exists('I$pm_IterationMetric$Date') THEN
CREATE INDEX I$pm_IterationMetric$Date ON pm_IterationMetric (MetricValueDate); 
END IF;

IF NOT check_index_exists('I$pm_Release$StartDate') THEN
CREATE INDEX I$pm_Release$StartDate ON pm_Release (StartDate); 
END IF;

IF NOT check_index_exists('I$pm_Release$FinishDate') THEN
CREATE INDEX I$pm_Release$FinishDate ON pm_Release (FinishDate); 
END IF;


IF check_column_exists('TestCaseExecution', 'pm_ChangeRequest') THEN
INSERT INTO pm_ChangeRequestTrace (VPD, ChangeRequest, ObjectId, ObjectClass, IsActual)
SELECT r.VPD, r.pm_ChangeRequestId, r.TestCaseExecution, 'TestCaseExecution', 'Y'
  FROM pm_ChangeRequest r WHERE r.TestCaseExecution IS NOT NULL;

DELETE FROM attribute WHERE ReferenceName = 'TestCaseExecution' AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_ChangeRequest');

ALTER TABLE pm_ChangeRequest DROP COLUMN TestCaseExecution;
END IF;

DELETE FROM pm_ProjectTemplate WHERE FileName IN ('issuetr_ru.xml', 'issuetr_en.xml');

IF NOT check_index_exists('I$WikiPage$Document') THEN
CREATE INDEX I$WikiPage$Document ON WikiPage (DocumentId);

INSERT INTO pm_StateAttribute (VPD, State, ReferenceName, Entity, IsVisible, IsRequired)
 SELECT s.VPD, s.pm_StateId, 'Type', 'request', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'request'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass)
  UNION 
 SELECT s.VPD, s.pm_StateId, 'Estimation', 'request', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'request'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass)
  UNION 
 SELECT s.VPD, s.pm_StateId, 'Author', 'request', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'request'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass)
  UNION 
 SELECT s.VPD, s.pm_StateId, 'Tags', 'request', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'request'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass)
  UNION 
 SELECT s.VPD, s.pm_StateId, 'Links', 'request', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'request'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass)
  UNION 
 SELECT s.VPD, s.pm_StateId, 'Deadlines', 'request', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'request'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass)
  UNION 
 SELECT s.VPD, s.pm_StateId, 'Watchers', 'request', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'request'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass);

INSERT INTO pm_StateAttribute (VPD, State, ReferenceName, Entity, IsVisible, IsRequired)
 SELECT s.VPD, s.pm_StateId, 'TraceTask', 'task', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'task'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass)
  UNION
 SELECT s.VPD, s.pm_StateId, 'Watchers', 'task', 'Y', 'N'
   FROM pm_State s
  WHERE s.ObjectClass = 'task'
    AND s.OrderNum = (SELECT MIN(s2.OrderNum) FROM pm_State s2 WHERE s2.VPD = s.VPD AND s2.ObjectClass = s.ObjectClass);

delete from pm_CalendarInterval where IntervalYear >= 2014;

END IF;

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName = 'Caption' AND AttributeType = 'TEXT';

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName IN ('ReferenceName', 'EntityReferenceName', 'DefaultValue') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_CustomAttribute');

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName = 'ReferenceName';

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName = 'ShortCaption';

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE ReferenceName IN ('ClosedInVersion', 'SubmittedVersion');

UPDATE attribute SET AttributeType = 'VARCHAR' WHERE AttributeType = 'TEXT' AND entityId IN (select entityId from entity where ReferenceName = 'pm_Subversion');

TRUNCATE TABLE co_AffectedObjects;


IF NOT check_constraint_exists('UK_pm_AccessRight', 'pm_AccessRight') THEN

DELETE FROM pm_AccessRight using pm_AccessRight, pm_AccessRight ar2 
 WHERE pm_AccessRight.pm_AccessRightId < ar2.pm_AccessRightId
   AND IFNULL(pm_AccessRight.ReferenceName,'') = IFNULL(ar2.ReferenceName,'')
   AND IFNULL(pm_AccessRight.ReferenceType,'') = IFNULL(ar2.ReferenceType,'')
   AND IFNULL(pm_AccessRight.ProjectRole,0) = IFNULL(ar2.ProjectRole,0)
   AND pm_AccessRight.VPD = ar2.VPD;

ALTER IGNORE TABLE pm_AccessRight ADD CONSTRAINT UK_pm_AccessRight UNIQUE (ReferenceName, ReferenceType, ProjectRole, VPD);

END IF;

IF NOT check_constraint_exists('UK_pm_AttributeValue', 'pm_AttributeValue') THEN

DELETE FROM pm_AttributeValue using pm_AttributeValue, pm_AttributeValue ar2 
 WHERE pm_AttributeValue.pm_AttributeValueId < ar2.pm_AttributeValueId
   AND IFNULL(pm_AttributeValue.CustomAttribute,0) = IFNULL(ar2.CustomAttribute,0)
   AND IFNULL(pm_AttributeValue.ObjectId,0) = IFNULL(ar2.ObjectId,0);

ALTER IGNORE TABLE pm_AttributeValue ADD CONSTRAINT UK_pm_AttributeValue UNIQUE (CustomAttribute, ObjectId);

END IF;


INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Цвет','RelatedColor','COLOR',NULL,'N','Y',e.entityId,20
  FROM entity e WHERE e.ReferenceName = 'pm_TaskType' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'RelatedColor')
 LIMIT 1;

IF NOT check_column_exists('RelatedColor', 'pm_TaskType') THEN
ALTER TABLE pm_TaskType ADD RelatedColor VARCHAR(16);
END IF;

INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Обратное название','BackwardCaption','VARCHAR',NULL,'Y','Y',e.entityId,15
  FROM entity e WHERE e.ReferenceName = 'pm_ChangeRequestLinkType' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'BackwardCaption')
 LIMIT 1;

IF NOT check_column_exists('BackwardCaption', 'pm_ChangeRequestLinkType') THEN
ALTER TABLE pm_ChangeRequestLinkType ADD BackwardCaption VARCHAR(255);

UPDATE pm_ChangeRequestLinkType SET BackwardCaption = Caption;

UPDATE pm_ChangeRequestLinkType SET BackwardCaption = 'Блокируется' WHERE ReferenceName = 'blocks';

UPDATE pm_ChangeRequestLinkType SET BackwardCaption = 'Блокирует' WHERE ReferenceName = 'blocked';
END IF;

IF NOT check_column_exists('MinDaysInWeek', 'pm_CalendarInterval') THEN
ALTER TABLE pm_CalendarInterval ADD MinDaysInWeek INTEGER;

UPDATE pm_CalendarInterval SET MinDaysInWeek = IF(StartDateWeekday=1,7,StartDateWeekday-1);

END IF;

UPDATE attribute set AttributeType = 'DATE' WHERE ReferenceName IN ('StartDate', 'FinishDate') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Version' );

UPDATE attribute set AttributeType = 'DATE' WHERE ReferenceName IN ('StartDate', 'FinishDate') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Release' );

UPDATE attribute SET AttributeType = 'DATE' WHERE ReferenceName IN ('StartDate', 'FinishDate') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

ALTER TABLE pm_Task MODIFY StartDate TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE pm_Task MODIFY FinishDate TIMESTAMP NULL DEFAULT NULL;

UPDATE pm_Task SET FinishDate = NULL WHERE FinishDate = 0;

UPDATE attribute SET AttributeType = 'DATE' WHERE ReferenceName IN ('StartDate', 'FinishDate') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_Task');

ALTER TABLE pm_ChangeRequest MODIFY StartDate TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE pm_ChangeRequest MODIFY FinishDate TIMESTAMP NULL DEFAULT NULL;

UPDATE pm_ChangeRequest SET FinishDate = NULL WHERE FinishDate = 0;

UPDATE attribute SET AttributeType = 'DATE' WHERE ReferenceName IN ('StartDate', 'FinishDate') AND entityId IN (SELECT entityId FROM entity WHERE ReferenceName = 'pm_ChangeRequest');


INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
SELECT NOW(), NOW(), NULL,'Модуль','Module','VARCHAR',NULL,'N','N',e.entityId,20
  FROM entity e WHERE e.ReferenceName = 'pm_CustomReport' 
   AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Module')
 LIMIT 1;

IF NOT check_column_exists('Module', 'pm_CustomReport') THEN
ALTER TABLE pm_CustomReport ADD Module VARCHAR(128);
END IF;



--
--
--
-- end of upgrade script
--
--

INSERT INTO cms_Update(Caption,RecordCreated,RecordModified) VALUES(TRIM(update_version), NOW(), NOW());

END$$

DELIMITER ;

