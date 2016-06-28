<?php

use \InlineStyle\InlineStyle;
include_once SERVER_ROOT_PATH.'core/classes/html/HtmlImageConverter.php';

class MailBox
{
 	var $to_address, $body, $from_address, $subject;
	private $mailer_settings = null;
	
	function __construct()
	{
		$this->to_address = array();
		$this->mailer_settings = getFactory()->getObject('MailerSettings')->getAll();
	}
	
	function appendAddress( $address )
	{
		array_push($this->to_address, $address);
	}
	
	function setBody( $body ) {
		$this->body = $body;
	}
	
	function getBody()
	{
		return $this->body;
	}

	function setSubject( $subject )
	{
		$this->subject = $this->encode($subject);
	}

	function setFrom( $from_address, $override = true )
	{
        if ( is_array($from_address) ) {
			$address = $this->quoteEmail(array_pop(array_values($from_address)).' <'.array_pop(array_keys($from_address)).'>');

        } else {
			$address = $this->quoteEmail($from_address);
        }

		if ( $override && $this->mailer_settings->get('EmailSender') == 'admin' ) {
			$address = $this->addressUpdateEmail($address, self::getSystemEmail());
		}

		$this->from_address = $address;
	}
	
	function setFromUser( $user_it )
	{
		if ( $user_it->getId('Email') == '' ) {
			$this->setFrom(self::getSystemEmail(), false);
		}
		else {
			$this->setFrom($user_it->get('Caption').' <'.$user_it->get('Email').'>');
		}
	}

	static function getSystemEmail()
	{
		$settings_it = getFactory()->getObject('cms_SystemSettings')->getAll();
		if ( $settings_it->get('AdminEmail') != '' )
		{
			$email_match = array();
			if ( preg_match('/<([^>]+)>/', $settings_it->getHtmlDecoded('AdminEmail'), $email_match) ) {
				return $email_match[1];
			}
			else {
				return $settings_it->getHtmlDecoded('AdminEmail');
			}
		}
		return '';
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
	
	static function getContentType() {
		return "Content-Type: text/plain; charset=".APP_ENCODING;
	}
	
	static function encode( $text ) {
		return '=?'.APP_ENCODING.'?B?'.base64_encode($text).'?=';
	}
	
	static function encodeAddress( $address ) {
		list($display, $email) = preg_split('/</',$address);
		
		if($email != '') {
			return self::encode($display).'<'.$email;
		}
		else {
			return $address;
		}
	}

	static function parseAddressString( $address ) {
		list($display, $email) = preg_split('/</',$address);
		if ( $email == '' ) {
			$email = $display;
			$display = '';
		}
		$email = array_shift(preg_split('/>/', $email));
		return array($email, $display);
	}

	function addressUpdateEmail( $address, $email ) 
	{
		return preg_replace('/\<([^>])+>/', '<'.$email.'>', $address); 
	}
 }
 
class HtmlMailBox extends MailBox
{
    const boundary = '5446b4677d9475446b481adbb3';
    const boundary_related = 'e61f23g3cba093338679c352faf8';
 	
	static function getContentType() {
		return "multipart/related; boundary=".self::boundary_related;
	}
	
	static function encode( $text ) {
	    return '=?UTF-8?B?'.base64_encode($text).'?=';
	}

	function setBody( $body ) 
	{
        // convert linked images into embedded ones
        $body = preg_replace_callback( '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceImageCallback'), $body);

		// process embedded images
		$images_body = '';
		$images_count = 0;
		$boundary = self::boundary_related;

		$body = preg_replace_callback('/src="data:([^;]+);base64,([^"]+)"/i',
			function($matches) use (&$images_body, &$images_count, $boundary)
			{
				list($type, $ext) = preg_split('/\//', $matches[1]);
				$image_name = 'image'.($images_count+1).'.'.$ext;
				$image_id = 'ii_'.(round(microtime(true) * 1000) + $images_count);

				$images_body .= "--".$boundary."\r\n";
				$images_body .= "Content-Type: ".$matches[1]."; name=\"".$image_name."\"\r\n";
				$images_body .= "Content-Disposition: inline; filename=\"".$image_name."\"\r\n";
				$images_body .= "Content-Transfer-Encoding: base64\r\n";
				$images_body .= "Content-ID: <".$image_id.">\r\n\r\n";
				$images_body .= $matches[2]."\r\n";

				$images_count++;
				return 'alt="'.text(2074).' '.($images_count).'" src="cid:'.$image_id.'"';
			}, $body);

        $this->body .= "--".self::boundary_related."\r\n";
        $this->body .= "Content-Type: multipart/alternative; boundary=".self::boundary."\r\n\r\n";
        $this->body .= "--".self::boundary."\r\n";
        $this->body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $this->body .= "Content-Transfer-Encoding: base64\r\n\r\n";

		// process texted part
        $html2text = new \Html2Text\Html2Text($body);
		$texted = $html2text->getText();
		
		$texted = preg_replace('/\s{2,}/', PHP_EOL, $texted);
		$texted = preg_replace('/[\r\n]{2,}/', PHP_EOL.PHP_EOL, $texted);
		
		$this->body .= base64_encode($texted)."\r\n";

		// process html part
        $this->body .= "--" . self::boundary . "\r\n";
        $this->body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $this->body .= "Content-Transfer-Encoding: base64\r\n\r\n";
		$this->body .= base64_encode($this->applyStyles(
		    '<html>'.PHP_EOL.
		    '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>'.PHP_EOL.
		    '<body>'.preg_replace('/[\r\n]+/', '', $body).'</body>'.PHP_EOL.
		    '</html>'
		));
		$this->body .= "\r\n--" . self::boundary . "--\r\n";

        $this->body .= $images_body;
        $this->body .= "--".self::boundary_related."--";

        $this->body = wordwrap($this->body, 76, PHP_EOL, true);
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
 