<?php

class DevpromRequestToRestoreForm extends DevpromBaseForm
{
 	function getAddCaption()
 	{
 		return translate('Запрос на восстановление пароля');
 	}

 	function getCommandClass()
 	{
 		return 'corestorerequest';
 	}
 	
	function getAttributes()
	{
		return array ( 'Email' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Email':
				return 'text';
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
			case 'Email':
				return translate('Введите ваш Email');

			default:
				return parent::getName( $attribute );
		}
	}

 	function getButtonText()
 	{
 		return 'Дальше';
 	}

	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', prepareToRestore)';
	}
}
