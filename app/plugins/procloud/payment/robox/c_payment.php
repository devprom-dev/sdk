<?php

 define('ROBOX_PASS_1', '{CB5E93A4-703A-4e22-A4D1-41739CF62A24}');
 define('ROBOX_PASS_2', '{302DD505-7DAD-4e3a-ADC7-0A822AA1A833}');
 
 ///////////////////////////////////////////////////////////////////////
 class RoboxPayment
 {
 	function _getTopUpUrl()
 	{
 		//return 'http://test.robokassa.ru/Index.aspx';
 		return 'https://merchant.roboxchange.com/Index.aspx';
 	}
 	
 	function getHash1( $invoice_id )
 	{
 		return strtoupper(md5('devprom::'.$invoice_id.':'.ROBOX_PASS_1));
 	}

 	function getHash2( $sum, $invoice )
 	{
 		return strtoupper(md5($sum.':'.$invoice.':'.ROBOX_PASS_2));
 	}

 	function getHash3( $sum, $invoice )
 	{
 		return strtoupper(md5($sum.':'.$invoice.':'.ROBOX_PASS_1));
 	}

	function getBillIt()
	{
 		global $model_factory;
 		
 		$bill = $model_factory->getObject('co_Bill');
 		return $bill->getCurrentIt();
	}

	function hashInvoice( $bill_id, $operation_id )
	{
		return abs(crc32($bill_id.'devprom'.$operation_id));
	}
	
	function getInvoice( $bill_it )
	{
		$operation_id = $bill_it->makePayment( 0, 
			str_replace('%1', 'ROBOKASSA', text(422)), translate('Ожидание платежа') );
		
		if ( $operation_id > 0 )
		{
			return $this->hashInvoice($bill_it->getId(), $operation_id);
		}
	}

 	function getTopUpUrl( $comment )
 	{
 		$bill_it = $this->getBillIt();
 		$invoice = $this->getInvoice($bill_it);
 		
 		return $this->_getTopUpUrl().'?MrchLogin=devprom&InvId='.$invoice.
 			'&Desc='.$comment.'&SignatureValue='.$this->getHash1($invoice).'&Culture=ru';
 	}

	function validate( $sum, $invoice, $signature )
	{ 	
 		global $model_factory;
 		
 		$this->log( array( 'Method' => 'validate', 'Sum' => $sum, 'Invoice' => $invoice, 'Signature' => $signature) );
 		
		if ( $this->getHash2($sum, $invoice) != $signature )
		{
 			$this->log( array( 'Method' => 'validate', 'Failed' => 'Wrong signature: '.$this->getHash2($sum, $invoice)) );
			return false;
		}
		
		return true;
	}

	function payment( $sum, $invoice, $signature )
	{ 	
 		global $model_factory;
 		
 		$this->log( array( 'Method' => 'payment', 'Sum' => $sum, 'Invoice' => $invoice, 'Signature' => $signature) );

		if ( $this->getHash3($sum, $invoice) != $signature )
		{
 			$this->log( array( 'Method' => 'payment', 'Failed' => 'Wrong signature: '.$this->getHash3($sum, $invoice)) );
			return false;
		}
		
		$bill = $model_factory->getObject('co_Bill');

		$bill_it = $bill->getAll();
		while ( !$bill_it->end() )
		{
			$operation_it = $bill_it->getOperationIt();
			while ( !$operation_it->end() )
			{
				if ( $invoice == $this->hashInvoice($bill_it->getId(), $operation_it->getId()) )
				{
					$bill_it->makePaymentInvoice( $operation_it->getId(), round($sum / (1 - 0.07)), 
						str_replace('%1', 'ROBOKASSA', text(422)) );
					
					$this->log( array( 'Method' => 'payment', 'Success' => 'Payment was fixed: '.$invoice) );

					exit(header('Location: /co/account.php'));
				}
				
				$operation_it->moveNext();
			}
			
			$bill_it->moveNext();
		}
		
		$this->log( array( 'Method' => 'payment', 'Failed' => 'Wrong invoice: '.$invoice) );
		return false;
	}
	
	function log( $parms )
	{
		$file = fopen(SERVER_ROOT_PATH.'payment/robox/log.txt', 'a');
		
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
