<?php
include "model/TaskMetadataBuilderExample2.php";

class example2PM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
    {
		return array(
            new TaskMetadataBuilderExample2()
        );
	}
}