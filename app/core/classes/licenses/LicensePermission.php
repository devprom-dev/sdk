<?php
include "LicensePermissionRegistry.php";
include "LicensePermissionIterator.php";

class LicensePermission extends Metaobject
{
	function __construct() {
		parent::__construct('entity', new LicensePermissionRegistry($this));
	}

	function createIterator() {
        return new LicensePermissionIterator($this);
    }
}
