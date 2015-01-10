<?php

include "ParticipantRoleIterator.php";

class ParticipantRole extends Metaobject
{
 	function __construct() 
 	{
		parent::__construct('pm_ParticipantRole');
	}

	function createIterator() 
	{
		return new ParticipantRoleIterator( $this );
	}
	
	function getDefaultAttributeValue( $attr )
	{
		switch ( $attr )
		{
			case 'Project':
				return getSession()->getProjectIt()->getId();
				
			case 'ProjectRole':
				$role = getFactory()->getObject('ProjectRole');
				return $role->getRegistry()->Query(
							array (
									new FilterVpdPredicate(),
									new FilterNotInPredicate($role->getByRef('ReferenceName', 'lead')->getId())
							)
					)->getId();
				
			default:
				return parent::getDefaultAttributeValue( $attr );
		}
	}
}
