<?php

class ChangeLogActionRegistry extends ObjectRegistrySQL
{
    function createSQLIterator( $sql )
    {
        return $this->createIterator(
				array (
						array ( 'entityId' => 1, 'ReferenceName' => 'added', 'Caption' => translate('��������') ),
						array ( 'entityId' => 2, 'ReferenceName' => 'modified', 'Caption' => translate('���������') ),
						array ( 'entityId' => 3, 'ReferenceName' => 'deleted', 'Caption' => translate('��������') ),
						array ( 'entityId' => 4, 'ReferenceName' => 'commented', 'Caption' => translate('����������') )
	        	)
		);
    }
}