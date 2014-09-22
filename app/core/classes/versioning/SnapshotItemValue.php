<?php

include "SnapshotItemValueIterator.php";

class SnapshotItemValue extends Metaobject
{
 	function SnapshotItemValue() 
 	{
		parent::Metaobject('cms_SnapshotItemValue');
	}
	
	function createIterator() 
	{
		return new SnapshotItemValueIterator( $this );
	}
}
