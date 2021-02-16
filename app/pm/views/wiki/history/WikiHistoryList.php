<?php
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH.'pm/methods/RevertWikiWebMethod.php';
include_once SERVER_ROOT_PATH . "pm/views/wiki/diff/WikiHtmlDiff.php";

class WikiHistoryList extends ProjectLogList
{
 	var $can_revert;
 	var $editor;
 	var $change_it;
 	
	function retrieve()
	{
		parent::retrieve();

		$object_it = $this->getTable()->getObjectIt();
		if ( $object_it->getId() < 1 ) return;

		$this->can_revert = true;
		$this->documentMode = $object_it->get('TotalCount') > 0;
		$this->editor = WikiEditorBuilder::build($object_it->get('ContentEditor'));
		$this->parser = $this->editor->getComparerParser();
        $this->parser->setObjectIt($object_it);

		$filterValues = $this->getFilterValues();
		$this->change_it = getFactory()->getObject('WikiPageChange')->getRegistry()->Query(
			array (
				new FilterAttributePredicate('WikiPage', $object_it->idsToArray()),
				new FilterModifiedBeforePredicate($filterValues['finish']),
				new FilterModifiedAfterPredicate($_REQUEST['start']),
				new SortAttributeClause('WikiPage'),
				new SortAttributeClause('RecordCreated')
			)
		);
	}

	function getColumnFields()
	{
		$fields = parent::getColumnFields();
 		
		unset($fields[array_search('EntityName', $fields)]);
		unset($fields[array_search('Project', $fields)]);

		return $fields; 
	}
	
	function getGroupFields()
	{
		$fields = parent::getGroupFields();
 		
		unset($fields[array_search('EntityName', $fields)]);
		unset($fields[array_search('Project', $fields)]);

		return $fields; 
	}
	
    function getChangeIds( $object_it )
    {
        $history_url = explode(',',$object_it->getHtmlDecoded('ObjectUrl'));
        if ( count($history_url) < 1 ) $history_url = array($object_it->getHtmlDecoded('Content'));

        $ids = array();
        foreach( $history_url as $url ) {
            if ( preg_match('/\&version=([\d]+)/i', $url, $matches) && is_object($this->change_it) ) {
                $ids[] = $matches[1];
            }
        }
        if ( count($ids) < 1 ) return array();
        asort($ids);
        return $ids;
    }

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Content':
			    $ids = $this->getChangeIds($object_it);

                $this->change_it->moveToId(array_shift($ids));
                $prevContent = $this->change_it->getHtmlDecoded('Content');
                if ( count($ids) < 1 ) {
                    $this->change_it->moveNext();
                }
                else {
                    $this->change_it->moveToId(array_pop($ids));
                    $this->change_it->moveNext();
                }
                $nowContent = $this->change_it->getHtmlDecoded('Content');

                if ( $this->change_it->get('WikiPage') != $object_it->get('ObjectId') || $nowContent == '' ) {
                    $pageIt = $this->getTable()->getObjectIt();
                    $pageIt->moveToId($object_it->get('ObjectId'));
                    $nowContent = $pageIt->getHtmlDecoded('Content');
                }

                if ( $prevContent != '' ) {
                    $data = $object_it->getData();
                    $data[$attr] = preg_replace(
                        '/\[url=[^\]]+\]/i', $this->getPagesDiff( $prevContent, $nowContent ), $data[$attr]
                    );
                    $object_it = $object_it->object->createCachedIterator(array($data));
                }

                parent::drawCell( $object_it, $attr );
				break;
				
			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function IsNeedToDisplayOperations( ) { return true; }
	
	function getActions( $object_it ) 
	{
		$actions = array();
		if ( !is_object($this->change_it) ) return $actions;

		$ids = $this->getChangeIds( $object_it );
        $revisionBeforeChanges = array_shift($ids);
		if ( $revisionBeforeChanges < 1 ) return $actions;
		
		$page_it = $object_it->getObjectIt();

		$method = new ObjectModifyWebMethod($page_it);
        $method->setObjectUrl($method->getObjectUrl() . '&revision=' . $revisionBeforeChanges);
		$actions[] = array( 
			'name' => text(1847),
			'url' => $method->getJSCall()
		);
		
		$method = new RevertWikiWebMethod();
		if ( getFactory()->getAccessPolicy()->can_modify($page_it) )
		{
			$actions[] = array();
			$actions[] = array(
				'name' => translate('Отменить'),
				'url' => $method->url( $page_it, $object_it, $revisionBeforeChanges )
			);
		}
		
		return $actions;
	}
	
	function getPagesDiff( $prev_content, $curr_content )
	{
		$html = '<div class="wysiwyg-body">';
		$diffBuilder = new WikiHtmlDiff(
			$this->parser->parse($prev_content),
			$this->parser->parse($curr_content)
		);
        $html .= $diffBuilder->build();
		$html .= '</div>';
		return $html;
	}
}
 