<?php

class SetupPhpOpCache extends Installable 
{
    function skip()
    {
        return !$this->checkWindows() || !$this->checkPHPVersionNoLessThan('5.5.0');
    }

    function check()
    {
    	return true;
    }
    
    function getRequiredExtensions()
    {
        return array ('php_opcache.dll');
    }
    
    function install()
    {
        $content = $this->getPhpIniContent();
        
        foreach( $this->getRequiredExtensions() as $extension )
        {
            $extension_regexp = preg_replace('/\./', '\.', $extension);
             
            if ( preg_match('/zend_extension\s*=\s*'.$extension.'/', $content, $matches) ) continue;
            
            $content = preg_replace('/extension\s*=\s*php_gd2\.dll/', 
                    'extension=php_gd2.dll'.PHP_EOL.'zend_extension='.$extension.PHP_EOL, $content); 
        }

        if ( !preg_match('/opcache\.memory_consumption/i', $content, $matches) )
        {
            $content = preg_replace('/track_vars=true/', 
		                    'track_vars=true'.PHP_EOL.
		            		'opcache.memory_consumption=100'.PHP_EOL. 
		            		'opcache.interned_strings_buffer=8'.PHP_EOL. 
		            		'opcache.max_accelerated_files=12000'.PHP_EOL. 
		            		'opcache.revalidate_freq=60'.PHP_EOL, 
            						$content); 
        }
        
        $this->writePhpIniContent($content);
        
        return true;
    }
    
    function getPhpIniContent()
    {
        return file_get_contents(php_ini_loaded_file());
    }
    
    function writePhpIniContent( $content )
    {
        file_put_contents(php_ini_loaded_file(), $content);
    }
}
