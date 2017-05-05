<?php

include_once SERVER_ROOT_PATH."core/classes/model/ModelEntityOriginationService.php";

class ModelProjectOriginationService extends ModelEntityOriginationService
{
	private $session = null;
	private $shared = null;
    private $sharedSettings = array();
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
					? parent::getCacheCategory($object) : 'projects/'.$origin;
	}
	
	protected function getSharedSet()
	{
		if ( !is_object($this->shared) ) {
			$this->shared = getFactory()->getObject('SharedObjectSet');
		}
		return $this->shared;
	}

    protected function getSharedSettings()
    {
        if ( count($this->sharedSettings) > 0 ) return $this->sharedSettings;

        $shared = $this->getSharedSet();
        $shared_it = $shared->getAll();
        while( !$shared_it->end() ) {
            if ( $shared_it->get('Category') == '3' ) {
                $check_field = 'Common';
            }
            else {
                $check_field = $shared_it->get('Category');
            }
            $this->sharedSettings[$shared_it->getId()] = $check_field;
            $shared_it->moveNext();
        }
        return $this->sharedSettings;
    }

	protected function getSettingsIt()
	{
		if ( is_object($this->linked_settings_it) ) {
			$this->linked_settings_it->moveFirst();
			return $this->linked_settings_it;
		}
		
		$link = getFactory()->getObject('pm_ProjectLink');
       	if ( method_exists($link, 'getFor') ) {
			$this->linked_settings_it = $link->getFor( $this->session->getProjectIt()->getId() );
       	}
       	else {
       		$this->linked_settings_it = $link->getEmptyIterator();
       	}
       	return $this->linked_settings_it;
	}

    protected function getProjectIt()
    {
        if ( is_object($this->project_it) ) return $this->project_it;
        return $this->project_it = getFactory()->getObject('Project')->getRegistry()->Query(
            array(
                new ProjectLinkedSelfPredicate()
            )
        );
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

        $skip_entities = array('pm_ProjectLink','pm_ProjectRole','pm_AccessRight','pm_ObjectAccess','cms_Resource', 'pm_Methodology');
		if ( in_array($object->getEntityRefName(), $skip_entities) ) return $vpds;
		
		$settings_it = $this->getSettingsIt();
		if ( $settings_it->getId() < 1 ) return $vpds;

        $sharedSettings = $this->getSharedSettings();
        $check_field = $sharedSettings[strtolower(get_class($object))];

		$valuesToCheck = array (
		    'Project' => array (
		        'source' => array('1', '3'),
                'target' => array('2', '3')
            )
        );

		$linked_it = $this->getProjectIt();
        if ( $linked_it->getId() == '' ) return $vpds;

		$settings_it->moveFirst();
		while ( !$settings_it->end() ) {
            foreach( $valuesToCheck as $fieldProject => $toCheck ) {
                $linked_it->moveToId( $settings_it->get($fieldProject) );
                if ( $linked_it->getId() != '' && $linked_it->getId() == $settings_it->get($fieldProject) ) {
                    if ( $this->getSharedSet()->sharedInProject( $object, $linked_it ) ) {
                        $shared_in_forward = in_array($settings_it->get($check_field), $toCheck['source'])
                            && $settings_it->get('Direction') == 'source';
                        if ( $shared_in_forward ) $vpds[] = $linked_it->get('VPD');

                        $shared_in_backward = in_array($settings_it->get($check_field), $toCheck['target'])
                            && $settings_it->get('Direction') == 'target';
                        if ( $shared_in_backward ) $vpds[] = $linked_it->get('VPD');
                    }
                }
            }
            $settings_it->moveNext();
        }

		return array_unique($vpds);
	}		
}