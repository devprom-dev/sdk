<?php

class ForecastModeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 	 	return $this->createIterator( array (
            array (
                'entityId' => 1,
                'ReferenceName' => 'visible',
                'Caption' => translate('Показывать')
            ),
            array (
                'entityId' => 2,
                'ReferenceName' => 'invisible',
                'Caption' => translate('Не показывать')
            )
 	 	));
 	}
}