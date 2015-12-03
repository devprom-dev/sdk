<?php
include "ObjectTemplateItemIterator.php";

class ObjectTemplateItem extends Metaobject
{
	public function __construct() {
		parent::__construct('cms_SnapshotItem');
	}

	function createIterator() {
		return new ObjectTemplateItemIterator($this);
	}
}