<?php
include "model/ActivityMetadataBuilderEx2Timing.php";

class extimingPM extends PluginPMBase
{
	public function getBuilders()
	{
		return array(
			new ActivityMetadataBuilderEx2Timing()
		);
	}
}