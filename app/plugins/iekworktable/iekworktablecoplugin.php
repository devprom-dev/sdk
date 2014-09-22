<?php

include "classes/WorkTableProject.php";
include "classes/WorkTableState.php";
include "classes/WorkTableMetaState.php";
include "classes/WorkTableDepartment.php";
include "classes/WorkTableCustomer.php";
include "classes/RequestWorkTableMetadataBuilder.php";

class iekworktablecoplugin extends PluginCOBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array();
	}
	
	// returns modules of the plugin
    function getModules()
    {
        return array(
            'main' =>
                array(
                        'includes' => array( 'iekworktable/views/WorkTablePage.php' ),
                        'classname' => 'WorkTablePage'
                )
        );
    }
}