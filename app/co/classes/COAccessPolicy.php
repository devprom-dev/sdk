<?php

class CoAccessPolicy extends AccessPolicy
{
	private $group_it = null;
	
 	private $subject_key = '';
 			
 	private $group_access_it = null;
	
	public function getGroupIt()
 	{
 		if ( is_object($this->group_it) )
 		{
 			$this->group_it->moveFirst();
 			
 			return $this->group_it;
 		}

 		$user_it = getSession()->getUserIt();

 	 	if ( $user_it->object->getAttributeType('GroupId') == '' )
 	    {
 	    	return getFactory()->getObject('co_UserGroup')->getEmptyIterator();
 	    }
 		
 		if ( $user_it->get('GroupId') == '' )
 		{
 		    return $this->group_it = getFactory()->getObject('co_UserGroup')->createCachedIterator(
 		    		array (
		 		    		array (
		 		    				'co_UserGroupId' => 0
		 		    		)
 					)
			);
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

		$plugins = getSession()->getPluginsManager();
		
 		$array = is_object($plugins) ? $plugins->getPluginsForSection('co') : array();
		
		foreach ( $array as $plugin )
		{
	        $access = $plugin->getEntityAccess( $action_kind, $this->getGroupIt(), $object);
	        
	    	if ( is_bool($access) ) return $access;
		}
		
 		if ( $ref_name == 'pm_Project' && $action_kind == ACCESS_CREATE )
		{
			return getFactory()->getObject('User')->getRecordCount() > 0;
		}

		return true;
	}
	
 	function getObjectAccess( $action_kind, &$object_it )
 	{ 
        if ( $object_it->object->getEntityRefName() != 'cms_PluginModule' )
        {
        	return $this->getDefaultObjectAccess( $action_kind, $object_it ); 
        }

        if ( !is_object($this->group_access_it) )
        {
            $class = getFactory()->getClass('co_AccessRight');
	            
            if ( $class != 'co_AccessRight' )
            {
                $access = getFactory()->getObject($class);
	                
                $this->group_access_it = $access->getOverriden();
            }
        }
        else
        {
            $this->group_access_it->moveFirst();
        }
        
        while ( is_object($this->group_access_it) && !$this->group_access_it->end() )
        {
        	$group_it = $this->getGroupIt();
	
			while( !$group_it->end() )
			{
	            $access = $this->group_access_it->getAccess( $group_it, $object_it );

	            if ( $access != -1 ) return $access;
		            
	            $group_it->moveNext();
        	}
	
            $this->group_access_it->moveNext();
        }
 	    
        return $this->getDefaultObjectAccess( $action_kind, $object_it );
 	}
 	
 	function getDefaultObjectAccess( $action_kind, &$object_it ) 
 	{
 		$plugins = getSession()->getPluginsManager();

 		$array = is_object($plugins) ? $plugins->getPluginsForSection('co') : array();
		
		foreach ( $array as $plugin )
		{
	    	$access = $plugin->getObjectAccess($action_kind, $this->getGroupIt(), $object_it);
		 
		    if ( is_bool($access) ) return $access;
		}
		
		return true;
	}
}
