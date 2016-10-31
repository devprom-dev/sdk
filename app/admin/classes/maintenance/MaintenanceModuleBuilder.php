<?php

class MaintenanceModuleBuilder extends ModuleBuilder
{
    public function build( ModuleRegistry & $object )
    {
    	$object->addModule( 
				array (
						'cms_PluginModuleId' => 'update-upload',
						'Caption' => text(2177),
						'AccessEntityReferenceName' => 'cms_Update',
						'Url' => 'updates.php'
				)
		);
		$object->addModule(
			array (
				'cms_PluginModuleId' => 'file-upload',
				'Caption' => text(2176),
				'AccessEntityReferenceName' => 'cms_Update',
				'Url' => 'updates.php'
			)
		);
    }
}