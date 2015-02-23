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
		
		$user_it = getFactory()->getObject('IssueAuthor')->getRegistry()->Query(
				array(
						new FilterInPredicate($authors)
				)
		);
		while( !$user_it->end() )
		{
			$data[] = array ( 
					'cms_UserId' => $user_it->getHtmlDecoded('cms_UserId'), 
					'Caption' => $user_it->getHtmlDecoded('Caption'),
					'Login' => $user_it->getId()
			);
			$user_it->moveNext();
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