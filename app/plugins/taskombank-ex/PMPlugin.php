<?php
include "model/TaskomBankActivityMetadataBuilder.php";

class taskombankexPM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
		    new TaskomBankActivityMetadataBuilder()
        );
	}
}