<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class DeploymentState
{
    protected static $singleInstance = null;
    private $activated = false;
    private $licensed = false;

    static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        $data = @file_get_contents(self::getFileName());
        if ( $data != '' && (time()-filemtime(self::getFileName()) < 12 * 3600) ) {
            static::$singleInstance = @unserialize($data);
            if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        }
        static::$singleInstance = new static();
        static::$singleInstance->build();
        return static::$singleInstance;
    }

	static function IsInstalled() {
		return defined('DB_HOST') && DB_HOST != '?HOST';
	}
	
	static function IsScriptsCompleted() {
	    return file_exists(DOCUMENT_ROOT.'conf/logger.xml');
	}
	
	static function IsMaintained() {
	    $lock = new LockFileSystem(MAINTENANCE_LOCK_NAME);
	    return $lock->Locked(300);
	}
	
	function IsActivated() {
		return $this->activated;
	}

	function IsLicensed() {
		return $this->licensed;
	}
	
	function IsReadyToBeUsed() {
		return $this->IsLicensed();
	}

	protected function build()
    {
        $lock = new \CacheLock();

        $license_it = getFactory()->getObject('LicenseState')->getAll();
        $this->activated = $license_it->get('LicenseType') != '';
        $this->licensed = self::IsScriptsCompleted() && $license_it->get('IsValid') == 'Y';

        @mkdir(dirname(self::getFileName()), 0777, true);
        @file_put_contents(self::getFileName(), serialize($this));

        $lock->Release();
    }

	protected function getFileName() {
        return CACHE_PATH."/appcache/global/deploymentState.php";
    }

	protected function __construct() {}

    function __sleep() {
        return array(
            'activated', 'licensed'
        );
    }
}