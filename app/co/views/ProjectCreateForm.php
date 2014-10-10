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
 		return translate('�������� ������ �������');
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
		return array ( 'Caption', 'Codename', 'Template', 'Participants' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'Codename':
			case 'Caption':
			case 'Participants':
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
		return $attribute != 'Participants';
	}
	
	function getName( $attribute )
	{
		global $model_factory;
		
		switch ( $attribute )
		{
			case 'Codename':
				return translate('������� �������� �������');

			case 'Caption':
				return translate('�������� �������');

			case 'Template':
				return translate('������ ��������� �������� �������');

			case 'Participants':
				return translate('���������� ����������');
				
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

			case 'Participants':
				return text(1865);
 		}
 	}
 }