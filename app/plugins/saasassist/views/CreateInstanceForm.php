<?php
 
class CreateInstanceForm extends AjaxForm
{
 	function getAddCaption()
 	{ 
 		return $_REQUEST['template'] != '' ? text('saasassist37') : text('saasassist6');
 	}

 	function getCommandClass()
 	{
 		return 'createinstance&namespace=saasassist';
 	}

	function getAttributes()
	{
		return array('instance', 'username', 'email', 'template');
	}

	function getAttributeType( $attribute )
	{
		return 'text';
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'email':
				return 'Email';

			case 'instance':
				return text('saasassist7');
				
			case 'username':
				return text('saasassist40');
		}
	}
	
	function getDescription( $attribute )
	{
		switch ( $attribute )
		{
			case 'email':
				return text('saasassist8');

			case 'instance':
				return preg_replace('/\%1/', SAAS_DOMAIN, text('saasassist9'));
		}
	}
	
	function getAttributeValue( $attribute )
	{
		if ( $attribute == 'template' )
		{		
			return htmlentities($_REQUEST['template']);
		}
		
		return parent::getAttributeValue( $attribute );
	}

	function IsAttributeVisible( $attribute )
	{
		return $attribute != 'template';
	}
 	
	function IsAttributeModifable( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return true;
	}
	
	function getButtonText()
	{
		return translate('Создать');
	}
	
	function getTemplate()
	{
		return '../../plugins/saasassist/views/templates/CreateInstance.tpl.php';
	}
}
