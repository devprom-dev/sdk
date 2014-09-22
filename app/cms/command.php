<?php

 $cms_path = '../cms/';

 if ( preg_match('/^[a-zA-Z0-9\_]+$/im', $class) < 1 )
 {
 	unset($class);
 }

 require_once('common.php');
 require_once('../command/c_'.strtolower($class).'.php');
 require_once('design.php');

 $command = new $class; 
 
 beginPage($command->getCaption());
 
 $command->draw();
 
 endPage();

?>