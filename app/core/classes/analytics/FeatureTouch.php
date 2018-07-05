<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class FeatureTouch
{
	protected static $singleInstance = null;
    protected $data = array();

	public static function Instance()
	{
		if ( is_object(static::$singleInstance) ) return static::$singleInstance;

        $data = @file_get_contents(self::getFileName());
		if ( $data != '' ) {
			static::$singleInstance = @unserialize($data);
		}
		if ( !is_object(static::$singleInstance) ) {
			static::$singleInstance = new static();
		}
		return static::$singleInstance;
	}

	function __destruct()
	{
	    return;

        @mkdir(dirname(self::getFileName()), 0777, true);
        @file_put_contents(self::getFileName(), serialize($this));
	}

	public function __sleep() {
		return array('data');
	}

	public function touch( $widget ) {
        if ( $widget == '' ) return;
        $this->data[$widget]++;
    }

	protected static function getFileName() {
	    $id = session_id();
		return SERVER_FILES_PATH.'analytics/'.($id == "" ? "0" : $id);
	}
}