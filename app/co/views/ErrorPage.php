<?php

include ('Error404Table.php');
include ('Error500Table.php');
include ('Error503Table.php');

class ErrorPage extends CoPage
{
 	var $project_it;
 	
 	function ErrorPage()
 	{
 		parent::Page();
 	}
 	
 	function needDisplayForm() 
 	{
 		return false;
 	}
 	
 	function getTable() 
 	{
 	    $parts = preg_split('/\?/', $_SERVER['REQUEST_URI']);
 	    
 		switch ( $parts[0] )
 		{
 			case '/404':
 				return new Error404Table;

 			case '/500':
 				return new Error500Table;

 			case '/503':
 				return new Error503Table;
 		}
 	}
}
