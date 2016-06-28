<?php

class WorkItemStateRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		$ref_names = array(
				'final' => array(),
				'initial' => array(),
				'progress' => array()
		);

		$state_objects = array(
			getFactory()->getObject('IssueState'),
			getFactory()->getObject('TaskState')
		);

		foreach( $state_objects as $state_object ) {
			$it = $state_object->getAll();
			while( !$it->end() ) {
				$key =  $it->get('VPD').$it->get('ObjectClass');
				if ( $ref_names['initial'][$key] == '' ) {
					$ref_names['initial'][$key] = $it->getId();
				}
				else {
					if ( $it->get('IsTerminal') == 'Y' ) {
						$ref_names['final'][] = $it->getId();
					}
					else {
						$ref_names['progress'][] = $it->getId();
					}
				}
				$it->moveNext();
			}
		}
        if ( count($ref_names['initial']) < 1 ) $ref_names['initial'][] = 'submitted';
		if ( count($ref_names['progress']) < 1 ) $ref_names['progress'][] = 'inprogress';
        if ( count($ref_names['final']) < 1 ) $ref_names['final'][] = 'resolved';

		return $this->createIterator(
			array (
				array (
					'pm_StateId' => 1,
					'ReferenceName' => join(',',
							array_unique($ref_names['initial'])
					),
					'Caption' => translate('Добавлено'),
					'IsTerminal' => 'N'
				),
				array (
					'pm_StateId' => 2,
					'ReferenceName' => join(',',
							array_unique($ref_names['progress'])
					),
					'Caption' => translate('В работе'),
					'IsTerminal' => 'N'
				),
				array (
					'pm_StateId' => 3,
					'ReferenceName' => join(',',
							array_unique($ref_names['final'])
					),
					'Caption' => translate('Выполнено'),
					'IsTerminal' => 'Y'
				)
			)
		);
	}
}