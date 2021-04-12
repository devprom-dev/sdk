<?php

class WorkTableDepartmentRegistry extends ObjectRegistrySQL
{
	function getAll()
	{
	 	$attribute_it = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
	 		array (
	 				new FilterVpdPredicate(WorkTableProject::getProgramIt()->get('VPD')),
	 				new FilterAttributePredicate('ReferenceName', DEPT_ATTRIBUTE_NAME)
	 		)
	 	);

	 	if ( $attribute_it->getId() < 1 ) return $this->createIterator(array());

	 	$data = array();
	 	
		foreach( $attribute_it->toDictionary() as $key => $value )
		{
			$data[] = array (
					'entityId' => $key,
					'Caption' => $value
			);
		}
		
		return $this->createIterator( $data );
	}
}