<?php
include "BackupFileSystemRegistry.php";

class Backup extends Metaobject
{
	function __construct( $registry = null ) {
		parent::__construct('cms_Backup', $registry);
		$this->defaultsort = 'RecordModified DESC';
	}
	
	function delete( $id, $record_version = ''  )
	{
		$it = $this->getExact( $id );
			
		if ( $it->getId() != '' ) {
			unlink(SERVER_BACKUP_PATH.$it->get('BackupFileName'));
			FileSystem::rmdirr(SERVER_BACKUP_PATH.basename($it->get('BackupFileName'), '.zip'));

            DAL::Instance()->Query(
                "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, ObjectId, ObjectClass) ".
                " SELECT NOW(), NOW(), " . $it->getId() . ", 'Backup' "
            );
		}
		
		return parent::delete( $id );
	}
}

