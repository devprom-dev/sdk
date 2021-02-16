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

 	function setObjectIt( $objectIt ) {
 	    $this->object = $objectIt;
    }

 	function getIterator() 
 	{
 		if ( !is_a($this->object, 'OrderedIterator') ) throw new Exception('Iterator is required');
 		
 		$registry = getFactory()->getObject('ChangeLogAggregated')->getRegistry();

 		$registry->setLimit($this->items + 1);
 		
		return $this->iterator = $registry->Query(
			array (
				new ChangeLogItemDateFilter($this->object),
				new FilterBaseVpdPredicate(),
				new SortReverseKeyClause(),
                new FilterAttributePredicate('ChangeKind', 'added,modified,deleted')
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

	function getContent( $objectIt ) {
        return $objectIt->get('Content');
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

			$content = $this->getContent($it);
            $anchor_it = $it->getObjectIt();
            if ( strpos($content, '[url') !== false && $anchor_it->object instanceof WikiPage ) {
                $content = str_replace('%1', $anchor_it->getHistoryUrl().'&start='.$it->getDateTimeFormat('RecordModified'), text(2319));
            }

			$rows[] = array(
				'author' => $it->get('UserName') != '' ? $it->get('UserName') : $it->get('AuthorName'),
			 	'datetime' => $it->getDateTimeFormat('RecordModified'),
			    'caption' => $content,
				'icon' => $it->get('ChangeKind') == 'added' ? 'icon-plus-sign' : 'icon-pencil'
			); 
			
			$it->moveNext();
		}

		$className = strtolower(get_class($this->object->object));
		return array_merge( parent::getRenderParms(), array (
			'section' => $this,
			'rows' => $rows,
            'moreUrl' => count($rows) >= 5
                ? getFactory()->getObject('PMReport')->getExact('project-log')->getUrl(
                        'entities='.$className.'&'.$className.'='.$this->object->getId().'&start='.$this->object->getDateFormatted('RecordCreated')
                    )
                : ''
		));
	}
}
