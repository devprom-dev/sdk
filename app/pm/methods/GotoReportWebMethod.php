<?php
 
class GotoReportWebMethod extends WebMethod
{
 	function execute_request() 
 	{
 		if ( $_REQUEST['report'] == '' ) return;
 		
 		$report_it = getFactory()->getObject('PMReport')->getExact($_REQUEST['report']);
 		if ( $report_it->getId() != '' )
 		{
	 		$info = $report_it->buildMenuItem();
	 		echo $info['url'];
 		}
 		
 	 	$module_it = getFactory()->getObject('Module')->getExact($_REQUEST['report']);

 	 	$info = $module_it->buildMenuItem();
 		echo $info['url'];
 	}
}
