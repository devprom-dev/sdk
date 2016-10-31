<?php

class SetupMySQLIni extends Installable 
{
    function check()
    {
        return true;
    }

    function getParameters()
    {
        return array (
                'max_allowed_packet' => 67108864 /* 64M */,
        		'lower_case_table_names' => 1,
				'ft_min_word_len' => 3,
				'group_concat_max_len' => 4294967295
        );
    }
    
    function install()
    {
        $content = $this->getMySQLIniContent();
        
        // remove obsolete way to set up variables
        $content = preg_replace('/set-variable\s+=\s+/mi', '', $content); 
        
        foreach( $this->getParameters() as $param_name => $required_value )
        {
            $matches = array();
            
            if ( !preg_match('/'.$param_name.'\s*=\s*(\w+)/mi', $content, $matches) )
            {
            	$content = preg_replace('/\[mysqld\]/', '[mysqld]'.PHP_EOL.$param_name.'='.$required_value.PHP_EOL, $content);
            }
            elseif ( $matches[1] != $required_value )
            {
                $content = str_replace($matches[0], $param_name.'='.$required_value.PHP_EOL, $content); 
            }
        }
        
        $this->writeMySQLIniContent($content);
        
        return true;
    }
    
    function getMySQLIniPath()
    {
        if ( $this->checkWindows() )
        {
            return SERVER_ROOT.'/mysql/my.ini';
        }
        else
        {
            return '/etc/my.cnf';
        }
    }
    
    function getMySQLIniContent()
    {
        return file_get_contents($this->getMySQLIniPath());
    }
    
    function writeMySQLIniContent( $content )
    {
        file_put_contents($this->getMySQLIniPath(), $content);
    }
}
