<?php

define('ROLE_GUEST', 0);
define('ROLE_LINKEDGUEST', -1);
define('CHECK_ALL', 0);
define('CHECK_ROLE_BASED', 1);
 
include_once "AccessPolicyBase.php";

class AccessPolicyProject extends AccessPolicyBase
{
 	var $access_it, $base_role_map, $object_access_it,
 		$role_manager, $role_user, $role_guest, $role_linkedguest,
 		$linked_vpds, $role_it, $check_type, $role_reference_names;
 	private $project_it = null;

 	function buildRoles()
 	{
 		$project_roles = getSession()->getRoles();
 		$this->project_it = getSession()->getProjectIt();

 		$user_it = getSession()->getUserIt();
 		
 		if ( is_object($this->access_it) ) return;

 		$permissions = $this->getCacheService()->get('permissions', $this->getCacheKey());
 		if ( is_array($permissions) )
 		{
 			$this->base_role_map = $permissions['roles_map'];
 			$this->role_manager = $permissions['manager'];
 			$this->role_user = $permissions['user'];
 			$this->role_guest = $permissions['guest'];
 			$this->role_linkedguest = $permissions['linkedguest'];
 			$this->setRestrictions($permissions['restrictions']);

 			$role = getFactory()->getObject('pm_ProjectRole');

 			$this->role_it = $role->createCachedIterator($permissions['role_iterator']);
	 		
	 		$access = getFactory()->getObject('pm_AccessRight');

	 		$this->access_it = $access->createCachedIterator($permissions['access_iterator']);
	 		
	 		$object_access = getFactory()->getObject('pm_ObjectAccess');
	 		
	 		$this->object_access_it = $object_access->createCachedIterator($permissions['object_access_iterator']);

	 		return $permissions['roles'];
 		}
 		else
 		{
 			$permissions = array();
 		}
 		
 		$access = getFactory()->getObject('pm_AccessRight');
 		$role = getFactory()->getObject('pm_ProjectRole');

		$this->role_it = $role->getAll();
		while ( !$this->role_it->end() )
		{
			$this->base_role_map[$this->role_it->getId()] = $this->role_it->get('ProjectRoleBase');
			switch ( $this->role_it->get('ReferenceName') )
 			{
 				case 'lead':
 					$this->role_manager = $this->role_it->get('ProjectRoleBase');
 					break;
 						
 				case 'client':
 					$this->role_user = $this->role_it->get('ProjectRoleBase');
 					break;

 				case 'guest':
 					$this->role_guest = $this->role_it->getId();
 					$this->base_role_map[$this->role_it->getId()] = ROLE_GUEST;
 					break;

 				case 'linkedguest':
 					$this->role_linkedguest = $this->role_it->getId();
 					$this->base_role_map[$this->role_it->getId()] = ROLE_LINKEDGUEST;
 					break;
 			}
 				
 			$this->role_it->moveNext();
 		}

	 	$roles = array();
	 	
	 	$part_it = getSession()->getParticipantIt();

	 	if ( is_object($part_it) && $part_it->getId() != GUEST_UID )
	 	{
			$roles = $part_it->getRoles();
            $lead_it = getSession()->getProjectIt()->getLeadIt();
            if ( $this->role_manager < 1 || $lead_it->getId() < 1 )
            {
                // if there is no lead role in the project or there is no lead
                // then any team member will be a lead
                //
                $base = getFactory()->getObject('ProjectRoleBase');
                $base_it = $base->getByRef('ReferenceName', 'lead');

                $this->role_manager = $base_it->getId();
                $this->base_role_map[$roles[0]] = $this->role_manager;
            }
            $this->access_it = $access->getAll();
	 	}
	 	else if ( is_object($user_it) && $user_it->count() > 0 )
	 	{
            $this->access_it = $access->getAll();
			if ( $project_roles['linkedguest'] ) {
				$roles[] = $this->role_linkedguest;
			}
			if ( $project_roles['guest'] ) {
				$roles[] = $this->role_guest;
			}
	 	}

		$object_access = getFactory()->getObject('pm_ObjectAccess');
		$this->object_access_it = $object_access->getAll(); 

 		$permissions['roles'] = $roles;
 		$permissions['roles_map'] = $this->base_role_map;
 		$permissions['manager'] = $this->role_manager;
 		$permissions['user'] = $this->role_user;
 		$permissions['guest'] = $this->role_guest;
 		$permissions['linkedguest'] = $this->role_linkedguest;
        $permissions['restrictions'] = $user_it->getRestrictions();
 		$permissions['role_iterator'] = $this->role_it->getRowset();
	 	$permissions['access_iterator'] = $this->access_it->getRowset();
	 	$permissions['object_access_iterator'] = $this->object_access_it->getRowset();

 		$this->getCacheService()->set('permissions', $permissions, $this->getCacheKey());
 		
 		return $roles;
 	}
 	
