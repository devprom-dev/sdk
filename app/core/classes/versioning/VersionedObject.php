<?php
include "VersionedObjectRegistry.php";

class VersionedObject extends MetaobjectCacheable
{
	function __construct() {
		parent::__construct('entity', new VersionedObjectRegistry($this));
	}
}