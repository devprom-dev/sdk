<?php

class AdminEmailNotificatorHandler
{
 	function getSender( $object_it, $action ) 
 	{
		global $model_factory, $part_it;
		
		$settings = $model_factory->getObject('cms_SystemSettings');
 		$settings_it = $settings->getAll();
 		
 		return $settings_it->getHtmlDecoded('AdminEmail');
 	}
 	
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		return array();
	}	

	function getBody( $action, $object_it, $prev_object_it, $recipient ) 
	{
		return '';
	}	

	function getMailBox() 
	{
		return new HtmlMailBox;
	}
}
