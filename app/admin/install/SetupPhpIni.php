<?php

class SetupPhpIni extends Installable 
{
    function check()
    {
        return true;
    }

    function getRequiredExtensions()
    {
        return array ('php_fileinfo.dll', 'php_pdo_mysql.dll', 'php_imap.dll');
    }
    
    function install()
    {
        if ( !$this->checkWindows() ) return true;
        
        $content = $this->getPhpIniContent();
        
        foreach( $this->getRequiredExtensions() as $extension )
        {
            $extension_regexp = preg_replace('/\./', '\.', $extension);
             
            if ( preg_match('/extension\s*=\s*'.$extension.'/', $content, $matches) ) continue;
            
            $content = preg_replace('/extension\s*=\s*php_gd2\.dll/', 
                    'extension=php_gd2.dll'.PHP_EOL.'extension='.$extension.PHP_EOL, $content); 
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
