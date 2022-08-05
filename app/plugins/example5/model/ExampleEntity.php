<?php
include 'ExampleEntityIterator.php';

/*
    CREATE TABLE `pm_ExampleEntity` (
        `pm_ExampleEntityId` int(11) NOT NULL AUTO_INCREMENT,
        `RecordCreated` datetime DEFAULT NULL,
        `RecordModified` datetime DEFAULT NULL,
        `OrderNum` int(11) DEFAULT NULL,
        `Caption` mediumtext,
        `URL` varchar(32) DEFAULT NULL,
        `VPD` varchar(32) DEFAULT NULL,
        `RecordVersion` int(11) DEFAULT '0',
        PRIMARY KEY (`pm_ExampleEntityId`),
        UNIQUE KEY `XPKpm_ExampleEntity` (`pm_ExampleEntityId`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    INSERT INTO entity (Caption, ReferenceName, packageId, IsOrdered, OrderNum, IsDictionary)
    SELECT 'Пример сущности', 'pm_ExampleEntity', 7, 'Y', 10, 'N'
    FROM (SELECT 1) t WHERE NOT EXISTS (SELECT 1 FROM entity WHERE ReferenceName = 'pm_ExampleEntity');

    INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
    SELECT NOW(), NOW(), NULL,'Название','Caption','VARCHAR',NULL,'Y','Y',e.entityId,10
    FROM entity e WHERE e.ReferenceName = 'pm_ExampleEntity' AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Caption')
    LIMIT 1;

    INSERT INTO attribute ( RecordCreated,RecordModified,VPD,`Caption`,`ReferenceName`,`AttributeType`,`DefaultValue`,`IsRequired`,`IsVisible`,`entityId`,`OrderNum` )
    SELECT NOW(), NOW(), NULL,'URL','URL','VARCHAR',NULL,'N','Y',e.entityId,20
    FROM entity e WHERE e.ReferenceName = 'pm_ExampleEntity' AND NOT EXISTS (SELECT 1 FROM attribute a WHERE a.entityId = e.entityId AND a.ReferenceName = 'Type')
    LIMIT 1;
*/

class ExampleEntity extends Metaobject
{
    public function __construct()
    {
        parent::__construct('pm_ExampleEntity');
        $this->setSortDefault(array(
            new SortOrderedClause()
        ));
    }

    public function createIterator() {
        return new ExampleEntityIterator($this);
    }

    public function getPage() {
        return getSession()->getApplicationUrl($this) . 'module/example5/list?';
    }
}