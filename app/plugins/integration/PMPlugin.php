<?php
include "classes/widgets/FunctionalAreaMenuIntegrationSettingsBuilder.php";
include "model/ModelIntegrationMetadataBuilder.php";

class integrationPM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
			// model
			new ModelIntegrationMetadataBuilder(),
			// widgets
			new FunctionalAreaMenuIntegrationSettingsBuilder()
		);
	}

	function getModules()
	{
		$modules = array(
			'list' =>
				array(
					'includes' => array( 'integration/views/IntegrationPage.php' ),
					'classname' => 'IntegrationPage',
					'title' => text('integration5'),
					'AccessEntityReferenceName' => 'pm_Integration',
					'AccessType' => 'modify',
					'area' => FunctionalAreaMenuSettingsBuilder::AREA_UID
				)
		);
		return $modules;
	}
}