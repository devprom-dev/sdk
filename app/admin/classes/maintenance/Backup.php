<?php

include "BackupRegistry.php";

class Backup extends MetaobjectCacheable
{
	function __construct()
	{
		parent::__construct('cms_Backup', new BackupRegistry($this));
		
		$this->defaultsort = 'RecordModified DESC';
	}
	
	function delete( $id )
	{
		$it = $this->getExact( $id );
			
		if ( $it->getId() != '' )
		{
			unlink(SERVER_BACKUP_PATH.$it->get('Caption'));
			
			FileSystem::rmdirr(SERVER_BACKUP_PATH.basename($it->get('Caption'), '.zip'));
		}
		
		$it = $this->getByRefArray( array(
		    'Caption' => $it->get('Caption')
		));
		
		return $it->count() > 0 ? parent::delete( $id ) : 0;
	}
}

