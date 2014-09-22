<?php

include_once "ExceptionHandlerListener.php";

class ExceptionHandlerListenerRaygun extends ExceptionHandlerListener
{
	public function handle( $data, $code )
	{
	    try
	    {
	    	ob_start();
	    	
	       	$client = new \Raygun4php\RaygunClient("D/50Z9SLGuKFAIZT+RKPBg==");
	        	
	       	set_error_handler(function() {});
	       	
	      	$client->SendError(
	      			$data['error']['errno'], 
	      			$data['error']['message'], 
	      			$data['error']['file'],
	      			$data['error']['errline'],
	      			null,
	      			$data
			);
	      	
	      	restore_error_handler();
	      	
	      	$message = ob_get_contents();
	      	
	      	if ( $message != "" ) Logger::getLogger('System')->info(ob_get_contents());
	      	
	      	ob_end_clean();
	    }
	    catch( Exception $e )
	    {
	    	try
	    	{
	    		Logger::getLogger('System')->error($e->getMessage());
	    	}
	    	catch( Exception $e )
	    	{
	    		error_log('Unable initialize logger: '.$e->getMessage());
	    	}
	    }
	}
}