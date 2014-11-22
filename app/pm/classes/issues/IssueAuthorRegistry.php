<?php

class IssueAuthorRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		$object = getFactory()->getObject('Request');
		
		$object->resetPersisters();
		
		$aggregage = new AggregateBase( 'Author', 'Author', 'COUNT' );
		
		$object->addAggregate( $aggregage );
		
		$it = $object->getAggregated();

		$data = array();
		
		$user_it = getFactory()->getObject('User')->getRegistry()->Query(
				array(
						new FilterInPredicate($it->fieldToArray('Author'))
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

		$object = getFactory()->getObject('pm_Watcher');

		$object->resetPersisters();
		
		$aggregage = new AggregateBase( 'Email', 'Email', 'COUNT' );
		
		$object->addFilter( new FilterAttributePredicate('SystemUser', 'none') );
		$object->addFilter( new FilterAttributePredicate('ObjectClass', 'request') );
		
		$object->addAggregate( $aggregage );
		
		$it = $object->getAggregated();

		while( !$it->end() )
		{
			$data[] = array ( 
					'cms_UserId' => $it->get('Email'), 
					'Caption' => $it->get('Email'),
					'Login' => $it->get('Email')
			);
			
			$it->moveNext();
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