<?php
 
class CreateInstanceForm extends AjaxForm
{
 	function getAddCaption()
 	{ 
 		return text('saasassist6');
 	}

 	function getCommandClass()
 	{
 		return 'createinstance&namespace=saasassist';
 	}

	function getAttributes()
	{
		return array('instance', 'email', 'info');
	}

	function getAttributeType( $attribute )
	{
		return $attribute == 'info' ? 'custom' : 'text';
	}
	
	function getName( $attribute )
	{
		switch ( $attribute )
		{
			case 'email':
				return 'Email';

			case 'instance':
				return text('saasassist7');
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

	function IsAttributeVisible( $attribute )
	{
		return true;
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
		return "co/FormAsyncNoHeader.php";
	}
	
	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		switch ( $attribute )
		{
			case 'info':
				$this->drawInfo();
				break;
				
			default:
				parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}
	
	function drawInfo()
	{
		echo text('saasassist21');
		
		echo '<input type="hidden" name="template" value="'.htmlentities($_REQUEST['template']).'">';
	}
}
