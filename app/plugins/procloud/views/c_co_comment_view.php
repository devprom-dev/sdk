<?php

/////////////////////////////////////////////////////////////////////////////////
class CoCommentPageContent extends CoPageContent
{
	function validate()
	{
		global $project_it, $_REQUEST, $model_factory, $user_it;
		
 		if ( $_REQUEST['id'] == '' && $_REQUEST['object'] == '' )
 		{
 			return false;
 		}

		if ( is_object($project_it) && $project_it->count() > 0 )
		{
			$session = new PMSession($project_it);

			if ( !$project_it->IsPublic() && !$project_it->HasProductSite() )
			{
				return false;
			}
		}
		else
		{
			$model_factory->enableVpd(false);
		}
		
		$object = $model_factory->getObject($_REQUEST['object']);
		$object_it = $object->getExact($_REQUEST['id']);

		if ( $object_it->count() < 1 )
		{
			return false;
		}

		if ( $_REQUEST['text'] != '' )
		{
			if ( !$user_it->IsReal() || $user_it->count() < 1 )
			{
				return false;
			}

			if ( $object->getClassName() == 'Comment' )
			{
				$prev_comment_it = $object_it;
				$object_it = $object_it->getAnchorIt();
			}
			
	 		$comment = $model_factory->getObject2('Comment', $object_it );
	 		$comment_text = $object_it->Utf8ToWin($_REQUEST['text']);

			if ( $comment->isVpdEnabled() && !is_object($project_it) )
			{
				return false;
			}

	 		if ( is_object($prev_comment_it) )
	 		{
	 			$last_comment_id = $prev_comment_it->getId();
	 		}
	 		else
	 		{
	 			$last_comment_id = '';
	 		}

	 		$comment_id = $comment->add_parms( 
	 			array('AuthorId' => $user_it->getId(),
	 				  'ObjectId' => $object_it->getId(),
	 				  'ObjectClass' => get_class($object_it->object),
	 				  'PrevComment' => $last_comment_id,
	 				  'Caption' => $comment_text) );
	 				  
	 		return false;
		}
		
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Pragma: no-cache"); // HTTP/1.0
		header("Content-Type: text/html; charset=windows-1251");

		$comment = $model_factory->getObject2('Comment', $object_it);
		$comment->defaultsort = "RecordCreated DESC";
		
		$comment_it = $comment->getAllRootsForObject($object_it);

		echo '<div class="reply">';
			echo '<div class="button" id="reply0" title="'.translate('Добавить комментарий').'">';
				if ( $user_it->IsReal() )
				{
					echo '<a href="javascript: getPostComment(0);">'.translate('Добавить комментарий').'</a>';
				}
				else
				{
					echo '<a href="javascript: getLoginForm(\'javascript: getPostComment(0)\');">'.translate('Добавить комментарий').'</a>';
				}
			echo '</div>';

			echo '<div id="comment0" class="postreply">';
			echo '</div>';
		echo '</div>';

		echo '<div id="combody">';
		echo '</div>';

		if ( $comment_it->count() > 0 )
		{
 			$this->drawThread( $comment_it, 1 );
		}

		die();
	}
	
	function drawThread( $comment_it, $level = 0 )
	{
		global $model_factory, $user_it;
		
 		if ( $level > 50 || $comment_it->count() < 1 ) 
 		{
 			return;
 		}
 		
		$user = $model_factory->getObject('cms_User');
 		
 		do 
 		{
 			$author_it = $user->getExact($comment_it->get('AuthorId'));
 			
 			echo '<div style="padding-left:'.($level * 34).'px;">';
				echo '<div class="info">';
					echo '<div class="date">';
						echo $comment_it->getDateTimeFormat('RecordCreated');
					echo '</div>';
					echo '<div class="author">';
						echo '<a href="'.ParserPageUrl::parse($author_it).'">'.$author_it->getDisplayName().'</a>';
					echo '</div>';
				echo '</div>';
	
				echo '<div class="content">';
					echo $comment_it->getHtml('Caption');
				echo '</div>';
	
				echo '<div class="reply">';
					echo '<div class="button" id="reply'.$comment_it->getId().'" title="'.translate('Ответить').'">';
					if ( $user_it->IsReal() )
					{
						echo '<a href="javascript: getPostComment('.$comment_it->getId().');">'.translate('Ответить').'</a>';
					}
					else
					{
						echo '<a href="javascript: getLoginForm(\'javascript: getPostComment('.$comment_it->getId().')\');">'.translate('Ответить').'</a>';
					}
					echo '</div>';
					
					echo '<div id="comment'.$comment_it->getId().'" class="postreply">';
					echo '</div>';
				echo '</div>';
			echo '</div>';

 			$thread_it = $comment_it->getThreadIt();
 			
 			$this->drawThread( $thread_it, $level + 1 );
 		}
 		while ( $comment_it->moveNext() );
 		
	}
}

?>
