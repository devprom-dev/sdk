<?php
/*
 * DEVPROM (http://www.devprom.net)
 * logoff.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 include('common.php');
  
 $session = getSession();
 $session->close();
 
 exit( header('Location: /') );

?>
