<?php

class CustomAttributeTypeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 		return $this->createIterator(array(
 				array (
 						'entityId' => 1,
 						'ReferenceName' => 'integer',
 						'Caption' => translate('�����')
 				),
 				array (
 						'entityId' => 2,
 						'ReferenceName' => 'dictionary',
 						'Caption' => translate('����������')
 				),
 				array (
 						'entityId' => 3,
 						'ReferenceName' => 'date',
 						'Caption' => translate('����')
 				),
 				array (
 						'entityId' => 4,
 						'ReferenceName' => 'string',
 						'Caption' => translate('������ ������')
 				),
 				array (
 						'entityId' => 5,
 						'ReferenceName' => 'text',
 						'Caption' => translate('��������� ����')
 				),
 				array (
 						'entityId' => 6,
 						'ReferenceName' => 'wysiwyg',
 						'Caption' => translate('�������� WYSIWYG')
 				),
 				array (
 						'entityId' => 7,
 						'ReferenceName' => 'reference',
 						'Caption' => translate('������')
 				)
 		));  
 	}
}