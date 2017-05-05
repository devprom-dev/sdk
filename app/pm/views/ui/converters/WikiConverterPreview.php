<?php

include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';

class WikiConverterPreview 
{
 	var $parser, $wiki_it, $b_draw_contents, $b_draw_section_num = true;
	private $root_it;
	private $options = array();
	private $compareTo = null;

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

		foreach( $this->options as $option ) {
			if ( preg_match('/baseline,(.+)/', $option, $matches) ) {
				$this->compareTo = $this->buildCompareTo($matches[1], $wiki_it);
				if ( $this->compareTo->getId() == '' ) $this->compareTo = null;
			}
		}

 		$this->parser = is_object($this->compareTo) ? $editor->getComparerParser() : $editor->getHtmlParser();
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

 	function buildCompareTo( $value, $wiki_it )
	{
		if( preg_match('/document:(\d+)/', $value, $matches) )
		{
			$registry = new WikiPageRegistryContent($wiki_it->object);
			return $registry->Query(array(new FilterInPredicate($matches[1])));
		}
		else if ( $value > 0 )
		{
			$snapshot = new WikiPageComparableSnapshot($wiki_it->getRef('DocumentId'));
			return $snapshot->getExact($value);
		}
		return $wiki_it->object->getEmptyIterator();
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

		$docLevels = array();
		while( !$this->wiki_it->end() ) {
			$docLevels[$this->wiki_it->get('DocumentId')][] = $this->wiki_it->getLevel();
			$this->wiki_it->moveNext();
		}
		$rootLevels = array_map(
			function(&$value) {
				return min($value);
			},
			$docLevels
		);
		$rootSiblings = array_map(
			function(&$value) {
				$minValue = min($value);
				return count(array_filter($value, function($filter) use ($minValue) {
					return $filter == $minValue;
				}));
			},
			$docLevels
		);

		$this->wiki_it->moveFirst();
		while ( !$this->wiki_it->end() )
		{
			// get page level and shift it when single document is exported
			$level = $this->wiki_it->getLevel() - (count($rootLevels) > 1 || $rootSiblings[$this->wiki_it->get('DocumentId')] > 1 ? 0 : 1) - $rootLevels[$this->wiki_it->get('DocumentId')];
			$this->drawChildren($this->wiki_it->copy(), $level);
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
		if ( $level >= 0 ) {
		?>
		<div class=section>
			<h<?=$headerIndex?> id="<?=$wiki_it->getId()?>"><? echo $this->getItemTitle($wiki_it); ?></h<?=$headerIndex?>>
		</div>
		<? } ?>
		<div class=text>
		<?
			if ( is_object($this->compareTo) )
			{
				$registry = new WikiPageRegistryComparison($wiki_it->object);
				$registry->setPageIt($wiki_it);
				$registry->setBaselineIt($this->compareTo);
				$compare_to_page_it = $registry->Query();

				$diffBuilder = new WikiHtmlDiff(
					$compare_to_page_it->getId() > 0
						? $this->parser->parse($compare_to_page_it->getHtmlDecoded('Content'))
						: "",
					$this->parser->parse($wiki_it->getHtmlDecoded('Content'))
				);
				$content = $diffBuilder->build();
			}
			else {
				$this->parser->setObjectIt($wiki_it);
				$content = $this->parser->parse( $wiki_it->getHtmlDecoded('Content') );
			}
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