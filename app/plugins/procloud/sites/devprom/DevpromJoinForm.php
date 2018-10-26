<?php

class DevpromJoinForm extends DevpromBaseForm
{
 	function getAddCaption()
 	{
 		return translate('Регистрация');
 	}

 	function getCommandClass()
 	{
 		return 'codevpromjoin';
 	}
 	
	function getAttributes()
	{
		$attrs = array ( 'Firstname', 'Email', 'Phone', 'Company', 'Possibilities', 'Captcha' );
    	return $attrs;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Surname':
			case 'Firstname':
			case 'Email':
			case 'Phone':
			case 'Company':
				return 'text';
				
			case 'Possibilities':
				return 'richtext'; 	

			case 'Captcha':
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
			case 'Firstname':
				return 'Имя и фамилия <span class="required">*</span>';

			case 'Phone':
				return 'Телефон <span class="required">*</span>';
				
			case 'Company':
				return 'Компания <span class="required">*</span>';
				
			case 'Email':
				return translate('E-mail').' <span class="required">*</span>';

			case 'Possibilities':
				return 'Какие возможности Devprom вас наиболее заинтересовали? <span class="required">*</span>';

			case 'Captcha':
				return 'Введите текст, изображенный на картинке';
				
			default:
				return parent::getName( $attribute );
		}
	}

 	function getButtonText()
 	{
 		return translate('Отправить');
 	}

	function drawCustomAttribute( $attribute, $value, $tab_index )
	{
		switch ( $attribute )
		{
			case 'Captcha':
				dsp_crypt(0, 0);
				return parent::drawCustomAttribute( $attribute, $value, $tab_index );
				
			default:
				return parent::drawCustomAttribute( $attribute, $value, $tab_index );
		}
	}

	function getSubmitScript()
	{
		return 'submitForm(\''.$this->getAction().'\', refreshWindow)';
	}
	
	function draw()
	{
		echo '<div class="title">';
			//echo 'Пожалуйста, аккуратно заполните все поля на форме, чтобы загрузить файл <span id="fileName"></span>';
			echo 'Пожалуйста, аккуратно заполните все поля на форме, чтобы загрузить выбранный файл. ';
			echo 'Или <a href="javascript: getLoginForm($(\'#loginRedirectUrl\').val());">авторизуйтесь</a> с использованием пароля, полученного ранее.';
		echo '</div>';
		
		parent::draw();
	}
}
