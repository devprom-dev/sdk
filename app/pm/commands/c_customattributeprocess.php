<?php
 
 class CustomAttributeProcess extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST;
 		
		$this->checkRequired( 
			array('form_url', 'EntityReferenceName', 'AttributeType') );

		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory;

		$url = SanitizeUrl::parseUrl($_REQUEST['form_url']).
			'&EntityReferenceName='.SanitizeUrl::parseUrl($_REQUEST['EntityReferenceName']).
			'&AttributeType='.SanitizeUrl::parseUrl($_REQUEST['AttributeType']).
			'&AttributeTypeClassName='.SanitizeUrl::parseUrl($_REQUEST['AttributeTypeClassName']);
		
		$url .= '&redirect='.urlencode($_REQUEST['redirect']);
		
		$this->replyRedirect($url);
	}
 }
 
?>