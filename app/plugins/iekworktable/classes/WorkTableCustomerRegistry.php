<?php

class WorkTableCustomerRegistry extends ObjectRegistrySQL
{
	function getAll()
	{
		$program_it = WorkTableProject::getProgramIt();
		
		$vpds = array_merge( array($program_it->get('VPD')), $program_it->getRef('LinkedProject')->fieldToArray('VPD') );
		
	 	$attribute_it = getFactory()->getObject('pm_CustomAttribute')->getRegistry()->Query(
	 		array (
	 				new FilterVpdPredicate($vpds),
	 				new FilterAttributePredicate('ReferenceName', CUSTOMER_ATTRIBUTE_NAME)
	 		)
	 	);
		
	 	if ( $attribute_it->getId() < 1 ) return $this->createIterator(array());
	 	
	 	$value_it = getFactory()->getObject('pm_AttributeValue')->getRegistry()->Query(
	 		array (
	 				new FilterVpdPredicate($vpds),
	 				new FilterAttributePredicate('CustomAttribute', $attribute_it->idsToArray())
	 		)
	 	);

	 	$data = array();
	 	
		while( !$value_it->end() )
		{
			$value = $value_it->get('StringValue') != '' 
				? $value_it->getHtmlDecoded('StringValue') : $value_it->getHtmlDecoded('TextValue');
			
			$data[] = array (
					'entityId' => $value,
					'Caption' => $value
			);
			
			$value_it->moveNext();
		}
		
		return $this->createIterator( $data );
	}
}