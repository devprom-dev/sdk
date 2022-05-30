<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewLogSubjectWebMethod extends FilterWebMethod
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
 			
 		$it = getFactory()->getObject('WorkerUser')->getAll();
 		while ( !$it->end() )
 		{
 			$values[$it->getId()] = $it->getDisplayName();
 			$it->moveNext();
 		}

        $values['external'] = text(2331);
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
