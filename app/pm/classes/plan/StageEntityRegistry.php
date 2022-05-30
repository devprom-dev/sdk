<?php

class StageEntityRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	 	return $this->createIterator( array (
            array (
                'entityId' => 1,
                'ReferenceName' => 'Release',
                'Caption' => translate('Релиз')
            ),
            array (
                'entityId' => 2,
                'ReferenceName' => 'Iteration',
                'Caption' => translate('Итерация')
            ),
            array (
                'entityId' => 2,
                'ReferenceName' => 'Milestone',
                'Caption' => translate('Веха')
            )
 	 	));
 	}
}