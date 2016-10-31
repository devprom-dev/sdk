<?php

class PMCustomAttributeIterator extends OrderedIterator
{
 	function toReferenceNames()
 	{
 		$names = array();
 		while ( !$this->end() )
 		{
 			array_push( $names, $this->get('ReferenceName')); 
 			$this->moveNext();
 		}
 		return $names;
 	}
 	
 	function toDictionary()
 	{
 		$lov = array();
 		
 		$lines = preg_split('/\n\r?/', $this->get('ValueRange'));

 		foreach( $lines as $line )
 		{
 			if ( trim($line) == '' ) continue;
 			
 			$parts = preg_split('/:/', $line );
 			
 			$lov[trim($parts[0], ' '.chr(10))] = trim($parts[1]);
 		}
 		
 		return $lov;
 	}
 	
 	function getEntityDisplayName()
 	{
 		return $this->object->getEntityDisplayName($this->get('EntityReferenceName'), $this->get('ObjectKind'));
 	}

	function getEntityRegistry()
	{
		$class_name = getFactory()->getClass($this->get('EntityReferenceName'));
		if ( !class_exists($class_name, false) ) return null;

		$ref = getFactory()->getObject($class_name);
		$registry = $ref->getRegistry();

		$filters = $registry->getFilters();
		$filters[] = new FilterVpdPredicate();

		if ( $this->get('ObjectKind') != '' ) {
			switch($ref->getEntityRefName()) {
				case 'pm_ChangeRequest':
					$filters[] = new FilterAttributePredicate('Type', $this->get('ObjectKind'));
					break;
				case 'pm_Task':
					$filters[] = new FilterAttributePredicate('TaskType', $this->get('ObjectKind'));
					break;
				case 'WikiPage':
					$filters[] = new FilterAttributePredicate('PageType', $this->get('ObjectKind'));
					break;
			}
		}

		$registry->setFilters($filters);
		return $registry;
	}
}
