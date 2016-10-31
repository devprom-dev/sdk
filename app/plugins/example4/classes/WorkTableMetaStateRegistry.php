<?php

class WorkTableMetaStateRegistry extends ObjectRegistrySQL
{
	function getAll()
	{
		$program_it = WorkTableProject::getProgramIt();
		
		$vpds = array_merge( array($program_it->get('VPD')), $program_it->getRef('LinkedProject')->fieldToArray('VPD') );
		
		$state_it = getFactory()->getObject('pm_State')->getRegistry()->Query(array (
				new FilterVpdPredicate( $vpds ),
				new FilterAttributePredicate( 'ObjectClass', 'request' )
		));
		
		$terminal = array();
		
		$nonterminal = array();
		
		while( !$state_it->end() )
		{
			$state_it->get('IsTerminal') == 'Y' 
					? $terminal[] = $state_it->get('ReferenceName') 
					: $nonterminal[] = $state_it->get('ReferenceName');
			
			$state_it->moveNext();
		}
		
		return $this->createIterator( 
				array (
						array (
								'ReferenceName' => join('-', $nonterminal),
								'IsTerminal' => 'N',
								'Caption' => 'В работе'
						),
						array (
								'ReferenceName' => join('-', $terminal),
								'IsTerminal' => 'Y',
								'Caption' => 'Завершено'
						)
				)
		);
	}
}