<?php
include "TextTemplateEntityRegistry.php";

class TextTemplateEntity extends MetaobjectCacheable
{
	public function __construct() {
		parent::__construct('entity', new TextTemplateEntityRegistry($this));
	}
}