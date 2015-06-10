<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class AccessClassFilterViewWebMethod extends FilterWebMethod
{
 	function getCaption()
 	{
 		return translate('Сущность');
 	}

 	function getValues()
 	{
 		global $model_factory;
 		
 		$values = array();
 		
 		$access = $model_factory->getObject('pm_ObjectAccess');
 		$class_it = $access->getClassesIt();
 		
 		$values['all'] = translate('Все');
 		
 		while ( !$class_it->end() )
 		{
 			$object = $model_factory->getObject($class_it->get('ObjectClass'));
 			
 			$values[$class_it->get('ObjectClass')] = $object->getDisplayName();
 			$class_it->moveNext();
 		}
 		
  		return $values;
	}
	
	function getStyle()
	{
		return 'width:200px;';
	}
	
	function getValueParm()
	{
		return 'class';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
}