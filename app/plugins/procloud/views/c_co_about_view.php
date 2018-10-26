<?php

/////////////////////////////////////////////////////////////////////////////////
class CoAboutPageContent extends CoPageContent
{
	function validate()
	{
		global $model_factory, $_REQUEST, $project_it;
		
		if ( $_REQUEST['id'] != '' )
		{
			$page = $this->getPage();
			
			$project_it = $page->getDevpromProjectIt();
			
			$session = new PMSession( $project_it );

			$doc = $model_factory->getObject('HelpPage');
			
			$doc_it = $doc->getExact($_REQUEST['id']);
			
			if ( $doc_it->count() < 1 )
			{
				return false;
			}
		}

		return true;
	}
	
	function draw()
	{
		global $model_factory, $project_it;
		
		$page = $this->getPage();

		$project_it = $page->getDevpromProjectIt();
		$session = new PMSession( $project_it );
		
		echo '<div style="float:left;">';
			echo '<div id="grbutton" style="width:220px;">';
				echo '<div id="lt">&nbsp;</div>';
				echo '<div id="bd"><div style="padding-top:4px;">';
					echo '<a href="/about">'.translate('О проекте').'</a>';
				echo '</div></div>';
				echo '<div id="rt">&nbsp;</div>';
				echo '<div id="an"></div>';
			echo '</div>';
		echo '</div>';

		echo '<div style="clear:both;"></div>';
		echo '<br/>';						
		
		$doc = $model_factory->getObject('HelpPage');
		$doc->defaultsort = 'OrderNum ASC';

		echo '<div id="bloglist">';
			if ( $_REQUEST['id'] != '' )
			{
				$wiki_it = $doc->getExact($_REQUEST['id']);
				$this->drawPage( $wiki_it, '' );
			}
			else
			{
				$wiki_it = $doc->getAll();
				$this->drawDocs( $wiki_it );
			}
		echo '</div>';

		echo '<div id="user_actions">';
			$page->drawGreyBoxBegin();

			echo '<div id="title">';
				echo translate('Содержание');
			echo '</div>';
			
			if ( $_REQUEST['id'] != '' )
			{
				$wiki_it->moveFirst();
				$this->drawPageIndex( $wiki_it, '' );
			}
			else
			{
				$wiki_it->moveFirst();
				$this->drawIndex( $wiki_it );
			}

			$page->drawGreyBoxEnd();
		echo '</div>';

		echo '<div style="clear:both;width:100%;">&nbsp;</div>';
		echo '<br/>';
	}
	
	function drawDocs( $doc_it )
	{
		global $model_factory, $project_it;
		
		$page = $this->getPage();
		$wiki = $model_factory->getObject('HelpPage');
		
		while ( !$doc_it->end() )
		{
			echo '<div class="post">';
				$page->drawWhiteBoxBegin();
				
					echo '<a name="'.$doc_it->getId().'"/>';
	
		 			echo '<h2>';
						echo '<a href="'.ParserPageUrl::parse($doc_it).'">'.$doc_it->getDisplayName().'</a>';
					echo '</h2>';
				
					echo '<br/>';
					
		 			echo '<div id="entry">';
						$wiki_it = $wiki->getByRef('WikiPageId', $doc_it->get('Content'));
	
						$parser = new SiteWikiParser($wiki_it, $project_it);
						echo $parser->parse_substr( $wiki_it->get('Content'), 120, $more_text );
	
						echo '<div style="clear:both;width:100%;"></div>';
	
						echo '<br/>';
	
						$page->drawBlackButton( 
							'<a href="'.ParserPageUrl::parse($doc_it).'">'.translate('Подробнее').'</a>' );
					echo '</div>';
								
				$page->drawWhiteBoxEnd();
			echo '</div>';
	
			echo '<br/>';
			$doc_it->moveNext();
		}
	}
	
	function drawIndex( $it )
	{
		$it->moveFirst();
		
 		while ( !$it->end() )
 		{
 			if ( $it->get('Content') != '' )
 			{
 				echo '<div style="padding-bottom:10px;"><a href="#'.$it->getId().'">'.$it->getDisplayName().'</a></div>';
 			}
 			
 			$it->moveNext();
 		}
	}
	
 	function drawPage( $it, $parent_page )
 	{
 		global $project_it, $model_factory;
 		$comment = $model_factory->getObject('Comment');
 		
		$page = $this->getPage();

 		while ( $it->get('ParentPage') == $parent_page && !$it->end() )
 		{
 			if ( $it->get('Content') == '' )
 			{
 				$it->moveNext();
 				continue;
 			}
 			
	 		echo '<div class="post">';
				echo '<a name="'.$it->getId().'"/>';

	 			echo '<div id="entry">';
					$page->drawWhiteBoxBegin();

		 			echo '<h2>';
						echo $it->getDisplayName();
					echo '</h2>';
	
					echo '<br/>';

					$parser = new SiteWikiParser($it, $project_it);
					echo $parser->parse();

					echo '<br/>';

					echo '<div style="float:right;padding-top:10px;">';
						echo '^ <a href="#top">'.translate('к содержанию').'</a>';
					echo '</div>';

					echo '<div style="clear:both;width:100%;"></div>';
	
					$page->drawWhiteBoxEnd();
				echo '</div>';
	 		echo '</div>';

			echo '<br/>';

 			$id = $it->getId();
 			$cit = $it->getChildrenIt();
 			
 			$this->drawPage( $cit, $id );
 			
 			$it->moveToId( $id );
 			$it->moveNext();
 		}
 	}	
 	
	function drawPageIndex( $it, $parent_page )
	{
 		while ( $it->get('ParentPage') == $parent_page && !$it->end() )
 		{
 			if ( $it->get('Content') != '' )
 			{
 				echo '<div style="padding-bottom:10px;"><a href="#'.$it->getId().'">'.$it->getDisplayName().'</a></div>';
 			}
 			
 			$id = $it->getId();
 			$cit = $it->getChildrenIt();
 			
 			$this->drawPageIndex( $cit, $id );
 			
 			$it->moveToId($id);
 			$it->moveNext();
 		}
	}
 	
}

?>
