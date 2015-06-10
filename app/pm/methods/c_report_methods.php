<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewReportTypeWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
		return translate('Тип отчета');
 	}
 	
 	function getValues()
 	{
  		return array (
 			'all' => $this->getCaption().': '.translate('все'),
 			'user' => translate('Пользовательские'),
 			'system' => translate('Системные')
 			);
	}
	
	function getStyle()
	{
		return 'width:155px;';
	}
	
	function getValueParm()
	{
		return 'type';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ReportModifyWebMethod extends WebMethod
 {
 	var $report_it, $redirect_url;
 	
 	function ReportModifyWebMethod( $report_it = null )
 	{
 		$this->report_it = $report_it;
 		
 		$this->redirect_url = "donothing";
 		
 		parent::WebMethod();
 	}
 	
 	function getCaption() 
	{
		return translate('Сохранить');
	}
	
	function getJSCall( $values, $redirect_url = '' )
	{
	    $this->redirect_url = $redirect_url;
	    
		return parent::getJSCall( 
			array( 'report' => $this->report_it->getId(),
				   'items' => join(array_keys($values), ','),
				   'values' => join(array_values($values), ';') )
			);
	}

	function getRedirectUrl()
	{
		return $this->redirect_url;
	}

 	function execute_request()
 	{
 		global $_REQUEST;
	 	
	 	if ( $_REQUEST['report'] != '' )
	 	{
	 		$this->execute($_REQUEST['report'], $_REQUEST['items'], $_REQUEST['values']);
	 	}
 	}
 	
 	function execute( $report_id, $items, $values )
 	{
 		$report = getFactory()->getObject('pm_CustomReport');
 		$report_it = $report->getExact( $report_id );

 		if ( !getFactory()->getAccessPolicy()->can_modify($report_it) ) return;
 		
		$parms = '';
		$items = preg_split('/,/', $items);
		$values = preg_split('/;/', $values);

		foreach ( $items as $key => $parm )
		{
			if ( $values[$key] != '' )
			{
				$parms .= $parm.'='.$values[$key].'&';
			}
		}

		$report->modify_parms($report_it->getId(), array ( 'Url' => trim($parms,'&') ));
	}
 	
 	function hasAccess()
 	{
 		return getFactory()->getAccessPolicy()->can_modify($this->report_it);
 	}
 } 
 
?>