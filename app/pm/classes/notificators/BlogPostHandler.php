<?php

include_once "EmailNotificatorHandler.php";
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class BlogPostHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		if ( $action != 'add' ) return array();
		return getSession()->getProjectIt()->getParticipantIt()->idsToArray();
	}

	function getUsers( $object_it, $prev_object_it, $action )
	{
		if ( $action != 'add' ) return array();
		if ( class_exists('PortfolioMyProjectsBuilder', false) ) return array();
		return getFactory()->getObject('UserActive')->getAll()->fieldToArray('Email');
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
}  
