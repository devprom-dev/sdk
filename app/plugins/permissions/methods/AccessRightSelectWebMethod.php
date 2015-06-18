<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class AccessRightSelectWebMethod extends SelectWebMethod
{
 	var $access_it;
 	
 	function execute_request()
 	{
 		global $_REQUEST;
 		
	 	if ( $_REQUEST['role'] != '' && $_REQUEST['object'] != '' && $_REQUEST['kind'] != '' ) 
	 	{
	 		$this->execute($_REQUEST['role'], $_REQUEST['object'], $_REQUEST['kind'], $_REQUEST['value']);
	 	}
 	}
 	
 	function drawSelect( $access_it, $access_type )
 	{
 		$this->access_it = $access_it;
 		
 		parent::drawSelect( 
			array( 
				'role' => $access_it->get('ProjectRole'),
				'object' => $access_it->get('ReferenceName'),
				'kind' => $access_it->get('ReferenceType')
				), 
			$access_type
		);
 	}
}

