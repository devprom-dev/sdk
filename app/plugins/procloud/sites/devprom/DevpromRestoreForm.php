<?php

class DevpromRestoreForm extends DevpromBaseForm
{
 	function getAddCaption()
 	{
 		return translate('Восстановление пароля');
 	}

 	function getCommandClass()
 	{
 		return 'corestore';
 	}
 	
	function getAttributes()
	{
		return array ( 'Key', 'Password', 'Password2' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Key':
				return 'text';
				
			case 'Password':
			case 'Password2':
				return 'password'; 	
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return false;
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Key':
				return translate('Ключ для сброса пароля');

			case 'Password':
				return translate('Новый пароль');

			case 'Password2':
				return translate('Повтор нового пароля');

			default:
				return parent::getName( $attribute );
		}
	}

	function getAttributeValue( $attribute )
	{
		global $_REQUEST;

		switch ( $attribute )
		{
			case 'Key':
				return $_REQUEST['key'];

			default:
				return parent::getAttributeValue( $attribute );
		}
	}
	
	function getButtonText()
 	{
 		return translate('Отправить');
 	}

	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', refreshWindow)';
	}
}
