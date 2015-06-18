<?php
 
class ForgetPasswordForm extends AjaxForm
{
 	function getAddCaption()
 	{ 
 		return text(1037);
 	}

 	function getRedirectUrl()
	{
		return '/login';
	}
	
	function getFormUrl()
	{
	}

	function getAttributes()
	{
		$attrs = array();
		
		array_push($attrs, 'email');
		
		return $attrs;
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
				return text(1038);
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
		return translate('Отправить');
	}
	
	function getTemplate()
	{
		return "co/FormAsyncNoHeader.php";
	}
}
