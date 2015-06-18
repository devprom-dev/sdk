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
 		return translate('Автор');
 	}

 	function getValues()
 	{
  		$values = array (
 			'all' => translate('Все')
 			);
 			
 		$it = getFactory()->getObject('ProjectUser')->getAll();
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
