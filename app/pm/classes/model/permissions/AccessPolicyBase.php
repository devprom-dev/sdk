<?php

include_once SERVER_ROOT_PATH."co/classes/COAccessPolicy.php";

abstract class AccessPolicyBase extends COAccessPolicy
{
    private $session;
    
    private $methodology_it;
    
    private $project_it;
    
 	private $roles = array();
 	
 	private $subject_key = '';
    
    function __construct( $cache_service, PMSession $session = null )
    {
        $this->session = is_object($session) ? $session : getSession();
        
        parent::__construct($cache_service);
    }
    
 	public function getRoles()
 	{
 		if ( count($this->roles) > 0 ) return $this->roles;
 		
 		return $this->roles = $this->buildRoles();
 	}
 	
 	public function setRoles( $roles )
 	{
 		$this->roles = $roles;
 	}

 	public function getSubjectKey()
 	{
 		return $this->subject_key == '' 
 				? ($this->subject_key = md5(join(',', $this->getRoles())))
 				: $this->subject_key;
 	}
 	
	abstract protected function buildRoles(); 
    
    function getEntityAccess( $action_kind, &$object ) 
 	{
        if ( !is_object($this->project_it)) $this->project_it = $this->session->getProjectIt();
        if ( !is_object($this->methodology_it)) $this->methodology_it = $this->project_it->getMethodologyIt();
		
 		// methodology based access rights
		switch ( $object->getClassName() )
		{
		    case 'pm_Activity':
		        
		        if ( !$this->methodology_it->IsTimeTracking() ) return false;

		        break;
		        
		    case 'pm_Milestone':
		        
		        if ( !$this->methodology_it->HasMilestones() ) return false;
		        
		        break;
		        
		    case 'pm_Release':

		        
		        if ( !$this->methodology_it->HasPlanning() ) return false;
		        
		        break;
		        
		    case 'pm_Version':
		        
		        if ( !$this->methodology_it->HasReleases() ) return false;
		        
		        break;

		    case 'pm_Function':
		        
		        if ( !$this->methodology_it->HasFeatures() ) return false;
		        
		        break;
		        
		     case 'cms_User': return $action_kind == ACCESS_READ;
		}
		
		switch ( strtolower(get_class($object)) )
		{
		    case 'projectpage':
		        
		        if ( $this->methodology_it->get('IsKnowledgeUsed') != 'Y' ) return false;
		        
		        break;
		}
		
		return parent::getEntityAccess( $action_kind, $object );
	}
	
	function getRoleReferenceName( $role_id )
	{
	    return 'guest';
	}
	
 	function getObjectAccess( $action_kind, &$object_it ) 
 	{
       	global $model_factory;

		$array = is_object(getSession()->getPluginsManager()) 
			? getSession()->getPluginsManager()->getPluginsForSection('pm') : array();
			
		foreach ( $array as $plugin )
		{
			foreach( $this->getRoles() as $role_id )
			{
				$ref_name = $this->getRoleReferenceName($role_id);
				 
				$access = $plugin->getObjectAccess( $action_kind, $ref_name, $object_it );
					 
				if ( is_bool($access) ) return $access;
			}
		}
		
		$ref_name = $object_it->object->getClassName(); 
		
 		switch ( $ref_name )
		{
			case 'pm_Project':

			    if ( !$object_it->IsPortfolio() )
			    {
             		$part = $model_factory->getObject('pm_Participant');
            
             		$count = $part->getByRefArrayCount( array (
             		        'SystemUser' => getSession()->getUserIt()->getId(), 
             		        "IFNULL(IsActive,'Y')" => "Y"
             		));

             		if ( $count > 0 ) return true;
			    }
			    
			    break;
			    
			case 'pm_Activity':
			    
			    if ( $action_kind != ACCESS_DELETE ) return true;
			    
			    return $object_it->get('Participant') == getSession()->getUserIt()->getId();
		} 				

		return parent::getObjectAccess( $action_kind, $object_it );
	}
	
 	function getDefaultObjectAccess( $action_kind, &$object_it ) 
 	{
 		$ref_name = $object_it->object->getClassName(); 
		
		switch ( $ref_name )
		{
		    case 'cms_PluginModule':
		        
                if ( $object_it->get('AccessEntityReferenceName') != '' )
         		{
         			$object = getFactory()->getObject($object_it->get('AccessEntityReferenceName'));
         			
         			$access = $this->getEntityAccess(
         			        $object_it->get('AccessType') != '' ? $object_it->get('AccessType') : $action_kind,
         			        $object
					);
         			
         			if ( is_bool($access) ) return $access;
         		}
		        
     		    break;
     		    
		    case 'cms_Report':
		    	
		    	if ( $object_it->get('Module') != '' )
		    	{
         			$module_it = getFactory()->getObject('Module')->getExact($object_it->get('Module'));
         			
         			return $this->getObjectAccess(ACCESS_READ, $module_it);
		    	}
		    	
		    	break;
		} 		
		
		return parent::getDefaultObjectAccess( $action_kind, $object_it );
 	}
}
