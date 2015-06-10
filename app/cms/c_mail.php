<?php

use \InlineStyle\InlineStyle;

class MailBox
{
 	var $to_address, $body, $from_address, $subject;
	
	function __construct()
	{
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
	
	function setFromUser( $user_it )
	{
		$this->from_address = $this->quoteEmail($user_it->get('Caption')).' <'.$user_it->get('Email').'>';
	}
	
 	private function quoteEmail( $email )
 	{
 		if ( strpos($email,",") !== false ) {
 			$email = '"'.trim($email, '"').'"'; 
 		}
 		return $email;
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
		return "Content-Type: text/plain; charset=".APP_ENCODING;
	}
	
	function encode( $text ) {
		return '=?'.APP_ENCODING.'?B?'.base64_encode($text).'?=';
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
 	private $boundary = '';
 	
 	function __construct()
 	{
 		parent::__construct();
 		
 		$this->boundary = "devprom-5446b4677d9475446b481adbb3";
 	}
 	
	function getContentType() 
	{
		return "MIME-Version: 1.0\r\n".
			   "Content-Type: multipart/alternative; boundary=\"".$this->boundary."\"";
	}
	
	function encode( $text ) {
	    return '=?UTF-8?B?'.base64_encode(IteratorBase::wintoutf8($text)).'?=';
	}

	function setBody( $body ) 
	{
		$this->body = "\r\n\r\n--" . $this->boundary . "\r\n";
		$this->body .= "Content-Type: text/plain; charset=\"utf-8\"\r\n\r\n";
				
		$texted = strip_tags(html_entity_decode($body, ENT_COMPAT | ENT_HTML401, APP_ENCODING));
		
		$texted = preg_replace('/\s{2,}/', PHP_EOL, $texted);
		$texted = preg_replace('/[\r\n]{2,}/', PHP_EOL.PHP_EOL, $texted);
		
		$this->body .= IteratorBase::wintoutf8($texted);
		
		$this->body .= "\r\n\r\n--" . $this->boundary . "\r\n";
		$this->body .= "Content-Type: text/html; charset=\"utf-8\"\r\n\r\n";
		
		$this->body .= $this->textWrap($this->applyStyles(
		    '<html>'.PHP_EOL.
		    '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>'.PHP_EOL.
		    '<body>'.IteratorBase::wintoutf8(preg_replace('/[\r\n]+/', '', $body)).'</body>'.PHP_EOL.
		    '</html>'
		));

		$this->body .= "\r\n\r\n--" . $this->boundary . "--";
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
	
 	function textWrap($text) { 
        $new_text = ''; 
        $text_1 = explode('>',$text); 
        $sizeof = sizeof($text_1); 
        for ($i=0; $i<$sizeof; ++$i) { 
            $text_2 = explode('<',$text_1[$i]); 
            if (!empty($text_2[0])) { 
                $new_text .= wordwrap($text_2[0], 255, PHP_EOL, true); 
            } 
            if (!empty($text_2[1])) { 
                $new_text .= '<' . wordwrap($text_2[1], 255, PHP_EOL, true) . '>';    
            } 
        } 
        return $new_text; 
    } 	
 }
 