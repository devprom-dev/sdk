<?php

include_once "Lock.php";

class LockFileSystem extends Lock
{
	static $cache_dir = '';
    static $is_windows = null;
    static $waitings = 15;
	
    public function __construct ( $name )
    {
        if ( self::$cache_dir == '' ) {
    		$dir_name = sys_get_temp_dir().'/'.md5(DOCUMENT_ROOT);
    		if ( !is_dir($dir_name) ) mkdir($dir_name, 755, true);
        	self::$cache_dir = $dir_name; 
        }
        if ( is_null(self::$is_windows) ) {
            self::$is_windows = EnvironmentSettings::getWindows();
            self::$waitings = self::$is_windows ? 3 : 15;
        }
        $this->file_name = self::$cache_dir.'/'.preg_replace("/([^\w\s\d\-_~,;:\[\]\(\]]|[\.]{2,})/", '', $name).'.lock';
    }
    
    public function Lock()
    {
        file_put_contents( $this->file_name, time() );
    }
    
    public function Release()
    {
        @unlink($this->file_name);
    }

    public function Locked( $timeout = 10 )
    {
        $value = $this->getLockTime();
        
        if ( !is_numeric($value) || $value < 1 ) return false;
        
        return $value > 0 && abs(time() - $value) < $timeout;
    }
    
    public function getLockTime()
    {
        return @file_get_contents($this->file_name);
    }
    
    public function LockAndWait( $timeout, $callable = null )
    {
        $this->Lock();
        return $this->Wait($timeout, $callable);
    }
    
    public function Wait( $timeout, $callable = null )
    {
    	$skip = 0;

        while( $this->Locked($timeout) && !connection_aborted() )
        {
            if ( self::$is_windows ) {
                sleep(1);
            } else {
                usleep(200000);
            }

        	if ( $skip > self::$waitings )
        	{
        		// check client connection is active (using connection_aborted func.)
	        	echo(" ");
	        	ob_flush();
	        	flush();

	        	// send data once in 3 seconds
	        	$skip = 0;

	        	if ( !is_null($callable) && is_callable($callable) ) {
	        		$result = call_user_func_array($callable, array());
	        		if ( $result ) break;
	        	}
        	}

        	$skip++;
        }
        
        DAL::Instance()->Reconnect();
    }
    
    /* private members */

    private $file_name;
}