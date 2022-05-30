<?php

class WorkItemStateRegistry extends ObjectRegistrySQL
{
	function createSQLIterator( $sql )
	{
		return $this->createIterator(
			array (
				array (
					'pm_StateId' => 1,
					'ReferenceName' => 'initial',
					'Caption' => translate('Добавлено'),
					'IsTerminal' => 'N'
				),
				array (
					'pm_StateId' => 2,
                    'ReferenceName' => 'progress',
					'Caption' => translate('В работе'),
					'IsTerminal' => 'I'
				),
				array (
					'pm_StateId' => 3,
                    'ReferenceName' => 'final',
					'Caption' => translate('Выполнено'),
					'IsTerminal' => 'Y'
				)
			)
		);
	}
}