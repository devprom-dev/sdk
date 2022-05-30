<?php
include "SnapshotItemValueIterator.php";

class SnapshotItemValue extends Metaobject
{
 	function SnapshotItemValue() {
		parent::Metaobject('cms_SnapshotItemValue');
        $this->addAttributeGroup('Value', 'skip-mapper');
	}
	
	function createIterator() {
		return new SnapshotItemValueIterator( $this );
	}
}
