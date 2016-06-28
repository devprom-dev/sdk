<?php

class ModulePluginsBuilder extends ModuleBuilder
{
    var $site;
    
    public function __construct( $site = 'co' )
    {
        $this->site = $site;
    }
    
    public function build( ModuleRegistry & $object )
    {
        global $plugins;
        
        if ( !is_object($plugins) ) return;
        
        $modules = $plugins->getModules( $this->site );
        
        foreach( $modules as $key => $module )
        {
 	        if ( $module['type'] == 'system' ) continue;
 	        
 	        $item = array();
 	        
 	        $item['cms_PluginModuleId'] = $key;
 	        $item['Caption'] = $module['title'];
 	        $item['Description'] = $module['description'];
 	        $item['AccessEntityReferenceName'] = $module['AccessEntityReferenceName'];
 	        $item['AccessType'] = $module['AccessType'];
 	        $item['Section'] = $module['section'];
 	        $item['Url'] = $module['url'];
 	        $item['Area'] = $module['area'];
            $item['Icon'] = $module['icon'];
 	        
            $object->addModule( $item );
 	    }
    }
}