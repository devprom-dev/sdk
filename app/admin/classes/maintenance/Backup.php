<?php
include "BackupRegistry.php";

class Backup extends MetaobjectCacheable
{
	function __construct()
	{
		parent::__construct('cms_Backup', new BackupRegistry($this));
		$this->defaultsort = 'RecordModified DESC';
	}
	
	function delete( $id, $record_version = ''  )
	{
		$it = $this->getExact( $id );
			
		if ( $it->getId() != '' ) {
			unlink(SERVER_BACKUP_PATH.$it->get('Caption'));
			FileSystem::rmdirr(SERVER_BACKUP_PATH.basename($it->get('Caption'), '.zip'));

            DAL::Instance()->Query(
                "INSERT INTO co_AffectedObjects (RecordCreated, RecordModified, ObjectId, ObjectClass) ".
                " SELECT NOW(), NOW(), " . $it->getId() . ", 'Backup' "
            );
		}
		
		$it = $this->getByRefArray( array(
		    'BackupFileName' => $it->get('Caption')
		));
		
		return $it->count() > 0 ? parent::delete( $id ) : 1;
	}
}

