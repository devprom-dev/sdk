<?php

include "SystemDictionaryRegistry.php";

class SystemDictionary extends Metaobject
{
	public function __construct()
	{
		parent::__construct('entity', new SystemDictionaryRegistry());
	}
}