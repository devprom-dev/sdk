<?php

 include ('common.php');
 
 $payment = new RoboxPayment;

 $payment->payment( $_REQUEST['OutSum'], $_REQUEST['InvId'], 
 	strtoupper($_REQUEST['SignatureValue']) );
 
?>