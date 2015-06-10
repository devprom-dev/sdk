<?php

class ParticipantForm extends PMPageForm
{
 	var $warning_message;
	
	function __construct() 
	{
		parent::__construct( getFactory()->getObject('pm_Participant') );
	}
		
	function editable()
	{
		$object_it = $this->getObjectIt();
		
		if ( is_object($object_it) && $object_it->getId() == getSession()->getParticipantIt()->getId() ) return true;
		
		return parent::editable();
	}
	
	function processEmbeddedForms( $object_it )
	{
		parent::processEmbeddedForms( $object_it );

		$hasRoles = preg_split('/,/', $object_it->get('ProjectRole'));
		
		if ( $_REQUEST['ProjectRole'] != '' && !in_array($_REQUEST['ProjectRole'], $hasRoles) ) 
		{
			$role = getFactory()->getObject('pm_ParticipantRole');
			
			$role->add_parms( array(
			    'Participant' => $object_it->getId(), 
				'ProjectRole' => $_REQUEST['ProjectRole'],
			    'Capacity' => $_REQUEST['Capacity']
			));
		}
	}

 	function getCaption() 
 	{
		return translate('Профиль участника проекта');
	}

	function validateInputValues( $id, $action )
	{
		global $model_factory, $_REQUEST;
		
		$user = $model_factory->getObject('cms_User');
		
		$user_it = $user->getExact($_REQUEST['SystemUser']);

		if( $user_it->count() < 1 )
		{ 
			return text(625);
		}
		
		if ( $action == 'add' )
		{
			$part = $model_factory->getObject('pm_Participant');
			$part->addFilter( new FilterBaseVpdPredicate() );
			
			$part_it = $part->getByRef( 'SystemUser', $user_it->getId() );
			
			if ( $part_it->count() > 0 )
			{
				return text(626);
			}
		}
		
		$_REQUEST['Project'] = getSession()->getProjectIt()->getId();
		
		return parent::validateInputValues( $id, $action );
	}

 	function IsNeedButtonNew() {
		return false;
	}

 	function IsNeedButtonCopy() {
		return false;
	}

	function IsAttributeEditable( $attr_name )
	{
		$object_it = $this->getObjectIt();
		
		if ( is_object($object_it) && $object_it->getId() == getSession()->getParticipantIt()->getId() ) return true;
		
		return parent::IsAttributeEditable( $attr_name );
	}
	
 	function IsAttributeVisible( $attr_name ) 
 	{
 		global $_REQUEST;
 		
 		$SystemUser = $_REQUEST['SystemUser'];
		$object_it = $this->getObjectIt();
		
		// при обычной модификации не позволяем управлять этим флагом
		if( $attr_name == 'OverrideUser' ) return false;
		
		$b_show_participant_own_attributes = false;

		if( $attr_name == 'Salary' ) 
		{
			return false;
		}

		if ( $attr_name == 'SystemUser' ) return true;

		// определим, перекрыты ли уже атрибуты
		if ( is_object($object_it) ) 
		{
			$b_show_participant_own_attributes = $object_it->get('OverrideUser') == 'Y';
		}

		if($b_show_participant_own_attributes) 
		{
			if($attr_name == 'Password') return false;
			if($attr_name == 'OrderNum') return false;
			if($attr_name == 'Project') return false;
		}
		
		switch ( $attr_name )
		{
			case 'Caption':
			case 'Email':
			case 'Login':
			case 'OrderNum':
				return false;

			case 'Capacity':
			case 'ProjectRole':
				return !is_object($object_it) || is_object($object_it) && $object_it->get('ProjectRole') == '';
				
			case 'Notification':
				return true;
				
			case 'IsActive':
				return is_object($object_it) && getFactory()->getAccessPolicy()->can_delete($object_it);
				
			default:
				return parent::IsAttributeVisible( $attr_name );
		}
	}

 	function getFieldValue( $attr )
 	{
 	    switch ( $attr )
 	    {
 	        case 'Notification':
 	            if ( !is_object($this->getObjectIt()) ) {
 	            	return getSession()->getProjectIt()->getDefaultNotificationType();
 	            }
 	            return parent::getFieldValue( $attr );
 	            
			default:
 	            return parent::getFieldValue( $attr );
 	    }
 	}
	
 	function createFieldObject( $name ) 
	{
		global $model_factory, $plugins;
		
		if($name == 'SystemUser') 
		{
			return new FieldAutoCompleteObject( getFactory()->getObject('UserActive'), array('Caption', 'Email') );
		}

		if($name == 'ProjectRole')	
		{
			$role = $model_factory->getObject('pm_ProjectRole');
			$role->addFilter( new ProjectRoleInheritedFilter() );
			
			return new FieldDictionary( $role );
		}

		if ( $name == 'Notification' )	
		{
			return new FieldDictionary( $model_factory->getObject('Notification') );
		}

		return parent::createFieldObject( $name );
	}

	function createField( $attr )
	{
	    global $plugins;
	    
	    $field = parent::createField( $attr );
	    
	    switch( $attr )
	    {
	        case 'SystemUser':
	            
	            if ( $plugins->hasIncluded('ProCloudPMPlugin') )
	            {
	                $field->setReadonly(true);
	            }
	            
	            $object_it = $this->getObjectIt();
	            
	            if ( is_object($object_it) && $object_it->getId() != '' )
	            {
	                $field->setReadonly(true);
	            }
	            
                break;	            
	    }
	    
	    return $field;
	}
	
 	function drawField( $name, $index ) 
 	{
		parent::drawField( $name, $index );
		
		if($name == 'Password') 
		{
			echo '</tr>';
			parent::drawField( 'RepeatPassword', $index + 1 );
			echo '<tr>';
		}
	}
	
	function getFieldDescription( $name )
	{
		switch ( $name )
		{
			case 'OverrideUser':
				return text(54);
				
			case 'IsActive':
				return text(1054);
			
			default:
				return parent::getFieldDescription( $name );
		}
	}
}
