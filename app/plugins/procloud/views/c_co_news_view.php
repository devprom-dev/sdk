<?php

/////////////////////////////////////////////////////////////////////////////////
class CoNewsPageContent extends CoPageContent
{
	function validate()
	{
		global $model_factory, $_REQUEST;
		
		return true;
	}
	
	function getTitle()
	{
		return translate('Новости проектов').' - '.parent::getTitle();
	}
	
	function getKeywords()
	{
		$words = array ( 
			translate('каталог'), 
			translate('проект'), 
			translate('проекты'), 
			translate('продукт'), 
			translate('devprom'), 
			translate('сайт'), 
			translate('новость'), 
			translate('поиск'), 
			translate('найти'), 
			translate('блог'), 
			translate('блоге'), 
			translate('облако'), 
			translate('новости'), 
			translate('пользователь'), 
			translate('использовать'), 
			translate('обсуждения'), 
			translate('обсудить') 
		);
		
		return join($words, ' ');
	}
	
	function getDescription()
	{
		return text('procloud566');
	}

	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();

		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;"><a href="/news">'.translate('Новости').'</a></div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an"></div>';
			echo '</div>';
		echo '</div>';
		
		echo '<div style="clear:both;"></div>';
		echo '<br/>';						
				
		// introduction
		echo '<div id="bloglist">';
		
			$comment = $model_factory->getObject('Comment');
			
			$post = $model_factory->getObject('procloud.BlogPost');
			$post->disableVpd();
			$post->addFilter( new PublicBlogPostFilter('-') );
			
			$total = $post->getRecordCount();
			$post_it = $post->getLatest( 10, $_REQUEST['page'] );

			while ( !$post_it->end() )
			{
				echo '<div id="post">';
					$page->drawWhiteBoxBegin();
					
					$project = $model_factory->getObject('pm_Project');
					
					$project->addFilter( new FilterAttributePredicate('VPD', $post_it->get('VPD')) );  
					
					$project_it = $project->getAll();
					
					$session = new PMSession( $project_it );
					
					echo '<div id="row">';
						echo '<h2><a class="author" href="'.ParserPageUrl::parse($project_it).'">'.$project_it->getDisplayName().'</a> | ';
						echo '<a id="post" href="'.ParserPageUrl::parse($post_it).'">'.$post_it->getWordsOnly('Caption', 5).'</a></h2>';
					echo '</div>';
	
					echo '<div id="content" style="padding-top:6px;">';
						$parser = new SiteBlogParser($post_it, $project_it);
						$more_text = false;
						
						echo $parser->parse_substr( $post_it->get('Content'), 60, $more_text );
						
						if ( $more_text )
						{
							echo ' ...';
						}
					echo '</div>';

					echo '<div id="basement" style="padding-top:6px;">';
						echo '<div id="comcount" title="'.translate('Комментарии').'">';
							echo '<a href="'.ParserPageUrl::parse($post_it).'#comment">'.$comment->getCount($post_it).'</a>';
						echo '</div>';

						echo '<div id="date" style="float:left;margin-top:4px;">';
							echo $post_it->getDateTimeFormat('RecordCreated');
						echo '</div>';

						echo '<div id="tags" style="padding-left:10px;margin-top:5px;">';
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
						
					$page->drawWhiteBoxEnd();
				echo '</div>';
						
				echo '<div style="clear:both;">&nbsp;</div>';
				echo '<br/>';

				$post_it->moveNext();
			}	

			$this->drawPaging( $total, 10 );
		echo '</div>';

		echo '<div id="user_actions">';
			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();

				echo '<div id="title">';
					echo translate('Обсуждения');
				echo '</div>';

				$comment = $model_factory->getObject('Comment');
				$project = $model_factory->getObject('pm_Project');

				$sql = " SELECT c.ObjectId, c.ObjectClass, " .
					   "		MAX(c.RecordModified) as RecordModified," .
					   "        (SELECT COUNT(1) FROM Comment c2 " .
					   "	      WHERE c2.ObjectId = c.ObjectId " .
					   "            AND c2.ObjectClass = c.ObjectClass ) as CommentsCount," .
					   "		i.Project " .
					   "   FROM Comment c, pm_PublicInfo i ".
					   "  WHERE c.ObjectClass IN ('pmblogpost', 'request', 'question', 'helppage')" .
					   "    AND c.VPD = i.VPD " .
					   "    AND i.IsProjectInfo = 'Y' " .
					   "  GROUP BY c.ObjectId, c.ObjectClass, i.Project ".
					   "  ORDER BY RecordModified DESC LIMIT 20 ";
				
				$comment_it = $comment->createSQLIterator( $sql );

				while ( !$comment_it->end() )
				{
					$session = new PMSession($comment_it->get('Project'));
					$project_it = $project->getExact($comment_it->get('Project'));

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
						'<a class="author" href="'.ParserPageUrl::parse($project_it).'">'.$project_it->getWordsOnly('Caption', 3).'</a> '.
							$object_it->getWordsOnly('Caption', 12).'</div>';

					$comment_it->moveNext();
				}

				$page->drawGreyBoxEnd();
			echo '</div>';
		echo '</div>';
	}
}

?>
