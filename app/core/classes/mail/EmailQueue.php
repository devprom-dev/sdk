<?php

include "EmailQueueIterator.php";

class EmailQueue extends Metaobject
{
 	var $subject;
 	var $from_address;
 	var $body;
 	var $recipients;
 	
 	function EmailQueue() {
		parent::Metaobject('EmailQueue');
		$this->recipients = array();
	}
	
	function createIterator() {
		return new EmailQueueIterator( $this );
	}
	
	function setSubject( $subject ) 
	{
		$this->subject = $subject;
	}
	
	function setFrom( $from )
	{
		$this->from_address = $from;
	}
	
	function setBody( $body )
	{
		$this->body = $body;
	}
	
	function addRecipient( $address )
	{
		array_push($this->recipients, $address);  
	}
	
	function push()
	{
		$queue_id = $this->add_parms(
			array('Caption' => $this->subject,
				  'FromAddress' => $this->from_address,
				  'Description' => $this->body) );
				  
		$addressee = getFactory()->getObject('EmailQueueAddress');
				  
		for( $i = 0; $i < count($this->recipients); $i++ )
		{
			$addressee->add_parms(
				array('EmailQueue' => $queue_id,
					  'ToAddress' => $this->recipients[$i]) );
		}
	}
}