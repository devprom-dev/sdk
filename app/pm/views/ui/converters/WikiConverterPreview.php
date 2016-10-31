<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class WikiConverterPreview 
{
 	var $parser, $wiki_it, $b_draw_contents, $b_draw_section_num = true;
	private $root_it;
	private $options = array();

	function setOptions( $options ) {
		$this->options = $options;
	}

	function getOptions() {
		return $this->options;
	}

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
		$this->wiki_it = $wiki_it;
 	}
	
	function getFileUrl( $file_it )
	{
		return _getServerUrl().$file_it->getFileUrl();
	}

 	function HasContents() 
 	{
 		return false;
 	}
 	
 	function HasNumberedSections() {
 		return in_array('numbering', $this->options);
 	}
 	
 	function parse()
 	{
 		header('Content-Type: text/html; charset='.APP_ENCODING);
		$this->uid = new ObjectUID();

 		$this->drawBegin();
		$this->drawBody();
 		$this->drawEnd();
 	}
 	
	function drawBegin() 
	{
	?>
		<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=".APP_ENCODING>
			<?php $this->drawTitle(); ?>
		</head>
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
		while ( !$this->wiki_it->end() )
		{
			$this->drawChildren($this->wiki_it->copy(), $this->wiki_it->getLevel());
			$this->wiki_it->moveNext();
		}
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
	
	function drawChildren( $wiki_it, $level )
	{
		$headerIndex = min($level + 1, 6);
		?>
		<div class=section>
			<h<?=$headerIndex?> id="<?=$wiki_it->getId()?>"><? echo $this->getItemTitle($wiki_it); ?></h<?=$headerIndex?>>
		</div>
		<div class=text>
		<?
            $this->parser->setObjectIt($wiki_it);
			$content = $this->parser->parse( $wiki_it->getHtmlDecoded('Content') );
			$content = preg_replace_callback( '/<img\s+([^>]*)>/i', array('HtmlImageConverter', 'replaceExternalImageCallback'), $content);
			echo $content;
		?>
		</div>
		<?
		$this->drawSeparator();
	}

	function getItemTitle( $wiki_it )
	{
		$title = '';
		if( $this->b_draw_section_num && $wiki_it->get('SectionNumber') != '' ) {
			$title .= $wiki_it->get('SectionNumber').'.&nbsp; ';
		}
		if ( in_array('uid', $this->options) && $wiki_it->get('ParentPage') != '' ) {
			$info = $this->uid->getUIDInfo($wiki_it);
			$title .= $info['uid'] . '&nbsp; ';
		}
		$title .= $wiki_it->getHtmlDecoded('Caption');
		return $title;
	}
	
	function drawEnd() 
	{
		echo '</body></html>';
	}
}
