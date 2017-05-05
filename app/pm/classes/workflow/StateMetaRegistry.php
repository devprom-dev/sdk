<?php

class StateMetaRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		$aggregated_state = $this->getObject()->getAggregatedStateObject();
		
		$ref_names = array(
            'initial' => array(),
            'progress' => array(),
            'final' => array(),
		);
        $queueLengths = array(
            'final' => 0,
            'initial' => 0,
            'progress' => 0
        );

		if ( !is_array($aggregated_state) ) $aggregated_state = array($aggregated_state);

		foreach( $aggregated_state as $state_object ) {
            $state_it = $state_object->getRegistry()->Query(
                array (
                    new FilterVpdPredicate(),
                    new SortOrderedClause()
                )
            );
            while (!$state_it->end()) {
                if ( !isset($ref_names['initial'][$state_it->get('VPD')]) ) {
                    $ref_names['initial'][$state_it->get('VPD')] = $state_it->get('ReferenceName');
                }
                $state_it->moveNext();
            }
            $state_it->moveFirst();
			while (!$state_it->end()) {
			    if ( in_array($state_it->get('ReferenceName'), $ref_names['initial']) ) {
                    $queueLengths['initial'] += $state_it->get('QueueLength');
                }
				if ($state_it->get('IsTerminal') == 'Y') {
					$ref_names['final'][] = $state_it->get('ReferenceName');
                    $queueLengths['final'] += $state_it->get('QueueLength');
				}
                elseif ( !in_array($state_it->get('ReferenceName'), $ref_names['initial']) ) {
                    $ref_names['progress'][] = $state_it->get('ReferenceName');
                    $queueLengths['progress'] += $state_it->get('QueueLength');
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
                    'QueueLength' => $queueLengths['initial'],
                    'IsTerminal' => 'N'
                ),
                array (
                    'pm_StateId' => 2,
                    'ReferenceName' => join(
                            $this->getObject()->getStatesDelimiter(),
                            array_unique($ref_names['progress'])
                    ),
                    'Caption' => translate('В работе'),
                    'QueueLength' => $queueLengths['progress'],
                    'IsTerminal' => 'N'
                ),
                array (
                    'pm_StateId' => 3,
                    'ReferenceName' => join(
                            $this->getObject()->getStatesDelimiter(),
                            array_unique($ref_names['final'])
                    ),
                    'Caption' => translate('Выполнено'),
                    'QueueLength' => $queueLengths['final'],
                    'IsTerminal' => 'Y'
                )
            )
		);
	}
}