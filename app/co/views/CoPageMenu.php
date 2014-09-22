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
			'name' => translate('���������'),
		    'uid' => 'main',
			'items' => array(
				array( 'url' => '/profile', 'name' => translate('�������') )   
			) 
		);

		return $pages;
 	}
 }
