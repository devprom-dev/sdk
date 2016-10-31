<?php

class CustomAttributeTypeRegistry extends ObjectRegistrySQL
{
 	function createSQLIterator( $sql )
 	{
 		return $this->createIterator(array(
			array (
				'entityId' => 1,
				'ReferenceName' => 'integer',
				'Caption' => translate('Число')
			),
			array (
				'entityId' => 2,
				'ReferenceName' => 'dictionary',
				'Caption' => translate('Справочник')
			),
			array (
				'entityId' => 3,
				'ReferenceName' => 'date',
				'Caption' => translate('Дата')
			),
			array (
				'entityId' => 4,
				'ReferenceName' => 'string',
				'Caption' => translate('Строка текста')
			),
			array (
				'entityId' => 5,
				'ReferenceName' => 'text',
				'Caption' => translate('Текстовое поле')
			),
			array (
				'entityId' => 6,
				'ReferenceName' => 'wysiwyg',
				'Caption' => translate('Редактор WYSIWYG')
			),
			array (
				'entityId' => 7,
				'ReferenceName' => 'reference',
				'Caption' => translate('Ссылка')
			),
			array (
				'entityId' => 8,
				'ReferenceName' => 'computed',
				'Caption' => text(2132)
			)
 		));  
 	}
}