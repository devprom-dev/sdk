<?php

include_once "ExceptionHandlerListener.php";

class ExceptionHandlerListenerLogger extends ExceptionHandlerListener
{
	public function handle( $data, $code )
	{
		try
		{
			\Logger::getLogger('System')->error(print_r($data, true));
		}
		catch(Exception $e)
		{
			error_log('Unhandled exception: '.print_r($data, true));
		}
	}
}