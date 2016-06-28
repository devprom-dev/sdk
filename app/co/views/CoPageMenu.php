<?php
 
 class CoPageMenu extends PageMenu
 {
 	function getSection()
 	{
 		return 'co';
 	}
 	
 	function getPages()
 	{
		$pages = array();
		
		$pages['main'] = array( 
		    'uid' => 'main',
			'items' => array(
				array( 'url' => '/profile', 'name' => translate('Профиль') )   
			) 
		);

		return $pages;
 	}
 }
