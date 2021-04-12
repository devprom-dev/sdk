<?php
include "model/ActivityMetadataBuilderExTiming.php";

class example3PM extends PluginPMBase
{
	public function getBuilders()
	{
		return array(
			new ActivityMetadataBuilderExTiming()
		);
	}
}