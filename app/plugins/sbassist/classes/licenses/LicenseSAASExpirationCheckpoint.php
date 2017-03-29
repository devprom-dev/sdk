<?php

include_once SERVER_ROOT_PATH.'admin/classes/CheckpointEntryDynamic.php';

class LicenseSAASExpirationCheckpoint extends CheckpointEntryDynamic
{
	private $days_left_to_warning = 7;
	private $trial_period_length = 14;
	
	function execute()
	{
		$license_it = getFactory()->getObject('LicenseInstalled')->getAll();

		$left_days = $license_it->get('LeftDays');
		$this->setValue( $left_days >= $this->days_left_to_warning ? "1" : "0" );

		if ( $license_it->get('LicenseValue') > $this->trial_period_length )
		{
			if ( $left_days == '' ) return;
			if ( $left_days >= 0 && $left_days < $this->days_left_to_warning && $this->timeToSendLicenseNotification() ) {
				$this->sendLicenseNotification($left_days);
			}
		}
		if ( $left_days <= -7 && $this->timeToSendTerminationNotification() ) {
			$this->sendTerminationNotification($left_days);
		}
	}
	
	function getTitle()
	{
		return text('sbassist20');
	}
	
	function getDescription()
	{
		return $this->getValue() == "0" ? text('sbassist35') : text('sbassist18');
	}
	
    function notificationRequired()
    {
    	return false;
    }
	
	protected function timeToSendLicenseNotification()
	{
		$timeline = DOCUMENT_ROOT.'conf/licenselastnotification.dat';
		
		$last_time = file_exists($timeline) ? file_get_contents($timeline) : 0;
		 
		if ( time() - $last_time > 172800 )
		{ 
			file_put_contents($timeline, time());
			
			return true;
		}
		
		return false;
	}
	
	protected function timeToSendTerminationNotification()
	{
		$timeline = DOCUMENT_ROOT.'conf/terminationlastnotification.dat';
		
		$last_time = file_exists($timeline) ? file_get_contents($timeline) : 0;
		 
		if ( time() - $last_time > 7 * 24 * 60 * 60 )
		{ 
			file_put_contents($timeline, time());
			
			return true;
		}
		
		return false;
	}
	
	protected function sendLicenseNotification( $left_days ) 
	{
	    $mail = new HtmlMailbox;

	    $emails = getFactory()->getObject('User')->getRegistry()->Query(
	    	array (
	    			new FilterAttributePredicate('IsAdmin', 'Y')
	    	)
	    )->fieldToArray('Email');
	    
	    foreach( $emails as $email ) $mail->appendAddress($email);
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/sbassist/resources/ru/license.html');
	    
	    $body = preg_replace('/_url_/', EnvironmentSettings::getServerUrl().'/admin/license/', $body);
	    
	    switch( $left_days )
	    {
	        case 1:
	        	$days_text = '1 day';
	        	break;
	        default:
	        	$days_text = $left_days.' days';
	    }
	    
	    $body = preg_replace('/_days_/', $days_text, $body);
		
	    $mail->setBody($body);
	    
	    $mail->setSubject(text('sbassist43'));
	    $mail->setFrom(str_replace('%1', SAAS_SENDER, text('sbassist44')));
	    	
	    $mail->send();
	}
	
	protected function sendTerminationNotification( $left_days ) 
	{
	    $mail = new HtmlMailbox;

	    $emails = getFactory()->getObject('User')->getRegistry()->Query(
	    	array (
	    			new FilterAttributePredicate('IsAdmin', 'Y')
	    	)
	    )->fieldToArray('Email');
	    
	    foreach( $emails as $email ) $mail->appendAddress($email);
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/sbassist/resources/ru/termination.html');
	    
	    $body = preg_replace('/_url_/', EnvironmentSettings::getServerUrl().'/admin/license/', $body);

	    $days_due_terminate = max(14 + $left_days, 0);
	    
	    switch( $days_due_terminate )
	    {
	        case 1:
	        	$days_text = '1 day';
	        	break;
	        default:
	        	$days_text = $days_due_terminate.' days';
	    }
	    
	    $body = preg_replace('/_days_/', $days_text, $body);
		
	    $mail->setBody($body);
	    
	    $mail->setSubject(text('sbassist45'));
	    $mail->setFrom(str_replace('%1', SAAS_SENDER, text('sbassist44')));
	    	
	    $mail->send();
	}	
}
