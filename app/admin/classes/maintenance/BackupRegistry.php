<?php

include_once SERVER_ROOT_PATH.'cms/c_iterator_file.php';

class BackupRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		$it = new IteratorFile( $this->getObject(), SERVER_BACKUP_PATH, 'zip' );
		
		$it->sortCreatedDesc();

		return $it;
	}
}
