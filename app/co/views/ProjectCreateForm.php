<?php

 ////////////////////////////////////////////////////////////////////////////////
 class CreateProjectForm extends AjaxForm
 {
 	var $question_it;
 	
 	function CreateProjectForm ( $object )
 	{
 		global $model_factory;
 		
		$question = $model_factory->getObject('cms_CheckQuestion');
		$this->question_it = $question->getRandom();
		
		parent::AjaxForm( $object );
 	}
 	
 	function getAddCaption()
 	{
 		return translate('Создание нового проекта');
 	}

 	function getCommandClass()
 	{
 		return 'projectmanage';
 	}
 	
 	function getFormUrl()
 	{
 	}
 	
	function getRedirectUrl()
	{
		return '/pm/';
	}
 	
	function getAttributes()
	{
		return array ( 'Caption', 'Codename', 'Template' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Codename':
			case 'Caption':
				return 'text'; 	

			case 'Template':
				return 'object';
		}
	}

	function getAttributeClass( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Template':
				return $model_factory->getObject('pm_ProjectTemplate');
		}
	}

	function getAttributeValue( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			default:
				return parent::getAttributeValue( $attribute );
		}
	}

	function IsAttributeVisible( $attribute )
	{
		return true;
	}

	function IsAttributeRequired( $attribute )
	{
		return true;
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Codename':
				return translate('Кодовое название проекта');

			case 'Caption':
				return translate('Название проекта');

			case 'Template':
				return translate('Шаблон начальных настроек проекта');

			default:
				return parent::getName( $attribute );
				
		}
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
			case 'Codename':
				return str_replace('%1', _getServerUrl(), text(479));

			case 'Caption':
				return text(480);

			case 'Template':
				return text(741);
 		}
 	}
 }