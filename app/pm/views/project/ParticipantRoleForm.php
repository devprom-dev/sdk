<?php

class ParticipantRoleForm extends PMPageForm
{
	function __construct() 
	{
	    global $model_factory;
	    
		parent::__construct( $model_factory->getObject('pm_ParticipantRole') );
	}

	function IsAttributeEditable( $attr_name )
	{
		if ( $attr_name == 'Participant' ) return false;
		
		return parent::IsAttributeEditable( $attr_name );
	}
	
	function IsAttributeVisible( $attr_name ) 
 	{
 		$object_it = $this->getObjectIt();
 		
		if($attr_name == 'OrderNum' || $attr_name == 'Project') 
			return false;
			
		if ( $attr_name == 'ProjectRole' )
		{
			// disable to delete single lead role
			if ( isset($object_it) )
			{
				$project_role_it = $object_it->getRef('ProjectRole');  
				return $project_role_it->get('ReferenceName') != 'lead' || getFactory()->getAccessPolicy()->can_delete($this->object_it);
			}
			else
			{
				return true;
			}
		}
			
		return parent::IsAttributeVisible( $attr_name );
	}
	
	function validateInputValues( $id, $action )
	{
		global $model_factory, $_REQUEST, $project_it;
		
		$part_role = $model_factory->getObject('pm_ParticipantRole');

		// check for duplicate role
		$role_it = $part_role->getByRefArray(
			array( 'Participant' => $_REQUEST['Participant'],
				   'ProjectRole' => $_REQUEST['ProjectRole'] ) );

		if( $role_it->count() > 0 && $role_it->getId() != $id )
		{ 
			return text(627);
		}
		
		// check for lead is in the project
		$role = $model_factory->getObject('ProjectRole');
		$part_role = $model_factory->getObject('pm_ParticipantRole');
		
		$lead_it = $role->getByRef('ReferenceName', 'lead');
		$lead_roles = $lead_it->idsToArray();

		$part_role_it = $part_role->getAll();
		$roles = $part_role_it->fieldToArray('ProjectRole');
		array_push( $roles, $_REQUEST['ProjectRole'] );

		if( count( array_intersect($lead_roles, $roles) ) < 1 )
		{ 
			return text(1046);
		}
		
		return parent::validateInputValues( $id, $action );
	}

	function createFieldObject( $name ) 
	{
		switch( $name )
		{
		    case 'ProjectRole':

				$role = getFactory()->getObject('pm_ProjectRole');
				
				$role->addFilter( new ProjectRoleInheritedFilter() );
				$role->addFilter( new FilterBaseVpdPredicate() );
				
				return new FieldDictionary( $role );
		    	
		    case 'Participant':
		    	
				return new FieldAutocompleteObject( getFactory()->getObject('ParticipantProjectRelated') );
		    			    	
		    default:
				return parent::createFieldObject( $name );    	
		}
	}
}
 