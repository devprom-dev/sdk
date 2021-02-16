<?php
include_once "Lock.php";

class LockFileSystem extends Lock
{
	static $cache_dir = '';
    static $is_windows = null;
    static $waitings = 15;
	
    public function __construct ( $name )
    {
        self::$cache_dir = sys_get_temp_dir();
        if ( is_null(self::$is_windows) ) {
            self::$is_windows = EnvironmentSettings::getWindows();
            self::$waitings = self::$is_windows ? 3 : 15;
        }
        $this->file_name = rtrim(self::$cache_dir, '\\/').'/'.md5($name.INSTALLATION_UID).'.lock';
    }

    public function Lock() {
        @file_put_contents( $this->file_name, time() );
    }
    
    public function Release() {
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
        $skip = self::$waitings;
        while( $this->Locked($timeout) && !connection_aborted() )
        {
            time_nanosleep(0, 500000000);

        	if ( $skip >= self::$waitings )
        	{
                echo " ";
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