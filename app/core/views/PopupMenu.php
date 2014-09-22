<?php
 
 use Symfony\Component\Templating\PhpEngine;
 use Symfony\Component\Templating\TemplateNameParser;
 use Symfony\Component\Templating\Loader\FilesystemLoader;

 class PopupMenu
 {
 	function draw( $classname, $title, $actions = array(), $url = "javascript:;" )
 	{
 	    $language = getLanguage();
 	     
 	    $last_month = $language->getPhpDate( strtotime('-1 month', strtotime(date('Y-m-j'))) );
 	    
 	    $last_week = $language->getPhpDate( strtotime('-1 week', strtotime(date('Y-m-j'))) );
 	    
 		$view = new PhpEngine(
 			new TemplateNameParser(), 
 			new FilesystemLoader(SERVER_ROOT_PATH.'/templates/views/%name%') 
		);
 		
		echo $view->render("core/PopupMenu.php", array (
			'class' => $classname,
			'title' => $title,
			'items' => $actions,
			'url' => preg_replace('/last-month/', $last_month, 
			            preg_replace('/last-week/', $last_week, $url))
		)); 
 	}
 }
