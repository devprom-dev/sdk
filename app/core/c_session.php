<?php

include SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactorySet.php";
include SERVER_ROOT_PATH."core/classes/widgets/ModuleBuilder.php";
include SERVER_ROOT_PATH."core/classes/widgets/ModulePluginsBuilder.php";
include SERVER_ROOT_PATH."core/classes/ResourceBuilderLanguageFiles.php";
include SERVER_ROOT_PATH."core/classes/ResourceBuilderPluginsLanguageFiles.php";
include SERVER_ROOT_PATH."core/classes/model/events/CacheResetTrigger.php";
include SERVER_ROOT_PATH."core/classes/model/events/AccessPolicyModelEventsHandler.php";
include SERVER_ROOT_PATH."core/classes/model/events/ChangesWaitLockReleaseTrigger.php";
include SERVER_ROOT_PATH."core/classes/versioning/triggers/SnapshotDeleteCascadeTrigger.php";
include SERVER_ROOT_PATH."core/classes/licenses/LicenseRegistryBuilderCommon.php";
include SERVER_ROOT_PATH."core/classes/project/PortfolioCommonBuilder.php";
include SERVER_ROOT_PATH."core/classes/project/ProjectMetadataBuilder.php";
include SERVER_ROOT_PATH."core/classes/resources/ContextResourceFileBuilder.php";

class SessionBase
{
 	var $user_it, $factory, $cache_engine, $factories;
 	var $active_tab;
 	var $builders;
 	var $language;
 	var $auth_factory_it;
 	
 	private $builders_cache = array();
 	
 	function SessionBase( $factory = null, $builders = null, $language = null, $cache_service = null )
 	{
 		global $session;
 		
 		$session = $this;

 		$this->builders = array_merge(
 				is_array($builders) ? $builders : array(),
 				array (
		 	            new ObjectMetadataModelBuilder(),
		 	            new ResourceBuilderLanguageFiles(),
		 	            new ResourceBuilderPluginsLanguageFiles(),
 						new ProjectMetadataBuilder()
 				)
 		);
 		
		$this->setAuthenticationFactory( $factory );
 		
		$this->cache_engine = is_object($cache_service) ? $cache_service : getFactory()->getCacheService();
 		
 		$this->language = $language;
 		
 		$this->configure();
 	}
 	
 	public function configure()
 	{
 		$this->builders = array_merge($this->builders, $this->createBuilders());
 		
		$plugins = $this->getPluginsManager();

		if ( is_object($plugins) )
		{
			$this->builders = array_merge($this->builders, $plugins->getCommonBuilders());
			
			if ( $this->getSite() != '' )
			{
				$this->builders = array_merge($this->builders, $plugins->getSectionBuilders($this->getSite()));
			}
		}

 		$notificators = $this->getBuilders( 'ObjectFactoryNotificator' );
 		
 		if ( is_array($notificators) )
 		{
 		    foreach( $notificators as $notificator )
     		{
     		    getFactory()->getEventsManager()->registerNotificator( $notificator );
     		}
 		}
 		
 		$this->builders_cache = array();
 	}
 	
 	function getCacheEngine()
 	{
 		return $this->cache_engine;
 	}
 	
 	function setCacheEngine( $service )
 	{
 		$this->cache_engine = $service; 
 	}
 	
 	function getPluginsManager()
 	{
 	    global $plugins;
 	    return $plugins;
 	}
 	
 	function getLanguageUid()
 	{
 	    global $model_factory;
 	    
 		$user_it = $this->getUserIt();
 		
 		if ( is_object($user_it) && $user_it->getId() > 0 )
 		{
            return $user_it->get('Language') == 2 ? 'EN' : 'RU'; 		    
 		}
 		else
 		{
    	 	$system = new Metaobject('cms_SystemSettings');
    	 	
    		$system_it = $system->getAll();
    		
    		return $system_it->get('Language') == 2 ? 'EN' : 'RU';
 		}
 	}
 	
 	function getLanguage() 
 	{
 		if ( is_object($this->language) ) return $this->language;
 		
    	$this->language = $this->getLanguageUid() == 'EN' ? new LanguageEnglish() : new Language();
         
        $this->language->Initialize();
		
		return $this->language; 
 	}
 	
 	function getAuthenticationFactory()
 	{
 	    return $this->factory;
 	}

 	function setAuthenticationFactory( $factory )
 	{
 		$this->factory = $factory;
 		
 		unset($this->user_it);
 	}
 	
 	/**
 	 * Opens the session and makes it available for use
 	 */
 	function open( $user_it )
 	{
 		global $model_factory, $_REQUEST;
 		
 		$factory = $this->getAuthenticationFactory();
 		
 		if ( !is_object($factory) )
 		{
 		    $auth_factories = new AuthenticationFactorySet($this);
 		    
 		    $factory = $auth_factories->getDefaultFactory();
 		}
 		
 		$factory->setUser( $user_it->getId() );
 		
 		$this->user_it = $user_it;
 		
 		$session_hash = $factory->logon( 
 			in_array('remember', array_keys($_REQUEST)) );
 		
 		// get the recent user's visit
		$stored_session = $model_factory->getObject('pm_ProjectUse');
	    $stored_session->defaultsort = 'RecordModified DESC';

		$prev_logon_it = $stored_session->getByRefArray(
			array( 'Participant' => $user_it->getId(),
				   'SessionHash' => $session_hash ), 1 );

		// store the user has accessed into the system
		// if there was access in the past just modify it
		$parms = array(
				'Timezone' => EnvironmentSettings::getClientTimeZone()->getName()
		);
		
		if ( $prev_logon_it->count() > 0 ) 
		{
			$parms['PrevLoginDate'] = $prev_logon_it->get('RecordModified');
			 
			$stored_session->getRegistry()->Store($prev_logon_it, $parms);
		}
		else 
		{
		 	// store new access record
			$parms['Participant'] = $user_it->getId();
			$parms['SessionHash'] = $session_hash;
			
		 	$stored_session->add_parms($parms);
		} 	
 	}

