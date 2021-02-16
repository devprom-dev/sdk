<?php
 
 class CustomAttributeProcess extends CommandForm
 {
 	function validate()
 	{
		$this->checkRequired(
			array('form_url', 'EntityReferenceName', 'AttributeType') );

		return true;
 	}
 	
 	function create()
	{
		$url = SanitizeUrl::parseUrl($_REQUEST['form_url']).
			'&EntityReferenceName='.SanitizeUrl::parseUrl($_REQUEST['EntityReferenceName']).
			'&AttributeType='.SanitizeUrl::parseUrl($_REQUEST['AttributeType']).
			'&AttributeTypeClassName='.SanitizeUrl::parseUrl($_REQUEST['AttributeTypeClassName']);
		
		$url .= '&redirect='.urlencode($_REQUEST['redirect']);
		
		$this->replyRedirect($url);
	}
 }