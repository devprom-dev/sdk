<?php

include_once SERVER_ROOT_PATH.'core/classes/system/CacheLock.php';

class ClearCache extends Installable
{
	private $cachePath = '';

	public function __construct() {
		parent::__construct();
		$this->setCachePath(defined('CACHE_PATH') ? CACHE_PATH : SERVER_ROOT_PATH.'cache/');
	}

	public function setCachePath( $path ) {
		$this->cachePath = $path;
	}

	// checks all required prerequisites
	function check()
	{
		return true;
	}

	// skip install actions
	function skip()
	{
		return false;
	}

	// cleans after the installation script has been executed
	function cleanup()
	{
	}

	// makes install actions
	function install()
	{
		$lock = new CacheLock(120);

		if ( method_exists($this, 'info') ) {
			$this->info( 'Clear directory: '.$this->cachePath );
		}
		for( $i = 0; $i < 5; $i++ ) {
			$this->info( 'Retry: '.$i );
			$result = $this->full_delete( rtrim($this->cachePath,'/').'/' );
			if ( !$result && method_exists($this, 'info') ) {
				$this->info( 'Unable to clear cache directory: '.$this->cachePath );
			}
		}
		
		return true;
	}
	
	function full_delete( $dir, $except = array() )
	{
       	if ( !is_dir($dir) )
       	{
       		if ( method_exists($this, 'info') ) $this->info( 'Is not a directory: '.$dir );
       		return false;
       	}
       
        if ( !($dh = opendir($dir)) ) 
        {
       		if ( method_exists($this, 'info') ) $this->info( 'Unable open directory: '.$dir );
       		return false;
        }
        
		while (($file = readdir($dh)) !== false ) 
		{
           	if( $file != "." && $file != ".." && !in_array($file, $except) )
			{
				if( is_dir( $dir . $file ) )
				{
					$this->full_delete( $dir . $file . "/" );
				}
				else
				{
					@unlink( $dir . $file );
				}
			}
		}
			
		closedir( $dh );
		@rmdir( $dir );
		
		return true;
	} 	
}
