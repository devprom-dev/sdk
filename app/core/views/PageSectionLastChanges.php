<?php
include_once SERVER_ROOT_PATH."core/views/PageInfoSection.php";

class PageSectionLastChanges extends InfoSection
{
 	var $object, $iterator, $items;
 	
 	function __construct( $object )
 	{
 		parent::__construct();
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
        return $objectIt->getHtmlDecoded('Content');
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
            if ( $anchor_it->object instanceof WikiPage ) {
                $url = '\\1';
                foreach( explode(ChangeLogAggregatePersister::CONTENT_SEPARATOR, $it->getHtmlDecoded('ObjectUrl')) as $data ) {
                    if ( strpos($data, 'history') !== false ) {
                        $url = $data;
                        break;
                    }
                }
                $content = preg_replace('/\[url=([^\]\s]+)(\s[^\]]+)?\]/i',
                                str_replace('%1', $url, text(2319)),
                                    $content);
            }

			$rows[] = array(
				'author' => $it->get('UserName') != '' ? $it->get('UserName') : $it->get('AuthorName'),
			 	'datetime' => $it->getDateTimeFormat('RecordCreated'),
			    'caption' => $content,
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
