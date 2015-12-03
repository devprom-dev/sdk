<?php

class StateMetaRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		$aggregated_state = $this->getObject()->getAggregatedStateObject();
		
		$ref_names = array(
				'final' => array(),
				'initial' => array(),
				'progress' => array()
		);

		if ( !is_array($aggregated_state) ) $aggregated_state = array($aggregated_state);

		foreach( $aggregated_state as $state_object ) {
            $refnames = array_unique($state_object->getAll()->fieldToArray('ReferenceName'));
            if ( count($refnames) > 0 ) {
                $ref_names['initial'][] = array_shift($refnames);
            }
            $state_it = $state_object->getAll();
			while (!$state_it->end()) {
				if ($state_it->get('IsTerminal') == 'Y') {
					$ref_names['final'][] = $state_it->get('ReferenceName');
				}
                elseif ( !in_array($state_it->get('ReferenceName'), $ref_names['initial']) ) {
                    $ref_names['progress'][] = $state_it->get('ReferenceName');
                }
				$state_it->moveNext();
			}
		}
        if ( count($ref_names['initial']) < 1 ) $ref_names['initial'][] = 'submitted';
		if ( count($ref_names['progress']) < 1 ) $ref_names['progress'][] = 'inprogress';
        if ( count($ref_names['final']) < 1 ) $ref_names['final'][] = 'resolved';

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