 	public function setRoles( $roles )
 	{
 		$this->buildRoles();
 		
 		parent::setRoles($roles);
 	}
 	
 	function getSharedAccess( $object, $role_id )
 	{
 		switch ( $this->base_role_map[$role_id] )
 		{
 			case ROLE_GUEST:
 				return parent::getObjectAccess(
 				    ACCESS_READ,
                    getFactory()->getObject('Module')->getExact('ee/allprojects')
                );
 				
 			case ROLE_LINKEDGUEST:
 				return count(getFactory()->getEntityOriginationService()->getAvailableOrigins($object, SHARED_DIRECTION_BWD )) > 1;
 		}
 	}
 	
 	function getAttributeAccess( $action_kind, &$object, $attribute_refname, $reference_class = '' )
 	{
 		if ( $this->access_it->count() > 0 )
 		{
 			$access_map = array();

 			foreach( $this->getRoles() as $role_id )
 			{
                $access = $this->access_it->getAttributeAccess( $role_id, $object, $attribute_refname );
				$access_map[$role_id] = $access > -1
						? (($access == 1 && $action_kind == ACCESS_READ || $access == 2) ? 1 : 0)
						: null;

 			 	// entity level access for references
	 			if ( is_null($access_map[$role_id]) && $reference_class != '' )
	 			{
	 			    $className = getFactory()->getClass($reference_class);
                    $access = $this->access_it->getClassAccess( $role_id,
                        array_merge(
                            class_parents($className, false),
                            array($className)
                        )
                    );
                    $access_map[$role_id] = $access > -1
                            ? (($access == 1 && $action_kind == ACCESS_READ || $access == 2) ? 1 : 0)
                            : null;

                    if ( is_null($access_map[$role_id]) ) {
                        // only for backward compatibility
                        $access = $this->access_it->getEntityAccess( $role_id, $reference_class );

                        $access_map[$role_id] = $access > -1
                                ? (($access == 1 && $action_kind == ACCESS_READ || $access == 2) ? 1 : 0)
                                : null;
                    }
	 			}

	 			if ( is_null($access_map[$role_id]) ) {
                    $access = $this->access_it->getClassAccess( $role_id,
                        array_merge(
                            class_parents($object, false),
                            array(get_class($object))
                        )
                    );
                    $access_map[$role_id] = $access > -1
                        ? (($access == 1 && $action_kind == ACCESS_READ || $access == 2) ? 1 : 0)
                        : null;
                }
 			}

 			$access = $this->calculateAccess($access_map);
 			if ( is_bool($access) ) {
 			    return $this->checkRestrictions($access, $action_kind, $object);
            }
 		}

        if ( $object->getEntityRefName() == 'pm_Activity' ) {
            switch( $attribute_refname ) {
                case 'Participant':
                    return $action_kind == ACCESS_READ;
            }
        }

        $modifiableAttributes = array('State', 'TransitionComment');
 		if ( !in_array($attribute_refname, $modifiableAttributes) ) {
 		    return $this->checkRestrictions(true, $action_kind, $object);
        }

		return true;
 	}

