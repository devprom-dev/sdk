<?php

class StateMetaRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
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

        $state_object = $this->getObject()->getAggregatedStateObject();
        $state_it = $state_object->getRegistry()->Query(
            array (
                new FilterVpdPredicate(getFactory()->getObject($state_object->getObjectClass())->getVpds()),
                new SortOrderedClause()
            )
        );
        while (!$state_it->end()) {
            switch( $state_it->get('IsTerminal') ) {
                case 'N':
                    $ref_names['initial'][] = $state_it->get('ReferenceName');
                    $queueLengths['initial'] += $state_it->get('QueueLength');
                    break;
                case 'I':
                    $ref_names['progress'][] = $state_it->get('ReferenceName');
                    $queueLengths['progress'] += $state_it->get('QueueLength');
                    break;
                case 'Y':
                    $ref_names['final'][] = $state_it->get('ReferenceName');
                    $queueLengths['final'] += $state_it->get('QueueLength');
                    break;
            }
            $state_it->moveNext();
        }

        if ( $state_object instanceof IssueState && class_exists('RequestState') )
        {
            $state_it = getFactory()->getObject('RequestState')->getRegistry()->Query(
                array (
                    new FilterVpdPredicate(getFactory()->getObject($state_object->getObjectClass())->getVpds()),
                    new SortOrderedClause()
                )
            );
            while (!$state_it->end()) {
                switch( $state_it->get('IsTerminal') ) {
                    case 'N':
                        $ref_names['initial'][] = $state_it->get('ReferenceName');
                        $queueLengths['initial'] += $state_it->get('QueueLength');
                        break;
                    case 'I':
                        $ref_names['progress'][] = $state_it->get('ReferenceName');
                        $queueLengths['progress'] += $state_it->get('QueueLength');
                        break;
                    case 'Y':
                        $ref_names['final'][] = $state_it->get('ReferenceName');
                        $queueLengths['final'] += $state_it->get('QueueLength');
                        break;
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
                    'IsTerminal' => 'N',
                    'Description' => text(2638)
                ),
                array (
                    'pm_StateId' => 2,
                    'ReferenceName' => join(
                            $this->getObject()->getStatesDelimiter(),
                            array_unique($ref_names['progress'])
                    ),
                    'Caption' => translate('В работе'),
                    'QueueLength' => $queueLengths['progress'],
                    'IsTerminal' => 'I',
                    'Description' => text(2639)
                ),
                array (
                    'pm_StateId' => 3,
                    'ReferenceName' => join(
                            $this->getObject()->getStatesDelimiter(),
                            array_unique($ref_names['final'])
                    ),
                    'Caption' => translate('Выполнено'),
                    'QueueLength' => $queueLengths['final'],
                    'IsTerminal' => 'Y',
                    'Description' => text(2640)
                )
            )
		);
	}
}