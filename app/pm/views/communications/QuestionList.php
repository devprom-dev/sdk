<?php

class QuestionList extends PMPageList
{
    function getPredicates( $values )
	{
		$predicates = array(
			new QuestionAuthorFilter( $values['author'] ),
			new StatePredicate( $values['state'] ),
			new TransitionObjectPredicate( $this->getObject(), $values['transition'] ),
			new CustomTagFilter( $this->getObject(), $values['tag'] )
		);
		
		return array_merge(parent::getPredicates( $values ), $predicates);
	}
	
	function IsNeedToDisplay( $attr )
	{
		switch( $attr ) 
		{
			case 'UID': 
			case 'Content': 
			case 'RecentComment':
			case 'TraceRequests': 
			case 'Attachment': 
				return true;
				
			default:
				return false;
		}
	}

	function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'Content':
			    drawMore($object_it, 'Content', 20);
			    break;
			default:
				parent::drawCell( $object_it, $attr );
		}			
	}
	
 	function getColumnWidth( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Comments':
 				return '40%';
 				
 			default:
 				return parent::getColumnWidth( $attr );
 		}
 	}
}