 	function getEntityAccess( $action_kind, &$object ) 
 	{
 		if ( $this->access_it->count() > 0 )
 		{
 			$access_map = array();

 			foreach( $this->getRoles() as $role_id )
 			{
				$access = $this->access_it->getClassAccess( $role_id,
                    array_merge(
                        class_parents($object, false),
                        array(get_class($object))
                    )
                );
				$access_map[$role_id] = $access > -1
						? (($access == 1 && $action_kind == ACCESS_READ || $access == 2) ? 1 : 0)
						: null; 
 			}

 			$access = $this->calculateAccess($access_map);
 			if ( is_bool($access) ) return $this->checkRestrictions($access, $action_kind, $object);

 			// only for backward compatibility
 			$access_map = array();
 			
 			foreach( $this->getRoles() as $role_id ) {
 				// obolete method
				$access = $this->access_it->getEntityAccess( $role_id, $object->getEntityRefName() );
				$access_map[$role_id] = $access > -1
						? (($access == 1 && $action_kind == ACCESS_READ || $access == 2) ? 1 : 0)
						: null; 
 			}

 			$access = $this->calculateAccess($access_map);
 			if ( is_bool($access) ) return $this->checkRestrictions($access, $action_kind, $object);
 		}

		return parent::getEntityAccess( $action_kind, $object );
 	}
 	
 	function getDefaultEntityAccess( $action_kind, &$object ) 
 	{
 		$access_map = array();
		$roles = $this->getRoles();

		// role bases access rights
		foreach( $roles as $role_id ) {
			$access = $this->getDefaultEntityAccessRole($action_kind, $object, $role_id);
			$access_map[$role_id] = is_bool($access) ? ($access ? 1 : 0) : null;
		}

		$access = $this->calculateAccess($access_map);
 		if ( is_bool($access) ) return $access;

		return parent::getDefaultEntityAccess( $action_kind, $object );
 	}
 	
