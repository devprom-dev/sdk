<?php
include "model/ActivityMetadataBuilderExample.php";

class example2PM extends PluginPMBase
{
	// returns builders which extend application behavior or model 
	public function getBuilders()
	{
		return array(
				new ActivityMetadataBuilderExample()
		);
	}
}