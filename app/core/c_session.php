<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactorySet.php";
include SERVER_ROOT_PATH."core/classes/widgets/ModuleBuilder.php";
include SERVER_ROOT_PATH."core/classes/widgets/ModulePluginsBuilder.php";
include SERVER_ROOT_PATH."core/classes/widgets/BulkActionBuilderCommon.php";
include SERVER_ROOT_PATH."core/classes/ResourceBuilderLanguageFiles.php";
include SERVER_ROOT_PATH."core/classes/ResourceBuilderPluginsLanguageFiles.php";
include SERVER_ROOT_PATH."core/classes/model/events/CacheResetTrigger.php";
include SERVER_ROOT_PATH."core/classes/model/events/AccessPolicyModelEventsHandler.php";
include SERVER_ROOT_PATH."core/classes/model/events/ChangesWaitLockReleaseTrigger.php";
include SERVER_ROOT_PATH."core/classes/versioning/triggers/SnapshotDeleteCascadeTrigger.php";
include SERVER_ROOT_PATH."core/classes/licenses/LicenseRegistryBuilderCommon.php";
include SERVER_ROOT_PATH."core/classes/project/ProjectMetadataBuilder.php";
include SERVER_ROOT_PATH."core/classes/user/UserMetadataBuilder.php";
include SERVER_ROOT_PATH."core/classes/resources/ContextResourceFileBuilder.php";
include SERVER_ROOT_PATH."core/classes/model/events/UserCreatedEvent.php";
include SERVER_ROOT_PATH."core/classes/system/SystemSettingsMetadataBuilder.php";
include SERVER_ROOT_PATH."core/classes/licenses/LicensePermissionRegistryBuilderCommon.php";

class SessionBase
{
    protected $id = '';
 	protected $user_it;
    protected $factory;
    protected $cache_engine;
    protected $factories;
    protected $active_tab;
    protected $builders;
    protected $language_uid = '';
    protected $language = null;
    protected $auth_factory_it;
    protected $builders_cache = array();
    protected $accessibleVpds = array();
    protected $terminateCallbacks = array();
 	
 	function __construct( $factory = null, $builders = null, $language = null, $cache_service = null )
 	{
 		global $session;
 		
 		$session = $this;
        $this->finalize();

 		$this->builders = array_merge(
 				is_array($builders) ? $builders : array(),
 				array (
 				    new SystemSettingsMetadataBuilder(),
                    new ResourceBuilderLanguageFiles(),
                    new ResourceBuilderPluginsLanguageFiles(),
                    new ProjectMetadataBuilder(),
                    new UserMetadataBuilder(),
                    new LicenseRegistryBuilderCommon(),
                    new LicensePermissionRegistryBuilderCommon()
 				),
				getFactory()->getPluginsManager()->getCommonBuilders()
 		);

		$this->setAuthenticationFactory( $factory );
 		
		$this->cache_engine = is_object($cache_service) ? $cache_service : getFactory()->getCacheService();
 		$this->language = $language;
 		
 		$this->configure();
 	}

 	public function finalize() {
        register_shutdown_function(array($this, 'terminate'));
    }

 	function __sleep() {
        return array (
            'id', 'user_it', 'factory', 'cache_engine', 'factories', 'active_tab',
            'builders', 'auth_factory_it', 'builders_cache', 'accessibleVpds', 'language_uid'
        );
    }

    function __wakeup() {
        global $session;
        $session = $this;
        $this->finalize();
        $this->buildFactories();
    }

    function setId( $id ) {
        $this->id = $id;
    }

    function getId() {
        return $this->id;
    }

    public function addCallbackDelayed( $parms, $callback ) {
 	    $key = md5(serialize($parms));
 	    $this->terminateCallbacks[$key] = function() use ($parms, $callback) {
            call_user_func( $callback, $parms );
        };
    }

    public function terminate()
	{
	    foreach( $this->terminateCallbacks as $callback ) {
	        call_user_func( $callback );
        }
		$this->builders = array();
	}

	protected function buildFactories()
    {
        getFactory()->getEntityOriginationService()->setCacheKey($this->getCacheKey());
        getFactory()->resetCache();

        $notificators = $this->getBuilders( 'ObjectFactoryNotificator' );
        if ( is_array($notificators) ) {
            $manager = getFactory()->getEventsManager();
            foreach( $notificators as $notificator ) {
                $manager->registerNotificator( $notificator );
            }
        }
        $this->getLanguage();
        $_SERVER['ENTRY_URL'] = defined('PERMISSIONS_ENABLED') ? '/pm/my' : '/pm/all';
    }

