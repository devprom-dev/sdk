<?php

include "LicenseStateRegistry.php";

class LicenseState extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_License', new LicenseStateRegistry());
	}
}