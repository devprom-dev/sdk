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
		return array ( 'Caption', 'CodeName', 'Template', 'Participants', 'DemoData' );
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'CodeName':
			case 'Caption':
			case 'Participants':
				return 'text'; 	
				
			case 'DemoData':
				return 'char';

			case 'Template':
				return 'object';
		}
	}

	function getAttributeClass( $attribute )
	{
		switch ( $attribute )
		{
			case 'Template':
				$template = getFactory()->getObject('pm_ProjectTemplate');
				
				$template->addFilter( new FilterAttributePredicate('Language', getSession()->getLanguage()->getLanguageId()) );
				
				$template->setSortDefault(
		 				array (
		 						new SortAttributeClause('Kind.D'),
		 						new SortAttributeClause('OrderNum')	
		 				)
	 			);
						
				return $template;
		}
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
		    case 'Caption':
		    case 'CodeName':
		    	return "";

		    case 'Template':
		    	return $_REQUEST['Template'];
		    	
		    case 'DemoData':
		    	return $this->getAttributeValue('Template') == '' ? 'N' : 'Y';
		    	
			default:
				return parent::getAttributeValue( $attribute );
		}
	}

	function IsAttributeVisible( $attribute )
	{
		switch( $attribute )
		{
		    case 'Template':
		    	return $this->getAttributeValue($attribute) == '';
		    	
		    default:
		    	return true;
		}
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
			case 'CodeName':
				return translate('Кодовое название проекта');

			case 'Caption':
				return translate('Название проекта');

			case 'Template':
				return translate('Шаблон начальных настроек проекта');

			case 'Participants':
				return translate('Пригласить участников');

			case 'DemoData':
				return text(1869);
				
			default:
				return parent::getName( $attribute );
				
		}
	}

 	function getDescription( $attribute )
 	{
 		switch( $attribute )
 		{
			case 'CodeName':
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