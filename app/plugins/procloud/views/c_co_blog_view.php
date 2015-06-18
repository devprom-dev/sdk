<?php

/////////////////////////////////////////////////////////////////////////////////
class CoBlogPageContent extends CoPageContent
{
	function validate()
	{
		global $model_factory, $project_it, $_REQUEST;
		
		if ( !is_object($project_it) )
		{
			return false;
		}
		
		if ( $project_it->count() < 1 )
		{
			return false;
		}
		
		if ( !$project_it->IsPublic() || $project_it->HasProductSite() )
		{
			return false;
		}

		if ( !$project_it->IsPublicBlog() )
		{
			return false;
		}

		if ( $_REQUEST['action'] != '' )
		{
			switch ( $_REQUEST['action'] )
			{
				case 'tag':
					$tag = $model_factory->getObject('Tag');
					$this->tag_it = $tag->getExact($_REQUEST['id']);
					
					if ( $this->tag_it->count() < 1 )
					{
						return false;
					}
					
					break;

				default:
					return false;
			}
		}
		else
		{
			if ( $_REQUEST['id'] != '' )
			{
				$post = $model_factory->getObject('BlogPost');
				$this->post_it = $post->getExact($_REQUEST['id']);
	
				if ( $this->post_it->count() < 1 )
				{
					return false;
				}
			}
		}
		
		return true;
	}
	
	function getTitle()
	{
		if ( is_object($this->post_it) )
		{
			return $this->post_it->getDisplayName().' - '.parent::getTitle();
		}
		else if ( is_object($this->tag_it) )
		{
			return $this->tag_it->getDisplayName().' - '.parent::getTitle();
		}
		else
		{
			return translate('Новости').' - '.parent::getTitle();
		}
	}
	
	function getKeywords()
	{
		return parent::getKeywords().' '.translate('новости').' '.translate('новое').' '.
			translate('блог').' '.translate('последнее').' '.translate('проект');
	}
	
	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();

		$this->drawProjectHeader('<a href="/news/'.$project_it->get('CodeName').'">'.translate('Новости').'</a>');
				
		// introduction
		echo '<div id="bloglist">';

			$post = $model_factory->getObject('BlogPost');
			
			if ( $_REQUEST['id'] != '' )
			{
				if ( $_REQUEST['action'] == 'tag' )
				{
					$tag = $model_factory->getObject('BlogPostTag');
					$post_it = $tag->getPostsByTag( $_REQUEST['id'] );

					$this->drawBlog( $post_it );
				}
				else
				{
					$this->drawPost( $_REQUEST['id'] );
				}
			}
			else
			{
				$total = $post->getByRefArrayCount(
					array( 'Blog' => $project_it->get('Blog') ) );

				$post_it = $post->getByRefArray(
					array( 'Blog' => $project_it->get('Blog') ), 5, $_REQUEST['page']);

				$this->drawBlog( $post_it, $total, 5 );
			}
	
		echo '</div>';

		echo '<div id="user_actions">';

			if ( $_REQUEST['id'] != '' )
			{
				$page->drawShareActionBox();
			}

//			if ( $_REQUEST['id'] == '' )
			{
				echo '<div class="action_box">';
					$page->drawGreyBoxBegin();
		
					echo '<div id="title">';
						echo translate('Тэги');
					echo '</div>';
		
					echo '<div id="content">';
						$tag = $model_factory->getObject('BlogPostTag');
						$tag_it = $tag->getAllTags();
						
						while ( !$tag_it->end() )
						{
							echo '<div>';
								echo '<a href="'.ParserPageUrl::parse($tag_it).'">' .
									$tag_it->getDisplayName().'</a> ('.$tag_it->get('ItemCount').')';
							echo '</div>';
				
							$tag_it->moveNext();
						}
					echo '</div>';
					$page->drawGreyBoxEnd();
				echo '</div>';
			}			
	
			if ( $project_it->IsPublicArtefacts() )
			{
				echo '<div class="action_box">';
					$page->drawGreyBoxBegin();
					
						echo '<div id="title">';
							echo translate('Загрузить');
						echo '</div>';
			
						echo '<div id="content">';
							$artefact = $model_factory->getObject('pm_Artefact');
							$artefact_it = $artefact->getLatestDisplayed(4);
							
							while ( !$artefact_it->end() )
							{
								echo '<div>';
								echo '<a href="'.ParserPageUrl::parse($artefact_it).'" title="'.$artefact_it->get('Description').'">'.
										$artefact_it->getDisplayName().'</a>';
								echo '</div>';
					
								$artefact_it->moveNext();
							}
						echo '</div>';
					$page->drawGreyBoxEnd();
				echo '</div>';			
			}
			
			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();

				echo '<div id="title">';
					echo translate('Обсуждения');
				echo '</div>';

				$comment = $model_factory->getObject('Comment');
				$comment_it = $comment->getByEntities( array('pmblogpost') );

				while ( !$comment_it->end() )
				{
					$object_it = $comment_it->getAnchorIt();
					if ( $object_it->count() < 1 )
					{
						$comment_it->moveNext();
						continue;
					}

					echo '<div id="comcount" title="'.translate('Комментарии').'">';
						echo '<a href="'.ParserPageUrl::parse($object_it).'#comment">'.$comment_it->get('CommentsCount').'</a>';
					echo '</div>';

					echo '<div style="float:left;">'.$comment_it->getDateTimeFormat('RecordModified').'</div>';
						
					echo '<div style="padding-bottom:16px;clear:both;">'.
						$object_it->getWordsOnly('Caption', 12).'</div>';

					$comment_it->moveNext();
				}