 	function getDefaultEntityAccessRole( $action_kind, &$object, $role_id ) 
 	{
 		$array = is_object(getFactory()->getPluginsManager()) ? getFactory()->getPluginsManager()->getPluginsForSection('pm') : array();
			
		foreach ( $array as $plugin )
		{
			$ref_name = $this->getRoleReferenceName($role_id);
			
			$access = $plugin->getEntityAccess( $action_kind, $ref_name, $object );

			if ( is_bool($access) ) return $access;
		}
 		
		if( is_object($object) )
		{
			$ref_name = $object->getClassName();
		}
		else 
		{
			return true;
		}

		switch( $this->base_role_map[$role_id] )
		{
			case ROLE_LINKEDGUEST:
				switch ( $ref_name )
				{
					case 'cms_Report':
					case 'pm_CustomReport':
					case 'pm_Workspace':
						return true;
				    case 'pm_Project':
						return parent::getDefaultEntityAccess($action_kind, $object)
                            && ($action_kind == ACCESS_CREATE || $action_kind == ACCESS_READ);
				}

			case ROLE_GUEST:
				switch ( $ref_name )
				{
					case 'cms_Report':
					case 'pm_CustomReport':
                    case 'pm_Activity':
                    case 'pm_Workspace':
					    return true;

					case 'pm_Build':
					case 'pm_Environment':
					case 'pm_Subversion':
					case 'pm_SubversionRevision':
					case 'pm_Version':
					case 'pm_Release':
					case 'pm_State':
					case 'pm_Transition':
					case 'pm_TransitionAttribute':
					case 'pm_TextTemplate':
                    case 'pm_Milestone':
					case 'WikiPage':
						return $action_kind == ACCESS_READ;
						
					case 'pm_ChangeRequest':
					case 'pm_Function':
					case 'pm_Question':
					case 'Comment':
					case 'ObjectChangeLog':
					case 'EmailQueue':
					case 'EmailQueueAddress':
					case 'cms_PluginModule':
					case 'pm_Attachment':
					case 'pm_Watcher':
					case 'pm_RequestTag':
					case 'pm_Tag':
					case 'pm_ChangeRequestTrace':
					case 'pm_ChangeRequestLink':
                    case 'pm_TaskTrace':
					case 'pm_FunctionTrace':
					case 'pm_CustomTag':
						return true;

					case 'pm_ProjectUse':
						return $action_kind == ACCESS_READ || $action_kind == ACCESS_MODIFY;
							
					case 'pm_Participant':
					    return $action_kind == ACCESS_READ;

					case 'pm_Invitation':
					case 'pm_ProjectRole':
					case 'pm_ParticipantRole':
						if ( $action_kind == ACCESS_CREATE )
						{
							$lead_it = $this->project_it->getLeadIt();
							if ( $lead_it->count() < 1 ) return true;
						}
						return $action_kind == ACCESS_READ && 
							$this->getSharedAccess( $object, $role_id );

                    case 'pm_Project':
                        return parent::getDefaultEntityAccess($action_kind, $object);

					default:
						return $action_kind == ACCESS_READ &&
							$this->getSharedAccess( $object, $role_id );
				}

			case $this->role_manager: 
			    switch ( $ref_name )
			    {
			        case 'Blog':
			        case 'BlogPost':
			            return $this->project_it->getMethodologyIt()->get('IsBlogUsed') == 'Y';
			        case 'pm_Project':
                        return parent::getDefaultEntityAccess($action_kind, $object) && $action_kind != ACCESS_DELETE;
			        case 'pm_Methodology':
                        return $action_kind != ACCESS_DELETE;
                    case 'pm_SubversionRevision':
			            return $action_kind != ACCESS_MODIFY;
			        default:
			            return true;
			    }
			    
			    break;

			case $this->role_user:
				switch ( $ref_name )
				{
					case 'pm_Build':
						return $action_kind == ACCESS_READ;
                    case 'pm_Participant':
                        return false;
					case 'pm_Competitor':
					case 'pm_FeatureAnalysis':
						return true;
				}
				
			default:
				switch ( $ref_name )
				{
                    case 'pm_Activity':
                        return true;

					case 'pm_Version':
					case 'pm_Release':
					case 'pm_Methodology':
					case 'pm_ProjectRole':
					case 'pm_ParticipantRole':
					case 'pm_State':
					case 'pm_Transition':
					case 'pm_TransitionAttribute':
					case 'pm_TransitionRole':
					case 'cms_Resource':
						return $action_kind == ACCESS_READ;
						
					case 'pm_Participant':
					case 'pm_Invitation':
						return in_array($action_kind, array(ACCESS_READ));
						
					case 'pm_Project': 
						return parent::getDefaultEntityAccess($action_kind, $object)
                            && ($action_kind == ACCESS_CREATE || $action_kind == ACCESS_READ);
					    
					case 'pm_AccessRight':
						return $action_kind == ACCESS_READ;

					case 'Blog':
					case 'BlogPost':
						return $this->project_it->getMethodologyIt()->get('IsBlogUsed') == 'Y';

                    case 'pm_SubversionRevision':
                        return $action_kind != ACCESS_MODIFY;
				}
				
				if ( strtolower(get_class($object)) == 'projectpage' )
				{
					return $this->project_it->getMethodologyIt()->get('IsKnowledgeUsed') == 'Y';
				}
		}
	}
	
