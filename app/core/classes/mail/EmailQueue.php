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
}