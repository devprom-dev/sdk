<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH."ext/htmldiff/html_diff.php";
include_once "EmailNotificatorHandler.php";

class WikiHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		// notify author of wiki page
		$result = array(
				$object_it->get('Author')
		);

		// notify co-authors of wiki page
		if ( $action == 'modify' )
		{
			$change_it = getFactory()->getObject('WikiPageChange')->getByRef('WikiPage', $object_it->getId());
			$result = array_merge($result, $change_it->fieldToArray('Author')); 
		}

		return $result;
	}	
 	
	function getUsers( $object_it, $prev_object_it, $action ) 
	{
		$result = array();

		// notify assignee and owners of tasks and issues related to wiki page
		if ( $object_it->get('Tasks') != '' ) {
			$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
					array (
							new FilterInPredicate($object_it->get('Tasks')),
							new StatePredicate('notresolved')
					)
			);
			$result = array_merge($result, $task_it->fieldToArray('Assignee'));
		}

		if ( $object_it->get('Increments') != '' ) {
			$issue_it = getFactory()->getObject('Request')->getRegistry()->Query(
					array (
							new FilterInPredicate($object_it->get('Increments')),
							new StatePredicate('notresolved')
					)
			);
			$result = array_merge($result, $issue_it->fieldToArray('Owner'));
			
			$task_it = getFactory()->getObject('Task')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('ChangeRequest', $issue_it->idsToArray()),
							new StatePredicate('notresolved')
					)
			);
			$result = array_merge($result, $task_it->fieldToArray('Assignee'));
		}
		
		return $result;
	}
	
	protected function getFields( $action, $object_it, $prev_object_it )
	{
		$fields = parent::getFields( $action, $object_it, $prev_object_it );
		
		if ( $action == 'modify' )
		{
			$change_it = getFactory()->getObject('WikiPageChange')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('WikiPage', $object_it->getId()),
							new SortRecentClause()
					)
				);

			$editor = WikiEditorBuilder::build($object_it->get('ContentEditor'));
			$editor->setObjectIt( $object_it );
			
			$diff_content = $this->getDiff( $editor, $change_it, $object_it );
			
			$diff_content = str_replace("diff-html-removed", '" style="background:#F59191;', $diff_content);
			$diff_content = str_replace("diff-html-added", '" style="background:#90EC90;', $diff_content);
			
			$fields['Content']['value'] = $diff_content;
			unset($fields['Content']['was_value']);
		}
		
		return $fields;
	}
	
	protected function getDiff( & $editor, & $change_it, & $object_it )
	{
		$parser = $editor->getComparerParser();

 		$diff = html_diff(
						IteratorBase::wintoutf8($parser->parse($change_it->getHtmlDecoded('Content'))),
	 					IteratorBase::wintoutf8($parser->parse($object_it->getHtmlDecoded('Content')))
		);
			
 		if ( strpos($diff, "diff-html-") !== false )
 		{
 			return IteratorBase::utf8towin( $diff );  
 		}
 		else
 		{
 			return $editor->getPageParser()->parse($object_it->getHtmlDecoded('Content'));
 		}
	}
		
	public static function getValue( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Content':
				$editor = WikiEditorBuilder::build( $object_it->get('ContentEditor') );
				$editor->setObjectIt( $object_it );
				
				$parser = $editor->getHtmlParser();
				$parser->setObjectIt( $object_it );
				return $parser->parse($object_it->getHtmlDecoded('Content'));
				
			default:
				return parent::getValue( $object_it, $attr );
		}
	}

	protected function IsAttributeVisible( $attribute_name, $object_it, $action )
	{
		switch ( $attribute_name )
		{
			case 'Content':
				return true;
				
			default:
				return parent::IsAttributeVisible( $attribute_name, $object_it, $action );
		}		
	}
	
	protected function IsAttributeRequired( $attribute_name, $object_it, $action )
	{
		switch ( $attribute_name )
		{
			case 'Caption':
			case 'Author':
			case 'ParentPage':
				return false;
				
			default:
				return parent::IsAttributeRequired( $attribute_name, $object_it, $action );
		}
	}
}
