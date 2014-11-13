<?php

include_once SERVER_ROOT_PATH.'admin/classes/CheckpointEntryDynamic.php';

class LicenseSAASExpirationCheckpoint extends CheckpointEntryDynamic
{
	private $days_left_to_warning = 7;
	
	function execute()
	{
		$left_days = getFactory()->getObject('LicenseInstalled')->getAll()->get('LeftDays');
		
		$this->setValue( $left_days >= $this->days_left_to_warning ? "1" : "0" );

		if ( $left_days == '' ) return;
		
		if ( $left_days >= 0 && $left_days < $this->days_left_to_warning && $this->timeToSendLicenseNotification() )
		{
			$this->sendLicenseNotification($left_days);
		}

		if ( $left_days < 0 && $this->timeToSendTerminationNotification() )
		{
			$this->sendTerminationNotification($left_days);
		}
	}
	
	function getTitle()
	{
		return text('saasassist20');
	}
	
	function getDescription()
	{
		return $this->getValue() == "0" ? text('saasassist35') : text('saasassist18');
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
		 
		if ( time() - $last_time > 432000 )
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
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/saasassist/resources/license.html');
	    
	    $body = preg_replace('/_url_/', EnvironmentSettings::getServerUrl().'/admin/license/', $body);
	    
	    switch( $left_days )
	    {
	        case 1:
	        	$days_text = '1 день';
	        	break;
	        	
	        case 2:
	        case 3:
	        case 4:
	        	$days_text = $left_days.' дн€';
	        	break;

	        default:
	        	$days_text = $left_days.' дней';
	    }
	    
	    $body = preg_replace('/_days_/', $days_text, $body);
		
	    $mail->setBody($body);
	    
	    $mail->setSubject( 'ќкончание периода использовани€ Devprom.ALM' );
	    $mail->setFrom("Devprom Software <".SAAS_SENDER.">");
	    	
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
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/saasassist/resources/termination.html');
	    
	    $body = preg_replace('/_url_/', EnvironmentSettings::getServerUrl().'/admin/license/', $body);

	    $days_due_terminate = max(7 + $left_days, 0);
	    
	    switch( $days_due_terminate )
	    {
	        case 1:
	        	$days_text = '1 день';
	        	break;
	        	
	        case 2:
	        case 3:
	        case 4:
	        	$days_text = $days_due_terminate.' дн€';
	        	break;

	        default:
	        	$days_text = $days_due_terminate.' дней';
	    }
	    
	    $body = preg_replace('/_days_/', $days_text, $body);
		
	    $mail->setBody($body);
	    
	    $mail->setSubject( '”даление вашего экземпл€ра Devprom.ALM' );
	    $mail->setFrom("Devprom Software <".SAAS_SENDER.">");
	    	
	    $mail->send();
	}	
}
