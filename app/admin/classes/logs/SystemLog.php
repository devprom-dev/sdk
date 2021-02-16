<?php

include "SystemLogRegistry.php";

class SystemLog extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_Backup', new SystemLogRegistry($this));
	}
	
	public function getExact( $id )
	{
		$it = $this->getAll();
		
		$it->moveToId( $id );
		
		return $it->getId() != '' ? $it->copy() : $this->getEmptyIterator();
	}
	
	public function getPage()
	{
		return '/admin/log/?';
	}

	function getDisplayName() {
        return text(1712);
    }
}