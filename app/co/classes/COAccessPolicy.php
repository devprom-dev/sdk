<?php

class CoAccessPolicy extends AccessPolicy
{
	private $group_it = null;
 	private $subject_key = '';
 	private $group_access_it = null;

	public function getGroupIt()
 	{
 		if ( is_object($this->group_it) ) {
 			$this->group_it->moveFirst();
 			return $this->group_it;
 		}

 		$user_it = getFactory()->getObject('User')->getRegistry()->Query(
 				array (
 						new FilterInPredicate(getSession()->getUserIt()->getId())
 				)
 		);
 	 	if ( $user_it->object->getAttributeType('GroupId') == '' ) {
 	    	return $this->group_it = getFactory()->getObject('co_UserGroup')->getEmptyIterator();
 	    }
 		if ( $user_it->get('GroupId') == '' ) {
 		    return $this->group_it = getFactory()->getObject('co_UserGroup')->getEmptyIterator();
 		} 		
 		return $this->group_it = $user_it->getRef('GroupId');
 	}
 	
 	public function setGroupIt( $group_it )
 	{
 		$this->group_it = $group_it;
 	}
 	
 	public function getSubjectKey()
 	{
 		return $this->subject_key == '' 
 				? ($this->subject_key = md5(join(',', $this->getGroupIt()->idsToArray())))
 				: $this->subject_key;
 	}
 	
 	function getEntityAccess( $action_kind, &$object ) 
 	{
	    return $this->getDefaultEntityAccess( $action_kind, $object );
 	}
 	
 	function getDefaultEntityAccess( $action_kind, &$object ) 
 	{
 		if( is_object($object) )
		{
			$ref_name = $object->getClassName();
		}
		else 
		{
			return true;
		}

		$plugins = getFactory()->getPluginsManager();
		
 		$array = is_object($plugins) ? $plugins->getPluginsForSection('co') : array();
		
		foreach ( $array as $plugin ) {
	        $access = $plugin->getEntityAccess( $action_kind, $this->getGroupIt(), $object);
	    	if ( is_bool($access) ) return $access;
		}

		if ( $action_kind == ACCESS_CREATE ) {
			switch( $ref_name ) {
				case 'pm_Project':
				case 'co_ProjectGroup':
					return getSession()->getUserIt()->getId() == '' || getSession()->getUserIt()->get('IsReadonly') == 'N';
			}
		}
		
		return true;
	}
	
 	function getObjectAccess( $action_kind, &$object_it )
 	{ 
        if ( $object_it->object->getEntityRefName() != 'cms_PluginModule' ) {
        	return $this->getDefaultObjectAccess( $action_kind, $object_it ); 
        }

        if ( !is_object($this->group_access_it) ) {
            $class = getFactory()->getClass('co_AccessRight');
	        if ( $class != 'co_AccessRight' ) {
				$access = getFactory()->getObject($class);
				$access->addSort( new SortAttributeClause('UserGroup') );
	            $this->group_access_it = $access->getOverriden();
            }
        }
        else {
            $this->group_access_it->moveFirst();
        }

		$access_map = array();
		$default = $this->getDefaultObjectAccess( $action_kind, $object_it );

		if ( is_object($this->group_access_it) )
		{
			$group_it = $this->getGroupIt();
			while( !$group_it->end() && $group_it->getId() > 0 ) {
				$this->group_access_it->moveTo('UserGroup', $group_it->getId());
				while ( $this->group_access_it->get('UserGroup') == $group_it->getId() ) {
					$access = $this->group_access_it->getAccess( $group_it, $object_it );
					$access_map[$group_it->getId()] = $access != -1 ? ($access ? 1 : 0) : $default;
					$this->group_access_it->moveNext();
				}
				$group_it->moveNext();
			}
			if ( $group_it->count() < 1 ) {
				$access = $this->group_access_it->getAccess( $group_it, $object_it );
				$access_map[0] = $access != -1 ? ($access ? 1 : 0) : $default;
			}
		}

		if ( count($access_map) > 0 ) {
			$access = $this->calculateAccess($access_map);
			if ( is_bool($access) ) return $access;
		}

        return $default;
 	}
 	
 	function getDefaultObjectAccess( $action_kind, &$object_it ) 
 	{
 		$plugins = getFactory()->getPluginsManager();

 		$array = is_object($plugins) ? $plugins->getPluginsForSection('co') : array();
		foreach ( $array as $plugin ) {
	    	$access = $plugin->getObjectAccess($action_kind, $this->getGroupIt(), $object_it);
		    if ( is_bool($access) ) return $access;
		}
		
		return true;
	}

	protected function calculateAccess( $access_map )
	{
		$overriden_access = array_filter( $access_map, function($value) {
			return !is_null($value);
		});
		// access permitted
		if ( array_sum($overriden_access) > 0 ) return true;
		// no access across all roles
		if ( count($access_map) == count($overriden_access) ) return false;
	}

}
