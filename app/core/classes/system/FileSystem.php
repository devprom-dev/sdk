<?php

class FileSystem
{
	static public function rmdirr($dir)
	{
		if (!is_dir($dir)) return;
		
        if ($dh = opendir($dir)) 
        {
            while (($file = readdir($dh)) !== false ) 
            {
                if( $file != "." && $file != ".." )
                {
                    if( is_dir( $dir . $file ) )
                    {
                        FileSystem::rmdirr( $dir . $file . "/" );
                    }
                    else
                    {
                        unlink( $dir . $file );
                    }
                }
            }
            
            closedir($dh);
            rmdir( $dir );
       }
	}
}