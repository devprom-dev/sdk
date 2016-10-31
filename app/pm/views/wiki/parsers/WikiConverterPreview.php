<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class WikiConverterPreview 
{
 	var $parser, $wiki_it, $b_draw_contents, $b_draw_section_num = true;
	private $root_it;

 	function setObjectIt( $wiki_it )
 	{
		$this->root_it = $wiki_it->copy();

		$editor = WikiEditorBuilder::build($wiki_it->get('ContentEditor'));
		$editor->setObjectIt($wiki_it);

 		$this->parser = $editor->getHtmlParser();
		$this->parser->setRequiredExternalAccess();
 		$this->parser->setObjectIt($wiki_it);

 		$this->parser->setHrefResolver(function($wiki_it) {
 			return '#'.$wiki_it->getId();
 		});
 		$this->parser->setReferenceTitleResolver(function($info) {
 			return $info['caption'];
 		});

		if ( $wiki_it->count() > 1 ) {
			$this->wiki_it = $wiki_it;
		}
		else {
			$this->wiki_it = $wiki_it->object->getRegistry()->Query(
				array_merge(
					array(
						new WikiRootTransitiveFilter($wiki_it->getId()),
						new SortDocumentClause()
					)
				)
			);
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
 		header('Content-Type: text/html; charset='.APP_ENCODING);

 		$this->drawBegin();
 		$this->drawTitle();
		$this->drawBody();
 		$this->drawEnd();
 	}
 	
	function drawBegin() 
	{
	?>
		<html>
		<meta http-equiv="content-type" content="text/html; charset=".APP_ENCODING>
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
		<? if($this->b_draw_contents && $this->root_it->count() < 2) { ?>
		<div class=content>
		<?
        while ( !$this->wiki_it->end() )
        {
            $this->drawChildrenLink($this->wiki_it);
            $this->wiki_it->moveNext();
        }
        $this->wiki_it->moveFirst();
		?>
		</div>
		<?
		}
		?>

		<div class=body>
		<?
		while ( !$this->wiki_it->end() )
		{
			$this->drawChildren($this->wiki_it->copy());
			$this->wiki_it->moveNext();
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
	
	function drawChildrenLink($wiki_it)
	{
        if($this->b_draw_section_num) {
            $level_name = $wiki_it->get('SectionNumber').' &nbsp;&nbsp; ';
        }
        echo '<div style="padding-bottom:2pt;">';
        echo $level_name
        ?>
			<a href="#<? echo $wiki_it->getId(); ?>">
				<? echo $wiki_it->getHtmlDecoded('Caption'); ?>
			</a>
		<?
        echo '</div>';
	}
	
	function drawChildren( $wiki_it )
	{
		if($this->b_draw_section_num) $level_name = $wiki_it->get('SectionNumber').' &nbsp; ';
		?>
		<div class=section>
			<a name="<? echo $wiki_it->getId(); ?>"></a>
			<h3><? echo $level_name.$wiki_it->getHtmlDecoded('Caption'); ?></h3>
		</div>
		<div class=text>
		<?
            $this->parser->setObjectIt($wiki_it);
			echo $this->parser->parse( $wiki_it->getHtmlDecoded('Content') );
		?>
		</div>
		<?
		$this->drawSeparator();
	}
	
	function drawEnd() 
	{
		echo '</body></html>';
	}
}
