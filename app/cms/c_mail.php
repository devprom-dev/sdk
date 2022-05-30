<?php

use \InlineStyle\InlineStyle;
include_once SERVER_ROOT_PATH.'core/classes/html/HtmlImageConverter.php';

class MailBox
{
 	var $to_address, $body, $from_address, $subject;
	private $mailer_settings = null;
	private $attachments = array();
	private $text = '';
	private $messageId = '';
	private $parameters = '';
	
	function __construct() {
		$this->to_address = array();
		$this->mailer_settings = getFactory()->getObject('MailerSettings')->getAll();
        $this->from_address = $this->getSystemEmail();
	}
	
	function appendAddress( $address ) {
		array_push($this->to_address, $address);
	}
	
	function setBody( $body ) {
		$this->body = $body;
		$this->setText($body);
	}
	
	function getBody() {
		return $this->body;
	}

	function setSubject( $subject ) {
		$this->subject = $this->encode($subject);
	}

	function setText( $text ) {
	    $this->text = $text;
    }

    function setInReplyMessageId( $value ) {
	    $this->messageId = $value;
    }

	function setAttachments( $attachments ) {
	    $this->attachments = $attachments;
    }

	function setFrom( $from_address ) {
		$this->from_address = $from_address;
	}

	function setParameters( $parms ) {
        $this->parameters = $parms;
    }

	protected function getSystemEmail()
    {
        $settingsIt = getFactory()->getObject('cms_SystemSettings')->getAll();
        $address = $settingsIt->getHtmlDecoded('AdminEmail');
        $parts = preg_split('/</', $address);
        if ( count($parts) > 1 ) {
            return $address;
        }
        else {
            return $settingsIt->getHtmlDecoded('Caption') . ' <' . $address . '>';
        }
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
		
		if ( $max_recipients > 0 ) {
			if ( count($this->to_address) > $max_recipients ) return false;
		}

 		$queue = new Metaobject('EmailQueue');
 		$address = new Metaobject('EmailQueueAddress');

 		$queueIt = $queue->getRegistry()->Merge(
 			array (
 			    'FromAddress' => $this->from_address,
 				'Caption' => $this->subject,
 				'Description' => \JSONWrapper::encode(
 				    array(
 				        'native' => $this->body,
 				        'text' => $this->text
                    )
                ),
 				'MailboxClass' => get_class($this),
                'Attachments' => serialize($this->attachments),
                'EmailMessageId' => $this->messageId,
                'Parameters' => $this->parameters,
                'Recipient' => array_shift(array_values($this->to_address))
            ),
            array(
                'EmailMessageId',
                'Recipient'
            )
        );

		for ( $i = 0; $i < count($this->to_address); $i++ ) {
	 		$address->getRegistry()->Merge(array(
	 		    'EmailQueue' => $queueIt->getId(),
	 			'ToAddress' => $this->to_address[$i]
            ));
		}

		return $queueIt->getId();
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
		return array(trim($email), trim($display));
	}

	function addressUpdateEmail( $address, $email ) 
	{
		return preg_replace('/\<([^>])+>/', '<'.$email.'>', $address); 
	}

	static function compareDomains( $emailLeft, $emailRight )
    {
	    $partsLeft = preg_split('/@/', str_replace('>', '', trim($emailLeft)));
        $partsRight = preg_split('/@/', str_replace('>', '', trim($emailRight)));
        return trim(mb_strtolower($partsLeft[1])) == trim(mb_strtolower($partsRight[1]));
    }
 }
 
class HtmlMailBox extends MailBox
{
    const mixed = '24ceef2d-b708-47f7';
    const boundary = '5446b4677d9475446b481adbb3';
    const boundary_related = 'e61f23g3cba093338679c352faf8';
 	
	static function getContentType() {
		return "multipart/mixed; boundary=".self::mixed;
	}
	
	static function encode( $text ) {
	    return '=?UTF-8?B?'.base64_encode($text).'?=';
	}

	function setBody( $body ) 
	{
        // convert linked images into embedded ones
        $body = preg_replace_callback( '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceImageCallback'), $body);
        $this->setText($body);

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

        $this->body .= "\r\n--".self::mixed."\r\n";
        $this->body .= "Content-Type: multipart/alternative; boundary=".self::boundary."\r\n";
        $this->body .= "\r\n";
        $this->body .= "This is multi-part message in MIME format.\r\n";

        $this->body .= "\r\n--".self::boundary."\r\n";
        $this->body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $this->body .= "Content-Transfer-Encoding: base64\r\n";
        $this->body .= "\r\n";

		// process texted part
        $html2text = new \Html2Text\Html2Text($body, array('width'=>0));
		$texted = $html2text->getText();
		$texted = preg_replace('/\s{2,}/', PHP_EOL, $texted);
		$texted = preg_replace('/[\r\n]{2,}/', PHP_EOL.PHP_EOL, $texted);
		$this->body .= base64_encode($texted)."\r\n";

        $this->body .= "\r\n--".self::boundary."\r\n";
        $this->body .= "Content-Type: multipart/related; boundary=".self::boundary_related."\r\n";
        $this->body .= "\r\n";

		// process html part
        $this->body .= "\r\n--" . self::boundary_related . "\r\n";
        $this->body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $this->body .= "Content-Transfer-Encoding: base64\r\n";
        $this->body .= "\r\n";
		$this->body .= base64_encode(
		    '<html>'.PHP_EOL.
		    '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head>'.PHP_EOL.
		    '<body>'.$body.'</body>'.PHP_EOL.
		    '</html>'
		);
        $this->body .= "\r\n";

        $this->body .= $images_body;
        $this->body .= "--" . self::boundary_related . "--\r\n";
		$this->body .= "--" . self::boundary . "--\r\n";
        $this->body .= "--" . self::mixed . "--\r\n";

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
 