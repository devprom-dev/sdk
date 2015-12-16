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
		if ( array_key_exists('Content', $fields) )
		{
			if ( $action == 'modify' && $fields['Content']['was_value'] != '' )
			{
				$diff_content = $this->getDiff( $fields['Content']['was_value'], $fields['Content']['value'] );
				if ( trim(preg_replace('/[\s\r\n]/', '', $diff_content)) == '' ) {
					unset($fields['Content']);
				}
				else {
					$fields['Content']['value'] = $diff_content;
					unset($fields['Content']['was_value']);
				}
			}
			else {
				$fields['Content']['value'] = parent::getValue( $object_it, 'Content' );
				unset($fields['Content']['was_value']);
			}
		}
		return $fields;
	}
	
	protected function getDiff( $was_value, $now_value )
	{
 		$diff = html_diff(
			IteratorBase::wintoutf8($was_value),
			IteratorBase::wintoutf8($now_value)
		);
			
 		if ( strpos($diff, "diff-html-") !== false )
 		{
			$diff = str_replace("diff-html-removed", '" style="background:#F59191;', $diff);
			$diff = str_replace("diff-html-added", '" style="background:#90EC90;', $diff);
 			return $diff;
 		}
 		else
 		{
 			return '';
 		}
	}

	public static function getWasValue( $object_it, $attr )
	{
		return self::getValue( $object_it, $attr );
	}

	public static function getValue( $object_it, $attr )
	{
		switch( $attr )
		{
			case 'Content':
				$editor = WikiEditorBuilder::build($object_it->get('ContentEditor'));
				$editor->setObjectIt( $object_it );
				$parser = $editor->getComparerParser();
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

			case 'Author':
				return false;

			default:
				return parent::IsAttributeVisible( $attribute_name, $object_it, $action );
		}		
	}
}
