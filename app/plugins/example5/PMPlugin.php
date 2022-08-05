<?php

class example5PM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array();
	}

    function getModules()
    {
        $modules = array(
            'list' =>
                array(
                    'includes' => array( 'example5/views/ExampleEntityPage.php' ),
                    'classname' => 'ExampleEntityPage',
                    'title' => text('example52'),
                    'AccessEntityReferenceName' => 'pm_ExampleEntity',
                    'AccessType' => 'read',
                    'area' => FunctionalAreaMenuSettingsBuilder::AREA_UID
                )
        );
        return $modules;
    }
}