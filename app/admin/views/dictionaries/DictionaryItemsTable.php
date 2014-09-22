<?php

include ('DictionaryItemsList.php');

class DictionaryItemsTable extends PageTable
{
	var $object;

	function DictionaryItemsTable ( $object )
	{
		$this->object = $object;

		parent::PageTable();
	}

	function getObject()
	{
		return $this->object;
	}

	function getList()
	{
		return new DictionaryItemsList( $this->getObject() );
	}

    function getCaption()
    {
        return translate($this->object->getDisplayName());
    }
	
	function getFilterActions()
	{
		return array();
	}
}
