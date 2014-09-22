<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class WikiConverterPreview 
{
 	var $parser, $wiki_it, $b_draw_contents, $b_draw_section_num;
 	
 	function setObjectIt( $wiki_it )
 	{
 		$this->wiki_it = $wiki_it;
 		
		$editor = WikiEditorBuilder::build($this->wiki_it->get('ContentEditor'));

		$editor->setObjectIt( $this->wiki_it );
		
 		$this->parser = $editor->getHtmlParser();
 		
 		$this->parser->setObjectIt( $this->wiki_it );
 	}
	
	function getGlobalUrl( $wiki_it )
	{
		if ( is_null($wiki_it) )
		{
			return '';
		}
		else
		{
			return '#'.$wiki_it->getId();
		}
	}
	
	function getFileUrl( $file_it )
	{
		return _getServerUrl().$file_it->getFileUrl();
	}

 	function HasContents() 
 	{
 		return false;
 	}
 	
 	function HasNumberedSections() 
 	{
 		return false;
 	}
 	
 	function parse()
 	{
 		header('Content-Type: text/html; charset=windows-1251');

 		$this->drawBegin();
 		$this->drawTitle();
		$this->drawBody();
 		$this->drawEnd();
 	}
 	
	function drawBegin() 
	{
	?>
		<html>
		<meta http-equiv="content-type" content="text/html; charset=Windows-1251">
	<?
		$this->drawCSS();
	?>
		<body>
	<?
		$this->b_draw_contents = $this->HasContents();
		$this->b_draw_section_num = $this->HasNumberedSections();
	}
	
 	function drawTitle() 
 	{
		echo '<title>'.$this->wiki_it->get('Caption').'</title>';
	}
	
	function drawCSS() 
	{
	}
	
	function drawSeparator()
	{
	}

	function setRevision( $change_it )
	{
		$this->change_it = $change_it;
	}
	
 	function drawBody() 
 	{
 	?>
		<div class=header>
		<? $this->drawHeader(); ?>
		</div>
		<? if($this->b_draw_contents && $this->wiki_it->count() < 2) { ?>
		<div class=content>
		<?  
			$this->drawChildrenLink($this->wiki_it);
		?>
		</div>
		<?
		}

		?>
		<div class=introduction>
		<? 
		while ( !$this->wiki_it->end() )
		{
			$this->drawSeparator(); 
			echo '<h3>'.$this->wiki_it->get('Caption').'</h3>';
			
			$this->setObjectIt( $this->wiki_it );
			echo $this->parser->parse( $this->wiki_it->getHtmlDecoded('Content') );
			
			$this->wiki_it->moveNext();
		}
		?>
		</div>
		<div class=body>
		<?
		if ( $this->wiki_it->count() < 2 )
		{
			$this->wiki_it->moveFirst();
			$this->drawChildren($this->wiki_it, 1, '' );
		}
		?>
		</div>
		<div class=footer>
		<? $this->drawFooter(); ?>
		</div>
 	<?
	}
	
	function drawHeader() 
	{
	}
	
	function drawFooter() 
	{
	}
	
	function drawChildrenLink( $wiki_it, $level_num = 0, $parent_level_name = '' ) 
	{
		$parent_id = $wiki_it->getId();
		$children_it = $wiki_it->getChildrenIt();

		$left_offset = $level_num * 15;
		$parent_level_name = $parent_level_name == '' ? $parent_level_name : $parent_level_name.'.';
		$i = 0;
		
		while( $children_it->get('ParentPage') == $parent_id ) 
		{
			$i++;

			if ( $children_it->IsArchived() )
			{
				$children_it->moveNext();
				$i--;
				continue;
			}
			
			$id = $children_it->getId();
			if($this->b_draw_section_num) {
				$level_name = $parent_level_name.$i;
			}
			echo '<div style="padding-bottom:2pt;padding-left:'.$left_offset.'">';
		?>
			<? echo $level_name ?>&nbsp;&nbsp;
			<a href="#<? echo $children_it->getId(); ?>">
				<? echo $children_it->get('Caption'); ?>
			</a>
		<?
			echo '</div>';
			$this->drawChildrenLink( $children_it, $level_num + 1, $level_name );
			
			$children_it->moveTo('WikiPageId', $id);
			$children_it->moveNext();
		}
	}
	
	function drawChildren( $wiki_it, $level_num, $parent_level_name ) 
	{
		$parent_id = $wiki_it->getId();
		$children_it = $wiki_it->getChildrenIt();

		$parent_level_name = $parent_level_name == '' ? 
			$parent_level_name : $parent_level_name.'.';

		for ( $i = 0; $children_it->get('ParentPage') == $parent_id; $i++ ) 
		{
			if ( $children_it->IsArchived() )
			{
				$children_it->moveNext();
				$i--;
				continue;
			}

			$id = $children_it->getId();
			if($this->b_draw_section_num) 
			{
				$level_name = $parent_level_name.($i+1).'&nbsp;  ';
			}
			?>
			<div class=section>
				<a name="<? echo $children_it->getId(); ?>"></a>
				<h3><? echo $level_name.$children_it->get('Caption'); ?></h3>
			</div>
			<div class=text>
			<?
				$this->setObjectIt( $children_it );
				echo $this->parser->parse( $children_it->getHtmlDecoded('Content') );
			?>
			</div>
			<?
			$this->drawSeparator(); 

			$this->drawChildren( $children_it, $level_num + 1, $level_name );

			$children_it->moveTo('WikiPageId', $id);
			$children_it->moveNext();
		}
	}
	
	function drawEnd() 
	{
		echo '</body></html>';
	}
}
