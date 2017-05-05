<?php

class CacheParameters extends Installable
{
	// checks all required prerequisites
	function check() {
		return true;
	}

	// skip install actions
	function skip() {
		return false;
	}

	// cleans after the installation script has been executed
	function cleanup() {
	}

	// makes install actions
	function install()
	{
		$_SERVER['APP_VERSION'] = getFactory()->getObject('cms_Update')->getLatest()->getDisplayName();
		$_SERVER['APP_IID'] = INSTALLATION_UID;

        $license_it = getFactory()->getObject('LicenseInstalled')->getAll();
        $_SERVER['LICENSE'] = $license_it->getName();
        $_SERVER['LICENSE_TYPE'] = $license_it->get('LicenseType');

		file_put_contents( DOCUMENT_ROOT.'conf/settings.php',
			"<?php
			\$_SERVER['APP_VERSION'] = '".$_SERVER['APP_VERSION']."';
			\$_SERVER['APP_IID'] = '".$_SERVER['APP_IID']."';
			\$_SERVER['LICENSE'] = '".$_SERVER['LICENSE']."';
			\$_SERVER['LICENSE_TYPE'] = '".$_SERVER['LICENSE_TYPE']."';
			"
		);

		return true;
	}
}
