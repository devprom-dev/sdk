<?php 

$view->extend('core/PageBody.php'); 

$view['slots']->output('_content');

$table->draw( $view );

?>