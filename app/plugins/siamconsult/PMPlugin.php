<?php
include "classes/TaskSiamconsultMetadataBuilder.php";

class siamconsultPM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
		    new TaskSiamconsultMetadataBuilder(getSession())
        );
	}
}