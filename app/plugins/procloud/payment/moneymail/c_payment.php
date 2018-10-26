<?php

 define('SECRET_KEY', 'CB5E93A4703A4e22A4D141739CF62A24');
 
 ///////////////////////////////////////////////////////////////////////
 class MoneyMailPayment
 {
 	function _getTopUpUrl()
 	{
 		return 'https://www.moneymail.ru/';
 	}
 	
	function getBillIt()
	{
 		global $model_factory;
 		
 		$bill = $model_factory->getObject('co_Bill');
 		return $bill->getCurrentIt();
	}

 	function getTopUpUrl()
 	{
 		return $this->_getTopUpUrl();
 	}

	function payment( $payment_id, $sender, $recipient, $purpose, $time, $value, $currency, $checksum )
	{ 	
 		global $model_factory;
 		
 		$this->log( array( 
			'Method' => 'payment', 
			'Payment' => $payment_id, 
			'Sender' => $sender, 
			'Recipient' => $recipient,
			'Purpose' => $purpose,
			'Time' => $time,
			'Value' => $value,
			'Currency' => $currency,
			'Checksum' => $checksum ) 
		);

		$hash = md5(SECRET_KEY.$payment_id.$sender.$recipient.$purpose.$time.$value.$currency);
		
		if ( strtolower($hash) != strtolower($checksum) )
		{
 			$this->log( array( 'Method' => 'payment', 
				'Failed' => 'Wrong checksum: '.$hash) );
				
			return false;
		}
		
 		$bill = $model_factory->getObject('co_Bill');
 		$bill_it = $bill->getAll();
 		
 		while ( !$bill_it->end() )
 		{
 			if ( strpos($purpose, $bill_it->getNumber()) > 0 )
 			{
				$bill_it->makePayment( $value, 
					str_replace('%1', 'MoneyMail', text(422)) );
				
 				$this->log( array( 'Method' => 'payment', 
					'Success' => $bill_it->getNumber()) );
 				
				exit(header('Location: /co/account.php'));
 			}
 			$bill_it->moveNext();
 		}

		$this->log( array( 'Method' => 'payment', 
			'Failed' => $purpose) );
				
		return false;
	}
	
	function log( $parms )
	{
		$file = fopen(SERVER_ROOT_PATH.'payment/moneymail/log.txt', 'a');
		
		$keys = array_keys($parms);
		foreach ( $keys as $key )
		{
			$text .= $key.'='.$parms[$key].',';
		}
		
		fwrite($file, strftime('%Y-%d-%m %H:%M:%S').' - '.$text.chr(10));
		
		fclose($file);
	}
 }
 
?>
