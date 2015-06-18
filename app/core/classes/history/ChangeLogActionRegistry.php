<?php

class ChangeLogActionRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator(
				array (
						array ( 'entityId' => 1, 'ReferenceName' => 'added', 'Caption' => translate('Создание') ),
						array ( 'entityId' => 2, 'ReferenceName' => 'modified', 'Caption' => translate('Изменение') ),
						array ( 'entityId' => 3, 'ReferenceName' => 'deleted', 'Caption' => translate('Удаление') ),
						array ( 'entityId' => 4, 'ReferenceName' => 'commented', 'Caption' => translate('Обсуждение') )
	        	)
		);
    }
}