				$page->drawGreyBoxEnd();
			echo '</div>';			
		echo '</div>';

		
		echo '<div style="clear:both;">&nbsp;</div>';
	}
	
	function drawBlog( $post_it, $total = 0, $limit = 0 )
	{
		global $model_factory, $project_it;
		
		$page = $this->getPage();
		$comment = $model_factory->getObject('Comment');
								
		for ( $i = 0; $i < $post_it->count(); $i++ )
		{
			echo '<div class="post">';
				$page->drawWhiteBoxBegin();
				
				echo '<h2>';
					echo '<a href="'.ParserPageUrl::parse($post_it).'">'.
						$post_it->getDisplayName().'</a>';
				echo '</h2>';
				
				echo '<div>'.$post_it->getDateTimeFormat('RecordCreated').'</div>';
				echo '<br/>';
				
				echo '<div id="entry" style="clear:both;">';
					$parser = new SiteBlogParser($post_it, $project_it);
					
					$more_text = false;
					echo $parser->parse_substr( $post_it->get('Content'), 320, $more_text );
					
					if ( $more_text )
					{
						echo '<div id="more"><a href="'.ParserPageUrl::parse($post_it).'">'.
							translate('читать дальше').'</a></div>';

						echo '<br/>';
					}
				echo '</div>';

				echo '<div>';
					echo '<div id="comcount" title="'.translate('Комментарии').'">';
						echo '<a href="'.ParserPageUrl::parse($post_it).'#comments">'.$comment->getCount($post_it).'</a>';
					echo '</div>';

					echo '<div id="tags">';
						$tag_it = $post_it->getTagsIt();
						$tags = array();
						
						while ( !$tag_it->end() )
						{
							$tags[$tag_it->getPos()] = '<a class="tag" href="'.
								ParserPageUrl::parse($tag_it).'">'.$tag_it->getDisplayName().'</a>';
								
							$tag_it->moveNext();	
						}
						echo join($tags, ', ');
					echo '</div>';
				echo '</div>';


				$post_it->moveNext();

				$page->drawWhiteBoxEnd();
			echo '</div>';	

			echo '<br/>';
		}		
		
		$this->drawPaging( $total, $limit );
		
	}
	
	function drawPost( $id )
	{
		global $model_factory, $project_it, $user_it;
		
		$page = $this->getPage();
		
		$post = new BlogPost;
		$user = $model_factory->getObject('pm_Participant');
		$comment = $model_factory->getObject('Comment');
		
		$post_it = $post->getExact($id);

		if ( $post_it->count() < 1 )
		{
			return;
		}
		
		echo '<div class="post">';
			$page->drawWhiteBoxBegin();
			
			if ( $project_it->HasUserAccess($user_it) )
			{
				echo '<div style="float:left;margin-right:8px;">';
					$page->drawBlackButton('<a href="/pm/'.$project_it->get('CodeName').
						'/'.$post_it->getEditUrl().'">'.translate('Редактировать').'</a>');	
				echo '</div>';
			}

			echo '<div style="float:left;">';
				echo '<h2>';
					echo $post_it->getDisplayName();
				echo '</h2>';
			echo '</div>';
		
			echo '<div style="clear:both;"></div>';
			echo '<br/>';
			
			echo '<div style="float:left;">';
				echo $post_it->getDateTimeFormat('RecordCreated');
				
				$user_it = $user->getExact($post_it->get('AuthorId'));
				$user_it = $user_it->getRef('SystemUser');
				
				echo ', <a href="'.ParserPageUrl::parse($user_it).'">'.$user_it->getDisplayName().'</a>';
			echo '</div>';

			echo '<div id="tags" style="padding-left:20px;">';
				$tag_it = $post_it->getTagsIt();
				$tags = array();
				
				while ( !$tag_it->end() )
				{
					$tags[$tag_it->getPos()] = '<a class="tag" href="'.
						ParserPageUrl::parse($tag_it).'">'.$tag_it->getDisplayName().'</a>';
						
					$tag_it->moveNext();	
				}
				echo join($tags, ', ');
			echo '</div>';

			echo '<div style="clear:both"></div>';
			echo '<br/>';
								
			echo '<div>';
				$parser = new SiteBlogParser($post_it, $project_it);
				echo $parser->parse();
			echo '</div>';

			$otherpost_it = $post_it->getWithSameTags( 3 );
			
			if ( $otherpost_it->count() > 0 )
			{
				echo '<div style="padding-top:9px;">';
					echo '<h4>'.translate('Другие новости по этой теме').':</h4>';
				echo '</div>';
			}
			
			while ( !$otherpost_it->end() )
			{
				echo '<div>';
					echo '<a href="'.ParserPageUrl::parse($otherpost_it).'">'.$otherpost_it->getDisplayName().'</a>';
				echo '</div>';
					
				$otherpost_it->moveNext();
			}

			if ( $otherpost_it->count() > 0 )
			{
				echo '<br/>';
			}
			
			$this->drawComments( $post_it );

			$page->drawWhiteBoxEnd();
		echo '</div>';
	}
}

?>