 	function getUserIt()
 	{
 	    if ( is_object($this->user_it) ) return $this->user_it;

 	    if ( is_object($this->factory) )
 	    {
 	        $this->user_it = $this->factory->authorize();
 	        
 	        if ( $this->user_it->getId() > 0 ) return $this->user_it;
 	    }
 	    
 		$auth_factories = new AuthenticationFactorySet($this);

 		foreach( $auth_factories->getFactories() as $factory )
 		{
 		    if ( !$factory->ready() ) continue;
 		    
 			$this->user_it = $factory->authorize();
 			
 			if ( $this->user_it->getId() > 0 )
 			{
 			    $this->factory = $factory;
 			    
 			    break;
 			}
 		}
 		
 		if ( !is_object($this->user_it) )
 		{
 		    $this->user_it = $auth_factories->getDefaultFactory()->authorize();
 		}
 		else if ( $this->user_it->get('Blocks') > 0 ) 
 		{
     		// check blocked user unable to access the system
 			$this->user_it->setRowset( array() );
 		}
 		
 		return $this->user_it;
 	}
 	
 	function getUserSettings()
 	{
 	    if ( is_object($this->settings) ) return $this->settings;
 	    
 	    $this->settings = new UserSettings;
 	    
 	    return $this->settings;
 	}
 	
 	function getProjectIt()
 	{
 	    global $model_factory;
 	    
 		$project = $model_factory->getObject('pm_Project');
 	    
 	    return $project->createCachedIterator( array() );
 	}

 	function close()
 	{
 		$factory = $this->getAuthenticationFactory();

 		if ( !is_object($factory) ) return;

 		$factory->setUser( $this->getUserIt()->getId() );
 		
 		$factory->logoff();
 	}
 	
 	function getCacheKey()
 	{
 		return 'global';	
 	}
 	
 	function get( $key, $category = '' )
 	{
 		$category = $category == '' ? $this->getCacheKey() : $category;
 		
 		return $this->cache_engine->get( $key, $category );
 	}
 	
 	function set( $key, $value = '', $category = '' )
 	{
 		$category = $category == '' ? $this->getCacheKey() : $category;
 		
 		if ( $value == '' )
 		{
 			$this->cache_engine->reset( $key, $category );
 		}
 		else
 		{
 			$this->cache_engine->set( $key, $value, $category );
 		}
 	}
 	
 	function truncate( $category = '' )
 	{
 		$category = $category == '' ? $this->getCacheKey() : $category;
 		
 		return $this->cache_engine->truncate( $category );
 	}
 	
 	function drop()
 	{
 		return $this->cache_engine->drop();
 	}
 	
 	function getApplicationUrl()
 	{
 	    return '/';
 	}
  	
 	function setActiveTab( $tab )
 	{
 	    $this->active_tab = $tab;
 	}
 	
 	function getActiveTab()
 	{
 	    return $this->active_tab;
 	}

 	function createBuilders()
 	{
 	    return array (
 	    		new LicenseRegistryBuilderCommon(),
                new ModulePluginsBuilder($this->getSite()),
                new PortfolioCommonBuilder(),
 	    		
 	    		// triggers
 	    		new AccessPolicyModelEventsHandler(),
 	    		new CacheResetTrigger(),
 	    		new SnapshotDeleteCascadeTrigger(),
 	    		new ChangesWaitLockReleaseTrigger(),
 	    		
 	    		new ContextResourceFileBuilder($this)
        );
 	}
 	
 	function getBuilders( $interface_name = '' )
 	{
 	    if ( $interface_name == '' ) return $this->builders;
 	    
 	    if ( isset($this->builders_cache[$interface_name]) ) return $this->builders_cache[$interface_name];
 	    
 	    $this->builders_cache[$interface_name] = array();
 	    
 	    foreach( $this->builders as $builder )
 	    {
            if ( is_a($builder, $interface_name) ) $this->builders_cache[$interface_name][get_class($builder)] = $builder;
 	    }
 	    
 	    return $this->builders_cache[$interface_name];
 	}

  	function addBuilder( $builder )
 	{
 	    $this->builders[] = $builder;

		$this->builders_cache = array();
 	 }
 	
 	function insertBuilder( $builder )
 	{
 	    array_unshift($this->builders, $builder);

		$this->builders_cache = array();
 	}
 	
 	function removeBuilder( $builder )
 	{
		$this->builders_cache = array();
 		
 		foreach( $this->builders as $key => $item )
 	    {
 	        if ( get_class($item) == get_class($builder) ) unset($this->builders[$key]);
 	    }
 	}
 	
 	public function getSite()
 	{
 		return '';
 	}
}
  

///////////////////////////////////////////////////////////////////////
function & getSession() 
{
 	global $session;
 	
 	if ( !is_object($session) ) {
 		$session = new SessionBase; 
 	}
 	
 	return $session;
}