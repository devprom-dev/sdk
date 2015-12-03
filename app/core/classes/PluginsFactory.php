<?php

include 'PluginBase.php';

if ( file_exists(SERVER_ROOT_PATH."plugins/plugins.php") ) include SERVER_ROOT_PATH."plugins/plugins.php";

class PluginsFactory
{
 	var $namespaces, $plugins, $resources;
 	
 	private $plugins_by_sections = array();
 	
 	function __construct()
 	{
 		global $plugins;
 		
 		$this->plugins = array();
 		$this->namespaces = array();
 		$this->resources = array();
 		
 		$plugins = $this;
 		
 		$this->buildPlugins();
 	}
 	
 	function buildPlugins()
 	{
 		$classes = array_filter( get_declared_classes(), function($value) 
 		{
 			return is_subclass_of($value, 'PluginBase');
 		});

 		foreach( $classes as $class_name )
 		{
 			$this->registerPlugin(new $class_name);
 		}
 			
		usort( $this->namespaces, "plugins_factory_index_sort" );

		$plugins = array();

		foreach( $this->namespaces as $plugin )
		{
		    $plugins[$plugin->getNamespace()] = $this->plugins[$plugin->getNamespace()];
		}
		
		$this->plugins = $plugins;

		$this->loadMethods();
 	}
 	
 	function registerPlugin( $plugin )
 	{
 		array_push($this->namespaces, $plugin);

		$this->plugins[$plugin->getNamespace()] = array();

		$sectionplugins = $plugin->getSectionPlugins();
		
		foreach ( $sectionplugins as $section )
		{
			$section->setNamespace( $plugin );
			
			array_push($this->plugins[$plugin->getNamespace()], $section);
		}
	
		// register resource files
		$namespace = strtolower($plugin->getNamespace());
		
		$lang_file = SERVER_ROOT_PATH.'plugins/'.$namespace.
			'/language/%lang%/resource.php';
			
		$this->resources[$namespace] = $lang_file; 
 	}

	function initializeResources( $language )
	{
		global $plugin_text_array;
		
		$text_array = array();
		
		$this->resources = array_unique($this->resources);

		$lang_name = strtolower($language);
		
		foreach( $this->resources as $namespace => $resource )
		{
			$lang_file = str_replace('%lang%', $lang_name, $resource);
			
			if ( !file_exists( $lang_file ) )
			{
				continue;
			}
			
			include($lang_file);

			foreach( $plugin_text_array as $key => $text )
			{
				if ( is_numeric($key) )
				{
					$text_array[$namespace.$key] = $text;
				}
				else
				{
					$text_array[$key] = $text;
				}
			}
		}
		
		return $text_array;
	}
	
	function getNamespaces()
	{
		return $this->namespaces; 	
	}
	
	function getPluginByFileName( $filename )
	{
 		foreach ( $this->namespaces as $namespace )
 		{
 			if ( strtolower($namespace->getFileName()) == strtolower($filename) )
 			{
 				return $namespace;
 			}
 		}
	}
	
