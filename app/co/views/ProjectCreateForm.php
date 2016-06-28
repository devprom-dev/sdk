<?php
include "fields/FieldProjectTemplateDictionary.php";

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
		return array ( 'Caption', 'CodeName', 'Template', 'Participants', 'DemoData', 'System' );
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
			case 'System':
				return 'custom';
		}
	}

	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
		    case 'Caption':
		    	$template = $this->getAttributeValue('Template');
		    	if ( $template == '' ) return "";
		    	return getFactory()->getObject('pm_ProjectTemplate')->getExact($template)->getDisplayName();
		    	
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

	 function drawCustomAttribute( $attribute, $value, $tab_index )
	 {
		 switch ( $attribute )
		 {
			 case 'Template':
				 $field = new FieldProjectTemplateDictionary();
				 $field->SetId($attribute);
				 $field->SetName($attribute);
				 $field->SetValue($value);
				 $field->SetTabIndex($tab_index);
				 $field->SetRequired(true);

				 echo $this->getName($attribute);
				 $field->draw();
				 break;

			 case 'System':
				 if ( is_numeric($_REQUEST['portfolio']) ) {
					 echo '<input type="hidden" name="portfolio" value="'.$_REQUEST['portfolio'].'">';
				 }
				 break;

			 default:
				 parent::drawCustomAttribute( $attribute, $value, $tab_index );
		 }
	 }
}