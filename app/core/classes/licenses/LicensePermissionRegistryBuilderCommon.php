<?php
include_once "LicensePermissionRegistryBuilder.php";

class LicensePermissionRegistryBuilderCommon extends LicensePermissionRegistryBuilder
{
	public function build( LicensePermissionRegistry & $registry )
    {
        $registry->add(
            'core', text(2643), array(), true
        );
    }
}
