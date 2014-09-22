<?php

include dirname(__FILE__).'/../app/bootstrap.php';

// create session object
$session = new AdminSession();

if ( !getSession()->getUserIt()->IsAdministrator() )
{
	exit(header('Location: /'));
}
