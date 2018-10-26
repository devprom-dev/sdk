<?php

 include ('common.php');
 
 $payment = new RoboxPayment;
 exit(header('Location: '.$payment->getTopUpUrl($_REQUEST['bill'])));
 
?>