<?php

include "ArtefactTypeList.php";

class ArtefactTypeTable extends PMPageTable
{
	function getList()
	{
		return new ArtefactTypeList( $this->getObject() );
	}

	function getFilterActions()
	{
	    return array();
	}
} 