<?php

include_once "Lock.php";

class LockFileSystem extends Lock
{
    public function __construct ( $name )
    {
        $this->file_name = preg_replace("([^\w\s\d\-_~,;:\[\]\(\]]|[\.]{2,})", '', $name).'.lock';
    }
    
    public function Lock()
    {
        file_put_contents( $this->getLockFileName(), time() );
    }
    
    public function Release()
    {
        @unlink( $this->getLockFileName() );
    }

    public function Locked( $timeout = 10 )
    {
        $value = $this->getLockTime();
        
        if ( !is_numeric($value) || $value < 1 ) return false;
        
        return $value > 0 && abs(time() - $value) < $timeout;
    }
    
    public function getLockTime()
    {
        return file_exists($this->getLockFileName()) ? file_get_contents( $this->getLockFileName() ) : 0;
    }
    
    public function LockAndWait( $timeout )
    {
        $this->Lock();
        
        return $this->Wait( $timeout );
    }
    
    public function Wait( $timeout )
    {
    	$skip = 0;
    	
        while( $this->Locked($timeout) && !connection_aborted() )
        {
        	sleep(1);
        	
        	if ( $skip > 3 )
        	{
        		// check client connection is active (using connection_aborted func.)
	        	echo(" ");
	
	        	ob_flush();
	        	flush();

	        	// send data once in 3 seconds
	        	$skip = 0;
        	}
        	
        	$skip++;
        }
        
        DAL::Instance()->Reconnect();
    }
    
    /* private members */

    private $file_name;
    
    private function getLockFileName()
    {
    	$dir_name = sys_get_temp_dir().'/'.md5(DOCUMENT_ROOT);
    	
    	if ( !is_dir($dir_name) ) mkdir($dir_name, 755, true);
    	
        return $dir_name.'/'.$this->file_name;
    }
}