 	function getObjectAccess( $action_kind, &$object_it ) 
 	{
 		$access_map = array();
 		$roles = $this->getRoles();

		// role bases access rights
		foreach( $roles as $role_id ) {
			$access = $this->getObjectAccessRole($action_kind, $object_it, $role_id);
			$access_map[$role_id] = is_bool($access) ? ($access ? 1 : 0) : null;
		}

		$access = $this->calculateAccess($access_map);
		if ( is_bool($access) ) return $this->checkRestrictions($access, $action_kind, $object_it->object);
		
 	 	switch ( $object_it->object->getClassName() )
		{
			case 'WikiPageType':
				if ( $action_kind == ACCESS_DELETE )
				{
					$this->setReason( text(938) );
	
					$wiki = getFactory()->getObject('WikiPage');
					
					$count = $wiki->getByRefArrayCount(
						array('PageType' => $object_it->getId()) );
							
					if ( $count > 0 )
					{
						return false;
					}
				}
				break;
					
			case 'WikiPage':
				if ( is_a($object_it->object, 'WikiPage') )
				{
					switch( $object_it->object->getReferenceName() )
					{
						case WikiTypeRegistry::KnowledgeBase:
							if ( $action_kind == ACCESS_DELETE && $object_it->get('ParentPage') == '' )
							{
								return false;
							}
					}
				}
				break;
					
			case 'pm_ParticipantRole': 
				if ( $action_kind == ACCESS_DELETE )
				{
					$lead_it = getSession()->getProjectIt()->getLeadIt();
					
					$role_it = $object_it->getRef('ProjectRole');
					
					if ( $lead_it->count() < 2 && $role_it->get('ReferenceName') == 'lead' )
					{
						$this->setReason( text(1046) );
						
						return false;
					}
				}
				break;
		}
				
		return parent::getObjectAccess( $action_kind, $object_it ); 
 	}
	
 	function getObjectAccessRole( $action_kind, &$object_it, $role_id )
 	{ 
		$ref_name = $object_it->object->getClassName();

 		if ( $this->access_it->count() > 0 )
 		{
			switch ( $ref_name )
			{
				case 'cms_PluginModule':
					$access = $this->access_it->getModuleAccess( $role_id, $object_it );

					if ( $access > -1 )
					{
						return $access == 1;
					}
					
					break;

				case 'cms_Report':
				case 'pm_CustomReport':
					$access = $this->access_it->getReportAccess( $role_id, $object_it );

					if ( $access > -1 )
					{
						return $access == 1;
					}
					break;

				case 'WikiPage':
					$access = $this->access_it->getWikiAccess( $role_id, $object_it );
	
					if ( $access > -1 )
					{
						return $access == 1 && $action_kind == ACCESS_READ
							|| $access == 2;
					}
					break;
			}
 		}

 		if ( $this->object_access_it->count() > 0 )
 		{
 			$access = $this->object_access_it->getAccess( $role_id, $object_it );

			if ( $access > -1 )
			{
				return $access == 1 && $action_kind == ACCESS_READ
					|| $access == 2;
			}
			
			switch ( strtolower(get_class($object_it->object)) )
			{
				case 'projectpage':

					$parent_it = $object_it->getParentsIt();
					
					while( !$parent_it->end() )
					{
			 			$access = $this->object_access_it->getAccess( $role_id, $parent_it );
			 			
						if ( $access > -1 )
						{
							if ( !($access == 1 && $action_kind == ACCESS_READ) || $access != 2 ) return false;
						}
						
						$parent_it->moveNext();
					}
					
					break;
			}			
 		}
 	}
 	
 	function getDefaultObjectAccess( $action_kind, &$object_it ) 
 	{
 		$access_map = array();
		$roles = $this->getRoles();

		// role bases access rights
		foreach( $roles as $role_id ) {
			$access = $this->getDefaultObjectAccessRole($action_kind, $object_it, $role_id);
			$access_map[$role_id] = is_bool($access) ? ($access ? 1 : 0) : null;
		}
		
		$access = $this->calculateAccess($access_map);
 		if ( is_bool($access) ) return $access;

		return parent::getDefaultObjectAccess( $action_kind, $object_it ); 
 	}
 	 	
