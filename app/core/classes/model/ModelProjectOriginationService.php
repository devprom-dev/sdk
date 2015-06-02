<?php

include_once SERVER_ROOT_PATH."core/classes/model/ModelEntityOriginationService.php";

class ModelProjectOriginationService extends ModelEntityOriginationService
{
	private $session = null;
	
	private $shared = null;
	
	private $linked_settings_it = null;
	
	public function __construct( $session, $cache_service = null )
	{
		$this->session = $session;
		
		parent::__construct($cache_service);
	}

	public static function getOrigin( $project_id )
	{
		return md5(PID_HASH.$project_id);
	}
	
	public function getSession()
	{
		return $this->session;
	}
	
	public function getCacheCategory( $object )
	{
		$origin = $this->getSelfOrigin($object);
		
		return in_array($origin,array('',DUMMY_PROJECT_VPD)) 
					? parent::getCacheCategory($object) : 'pm-'.$origin;
	}
	
	protected function getSharedSet()
	{
		if ( !is_object($this->shared) )
		{
			$this->shared = getFactory()->getObject('SharedObjectSet');
		}
		
		return $this->shared;
	}
	
	protected function getSettingsIt()
	{
		if ( is_object($this->linked_settings_it) )
		{
			$this->linked_settings_it->moveFirst();
			
			return $this->linked_settings_it;
		}
		
		$link = getFactory()->getObject('pm_ProjectLink');

       	if ( method_exists($link, 'getFor') )
       	{
			$this->linked_settings_it = $link->getFor( $this->session->getProjectIt()->getId() );
       	}
       	else
       	{
       		$this->linked_settings_it = $link->getEmptyIterator();
       	}
       	
       	return $this->linked_settings_it;
	}
	
	protected function buildSelfOrigin( $object )
	{
		$value = parent::buildSelfOrigin( $object );
		
		if ( $value != DUMMY_PROJECT_VPD ) return $value;

		return self::getOrigin($this->session->getProjectIt()->getId());
	}
	
	public function buildAvailableOrigins( $object )
	{
		$vpds = array();
		
		if ( $object instanceof SharedObjectSet ) return $vpds;
		if ( $object instanceof CustomResource ) return $vpds;
		if ( $object instanceof PMObjectCacheable ) return $vpds;
		
		if ( in_array($object->getEntityRefName(), array('pm_ProjectLink','pm_ProjectRole','pm_AccessRight','pm_ObjectAccess','cms_Resource')) ) return $vpds;
		
		$settings_it = $this->getSettingsIt();
		
		if ( $settings_it->getId() < 1 ) return $vpds;

		
		if ( !is_object($this->shared) )
		{
			$this->shared = getFactory()->getObject('SharedObjectSet');
		}
		
		$shareable_it = $this->getSharedSet()->getExact(strtolower(get_class($object)));

		if ( $shareable_it->getId() == '' ) return $vpds;
		
		if ( $shareable_it->get('Category') == '3' )
		{
			$check_field = 'Common';
		}
		else
		{
			$check_field = $shareable_it->get('Category');
		}
		
		$direction = SHARED_DIRECTION_FWD;
		
		switch ( $direction )
		{
			case SHARED_DIRECTION_FWD:
				$source_values = array('1', '3');
				$target_values = array('2', '3');
				break;

			case SHARED_DIRECTION_BWD:
				$source_values = array('2', '3');
				$target_values = array('1', '3');
				break;
		}
		
		$settings_it->moveFirst();
		
		$linked_it = $this->getSession()->getLinkedIt();

		$linked_it->moveFirst();
		
		while ( !$settings_it->end() )
		{
		    $linked_it->moveToId( $settings_it->get('Project') );
		    
		    if ( $linked_it->getId() != $settings_it->get('Project') )
		    {
		        $settings_it->moveNext();
		        
		        continue;
		    }
		    
		    if ( !$this->getSharedSet()->sharedInProject( $object, $linked_it ) )
		    {
		        $settings_it->moveNext();
		        
		        continue;
		    }
		        
		    $shared_in_forward = in_array($settings_it->get($check_field), $source_values) 
		        && $settings_it->get('Direction') == 'source';
		    
		    if ( $shared_in_forward )
		    {
			    $vpds[] = $settings_it->get('VPD');
		    }

		    $shared_in_backward = in_array($settings_it->get($check_field), $target_values) 
		        && $settings_it->get('Direction') == 'target';
		     
			if ( $shared_in_backward )
		    {
    	        $vpds[] = $settings_it->get('VPD');
		    }
			
			$settings_it->moveNext();
		}

		return array_unique($vpds);		
	}		
}