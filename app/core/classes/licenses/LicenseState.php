<?php

include "LicenseStateIterator.php";
include "LicenseStateRegistry.php";

class LicenseState extends Metaobject
{
	public function __construct()
	{
		parent::__construct('cms_License', new LicenseStateRegistry());
	}

	public function createIterator()
	{
		return new LicenseStateIterator($this);
	}
}