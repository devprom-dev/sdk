<?php

class MetricsServer
{
	private static $startTime = null;
	
	public function Start()
	{
		if ( !is_null($this->startTime) ) return;
		
		$this->startTime = microtime(true);
	}
	
	public function getDuration()
	{
		return round(microtime(true) - $this->startTime, 3);
	}

    protected static $singleInstance = null;
    
    public static function Instance()
    {
        if ( is_object(static::$singleInstance) ) return static::$singleInstance;
        
        static::$singleInstance = new static();

        return static::$singleInstance;
    }
    
    private function __construct() {}
}