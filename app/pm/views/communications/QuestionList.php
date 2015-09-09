<?php

class QuestionList extends PMPageList
{
    var $comment_it;
    
    function getPredicates( $values )
	{
		$predicates = array(
			new QuestionAuthorFilter( $values['author'] ),
			new StatePredicate( $values['state'] ),
			new TransitionObjectPredicate( $this->getObject(), $values['transition'] ),
			new CustomTagFilter( $this->getObject(), $values['tag'] ),
			new FilterAttributePredicate( 'Owner', $values['participant'] )
		);
		
		return array_merge(parent::getPredicates( $values ), $predicates);
	}
	
	function retrieve()
	{
	    global $model_factory;
	    
	    $iterator = parent::retrieve();
	    
	    $comment = $model_factory->getObject('Comment');
	    
	    $comment->addFilter( new CommentObjectFilter($iterator) );
	    
	    $comment->addSort( new SortRecentClause() );
	    
	    $this->comment_it = $comment->getAll();
	    
	    $this->comment_it->buildPositionHash( array('ObjectId') );
	    
	    return $iterator;
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

  	function IsNeedToSelect()
	{
		return true;
	}
	
	function IsNeedToSelectRow( $object_it )
	{
		return true;
	}
	
	function drawCell( $object_it, $attr ) 
	{
		global $model_factory;
		
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