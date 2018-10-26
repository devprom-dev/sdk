<?php
include "fields/FieldProjectTemplateDictionary.php";

class CreateProjectForm extends AjaxForm
{
 	function getAddCaption()
 	{
 		return text('projects.create.title');
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
		$attributes = array ( 'Caption', 'CodeName', 'Template', 'Participants', 'DemoData', 'System' );
        if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED ) {
            $usersCount = getFactory()->getObject('User')->getRegistry()->Count();
            if ( $usersCount > 1 ) {
                $attributes[] = 'Users';
            }
        }
		return $attributes;
	}
	
	function getAttributeType( $attribute )
	{
		switch ( $attribute )
		{
			case 'CodeName':
			case 'Caption':
                return 'text';
            case 'Participants':
				return 'largetext';

			case 'Template':
			case 'System':
            case 'DemoData':
            case 'Users':
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
		        $codeName = defined('SKIP_TARGET_BLANK') && SKIP_TARGET_BLANK ? md5(microtime(true)) : "";
                if ( $codeName == "" && is_numeric($_REQUEST['program']) ) {
                    $programIt = getFactory()->getObject('Project')->getExact($_REQUEST['program']);
                    if ( $programIt->getId() != '' ) {
                        $projectsNumber = count(preg_split('/,/', $programIt->get('LinkedProject')));
                        $codeName = $programIt->get('CodeName') . '-' .strval($projectsNumber + 1);
                    }
                }
		    	return $codeName;

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
				return translate('Кодовое название');
			case 'Caption':
				return translate('Название');
			case 'Template':
				return translate('Процесс');
			case 'Participants':
				return text(2001);
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
            case 'Users':
                return text(2501);
 		}
 	}

	 function drawCustomAttribute( $attribute, $value, $tab_index, $view )
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
                 if ( is_numeric($_REQUEST['program']) ) {
                     echo '<input type="hidden" name="program" value="'.$_REQUEST['program'].'">';
                 }
				 break;

             case 'DemoData':
                 if ( class_exists(getFactory()->getClass('IntegrationTracker')) ) {
                     $trackerField = new FieldDictionary(new IntegrationTracker());
                     $trackerField->SetId('Tracker');
                     $trackerField->SetName('Tracker');
                     $trackerField->setStyle('margin-left:20px;width:200px;');
                 }
                 echo $view->render(SERVER_ROOT_PATH . 'co/views/templates/ProjectDataSelector.tpl.php', array(
                     'value' => $this->getAttributeValue($attribute),
                     'trackerField' => $trackerField
                 ));
                 break;

             case 'Users':
                 $usersData = array();
                 $userIt = getFactory()->getObject('UserActive')->getAll();
                 while( !$userIt->end() ) {
                     if ( $userIt->getId() == getSession()->getUserIt()->getId() ) {
                         $userIt->moveNext();
                         continue;
                     }
                     $usersData[] = array(
                         'id' => $userIt->getId(),
                         'name' => $userIt->getDisplayName()
                     );
                     $userIt->moveNext();
                 }
                 echo $view->render(SERVER_ROOT_PATH . 'co/views/templates/ProjectUsersSelector.tpl.php', array(
                     'rows' => $usersData
                 ));
                 break;

			 default:
				 parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
		 }
	 }

	 function buildColumns($attributes)
     {
         $leftColumn = $attributes;
         $rightColumn = array();

         foreach( array('Participants', 'Users') as $attribute ) {
             $rightColumn[$attribute] = $attributes[$attribute];
             unset($leftColumn[$attribute]);
         }

         return array(
             $leftColumn, $rightColumn
         );
     }
}