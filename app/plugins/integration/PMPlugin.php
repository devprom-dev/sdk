<?php
include "model/ModelIntegrationMetadataBuilder.php";
include "model/RequestIntegration.php";
include "model/TaskIntegration.php";
include "classes/widgets/FunctionalAreaMenuIntegrationSettingsBuilder.php";
include "classes/SharedObjectsIntegrationBuilder.php";
include "classes/widgets/IntegrationObjectsListWidgetBuilder.php";
include "classes/SearchableObjectsIntegrationBuilder.php";

class integrationPM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
			// model
			new ModelIntegrationMetadataBuilder(),
            new SharedObjectsIntegrationBuilder(),
			new SearchableObjectsIntegrationBuilder(),
			// widgets
			new FunctionalAreaMenuIntegrationSettingsBuilder(),
            new IntegrationObjectsListWidgetBuilder()
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
					'AccessType' => 'read',
					'area' => FunctionalAreaMenuSettingsBuilder::AREA_UID
				)
		);
		return $modules;
	}
}