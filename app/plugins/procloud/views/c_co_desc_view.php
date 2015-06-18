<?php

/////////////////////////////////////////////////////////////////////////////////
class CoDescPageContent extends CoPageContent
{
	function validate()
	{
		global $project_it, $user_it, $_REQUEST;
		
		if ( !is_object($project_it) )
		{
			return false;
		}
		
		if ( !$project_it->IsPublic() && !$project_it->hasUserAccess( $user_it->getId() ) || $project_it->HasProductSite() )
		{
			return false;
		}

		if ( !$project_it->IsPublicKnowledgeBase() )
		{
			return false;
		}

		return true;
	}
	
	function getTitle()
	{
		return translate('Описание').' - '.parent::getTitle();
	}
	
	function getKeywords()
	{
		return parent::getKeywords().' '.translate('описание').' '.translate('назначение').' '.
			translate('цель').' '.translate('предназначена').' '.translate('позволяет').' '.translate('содержание');
	}
	
	function draw()
	{
		global $model_factory, $project_it, $user_it, $_REQUEST;
		
		$page = $this->getPage();
		
		$this->drawProjectHeader(translate('Описание'));
		
		// introduction
		echo '<div id="bloglist">';
			$page_it = $project_it->getProductPageIt();
			
			echo '<a name=top/>';
/*			
			echo '<div class="post">';
				$page->drawWhiteBoxBegin();
				
				echo '<div>';
		 			$parser = new SiteWikiParser( $page_it, $project_it );
					echo $parser->parse();
				echo '</div>';
	
				echo '<br/>';
									
				$page->drawWhiteBoxEnd();
			echo '</div>';
*/			
			$this->drawPage( $page_it );
		echo '</div>';

		echo '<div id="user_actions">';

			echo '<div class="action_box">';
				$page->drawGreyBoxBegin();
	
				echo '<div id="title">';
					echo translate('Содержание');
				echo '</div>';
				
				$this->drawIndex( $page_it );

				$page->drawGreyBoxEnd();
			echo '</div>';

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
				$comment_it = $comment->getByEntities( array('knowledgebase', 'projectpage') );

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
	
	function drawIndex( $page_it )
	{
		$parent_id = $page_it->getId();
 		$it = $page_it->getChildrenIt();
 		
 		while ( $it->get('ParentPage') == $parent_id )
 		{
 			if ( $it->get('Content') != '' )
 			{
 				echo '<div style="padding-bottom:10px;"><a href="#'.$it->getSearchName().'">'.$it->getDisplayName().'</a></div>';
 			}
 			
 			$id = $it->getId();
 			$this->drawIndex( $it );
 			
 			$it->moveToId( $id );
 			$it->moveNext();
 		}
	}
	
 	function drawPage( $page_it )
 	{
 		global $project_it, $model_factory;
 		$comment = $model_factory->getObject('Comment');
 		
		$page = $this->getPage();
		
		$parent_id = $page_it->getId();
 		$it = $page_it->getChildrenIt();
 		
 		while ( $it->get('ParentPage') == $parent_id )
 		{
 			if ( $it->get('Content') == '' )
 			{
 				$it->moveNext();
 				continue;
 			}
 			
	 		echo '<div class="post">';
	 			echo '<div id="entry">';
					$page->drawWhiteBoxBegin();

					echo '<a name="'.$it->getSearchName().'" title="'.$it->getId().'"></a>';
	
		 			echo '<h2>';
						echo $it->getDisplayName();
					echo '</h2>';
					echo '<br/>';
	
					$parser = new SiteWikiParser($it, $project_it);
					echo $parser->parse();

					echo '<div style="float:right;padding-top:10px;">';
						echo '^ <a href="#top">'.translate('к содержанию').'</a>';
					echo '</div>';

					echo '<a name="'.$it->getSearchName().'#comment" title="'.$it->getId().'"></a>';

					echo '<div class="commentsholder" id="comments'.$it->getId().'" style="float:left;padding-top:10px;width:80%;">';
						echo '<div id="comcount" title="'.translate('Комментарии').'">';
							echo '<a href="javascript: initComments(\''.$project_it->get('CodeName').'\', ' .
								'\'projectpage\', \''.$it->getId().'\')">'.
									$comment->getCount($it).'</a>';
						echo '</div>';
					echo '</div>';

					echo '<div style="clear:both;">';
					echo '</div>';
	
					$page->drawWhiteBoxEnd();
				echo '</div>';

				echo '<br/>';
	 		echo '</div>';

			$id = $it->getId();
 			$this->drawPage( $it );
 			
 			$it->moveToId($id);
 			$it->moveNext();
 		}
 	}	

	function drawScripts()
	{
		global $project_it;
		
		?>
		<script language="javascript">
 			$(document).ready(function() { 
 				var locstr = new String(decodeURI(window.location));
				if ( locstr.indexOf('#comment') > 0 )
				{
					var commentString = locstr.substring(locstr.indexOf('#'));
					var parts = commentString.split('#');
					
					if ( parts.length > 0 )
					{
						initComments('<? echo $project_it->get('CodeName')?>', 'projectpage', 
							$('a[name="'+parts[1]+'"]').attr('title') );
					}
				}
 			});
		</script>
		<?		
	}
}

?>