 	function getDefaultObjectAccessRole( $action_kind, &$object_it, $role_id ) 
 	{
		$ref_name = $object_it->object->getClassName();	

		$this->project_it = getSession()->getProjectIt();
		
		$user_it = getSession()->getUserIt();
		
		$part_it = getSession()->getParticipantIt();

		switch ( $this->base_role_map[$role_id] ) 
		{
			case $this->role_manager:
			    
				if ( $ref_name == 'pm_Participant' ) 
				{
					if ( $object_it->getId() == $part_it->getId() ) 
					{
						$this->setReason(text(1247));
						return $action_kind != ACCESS_DELETE;
					}
					
					$lead_it = $this->project_it->getLeadIt();
					
					while ( !$lead_it->end() )
					{
						if ( $lead_it->getId() == $part_it->getId() )
						{
							if ( $lead_it->count() == 1 && $lead_it->getId() == $object_it->getId() )
							{
								return $action_kind != ACCESS_DELETE;
							}
							else
							{
								break;
							}
						}
						
						$lead_it->moveNext();
					}
					
					return $action_kind == ACCESS_READ 
					    || $object_it->get('VPD') == getSession()->getProjectIt()->get('VPD');
				}
				
				if ( $ref_name == 'pm_ProjectRole' && $action_kind == ACCESS_DELETE ) 
				{
					$part = getFactory()->getObject('pm_Participant');
					return !$part->hasTeamMembers( $object_it );
				}

                if ( $ref_name == 'pm_Activity' ) return true;

                break;

			case ROLE_GUEST:
			case ROLE_LINKEDGUEST: 

				switch ( $ref_name ) 
				{
					case 'cms_Snapshot':
						return $action_kind == ACCESS_READ;
					
					case 'pm_ArtefactType':
						if ( !isset($this->artefact) )
						{
							$this->artefact = getFactory()->getObject('pm_Artefact');
						}
	
						$this->artefact_it = $this->artefact->getByRef('Kind', $object_it->getId());
						return $action_kind == ACCESS_READ && $this->artefact_it->count() > 0;
						
					case 'pm_ChangeRequest':
						return $action_kind == ACCESS_READ || 
							$object_it->get('Author') == $user_it->getId() && !$object_it->IsFinished();
								
					case 'pm_Question':
						return $action_kind == ACCESS_READ || 
							$object_it->get('Author') == $user_it->getId();
				}
				
				break;

			default:
				switch ( $ref_name )
				{
					case 'pm_Question':
						
						return $action_kind == ACCESS_READ || $object_it->get('Author') == $user_it->getId();
					
					case 'pm_ProjectUse':
						
						return $action_kind == ACCESS_READ || $action_kind == ACCESS_MODIFY;
						
					case 'pm_Participant':

						if ( $object_it->getId() == $part_it->getId() )
						{
					    	$this->setReason(text(1247));
					    	return true;
						}

						break;
						
					case 'cms_Snapshot':
						return $object_it->get('SystemUser') == $user_it->getId() || $action_kind == ACCESS_READ;

                    case 'pm_Activity':
                        return $object_it->get('Participant') == $user_it->getId() || $action_kind == ACCESS_READ;

					case 'pm_CustomReport':
						// common reports can be modified or deleted by lead only
						return $object_it->get('Author') > 0 || $action_kind == ACCESS_READ;
				}
		}
	}
	
 	function getRoleReferenceName( $role_id )
	{
		$this->getRoles();
		
	    if ( is_array($this->role_reference_names) ) return $this->role_reference_names[$role_id];

	    $this->role_it->moveFirst();
        
        while( !$this->role_it->end() )
        {
            $this->role_reference_names[$this->role_it->getId()] = $this->role_it->get('ReferenceName');
            
            $this->role_it->moveNext();
        }
        
        return $this->role_reference_names[$role_id];
	}
	
	function getRoleByBase( $base_role_id )
	{
	    $roles = array_flip( $this->base_role_map );
	    
	    return $roles[$base_role_id];
	}

	function checkRestrictions( $access, $action_kind, $object )
    {
        if ( $access && in_array($action_kind, array(ACCESS_CREATE, ACCESS_MODIFY, ACCESS_DELETE)) ) {
            return !in_array(get_class($object), $this->getRestrictions());
        }
        return $access;
    }
}
