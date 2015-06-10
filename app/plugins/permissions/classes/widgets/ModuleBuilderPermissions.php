<?php

include_once SERVER_ROOT_PATH."core/classes/widgets/ModuleBuilder.php";

class ModuleBuilderPermissions extends ModuleBuilder
{
    public function __construct()
    {
    }
    
    public function build( ModuleRegistry & $object )
    {
        $item = array();
        
        $item['cms_PluginModuleId'] = 'dicts-projectrole';
        $item['Caption'] = text('permissions4');
        $item['Description'] = text(1818);
        $item['AccessEntityReferenceName'] = 'pm_ProjectRole';
        $item['Url'] = 'project/dicts/pm_ProjectRole';
        
        $object->addModule( $item );
    }
}