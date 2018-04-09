<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

abstract class CacheEngine
{
    protected static $singleInstance = null;
	private $readonly = false;

    static function Instance() {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        return static::$singleInstance = new static();
    }

    function __sleep() {
        return array ();
    }

	public function setReadonly( $flag = true )
	{
		$this->readonly = $flag;
	}
	
	public function getReadonly()
	{
		return $this->readonly;
	}

	function reset( $key, $path = '' ) {
		$this->set($key, '', $path);
	}

    abstract function get( $key, $path = '' );
    abstract function set( $key, $value, $path = '' );
    abstract function invalidate( $path = '' );

    protected function __construct() {
    }
}
