<?php

class StateMetaRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		$aggregated_state = $this->getObject()->getAggregatedStateObject();
		
		$ref_names = array();
		
		$state_it = $aggregated_state->getAll();
		
		while( !$state_it->end() )
		{
			if ( $state_it->get('IsTerminal') == 'Y' )
			{
				$ref_names['final'][] = $state_it->get('ReferenceName'); 
			}
			else
			{
				if ( !isset($ref_names['initial'][$state_it->get('VPD')]) )
				{
					$ref_names['initial'][$state_it->get('VPD')] = $state_it->get('ReferenceName'); 
				}
				else
				{
					$ref_names['progress'][] = $state_it->get('ReferenceName');
				}
			}
			
			$state_it->moveNext();
		}
		
		if ( count($ref_names['progress']) < 1 ) $ref_names['progress'][] = 'inprogress';

		return $this->createIterator(
				array (
						array ( 
								'pm_StateId' => 1, 
								'ReferenceName' => join(
										$this->getObject()->getStatesDelimiter(),
										array_unique($ref_names['initial'])
								), 
								'Caption' => translate('Добавлено'),
								'IsTerminal' => 'N'
						),
						array ( 
								'pm_StateId' => 2, 
								'ReferenceName' => join(
										$this->getObject()->getStatesDelimiter(),
										array_unique($ref_names['progress'])
								), 
								'Caption' => translate('В работе'),
								'IsTerminal' => 'N'
						),
						array ( 
								'pm_StateId' => 3,
								'ReferenceName' => join(
										$this->getObject()->getStatesDelimiter(),
										array_unique($ref_names['final'])
								),
								'Caption' => translate('Выполнено'),
								'IsTerminal' => 'Y'
						)
				)
		);
	}
}