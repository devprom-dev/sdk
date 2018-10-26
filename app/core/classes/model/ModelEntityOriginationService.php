<?php

class ModelEntityOriginationService
{
	private $cache_service = null;
    private $cache_key = '';
	private $data = array();
	
	public function __construct( $cache_service = null, $cache_key = 'global' )
	{
        $this->cache_key = $cache_key;
		$this->setCacheService($cache_service);
		$this->data = $this->getCacheService()->get($this->getCacheName(), $this->cache_key);
	}

	public function __destruct()
	{
		$this->persistCache();
	}
	
 	public function invalidateCache()
 	{
 		$this->data = array();
 	}
	
	public function setCacheService( $service )
	{
		$this->cache_service = $service;
	}

	public function setCacheKey( $key ) {
        $this->cache_key = $key;
    }
	
	public function getCacheService() {
		return $this->cache_service;
	}
	
	public function getCacheCategory( $object ) {
		return $this->cache_key;
	}
	
	public function getSelfOrigin( $object )
	{
		if ( !$object instanceof Metaobject ) return '';
		
		$class_name = get_class($object);

		if ( isset($this->data['self'][$class_name]) ) return $this->data['self'][$class_name];
		
		return $this->data['self'][$class_name] = $this->buildSelfOrigin($object);
	}

	public function getAvailableOrigins( $object )
	{
		if ( !$object instanceof Metaobject ) return array();
		
		$class_name = get_class($object);
		
		if ( isset($this->data['available'][$class_name]) ) return $this->data['available'][$class_name];
		
		$self = $this->getSelfOrigin($object);
		
		if ( $self == '' || $self == DUMMY_PROJECT_VPD ) return array();

		return $this->data['available'][$class_name] = 
			array_merge (
					$this->buildAvailableOrigins($object),
					array (
							$self
					)
			);
	}		
	
	protected function buildSelfOrigin( $object )
	{
		switch( get_class($object) )
		{
		    case 'AttributeGroup':
		    case 'ModuleCategory':
		    case 'ChangeLogEntitySet':
		    	return DUMMY_PROJECT_VPD;

		    case 'DateMonth':
		    case 'DateWeekday':
		    case 'DateYear':
            case 'Module':
            case 'Report':
            case 'PMReport':
		    	return '';
		}
		
		switch( $object->getEntityRefName() )
		{
			// disable VPD for following classes
			case 'Email':
			case 'EmailQueue':
			case 'EmailQueueAddress':
			case 'ObjectEmailNotification':
			case 'ObjectEmailNotificationLink':
			case 'pm_Project':
			case 'pm_ProjectUse':
			case 'pm_ProjectCreation':
			case 'Priority':
            case 'pm_Severity':
			case 'cms_Language':
			case 'cms_User':
			case 'cms_UserSettings':
			case 'cms_Update':
			case 'cms_Backup':
			case 'pm_ChangeRequestLinkType':
			case 'cms_UserLock':
			case 'pm_ReleaseMetrics':
			case 'pm_VersionBurndown':
			case 'cms_License':
			case 'pm_ProjectTag':
			case 'cms_SystemSettings':
			case 'pm_Invitation':
			case 'pm_DownloadAction':
			case 'pm_DownloadActor':
			case 'cms_BlackList':
			case 'cms_LoginRetry':
			case 'cms_CheckQuestion':
			case 'co_UserRole':
			case 'pm_ProjectTemplate':
            case 'pm_ProjectSettings':
			case 'cms_BatchJob':
			case 'cms_EmailNotification':
			case 'cms_NotificationSubscription':
			case 'co_RemoteMailbox':
			case 'co_Company':
			case 'co_ScheduledJob':
			case 'co_JobRun':
			case 'pm_Importance':
			case 'co_CustomReport':
			case 'co_MailboxProvider':
			case 'co_MailTransport':
			case 'cms_ExternalUser':
			case 'ObjectChangeLogAttribute':
            case 'ObjectChangeNotification':
            case 'pm_AttributeValue':
				return '';
				
			default:
				return DUMMY_PROJECT_VPD;
		}
	}
	
	public function buildAvailableOrigins( $object )
	{
		return array();
	}

 	private function persistCache()
 	{
 		$this->getCacheService()->set($this->getCacheName(), $this->data, $this->cache_key);
 	}
 	
 	private function getCacheName()
 	{
 		return 'entity-origination-'.get_class($this);
 	}
}