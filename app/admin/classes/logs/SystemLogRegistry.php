<?php

include_once SERVER_ROOT_PATH."cms/c_iterator_file.php";

class SystemLogRegistry extends ObjectRegistrySQL
{
	public function getAll()
	{
		$data = array();
		
		$it = new IteratorFile( $this->getObject(), SERVER_LOGS_PATH, 'log' );

		while( !$it->end() )
		{
			$data[] = array (
					'cms_BackupId' => $it->get('name'),
					'Caption' => $it->get('name'),
					'BackupFileName' => $it->get('name'),
					'Size' => $it->get('size')
			);
			
			$it->moveNext();
		}
		
		return $this->createIterator($data);
	}
}