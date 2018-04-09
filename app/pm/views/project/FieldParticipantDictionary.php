<?php

class FieldParticipantDictionary extends FieldDictionary
{
    private $iterationId = '';

    function __construct( $iterationId = '' ) {
        parent::__construct(getFactory()->getObject('ProjectUser'));
        $this->iterationId = $iterationId;
    }

    function getGroups()
	{
		$groups = array();
		
		$role_it = getFactory()->getObject('ProjectRole')->getRegistry()->Query(
            array (
                new FilterBaseVpdPredicate()
            )
		);
		while( !$role_it->end() ) {
			$groups[$role_it->getId()] = array (
					'label' => $role_it->getDisplayName()
			);
			$role_it->moveNext();
		}
			
		return $groups;
	}
	
 	function getOptions()
	{
 		$part_it = $this->getObject()->getAll();
 		return defined('PERMISSIONS_ENABLED')
                    ? $this->getRoleBasedOptions($part_it)
                    : $this->getSimpleOptions($part_it);
	}
	
	protected function getSimpleOptions($part_it)
	{
		$options = array();
		while ( !$part_it->end() ) 
 		{
		    $options[] = array (
		        'value' => $part_it->getId(),
                'caption' => $part_it->getDisplayNameExt($this->iterationId)
            );
 			$part_it->moveNext();
 		}
 		return $options;
	}
	
	protected function getRoleBasedOptions($part_it)
	{
 		$groups = array();
	 	while ( !$part_it->end() ) 
 		{
 		    $roles = preg_split('/,/', $part_it->get('ProjectRole'));
 		    foreach( $roles as $roleId ) {
                $groups[$roleId][] = array (
                    'value' => $part_it->getId(),
                    'caption' => $part_it->getDisplayNameExt($this->iterationId)
                );
            }
 			$part_it->moveNext();
 		}
 		
 		$options = array();
 		foreach( $groups as $group => $items ) {
 			foreach( $items as $item ) {
 				$options[] = array_merge( $item, array('group' => $group) );
 			}
 		}
 		return $options;
	}
}