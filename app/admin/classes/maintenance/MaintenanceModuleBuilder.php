<?php

class MaintenanceModuleBuilder extends ModuleBuilder
{
    public function build( ModuleRegistry & $object )
    {
    	$object->addModule( 
				array (
						'cms_PluginModuleId' => 'update-upload',
						'Caption' => text(1254),
						'AccessEntityReferenceName' => 'cms_Update',
						'Url' => 'updates.php'
				)
		);
    }
}