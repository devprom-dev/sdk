<?php
include "TextTemplateEntityRegistry.php";

class TextTemplateEntity extends Metaobject
{
	public function __construct() {
		parent::__construct('entity', new TextTemplateEntityRegistry($this));
	}
}