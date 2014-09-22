<?php

$list->draw( $view );

if ( $hint != '' )
{
	echo $view->render('core/Hint.php', array('title' => $hint, 'name' => 'board-hint'));
}
