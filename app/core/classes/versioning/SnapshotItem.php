<?php
include "SnapshotItemIterator.php";

class SnapshotItem extends Metaobject
{
 	function SnapshotItem() {
		parent::Metaobject('cms_SnapshotItem');
	}
	
	function createIterator() {
		return new SnapshotItemIterator( $this );
	}

	function getDisplayName() {
		return translate('Версия');
	}
}