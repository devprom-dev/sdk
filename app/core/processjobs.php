<?php

include dirname(__FILE__).'/../common.php'; 

$urls = array(
        EnvironmentSettings::getServerUrl(),
        EnvironmentSettings::getServerUrlByIpAddress(),
        EnvironmentSettings::getServerUrlLocalhost()
);

try 
{
	$log = Logger::getLogger('Commands');
}
catch( Exception $e)
{
    echo $e->getMessage();
}

try 
{
	foreach( $urls as $url )
	{
		if ( is_object($log) ) $log->info( 'Running background tasks on url: '.$url);
	    
        $ctx = stream_context_create(array( 
            'http' => array( 
                'timeout' => 55
                ) 
            ) 
        ); 	
	    
        $result = file_get_contents( $url.'/tasks/command.php?class=runjobs', false, $ctx );
        
        if ( $result === false )
        {
            $info = error_get_last();
            
            if ( is_object($log) ) $log->error( 'Couldn\'t run background tasks on url: '.$url.' ('.$info['message'].')');
        }
        else
        {
            echo $result;
            
            break; // stop trying to execute background tasks
        }
	}
}
catch( Exception $e)
{
    echo $e->getMessage();
}
