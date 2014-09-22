<?php

include SERVER_ROOT_PATH."plugins/sourcecontrol/classes/LoggerAppenderText.php";

class SubversionDebugTable extends PageTable
{
    function getTemplate()
    {
        return "../../plugins/sourcecontrol/templates/SubversionDebugTable.php";
    }
    
    function getRenderParms( $parms )
    {
        $object = $this->getObject();
        
        $connectors = array();
        
        $object_it = $object->getExact($_REQUEST['connection']);
        
        while ( !$object_it->end() )
        {
			$connectors[] = $this->getConnectorData($object_it);
        	
            $object_it->moveNext();
        }
        
        return array_merge( parent::getRenderParms( $parms ), array (
                'connectors' => $connectors
        ));
    }

    private function getConnectorData( $object_it )
    {
     	try
 		{
	 		$logger = Logger::getLogger('SCM');
	 		
			$layout = new LoggerLayoutPattern();
			$layout->setConversionPattern("%d %m %l %n");
			$layout->activateOptions();
	
			$appender = new LoggerAppenderText('scm-debugger');
			$appender->setLayout($layout);
			$appender->setThreshold('all');
			$appender->activateOptions();
			
			$logger->addAppender($appender);
			$logger->setLevel('debug');

            $connector = $object_it->getConnector();

			$connector->getRecentLogs();

            $credentials = $connector->getCredentials();

            $data = array (
                'url' => $credentials->getUrl(),
                'path' => $credentials->getPath(),
                'login' => $credentials->getLogin(),
            	'debug' => $appender->getText()
            );

			return $data;
 		}
 		catch( Exception $e )
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 			
 			return array();
 		}
    }
}