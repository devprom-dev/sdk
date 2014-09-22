<?php

use \InlineStyle\InlineStyle;

class MailBox
{
 	var $to_address, $body, $from_address, $subject;
	
	function MailBox() {
		$this->to_address = array();
	}
	
	function appendAddress( $address ) {
		array_push($this->to_address, $address);
	}
	
	function setBody( $body ) {
		$this->body = $body;
	}
	
	function getBody()
	{
		return $this->body;
	}

	function setSubject( $subject )	{
		$this->subject = $this->encode($subject);
	}

	function setFrom( $from_address )	{
		$this->from_address = $from_address;
	}
	
	function send()
	{
		$configuration = getConfiguration();
		$max_recipients = $configuration->getMaxEmailRecipients();
		
		if ( $max_recipients > 0 )
		{
			if ( count($this->to_address) > $max_recipients )
			{
				return false;
			}
		}

 		$queue = new Metaobject('EmailQueue');
 		$address = new Metaobject('EmailQueueAddress');

 		$queue_id = $queue->add_parms(
 			array ( 'FromAddress' => $this->from_address,
 				    'Caption' => $this->subject,
 				    'Description' => $this->body,
 				    'MailboxClass' => get_class($this) )
 			);

		for ( $i = 0; $i < count($this->to_address); $i++ )
		{
	 		$address->add_parms(
	 			array ( 'EmailQueue' => $queue_id,
	 				    'ToAddress' => $this->to_address[$i] )
	 			);
		}

		return $queue_id;	
	}
	
	function getContentType() {
		return "Content-Type: text/plain; charset=windows-1251";
	}
	
	function encode( $text ) {
		return '=?Windows-1251?B?'.base64_encode($text).'?=';
	}
	
	function encodeAddress( $address ) {
		list($display, $email) = preg_split('/</',$address);
		
		if($email != '') {
			return $this->encode($display).'<'.$email;
		}
		else {
			return $address;
		}
	}

	function addressUpdateEmail( $address, $email ) 
	{
		return preg_replace('/\<([^>])+>/', '<'.$email.'>', $address); 
	}
 }
 
 class HtmlMailBox extends MailBox
 {
	function getContentType() 
	{
		return "MIME-Version: 1.0".Chr(13).Chr(10)."Content-type: text/html; charset=utf-8";
	}
	
	function encode( $text ) {
	    return '=?UTF-8?B?'.base64_encode(IteratorBase::wintoutf8($text)).'?=';
	}

	function setBody( $body ) 
	{
		$this->body = $this->applyStyles(
		    '<html>'.PHP_EOL.
		    '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>'.PHP_EOL.
		    '<body>'.IteratorBase::wintoutf8($body).'</body>'.PHP_EOL.
		    '</html>'
		);
	}
	
	function applyStyles( $html )
	{
	    $was_state = libxml_use_internal_errors(true);
	    
	    $htmldoc = new \InlineStyle\InlineStyle($html);
	    
	    $htmldoc->applyStylesheet(file_get_contents(SERVER_ROOT_PATH.'styles/legacy/style_email.css'));
	    
        $html = $htmldoc->getHTML();
        
        libxml_clear_errors();

        libxml_use_internal_errors($was_state);
        
        return $html;
	}
 }
 