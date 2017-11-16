<?php

include_once SERVER_ROOT_PATH."pm/classes/report/ReportsBuilder.php";

class ReportsFileServerBuilder extends ReportsBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
	public function build( ReportRegistry & $object )
    {
    	if( $this->session->getProjectIt()->getMethodologyIt()->get('IsFileServer') != 'Y' ) return;
    	
 		$module = getFactory()->getObject('Module');
 		
		$module_it = $module->getExact('fileserver/files');
		
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
    		$object->addReport(
    			array ( 'name' => 'fileserverfiles',
    					'category' => ModuleCategoryBuilderFileServer::AREA_UID,
    					'description' => $module_it->get('Description'),
    			        'module' => $module_it->getId() )
    		);
		}
    }
}