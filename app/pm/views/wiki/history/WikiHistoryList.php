<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class WikiHistoryList extends ProjectLogList
{
 	var $prev_content;
 	var $curr_content;
 	var $can_revert;
 	var $editor;
 	var $change_it;
 	
	function retrieve()
	{
		global $model_factory;
		
		parent::retrieve();
	
		$it = $this->getIteratorRef();
		
		$filter_values = $this->getFilterValues();
		
		$object_it = $this->getTable()->getObjectIt();
		
		if ( $object_it->getId() < 1 ) return;
		
		$this->can_revert = true; 
		
		$this->editor = WikiEditorBuilder::build($object_it->get('ContentEditor'));
		
		$this->change_it = getFactory()->getObject('WikiPageChange')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('WikiPage', $object_it->idsToArray()),
						new SortAttributeClause('WikiPage'),
						new SortAttributeClause('RecordCreated')
				)
		);
		
		$object_it->moveFirst();
		
		while( !$object_it->end() )
		{
			$this->curr_content[$object_it->getId()] = $object_it->getHtmlDecoded('Content');
			
			$object_it->moveNext();
		}
	}

	function getSorts()
	{
		$sorts = PageList::getSorts();
		foreach( $sorts as $key => $sort )
		{
			if ( !$sort instanceof SortAttributeClause ) continue;
			if ( $sort->getAttributeName() == 'ChangeDate' ) {
				$sorts[$key] = new SortRecentClause();
			}
		}
		return $sorts;
	}
	
  	function setupColumns()
 	{
        parent::setupColumns();
        
        $object_it = $this->getTable()->getWikiPageIt();

        if ( $object_it->getId() > 0 && $object_it->get('ParentPage') == '' )
        {
        	$this->getObject()->setAttributeVisible('Caption', true);
        }
        
        $this->getObject()->setAttributeCaption('Caption', $object_it->object->getDisplayName());
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
	
	function getChangeIt( $object_it )
 	{
		$history_url = $object_it->getHtmlDecoded('ObjectUrl');
		if ( $history_url == '' ) $history_url = $object_it->getHtmlDecoded('Content');
		if ( preg_match('/\&version=([\d]+)/i', $history_url, $matches) )
		{
			$this->change_it->moveToId($matches[1]);
			return $this->change_it->copy();
		}
		else
		{
			return $this->change_it->object->getEmptyIterator();
		}
 	}
 	
	function drawCell( $object_it, $attr ) 
	{
		global $model_factory, $session;

		switch ( $attr ) 
		{
			case 'Content':
				
				if ( strpos($object_it->get('Content'), '[url=') === false )
				{
					parent::drawCell( $object_it, $attr );	
					
					break;
				}
				
				if ( !is_object($this->change_it) )
				{
					parent::drawCell( $object_it, $attr );	
					
					break;
				}
				
				$change_it = $this->getChangeIt( $object_it );

				if ( $change_it->getId() > 0 )
				{
					$page_id = $object_it->get('ObjectId');

					$this->prev_content[$page_id] = $change_it->getHtmlDecoded('Content');
					
					$diff = $this->getPagesDiff( $this->prev_content[$page_id], $this->curr_content[$page_id] );
		            
		            if ( $diff == '' )
		            {
						echo ($this->prev_content[$page_id] != $this->curr_content[$page_id] ? text(1508) : translate('Нет изменений'));
		            }
		            else
		            {
		            	echo $diff;
		            }
		            
					$this->curr_content[$page_id] = $this->prev_content[$page_id];
				}
				else
				{
					parent::drawCell( $object_it, $attr );	
				}
				
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

		$change_it = $this->getChangeIt( $object_it );
		
		if ( $change_it->getId() < 1 ) return $actions;
		
		$page_it = $object_it->getObjectIt();
		
		$actions[] = array( 
			'name' => text(1847),
			'url' => $page_it->getViewUrl().'&revision='.$change_it->getId()
		);
		
		$method = new RevertWikiWebMethod();
		if ( $change_it->get('RecentChangesCount') < 1 && getFactory()->getAccessPolicy()->can_modify($page_it) )
		{
			$actions[] = array();
			$actions[] = array(
				'name' => translate('Отменить'),
				'url' => $method->getJSCall( $page_it, $object_it ) 
			);
		}
		
		return $actions;
	}
	
	function getPagesDiff( $prev_content, $curr_content )
	{
		$method = new WikiFilterHistoryFormattingWebMethod();

		switch ( $method->getValue() )
		{
			case 'text':

				$parser = $this->editor->getHtmlParser();
				
			    $html2text = new Html2Text($parser->parse($prev_content));
				$prev_content = $html2text->get_text(); //preg_replace('/[\r\n]+/', '<br/>', $html2text->get_text());

				$html2text = new Html2Text($parser->parse($curr_content));
				$curr_content = $html2text->get_text(); //preg_replace('/[\r\n]+/', '<br/>', $html2text->get_text());

				return $this->editor->getDiff( $prev_content, $curr_content );
				
			default:
				return $this->editor->getDiff( $prev_content, $curr_content );
		}		
	}
}
 