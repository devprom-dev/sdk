<?php

class IssueActualAuthorRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		$object = getFactory()->getObject('Request');
		
		$aggregage = new AggregateBase( 'Author', 'Author', 'COUNT' );
		$object->addAggregate( $aggregage );
		$it = $object->getAggregated();

		$data = array();
		$authors = $it->fieldToArray('Author');
		
		$user_it = getFactory()->getObject('User')->getRegistry()->Query(
				array(
						new FilterInPredicate(array_filter($authors, function($value) {
								return is_numeric($value);
						}))
				)
		);
		while( !$user_it->end() )
		{
			$data[] = array ( 
					'cms_UserId' => $user_it->getId(), 
					'Caption' => $user_it->getDisplayName(),
					'Login' => $user_it->getId()
			);
			$user_it->moveNext();
		}

		foreach( array_filter($authors, function($value) {
					return !is_numeric($value);
				 }) as $author_name )
		{
			$data[] = array ( 
					'cms_UserId' => $author_name, 
					'Caption' => $author_name,
					'Login' => $author_name
			);
		}
		return $this->createIterator( $data	);
	}
	
	function Query( $parms )
	{
		$iterator = parent::Query( $parms );
		
		$rowset = $iterator->getRowset();

		foreach( $parms as $parm )
		{
			if ( $parm instanceof FilterInPredicate )
			{
				$id_key = $iterator->getIdAttribute();
				
				$id_value = preg_split('/,/', $parm->getValue());
				
				$rowset = array_filter( $rowset, function(&$row) use($id_key, $id_value)
				{
						return in_array($row[$id_key], $id_value);
				});

			}

			if ( $parm instanceof FilterAttributePredicate )
			{
				$id_key = $parm->getAttribute();
				
				$id_value = preg_split('/,/', $parm->getValue());
				
				$rowset = array_filter( $rowset, function(&$row) use($id_key, $id_value)
				{
						return in_array($row[$id_key], $id_value);
				});

			}
		}
		
		return $this->createIterator(array_values($rowset));
	}
}