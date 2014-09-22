<?php

include_once SERVER_ROOT_PATH."ext/htmldiff/html_diff.php";

class FieldCompareToContent extends FieldStatic
{
	private $page_it;
	
	private $compare_to_page_it;
	
	public function __construct( $page_it, $compare_to_page_it )
	{
		$this->page_it = $page_it;
		
		$this->compare_to_page_it = $compare_to_page_it;
	}
	
	function draw()
	{
		$editor = WikiEditorBuilder::build($this->page_it->get('ContentEditor'));
		
		echo '<div class="reset wysiwyg">';
		
			$parser = $editor->getComparerParser();

	 		$diff = html_diff(
 						$this->compare_to_page_it->getId() > 0
 							? IteratorBase::wintoutf8($parser->parse($this->compare_to_page_it->getHtmlDecoded('Content')))
 							: "", 
	 					IteratorBase::wintoutf8($parser->parse($this->page_it->getHtmlDecoded('Content')))
			);
			
	 		if ( strpos($diff, "diff-html-") !== false )
	 		{
	 			echo IteratorBase::utf8towin( $diff );  
	 		}
	 		else
	 		{
	 			echo $editor->getPageParser()->parse($this->page_it->getHtmlDecoded('Content'));
	 		}

	 	echo '</div>';
	}
}