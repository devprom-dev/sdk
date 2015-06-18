<?php

class DevpromLoginForm extends DevpromBaseForm
{
 	function getAddCaption()
 	{
 		return translate('Авторизация');
 	}

 	function getCommandClass()
 	{
 		return 'cologin';
 	}
 	
	function getAttributes()
	{
		$attrs = array ( 'email', 'pass' );
    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'email':
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
			case 'email':
				return translate('Email');

			case 'pass':
				return translate('Пароль'); 	

			default:
				return parent::getName( $attribute );
				
		}
	}

 	function getButtonText()
 	{
 		return translate('Войти');
 	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		global $tab_index;
		
		if ( $attribute == 'pass' )
		{
			echo '<input class="input_value form-control" type="password" id="'.$attribute.'" name="'.$attribute.'" value="'.$value.'" tabindex="'.$tab_index.'">';

			echo '<div style="font-size:13px;float:right;padding-top:3px;">';
				echo '<a href="javascript: getRestoreRequestForm();">'.translate('Восстановить пароль').'</a>';
			echo '</div>';
		}
		else
		{
			return parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}

	function draw()
	{
		echo '<div class="title">';
			echo 'Для авторизации вам необходимо указать ваш электронный адрес и пароль, высланный вам ранее. ';
		echo '</div>';
		
		parent::draw();
	}

	function drawTitle()
	{
	}
	
	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', refreshWindow)';
	}
}