	function getPluginsForSection( $section )
	{
		if ( isset($this->plugins_by_sections[$section]) ) return $this->plugins_by_sections[$section];
		
		$plugins = array();
		
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
				if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) ) 	
				{
	 		    	if ( !$plugin->checkEnabled() ) continue;
					
					array_push( $plugins, $plugin );
	 			}
	 		}
 		}
 		
 		return $this->plugins_by_sections[$section] = $plugins;
	}
	
 	function hasIncluded( $name )
 	{
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( strtolower(get_class($plugin)) == strtolower($name) )
	 			{
		 			return true;
	 			}
	 		}
 		}
 		
 		return false;
 	}
 	
 	function getModule( $namespace, $section, $name )
 	{
 		if ( !is_array($this->plugins[$namespace]) )
 		{
 			return;
 		}
 		
 		foreach ( $this->plugins[$namespace] as $plugin )
 		{
 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
 			{
	 			$modules = $plugin->getModules();
	 			
	 			if ( is_array( $modules[$name] ) )
	 			{
	 				return $modules[$name];
	 			}
 			}
 		}
 		
 		return;
 	}
 	
 	function getModules( $section )
 	{
 		$modules = array();
		
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $key => $plugin )
	 		{
	 			if ( !is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) ) continue;

 				$plugin_modules = $plugin->getModules();
 				
 				if ( !is_array($plugin_modules) ) continue;

 				foreach( $plugin_modules as $key => $module )
 				{
 				    $plugin_modules[$key]['section'] = $section;
 				    $plugin_modules[$key]['namespace'] = $plugin->getNamespace();
 				    $plugin_modules[$key]['url'] = 'module/'.$plugin->getNamespace().'/'.$key;
 				    
 				    $plugin_modules[$plugin->getNamespace().'/'.$key] = $plugin_modules[$key];

 				    unset($plugin_modules[$key]);
 				}
 				
 				$modules = array_merge($modules, $plugin_modules);
	 		}
 		}

 		return $modules;
 	}

 	function useModule( $namespace, $section, $name )
 	{
 		$module = $this->getModule( $namespace, $section, $name );
 		
		if ( is_array($module) )
		{
		 	foreach ( $module['includes'] as $include )
		 	{
		 		include ( SERVER_ROOT_PATH.'plugins/'.$include );
		 	}
		}
		
		return $module;
 	}
 	
 	function getCommand( $namespace, $section, $name )
 	{
 		if ( !is_array($this->plugins[$namespace]) )
 		{
 			return;
 		}
 		
 		foreach ( $this->plugins[$namespace] as $plugin )
 		{
 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
 			{
	 			$command = $plugin->getCommand( $name );
	 			
			 	foreach ( $command['includes'] as $include )
			 	{
			 		include ( SERVER_ROOT_PATH.'plugins/'.$include );
			 	}
			 	
			 	$command = new $name;
			 	
			 	return $command;
 			}
 		}
 	}

 	function getQuickActions( $section )
 	{
 		$actions = array();
 		
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
		 			$actions = array_merge($actions, $plugin->getQuickActions() );
	 			}
	 		}
 		}
 		
 		return $actions;
 	}
 	
 	function getProfileActions( $section, $user_it )
 	{
 		$actions = array();
 		
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
		 			$actions = array_merge($actions, $plugin->getProfileActions( $user_it ) );
	 			}
	 		}
 		}
 		
 		return $actions;
 	}
 	
 	function buildMenuItems( $section, $owner, & $items, $parms = null )
 	{
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
		 			$plugin->buildMenuItems( $owner, $items, $parms );
	 			}
	 		}
 		}
 	}

 	function getHeaderTabs( $section )
 	{
 		global $project_it;
 		
 		$tabs = array();
 		
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			switch ( $section )
	 			{
	 				case 'pm':
	 					$base_url = '/pm/'.$project_it->get('CodeName').'/module/'.$plugin->getNamespace().'/';
	 					break;
	 					
	 				default:
	 					$base_url = '/'.$section.'/module/'.$plugin->getNamespace().'/';
	 			}
		 					
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
		 			$plugin_tabs = $plugin->getHeaderTabs();

		 			foreach ( $plugin_tabs as $tab )
		 			{
		 				$tab['url'] = $base_url.$tab['module'].$tab['url'];
		 				$tab['uid'] = $tab['module'];
		 				$skip = false;
		 				
		 				if ( is_array($tab['items']) )
		 				{
			 				foreach ( array_keys($tab['items']) as $key )
			 				{
			 					if ( $tab['items'][$key]['url'] != '' ) continue;
			 					$tab['items'][$key]['url'] = $base_url.$tab['items'][$key]['module'];
			 				}
		 				}

						if ( !$skip )
						{
		 					array_push( $tabs, $tab );
						}
		 			}
	 			}
	 		}
 		}
 		
 		return $tabs;
 	}

 	function getHeaderMenus( $section )
 	{
 		$menus = array();

 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
	 				if ( count($menus) < 1 )
	 				{
		 				$menus = $plugin->getHeaderMenus();
	 				}
	 				else
	 				{
		 				$menus = array_merge($menus, $plugin->getHeaderMenus() );
	 				}
	 			}
	 		}
 		}
 		
 		return $menus;
 	}
 	
 	function getClasses()
 	{
 		$classes = array();
 		
 		foreach ( $this->namespaces as $plugin )
 		{
 			$classes_dir = SERVER_ROOT_PATH.'plugins/'.
 				$plugin->getNamespace().'/classes';
 				
			$info = $plugin->getClasses();
			
			foreach ( $info as $key => $class )
			{
				$info[$key][1] = $classes_dir.'/'.$info[$key][1];
				$info[$key][3] = 'plugins';
			}
			
 			$classes = array_merge($classes, $info); 
 		}
 		
 		return $classes;
 	}
 	
 	function getCommonBuilders()
 	{
 		$builders = array();

 	 	foreach ( $this->namespaces as $plugin )
 		{
			$builders = array_merge( $builders, $plugin->getBuilders() );
 		}
 		
 		return $builders;
 	}
 	
 	function getSectionBuilders( $section )
 	{
		$builders = array();

		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
	 			    $builders = array_merge( $builders, $plugin->getBuilders() );
	 			}
	 		}
 		}

 		return $builders;
 	}
 	
	function getObjectUrl( $section, $object_it )
	{
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
		 			$url = $plugin->getObjectUrl( $object_it);
		 			
		 			if ( $url != '' )
		 			{
		 				return $url;
		 			}
	 			}
	 		}
 		}
 		
 		foreach ( $this->namespaces as $namespace )
 		{
	 		$url = $namespace->getObjectUrl( $object_it);
	 		
	 		if ( $url != '' )
	 		{
	 			return $url;
	 		}
 		}
 		
 		return '';
	}
	
	function getObjectActions( $section, $object_it )
	{
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			if ( is_subclass_of($plugin, $this->_getPluginClass4Section( $section ) ) )
	 			{
		 			return $plugin->getObjectActions( $object_it);
	 			}
	 		}
 		}

 		return array();
	}

	function getAuthFactories()
	{
		$factories = array();
		
	 	foreach ( $this->namespaces as $namespace )
 		{
	 		$factories = array_merge(
	 			$factories, $namespace->getAuthorizationFactories() );
 		}
 		
 		return $factories;
	}
	
	function getPageInfoSections( $page )
	{
		$sections = array();
		
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			$sections = array_merge($sections, $plugin->getPageInfoSections( $page ));
	 		}
 		}
 		
 		return $sections;
	}

	public function pluginEnabled( $plugin_name )
	{
		return !file_exists(SERVER_ROOT_PATH.'plugins/blocked/'.$plugin_name);		
	}
	
	public function enablePlugin( $plugin_name, $enabled = true )
	{
		if ( $enabled )
		{
			unlink(SERVER_ROOT_PATH.'plugins/blocked/'.$plugin_name);
		}
		else 
		{
			if ( !file_exists(SERVER_ROOT_PATH.'plugins/blocked') ) mkdir(SERVER_ROOT_PATH.'plugins/blocked');
			
			file_put_contents(SERVER_ROOT_PATH.'plugins/blocked/'.$plugin_name, '');
		}
		
		$this->buildPluginsList();
	}
	
	public function buildPluginsList()
	{
		$files = array();
		
		if ( $handle = opendir(SERVER_ROOT_PATH.'plugins') ) 
		{
		    while (false !== ($file = readdir($handle))) 
		    {
		    	$path = SERVER_ROOT_PATH.'plugins/'.$file;
		    	
		    	if ( in_array($file, array(".","..","plugins.php")) || is_dir($path) ) continue;

		    	if ( file_exists(SERVER_ROOT_PATH.'plugins/blocked/'.$file) ) continue;

		    	$files[] = $file;
		    }
		    
	    	closedir($handle);
		}
		
		asort($files);
		
		$plugins = array();

		foreach( $files as $file )
		{
        	$plugins[] = 'include SERVER_ROOT_PATH."plugins/'.$file.'";';
		}
		
		file_put_contents(SERVER_ROOT_PATH.'plugins/plugins.php', '<?php '.PHP_EOL.join(PHP_EOL,$plugins));
		
	    // reset opcache after list of plugins have been changed
	    if ( function_exists('opcache_reset') ) opcache_reset();
	}
	
 	protected function loadMethods()
 	{
 		global $_REQUEST;
 		
 		$namespaces = array();
 		
 		foreach ( $this->plugins as $namespace )
 		{
	 		foreach ( $namespace as $plugin )
	 		{
	 			$namespaces[$plugin->getNamespace()] = 
	 				SERVER_ROOT_PATH.'plugins/'.$plugin->getNamespace().'/methods';
	 		}
 		}
 		
 		foreach ( $namespaces as $methods_dir )
 		{
			if ( is_dir($methods_dir) && $handle = opendir($methods_dir) ) 
			{
			    while (false !== ($file = readdir($handle))) 
			    {
			        if ( $file != "." && $file != ".." && !is_dir($methods_dir.'/'.$file) ) 
			        {
			            include_once ($methods_dir.'/'.$file);
			        }
			    }
			    
		    	closedir($handle);
			}
 		}
 		
 		return false;
 	}

 	function _scanForPlugins()
 	{
 		global $plugins;
 		
		if ( $handle = opendir(SERVER_ROOT_PATH.'plugins') ) 
		{
		    while (false !== ($file = readdir($handle))) 
		    {
		    	$path = SERVER_ROOT_PATH.'plugins/'.$file;
		    	
		        if ($file != "." && $file != ".." && $file != "plugins.php" && !is_dir($path) ) 
		        {
	            	include_once $path;
		        }
		    }
		    
	    	closedir($handle);
		}
 	}
 	
 	function _getPluginClass4Section ( $section )
 	{
 		switch ( $section )
 		{
 			case 'co':
 				return 'PluginCoBase';
 				
 			case 'admin':
 				return 'PluginAdminBase';
 				
 			case 'pm':
 				return 'PluginPMBase';

 			case 'api':
 				return 'PluginAPIase';
 		}
 	}
}

function plugins_factory_index_sort( $left, $right )
{
 	return $left->getIndex() > $right->getIndex() ? 1 : -1;
}
