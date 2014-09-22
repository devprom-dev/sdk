<?php

include "ScrumList.php";

class ScrumTable extends PMPageTable
{
	function getList()
	{
		return new ScrumList( $this->getObject() );
	}
} 