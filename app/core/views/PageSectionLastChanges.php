<?php
include_once SERVER_ROOT_PATH."core/views/PageInfoSection.php";

class LastChangesSection extends InfoSection
{
 	var $object, $iterator, $items;
 	
 	function LastChangesSection( $object ) 
 	{
 		parent::InfoSection();
 		
 		$this->object = $object;
 		
 		$this->items = 8;
 	}
 	
 	function getCaption() 
 	{
 		return translate('Последние изменения');
 	}
 	
 	function getIterator() 
 	{
 		if ( !is_a($this->object, 'OrderedIterator') ) throw new Exception('Iterator is required');
 		
 		$registry = getFactory()->getObject('ObjectChangeLog')->getRegistry();

 		$registry->setLimit($this->items + 1);
 		
		return $this->iterator = $registry->Query(
			array (
				new ChangeLogItemDateFilter($this->object),
				new FilterBaseVpdPredicate(),
				new SortReverseKeyClause()
			)
		);
 	}

	function setItems( $items ) {
		$this->items = $items;
	}

 	function getItems() {
 		return $this->items;
 	}
 	
 	function & getIteratorRef() {
 		return $this->iterator;
 	}
 	
 	function getObject() {
 		return $this->object;
 	}

	function getTemplate() {
		return 'core/PageSectionLastChanges.php';
	}

	function getRenderParms()
	{
		$rows = array();
		
		$it = $this->getIterator();

		for($i = 0; $i < min($it->count(), $this->items); $i++) 
		{
			if ( $it->get('Content') == '' && $it->get('ChangeKind') == 'modified' ) {
				$it->moveNext();
				continue;
			}
			if ( $it->get('ChangeKind') == 'modified' && strpos($it->get('Content'), ';rarr;') !== false ) {
				$it->moveNext();
				continue;
			}

			$rows[] = array(
				'author' => $it->getHtmlDecoded('AuthorName'),
			 	'datetime' => $it->getDateTimeFormat('RecordModified'),
			    'caption' => $it->getHtmlDecoded('Content'),
				'icon' => $it->get('ChangeKind') == 'added' ? 'icon-plus-sign' : 'icon-pencil'
			); 
			
			$it->moveNext();
		}
		
		return array_merge( parent::getRenderParms(), array (
			'section' => $this,
			'rows' => $rows
		));
	}
}
