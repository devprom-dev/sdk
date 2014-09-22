<?php

include "LicenseRegistry.php";

class License extends Metaobject
{
	function __construct()
	{
		parent::__construct('cms_License', new LicenseRegistry($this));
	}
}
