<?php

class StaticPageTable extends PageTable
{
 	function __construct( $object )
 	{
 		parent::__construct( $object );
 	}

	function IsNeedToAdd() { return false; }
	
	function IsNeedToDelete() { return false; }
}
