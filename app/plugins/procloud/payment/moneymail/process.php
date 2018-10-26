<?php

 include ('common.php');
 
 $payment = new MoneyMailPayment;

 $result = $payment->payment( $_REQUEST['payment_id'], $_REQUEST['sender'], 
 	$_REQUEST['recipient'], $_REQUEST['purpose'], $_REQUEST['time'], 
 	$_REQUEST['value'], $_REQUEST['currency'], $_REQUEST['checksum'] );
 
 if ( $result )
 {
 	echo 'OK';
 }
 else
 {
 	echo 'FAIL';
 }
?>