<?php
include_once "ModifiableAttributeRegistry.php";

class ModifiableAttribute extends Metaobject
{
	public function __construct() {
		parent::__construct('entity', new ModifiableAttributeRegistry($this));
	}
}