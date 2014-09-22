<?php
 
 /////////////////////////////////////////////////////////////////////////////////
 class Error404Table
 {
 	function draw()
 	{
 		global $_REQUEST, $plugins;

 		echo '<div>';
 			echo '<b>404/Not Found</b>';
 		echo '</div>';

		echo '<br/>';
		
 		echo '<div>';
 			$text = $plugins->hasIncluded('eecoplugin') ? text(674) : text(676);
 			echo preg_replace('/%2/', urlencode($_REQUEST['page']), preg_replace('/%1/', $_REQUEST['page'], $text));
 		echo '</div>';
 	}
 }

 /////////////////////////////////////////////////////////////////////////////////
 class Error500Table
 {
 	function draw()
 	{
 		global $plugins;
 		
 		echo '<div>';
 			if ( $plugins->hasIncluded('eecoplugin') )
 			{
	 			echo text(673);
 			}
 			else
 			{
	 			echo text(677);
 			}
 		echo '</div>';
 	}
 }

 /////////////////////////////////////////////////////////////////////////////////
 class ErrorPage extends Page
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
 		global $_REQUEST;
 		
 		switch ( $_REQUEST['mode'] )
 		{
 			case '404':
 				return new Error404Table;

 			case '500':
 				return new Error500Table;
 		}
 	}
 }
