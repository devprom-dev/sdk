<?php

include ('SystemDictionaryItemsList.php');

class SystemDictionaryItemsTable extends PageTable
{
	var $object;

	function __construct ( $object )
	{
		$this->object = $object;

		parent::__construct($object);
	}

	function getObject()
	{
		return $this->object;
	}

	function getList()
	{
		return new SystemDictionaryItemsList( $this->getObject() );
	}

    function getCaption()
    {
        return translate($this->object->getDisplayName());
    }
	
	function getFilterActions()
	{
		return array();
	}
	
	function getBulkActions()
	{
		return array();
	}
}
