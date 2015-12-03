<?php
include "model/ActivityMetadataBuilderExTiming.php";

class extimingPM extends PluginPMBase
{
	public function getBuilders()
	{
		return array(
			new ActivityMetadataBuilderExTiming()
		);
	}
}