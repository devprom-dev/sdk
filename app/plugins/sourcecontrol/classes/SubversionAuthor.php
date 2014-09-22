<?php

include "SubversionAuthorRegistry.php";

class SubversionAuthor extends Metaobject
{
	public function __construct()
	{
		parent::__construct('entity', new SubversionAuthorRegistry($this) );
	}
}