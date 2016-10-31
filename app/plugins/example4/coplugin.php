<?php

include "classes/WorkTableProject.php";
include "classes/WorkTableState.php";
include "classes/WorkTableMetaState.php";
include "classes/WorkTableDepartment.php";
include "classes/WorkTableCustomer.php";
include "classes/RequestWorkTableMetadataBuilder.php";

class example4coplugin extends PluginCOBase
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
            // URL http://server/co/module/example4/main
            'main' => array(
                'includes' => array( 'example4/views/WorkTablePage.php' ),
                'classname' => 'WorkTablePage'
            )
        );
    }
}