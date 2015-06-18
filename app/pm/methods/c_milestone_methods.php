<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class MilestoneWebMethod extends WebMethod
 {
 	function execute_request()
 	{
 		global $_REQUEST;
	 	if($_REQUEST['Milestone'] != '') {
	 		$this->execute($_REQUEST['Milestone']);
	 	}
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class SetPassedWebMethod extends MilestoneWebMethod
 {
	function getCaption() 
	{
		return translate('Пройдена');
	}
	
	function hasAccess()
	{
		global $part_it;
		return $part_it->getId() != GUEST_UID;
	}
	
 	function execute( $object_id )
 	{
 		global $model_factory;
 		$milestone = $model_factory->getObject('pm_Milestone');
 		
 		$milestone_it = $milestone->getExact( $object_id );
 		if($milestone_it->count() > 0) 
 		{
 			$milestone->modify_parms( $milestone_it->getId(), array('Passed' => 'Y')); 
 		}
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class SetCurrentWebMethod extends MilestoneWebMethod
 {
	function getCaption() {
		return translate('Активна');
	}
	
	function hasAccess()
	{
		global $part_it;
		return $part_it->getId() != GUEST_UID;
	}
	
 	function execute( $object_id )
 	{
 		global $model_factory;
 		$milestone = $model_factory->getObject('pm_Milestone');
 		
 		$milestone_it = $milestone->getExact( $object_id );
 		if($milestone_it->count() > 0) 
 		{
 			$milestone->modify_parms( $milestone_it->getId(), array('Passed' => 'N')); 
 		}
 	}
 }

 //////////////////////////////////////////////////////////////////////////////////////
 class MilestoneFilterStateWebMethod extends FilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('Статус');
 	}
 	
 	function getValues()
 	{
  		return array (
  			'all' => translate('Все'),
 			'N' => translate('Актуальные'),
 			'Y' => translate('Прошедшие')
 			);
	}

	function getStyle()
	{
		return 'width:130px;';
	}

 	function getValueParm()
 	{
 		return 'state';
 	}
 
  	function getType()
 	{
 		return 'singlevalue';
 	}
 	
 	function getValue()
 	{
 		$value = parent::getValue();
 		
 		if ( $value == '' )
 		{
 			return 'N'; 
 		}
 		
 		return $value;
 	}
 }

?>