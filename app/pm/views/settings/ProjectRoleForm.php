<?php

class ProjectRoleForm extends PMPageForm
{
	function validateInputValues( $id, $action )
	{
	    global $model_factory;
	    
	    // check at least one 'lead' derived role is in the list or project roles
	    $base = $model_factory->getObject('ProjectRoleBase');
	    
	    $roles = array( $base->getExact($_REQUEST['ProjectRoleBase'])->get('ReferenceName') );
	    
	    $role = $model_factory->getObject('ProjectRole');
	    
	    $role_it = $role->getAll();
	    
	    $base_ids = array();
	    
	    while ( !$role_it->end() )
	    {
	        if ( $role_it->getId() == $id ) 
	        {    
	            $role_it->moveNext();
	            
	            continue;
	        }
	        
	        $base_ids[] = $role_it->get('ProjectRoleBase');
	        
	        $role_it->moveNext();
	    }
	            
	    $base_it = $base->getExact($base_ids);
	    
	    $roles = array_merge( $roles, $base_it->fieldToArray('ReferenceName') );

	    if ( !in_array('lead', $roles) )
	    {
	        return text(1331);
	    }
	}
	
 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch ( $attr_name )
 		{
 			case 'ReferenceName':
 				return false;
 				
 			case 'ProjectRoleBase':
 				return true;

 			default:
 				return parent::IsAttributeVisible( $attr_name );
 		}
 	}

	function createFieldObject( $attr_name ) 
	{
		if( $attr_name == 'ProjectRoleBase') {
			return new FieldDictionary( getFactory()->getObject('ProjectRoleBase') );
		}
		return parent::createFieldObject( $attr_name );
	}
}