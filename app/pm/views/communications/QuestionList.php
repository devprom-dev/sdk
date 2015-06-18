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
			case 'Comments':
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
				
			case 'Comments':
				$this->comment_it->moveTo( 'ObjectId', $object_it->getId() );
			    
			    if ( $this->comment_it->get('ObjectId') != $object_it->getId() ) break;
			    
			    $user_it = $this->comment_it->getRef('AuthorId');

		        echo '<div class="row-fluid">';
    				echo '<span class="span1" style="width:55px;">';
		    	    	echo $this->getTable()->getView()->render('core/UserPicture.php', array (
							'id' => $user_it->getId(), 
		    	    		'class' => 'user-avatar',
		    	    		'title' => $user_it->getDisplayName()
						));
    				echo '</span>';
    				
    				echo '<span class="span9">';
    				    echo '<div>'.$user_it->getDisplayName().'</div>';
    				    echo '<div>';
    				        drawMore($this->comment_it, 'Caption', 35);
    				    echo '</div>';
    				echo '</span>';
				echo '</div>';

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