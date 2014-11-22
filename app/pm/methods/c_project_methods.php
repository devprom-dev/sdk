<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewProjectLogWebMethod extends FilterWebMethod
 {
 }
  
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewLogSubjectWebMethod extends ViewProjectLogWebMethod
 {
 	function getCaption()
 	{
 		return translate('Участник');
 	}

 	function getValues()
 	{
 		global $project_it;
 		
  		$values = array (
 			'all' => translate('Все')
 			);
 			
 		$it = $project_it->getParticipantIt();
 		
 		while ( !$it->end() )
 		{
 			$values[$it->getId()] = $it->getDisplayName();
 			$it->moveNext();
 		}
 		
 		$values['notme'] = text(1077);
 		
 		return $values;
	}
	
	function getStyle()
	{
		return 'width:125px;';
	}

	function getValueParm()
	{
		return 'participant';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewTerminologyWebMethod extends ViewProjectLogWebMethod
 {
 	function getCaption()
 	{
 		return translate('Значения');
 	}

 	function getValues()
 	{
  		return array (
 			'all' => translate('Все'),
 			'yes' => translate('Переопределенные пользователем'),
 			'no' => translate('Системные значения')
 			);
	}
	
	function getStyle()
	{
		return 'width:245px;';
	}
	
	function getValueParm()
	{
		return 'overriden';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class DeleteCustomTerminologyWebMethod extends WebMethod
 {
 	function execute_request()
 	{
 		global $_REQUEST;
	 	$this->execute($_REQUEST);
 	}

 	function getCaption()
 	{
 		return text(954);
 	}
 	
 	function hasAccess()
 	{
 		$project_roles = getSession()->getRoles();
 		
 		return $project_roles['lead'];
 	}

 	function execute( $parms )
 	{
 		global $model_factory;
 		
 		$resource = $model_factory->getObject('CustomResource');
 		
 		$resource->deleteAll();
 	}
 }