 	public function configure()
 	{
 		$this->builders = array_merge($this->builders, $this->createBuilders());
		if ( $this->getSite() != '' ) {
			$this->builders = array_merge($this->builders, getFactory()->getPluginsManager()->getSectionBuilders($this->getSite()));
		}

        $this->buildFactories();
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
 	
 	function getLanguageUid()
 	{
        if ( $this->language_uid != '' ) return $this->language_uid;
 		$user_it = $this->getUserIt();
 		if ( is_object($user_it) && $user_it->getId() > 0 ) {
            return $this->language_uid = $user_it->get('Language') == 2 ? 'EN' : 'RU';
 		}
 		else {
    	 	$system = new Metaobject('cms_SystemSettings');
            return $this->language_uid = $system->getAll()->get('Language') == 2 ? 'EN' : 'RU';
 		}
 	}
 	
 	function getLanguage() 
 	{
 		if ( is_object($this->language) ) return $this->language;
    	$this->language = $this->getLanguageUid() == 'EN' ? new LanguageEnglish() : new Language();
        $this->language->Initialize();
		return $this->language;
 	}

 	function resetLanguage()
 	{
 		unset($this->language);
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
 		$factory = $this->getAuthenticationFactory();
 		if ( !is_object($factory) ) {
 		    $auth_factories = new AuthenticationFactorySet($this);
 		    $factory = $auth_factories->getDefaultFactory();
            $this->setAuthenticationFactory($factory);
 		}
		$this->setUserIt($user_it);
        $factory->logon( in_array('remember', array_keys($_REQUEST)) );
 	}

	function setUserIt( $user_it )
	{
		$this->user_it = $user_it;
		$factory = $this->getAuthenticationFactory();
		if ( is_object($factory) ) {
			$factory->setUser( $this->user_it );
		}
		if ( $this->user_it->getId() > 0 ) {
            $project = new Project;
            $this->accessibleVpds = $project->getRegistry()->Query(
                    array (
                        new ProjectAccessiblePredicate($this->user_it)
                    )
                )->fieldToArray('VPD');
        }
	}

 	function getUserIt()
 	{
 	    if ( is_object($this->user_it) ) return $this->user_it;
 	    if ( is_object($this->factory) )
 	    {
			if ( is_object($this->factory->getUser()) ) {
			    $this->setUserIt($this->factory->getUser());
				return $this->user_it;
			}
			else if ( $this->factory->ready() ) {
				$this->user_it = $this->factory->authorize();
				if ( $this->user_it->count() > 0 ) {
                    $this->setUserIt($this->user_it);
                    return $this->user_it;
                }
			}
 	    }

 		$auth_factories = new AuthenticationFactorySet($this);
 		foreach( $auth_factories->getFactories() as $factory )
 		{
 		    if ( !$factory->ready() ) continue;

			$this->factory = $factory;
 			$this->user_it = $factory->authorize();

 			if ( $this->user_it->getId() > 0 ) break;
 		}

		if ( !is_object($this->user_it) || $this->user_it->getId() < 1 )
 		{
			$this->factory = $auth_factories->getDefaultFactory();
			if ( $this->factory->ready() ) {
				$this->user_it = $this->factory->authorize();
			} else {
				$factory = new AuthenticationFactory();
				$this->user_it = $factory->authorize();
			}
 		}

		if ( $this->user_it->get('Blocks') > 0 ) {
     		// check blocked user unable to access the system
 			$this->user_it->setRowset( array() );
 		}
        $this->setUserIt($this->user_it);
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
 		$project = new Project;
 	    return $project->createCachedIterator( array() );
 	}

 	function getAccessibleVpds() {
 	    return $this->accessibleVpds;
    }

 	function close()
 	{
 		$factory = $this->getAuthenticationFactory();

 		if ( !is_object($factory) ) return;

 		$factory->setUser($this->getUserIt());
 		$factory->logoff();
 	}
 	
 	function getCacheKey()
 	{
 		return 'global-'.$this->getLanguageUid();	
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
 		return $this->cache_engine->invalidate( $category );
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
            new ModulePluginsBuilder($this->getSite()),

            // triggers
            new AccessPolicyModelEventsHandler(),
            new CacheResetTrigger(),
            new SnapshotDeleteCascadeTrigger(),
            new ChangesWaitLockReleaseTrigger(),
            new UserCreatedEvent(),

            new ContextResourceFileBuilder($this),
            new BulkActionBuilderCommon()
        );
 	}
 	
 	function getBuilders( $interface_name = '' )
 	{
 	    if ( $interface_name == '' ) return $this->builders;
 	    if ( count($this->builders_cache[$interface_name]) > 0 ) return $this->builders_cache[$interface_name];
 	    
 	    $this->builders_cache[$interface_name] = array();
 	    
 	    foreach( $this->builders as $builder ) {
            if ( is_a($builder, $interface_name) ) {
				$this->builders_cache[$interface_name][get_class($builder)] = $builder;
			}
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
 		return 'co';
 	}
}
  
function getSession()
{
 	global $session;
 	return $session;
}