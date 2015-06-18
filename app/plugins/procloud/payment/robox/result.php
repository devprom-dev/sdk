<?php

 include ('common.php');
 
 $payment = new RoboxPayment;
 
 if ( $payment->validate( $_REQUEST['OutSum'], $_REQUEST['InvId'], strtoupper($_REQUEST['SignatureValue']) ) )
 {
 	echo 'OK'.$_REQUEST['InvId'];
 }
 else
 {
 	echo 'FAIL';
 }
 
?>